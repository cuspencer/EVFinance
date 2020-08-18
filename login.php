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


    //populate options from XML file
    $systems=simplexml_load_file("sysinfo.xml") or die("Error: Cannot create object");

    if($systems->count() > 1){
        $strToReturn = $strToReturn . "<option value=\"\" selected disabled>Select a system...</option>";
    }
    foreach($systems->system as $s){
        $sysname = $s->name;
        $strToReturn = $strToReturn . "<option>$sysname</option>";
    }

    $strToReturn = $strToReturn . "</SELECT></TD><TD/></TR>";
    $strToReturn = $strToReturn . "<TR><TD></TD><TD><INPUT type=\"submit\" value=\"Login\"/></TD><TD/></TR>";
    $strToReturn = $strToReturn . "<TR/><TR><TD/><TD><A HREF=\"password-forgot.php\">Forgot Password?</A></TD></TR>";
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


if (!isset($_SESSION['userID'])) {

    //process form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = test_input($_POST["username"]);
        $password = $_POST["password"];
        $sysname = $_POST["dbSystem"];

        //get DB info from XML
        $xmlpathstring = "/systems/system[name = \"$sysname\"]";
        $systems=simplexml_load_file("sysinfo.xml") or die("Error: Cannot create object");
        $dbsys = $systems->xpath($xmlpathstring)[0];

        $_SESSION['dbname'] = (string)$dbsys->database;
        $_SESSION['dblogin'] = (string)$dbsys->login;
        $_SESSION['dbpassword'] = (string)$dbsys->password;

        if(!test_email($email)){
          $errMessage = "Invalid email format.";
          echo showLoginScreen($errMessage);
        }else{
          $userQuery = "SELECT * FROM users WHERE email='$email'";
          $result = DBwrapper::DBselect($userQuery);
          $loginSuccess = false;

          if(count($result)>0){
            $loginSuccess = password_verify($password, $result[0]['password']);
          }

          if ($loginSuccess){
            $_SESSION['userID'] = $result[0]['user_id'];
            $_SESSION['fname'] = $result[0]['fname'];
            $_SESSION['lname'] = $result[0]['lname'];
            $_SESSION['email'] = $result[0]['email'];
            $_SESSION['userRole'] = $result[0]['user_role'];

            //set system session variables
            $currQuery = "SELECT * FROM sysinfo LEFT JOIN currencies ON sysinfo.sys_currency_id=currencies.currency_id";
            $sysResult = DBwrapper::DBselect($currQuery);
            $_SESSION['sysName'] = $sysResult[0]['sys_name'];
            $_SESSION['currencyID'] = $sysResult[0]['currency_id'];
            $_SESSION['currencySymbol'] = $sysResult[0]['symbol'];
            $_SESSION['currencyShort'] = $sysResult[0]['short'];

            require 'left_nav.php';
            echo showWelcomeScreen();
          } else {
            $errMessage = "Username and password don't match. Please try again.";
            echo showLoginScreen($errMessage);
          }
        }
      }
      else {
        echo showLoginScreen();
      }
} else{
    require 'left_nav.php';
    echo showWelcomeScreen();
}
require 'footer.php';
?>
