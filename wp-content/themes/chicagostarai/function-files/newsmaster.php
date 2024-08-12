<?php

// create scrape and custom post
function newsmaster_content_action()
{
    global $chat_gpt_rephraser;
    $status_Code = '';
    $prompt_textarea = $_POST['prompt_textarea'] ?? NULL;
    $text_textarea = $_POST['text_textarea'] ?? NULL;
    $gpt_post_id = $_POST['gpt_post_id'] ?? NULL;
    $get_secondary_taxonomy = $_POST['secondarytaxonomy'] ?? NULL;
    $inputTextValue = $_POST['inputTextValue']?? NULL;
    $draftGPTPostID = $_POST['draftGPTPostID']?? NULL;
    if (!empty($prompt_textarea) ) { //&& !empty($text_textarea)
                $static_prompt='Output Will Be in HTML with Bullets Points';

        $input_data = array(
            'text_textarea'=>$text_textarea,
            'prompt_textarea'=>$prompt_textarea.$static_prompt

        );

        if($_POST['draft'] !== 'true') {
            $gpt_response = $chat_gpt_rephraser->chat_gpt_request_newsmaster($input_data);
            if(!empty($gpt_response)){
                $status_Code = 200;
            }else{
                $status_Code = 404;
            }
        } else {
            if($gpt_post_id){
                $gpt_response = update_as_newsmaster_draft($gpt_post_id , $input_data , $get_secondary_taxonomy , $inputTextValue ,$draftGPTPostID);
            }else{
                $gpt_response = save_as_newsmaster_draft($input_data , $get_secondary_taxonomy , $inputTextValue , $draftGPTPostID);
            }
            if(!empty($gpt_rephraser)){
                $status_Code = 200;
            }
        }


    }
    else{
        $status_Code = 503;
    }
    wp_send_json(['status_Code'=>$status_Code,'gpt_response'=>$gpt_response]);
}
add_action('wp_ajax_newsmaster_content_action', 'newsmaster_content_action');



// create gpt post
function save_gpt_post_newsmaster()
{
    global $chat_gpt_rephraser;
    $status_Code = '';
    $newsmaster_content = $_POST['newsmaster_content'];
    $taxanomy = $_POST['taxanomy'];
    $prompt = $_POST['prompt'];
    $input_text_value = $_POST['inputTextValue'];
    $secondary_taxonomy = $_POST['secondaryTaxonomy'];
    $created_gpt_post_id = $_POST['created_gpt_post_id'];
   
    if (!empty($newsmaster_content)) {
 
        $title = getFirstSentence($newsmaster_content);
        $flag = "from_newsmaster_".$_POST['created_gpt_post_id'];
        if(empty($created_gpt_post_id)){
            $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post($newsmaster_content,$prompt,NULL,$taxanomy,$title,$flag,$draft_flag=NULL,$input_text_value,$secondary_taxonomy);
        }else{
            $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post($newsmaster_content,$prompt,NULL,$taxanomy,$title,$flag,$input_text_value,$secondary_taxonomy,$created_gpt_post_id);
        }
        $gpt_post_permalink = get_permalink($gpt_post_id);
        if(!empty($gpt_post_id)){
            $status_Code = 200;
        }
        else{
            $status_Code = 502;
        }
    }
    else{
        $status_Code = 503;
    }
    wp_send_json(['gpt_post_id'=>$gpt_post_id,'status_Code'=>$status_Code,'gpt_post_permalink'=>$gpt_post_permalink]);

}
add_action('wp_ajax_save_gpt_post_newsmaster', 'save_gpt_post_newsmaster');

function update_gpt_post_newsmaster()
{
    global $chat_gpt_rephraser;
    $status_Code = '';
    $newsmaster_content = $_POST['newsmaster_content'];
    $taxanomy = $_POST['taxanomy'];
    $prompt = $_POST['prompt'];
    $input_text_value = $_POST['inputTextValue'];
    $secondary_taxonomy = $_POST['secondaryTaxonomy'];
    $created_gpt_post_id = $_POST['created_gpt_post_id'];
   
    if (!empty($newsmaster_content)) {

        $title = getFirstSentence($newsmaster_content);
        $flag = "from_newsmaster_".$_POST['gpt_post_id'];
        if(empty($created_gpt_post_id)){
            $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post($newsmaster_content,$prompt,NULL,$taxanomy,$title,$flag=NULL,$draft_flag=NULL,$input_text_value,$secondary_taxonomy);
        }else{
            $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post($newsmaster_content,$prompt,NULL,$taxanomy,$title,$flag,$input_text_value,$secondary_taxonomy,$created_gpt_post_id);
        }
        $gpt_post_permalink = get_permalink($gpt_post_id);
        if(!empty($gpt_post_id)){
            $status_Code = 200;
        }
        else{
            $status_Code = 502;
        }
    }
    else{
        $status_Code = 503;
    }
    wp_send_json(['gpt_post_id'=>$gpt_post_id,'status_Code'=>$status_Code,'gpt_post_permalink'=>$gpt_post_permalink]);

}
add_action('wp_ajax_update_gpt_post_newsmaster', 'update_gpt_post_newsmaster');


// getting first sentence from string
function getFirstSentence($paragraph) {
    // Match the first sentence using a regular expression
    preg_match('/^.*?([^.!?]+[.!?])/', $paragraph, $matches);

    return isset($matches[1]) ? $matches[1] : '';
}
function save_as_newsmaster_draft($input_data , $get_secondary_taxonomy , $inputTextValue , $draftGPTPostID) {
    global $chat_gpt_rephraser;
    $prompt = '';
    $title = 'draft-'.mt_rand();
    $secondary_taxonomy = $get_secondary_taxonomy;
    $input_text_value = $inputTextValue;
    $draftGPTPostID = $draftGPTPostID;
    if(isset($_POST['prompt_textarea'])){
        $prompt .= $_POST['prompt_textarea'] ;
    }
    if(empty($draftGPTPostID)){
        $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post('',$prompt,null,'save-archive-articles-as-draft',$title ,null,true,$input_text_value,$secondary_taxonomy);
    } else {
        $flag = "from_newsmaster_".$_POST['gpt_post_id'];
        $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post('',$prompt,null,'save-archive-articles-as-draft',$title , $flag,$input_text_value,$secondary_taxonomy,null,$draftGPTPostID);
    }
    return $gpt_post_id;
}

function update_as_newsmaster_draft($gpt_post_id , $input_data , $get_secondary_taxonomy , $inputTextValue , $draftGPTPostID){
    global $chat_gpt_rephraser;
    $prompt = '';
    $title = 'draft-' . $gpt_post_id;
    $secondary_taxonomy = $get_secondary_taxonomy;
    $input_text_value = $inputTextValue;
    $draftGPTPostID = $draftGPTPostID;

    if(isset($_POST['prompt_textarea']) && isset($_POST['text_textarea'])){
        $prompt .= $_POST['prompt_textarea'] ." :". $_POST['text_textarea']."" ;
    }

    if(empty($gpt_post_id)){
        $gpt_post_id = $chat_gpt_rephraser->create_custom_gpt_post('',$prompt,null,'save-archive-articles-as-draft',$title ,null,true,$input_text_value,$secondary_taxonomy);
    }else{
        $flag = "from_newsmaster_" . $gpt_post_id;
        if(!empty($flag)){
            $created_gpt_post_id = $gpt_post_id;
        }
        $gpt_post_id = $chat_gpt_rephraser->update_custom_gpt_post('',$prompt,null,'save-archive-articles-as-draft',$title ,$flag,$input_text_value,$secondary_taxonomy,$created_gpt_post_id,$draftGPTPostID);
    }

    return $gpt_post_id;
}

function sessionManagerNewsMaster() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    ob_start();
    $storedData = [];
    if (!isset($_POST['data']) || !is_array($_POST['data'])) {
        echo json_encode(['error' => 'No valid data received']);
        wp_die();
    }
    $data = $_POST['data'];
    foreach ($data as $item) {
        if (isset($item['fieldID']) && isset($item['value'])) {
            $fieldID = $item['fieldID'];
            $value = $item['value'];          
            $_SESSION[$fieldID] = [$value];            
            $storedData[$fieldID] = $_SESSION[$fieldID];
        }
    }
    ob_end_clean();
    echo json_encode($storedData);
    wp_die(); 
}
add_action('wp_ajax_sessionManagerNewsMaster', 'sessionManagerNewsMaster');
add_action('wp_ajax_nopriv_sessionManagerNewsMaster', 'sessionManagerNewsMaster');

function destroy_session_newsmaster(){
    session_start();  
    session_destroy();  
    echo json_encode(["status" => "success", "message" => "Session destroyed."]);
    exit; 
}

add_action('wp_ajax_destroy_session_newsmaster', 'destroy_session_newsmaster');
add_action('wp_ajax_nopriv_destroy_session_newsmaster', 'destroy_session_newsmaster');