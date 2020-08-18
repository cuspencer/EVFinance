<?php
session_start();

require 'DBWrapper.php';
require 'validation.php';

$userID = $_SESSION['userID'];
$oldHashedPassword = "";
$newHashedPassword = "";
$passwordError = "";

//process form data
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $oldPassword = trim($_GET["old"]);
    $newPassword1 = trim($_GET["new1"]);
    $newPassword2 = trim($_GET["new2"]);

    if($newPassword1 == $newPassword2){

      if($oldPassword == $newPassword1){
        $passwordError = "New password must be different from old.";
      }else {
        //get old password from DB
        $passwordQuery = "SELECT password FROM users WHERE user_id=" . $userID;
        $passwordResult = DBwrapper::DBselect($passwordQuery)[0]['password'];
        $oldHashedPassword = password_hash($oldPassword, PASSWORD_DEFAULT);
        $newHashedPassword = password_hash($newPassword1, PASSWORD_DEFAULT);

        if(password_verify($oldHashedPassword,$passwordResult)){
            $passwordError = "Incorrect current password"; //incorrect current password
        }

      }
    }else{
        $passwordError = "Passwords Don't Match"; //new passwords don't match
    }

}//end form process


if($passwordError == ""){
    $passwordUpdate = "UPDATE users SET password=\"" . $newHashedPassword . "\" WHERE user_id=" . $userID;
    $updateResult = DBwrapper::DBupdate($passwordUpdate);

    if($updateResult){
        $passwordError = "Password updated!";
    }else{
        $passwordError = "Database error - contact sys admin"; //Database error
    }
}

echo "<form id=\"passwordForm\">";
echo "<DIV>Current Password <input type=\"password\" name=\"oldPassword\" id=\"oldPassword\"/></DIV>";
echo "<DIV>New Password <input type=\"password\" name=\"newPassword1\" id=\"newPassword1\"/></DIV>";
echo "<DIV>New Password <input type=\"password\" name=\"newPassword2\" id=\"newPassword2\"/></DIV>";
echo "<DIV id=\"passwordError\" class=\"errMsg\">" . $passwordError . "</DIV>";
echo "</form>";


?>
