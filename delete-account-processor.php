<?php
session_start();

require 'DBwrapper.php';

$accountID = $_POST["accountID"];

$sqlDeleteAccess = "DELETE FROM acct_user_access WHERE acct_id=$accountID";
$sqlDeleteTransactions = "DELETE FROM transactions WHERE acct_payer=$accountID OR acct_receiver=$accountID";
$sqlDeleteAccount = "DELETE FROM accounts WHERE acct_id=$accountID";

$result = DBwrapper::DBupdate($sqlDeleteAccess);
$result = $result && DBwrapper::DBupdate($sqlDeleteTransactions);
$result = $result && DBwrapper::DBupdate($sqlDeleteAccount);

$deleted = 2;

if($result){
  $deleted = 1;
}

echo "1: $sqlDeleteAccess";
header("Location: account-admin.php?deleted=$deleted");

?>
