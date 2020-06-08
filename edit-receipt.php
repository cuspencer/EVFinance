<?php
session_start();

require 'Account.php';
require 'validation.php';

$acct_id = 0;
$receiptNum = "";
$receiptInfo = array();

$rType = 0; //receipt type (1, 2, 3)
$tDate = "";
$tAmount = 0;
$tAcctRecvNum = 0;
$tAcctPayerNum = 0;
$tCategoryId = 0;
$tDescription = "";
$tOtherAcctName = "";
$success = 0;

function findAcctNum($acctName){
    
    $acctNum = -1;
    $query = "SELECT acct_id FROM accounts WHERE acct_name=\"" . $acctName . "\"";
    
    $result = DBwrapper::DBselect($query);
    
    if(($result !== false) && (!empty($result))){
        $acctNum = $result[0]['acct_id'];
    } else {
        echo "ERROR";
    }
    
    return $acctNum;
}//end function findAcctNum()


function addExternalAccountByName($acctName){
    
    $currencyType = 1;
    
    $insert = "INSERT INTO accounts (acct_name, acct_type, acct_currency) VALUES (\"" . $acctName . "\", 3, " . $currencyType . ")";
    DBwrapper::DBupdate($insert);
    
    return findAcctNum($acctName);
}//end function addExternalAccountByName()


//handle form input
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $receiptNum = $_POST["receiptNum"];
    $rType = $_POST["rType"];
    $tDate = $_POST["tDate"];
    $tAmount = $_POST["tAmount"];
    $tCategoryId = $_POST["tCategoryId"];
    $tDescription = test_input($_POST["tDescription"]);
    $acct_id = $_POST["tMyAcctNum"];
    
    
    if ($rType == 3){ //transfer
        $tAcctPayerNum = $acct_id;
        $tAcctRecvNum = $_POST["tOtherAcctNum"];
    }else if($rType == 1){ // expense
        $tAcctPayerNum = $acct_id;
        $tOtherAcctName = test_input($_POST["tOtherAcctName"]);
        $tAcctRecvNum = findAcctNum($tOtherAcctName);
    }else if ($rType == 2){ //income
        $tAcctRecvNum = $acct_id;
        $tOtherAcctName = test_input($_POST["tOtherAcctName"]);
        $tAcctPayerNum = findAcctNum($tOtherAcctName);
    }else {
        echo "Type Error <br>";
    }
    
    //DB INSERT TRANSACTION
    
    //if new acct, add to DB first
    if($tAcctPayerNum == -1){
        $tAcctPayerNum = addExternalAccountByName($tOtherAcctName);
    }else if($tAcctRecvNum == -1){
        $tAcctRecvNum = addExternalAccountByName($tOtherAcctName);
    }
    
    
}else{
    echo "ERROR IN PROCESSING REQUEST IN: edit-receipt";
    exit();
}

//select receipt data
$select_query = "SELECT * FROM transactions WHERE trans_id=" . $receiptNum;
$receiptInfo = DBwrapper::DBselect($select_query)[0];

//create delete
$delete_query = "DELETE FROM transactions WHERE trans_id=" . $receiptNum;
DBwrapper::DBupdate($delete_query);

//update balances
$update_recv_query = "UPDATE accounts SET acct_balance=acct_balance - " . $receiptInfo["trans_amount"] .
" WHERE acct_id =" . $receiptInfo["acct_receiver"];
$update_payer_query = "UPDATE accounts SET acct_balance=acct_balance + " . $receiptInfo["trans_amount"] .
" WHERE acct_id =" . $receiptInfo["acct_payer"];

DBwrapper::DBupdate($update_recv_query);
DBwrapper::DBupdate($update_payer_query);


//create insert 




//submit new receipt, update balance
$insert = "INSERT INTO transactions (trans_date, trans_amount, acct_payer, acct_receiver, category_id, description) VALUES (\"" .
    $tDate . "\"," . $tAmount . "," . $tAcctPayerNum . "," . $tAcctRecvNum . "," . $tCategoryId . ",\"" . $tDescription . "\")";
    
    $updatePayer = "UPDATE accounts SET acct_balance = acct_balance - " . $tAmount . " WHERE acct_id = " . $tAcctPayerNum;
    $updateRecv = "UPDATE accounts SET acct_balance = acct_balance + " . $tAmount . " WHERE acct_id = " . $tAcctRecvNum;
    
    //update on successful insert
    if(DBwrapper::DBupdate($insert)){
        DBwrapper::DBupdate($updatePayer);
        DBwrapper::DBupdate($updateRecv);
        $success = 1;
    }
    


//re-direct to display page
header("Location: account-display.php?acct_id=$acct_id&pagenum=0&success=$success");

?>