<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}
if($_SESSION['userRole'] == "3"){ //test this!
    header("Location: login.php");
}

require 'header.php';
require 'left_nav.php';
require 'DBwrapper.php';
require 'Receipt.php';

//Globals
$newAccountName = "";
$errorMessage = "";
$successMessage = "";


function printAddAccountModal(){
  $patternTxt = "^\d*([.]\d{1,2})?$";
  //create hidden add account modal
  echo "<div id=\"addAccountModal\" class=\"w3-modal\">";
  echo "<div class=\"w3-modal-content\">";
  echo "<form id=\"addAccountForm\" action=\"add-account-processor.php\" method=\"POST\">";
  echo "<header class=\"w3-container w3-light-green\">";
  echo "<button  type=\"reset\" onclick=\"closeAddAccountModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
  echo "<h3>Add a new Account</h3>";
  echo "<h6>Note: Hidden accounts will not be displayed in reports or \"Account Info\" section.</h6>";
  echo "<div class=\"errMsg\" id=\"addAccountModalErrorMessage\"></div>";
  echo "</header>";

  echo "<div class=\"w3-container\">";
  echo "<table>";
  echo "<tr><td>Account Name:</td><td><input type=\"text\" required id=\"newAccountName\" name=\"newAccountName\"></td></tr>";
  echo "<tr><td>Account Type:</td><td><select name=\"newAccountType\">";
  echo "<option value=\"1\" selected>Cash Account</option>";
  echo "<option value=\"2\">Bank Account</option>";
  echo "</select></td></tr>";
  echo "<tr><td>Initial Balance</td><td>" . $_SESSION['currencySymbol'];
  echo "<input type=\"text\" required name=\"newAccountBalance\" id=\"newAccountBalance\" value=\"0.00\" /></td></tr>";
  echo "<tr><td>Account Active</td><td><input type=\"checkbox\" name=\"newAccountActive\" checked/></td></tr>";
  echo "</table>";
  echo "</div>";

  echo "<footer class=\"w3-container w3-light-green\">";
  echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeAddAccountModal()\">Cancel</button>";
  echo "<DIV class=\"w3-button\" onclick=\"addAccount()\">Create Account</DIV>";
  echo "</footer>";
  echo "</form></div></div>";
}

function printAccountsTable(){
  $currencyString = $_SESSION['currencyShort'];
  $currencySymbol = $_SESSION['currencySymbol'];

  //select all cash and bank accounts
  $sqlAccountSelect = "SELECT * FROM accounts WHERE acct_type!=3";
  $accounts_array = DBwrapper::DBselect($sqlAccountSelect);

  //create editable form
  $strToReturn = "<DIV id=\"accounts_table\" class=\"w3-container\">";
  $strToReturn .= "<FORM id=\"editAccountForm\" name=\"editAccountForm\" action=\"\" method=\"POST\" autocomplete=\"off\">";
  $strToReturn .= "<INPUT type=\"hidden\" id=\"currencySymbol\" value=\"$currencySymbol\" />";
  $strToReturn .= "<table class=\"w3-table-all\">";
  $strToReturn .= "<tr><th>Name</th><th>Type</th><th>Balance</th><th>Active</th><th>Modify</th></tr>";

  foreach($accounts_array as $a) {
    $acctID = $a['acct_id'];
    $acctName = $a['acct_name'];
    $acctType = $a['acct_type'];
    $acctBalance = $a['acct_balance'];
    $acctActive = $a['acct_active'];

    $strToReturn .= "<tr id=\"" . $acctID . "\">";
    $strToReturn .= "<td class=\"accountNameLabel\">" . $acctName . "</td>";
    if($acctType == 1){
      $strToReturn .= "<td>Cash Account</td>";
    }else{
      $strToReturn .= "<td>Bank Account</td>";
    }
    $strToReturn .= "<td class=\"accountBalanceLabel\">" . moneyFormat($acctBalance, $currencyString) . "</td>";
    if($acctActive == 1){
      $strToReturn .= "<td><input type=\"checkbox\" name=\"accountActive\" disabled checked/></td>";
    }else{
      $strToReturn .= "<td><input type=\"checkbox\" name=\"accountActive\" disabled/></td>";
    }

    $strToReturn .= "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editAccount($acctID)\">create</label></td>";
    //$strToReturn .= "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteAccount($acctID)\">delete</label></td>";
    $strToReturn .= "</tr>";
  }
  $strToReturn = $strToReturn . "</table></form>";
  $strToReturn = $strToReturn .  "</DIV>"; //end accounts_table
  return $strToReturn;
}//end function printAccountsTable()


//Process success and error messages from adding/deleting accounts
if ($_SERVER["REQUEST_METHOD"] == "GET"){

  $dResult = 0;
  $aResult = 0;

  if(isset($_GET["deleted"])){
    $dResult = $_GET["deleted"];
  }
  if(isset($_GET["added"])){
    $aResult = $_GET["added"];
  }

  if($dResult == "2"){
    $errorMessage = "A database error occurred when deleting the account.";
  } else if ($dResult == "1"){
    $successMessage = "Account successfully deleted.";
  }

  if($aResult == "2"){
    $errorMessage = "A database error occurred when adding the account.";
  } else if ($aResult == "1"){
    $successMessage = "Account successfully added.";
  }

}//end GET processing

//process POST data for account editing
if ($_SERVER["REQUEST_METHOD"] == "POST"){

  $accountID = $_POST["accountID"];
  $accountName = test_input($_POST["accountName"]);
  $accountType = $_POST["accountType"];
  $accountBalance = test_input($_POST["accountBalance"]);
  $accountActive = "";


  if(isset($_POST["accountActive"])){
    $accountActive = 1;
  } else {
    $accountActive = 0;
  }

  $sqlUpdateAccount = "UPDATE accounts SET acct_name='" . $accountName . "', acct_active=" . $accountActive .
    ", acct_type=" . $accountType . ", acct_balance=" . $accountBalance . " WHERE acct_id=" . $accountID;
  $success = DBwrapper::DBupdate($sqlUpdateAccount);

  if($success){
    $successMessage = "Account edited successfully.";
  }else{
    $errorMessage = "Error editing account.";
  }

}//end if POST


echo "<DIV id=\"maincontent\" class=\"w3-container\">";
echo "<DIV id=\"admin-back-button\" class=\"w3-container\"><BUTTON onclick=\"location.href = 'admin-menu.php';\">";
echo "<span class=\"material-icons\">reply</span>BACK</BUTTON></DIV>";
echo "<DIV id=\"titlearea\" class=\"w3-container\">";
echo "<H3>Account Editor</H3>";
echo "<DIV id=\"addAccountButtonHolder\" class=\"w3-container\">";
echo "<BUTTON type=\"button\" onclick=\"showAddAccountModal()\">ADD ACCOUNT</BUTTON>";
echo "<DIV id=\"errorMessage\" class=\"errMsg\" />$errorMessage</DIV>";
echo "<DIV id=\"successMessage\" class=\"w3-green\" />$successMessage</DIV>";
echo "</DIV></DIV>";

//Placeholders for modal
echo printAddAccountModal();
echo "<div id=\"confirmAccountDeleteModal\" class=\"w3-modal\"></DIV>";
echo printAccountsTable();
echo "</DIV>"; //end maincontent

require 'footer.php';
?>
