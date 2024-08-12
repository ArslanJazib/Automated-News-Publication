<?php

namespace GPT\Controllers;

use GPT\Models\ContentRephraser;
use GPT\Models\GptApiSetting;
use GPT\Controllers\GptApiPromptSettings;

class ContentRephrasers {

    private $contentRephraserModel;
    private $promptSettings;
    private $apiSettingsTable;

    public function __construct(){
        $this->apiSettingsTable = new GptApiSetting('gpt_api_settings');
        $this->promptSettings = new GptApiPromptSettings();
        $this->contentRephraserModel =  new ContentRephraser();
    }

    public function chat_gpt_api_call($input, $prompt = null, $customSettings = null, $flag = array(),$gpt_model_id= null) {
        $data_string = ""; 

        // Use custom settings if provided; otherwise, use the default settings
        $api_settings = $customSettings ?? $this->apiSettingsTable->get_all_records();
        if (empty($api_settings) || !is_array($api_settings) || count($api_settings) === 0) {
            // Handle the case where no API settings are found
            return json_encode(["error" => "No API settings found."]);
        }

        $api_settings = $api_settings[0];

        $endpoint = $api_settings['api_url'] ?? null;
        $apiKey = $api_settings['api_key'] ?? null;
        $modelId = $gpt_model_id ?? $api_settings['model_id'] ;

        if (empty($endpoint) || empty($apiKey) || empty($modelId)) {
            // Handle missing API parameters
            return json_encode(["error" => "Incomplete API parameters."]);
        }

        $model = $this->apiSettingsTable->get_model_name_by_id($modelId);
    
        if (empty($model)) {
            // Handle the case where the model name is not found
            return json_encode(["error" => "Model name not found."]);
        }

        $final_prompt = (!empty($prompt)) ? $prompt :  $this->promptSettings->generate_dynamic_settings_prompt();

        if (array_key_exists('comparison', $flag) && $flag['comparison'] == '1') {
			if($flag['comp_flag'] == 'differences'){
            $comparePrompt ="Compare the two text models, Source and AI Story below. Identify differences and false information that was added in the AI story which is not contained in Source.
            AI story is in between [AICONTENT] this tag, and Original Source is in between [ORIGINALCONTENT] this tag. Stick to the facts do now embellish or create new information. Do not editorialize or give opinions that are not already stated in the original. Output must be factually correct to the reference  Source. Do not generate new facts. Display the differences in a numbered bullet point format.";
            }
            elseif($flag['comp_flag'] == 'similarities'){

                $comparePrompt ="Compare the two text models, Source and AI Story below for plagiarism. Identify similarities in words and phrasing and display similarities and suspected plagiarism in number bullet point format in the sequence in which the plagiarism occurs.
                AI story is in between [AICONTENT] this tag, and Original Source is in between [ORIGINALCONTENT] this tag. Stick to the facts do not embellish or create new information. Do not editorialize or give opinions that are not already stated in the original. Output must be factually correct to the reference source. Do not generate new facts. Display the similarities in a numbered bullet point format.";
                // add actual prompt here
            }

            $originalPostContent = $flag['original_content'];
            $postcontent = $flag['gpt_postcontent'];
            $data_string = $comparePrompt.' [ORIGINALCONTENT] '.$originalPostContent.' [AICONTENT] '.$postcontent;

            $data = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'user', 'content' =>  $data_string)
                )
            );

        }elseif(array_key_exists('newsmaster', $flag) && $flag['newsmaster'] == '1'){

            $data_string .= $flag['prompt_textarea'] ." :". $flag['text_textarea']."" ;
            $data = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'user', 'content' =>  $data_string)
                )
            );
        }
        else {
            $data = array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'user', 'content' =>  $final_prompt.' '.$input)
                )
            );
        }

        $ch = curl_init($endpoint);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        );

        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            // Handle API call error
            return json_encode(["error" => "API call error: " . curl_error($ch)]);
        }
    
        curl_close($ch);

        return json_encode(["response" => $response]);
    }

    // Rephrasing posts created from RSS Feed URLs With and Without Paywall
    public function scraped_posts_to_gpt() {

        $valid_scraped_posts = $this->contentRephraserModel->get_unpublished_no_paywall_scraped_posts();

        $permalink = null;

        foreach ($valid_scraped_posts as $originalPost) {

            $content = $this->manipulate_content($originalPost);

            $response = $this->chat_gpt_api_call($content);

            $responseData = json_decode($response, true);
            $responseData = json_decode($responseData['response']);

            if (isset($responseData->error)) {
                error_log('Error: ' . $responseData->error->message);
            }
            else{

                $responseContent = $responseData->choices[0]->message->content;

                $parsed_data = $this->parseContent($responseContent);

                if (isset($parsed_data['Title'], $parsed_data['Content'])) {
                    $post_data = array(
                        'post_title'   => $parsed_data['Title'],
                        'post_content' => $parsed_data['Content'],
                        'post_status'  => 'publish',
                        'post_type'    => 'gpt_posts'
                    );

                    $post_id = wp_insert_post($post_data);

                    if ($post_id) {

                        $term_slug = 'from-automatic-rss-feed';

                        // Assign the term 'From Automatic RSS Feed' to the new gpt post
                        wp_set_post_terms($post_id, $term_slug, 'gpt_categories', true);

                        update_post_meta($post_id, 'used_prompt', $this->promptSettings->generate_dynamic_settings_prompt());
                        update_post_meta($post_id, 'seo', $parsed_data['SEO Preview'] ?? '');
                        update_post_meta($post_id, 'newsmaster_text', $parsed_data['Newsmaster Text'] ?? '');
                        update_post_meta($post_id, 'fetch_url_gpt_response', $parsed_data['Fetch from url GPT Response'] ?? '');
                        update_post_meta($post_id, 'keywords', $parsed_data['Keywords'] ?? '');
                        update_post_meta($post_id, 'source_urls', $parsed_data['Source URLs'] ?? '');
                        update_post_meta($post_id, 'source_post_id', $originalPost->ID ?? '');

                        $post = get_post($post_id);
                        if ($post) {
                            error_log('RSS Posts Converted to GPT Posts Successfully ' . current_time('mysql'));
                            $permalink = get_permalink($post);
                        }
                    }
                }
            }
        }

        return $permalink;
    }

    private function manipulate_content($custompost_id) {
        $combined_string = "";
        if($custompost_id !== 'null'){
            $scraped_post_ids = get_field('scraped_post_ids', $custompost_id);
            if(!empty($scraped_post_ids)){
                foreach ($scraped_post_ids as $scraped_post_id) {
                    $content = preg_replace('/<img[^>]+>/', '', get_post_field('post_content', $scraped_post_id));
                    $stripContent = strip_tags($content);
                    $combined_string .= " [ARTICLE]: " . get_the_title($scraped_post_id);
                    $combined_string .= " [BODY]: " . $stripContent;
                    $combined_string .= " [URL]: " . get_field('_source_urls', $scraped_post_id);
                }
            }
            $source_repeaters = get_field('source_repeater', $custompost_id);
            if(!empty($source_repeaters)){
                foreach ($source_repeaters as $source_repeater) {
                    $combined_string .= " [ARTICLE]: " . $source_repeater['custom_source_title'];
                    $combined_string .= " [SUBJECT]: " . get_field('field_subject', $custompost_id);
                    $combined_string .= " [QUESTIONS]: " . get_field('field_questions', $custompost_id);
                    $combined_string .= " [URL]: " . $source_repeater['custom_source_url'];
                }
            }
            return $combined_string;
        }
    }

    public static function parseContent($content) {
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

        $toReplace = array_diff($sectionTitles,$allowedPlaceholders);

        $modifiedString = str_ireplace($toReplace, '', $content);
    
        // Use a regular expression to match each key-value pair
        preg_match_all('/\[(.+?)\]:\s*([^[]+?)(?=\[|$)/s', $modifiedString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            // Trim the key and value and add them to the data array
            $key = ucfirst(strtolower(trim($match[1])));
            $value = trim($match[2]);

            // Add to the data array
            $data[$key] = $value;
        }

        return $data;
    }


    public function call_gpt_api($custom_post_id,$prompt,$gpt_model_name,$custom_text = NULL){

        $gpt_post_input = $this->manipulate_content($custom_post_id);       
    
        $gpt_post_output = $this->chat_gpt_api_call(
            input:          $gpt_post_input,
            prompt:         $prompt,
            customSettings: NULL,
            gpt_model_id:   $gpt_model_name
        );

        $data = json_decode($gpt_post_output, true);

        $responseData = json_decode($data['response']);
        if (isset($responseData->error)) {
            $message = $responseData->error->message;
        }
        else{
            $message = $responseData->choices[0]->message->content;
            $gpt_data = $this->parseContent($message);
            $formatted_gpt_data = $this->format_gpt_output($gpt_data);

        }
        return $formatted_gpt_data;
    }

    function format_gpt_output($gpt_data){

        $formatted_data = "";
        $count = 1;
        foreach ($gpt_data as $key => $value) {
            if($count != 1){
                $formatted_data .= "<br>";
            }
            $formatted_data .= "[<b>".$key."</b>]: ";
            $formatted_data .= $value;
            $count++;
        }
        return $formatted_data;
    }

    public function create_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title=NULL,$flag=NULL,$draft_flag=NULL,$input_text_value,$secondary_taxonomy,$created_gpt_post_id){
        if(!empty($c_id) || $flag == "from_gmail"){
            if($draft_flag){
                $gpt_data['Title'] = $title;
                $gpt_data['Content'] = $content;
            } else {
                $gpt_data = $this->parseContent($content);
            }

            $prompt_updated = (!empty($prompt)) ? $prompt : $this->promptSettings->generate_dynamic_settings_prompt();
            $gpt_post_id = $this->get_gpt_post_from_custom_post($c_id);
            if($gpt_post_id) {
                $gpt_post_id = $this->contentRephraserModel->update_gpt_post($gpt_post_id,$gpt_data,$prompt_updated,$taxanomy,$input_text_value,$secondary_taxonomy);
            }else{
                $gpt_post_id = $this->contentRephraserModel->create_gpt_post($c_id,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
            }
        }
        else{
            $gpt_data = array(
                'Content'=>$content,
                'Title'=>$title
            );
            if(empty($created_gpt_post_id)){
                $gpt_post_id = $this->contentRephraserModel->create_gpt_post(NULL,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
            }else{
                $gpt_post_id = $this->contentRephraserModel->update_gpt_post($created_gpt_post_id,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
            }
            
        }

        return $gpt_post_id;
    }

    public function update_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title=NULL,$flag=NULL,$input_text_value,$secondary_taxonomy,$created_gpt_post_id,$draftGPTPostID){
        if(!empty($c_id) || $flag == "from_gmail"){
            $gpt_data = $this->parseContent($content);

            $prompt_updated = (!empty($prompt)) ? $prompt : $this->promptSettings->generate_dynamic_settings_prompt();
            $gpt_post_id = $this->get_gpt_post_from_custom_post($c_id);
            $created_gpt_post_id = $gpt_post_id;
            if($gpt_post_id) {
                $gpt_post_id = $this->contentRephraserModel->update_gpt_post($created_gpt_post_id,$gpt_data,$prompt_updated,$taxanomy,$input_text_value,$secondary_taxonomy);
            }else{
                $gpt_post_id = $this->contentRephraserModel->create_gpt_post($c_id,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
            }
        }
        else{
            $gpt_data = array(
                'Content'=>$content,
                'Title'=>$title
            );
            if (strpos($flag, "from_newsmaster_") !== false) {
                $parts = explode("from_newsmaster_", $flag);
                if(is_array($parts) && count($parts) > 1){
                    $gpt_post_id = $parts[1];
                    if(!empty($draftGPTPostID)){
                        $created_gpt_post_id = $draftGPTPostID;
                    }
                    if($created_gpt_post_id){
                        $gpt_post_id = $this->contentRephraserModel->update_gpt_post($created_gpt_post_id,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
                    }else{
                        $gpt_post_id = $this->contentRephraserModel->create_gpt_post(NULL,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
                    }
                }
            } else {
                $gpt_post_id = $this->get_gpt_post_from_custom_post($c_id);
                $gpt_post_id = $created_gpt_post_id;
                if($created_gpt_post_id) {
                    $gpt_post_id = $this->contentRephraserModel->update_gpt_post($gpt_post_id,$gpt_data,$prompt,$taxanomy,$input_text_value,$secondary_taxonomy);
                } 
            }
       
        }

        return $gpt_post_id;
    }

    // Mode to Model
    public function get_gpt_post_from_custom_post($custom_post_id){
        if ($custom_post_id) {
            $args = array(
                'post_type' => 'gpt_posts',
                'meta_query' => array(
                    array(
                        'key' => 'custom_post_id',
                        'value' => $custom_post_id,
                        'compare' => '='
                    )
                )
            );
            $query = new \WP_Query($args);
            if ($query->have_posts()) {
                $query->the_post();
                $gpt_post_id = get_the_ID();
                wp_reset_postdata();
                return $gpt_post_id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    } 

    public function get_scraped_post_from_custom_post($custom_post_id){
        $scraped_post_ids = [];

        $scraped_post_ids = get_post_meta($custom_post_id, 'scraped_post_ids', true);

        return $scraped_post_ids;
    }

    function compare_ai_original_content_chatgpt($original_content,$gpt_postcontent,$comp_flag){
        $comparison_arr = array(
            'comparison'        => '1',
            'original_content'  => $original_content,
            'gpt_postcontent'   => $gpt_postcontent,
            'comp_flag'         => $comp_flag
        );
        $comparison_output = $this->chat_gpt_api_call(
            input: NULL,
            flag: $comparison_arr
        );
        $data = json_decode($comparison_output,true);
        $responseData = json_decode($data['response']);
        $comparison_output_message = $responseData->choices[0]->message->content;
        return $comparison_output_message;
    }

    function call_gpt_api_newsmaster($input_data = array()){
        $input_data['newsmaster'] = '1';
        $gpt_newsmaster_output = $this->chat_gpt_api_call(
            input: NULL,
            flag:$input_data
        );
        $output = json_decode($gpt_newsmaster_output,true);
        $responseData = json_decode($output['response']);
        $newsmaster_output = $responseData->choices[0]->message->content;
        return $newsmaster_output;
    }

    public function call_gpt_api_for_gmail($gmail_post_id){

        $combined_string = "";
        $content = preg_replace('/<img[^>]+>/', '', get_post_field('post_content', $gmail_post_id));

        $stripContent = strip_tags($content);
        $combined_string .= " [ARTICLE]: ". get_the_title($gmail_post_id);
        $combined_string .= " [BODY]: " . $stripContent;

        $gpt_post_output = $this->chat_gpt_api_call(
            input: $combined_string
        );

        $data = json_decode($gpt_post_output, true);

        $responseData = json_decode($data['response']);
        if (isset($responseData->error)) {
            $message = $responseData->error->message;
        }
        else{
            $message = $responseData->choices[0]->message->content;
            $gpt_data = $this->parseContent($message);
            $formatted_gpt_data = $this->format_gpt_output($gpt_data);

        }
        return $formatted_gpt_data;

    }
    
        public function call_gpt_api_for_rephrasing($post_data){
        $res = '';
        // Use custom settings if provided; otherwise, use the default settings
        $api_settings = $this->apiSettingsTable->get_all_records();
        if (empty($api_settings) || !is_array($api_settings) || count($api_settings) === 0) {
            return json_encode(["error" => "No API settings found."]);
        }
        $final_prompt = "Rephrase the content to reduce plagiarism but do not alter or create new content. Copy quotes exactly verbatim. Quotes are indicated by quotation marks: ";
        $api_settings = $api_settings[0];

        $endpoint = $api_settings['api_url'];
        
        $apiKey = $api_settings['api_key'];
        $modelId = $gpt_model_id ?? $api_settings['model_id'] ;

        if (empty($endpoint) || empty($apiKey) || empty($modelId)) {
            return json_encode(["error" => "Incomplete API parameters."]);
        }
        
        $model = $this->apiSettingsTable->get_model_name_by_id($modelId);
        if (empty($model)) {
            return json_encode(["error" => "Model name not found."]);
        }
        $data = array(
            'model' => $model,
            'messages' => array(
                array('role' => 'user', 'content' =>  $final_prompt.' '.$post_data[0])
            )
        );

        $ch = curl_init($endpoint);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        );

        $jsonData = json_encode($data);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            return json_encode(["error" => "API call error: " . curl_error($ch)]);
        }
        curl_close($ch);
        
        $response = json_encode(["response" => $response]);
        $data = json_decode($response, true);

        $responseData = json_decode($data['response']);
        if (isset($responseData->error)) {
            $res = $responseData->error->message;
        }
        else{
            $res = $responseData->choices[0]->message->content;
        }
        return $res;
    }
}
?>