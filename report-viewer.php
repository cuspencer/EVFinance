<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}
if($_SESSION['userRole'] == "3"){ //test this!
    header("Location: login.php");
}
?>

<?php
require 'header.php';
require 'DBwrapper.php';
require 'CashFlowCategory.php';
require 'AccountBalanceReport.php';
?>

<div id="mainbody">
<?php require 'left_nav.php'?>

<div id="maincontent">

<?php


function format2dp($strToFormat){
    return number_format((float)$strToFormat, 2, '.', '');
}

function printCashFlowObject($cashFlowObject, $total){

    $strTotal = format2dp($total);
    $strToReturn = "";
    $month = $cashFlowObject->getMonth();
    $year = $cashFlowObject->getYear();
    $name = $cashFlowObject->getName();
    $type = $cashFlowObject->getType();
    $id = $cashFlowObject->getID();

    if(($type == "0") || ($type == "1")){
        $strToReturn = $strToReturn . "<DIV class=\"category-name\">". $name . "</DIV>";
    }
    else{
        if($month == ""){
            $month = "0";
        }
        $strToReturn = $strToReturn . "<DIV class=\"category-name\"> <a onclick=\"showCategoryCashFlowModal(" .
                   $id . "," . $month . "," . $year . ")\">". $name . "</a></DIV>";
    }
    $strToReturn = $strToReturn . "<DIV class=\"category-total\">";
    $strToReturn = $strToReturn . "<LABEL class=\"currency-symbol\">" . $_SESSION['currencySymbol'] . " </LABEL>";
    $strToReturn = $strToReturn . "<LABEL class=\"report-amount\">" . $strTotal . "</LABEL></DIV>";

    return $strToReturn;
}//printCashFlowObject()

/*
 *
 */
function recursivePrintCashFlow($cashFlowObject){

    $strToReturn = "";
    $children = $cashFlowObject->getChildren();
    $type = $cashFlowObject->getType();
    $total = $cashFlowObject->getTotal();

    //print self
    if(($type == "3") && ($total != "0")){ //only print non-empty children
        $strToReturn = $strToReturn . "<DIV class=\"cf-sub-child-node\">";
        $strToReturn = $strToReturn . printCashFlowObject($cashFlowObject, $total);
        $strToReturn = $strToReturn . "</DIV>";
    }else if(($type == "2") && ($total != "0")){ //only print non-empty children
        $strToReturn = $strToReturn . "<DIV class=\"cf-child-node\">";
        $strToReturn = $strToReturn . printCashFlowObject($cashFlowObject, $total);
        $strToReturn = $strToReturn . "</DIV>";
    }else if(($type == "1") && ($total != "0")){
        $strToReturn = $strToReturn . "<DIV class=\"cf-parent-node\">";
        $strToReturn = $strToReturn . printCashFlowObject($cashFlowObject, $total);
        $strToReturn = $strToReturn . "</DIV>";
    }else if($type == "0"){
        $strToReturn = $strToReturn . "<DIV class=\"cf-super-node\">";
        $strToReturn = $strToReturn . printCashFlowObject($cashFlowObject, $total);
        $strToReturn = $strToReturn . "</DIV>";
    }

    //print children
    foreach($children as $c){
        $strToReturn = $strToReturn . recursivePrintCashFlow($c);
    }

    return $strToReturn;
}//end function recursivePrintCashFlow()


/*
 *
 */
function printCashFlow($reportType, $reportMonth, $reportYear){
    $inflows = new CashFlowCategory("1", $reportType, $reportMonth, $reportYear);
    $outflows = new CashFlowCategory("2", $reportType, $reportMonth, $reportYear);

    $strToReturn = "<DIV id=\"cash-flow-report\">";
    $strToReturn = $strToReturn . "CASH FLOW:<BR>";

    $strToReturn = $strToReturn . recursivePrintCashFlow($inflows);
    $strToReturn = $strToReturn . recursivePrintCashFlow($outflows);

    //NET CASH FLOW
    $netCashFlow = $inflows->getTotal() - $outflows->getTotal();
    $strToReturn = $strToReturn . "<DIV class=\"cf-super-node\">";
    $strToReturn = $strToReturn . "<DIV class=\"category-name\"> NET CASH FLOW: </DIV>";
    $strToReturn = $strToReturn . "<DIV class=\"category-total\">";
    $strToReturn = $strToReturn . "<LABEL class=\"currency-symbol\">" . $_SESSION['currencySymbol'] . " </LABEL>";
    $strToReturn = $strToReturn . "<LABEL class=\"report-amount\">" . $netCashFlow . "</LABEL>";
    $strToReturn = $strToReturn . "</DIV></DIV>";

    $strToReturn = $strToReturn . "</DIV>";
    return $strToReturn;
}//end function printCashFlow


/*
 *
 */
function printAccountBalances($reportType, $reportMonth, $reportYear){

    $balances = new AccountBalanceReport($reportType, $reportMonth, $reportYear);
    $cashOnHand = "0";
    $strTemp = "";

    $strToReturn = "<DIV id=\"account-balances\">";
    $strToReturn = $strToReturn . "ACCOUNT BALANCES:";

    //print cash accounts (if negative color red?)
    $strToReturn = $strToReturn . "<DIV id=\"cash-accounts\"><label class=\"section-header\">Cash Accounts:</label>";
    foreach($balances->cashAccounts as $a){
        $strTemp = format2dp($a->accountBalance);
        $strToReturn = $strToReturn . "<DIV class=\"account-name\">" . $a->accountName . "</DIV>";
        $strToReturn = $strToReturn . "<DIV class=\"account-balance\">";
        $strToReturn = $strToReturn . "<LABEL class=\"currency-symbol\">" . $_SESSION['currencySymbol'] . " </LABEL>";
        $strToReturn = $strToReturn . "<LABEL class=\"report-amount\">" . $strTemp . "</LABEL></DIV>";
        $cashOnHand += $a->accountBalance;
    }
    $strToReturn = $strToReturn . "</DIV>";

    //print bank accounts
    $strToReturn = $strToReturn . "<DIV id=\"bank-accounts\"><label class=\"section-header\">Bank Accounts:</label>";
    foreach($balances->bankAccounts as $a){
        $strTemp = format2dp($a->accountBalance);
        $strToReturn = $strToReturn . "<DIV class=\"account-name\">" . $a->accountName . "</DIV>";
        $strToReturn = $strToReturn . "<DIV class=\"account-balance\">";
        $strToReturn = $strToReturn . "<LABEL class=\"currency-symbol\">" . $_SESSION['currencySymbol'] . " </LABEL>";
        $strToReturn = $strToReturn . "<LABEL class=\"report-amount\">" . $strTemp . "</LABEL></DIV>";
        $cashOnHand += $a->accountBalance;
    }
    $strToReturn = $strToReturn . "</DIV>";

    $strTemp = format2dp($cashOnHand);
    $strToReturn = $strToReturn . "<DIV id=\"total-cash\">";
    $strToReturn = $strToReturn . "<DIV class=\"account-name\">Total Cash On Hand: </DIV>";
    $strToReturn = $strToReturn . "<DIV class=\"account-balance\">";
    $strToReturn = $strToReturn . "<LABEL class=\"currency-symbol\">" . $_SESSION['currencySymbol'] . " </LABEL>";
    $strToReturn = $strToReturn . "<LABEL class=\"report-amount\">" . $strTemp . "</LABEL></DIV>";
    $strToReturn = $strToReturn . "</DIV>"; //total-cash

    $strToReturn = $strToReturn . "</DIV>";
    return $strToReturn;
}//end functino printAccountBalances()



$reportType = "";
$reportYear = "";
$reportMonth = "";
$monthsArray = array('Zero Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
    'September', 'October', 'November', 'December');

//get form info and display
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $reportType = $_GET["reportType"];
    $reportYear = $_GET["reportYear"];
    if($reportType == "2"){
        $reportMonth = $_GET["reportMonth"];
    }
}else if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $reportType = $_GET["reportType"];
    $reportYear = $_GET["reportYear"];
    if($reportType == "2"){
        $reportMonth = $_GET["reportMonth"];
    }

    //currency stuff
    $sqlCurrShortQuery = "SELECT short FROM currencies";
    $currShortArray = DBwrapper::DBselect($sqlCurrShortQuery);

    foreach($currShortArray as $c){
        $cShort = $c['short'];
        $newRate = $_POST[$cShort];
        $sqlCurrUpdate = "UPDATE currencies SET exchange_rate=" . $newRate . " WHERE short='" . $cShort . "'";
        DBwrapper::DBupdate($sqlCurrUpdate);
    }
}




echo "<div id=\"currency-bar\">";
echo "<div id=\"currency-selector-bar\">";

//get currency type from session
$myCurrency = $_SESSION['currencyID'];
$sqlCurrencyQuery = "SELECT * FROM currencies";
$currencyArray = DBwrapper::DBselect($sqlCurrencyQuery);

echo "<text>Change currency?  </text>";

foreach($currencyArray as $c){

    echo "<label>" . $c['short'] . "<input type=\"radio\" class=\"currency-chooser\" name=\"currType\" value=\""
    . $c['exchange_rate'] . "\" ";
    if($c['currency_id'] == $myCurrency){
        echo "checked ";
    }
    echo "onclick=\"setCurrencyType('" . $c['symbol'] . "', this.value)\"/> </label>";
}

echo "</div>";
echo "<div id=\"currency-editor-buttons\">";
if($_SESSION['userRole'] == "1"){
    echo "<button onclick=\"showCurrencyModal()\">Exchange Rates</button>";
}
echo "<button onclick=\"printReportEasy()\">Print Report</button>";
echo "</div>";
echo "</div>";

//CREATE CURRENCY EDITING MODAL
echo "<div id=\"currencyModal\" class=\"w3-modal\">";
echo "<div class=\"w3-modal-content mediumModal\">";
echo "<form id=\"currencyForm\" method=\"POST\">";
echo "<header class=\"w3-container w3-light-green\">";
echo "<button type=\"reset\" onclick=\"closeCurrencyModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
echo "<h3>Update Exchange Rates</h3>";
echo "<h6>Value per " . $_SESSION['currencyShort'] . "</h6>";
echo "</header>";
echo "<div class=\"w3-container\">";

$patternTxt = "^\d*([.]\d{1,6})?$";
foreach($currencyArray as $c){
    $eRate = $c['exchange_rate'];
    $eRate = number_format((float)$eRate, 4, '.', '');
    $shorty = $c['short'];
    echo "<div class=\"exchangeRateDisplay\">";
    echo "<div class=\"twoColumns\">";
    echo "<span>" . $c['name'] . "</span>";
    echo "</div>";
    echo "<div class=\"twoColumns\">";
    echo "<input type=\"text\" class=\"currInput\" name=\"" . $shorty . "\" value=\"" . $eRate . "\"";
    if ($_SESSION['currencyShort'] == $shorty){
        echo " readonly/>";
    }
    else{
        echo "pattern=\"$patternTxt\"/>";
    }

    echo "</div>";
    echo "</div>";
}//end foreach

echo "</div>";
echo "<footer class=\"w3-container w3-light-green\">";
echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeCurrencyModal()\">Cancel</button>";
echo "<button type=\"submit\" class=\"w3-button\">Update</button>";
echo "</footer>";
echo "</form></div></div>";


//CREATE CATEGORY DISPLAY MODAL HOLDER
echo "<div id=\"categoryCashFlowModal\" class=\"w3-modal\"></DIV>";

//SHOW REPORT TITLE
echo "<div id=\"report-block\">";
echo "<div id=\"report-title\">";
echo "<h3>" . $_SESSION['sysName'] ."</h3>";
if($reportType == "1"){
    echo "<h5>Annual Report " . $reportYear . "</h5>";
}else{
    echo "<h5>Monthly Report " . $monthsArray[$reportMonth] . " " . $reportYear . "</h5>";
}
echo "</div>";

echo "<div id=\"report-data\">";
echo printCashFlow($reportType, $reportMonth, $reportYear);
echo printAccountBalances($reportType, $reportMonth, $reportYear);
echo "</DIV>";
echo "</DIV>";
?>

</div>
</div>
</body>
<?php require 'footer.php' ?>
