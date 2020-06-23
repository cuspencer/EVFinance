<?php
session_start();
require 'DBwrapper.php';

function searchAccounts($acctName){
    
    $query = "SELECT acct_name FROM accounts WHERE acct_name LIKE\"" . $acctName . "%\" AND acct_type = 3";
    
    $result = DBwrapper::DBselect($query);
    
    return $result;
}//end function searchAccounts()

$hint = "";
$results = array();

//get form info
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $hint = $_GET["q"];
}

//query DB for possible matches
$results = searchAccounts($hint);

//create HTML
foreach ($results as $r){
    $acctName = $r['acct_name'];
    echo "<label class=\"livesearch-result\" onclick=\"fillSearch(this.innerHTML)\">$acctName</label><br>";
}
?>
