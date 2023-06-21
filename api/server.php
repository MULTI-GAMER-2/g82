<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 60");    // cache for 1 minute
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: *");

if (isset($_GET['s'])) {
    $s = $_GET['s'];

    $url = "https://g82.me/t?p=" . $s;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $resp = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        $response = array(
            'status' => 'error',
            'message' => 'cURL request failed: ' . $error
        );
    } else {
        // No need to encode the response again, as it is already expected to be in JSON format
        $response = $resp;
    }

    echo $response;
}
?>
