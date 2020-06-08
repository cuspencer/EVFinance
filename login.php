<?php
session_start();

require 'header.php';
require 'DBwrapper.php';


function showLoginScreen($errMessage = ""){
    
    $strToReturn = "";
    
    $strToReturn = $strToReturn . "<DIV id=\"maincontent\" class=\"w3-container\">";
    if($errMessage == ""){
        $strToReturn = $strToReturn . "<H3>Please Login</H3>";
    }else{
        $strToReturn = $strToReturn . "<H3>" . $errMessage . "</H3>";
    }
    $strToReturn = $strToReturn . "<FORM id=\"login-form\" name=\"login-form\" action=\"\" method=\"POST\">";
    $strToReturn = $strToReturn . "<TABLE id=\"login-table\" name=\"login-table\">";
    $strToReturn = $strToReturn . "<TR><TD>Username</TD>";
    $strToReturn = $strToReturn . "<TD><INPUT required type=\"text\" name=\"username\" autocomplete=\"username\"/></TD>";
    $strToReturn = $strToReturn . "<TD><DIV id=\"usernameErr\" class=\"errMsg\"/></TD></TR>";
    $strToReturn = $strToReturn . "<TR><TD>Password</TD>";
    $strToReturn = $strToReturn . "<TD><INPUT required type=\"password\" name=\"password\" autocomplete=\"current-password\"/></TD>";
    $strToReturn = $strToReturn . "<TD><DIV id=\"passwordErr\" class=\"errMsg\"/></TD></TR>";
    $strToReturn = $strToReturn . "<TR><TD>System</TD>";
    $strToReturn = $strToReturn . "<TD><SELECT required id=\"dbSystem\" name=\"dbSystem\">";
    
    //populate from XML file?
    //$strToReturn = $strToReturn . "<option value=\"\" selected disabled>Select a system...</option>";
    $strToReturn = $strToReturn . "<option value=\"envivo_test\">En Vivo Test</option>";
    
    $strToReturn = $strToReturn . "</SELECT></TD><TD/></TR>";
    $strToReturn = $strToReturn . "<TR><TD></TD><TD><INPUT type=\"submit\" value=\"Login\"/></TD><TD/></TR>";
    $strToReturn = $strToReturn . "</TABLE></FORM></DIV>";
    
    return $strToReturn;
    
}//end function showLoginScreen()


function showWelcomeScreen(){
    $strToReturn = "";
    $strToReturn = $strToReturn . "<DIV id=\"maincontent\" class=\"w3-container\">";
    $strToReturn = $strToReturn . "Welcome, " .  $_SESSION['fname'] . "!";
    $strToReturn = $strToReturn . "</DIV>";
    
    return $strToReturn;    
}//end function showWelcomeScreen()


//process form, set userID

if (!isset($_SESSION['userID'])) {
    
    //process form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["username"];
        $password = $_POST["password"];
        $dbname = $_POST["dbSystem"];
        
        //set DB to query
        $_SESSION['dbname'] = $dbname;
        
        $query = "SELECT * FROM users INNER JOIN currencies ON users.default_currency=currencies.currency_id WHERE " . 
        "email='$email' and password='$password'";
        
        $result = DBwrapper::DBselect($query);
        $count = count($result);
        
        if ($count > 0){
            $_SESSION['userID'] = $result[0]['user_id'];
            $_SESSION['fname'] = $result[0]['fname'];
            $_SESSION['lname'] = $result[0]['lname'];
            $_SESSION['email'] = $result[0]['email'];
            $_SESSION['userRole'] = $result[0]['user_role'];
            
            //SET THESE VIA DB QUERY?
            $_SESSION['currencyID'] = $result[0]['currency_id'];
            $_SESSION['currencySymbol'] = $result[0]['symbol'];
            $_SESSION['currencyShort'] = $result[0]['short'];
            
            
            require 'left_nav.php';
            //echo "<H3>LOGIN SUCCESSFUL!</H3><BR>";
            echo showWelcomeScreen();
        }
        else {
            //require 'left_nav.php';
            $errMessage = "LOGIN ERROR!";
            echo showLoginScreen($errMessage);
        }
    }  
    else {
        echo showLoginScreen();
    }
} 
else{
    require 'left_nav.php';
    echo showWelcomeScreen();
    
}
require 'footer.php';
?>