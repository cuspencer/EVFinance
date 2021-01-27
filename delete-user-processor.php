<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

require 'DBwrapper.php';

$userID = $_POST["userID"];
echo "USER IS: " . $userID;

$sqlAccessDelete = "DELETE FROM acct_user_access WHERE user_id=$userID";
$sqlUserDelete = "DELETE FROM users WHERE user_id=$userID";

$aSuccess = DBwrapper::DBupdate($sqlAccessDelete);
$dSuccess = DBwrapper::DBupdate($sqlUserDelete);

$deleted = 2;

if($dSuccess){
  $deleted = 1;
}

header("Location: user-admin.php?deleted=$deleted");
