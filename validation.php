<?php

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}//end function test_input($data)

function test_email($email){
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function randomPassword( $length = 8 ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
  $length = rand(10, 16);
  $password = substr( str_shuffle(sha1(rand() . time()) . $chars ), 0, $length );
  return $password;
}//end randomPassword()
?>
