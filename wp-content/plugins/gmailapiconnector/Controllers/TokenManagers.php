<?php

namespace GMAIL\Controllers;

use GMAIL\Models\TokenManager;

class TokenManagers {
    private static $instance;
    private $tokenManager;

    private function __construct() {
        $this->tokenManager = new TokenManager('gmail_tokens');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new TokenManagers();
        }
        return self::$instance;
    }

    public function set_access_token($client, $gmail_auth_code = null) {
        $tokenStatus = $this->tokenManager->get_tokens_from_database();

        if ($tokenStatus == null) {
            if (isset($gmail_auth_code) && $gmail_auth_code != '') {
                $tokenData = $client->authenticate($gmail_auth_code);
        
                if (isset($tokenData["refresh_token"])) {
                    $client->setAccessToken($tokenData["access_token"]);
                    $this->tokenManager->store_access_tokens($tokenData["access_token"], $tokenData["refresh_token"], $tokenData["expires_in"]);
                    return ['status' => true, 'message' => 'Token Validated Successfully'];
                } else {
                    $errorMessage =  "Please click Sign Out, then click on GMAIL API Settings, last click on Delete all connections you have with GMAIL API Settings, Next back to this page and click Authorize Gmail Connection button";
                    return ['status' => false, 'message' => $errorMessage];
                }
            }else{
                return ['status' => false, 'message' => "Please Press the Authorize Gmail Connection after filling all the credentials."];
            }
        } else {
            if ($tokenStatus->access_token == null || $client->isAccessTokenExpired()) {
                if ($tokenStatus->refresh_token != null) {
                    $newAccessToken = $client->fetchAccessTokenWithRefreshToken($tokenStatus->refresh_token);
                    if (!isset($newAccessToken["error"])) {
                        $client->setAccessToken($newAccessToken["access_token"]);
                        $this->tokenManager->update_access_token($newAccessToken["access_token"], $tokenStatus->id);
                        return ['status' => true, 'message' => 'Token Validated Successfully'];
                    } else {
                        $this->tokenManager->delete_tokens();
                        $errorMessage = "Refresh Token is expired, Please click Sign Out, then click on GMAIL API Settings in the sidebar, last click on Delete all connections you have with GMAIL API Settings, Next back to this page and click Authorize Gmail Connection button.";
                        return ['status' => false, 'message' => $errorMessage];
                    }
                }
            } else {
                $client->setAccessToken($tokenStatus->access_token);
                return ['status' => true, 'message' => 'Token Validated Successfully'];
            }
        }
    }
}