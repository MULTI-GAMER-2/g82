<?php
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
  $datetime = DateTime::createFromFormat('U', floor(intval($timestamp) / 1000));

  // Calculate the time difference between the current time and the token's timestamp
  $currentDatetime = new DateTime();
  $timeDiff = $currentDatetime->getTimestamp() - $datetime->getTimestamp();

  // Set the time limit in seconds (5 seconds in this example)
  $timeLimit = 5;

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
  // Required parameters are missing
  $response['status'] = 'error';
  $response['messsage'] = 'Missing required parameters';
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
