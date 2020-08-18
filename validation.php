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
?>
