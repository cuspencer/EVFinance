<?php
session_start();

require 'Account.php';
require 'validation.php';

//account info
$acct_id = 0;

//transaction info
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
        //echo "addPayer <br>   ";
        $tAcctPayerNum = addExternalAccountByName($tOtherAcctName);
    }else if($tAcctRecvNum == -1){
        //echo "addRecv <br>";
        $tAcctRecvNum = addExternalAccountByName($tOtherAcctName);
    }

    
}else{
    echo "ERROR IN PROCESSING REQUEST IN: add-receipt-processor";
    exit();
}

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