<?php

namespace GMAIL\Controllers;

use GMAIL\Controllers\EmailContentManagers;
use GMAIL\Controllers\TokenManagers;
use GMAIL\Models\GmailApiSetting;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\ModifyMessageRequest;
class GmailInboxParser {
    private $client;
    private $apiSettings;
    private $apiSettingModel;
    private $tokenManagerController;
    private $emailContentManager;

    public function __construct() {
        $this->tokenManagerController = TokenManagers::getInstance();

        $this->emailContentManager = new EmailContentManagers();

        // Initialize the GmailApiSetting model
        $this->apiSettingModel = new GmailApiSetting('gmail_api_settings');
        $this->apiSettings = $this->apiSettingModel->get_api_settings();

        // Initialize the Google client
        $this->initializeGoogleClient();
    }

    private function initializeGoogleClient() {
        // Create a new Google client instance and set your configuration
        $this->client = new Client();

        if(!$this->apiSettings){
            return false;
        }else{
            $this->client->setAuthConfig(array(
                'client_id' => $this->apiSettings['client_id'],
                'project_id' => $this->apiSettings['project_id'],
                'auth_uri' => $this->apiSettings['auth_uri'],
                'token_uri' => $this->apiSettings['token_uri'],
                'auth_provider_x509_cert_url' => $this->apiSettings['auth_provider_x509_cert_url'],
                'client_secret' => $this->apiSettings['client_secret'],
                'redirect_uris' => $this->apiSettings['redirect_uris'],
            ));
    
            $this->client->addScope(Gmail::MAIL_GOOGLE_COM);
            $this->client->setRedirectUri($this->apiSettings['redirect_uris']);
            $this->client->setAccessType('offline');
            $this->client->setIncludeGrantedScopes(true);
        }   
    }

    public function get_auth_url(){
        return $this->client->createAuthUrl(); 
    }

    public function create_access_token($authCode){
        return $this->tokenManagerController->set_access_token($this->client,$authCode);
    }

    public function processEmails() {
        $this->tokenManagerController->set_access_token($this->client,null);
        $this->fetchAndProcessEmails();
    }

    private function fetchAndProcessEmails() {
        $service = new Gmail($this->client);

        // get the gmail results
        $results = $service->users_messages->listUsersMessages('me', [
            'labelIds' => $this->apiSettings['label_id'],
            'q' => $this->apiSettings['search_query'],
        ]);

        $labels = $service->users_labels->listUsersLabels("me");

        $labelIdOfPress = "";

        foreach ($labels as $label) {
            if ($label->name == $this->apiSettings['assign_labels_to_fetched_emails']) {
                $labelIdOfPress = $label->id;
                break;
            }
        }

        $email_count = 0;
        // get the mail details
        foreach ($results->getMessages() as $message) {
            if ($email_count < (int)$this->apiSettings['max_limit']) {
                $emailDetails = $service->users_messages->get('me', $message->getId());
                $messageId = $emailDetails->getId();
                $mailLabels = $emailDetails->getLabelIds();

                // Check if the email has the custom label assigned
                $hasCustomLabel = in_array($labelIdOfPress, $mailLabels);

                // Process the email only if it doesn't have the custom label
                if (!$hasCustomLabel) {
                    $payload = $emailDetails->getPayload();
                    $this->processEmailPayload($payload, $service, $messageId, $labelIdOfPress);

                    $addNewLabel = new ModifyMessageRequest();
                    $addNewLabel->setAddLabelIds([$labelIdOfPress]);

                    // Assign the custom label to the email
                    $addlabels = $service->users_messages->modify('me', $messageId, $addNewLabel);
                }

                $email_count = $email_count + 1;
            }else{
                break;
            }
        }
    }

    private function processEmailPayload($payload, $service, $messageId, $labelIdOfPress) {
        $subject = $this->getEmailSubject($payload);

        foreach($payload->parts as $part){

            if ($payload->mimeType == "multipart/alternative") {
                $postContent = $this->emailContentManager->process_simple_content($service, $part, $subject, $messageId, $labelIdOfPress);
            }

            if ($payload->mimeType == "multipart/mixed" || $payload->mimeType == "multipart/related") {
                $postContent = $this->emailContentManager->process_mixed_content($service, $part, $subject, $messageId, $labelIdOfPress);
            }

            $this->insert_gmail_post($subject , $postContent);
        }
    }

    private function getEmailSubject($payload) {
        foreach ($payload->headers as $header) {
            if ($header->name == "Subject") {
                return $header->value;
            }
        }
        return "";
    }

    private function insert_gmail_post($title, $content) {
        // Check if post with the given title already exists
        $post_id = post_exists($title);
    
        // If post doesn't exist, insert a new one
        if (!$post_id) {
            $gamil_post = array(
                'post_title'    => $title,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type'     => 'gmail_posts'
            );
    
            // Insert the new post
            wp_insert_post($gamil_post);
        }
    }    
}