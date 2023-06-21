<?php

if (isset($_SERVER["HTTP_ORIGIN"])) {
    // You can decide if the origin in $_SERVER['HTTP_ORIGIN'] is something you want to allow, or as we do here, just allow all
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
} else {
    // No HTTP_ORIGIN set, so we allow any. You can disallow if needed here
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 600");    // cache for 10 minutes

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT"); // Make sure you remove those you do not want to support
    }

    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    // Just exit with 200 OK with the above headers for OPTIONS method
    exit(0);
}
// From here, handle the request as it is ok

// Retrieve the JSON data from the request body
$json = file_get_contents('php://input');

// Decode the JSON data into an associative array
$data = json_decode($json, true);

// Create a response array
$response = array();

// Check if the required parameters are present
if (isset($data['token']) && isset($data['p'])) {
    $token = $data['token'];
    $p = $data['p'];

    // Extract the timestamp from the token
    $timestamp = substr($token, -13);

    // Convert the timestamp to a DateTime object
    $datetime = DateTime::createFromFormat('U', intval($timestamp) / 1000);

    // Check if the DateTime object was created successfully
    if ($datetime !== false) {
        // Calculate the time difference between the current time and the token's timestamp
        $currentDatetime = new DateTime();
        $timeDiff = $currentDatetime->getTimestamp() - $datetime->getTimestamp();

        // Set the time limit in seconds (5 seconds in this example)
        $timeLimit = 10;

        // Validate the token based on the time limit
        if ($timeDiff <= $timeLimit) {
            // Token is valid within the time limit

            // Send a request to https://g82.me/t with the parameter 'p'
            $url = 'https://g82.me/t';
            $requestData = array('p' => $p);

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($requestData),
                    'timeout' => 5 // 5 seconds timeout
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            // Set the result in the response
            $response['status'] = 'success';
            $response['message'] = $result;
        } else {
            // Token has expired
            $response['status'] = 'error';
            $response['message'] = 'Token expired';
        }
    } else {
        // Invalid token format
        $response['status'] = 'error';
        $response['message'] = 'Invalid token format';
    }
} else {
    // Required parameters are missing
    $response['status'] = 'error';
    $response['message'] = 'Missing required parameters';
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
