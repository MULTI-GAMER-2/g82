<?php
// Retrieve the token from the POST request
header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

$token = isset($_POST['token']) ? $_POST['token'] : null;

// Create a response array
$response = array();

// Validate if the token exists and is not empty
if ($token) {
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
    $p = isset($_POST['p']) ? $_POST['p'] : null;
    
    if ($p) {
      $data = array('p' => $p);
      
      $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
          'timeout' => 5 // 5 seconds timeout
        )
      );
      
      $context  = stream_context_create($options);
      $response['status'] = 'success';
      $response['message'] = file_get_contents($url, false, $context);
    } else {
      // Parameter 'p' is missing or empty
      $response['status'] = 'error';
      $response['message'] = 'Missing or empty parameter';
    }
  } else {
    // Token has expired
    $response['status'] = 'error';
    $response['message'] = 'Token expired';
  }
} else {
  // Token is missing or empty
  $response['status'] = 'error';
  $response['message'] = 'Missing or empty token';
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
