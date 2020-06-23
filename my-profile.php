<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}

require 'header.php';
require 'DBwrapper.php';
?>

<div id="mainbody">
<?php require 'left_nav.php'?>

<div id="maincontent">

<?php

$fname = "";
$lname = "";
$email = "";
$fnameErr = "";
$lnameErr = "";
$emailErr = "";
$userID = $_SESSION['userID'];
$noErr = true;


function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//check and process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname= test_input($_POST["fname"]);
    $lname = test_input($_POST["lname"]);
    $email = test_input($_POST["email"]);
    

    if (!preg_match("/^[a-zA-Z ]*$/",$fname)) {
        $fnameErr = "Only letters and white space allowed";
        $noErr = false;
    }
    
    if (!preg_match("/^[a-zA-Z ]*$/",$lname)) {
        $lnameErr = "Only letters and white space allowed";
        $noErr = false;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
        $noErr = false;
    }
    
    //query DB for existent email
    $emailQuery = "SELECT user_id FROM users WHERE email=\"" . $email . "\"";
    
    //update the DB if valid
    $userQuery = "UPDATE users SET fname=\"" . $fname . "\", lname=\"" . $lname . "\", email=\"" . $email . "\" WHERE " .
        "user_id=" . $userID;
    if($noErr){
        $emailResult = DBwrapper::DBselect($emailQuery);
        
        if(count($emailResult) > 0){
            $uid = $emailResult[0]['user_id'];
            if($uid != $userID){
                $emailErr = "This email is currently assigned to another user";
                $noErr = false;
            }
        }
        
        if($noErr){
            $updateResult = DBwrapper::DBupdate($userQuery);
            if(!$updateResult){
                //display error message
                echo "ERROR IN DB UPDATE";
            }
        }
    }//end if
}//end if (form processing)
else{
    //query DB for user info
    $userQuery = "SELECT fname, lname, email FROM users WHERE user_id=" . $userID;
    $userResult = DBwrapper::DBselect($userQuery);
    $fname = $userResult[0]['fname'];
    $lname = $userResult[0]['lname'];
    $email = $userResult[0]['email'];
}


//CREATE PASSWORD EDITING MODAL
echo "<div id=\"passwordModal\" class=\"w3-modal\">";
echo "<div class=\"w3-modal-content mediumModal\">";
echo "<header class=\"w3-container w3-light-green\">";
echo "<button type=\"reset\" onclick=\"closePasswordModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
echo "<h3>Change Password</h3>";
echo "</header>";
echo "<div class=\"w3-container\" id=\"passwordFormHolder\">";
echo "<form id=\"passwordForm\">";
echo "<DIV>Current Password <input type=\"password\" name=\"oldPassword\" id=\"oldPassword\"/></DIV>";
echo "<DIV>New Password <input type=\"password\" name=\"newPassword1\" id=\"newPassword1\"/></DIV>";
echo "<DIV>New Password <input type=\"password\" name=\"newPassword2\" id=\"newPassword2\"/></DIV>";
echo "<DIV id=\"passwordError\" class=\"errMsg\"></DIV>";
echo "</form>";
echo "</div>";
echo "<footer class=\"w3-container w3-light-green\">";
echo "<button class=\"w3-button\" onclick=\"closePasswordModal()\">Cancel</button>";
echo "<button class=\"w3-button\" onclick=\"submitPassword()\">Update</button>";
echo "</footer>";
echo "</div></div>";

//display form and error msgs
echo "<H3> My Profile </H3>";
echo "<FORM id=\"userForm\" METHOD=\"POST\">";
echo "<TABLE>";
echo "<TR><TD>First Name(s)</TD>";
echo "<TD><INPUT type=\"text\" name=\"fname\" id=\"fname\" onchange=\"enableSubmit()\" defaultValue=\"" . $fname ."\" value=\"" . $fname ."\"/></TD>";
echo "<TD><LABEL id=\"fnameErr\" class=\"errMsg\">" . $fnameErr . "</LABEL></TD>";
echo "</TR>";

echo "<TR><TD>Last Name(s)</TD>";
echo "<TD><INPUT type=\"text\" name=\"lname\" id=\"lname\" onchange=\"enableSubmit()\" defaultValue=\"" . $lname ."\" value=\"" . $lname ."\"/></TD>";
echo "<TD><LABEL id=\"lnameErr\" class=\"errMsg\">" . $lnameErr . "</LABEL></TD>";
echo "</TR>";

echo "<TR><TD>Email</TD>";
echo "<TD><INPUT type=\"text\" name=\"email\" id=\"email\" onchange=\"enableSubmit()\" defaultValue=\"" . $email ."\" value=\"" . $email ."\"/></TD>";
echo "<TD><LABEL id=\"emailErr\" class=\"errMsg\">" . $emailErr . "</LABEL></TD>";
echo "</TR></TABLE>";

echo "<INPUT type=\"submit\" id=\"submitButton\" value=\"Save Changes\" disabled/>";
echo "</FORM>";
echo "<button onclick=\"showPasswordModal()\">Change Password</button>";
?>

</div>
</div>
</body>
<?php require 'footer.php' ?>