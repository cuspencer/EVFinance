<?php

//require 'DBwrapper.php';
class AccountBalance{
    
    public $accountName = "";
    public $accountBalance = "";
    public $accountType ="";
    
    public function __construct($accountName, $accountBalance, $accountType){
        $this->accountName = $accountName;
        $this->accountBalance = $accountBalance;
        $this->accountType = $accountType;
    }//end function __construct()
}//end class AccountBalance



class AccountBalanceReport{
    
    public $cashAccounts = array();
    public $bankAccounts = array();
    public $totalCash = "0";
    
    private $reportType = "";
    private $reportMonth = "";
    private $reportYear = "";
    
    public function __construct($reportType, $reportMonth, $reportYear){
        $this->reportType = $reportType;
        $this->reportMonth = $reportMonth;
        $this->reportYear = $reportYear;
        
        $this->setAccountBalances();
        
    }//end function __construct()
    
    
    private function setAccountBalances(){
 
        $accountsArray = array();
        $transArray = array();
        
        $sqlAccountQuery = "SELECT * FROM accounts WHERE acct_active=1 AND acct_type < 3 ORDER BY acct_type, acct_name";
        $sqlTransQuery = "";
        $sqlTransDate = "";

        if($this->reportType == 2){
            $nextMonth = $this->reportMonth + 1;
            $nextYear = $this->reportYear;
            if($nextMonth == 13){
                $nextMonth = 1;
                $nextYear++;
            }
            if($nextMonth < "10"){
                $nextMonth = "0" . $nextMonth;
            }
            $sqlTransDate = " trans_date > \"" . $nextYear . "-" . $nextMonth . "-01\"";
        }
        else{
            $sqlTransDate = " year(trans_date) > " . $this->reportYear;
        }
        
        $accountsArray = DBwrapper::DBselect($sqlAccountQuery);
        $this->numAccounts = count($accountsArray);
        foreach($accountsArray as $a){
            $sqlTransQuery = "SELECT * FROM transactions WHERE (acct_payer=" . $a['acct_id'] . " OR acct_receiver=" . $a['acct_id'] .
            ") AND " . $sqlTransDate;
            
            
            $transArray = DBwrapper::DBselect($sqlTransQuery);
            
            $tempBalance = $a['acct_balance'];
            foreach($transArray as $t){
                if($t['acct_payer'] == $a['acct_id']){
                    $tempBalance += $t['trans_amount'];
                }
                else{
                    $tempBalance -= $t['trans_amount'];
                }
            }
            
            
            if($a['acct_type'] == 1){//cash account
                array_push($this->cashAccounts, new AccountBalance($a['acct_name'], $tempBalance, 1));
            }else if($a['acct_type'] == 2){
                array_push($this->bankAccounts, new AccountBalance($a['acct_name'], $tempBalance, 2));
            }
            $this->totalCash += $tempBalance;
        }//foreach
        
    }//end function setAccountBalances()
    
    
}//end class AccountBalanceReport
    
?>