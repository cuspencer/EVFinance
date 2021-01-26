<?php
//session_start();
require 'Receipt.php';
require 'DBwrapper.php';

class Account{

  public $acct_id;
  public $user_id;
  public $acct_name;
  public $acct_balance;
  public $canEdit = false;
  public $userRole;
  public $currency_id;
  public $currency_string;
  public $receiptArray = array();
  public $numPages = 0;
  public $rowsPerPage = 20;
  public $pageNum;

  public function __construct($acct_id, $user_id, $pageNum){
      $this->acct_id = $acct_id;
      $this->user_id = $user_id;
      $this->pageNum = $pageNum;
      $this->currency_id = $_SESSION['currencyID'];
      $this->currency_string = $_SESSION['currencyShort'];
      $this->userRole = $_SESSION['userRole'];
      $this->setAccountInfo();
      $this->setRows();
      $this->setReceipts();
  }

  public function displayAccountInfo(){
      $strBalanceColor = moneyFormat($this->acct_balance, $this->currency_string);
      if($this->acct_balance < 0){
          $strBalanceColor = "<span class=\"negative-balance-text\">" . $strBalanceColor . "</span>";
      }

      $strToReturn = "<DIV class=\"w3-container\" id=\"acct_info\">";
      $strToReturn = $strToReturn . "<label hidden id=\"hiddenAcctNum\">" .$this->acct_id . "</label>";
      $strToReturn = $strToReturn . "ACCOUNT: <b>" . $this->acct_name . "</b><BR>";
      $strToReturn = $strToReturn . "BALANCE: " . $strBalanceColor . "<BR>";
      $strToReturn = $strToReturn . "</DIV>";

      return $strToReturn;
  }//end function displayAccountInfo()

  //populate info from DB
  private function setAccountInfo(){

      //gather account info
      $acct_info_stmt = "SELECT accounts.acct_name, accounts.acct_balance, acct_user_access.edit ".
      "FROM accounts INNER JOIN acct_user_access ON accounts.acct_id = acct_user_access.acct_id " .
      "WHERE accounts.acct_id=$this->acct_id AND acct_user_access.user_id=$this->user_id";

      $results_array = DBwrapper::DBselect($acct_info_stmt);

      if(sizeof($results_array)>0){
        $this->acct_balance = $results_array[0]['acct_balance'];
        $this->acct_name = $results_array[0]['acct_name'];

        if($results_array[0]['edit'] == 1){
            $this->canEdit = true;
        }

      }else{ //no data
         echo "ERROR ON ACCT INFO GRAB";
      }

  }//end function setAccountInfo()


  private function setRows(){

      //number of rows for pagination
      $row_num_stmt = "SELECT COUNT(trans_id) FROM transactions WHERE acct_payer=$this->acct_id  OR acct_receiver=$this->acct_id ";
      $results_array = DBwrapper::DBselect($row_num_stmt);
      $numRows = (int)$results_array[0]['COUNT(trans_id)'];
      $this->numPages = $numRows / $this->rowsPerPage;

  }// end function setRows()


  public function setReceipts(){

      $balance = $this->acct_balance;
      $isCredit = false;
      $acct_num = 0;
      $offset = $this->pageNum * $this->rowsPerPage;

      //select all receipts for this account
      $select_stmt = "SELECT transactions.trans_id, transactions.trans_date, transactions.category_id, transactions.trans_amount,
            transactions.acct_payer, transactions.acct_receiver, accounts.acct_name, categories.category_name, transactions.description
            FROM ((transactions INNER JOIN accounts ON transactions.acct_receiver=accounts.acct_id)
            INNER JOIN categories ON transactions.category_id=categories.category_id)
            WHERE acct_payer=$this->acct_id
            UNION SELECT transactions.trans_id, transactions.trans_date, transactions.category_id, transactions.trans_amount,
            transactions.acct_payer, transactions.acct_receiver, accounts.acct_name, categories.category_name, transactions.description
            FROM ((transactions INNER JOIN accounts ON transactions.acct_payer=accounts.acct_id)
            INNER JOIN categories ON transactions.category_id=categories.category_id)
            WHERE acct_receiver=$this->acct_id
            ORDER BY trans_date DESC";

      $results_array = DBwrapper::DBselect($select_stmt);

      //calculate how many receipts to show
      $rCount = count($results_array);
      $numReceipts = $rCount - $offset;
      if($numReceipts > $this->rowsPerPage){
          $numReceipts = $this->rowsPerPage;
      }

      //add up balance until offset
      for($i=0;$i<$offset;$i++){
          $r = $results_array[$i];
          if($r['acct_payer'] == $GLOBALS['acct_id']){
              $balance += $r['trans_amount'];
          }else{
              $balance -= $r['trans_amount'];
          }
      }//end for

      //iterate a page of receipts
      for($i=0;$i<$numReceipts;$i++) {
          $r = $results_array[$offset + $i];

          if($r['acct_payer'] == $GLOBALS['acct_id']){
              $acct_num = $r['acct_receiver'];
              $isCredit = false;
          } else{
              $acct_num = $r['acct_payer'];
              $isCredit = true;
          }
          $this->receiptArray[$i] = new Receipt($r['trans_id'], $r['trans_date'], $r['category_id'], $r['trans_amount'], $acct_num, $r['acct_name'], $r['category_name'], $r['description'], $isCredit, $balance);

          if($isCredit == false){
              $balance += $r['trans_amount'];
          }else{
              $balance -= $r['trans_amount'];
          }
      }//end for

  }//end function setReceipts()

  public function printReceiptRow($r){

      $toPrint = "<tr id=\"" . $r->receiptID . "\">";
      $toPrint = $toPrint . "<td>" . $r->date . "</td>";
      $toPrint = $toPrint . "<td>" . $r->category_name . "</td>";
      $toPrint = $toPrint . "<td>" . $r->acct_name . "</td>";
      $toPrint = $toPrint . "<td>" . $r->description . "</td>";


      if($r->isCredit == false){
          $toPrint = $toPrint . "<td> " . moneyFormat($r->amount, $this->currency_string) . "</td>";
          $toPrint = $toPrint . "<td> </td>";
      } else {
          $toPrint = $toPrint . "<td> </td>";
          $toPrint = $toPrint . "<td> " . moneyFormat($r->amount, $this->currency_string) . "</td>";
      }

      $toPrint = $toPrint . "<td>" . moneyFormat($r->balance, $this->currency_string) . "</td>";

      //add edit/delete form
      if(($this->canEdit) && !($r->isTransfer && !($this->userRole == "1"))){
        $toPrint = $toPrint . "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editReceipt($this->acct_id,$r->receiptID)\">create</label>" .
            "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDelete($this->acct_id,$r->receiptID)\">delete</label></td>";
      }else{
          $toPrint = $toPrint . "<td/>";
      }
      return $toPrint;
  }//end function printReceiptRow()

  private function displayPageNumbers(){
      //Pages
      $strToReturn = "<DIV class=\"w3-bar\" id=\"table-pages\">";

      //Page changing buttons
      if($this->numPages > 1){
        for ($i=0; $i<$this->numPages; $i++){
            $p = $i+1;
            if($i == $this->pageNum){
                $strToReturn = $strToReturn . "<BUTTON id=\"currPageNum\" class=\"w3-button w3-green\">" . $p . "</button>";
            }else{
                $strToReturn = $strToReturn . "<BUTTON class=\"w3-button\" onclick=\"showReceiptPage(". $this->acct_id . ",".
                $i . ")\">" . $p . "</BUTTON>";
            }
        }//end for
      }//end if
      $strToReturn = $strToReturn . "</DIV>";

      return $strToReturn;
  }//end function displayPageNumbers()

  public function printReceiptPage(){
      $strToReturn = "";
      //$this->pageNum = $pageNum;

      $strToReturn = $strToReturn . "<DIV class=\"w3-container\" id=\"receipt-container\">";

      $strToReturn = $strToReturn . "<DIV class=\"w3-container\" id=\"add-receipt-bar\">";
      if($this->canEdit){
        $strToReturn = $strToReturn . "<BUTTON type=\"button\" onclick=\"addReceiptTypeButtons(" . $this->acct_id . ")\">ADD RECEIPT</BUTTON>";
      }
      $strToReturn = $strToReturn . "<BR></DIV><BR>";//close add-receipt-bar

      //Table
      $strToReturn = $strToReturn . "<DIV class=\"w3-container\" id=\"receipt-table\">";
      $strToReturn = $strToReturn . $this->displayPageNumbers();

      //create editable form
      $strToReturn = $strToReturn . "<FORM name=\"editReceiptForm\" id=\"editReceiptForm\" action=\"edit-receipt.php\" method=\"POST\" autocomplete=\"off\">";

      $strToReturn = $strToReturn . "<table class=\"w3-table-all\">";
      $strToReturn = $strToReturn . "<tr><th>Date</th><th>Category</th><th>Account</th><th>Description</th><th>Amount Paid</th><th>Amount Received</th><th>Acct Balance</th><th>Modify</th></tr>";

      foreach($this->receiptArray as $r) {

          $strToReturn = $strToReturn . $this->printReceiptRow($r);
      }
      $strToReturn = $strToReturn . "</table></form>";
      $strToReturn = $strToReturn . "</div></div>"; //close receipt-table and receipt-container

      return $strToReturn;
  }//end function displayAccountReceipts()


}//end class Account


?>
