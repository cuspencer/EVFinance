<?php
session_start();

require 'header.php';
require 'DBwrapper.php';


function randomPassword( $length = 8 ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
  $length = rand(10, 16);
  $password = substr( str_shuffle(sha1(rand() . time()) . $chars ), 0, $length );
  return $password;
}//end randomPassword()

function showForm($errMessage = ""){

  $strToReturn = "";

  $strToReturn .= "<DIV id=\"maincontent\" class=\"w3-container\">";
  if($errMessage == ""){
      $strToReturn .= "<H3>Reset Password</H3>";
  }else{
      $strToReturn .= "<H3>" . $errMessage . "</H3>";
  }
  $strToReturn .= "<FORM id=\"pwd-reset\" name=\"pwd-reset\" action=\"\" method=\"POST\">";
  $strToReturn .= "<TABLE>";
  $strToReturn .= "<TR><TD>User Email</TD>";
  $strToReturn .= "<TD><INPUT required type=\"text\" name=\"username\" autocomplete=\"username\"/></TD>";
  $strToReturn .= "<TD><DIV id=\"usernameErr\" class=\"errMsg\"/></TD></TR>";
  $strToReturn .= "<TR><TD>System</TD>";
  $strToReturn .= "<TD><SELECT required id=\"dbSystem\" name=\"dbSystem\">";

  //populate options from XML file
  $systems=simplexml_load_file("sysinfo.xml") or die("Error: Cannot create object");

  if($systems->count() > 1){
      $strToReturn .= "<option value=\"\" selected disabled>Select a system...</option>";
  }
  foreach($systems->system as $s){
      $sysname = $s->name;
      $strToReturn .= "<option>$sysname</option>";
  }

  $strToReturn .= "</SELECT></TD><TD/></TR>";
  $strToReturn .= "<TR><TD></TD><TD><INPUT type=\"submit\" value=\"Reset Password\"/></TD><TD/></TR>";
  $strToReturn .= "</TABLE></FORM></DIV>";

  return $strToReturn;
}//end function showForm()

function resetSuccess(){
  $strToReturn = "<DIV id=\"maincontent\" class=\"w3-container\">";
  $strToReturn .= "<DIV><H4>Password reset successful. Please check your email for your temporary password " .
                  "and <A HREF=\"login.php\">log in</A> again.</H4>";
  $strToReturn .= "</DIV>";
  return $strToReturn;
}

$errMessage = "";
//process form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = test_input($_POST["username"]);
  $sysname = $_POST["dbSystem"];
  $result = array();

  //get DB info from XML
  $xmlpathstring = "/systems/system[name = \"$sysname\"]";
  $systems=simplexml_load_file("sysinfo.xml") or die("Error: Cannot create object");
  $dbsys = $systems->xpath($xmlpathstring)[0];

  $_SESSION['dbname'] = (string)$dbsys->database;
  $_SESSION['dblogin'] = (string)$dbsys->login;
  $_SESSION['dbpassword'] = (string)$dbsys->password;

  //check if username exists
  if(test_email($email)){
    $sqlQuery = "SELECT user_id FROM users WHERE email = \"" . $email . "\"";
    $result = DBwrapper::DBselect($sqlQuery);
  } 

  //does user exist?
  if(count($result)>0){
    $userID = $result[0]['user_id'];

    //create, send, update password
    $newPassword = randomPassword(8);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateQuery = "UPDATE users SET password = \"" . $hashedPassword . "\" WHERE user_id = " . $userID;
    $updateResult = DBwrapper::DBupdate($updateQuery);

    if ($updateResult){
      //send email
      $subject = "Finance Portal password reset";

      $message = "<b>Your password has been reset.</b>";
      $message .= "<h4>Your new password is: ";
      $message .= $newPassword . "</h4> \n ";

      $header = "From:envivo.salamanca@gmail.com \r\n";
      $header .= "MIME-Version: 1.0\r\n";
      $header .= "Content-type: text/html\r\n";

      $retval = mail ($email,$subject,$message,$header);

      if( $retval == true ) {
        //echo "Message sent successfully...";
        echo resetSuccess();
      }else {
        echo "Message could not be sent...";
      }
    }else{
      echo "Error resetting password.";
    }
  }
  else{
    echo showForm("User doesn't exist. Try again!");
  }

}//end form processing
else{
  echo showForm();
}
require 'footer.php';
?>
