<?php

//require 'DBwrapper.php';

class CashFlowCategory{

    private $categoryID = "";
    private $categoryName = "";
    private $categoryType = "";
    private $categoryCashFlowTotal = "0";
    private $childCategories = array();
    
    private $reportType = "";
    private $reportMonth = "";
    private $reportYear = "";
    
    public function __construct($categoryID, $reportType, $reportMonth, $reportYear){
        $this->categoryID = $categoryID;
        $this->reportType = $reportType;
        $this->reportMonth = $reportMonth;
        $this->reportYear = $reportYear;
        
        $this->categoryInfo();
        $this->findChildren();
        $this->categoryCashFlowTotal += $this->getSubTotal();
        
        if($this->categoryType == "2"){
            $this->categoryCashFlowTotal += $this->getMyCashFlow();
        }
        
    }//end function __construct()
    
    
    private function categoryInfo(){
        //get info
        $sqlCategoryInfoQuery = "SELECT * from categories WHERE category_id=" . $this->categoryID;
        $categoryInfo = DBwrapper::DBselect($sqlCategoryInfoQuery)[0];
        $this->categoryName = $categoryInfo['category_name'];
        $this->categoryType = $categoryInfo['category_type'];
    }
    
    private function findChildren(){
        $sqlCategoryChildrenQuery = "SELECT child_category FROM category_tree WHERE parent_category=" . $this->categoryID;
        $catChildrenArray = DBwrapper::DBselect($sqlCategoryChildrenQuery);
        
        //iterate and construct
        foreach ($catChildrenArray as $c){
            array_push($this->childCategories, 
                new CashFlowCategory($c['child_category'], $this->reportType, $this->reportMonth, $this->reportYear));
        }
    }//end function findChildren()
    
    private function getSubTotal(){
        $total = "0";
                 
        foreach($this->childCategories as $c){
            $total += $c->categoryCashFlowTotal;
        }
        return $total;
    }//end function getSubTotal()

    
    private function getMyCashFlow(){
        $total = "0";
        
        $sqlTransQuery = "select * FROM transactions WHERE year(trans_date)=" . $this->reportYear;
        if($this->reportType == 2){
            $sqlTransQuery = $sqlTransQuery . " AND month(trans_date)=" . $this->reportMonth;
        }
        $sqlTransQuery = $sqlTransQuery . " AND category_id=" . $this->categoryID;
        
        $transArray = DBwrapper::DBselect($sqlTransQuery);
        
        foreach ($transArray as $t){
            $cid = (int)$this->categoryID;
            if($cid > 2000){ //outflows
                if($t['acct_payer'] < "99"){
                    $total += $t['trans_amount'];
                }else{
                    $total -= $t['trans_amount'];
                }
            }else { //inflows
                if($t['acct_payer'] < "99"){
                    $total -= $t['trans_amount'];
                }else{
                    $total += $t['trans_amount'];
                }
            }//end if-else
        }//end foreach
        return $total;
    }//end function getMyCashFlow()
    
    public function getName(){
        return $this->categoryName;
    }
    
    public function getType(){
        return $this->categoryType;
    }
    
    public function getTotal(){
        return $this->categoryCashFlowTotal;
    }
    
    public function getChildren(){
        return $this->childCategories;
    }
    
} //end class CashFlowCategory

?>