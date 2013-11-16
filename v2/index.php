<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
session_start();
########## Google Settings.. Client ID, Client Secret #############
########## MySql details #############
include("config.php");

include("User.php");

$client = ApiManager::go()->createClient();
$oauth2 = new Google_Oauth2Service($client);
$cal = new Google_CalendarService($client);
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    return;
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
}
?>
<!doctype html>
<html>
    <head><meta charset = "utf-8">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/style.css"/>
        <script>
            $(function() {
                $("#datepicker, #datepicker2").datepicker();
            });
        </script>
    </head>
    <body>
        <header><h1>CLNDR</h1></header>
        <?php
        if ($client->getAccessToken()) {
            $userinfo = $oauth2->userinfo->get();
            // These fields are currently filtered through the PHP sanitize filters.
            // See http://www.php.net/manual/en/filter.filters.sanitize.php
            $email = filter_var($userinfo['email'], FILTER_SANITIZE_EMAIL);
            $full_name = filter_var($userinfo['name'], FILTER_SANITIZE_STRING);

            $_SESSION['primary_id'] = $email;
            $img = filter_var($userinfo['picture'], FILTER_VALIDATE_URL);
            $checkExisting = $pdo->query("SELECT * FROM `users` WHERE `primary_email` = '$email'");
            if ($checkExisting->rowCount() <= 0) {
                try {
                    $insertq = $pdo->prepare("INSERT INTO `users` (`api_token`, `primary_email`, `picture`) VALUES (?,?,?)");
                    $insertq->execute(array($client->getAccessToken(), $email, $img));
                } catch (PDOEXCEPTION $e) {
                    echo $e;
                }
            }
            ?>
            <img src='<?= $img ?>' width='100px' height='120px'/>
            <b style='vertical-align:top'><?= $full_name ?></b>
            <a class='logout' href='?logout'>Logout</a>
            <br/>Welcome to <b>CLNDR</b> to get started select friends:<br/>
            <form method='post' action='selectTimes.php'>

                <select name='friends'>
                    <option>Friend List</option>
                    <option>Just kidding we know you don't have any</option>
                    <option> We'll just assume sean</option>

                </select><br/>
                <input name="friend[]" value="rabautse@gmail.com" type="hidden"/>
                <input name="friend[]" value="serabaut@gmail.com" type="hidden"/>

                But really: Choose a time and date to hang out with them! <br/>
                Start Date: <input type="text" name="rangestart" id="datepicker"/>
                <br/>
                End Date: <input type="text" name="rangeend" id="datepicker2"/><br/>
                <input type="submit" name="go" value="Find Times!"/>
            </form>
            <?php
            // The access token may have been updated lazily.
            $_SESSION['token'] = $client->getAccessToken();
        } else {

            $authUrl = $client->createAuthUrl();
            ?>

            <?php
            print "<a class='login' href='$authUrl'>Connect Me!</a>";
            ?>
            <?php
        }
        ?>

    </body>
</html>