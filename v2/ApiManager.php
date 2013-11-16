<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiClient
 *
 * @author bmax
 */
require_once '../google-api-php-client/src/Google_Client.php';
require_once '../google-api-php-client/src/contrib/Google_Oauth2Service.php';
require_once '../google-api-php-client/src/contrib/Google_CalendarService.php';
include("config.php");

class ApiManager extends Google_Client {

    static private $_instance = null;

//put your code here
    public static function go() {
        if (self::$_instance == null) {
            self::$_instance = new ApiManager();
        }
        return self::$_instance;
    }

    function createClient() {
        global $google_client_id, $google_client_secret, $google_developer_key;

        $client = new Google_Client();
        $client->setApplicationName("Google UserInfo PHP Starter Application");

        $client->setClientId($google_client_id);
        $client->setClientSecret($google_client_secret);
        $client->setRedirectUri('http://clndr.royalbtc.com/v2/');
        $client->setDeveloperKey($google_developer_key);
        return $client;
    }

    function getCalService($client) {
        $client->setAccessType("offline");
        return new Google_CalendarService($client);
    }

}
