<?php

// Include Mailchimp API class
  require_once('MCAPI.class.php');
               
  // Your API Key: http://admin.mailchimp.com/account/api/ 
  $api = new MCAPI('ffb308011b120094f69e5611c1586be0-us2');
               
  // Your List Unique ID: http://admin.mailchimp.com/lists/ (Click "settings")
  $list_id = "26e62613fd";
  
  // Variables in your form that match up to variables on your subscriber
  // list. You might have only a single 'name' field, no fields at all, or more
  // fields that you want to sync up.
  $merge_vars = array(
  	'EMAIL' => $_POST['email'],
    'FNAME' => $_POST['firstname'],
    'LNAME' => $_POST['lastname']
  );
               
  // SUBSCRIBE TO LIST
  if ( $api->listSubscribe($list_id, $_POST['email'], $merge_vars) === true ){
    $mailchimp_result = 'Success! Check your email to confirm sign up.';
  } else {
    $mailchimp_result = 'Error: ' . $api->errorMessage;
  }
  print "hello";
?>
