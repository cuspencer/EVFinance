<?php
//session_start();

class DBwrapper {
    
    private static $servername = "localhost";
    private static $username = "finance";
    private static $password = "EV_finance2020!";
    
    public static function DBupdate($update_stmt){
       
            
        $servername = self::$servername;
        $dbname = $_SESSION['dbname'];
        $username = $_SESSION['dblogin'];
        $password = $_SESSION['dbpassword'];
        $success = false;
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=UTF8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($update_stmt);
            $success = $stmt->execute();
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage() . "<BR>";
        }
        $conn = null;
 
        return $success;
    }//end function DBinsert()
    
    
    public static function DBselect($select_stmt){
        $servername = self::$servername;
        $dbname = $_SESSION['dbname'];
        $username = $_SESSION['dblogin'];
        $password = $_SESSION['dbpassword'];
        $results = array();
        
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=UTF8", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare($select_stmt);
            $stmt->execute();
            
            if($stmt->setFetchMode(PDO::FETCH_ASSOC)){
                $results = $stmt->fetchAll();
            }
            
        }catch(PDOException $e) {
            echo "Error: " . $e->getMessage() . "<BR>";
        }
        $conn = null;
        
        return $results;
        
    }//end function DBselect
   
    
}//end class DBwrapper
?>