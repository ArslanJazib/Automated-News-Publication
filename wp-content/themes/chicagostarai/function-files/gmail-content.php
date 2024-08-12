<?php

// create scrape and custom post
function send_gmail_content_to_gpt()
{
    global $chat_gpt_rephraser;

    $gmail_post_id = $_POST['gmail_post_id'];

    $gpt_rephraser = $chat_gpt_rephraser->call_gpt_api_for_gmail_controller($gmail_post_id);

    $status_Code = (!empty($gpt_rephraser)) ? 200 : 404;
    
    wp_send_json(['content_array'=>$gpt_rephraser,'gmail_post_id'=>$gmail_post_id,'status_Code'=>$status_Code], JSON_FORCE_OBJECT);

}
add_action('wp_ajax_send_gmail_content_to_gpt', 'send_gmail_content_to_gpt');
?>