<?php

namespace RSS\Controllers;

use DOMDocument;
use DOMXPath;

use RSS\Models\RssSetting;
use RSS\Models\RssFeedParser;
use RSS\Models\ScrapperApiSetting;


class RssFeedsParser
{
    private $rssSettingsModel;
    private $apiSettingsModel;
    private $rssParserModel;


    public function __construct()
    {
        $this->rssParserModel = new RssFeedParser();
        $this->rssSettingsModel = new RssSetting('rss_feeds');
        $this->apiSettingsModel = new ScrapperApiSetting('scrapper_api_settings');
    }

    // Parse RSS feeds
    public function fetch_rss_feeds()
    {
        // Get RSS feed URLs from the database
        $rssFeedUrls = $this->rssSettingsModel->get_rss_urls_records();

        foreach ($rssFeedUrls as $rssFeedData) {
            // Fetch and parse the RSS feed content
            $parsedData = $this->parse_rss_content($rssFeedData);

            if (!empty($parsedData)) {
                // Save parsed articles as custom post type posts
                if (!($this->rssParserModel->create_scraped_post($parsedData))) {
                    error_log('Error' . current_time('mysql'));
                }

                error_log('RSS feed parsing event ran successfully at ' . current_time('mysql'));
            }
        }
    }

    // Scrape RSS feed content using Abstract API
    public function scrape_rssfeed_content($url)
    {
        $api_url = $this->apiSettingsModel->get_api_url();
        $api_key = $this->apiSettingsModel->get_api_key();

        // Initialize cURL.
        $ch = curl_init();

        // Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, $api_url['api_url'] . '?api_key=' . $api_key['api_key'] . '&url=' . $url);

        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        try {
            // Execute the request.
            $data = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }
        } catch (\Exception $e) {
            // Log the error message in the debug log
            error_log('Error in cURL request: ' . $e->getMessage());
            return 'Error with Abstract API.' . $e->getMessage();
        } finally {
            // Close the cURL handle.
            curl_close($ch);
        }
        //handle bug  [22-Oct-2023 16:00:22 UTC] Post Data: {"error":{"message":"You have reached your quota. Please, subscribe to a paid plan to continue using this API.","code":"quota_reached","details":null}}
        // $this->check_error($data);

        return $data;
    }

    // Parse RSS content using SimpleXML
    private function parse_rss_content($rssFeedData)
    {
        $parsedData = [];

        $rss_data = simplexml_load_file($rssFeedData['rss_feed_link']);

        if ($rss_data !== false) {
            $maxItems = $rssFeedData['max_allowed_items']; // Get the max allowed items

            foreach ($rss_data->channel->item as $xml_obj) {
                if ($maxItems <= 0) {
                    break; // Stop parsing if max allowed items limit is reached
                }

                $new_post_data = $this->xml_object_to_php($xml_obj);

                if (($this->rssParserModel->scraped_posts_title_check($new_post_data))) {

                    $rss_api_content = $this->scrape_rssfeed_content($new_post_data['_source_urls']);

                    $parsedData['post_title'] = $new_post_data['new_post_title'];
                    $parsedData['post_source'] = $new_post_data['_source'];
                    $parsedData['post_source_url'] = $new_post_data['_source_urls'];
                    $parsedData['post_source_publish_date'] = $new_post_data['new_post_date'];

                    // For Articles that are behind a paywall
                    if (strpos($rss_api_content, 'paywall') !== false) {
                        $parsedData['post_content'] = $new_post_data['new_post_description'];
                        $parsedData['_paywall'] = 'paywall';
                    }

                    // For Articles that are not behind a paywall
                    else {
                        $parsedData['post_content'] = $this->dom_parser($rss_api_content);
                        $parsedData['_paywall'] = 'without_paywall';
                    }
                }

                $maxItems--; // Decrement the max allowed items counter
            }
        }

        return $parsedData;
    }

    // Parse RSS content using SimpleXML
    public function insert_scraped_post($url, $title = array(), $text = array(), $link = array(), $extra_inputs = array())
    {

        $scrape_post_ids = [];
        $res = "";
        if (!empty($url)) {

            for ($j = 0; $j < count($url); $j++) {

                if (!empty($url[$j])) {
                    $new_post_data = [];

                    $rss_api_content = $this->scrape_rssfeed_content($url[$j]);
                    $new_post_data = $this->dom_parser($rss_api_content, $url[$j]);

                    $new_post_data['_source'] = $this->get_source_from_url($url[$j]);
                    $new_post_data['_source_urls'] = $url[$j];
                    $new_post_data['_source_publish_date'] = $new_post_data['source_date'];

                    if (strpos($rss_api_content, 'paywall') !== false) {
                        $new_post_data['_paywall'] = 'paywall';
                    } else {
                        $new_post_data['_paywall'] = 'without_paywall';
                    }

                    if ($this->rssParserModel->scraped_posts_title_check($new_post_data)) {

                        $scrape_post_id = $this->rssParserModel->create_scraped_post($new_post_data, $title, $text, $link);
                        array_push($scrape_post_ids, $scrape_post_id);
                    } else {
                        $scrape_post_id =  "";
                        $res = "Duplicate Post";
                        break;
                    }
                }
            }
        }
        if ($res == "Duplicate Post") {
            return $res;
        } else {
            $custom_post_id = $this->rssParserModel->create_custom_post($scrape_post_ids, $title, $text, $link, $extra_inputs);
            return $custom_post_id;
        }
    }



    // Get articles without paywall
    public function dom_parser($rss_api_content, $url = null)
    {
        // Create a new DOMDocument instance
        $dom = new DOMDocument();

        libxml_use_internal_errors(true); // Disable libxml errors

        // Load the HTML content
        $dom->loadHTML('<?xml encoding="UTF-8">' . $rss_api_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $dom->encoding = 'UTF-8'; // Adjust the encoding as needed

        // Restore libxml error handling
        libxml_use_internal_errors(false);

        $h1_tags = $dom->getElementsByTagName('h1');

        $title = '';

        foreach ($h1_tags as $h1_tag) {
            $title = $h1_tag->textContent;
            break;
        }

        // Create a DOMXPath instance to query the DOMDocument
        $xpath = new DOMXPath($dom);

        $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

        // XPath query to select all paragraphs
        $paragraphs = $xpath->query('//p');

        $paragraph_text = "";

        // This code is to remove HTML Tags From HTML (Output plain HTML content)
        // foreach ($paragraphs as $paragraph) {

        //     $encoding = mb_detect_encoding( $paragraph->textContent, 'UTF-8, ISO-8859-1', true);

        //     $clean_string = mb_convert_encoding($paragraph->textContent, 'UTF-8', $encoding);

        //     $clean_string = preg_replace('/[^\PC\s]/u', '',  $clean_string);

        //     $clean_string = str_replace('â', '', $clean_string);

        //     $paragraph_text .= $clean_string;

        // }

        // $filtered_text = str_replace('â', '', $paragraph_text);

        // $filtered_text = preg_replace('/â/', '', $filtered_text);

        // $filtered_text = strtr($filtered_text, array('â' => ''));

        // $paragraph_text = $filtered_text;

        // Iterate over the selected paragraphs

        // (Output HTML acoording to tags)
        foreach ($paragraphs as $paragraph) {
            // Append the outerHTML of each paragraph
            $paragraph_text .= $dom->saveHTML($paragraph);
        }

        // Printing the extracted paragraph text
        // print_r($paragraph_text);
        // exit;

        // Published Date Scrapper
        $meta_tags = $dom->getElementsByTagName('meta');
        $published_time =  date("Y-m-d");
        foreach ($meta_tags as $tag) {
            if ($tag->getAttribute('property') === 'article:published_time') {
                $published_time = $tag->getAttribute('content');
                break;
            }
        }

        $new_post_data = array(
            'new_post_title' => $title,
            'new_post_description' => $paragraph_text,
            '_source_urls' => $url,
            'source_date' => $published_time
        );

        return $new_post_data;
    }

    public function xml_object_to_php($xml_obj)
    {
        // Typecasting data from XML Object to be useable for
        $typeCastedData = [];
        $typeCastedData['new_post_title'] = (string)$xml_obj->title;
        $new_post_date = (string)$xml_obj->pubDate;
        $new_post_date = strtotime($new_post_date);
        $typeCastedData['new_post_date'] = date("Y-m-d", $new_post_date);
        $typeCastedData['_source_urls'] = (string)$xml_obj->link;
        $typeCastedData['_source'] = $this->get_source_from_url((string)$xml_obj->link);
        $typeCastedData['new_post_description'] = (string)$xml_obj->description;
        return $typeCastedData;
    }

    public function get_source_from_url($source_link)
    {

        // Check if the URL matches the expected pattern
        if (preg_match('/https:\/\/(www\.)?([^\/]+)/', $source_link, $matches)) {
            if (isset($matches[2])) {
                // The publisher name will be in $matches[2]
                $publisher_name = $matches[2];
                // You can further clean or format the publisher name if needed
                // For example, removing "www." or other characters
                $publisher_name = str_replace("www.", "", $publisher_name);
                return $publisher_name;
            } else {
                return "Unknown";
            }
        } else {
            // URL doesn't match the expected pattern
            return "Unknown";
        }
    }
    public function check_error($data)
    {

        $jsonData = json_decode($data);
        if (json_last_error() === JSON_ERROR_NONE && is_object($jsonData)) {

            if ($jsonData && isset($jsonData->error)) {
                $error = $jsonData->error;
                print_r($data);
                exit;
            } else {
                return $data;
            }
        } else {
            return $data;
        }
    }

    public function insert_custom_post($title = array(), $text = array(), $link = array(), $extra_inputs = array())
    {
        $c_post = $this->rssParserModel->create_custom_post(NULL, $title, $text, $link, $extra_inputs);
        return $c_post;
    }

    public function return_formated_scrapped_content($url)
    {

        $custom_post_id = $this->insert_scraped_post($url);

        $scrapped_post_ids = get_field('scraped_post_ids', $custom_post_id);

        for ($j = 0; $j < sizeof($scrapped_post_ids); $j++) {

            $scrapped_post = get_post($scrapped_post_ids[$j]);
            $new_post_data[$j]['_source_urls'] = get_field('_source_urls', $scrapped_post_ids[$j]);
            $new_post_data[$j]['_paywall'] = get_field('_paywall', $scrapped_post_ids[$j]);
            $new_post_data[$j]['new_post_title'] = $scrapped_post->post_title;
            $new_post_data[$j]['new_post_description'] = $scrapped_post->post_content;
            $new_post_data[$j]['scrapped_post_id'] = $scrapped_post_ids[$j];
        }
        $new_post_data[$j]['custom_post_id'] = $custom_post_id;

        return $new_post_data;
        exit;
    }
}