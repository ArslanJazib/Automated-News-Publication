<?php

namespace GMAIL\Controllers;

use GMAIL\Controllers\EmailAttachmentManagers;

class EmailContentManagers {
    private $emailAttachmentManager;

    public function __construct() {
        $this->emailAttachmentManager = new EmailAttachmentManagers();
    }

    public function process_simple_content ($service, $part, $subject, $messageId, $labelIdOfPress){
        // get the email content
        $data = $part->body->data;
        
        // decode the email content
        $content = base64_decode(str_replace(array('-', '_'), array('+', '/'), $data)); 

        return $content;
    }

    public function process_mixed_content ($service, $part, $subject, $messageId, $labelIdOfPress){

        // Get text from mixed email
        if(isset($part->parts)){
            foreach($part->parts as $emcontent){
                if($emcontent->mimeType=="text/plain"){
                    $data=$emcontent->body->data;            
                    // decode the email content
                    $textContent = base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));          
                }
            }
        }

        $attachmentContent = $this->emailAttachmentManager->process_attachment($service, $part, $subject, $messageId, $labelIdOfPress);

        $content = $textContent.' '.$attachmentContent;

        return $content;
    }

}
