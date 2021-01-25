<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

require 'Account.php';
require 'header.php';
?>

<div id="mainbody" class="w3-container">
<?php require 'left_nav.php'?>

<div id="maincontent">
<?php


//USER GLOBALS - get from session?
$user_id = $_SESSION['userID'];

//form data
$acct_id = 0;
$pageNum = 0;

$success = -1;

//handle form input - collect account number to display
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acct_id = $_POST["account"];
    $pageNum = $_POST["pagenum"];
}else if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $acct_id = $_GET["acct_id"];
    $pageNum = $_GET["pagenum"];
    $success = $_GET["success"];
}else{
    //re-direct to force account selection
    header("Location: view-accounts.php");
}

$_SESSION["currentAccount"] = $acct_id;
$a = new Account($acct_id, $user_id, $pageNum);
echo $a->displayAccountInfo();
if ($success == 0){
    echo "ERROR in updating record<br>";
}

echo $a->printReceiptPage();

?>
</div>
</div>
</body>
<?php require 'footer.php' ?>
