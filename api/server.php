<?php

header("Access-Control-Allow-Origin: 'http://127.0.0.1:5500/index.html'");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 60");    // cache for 1 minute
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: *");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');

    $url = "https://g82.me/t";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    // For debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);

    if ($resp === false) {
        $response = array(
            'status' => 'error',
            'message' => 'Failed to make the request.'
        );
    } else {
        $decodedResp = json_decode($resp, true);

        if ($decodedResp === null && json_last_error() !== JSON_ERROR_NONE) {
            $response = array(
                'status' => 'error',
                'message' => 'Failed to decode the response JSON. Error: ' . json_last_error_msg()
            );
        } else {
            $response = array(
                'status' => 'success',
                'message' => $decodedResp
            );
        }
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method. Only POST requests are allowed.'
    );
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
