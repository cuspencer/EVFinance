<?php
session_start();

require 'DBwrapper.php';
require 'validation.php';

$userID = "";
$accountID = "";
$createAccount = false;
$addAccountSuccess = false;

$userFName = test_input($_POST["newUserFName"]);
$userLName = test_input($_POST["newUserLName"]);
$userEmail = test_email($_POST["newUserEmail"]);
$userRole = $_POST["newUserRole"];

//ADD ACCOUNT?
if(isset($_POST["createAccount"])){
  $createAccount = true;

  //get next available accountID
  $sqlAccountNumbersSelect = "SELECT acct_id FROM accounts WHERE acct_type < 3 ORDER BY acct_id DESC";
  $accountID = DBwrapper::DBselect($sqlAccountNumbersSelect)[0]['acct_id'] + 1;
  $accountName = $userFName . " " . $userLName;

  //add new account to DB
  $sqlAccountInsert = "INSERT INTO accounts (acct_id, acct_name, acct_active, acct_type, acct_balance) VALUES (" .
    $accountID . ",'$accountName',1,1,0)";

  //add account access for admins
  $sqlAdminSelect = "SELECT user_id FROM users WHERE user_role=1";
  $adminArray = DBwrapper::DBselect($sqlAdminSelect);
  $sqlAccessInsert = "INSERT INTO acct_user_access (user_id, acct_id, owner, view, edit) VALUES ";
  foreach ($adminArray as $a) {
    $aUserID = $a['user_id'];
    $sqlAccessInsert .= "($aUserID,$accountID,0,1,1),";
  }

  //add account access for moderators also
  $sqlModeratorSelect = "SELECT user_id FROM users WHERE user_role=2";
  $moderatorArray = DBwrapper::DBselect($sqlModeratorSelect);
  foreach ($moderatorArray as $m) {
    $mUserID = $m['user_id'];
    $sqlAccessInsert .= "($mUserID,$accountID,0,1,0),";
  }
  $sqlAccessInsert = rtrim($sqlAccessInsert, ", "); //trim final comma

  //conditionally update DB
  $addAccountSuccess = DBwrapper::DBupdate($sqlAccountInsert);
  if($addAccountSuccess){
    $addAccountSuccess = DBwrapper::DBupdate($sqlAccessInsert);
  }
}//end if (create account?)

//ADD USER
//get next available userID
$sqlUserNumbersSelect = "SELECT user_id FROM users ORDER BY user_id DESC";
$userID = DBwrapper::DBselect($sqlUserNumbersSelect)[0]['user_id'] + 1;

//create password??
$newPassword = randomPassword(8);
$hashedpwd = password_hash($newPassword, PASSWORD_DEFAULT);

$sqlNewUserInsert = "INSERT INTO users (user_id,user_role,fname,lname,email,password) VALUES ($userID,$userRole," .
  "'$userFName','$userLName','$userEmail','$hashedpwd')";
$addUserSuccess = DBwrapper::DBupdate($sqlNewUserInsert);

//upon success, email user
if ($addUserSuccess){
  //send email
  $subject = "Finance Portal account created";

  $message = "<b>Your account has been created. Your user name is this email.</b>";
  $message .= "<h4>Your inital password is: ";
  $message .= $newPassword . "</h4> \r\n ";
  $message = "<b>Please reset this password when you first log in.</b>";

  $header = "From:envivo.salamanca@gmail.com \r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-type: text/html\r\n";

  $addUserSuccess = mail ($email,$subject,$message,$header);
}//end email new user

if($createAccount){
  //access for user to own account
  $sqlSelfAccessInsert = "INSERT INTO acct_user_access (user_id, acct_id, owner, view, edit) VALUES ";
  $sqlSelfAccessInsert .= "($userID,$accountID,1,1,1)";
  DBwrapper::DBupdate($sqlSelfAccessInsert);
}

//add admin/moderator access to other accounts
if($userRole != "3"){
  $sqlAccountSelect = "SELECT acct_id FROM accounts WHERE acct_type < 3 AND acct_id !=$accountID";
  $adminArray = DBwrapper::DBselect($sqlAccountSelect);
  $sqlAccessInsert = "INSERT INTO acct_user_access (user_id, acct_id, owner, view, edit) VALUES ";
  foreach ($adminArray as $a) {
    $acctID = $a['acct_id'];
    if($userRole == "1"){
      $sqlAccessInsert .= "($userID,$acctID,0,1,1),";
    }else {
      $sqlAccessInsert .= "($userID,$acctID,0,1,0),";
    }
  }
  $sqlAccessInsert = rtrim($sqlAccessInsert, ", "); //trim final comma
  DBwrapper::DBupdate($sqlAccessInsert);
}

$added = 2;
if($addUserSuccess){
  $added = 1;
}

header("Location: user-admin.php?added=$added");
