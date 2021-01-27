<?php
session_start();

require 'DBwrapper.php';

$accountID = "";
$accountName = $_POST["newAccountName"];
$accountType = $_POST["newAccountType"];
$accountBalance = $_POST["newAccountBalance"];
$accountActive = "";

if(isset($_POST["newAccountActive"])){
  $accountActive = 1;
} else {
  $accountActive = 0;
}

//get next available accountID
$sqlAccountNumbersSelect = "SELECT acct_id FROM accounts WHERE acct_type < 3 ORDER BY acct_id DESC";
$accountID = DBwrapper::DBselect($sqlAccountNumbersSelect)[0]['acct_id'] + 1;

//add new account to DB
$sqlAccountInsert = "INSERT INTO accounts (acct_id, acct_name, acct_active, acct_type, acct_balance) VALUES (" .
  $accountID . ",'$accountName',$accountActive,$accountType,$accountBalance)";

//add account access for admins
$sqlAdminSelect = "SELECT user_id FROM users WHERE user_role=1";
$adminArray = DBwrapper::DBselect($sqlAdminSelect);
$sqlAccessInsert = "INSERT INTO acct_user_access (user_id, acct_id, owner, view, edit) VALUES ";
foreach ($adminArray as $a) {
  $userID = $a['user_id'];
  $sqlAccessInsert .= "($userID,$accountID,0,1,1),";
}

//add account access for moderators also
$sqlModeratorSelect = "SELECT user_id FROM users WHERE user_role=2";
$moderatorArray = DBwrapper::DBselect($sqlModeratorSelect);
foreach ($moderatorArray as $m) {
  $userID = $m['user_id'];
  $sqlAccessInsert .= "($userID,$accountID,0,1,0),";
}
$sqlAccessInsert = rtrim($sqlAccessInsert, ", "); //trim final comma

//conditionally update DB
$success = DBwrapper::DBupdate($sqlAccountInsert);
if($success){
  $success = DBwrapper::DBupdate($sqlAccessInsert);
}

$added = 2;
if($success){
  $added = 1;
}
header("Location: account-admin.php?added=$added");
