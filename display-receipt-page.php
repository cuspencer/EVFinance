<?php
//SEND TO main-content
session_start();
require 'Account.php';


//USER GLOBALS - get from session?
$user_id = $_SESSION['userID'];

//form data
$acct_id = 0;
$pageNum = 0;

//handle form input - collect account number to display
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $acct_id = $_GET["acct_id"];
    $pageNum = $_GET["pagenum"];
    $a = new Account($acct_id, $user_id, $pageNum);
    echo $a->displayAccountInfo();
    echo $a->printReceiptPage();
}else{
    echo "ERROR IN PROCESSING REQUEST IN: display-receipt-page";
}


?>