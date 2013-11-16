<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
session_start();
########## Google Settings.. Client ID, Client Secret #############
########## MySql details #############
include("config.php");
include("User.php");
include("clndr_functions.php");
?>
<!doctype html>
<html>
    <head><meta charset = "utf-8">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <link rel="stylesheet" href="css/style.css"/>
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script>
            $(function() {
                $("#datepicker, #datepicker2").datepicker();
            });
        </script>
    </head>
    <body>
        <header><h1>CLNDR</h1></header>

        <?php
        if (!$_SESSION['token']) {
            //lawls
            echo "You must login first silly bear, <a href='index.php'>Login</a>";
        }

        $userController[] = array($_SESSION['primary_id'], $_SESSION['token']);

        if (isset($_POST['go'])) {
            if (isset($_POST['friend']) && count($_POST['friend']) > 0) {
                if (isset($_POST['rangestart']) && isset($_POST['rangeend'])) {
                    $rangeStart = strtotime($_POST['rangestart']);
                    $rangeEnd = strtotime($_POST['rangeend']);
                    $difference = $rangeEnd - $rangeStart;

                    $indexDays = $difference / (60 * 60 * 24);
                    $friends = $_POST['friend'];
                    $inQuery = implode(',', array_fill(0, count($friends), '?'));
                    echo count($friends);
                    try {
                        $getFriends = $pdo->prepare("SELECT * FROM `users` WHERE `primary_email` IN ($inQuery)");

                        $getFriends->execute($friends);
                        if ($getFriends->rowCount() == count($friends)) {
                            $matchFriends = $getFriends->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($matchFriends as $row => $userInfo) {
                                $primaryID = $userInfo['primary_email'];
                                $ap = $userInfo['api_token'];
                                $userController[] = array($primaryID, $ap);
                            }
                        }
                        $busy = array();
                        foreach ($userController as $friendNum => $info) {
                            $primaryID = $info[0];
                            $api_token = $info[1];
                            $userClass = new User($info[0], $info[1]);


//                            $userController[$email]['freebusy'] = $userClass->getFreeBusy($rangeStart, $rangeEnd);
                            echo "$primaryID --> <br/>";

                            $busy[$primaryID] =  $userClass->getFreeBusy($rangeStart, $rangeEnd);
                        }

                        foreach ($busy as $key => $busyDates) {
                            for ($i = 0; $i <= ($indexDays * 3600); $i++) {
                                $timeArray[$key][$i] = 1;
                            }
                            foreach ($busyDates as $times) {
                                $startTimeEvent = strtotime($times['start']);
                                $endTimeEvent = strtotime($times['end']);
                                echo "<br/>---------------<br/>" . $key . "<br/>---------------<br/>";
                                echo "Minutes: " . ($endTimeEvent - $startTimeEvent) / (60) . "<br/>";
                                echo "Subtract start date: " . ($startTimeEvent - $rangeStart) / (60) . "<br/>";
                                echo "Subtract end date: " . ($endTimeEvent - $rangeStart) / (60) . "<br/>";

                                $startAndEndTime[$key][] = array(($startTimeEvent - $rangeStart) / (60), ($endTimeEvent - $rangeStart) / (60));
                            }
                        }

                        print_r($startAndEndTime);
                        foreach ($startAndEndTime as $person => $eventNum) {
                            foreach ($eventNum as $times) {
                                for ($i = $times[0]; $i <= $times[1]; $i++) {
                                    $timeArray[$person][$i] = 0;
                                }
                            }
                        }
//
                       print_r(and_all_combinations(array($timeArray)));
                    } catch (PDOException $e) {
                        echo $e;
                    }
                }
            }
        }
        ?>
    </body>
</html>

