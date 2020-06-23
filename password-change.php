<?php
session_start();

require 'DBWrapper.php';

$userID = $_SESSION['userID'];
$oldPassword = "";
$newPassword = "";
$passwordError = "";


function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


//process form data
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $oldPassword= password_hash(trim($_GET["old"]), PASSWORD_DEFAULT);
    $newPassword1 = trim($_GET["new1"]);
    $newPassword2 = trim($_GET["new2"]);

    if($newPassword1 == $newPassword2){
        $newPassword = password_hash($newPassword1, PASSWORD_DEFAULT);
    }else{
        $passwordError = "Passwords Don't Match"; //new passwords don't match
    }
        
}

//get old password from DB
$passwordQuery = "SELECT password FROM users WHERE user_id=" . $userID;
$passwordResult = DBwrapper::DBselect($passwordQuery)[0]['password'];


if(password_verify($oldPassword,$passwordResult)){
    $passwordError = "Incorrect current password"; //incorrect current password
}

if($passwordError == ""){
    $passwordUpdate = "UPDATE users SET password=\"" . $newPassword . "\" WHERE user_id=" . $userID;
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

