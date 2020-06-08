<?php
session_start();

require 'DBwrapper.php';

$receiptNum = "";
$receiptInfo = array();

//get receipt num from form
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $receiptNum = $_GET["r"];
}


//select receipt data
$select_query = "SELECT * FROM transactions WHERE trans_id=" . $receiptNum;
$receiptInfo = DBwrapper::DBselect($select_query)[0];


//create delete
$delete_query = "DELETE FROM transactions WHERE trans_id=" . $receiptNum;
DBwrapper::DBupdate($delete_query);


//create update for balances
$update_recv_query = "UPDATE accounts SET acct_balance=acct_balance - " . $receiptInfo["trans_amount"] . 
                        " WHERE acct_id =" . $receiptInfo["acct_receiver"];
$update_payer_query = "UPDATE accounts SET acct_balance=acct_balance + " . $receiptInfo["trans_amount"] .
" WHERE acct_id =" . $receiptInfo["acct_payer"];

DBwrapper::DBupdate($update_recv_query);
DBwrapper::DBupdate($update_payer_query);

?>