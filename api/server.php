<?php
// Retrieve the token from the POST request
$token = $_POST['token'];

// Extract the timestamp from the token
$timestamp = substr($token, -13);

// Convert the timestamp to a DateTime object
$datetime = DateTime::createFromFormat('U', floor($timestamp / 1000));

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
  $p = $_POST['p'];
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
  $response = file_get_contents($url, false, $context);
  
  // Return the response to the JavaScript code
  echo $response;
} else {
  // Token has expired
  echo 'Token expired';
}
?>

