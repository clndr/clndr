<?php

$db = array();
$db['server'] = "127.0.0.1";
$db['user'] = "clndr_admin";
$db['pass'] = "~{nLrovZ+,Rt";
$db['db'] = "clndr_clndr";
$google_client_id   = '593963401069.apps.googleusercontent.com';
$google_client_secret   = '87phJdDjQXzFBHJLFlCpq2D1';
$google_redirect_url    = 'http://clndr.royalbtc.com/';
$google_developer_key   = 'AIzaSyA__Jz4znnJxpZVDjhznMaLu9H4dXR9Mf8';

//$db['link'] = mysql_connect($db['server'], $db['user'], $db['pass']);
//mysql_select_db($db['db'],$db['link']);
try {
$pdo = new PDO('mysql:host='.$db['server'].';dbname='.$db['db'].';charset=utf8', $db['user'], $db['pass'], 
        array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    echo $e;
}