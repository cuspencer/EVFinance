<?php
require 'ref/money/helpers.php';
require 'ref/money/Currency.php';
require 'ref/money/Money.php';

class Receipt {
    
    //receipt details
    public $receiptID; //hidden form field (edit/delete receipt)
    public $isCredit = false; //credit or debit, default is debit
    public $date;
    public $amount;
    public $acct_name;
    public $acct_num;
    public $categoryID;
    public $category_name;
    public $description;
    public $balance;
    
    function __construct($receiptID, $date, $categoryID, $amount, $acct_num, $acct_name,  $category_name, $description, $isCredit, $balance){
        $this->receiptID = $receiptID;
        $this->date = $date;
        $this->amount = $amount;
        $this->acct_num = $acct_num;
        $this->acct_name = utf8_encode($acct_name);
        $this->categoryID = $categoryID;
        $this->category_name = utf8_encode($category_name);
        $this->description = utf8_encode($description);
        $this->isCredit = $isCredit;
        $this->balance = $balance;
    }//end function (__construct)
    
    
}//end class Receipt


?>