<?php
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
include('mysql_config.php');
session_start();



$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");
// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('593963401069.apps.googleusercontent.com');
$client->setClientSecret('87phJdDjQXzFBHJLFlCpq2D1');
$client->setRedirectUri('http://clndr.royalbtc.com/');
$client->setDeveloperKey('AIzaSyA__Jz4znnJxpZVDjhznMaLu9H4dXR9Mf8');
$cal = new Google_CalendarService($client);
if (isset($_GET['logout'])) {
    unset($_SESSION['token']);
}



if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
    $calList = $cal->calendarList->listCalendarList();
    foreach ($calList as $check) {
        if (is_array($check)) {
            foreach ($check as $checkP) {
                $events = $cal->events;


                if (isset($checkP['primary'])) {
                    if ($checkP['primary'] == 1)
                        $primaryID = $checkP['id'];
                }
            }
        }
    }
    ?>
    <iframe src="https://www.google.com/calendar/embed?src=<?= $primaryID ?>&ctz=America/New_York" style="border: 0" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
    <?
    if ($primaryID) {
        try {
            $insertq = $pdo->prepare("INSERT INTO `users` (`api_token`, `primary_email`) VALUES (?,?)");
            $insertq->execute(array($client->getAccessToken(), $primaryID));
        } catch (PDOEXCEPTION $e) {
            echo $e;
        }
    }
    $events = $cal->events;
    $params = array(
        'timeMin' => date("Y-m-d\TH:i:sP"),
        'maxResults' => 150,
        'singleEvents' => true,
        'orderBy' => "startTime"
        
    );
    print_r($params);
    $event = $events->listEvents($primaryID, $params);
    echo "<h3> $primaryID's Calendar Starting from <b>9/1/2013</b></h3>";
    foreach ($event['items'] as $eventInfo) {
//        print_r($eventInfo);
        echo "<br/> " . $eventInfo['summary'] . " - Starting At " . date("m-d-Y g:ia", strtotime($eventInfo['start']['dateTime']));
        echo " to " . date("m-d-Y g:ia", strtotime($eventInfo['end']['dateTime']));
        if (isset($eventInfo['description'])) {
            echo "<br/><b>Description:</b> " .
            nl2br($eventInfo['description']);
        }
        echo "<br/> <a href='" . $eventInfo['htmlLink'] . "'>View Here</a> ";
    }
    $_SESSION['token'] = $client->getAccessToken();
} else {
    $authUrl = $client->createAuthUrl();
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
}
