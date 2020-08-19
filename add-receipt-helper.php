<?php
session_start();
require 'DBwrapper.php';

$userId = $_SESSION['userID'];

$t = $_GET['t']; //receiptType
$acctId = $_GET['a'];

//$maxdate = date("Y-m-d", strtotime("tomorrow"));
$maxdate = date("Y-m-d");


//Populate transfer_array and category_array
function populateCategories(){
    $category_stmt = "SELECT * from categories WHERE category_id < 9000 AND category_type = 2";
    return DBwrapper::DBselect($category_stmt);
}//end function populateCategories()

function populateTransferAccts($userId, $acctId){
    $editable_accts_stmt = "SELECT accounts.acct_id, accounts.acct_name FROM accounts INNER JOIN acct_user_access " .
    "ON accounts.acct_id=acct_user_access.acct_id WHERE acct_active = 1 AND acct_user_access.user_id = $userId " .
    "AND acct_user_access.edit = 1 AND accounts.acct_id <> $acctId";
    return DBwrapper::DBselect($editable_accts_stmt);
}//end function populateTransferAccts()


$transfer_array = populateTransferAccts($userId, $acctId);
$category_array = populateCategories();

$transferCount = count($transfer_array);

//onsubmit=\"return validateAddReceipt()\"  ???

echo "<FORM name=\"addReceipt\" id=\"addReceipt\" action=\"add-receipt-processer.php\" method=\"POST\" " .
     "autocomplete=\"off\">";
echo "<input type=\"hidden\" id=\"tMyAcctNum\" name=\"tMyAcctNum\" value=\"" . $acctId . "\"/>";


echo "<label>Expense<input type=\"radio\" value =\"1\" name=\"rType\" ";
if($t == 1){
    echo "checked ";
}
echo "onclick=\"addReceiptFields(1," . $acctId . ")\"/></label>";
echo "<label>  Income<input type=\"radio\" value =\"2\" name=\"rType\" ";
if($t == 2){
    echo "checked ";
}
echo "onclick=\"addReceiptFields(2," . $acctId . ")\"/></label>";
echo "<label>  Internal Transfer<input type=\"radio\" value =\"3\" name=\"rType\" ";
if($t == 3){
    echo "checked ";
}
echo "onclick=\"addReceiptFields(3," . $acctId . ")\"/></label>";

//$patternTxt = "^[-]?([1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|\.[0-9]{1,2})";
$patternTxt = "^\d*([.]\d{1,2})?$"; //matches money

echo "<table>";
echo "<tr><td><text>Date: </text></td>";
echo "<td><INPUT type=\"date\" required id=\"tDate\" name=\"tDate\" value=\"" . $maxdate . "\" max=\"" .
    $maxdate . "\"/></td>";
echo "<td><div id=\"dateErrMsg\" name=\"dateErrMsg\" class=\"errMsg\"></div></td></tr>";

echo "<tr><td><text>Amount: </text></td>";
echo "<td><span class=\"input-euro left\">";
echo "<INPUT type=\"text\" required id=\"tAmount\" name=\"tAmount\" pattern=\"$patternTxt\"/></span></td>";
echo "<td><div id=\"amtErrMsg\" name=\"amtErrMsg\" class=\"errMsg\"></div></td></tr>";

echo "<tr><td><text>Description: </text></td>";
echo "<td><INPUT type=\"text\" required id=\"tDescription\" name=\"tDescription\"/></td>";
echo "<td><div id=\"descErrMsg\" name=\"descErrMsg\" class=\"errMsg\"></div></td></tr>";


if ($t == 3){ //internal transfer
    echo "<tr><td><text>Transfer To:</text></td>";
    echo "<td><select required id=\"tOtherAcctNum\" name=\"tOtherAcctNum\">";
    echo "<option value=\"\" selected disabled>Select an account...</option>";

    //loop and add transfer options
    foreach ($transfer_array as $r){
        echo "<option value=" . $r['acct_id'] . ">" . $r['acct_name'] . "</option>";
    }
    echo "<input type=\"hidden\" id=\"tCategoryId\" name=\"tCategoryId\" value=\"9000\"/>";
    echo "</td>";
    if($transferCount == 0){
        echo "<td><div id=\"catErrMsg\" name=\"catErrMsg\" class=\"errMsg\">INSUFFICIENT ACCOUNT ACCESS</div></td></tr>";
    }else{
        echo "<td><div id=\"catErrMsg\" name=\"catErrMsg\" class=\"errMsg\"></div></td></tr>";
    }
} else {
    echo "<tr>";
    if($t == 1){//expense
        echo "<td><text>Paid To: </text></td>";
    }else if ($t == 2){//income
        echo "<td><text>Received From: </text></td>";
    }
    echo "<td><INPUT type=\"text\" required id=\"tOtherAcctName\" name=\"tOtherAcctName\" onkeyup=\"lookupAccount(this.value)\">" .
    "</input></td>";
    echo "<td><div id=\"acctErrMsg\" name=\"acctErrMsg\" class=\"errMsg\"></div></td></tr>";

    echo "<tr><td/><td><DIV id=\"livesearch\" class=\"searchResults\"></DIV></td><td/></tr>";

    echo "<tr><td><text>Category: </text></td>";
    echo "<td><select required id=\"tCategoryId\" name=\"tCategoryId\">";
    echo "<option value=\"\" selected disabled>Select a category...</option>";
    //loop and add category options
    foreach ($category_array as $r){
        echo "<option value=" . $r['category_id'] . ">" . $r['category_name'] . "</option>";
    }
    echo "</td>";
    echo "<td><div id=\"catErrMsg\" name=\"catErrMsg\" class=\"errMsg\"></div></td></tr>";

}//end else


echo "<tr><td><input type=\"submit\" value=\"Submit Receipt\"/></td></tr>";
echo "</FORM>";


?>
