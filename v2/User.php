<?php

/**
 * Description of user
 *
 * @author bmax
 */
include("ApiManager.php");

class User {

    private $primaryID;
    private $apiClient;
    private $api_token;
    private $calService;

    function __construct($primaryID, $access_token) {
        $this->apiClient = ApiManager::go()->createClient();
        $this->calService = ApiManager::go()->getCalService($this->apiClient);

        $this->api_token = $access_token;
        $this->apiClient->setAccessToken($this->api_token);
        $this->primaryID = $primaryID;
    }

    function getFreeBusy($rangeStart, $rangeEnd, $timezone = "America/Detroit") {
//        return $this->calService->calendarList->listCalendarList();
        $freebusy = $this->calService->freebusy;
        $freebusy_req = new Google_FreeBusyRequest();
        $freebusy_req->setTimeMin(date(DateTime::ATOM, $rangeStart));
        $freebusy_req->setTimeMax(date(DateTime::ATOM, $rangeEnd));
        $freebusy_req->setTimeZone($timezone);
        $item = new Google_FreeBusyRequestItem();
        $item->setId($this->primaryID);
        $freebusy_req->setItems(array($item));
        $query = $freebusy->query($freebusy_req);
        return $query['calendars'][$this->primaryID]['busy'];
    }

}
