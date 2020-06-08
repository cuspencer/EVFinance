<?php
session_start();

require 'DBwrapper.php';

$receiptNum = "";
$userID = $_SESSION['userID'];
$acctID = $_SESSION['currentAccount'];
$receiptInfo = array();
$transfer_array = array();
$category_array = array();
$isTransfer = false;
$isCredit = false;


$tOtherAcctName = "";

$patternTxt = "^\d*([.]\d{1,2})?$"; //matches money
$maxdate = date("Y-m-d"); //today's date

//Populate transfer_array and category_array
function populateCategories(){
    $category_stmt = "SELECT * from categories WHERE category_id < 9000";
    return DBwrapper::DBselect($category_stmt);
}//end function populateCategories()

function populateTransferAccts($userID, $acctID){
    $editable_accts_stmt = "SELECT accounts.acct_id, accounts.acct_name FROM accounts INNER JOIN acct_user_access " .
        "ON accounts.acct_id=acct_user_access.acct_id WHERE acct_user_access.user_id = $userID AND acct_user_access.edit = 1 " .
        "AND accounts.acct_id <> $acctID";
    return DBwrapper::DBselect($editable_accts_stmt);
}//end function populateTransferAccts()

$transfer_array = populateTransferAccts($userID, $acctID);
$category_array = populateCategories();

//$transferCount = count($transfer_array);

//get receipt num from form
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $receiptNum = $_GET["r"];
}

//select receipt data
$select_query = "SELECT * FROM transactions WHERE trans_id=" . $receiptNum;
$receiptInfo = DBwrapper::DBselect($select_query)[0];


//Is credit?
if($receiptInfo["acct_receiver"] == $acctID){
    $isCredit = true;
}

//is transfer?
if($receiptInfo["category_id"] > "8999"){
    $isTransfer = true;
}else{
    //other acct name
    $account_query = "SELECT acct_name FROM accounts WHERE acct_id = ";
    if($isCredit) { 
        $account_query = $account_query . $receiptInfo['acct_payer'];
    }
    else{
        $account_query = $account_query . $receiptInfo['acct_receiver'];
    }
    $tOtherAcctName = DBwrapper::DBselect($account_query)[0]["acct_name"];
}



//echo "<FORM id=\"editReceiptForm\" ACTION=\"edit-receipt.php\" METHOD=\"POST\">";
echo "<input type=\"hidden\" id=\"tMyAcctNum\" name=\"tMyAcctNum\" value=\"" . $acctID . "\"/>";
echo "<input type=\"hidden\" id=\"receiptNum\" name=\"receiptNum\" value=\"" . $receiptNum . "\"/>";

//DATE
echo "<td><INPUT type=\"date\" required id=\"tDate\" name=\"tDate\" value=\"" . $receiptInfo["trans_date"] . "\" max=\"" . 
$maxdate . "\"/></td>";

//CATEGORY
if($isTransfer){
    echo "<td><input type=\"hidden\" id=\"tCategoryId\" name=\"tCategoryId\" value=\"9000\"/>";
    echo "<input type=\"text\" id=\"tCategoryName\" name=\"tCategoryName\" value=\"Internal Transfer\" readonly/></td>";
}
else{
    echo "<td><select required id=\"tCategoryId\" name=\"tCategoryId\">";
    
    //loop and add category options
    foreach ($category_array as $r){
        echo "<option value=" . $r['category_id'];
        if($r['category_id'] == $receiptInfo['category_id']){
               echo " selected";
        }
        echo ">" . utf8_encode($r['category_name']) . "</option>";
    }
    echo "</td>";
}


//ACCOUNT
if($isTransfer){
    echo "<td><select required id=\"tOtherAcctNum\" name=\"tOtherAcctNum\">";

    //loop and add transfer options, select if currently selected
    foreach ($transfer_array as $r){
        echo "<option value=" . $r['acct_id'];
        if(($r['acct_id'] == $receiptInfo['acct_payer']) || ($r['acct_id'] == $receiptInfo['acct_receiver'])){
            echo " selected";
        }
        echo ">" . utf8_encode($r['acct_name']) . "</option>";
    }
    echo "</td>";
    echo "<input type=\"hidden\" id=\"rType\" name=\"rType\" value=\"3\"/>"; //transfer code
}
else{
    echo "<td><INPUT type=\"text\" required id=\"tOtherAcctName\" name=\"tOtherAcctName\" value=\"" . 
        $tOtherAcctName ."\" onkeyup=\"lookupAccount(this.value)\">" . "</input><BR>" .
        "<DIV id=\"livesearch\" class=\"searchResults\"></DIV></td>";
        if($isCredit){
            echo "<input type=\"hidden\" id=\"rType\" name=\"rType\" value=\"2\"/>"; //income code
        }else{
            echo "<input type=\"hidden\" id=\"rType\" name=\"rType\" value=\"1\"/>"; //expense code
        }
}

//DESCRIPTION
echo "<td><INPUT type=\"text\" required id=\"tDescription\" name=\"tDescription\"/ value=\"" . 
    utf8_encode($receiptInfo['description']) . "\"></td>";    
    

//AMOUNT 
if($isCredit){
    echo "<td/><td><span class=\"input-euro left\">";
    echo"<INPUT type=\"text\" required id=\"tAmount\" name=\"tAmount\" pattern=\"$patternTxt\" value=\"" .
    $receiptInfo["trans_amount"] . "\"/></span></td>";
    
}
else {
    echo "<td><span class=\"input-euro left\">";
    echo "<INPUT type=\"text\" required id=\"tAmount\" name=\"tAmount\" pattern=\"$patternTxt\" value=\"" .
    $receiptInfo["trans_amount"] . "\"/></span></td><td/>";
}

//Cancel
echo "<TD/><TD><label title=\"cancel\" class=\"material-icons\" onclick=\"cancelEdit()\">close</label>";

//Submit
echo "<label title=\"submit\" class=\"material-icons\" onclick=\"submitEdit()\">done</label></td>";

?>