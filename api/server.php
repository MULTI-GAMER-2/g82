<?php

header("Access-Control-Allow-Origin: http://127.0.0.1:5500/");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 60");    // cache for 1 minute
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: *");


$url = "https://g82.me/t?p=". $_GET['s'];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);
?>
