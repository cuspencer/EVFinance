<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
} 
?>

<?php require 'header.php'?>
<?php require 'DBwrapper.php'?>

<div id="mainbody">
<?php require 'left_nav.php'?>

<div id="maincontent">

<?php

//USER GLOBALS - get from form
$user_id = $_SESSION['userID'];


//get user and acct info - this should go in the previous form and pass as hidden variables
$accts_info_stmt = "SELECT accounts.acct_id, accounts.acct_name FROM accounts INNER JOIN acct_user_access ON ". 
"accounts.acct_id=acct_user_access.acct_id WHERE acct_user_access.user_id = $user_id AND acct_user_access.view = 1";

$results_array = DBwrapper::DBselect($accts_info_stmt);

if(sizeof($results_array)>0){
    //create form with dropdown 
        
    echo "<form action=\"account-display.php\" method=\"POST\">";
    echo "SELECT AN ACCOUNT TO VIEW: ";
    echo "<select id=\"account\" name=\"account\">";
        
    foreach ($results_array as $r){
        echo "<option value=" . $r['acct_id'] . ">" . $r['acct_name'] . "</option>";
    }
        
    echo "</select>";
    echo "<input type=\"hidden\" name=\"pagenum\" value=\"0\">";
    echo "<input type=\"submit\" value=\"Submit\">";
    echo "</form>";
} else{
    echo "NO ACCOUNTS AVAILABLE!";
}
 
?>
</div>
</div>
</body>
<?php require 'footer.php' ?>