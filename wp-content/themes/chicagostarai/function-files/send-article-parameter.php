<?php

// create scrape and custom post
function scrape_rssfeed_content_action() {
    global $rss_feed_fetcher;
    global $chat_gpt_rephraser;
    $status_Code = 0;
    $newsFeedLink = $_POST['newsFeedLink'];
    $sourceTitle = $_POST['sourceTitle'];
    $sourceText = $_POST['sourceText'];
    $sourceUrl = $_POST['sourceUrl'];
    $subject = $_POST['subject'];
    $questions = $_POST['questions'];
    $prompt = $_POST['prompt_textarea'];
    $gpt_model_name = $_POST['gpt_model_name'];

    $newsFeedLinkArray = array_filter($newsFeedLink, function ($value) {
        return $value !== null && !empty($value);
    });
    $sourceTitleArray = array_filter($sourceTitle, function ($value) {
        return $value !== null && !empty($value);
    });
    $sourceTextLinkArray = array_filter($sourceText, function ($value) {
        return $value !== null && !empty($value);
    });
    $sourceUrlArray = array_filter($sourceUrl, function ($value) {
        return $value !== null && !empty($value);
    });

    if(count($newsFeedLinkArray) > 0 || (count($sourceTitleArray) > 0 && count($sourceTextLinkArray) > 0 && count($sourceUrlArray) > 0)) {
        $extra_inputs=array('field_subject' => $subject, 'field_questions' => $questions);

        $custom_post_id = $rss_feed_fetcher->do_rss_parse($newsFeedLink, $sourceTitle, $sourceText, $sourceUrl,$extra_inputs);
        if($custom_post_id == "Duplicate Post") {
            $status_Code = 505;
            wp_send_json(['status_Code' => $status_Code], JSON_FORCE_OBJECT);
        } else {
            $gpt_rephraser = $chat_gpt_rephraser->process_chatgpt_request($custom_post_id, $prompt,$gpt_model_name);
            if(!empty($gpt_rephraser)) {
                $status_Code = 200;
                wp_send_json(['content_array' => $gpt_rephraser, 'custom_post_id' => $custom_post_id, 'prompt' => $prompt, 'status_Code' => $status_Code], JSON_FORCE_OBJECT);
            } else {
                $status_Code = 404;
                wp_send_json(['status_Code' => $status_Code], JSON_FORCE_OBJECT);
            }
        }
    } else {
        $status_Code = 503;
        wp_send_json(['status_Code' => $status_Code], JSON_FORCE_OBJECT);
    }

}
add_action('wp_ajax_url_link_parse', 'scrape_rssfeed_content_action');

// create gpt post
function save_gpt_post() {
    global $chat_gpt_rephraser;
  
    $content_array = str_replace(['<b>', '</b>'], '', $_POST['content_array']);


    $ex_data=extractData($content_array);

    if(is_array($ex_data)){
    
        $c_id = $_POST['c_id'];
        $taxanomy = $_POST['taxanomy'];
        $prompt = $_POST['prompt'];
        $flag = $_POST['from_gmail'];
        $gmail_post_id = $_POST['gmail_post_id'];
        $taxanomy = $_POST['taxanomy'];
        $secondary_taxonomy = $_POST['secondarytaxanomy'];
        $gpt_post_id = $_POST['gptPostId'];
        $created_gpt_post_id = $gpt_post_id;
        if(!empty($content_array)) {
            update_scrape_custom_post(true);
            if(!empty($flag)) {
                $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post($content_array, $prompt, $c_id, $taxanomy, NULL, $flag , null , null , $secondary_taxonomy);
                if(!empty($gpt_post_id)) {
                    update_field('gpt_post_ids', $gpt_post_id, $gmail_post_id);
                }
            } else {
                if($created_gpt_post_id){
                    $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post($content_array, $prompt, $c_id, $taxanomy, NULL, $flag , null , $secondary_taxonomy , $created_gpt_post_id);
                }else{
                    $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post($content_array, $prompt, $c_id, $taxanomy , null , $flag , null , null ,  $secondary_taxonomy);
                }
            }
            $gpt_post_permalink = get_permalink($gpt_post_id);
        }
        wp_send_json(['gpt_post_id' => $gpt_post_id, 'status_Code' => 200, 'gpt_post_permalink' => $gpt_post_permalink]);

    }else{
        wp_send_json(['message' => $ex_data, 'status_Code' => 400]);
    }
}

add_action('wp_ajax_save_gpt_post', 'save_gpt_post');

function update_gpt_post() {
    global $chat_gpt_rephraser;

    $content_array = str_replace(['<b>', '</b>'], '', $_POST['content_array']);

    $ex_data=extractData($content_array);

    if(is_array($ex_data)){
    
        $c_id = $_POST['c_id'];
        $taxanomy = $_POST['taxanomy'];
        $prompt = $_POST['prompt'];
        $flag = $_POST['from_gmail'];
        $gmail_post_id = $_POST['gmail_post_id'];
        $taxanomy = $_POST['taxanomy'];
        $secondary_taxonomy = $_POST['secondarytaxanomy'];
        $gpt_post_id = $_POST['gptPostId'];
        $created_gpt_post_id = $gpt_post_id;
        if(!empty($content_array)) {
            update_scrape_custom_post(true);
            if(!empty($flag)) {
                $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post($content_array, $prompt, $c_id, $taxanomy, NULL, $flag , null , $secondary_taxonomy , $created_gpt_post_id);
                if(!empty($gpt_post_id)) {
                    update_field('gpt_post_ids', $gpt_post_id, $gmail_post_id);
                }
            } else {
                $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post($content_array, $prompt, $c_id, $taxanomy , null , null , null, $secondary_taxonomy , $created_gpt_post_id);
            }
            $gpt_post_permalink = get_permalink($gpt_post_id);
        }
        
        update_field('used_prompt', $prompt, $gpt_post_id);

        //update_field('newsmaster_text', $prompt ,  $gpt_post_id);

        update_field('fetch_url_gpt_response', $content_array , $gpt_post_id);

        wp_send_json(['gpt_post_id' => $gpt_post_id, 'status_Code' => 200, 'gpt_post_permalink' => $gpt_post_permalink]);


    }else{
        wp_send_json(['message' => $ex_data, 'status_Code' => 400]);
    }
}

add_action('wp_ajax_update_gpt_post', 'update_gpt_post');

// Function For Extract Data As Tags
function extractData($content) {
    $data = array();

    preg_match_all("/\[(.*?)\]/", $content, $sectionName);
    $sectionTitles = $sectionName[0];
    $allowedPlaceholders =  array(
        '[TITLE]',
        '[Title]',
        '[Author]',
        '[SEO Preview]',
        '[Seo preview]',
        '[SEO preview]',
        '[Keywords]',
        '[Content]',
        '[content]',
        '[Source urls]',
        '[Source URLs]',
        '[SUBJECT]',
        '[Subject]',
        '[QUESTIONS]',
        '[Questions]'
    );

    $toReplace = array_diff($sectionTitles, $allowedPlaceholders);

    $modifiedString = str_ireplace($toReplace, '', $content);
    
    preg_match_all('/\[(.+?)\]:\s*([^[]+?)(?=\[|$)/s', $modifiedString, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $key = ucfirst(strtolower(trim($match[1])));
        $value = trim($match[2]);

        $strip=strip_tags($value);
        $new_str = str_replace(' ', '', $strip);

        $data[$key] =$new_str;
    }
   $title=str_word_count($data['Title']);
   $content=str_word_count($data['Content']);
   
    if($title == 0){
        return "Title Tag Is Required Please click again on Send To Chicago Star AI button again.";
    }

    if($content == 0){
        return "Content Tag Is Required Please click again on Send To Chicago Star AI button again.";
    }
    
    return $data;
}


// create default wordpress post
function create_wp_post_for_blox() {
    $status_Code = '';
    $gpt_post_id = $_POST['gpt_post_id'];
    $title = get_the_title($gpt_post_id);
    $content = get_post_field('post_content', $gpt_post_id);

    // Get all ACF custom fields and their values
    $all_acf_fields = get_fields($gpt_post_id);

    foreach($all_acf_fields as $key => $value) {

        if($key !== "used_prompt") {
            if($key == "custom_post_id") {
                $all_fields = get_fields($value);
                foreach($all_fields as $index => $val) {
                    if($index == "scraped_post_ids") {
                        foreach($val as $scrape_post_id) {

                            $content .= "<br><b>Source URL(scrapped):</b> ".get_field('_source_urls', $scrape_post_id);
                            $content .= "<br><b>Source Platform:</b> ".get_field('_source', $scrape_post_id);
                        }
                    } elseif($index == "source_repeater") {
                        foreach($val as $source_data) {
                            foreach($source_data as $in => $k) {
                                if($in == "custom_source_url") {

                                    $content .= "<br><b>Source URLs:</b> ".$k;
                                }
                            }
                        }
                    }
                }
            } else {
                $field_object = get_field_object($key, $gpt_post_id);
                $label = $field_object['label'];
                $content .= "<br><b>".$label.":</b> ".$value;
            }
        }
    }
    $user_id = get_current_user_id();
    $new_post = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => $user_id,
    );

    // print_r($content);
    $new_post_id = wp_insert_post($new_post);

    if($new_post_id) {
        $status_Code = 200;
        update_field('wp_post_id', $new_post_id, $gpt_post_id);
        wp_send_json(['new_post_id' => $new_post_id, 'status_Code' => $status_Code]);
    } else {
        $status_Code = 502;
        wp_send_json(['status_Code' => $status_Code]);
    }
}
add_action('wp_ajax_create_wp_post_for_blox', 'create_wp_post_for_blox');


// comparison
function output_comparison() {
    global $chat_gpt_rephraser;
    $status_Code = '';
    $original_content = "";
    $gpt_post_id = $_POST['gpt_post_id'];
    $gmail_flag = $_POST['gmail_flag'];
    $gmail_post_id = $_POST['gmail_post_id'];
    $gpt_post = get_post($gpt_post_id);
    $gpt_postcontent = $gpt_post->post_content;

    if($gmail_flag == "gmail_flag") {
        $gmail_post = get_post($gmail_post_id);
        $original_content = $gmail_post->post_content;
        $post_permalink = get_permalink($gmail_post);
    } else {
        $custom_post_id = get_field('custom_post_id', $gpt_post_id);
        $sources_repeaters = get_field('source_repeater', $custom_post_id);
        $scraped_post_ids = get_field('scraped_post_ids', $custom_post_id);
        $post_permalink = get_permalink($gpt_post_id);

        foreach($sources_repeaters as $sources_repeater) {
            if(!empty($sources_repeater['custom_source_content'])) {
                $original_content .= $sources_repeater['custom_source_content']."<br>";
            }
        }
        foreach($scraped_post_ids as $scraped_post_id) {
            $scraped_post = get_post($scraped_post_id);
            if(!empty($scraped_post->post_content)) {
                $original_content .= $scraped_post->post_content."<br>";
            }
        }
    }

    if(!empty($original_content) && !empty($gpt_postcontent)) {

        $comparison_response = $chat_gpt_rephraser->compare_ai_original_content($original_content, $gpt_postcontent,'differences');
        update_field('compare_results', $comparison_response, $gpt_post_id);

        $comparison_similarities = $chat_gpt_rephraser->compare_ai_original_content($original_content, $gpt_postcontent,'similarities');
        update_field('compare_results_similarities', $comparison_similarities, $gpt_post_id);

        $status_Code = 200;
    } else {
        $status_Code = 503;
    }
    wp_send_json(['status_Code' => $status_Code,'post_permalink' => $post_permalink]);
}
add_action('wp_ajax_output_comparison', 'output_comparison');

?>