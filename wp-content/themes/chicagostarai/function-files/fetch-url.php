<?php

// create scrape and custom post
function scrape_rssfeed()
{
    $status_Code = '';
    global $rss_feed_fetcher;
    // global $chat_gpt_rephraser;

    $newsFeedLink = $_POST['newsFeedLink'];
    // print_r($newsFeedLink);
    // exit;
    if(!empty($newsFeedLink[0])){
        $scrapped_data = $rss_feed_fetcher->formated_scrapped_content($newsFeedLink);
        if(!empty($scrapped_data)){
            $status_Code = 200;  
        }
        else{
            $status_Code = 404;
        }
    }else{
        $status_Code = 503;
    }
    
    // $gpt_rephraser = $chat_gpt_rephraser->process_chatgpt_request($custom_post_id,$prompt);

    wp_send_json(['scrapped_data'=>$scrapped_data,'status_Code'=>$status_Code], JSON_FORCE_OBJECT);
}
add_action('wp_ajax_scrape_rssfeed', 'scrape_rssfeed');

// update scrape post
function update_scrape_post_and_gpt_call($updateFlag = false)
{
    global $chat_gpt_rephraser;
    $status_Code = '';
    $totalWords = $_POST['totalWords'];

    if(intval($totalWords) > 1200){
        wp_send_json(['status_Code'=>509], JSON_FORCE_OBJECT);
    }
    else{
        $data = update_scrape_custom_post($updateFlag);
        $custom_post_id = $data['custom_post_id'];
        $prompt = $data['prompt'];
        $gpt_model_id = $data['gpt_model_id'];
        $customtext_txtarea = $data['customtext_txtarea'];

        if($_POST['draft'] !== 'true') {
            $gpt_rephraser = $chat_gpt_rephraser->process_chatgpt_request($custom_post_id,$prompt,$gpt_model_id,$customtext_txtarea);
            if(!empty($gpt_rephraser)){
                $status_Code = 200;
            }
        } else {
            $gpt_rephraser = save_as_draft($custom_post_id,$prompt);
            if(!empty($gpt_rephraser)){
                $status_Code = 200;
            }
        }
        wp_send_json(['content_array'=>$gpt_rephraser,'custom_post_id'=>$custom_post_id,'status_Code'=>$status_Code], JSON_FORCE_OBJECT);
    }
    
}
add_action('wp_ajax_update_scrape_post_and_gpt_call', 'update_scrape_post_and_gpt_call');
function save_as_draft($custom_post_id,$prompt) {
    global $chat_gpt_rephraser;
    $title = 'draft-'.$custom_post_id;

    $gpt_post_id = $chat_gpt_rephraser->get_gpt_post_from_custom_post($custom_post_id);
    if(!$gpt_post_id){
        $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post('',$prompt,$custom_post_id,'save-archive-articles-as-draft',$title,null,true,null,'From Fetch from URL');
    } else {
        $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post('',$prompt,$custom_post_id,'save-archive-articles-as-draft',$title,null ,null,'From Fetch from URL');
    }
    return $gpt_post_id;
}

function update_scrape_custom_post($updateFlag = false){
    global $chat_gpt_rephraser;

    // $fetchUrlLink = preg_replace("#^(https?://)#i", "", $_POST['fetchUrlLink']);

    $fetchUrlTitle = ($_POST['fetchUrlTitle']);
    $fetchURLContent = ($_POST['fetchURLContent']);

    $scrapped_post_ids = [];

    if(is_null(($_POST['custom_post_id']))) {
        if($updateFlag && isset($_POST['c_id']) && !empty($_POST['c_id'])) {
            $custom_post_id = $_POST['c_id'];
            $scrapped_post_ids = $chat_gpt_rephraser->get_scraped_post_from_custom_post($custom_post_id);
        } else {
            $custom_post_id = NULL;
        }
    } else {
        $custom_post_id = $_POST['custom_post_id'];
    }
    

    if(is_null($_POST['scrapped_post_id'])){
        if($updateFlag && isset($scrapped_post_ids) && !empty($scrapped_post_ids)) {
            $scrapped_post_id = $scrapped_post_ids;
        } else {
            $scrapped_post_id = NULL;
        }
    } else {
        $scrapped_post_id = explode(",", $_POST['scrapped_post_id']);
    }
    

    $prompt = $_POST['prompt'];
    $gpt_model_id = $_POST['gpt_model_id'];
    $customtxt_url = preg_replace("#^(https?://)#i", "", $_POST['customtxt_url']);
    $customtxt_title = $_POST['customtxt_title'];
    $customtext_txtarea = $_POST['customtext_txtarea'];

    // Update the 'null' string throughout the project
    if(!empty($scrapped_post_id) && !empty($custom_post_id) && $scrapped_post_id !== 'null' && $custom_post_id !== 'null'){
  
        for ($i=0; $i < sizeof($scrapped_post_id); $i++) { 
            if(!empty($fetchUrlTitle[$i]) && !empty($fetchURLContent[$i]) ){
 
                $update_post = array(
                    'ID'           => $scrapped_post_id[$i],
                    'post_title'   => $fetchUrlTitle[$i],
                    'post_content' => $fetchURLContent[$i],
                );
                $post_updated = wp_update_post($update_post) ;
            }
            else{
                $status_Code = 503;
            }
        }
        if(!empty($customtxt_title) && !empty($customtext_txtarea) && !empty($customtxt_url)){
            $new_row = array(
                "custom_source_title" => $customtxt_title,
                "custom_source_content" => $customtext_txtarea,
                "custom_source_url" => $customtxt_url
            );
            $new_rows[] = $new_row;
            update_field("source_repeater", $new_rows, $custom_post_id);
        }
        
    } else if(!empty($custom_post_id) &&  $custom_post_id !== 'null') {
        $new_row = array(
            "custom_source_title" => $customtxt_title,
            "custom_source_content" => $customtext_txtarea,
            "custom_source_url" => $customtxt_url
        );
        $new_rows[] = $new_row;
        update_field("source_repeater", $new_rows, $custom_post_id);
    }
    else{
        $new_custom_post_data = array(
            'post_title' => $customtxt_title,
            'post_type' => 'custom_posts',
            'post_status' => 'publish',
        );

        $custom_post_id = wp_insert_post($new_custom_post_data);
        if ($custom_post_id && !is_wp_error($custom_post_id)) {
            $new_row = array(
                "custom_source_title" => $customtxt_title,
                "custom_source_content" => $customtext_txtarea,
                "custom_source_url" => $customtxt_url
            );
            $new_rows[] = $new_row;
            update_field("source_repeater", $new_rows, $custom_post_id);
        }

        save_as_draft($custom_post_id,$prompt);
    }

    return [
        'custom_post_id'    => $custom_post_id,
        'prompt'    => $prompt,
        'gpt_model_id'    => $gpt_model_id,
        'customtext_txtarea'    => $customtext_txtarea,
    ];
}

function sessionManager() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }  
    $storedData = [];  
    if (!isset($_POST['data']) || !is_array($_POST['data'])) {
        echo json_encode(['error' => 'No valid data received']);
        wp_die();
    }  
    $data = $_POST['data'];
    foreach ($data as $index => $value) {
        if (!empty($index) && !empty($value)) {
            $fieldID = 'field_' . $index;
            $_SESSION[$fieldID] = $value;
            $storedData[$fieldID] = $_SESSION[$fieldID];
        }
    }
    
    echo json_encode($storedData);
    wp_die();
}

add_action('wp_ajax_sessionManager', 'sessionManager');
add_action('wp_ajax_nopriv_sessionManager', 'sessionManager');


function destroy_session(){
    session_start();  
    session_destroy();  
    echo json_encode(["status" => "success", "message" => "Session destroyed."]);
    exit; 
}

add_action('wp_ajax_destroy_session', 'destroy_session');
add_action('wp_ajax_nopriv_destroy_session', 'destroy_session');