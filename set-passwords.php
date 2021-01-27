<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}
if($_SESSION['userRole'] != "1"){
    header("Location: login.php");
}
?>

<?php require 'header.php'?>
<?php require 'DBwrapper.php'?>

<div id="mainbody">
<?php require 'left_nav.php'?>

<div id="maincontent">
  <DIV id="admin-back-button" class="w3-container">
    <BUTTON onclick="location.href = 'admin-menu.php';">
      <span class="material-icons">reply</span>
      BACK
    </BUTTON>
  </DIV>
<?php

$errMsg = "";

//process POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['user'];
    $pwd = $_POST['password'];

    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);
    $sqlUpdate = "UPDATE users SET password=\"" . $hashedpwd . "\" WHERE user_id=" . $uid;
    $result = DBwrapper::DBupdate($sqlUpdate);

    if($result){
        $errMsg = "Password updated successfully!";
    }else{
        $errMsg = "Error updating password!";
    }
}


//get a list of users
$users_info_stmt = "SELECT * FROM users";
$results_array = DBwrapper::DBselect($users_info_stmt);

if(sizeof($results_array)>0){

    echo "<form method=\"POST\">";
    echo "SELECT A USER TO EDIT: ";
    echo "<select id=\"user\" name=\"user\">";

    foreach ($results_array as $r){
        echo "<option value=" . $r['user_id'] . ">" . $r['fname'] . " " . $r['lname'] . "</option>";
    }

    echo "</select>";
    echo "<input type=\"text\" name=\"password\"/>";
    echo "<input type=\"submit\" value=\"Submit\">";
    echo "</form>";
}
echo "<DIV class=\"errMsg\">" . $errMsg . "</DIV>";
?>
</div>
</div>
</body>
<?php require 'footer.php' ?>
