<?php
session_start();

require 'DBwrapper.php';

$accountID = "";
$accountName = "";
$accountBalance = "";
$accountActive = "";
$accountType = "";

//get account ID from AJAX request
$accountID = $_GET["a"];

//get account info
$sqlAccountSelect = "SELECT * FROM accounts WHERE acct_id=" . $accountID;
$accounts_array = DBwrapper::DBselect($sqlAccountSelect);
$accountName = $accounts_array[0]['acct_name'];
$accountBalance = $accounts_array[0]['acct_balance'];
$accountActive = $accounts_array[0]['acct_active'];
$accountType = $accounts_array[0]['acct_type'];


//Create Editable Row
echo "<tr class=\"w3-table\" id=\"" . $accountID . "\">";
echo "<input type=\"hidden\" name=\"accountID\" value=\"$accountID\"/>";
echo "<td><input type=\"text\" required id=\"editAccountName\" name=\"accountName\" value=\"" .
  $accountName . "\"></td>";
echo "<td><select id=\"editAccountType\" name=\"accountType\">";
if($accountType == 1){
  echo "<option value=\"1\" selected>Cash Account</option>";
  echo "<option value=\"2\">Bank Account</option>";
}else{
  echo "<option value=\"1\">Cash Account</option>";
  echo "<option value=\"2\" selected>Bank Account</option>";
}
echo "</select></td>";
echo "<td><input type=\"text\" required id=\"editAccountBalance\" name=\"accountBalance\" value=\"$accountBalance\" /></td>";
if($accountActive == 1){
  echo "<td><input type=\"checkbox\" id=\"editAccountActive\" name=\"accountActive\" checked/></td>";
}else{
  echo "<td><input type=\"checkbox\" id=\"editAccountActive\" name=\"accountActive\" /></td>";
}

//Cancel
echo "<td><label title=\"cancel\" class=\"material-icons\" onclick=\"cancelAccountEdit($accountID, '$accountName', $accountType, '$accountBalance', $accountActive)\">close</label>";

//Submit
echo "<label title=\"submit\" class=\"material-icons\" onclick=\"submitAccountEdit()\">done</label></td></tr>";

?>
