<?php
session_start();

require 'DBWrapper.php';

$categoryID = "";
$categoryName = "";
$month = "";
$year = "";

//process form data
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $categoryID = $_GET["id"];
    $month = $_GET["m"];
    $year = $_GET["y"];    
}

$categoryQuery = "SELECT category_name FROM categories WHERE category_id = " . $categoryID;
$categoryName = DBwrapper::DBselect($categoryQuery)[0]['category_name'];

$sqlTransQuery = "SELECT transactions.trans_date, transactions.trans_amount, P.acct_name AS payer , R.acct_name AS receiver, " .
    "transactions.description FROM transactions LEFT JOIN accounts R ON transactions.acct_receiver=R.acct_id LEFT JOIN" .
    " accounts P ON transactions.acct_payer=P.acct_id WHERE year(transactions.trans_date)=" . $year;

if($month != "0"){
    $sqlTransQuery = $sqlTransQuery . " AND month(transactions.trans_date)=" . $month;
}
$sqlTransQuery = $sqlTransQuery . " AND transactions.category_id=" . $categoryID;

$transArray = DBwrapper::DBselect($sqlTransQuery);

//create modal
echo "<div class=\"w3-modal-content\">";
echo "<header class=\"w3-container w3-light-green\">";
echo "<button type=\"reset\" onclick=\"closeCategoryCashFlowModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
echo "<h3>Category: $categoryName</h3>";
echo "</header>";
echo "<div id=\"categoryCashFlowData\">";
echo "<table class=\"w3-table-all\">";
echo "<tr><th>Date</th><th>Account Payer</th><th>Account Receiver</th><th>Amount</th><th>Description</th></tr>";

foreach($transArray as $t){
    $amount = number_format((float)$t['trans_amount'], 2, '.', '');
    echo "<tr><td>" . $t['trans_date'] . "</td><td>" . $t['payer'] . "</td><td>" . $t['receiver'] . "</td><td>";
    echo $_SESSION['currencySymbol'] . " " . $amount;
    echo "</td><td>" . $t['description'] . "</td></tr>";
}

echo "</table></DIV>";
echo "<footer class=\"w3-container w3-light-green\">";
echo "<h6> </h6>";
echo "</footer>";
echo "</div>";