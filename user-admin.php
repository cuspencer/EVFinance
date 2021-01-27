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


function printAddUserModal(){
  //create add user modal
  echo "<div id=\"addUserModal\" class=\"w3-modal\">";
  echo "<div class=\"w3-modal-content\">";
  echo "<form id=\"addUserForm\" action=\"add-user-processor.php\" method=\"POST\">";
  echo "<header class=\"w3-container w3-light-green\">";
  echo "<button  type=\"reset\" onclick=\"closeAddUserModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
  echo "<h3>Add a new User</h3>";
  echo "<h6>Note: Associated account will be named using the user's first and last names.</h6>";
  echo "<div class=\"errMsg\" id=\"addUserModalErrorMessage\"></div>";
  echo "</header>";

  echo "<div class=\"w3-container\">";
  echo "<table>";
  echo "<tr><td>First Name:</td><td><input type=\"text\" required id=\"newUserFName\" name=\"newUserFName\"></td>";
  echo "<td>Last Name:</td><td><input type=\"text\" required id=\"newUserLName\" name=\"newUserLName\"></td></tr>";
  echo "<tr><td>Email:</td><td><input type=\"text\" required id=\"newUserEmail\" name=\"newUserEmail\"></td></tr>";
  echo "<tr><td>Role:</td><td><select name=\"newUserRole\">";
  echo "<option value=\"3\" selected>User</option>";
  echo "<option value=\"2\">Moderator</option>";
  echo "<option value=\"1\">Admin</option>";
  echo "</select></td>";
  echo "<td><input type=\"checkbox\" name=\"createAccount\">Create associated Account?</input></td></tr>";
  echo "</table>";
  echo "</div>";

  echo "<footer class=\"w3-container w3-light-green\">";
  echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeAddUserModal()\">Cancel</button>";
  echo "<DIV class=\"w3-button\" onclick=\"addUser()\">Create User</DIV>";
  echo "</footer>";
  echo "</form></div></div>";
}//end printAddUserModal

function printDeleteUserModal(){
  //create user deletion modal
  echo "<div id=\"confirmUserDeleteModal\" class=\"w3-modal\">";
  echo "<div class=\"w3-modal-content\">";
  echo "<form id=\"deleteUserForm\" action=\"delete-user-processor.php\" method=\"POST\">";
  echo "<input type=\"hidden\" name=\"userID\" id=\"confirmUserDeleteID\" value=\"\"></input>";
  echo "<header class=\"w3-container w3-light-green\">";
  echo "<button  type=\"reset\" onclick=\"closeConfirmDeleteUserModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
  echo "<h3>Delete User</h3>";
  echo "<h6>Notes: Any associated accounts must be deleted individually. ";
  echo "The last Admin in a system cannot be deleted. To delete an admin, promote another user to Admin.</h6>";
  echo "</header>";

  echo "<div class=\"w3-container\">";
  echo "Are you sure you want to delete ";
  echo "<label id=\"confirmUserDeleteName\"></label>?";
  echo "</div>";

  echo "<footer class=\"w3-container w3-light-green\">";
  echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeConfirmDeleteUserModal()\">Cancel</button>";
  echo "<DIV class=\"w3-button\" onclick=\"deleteUser()\">Delete User</DIV>";
  echo "</footer>";
  echo "</form></div></div>";
}//end printDeleteUserModal()

function printUsersTable(){

  //select all users
  $sqlUserSelect = "SELECT * FROM users";
  $users_array = DBwrapper::DBselect($sqlUserSelect);

  //create editable form
  $strToReturn = "<DIV id=\"users_table\" class=\"w3-container\">";
  $strToReturn .= "<FORM id=\"editUserForm\" name=\"editUserForm\" action=\"\" method=\"POST\" autocomplete=\"off\">";
  $strToReturn .= "<table class=\"w3-table-all\">";
  $strToReturn .= "<tr><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Modify</th></tr>";

  foreach($users_array as $u) {
    $userID = $u['user_id'];
    $userFName = $u['fname'];
    $userLName = $u['lname'];
    $userRole = $u['user_role'];
    $userEmail = $u['email'];
    $fullName = $userFName . " " . $userLName;

    $strToReturn .= "<tr id=\"" . $userID . "\">";
    $strToReturn .= "<td>" . $userFName . "</td>";
    $strToReturn .= "<td>" . $userLName . "</td>";
    $strToReturn .= "<td>" . $userEmail . "</td>"; //hyperlink mailto?
    if($userRole == 3){
      $strToReturn .= "<td>User</td>";
    }else if ($userRole == 2){
      $strToReturn .= "<td>Moderator</td>";
    }else if ($userRole == 1){
      $strToReturn .= "<td>Admin</td>";
    }else{
      $strToReturn .= "<td>ERROR: contact sysadmin</td>";
    }

    $strToReturn .= "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editUser($userID)\">create</label>";
    $strToReturn .= "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteUser($userID, '$fullName')\">delete</label></td>";
    $strToReturn .= "</tr>";
  }
  $strToReturn = $strToReturn . "</table></form>";
  $strToReturn = $strToReturn .  "</DIV>"; //end users_table
  return $strToReturn;
}//end function printUsersTable()


//Process success and error messages from adding/deleting users
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
    $errorMessage = "A database error occurred when deleting the user.";
  } else if ($dResult == "1"){
    $successMessage = "User successfully deleted.";
  }

  if($aResult == "2"){
    $errorMessage = "A database error occurred when adding the user.";
  } else if ($aResult == "1"){
    $successMessage = "User successfully added.";
  }

}//end GET processing

//process POST data for user editing
if ($_SERVER["REQUEST_METHOD"] == "POST"){
  $userID = $_POST["userID"];
  $userFName = test_input($_POST["userFName"]);
  $userLName = test_input($_POST["userLName"]);
  $userRole = $_POST["userRole"];
  $userEmail = test_email($_POST["userEmail"]);

  if(!$userEmail){
    $errorMessage = "Error updating user: invalid email address";
  }else{
    $sqlUpdateUser = "UPDATE users SET fname='$userFName', lname='$userLName', email='$userEmail'," .
      " user_role=$userRole WHERE user_id=" . $userID;
      $success = DBwrapper::DBupdate($sqlUpdateUser);
    if($success){
      $successMessage = "User edited successfully.";

      //find account owned by this user
      $sqlAccountOwner = "SELECT acct_id FROM acct_user_access WHERE user_id=$userID AND owner=1";
      $accountOwner = DBwrapper::DBselect($sqlAccountOwner);
      $userAccount = "";
      if(sizeof($accountOwner) > 0){
        $userAccount = $accountOwner[0]['acct_id'];
      }

      //update admin/moderator access. Delete all but own, then add back.
      $sqlAccessDelete = "DELETE FROM acct_user_access WHERE user_id=$userID AND owner!=1";
      DBwrapper::DBupdate($sqlAccessDelete);

      if($userRole != "3"){
        $sqlAccountSelect = "SELECT DISTINCT acct_id FROM acct_user_access";
        if($userAccount != ""){
          $sqlAccountSelect .= " WHERE acct_id !=$userAccount";
        }
        $adminArray = DBwrapper::DBselect($sqlAccountSelect);
        $sqlAccessInsert = "INSERT INTO acct_user_access (user_id, acct_id, owner, view, edit) VALUES ";
        foreach ($adminArray as $a) {
          $acctID = $a['acct_id'];
          if($userRole == "1"){
            $sqlAccessInsert .= "($userID,$acctID,0,1,1),";
          }else {
            $sqlAccessInsert .= "($userID,$acctID,0,1,0),";
          }
        }
        $sqlAccessInsert = rtrim($sqlAccessInsert, ", "); //trim final comma
        DBwrapper::DBupdate($sqlAccessInsert);
      }

    }else{
      $errorMessage = "Error editing user.";
    }
  }

}//end if POST


echo "<DIV id=\"maincontent\" class=\"w3-container\">";

echo "<DIV id=\"titlearea\" class=\"w3-container\">";
echo "<H3>User Editor</H3>";
echo "<DIV id=\"addUserButtonHolder\" class=\"w3-container\">";
echo "<BUTTON type=\"button\" onclick=\"showAddUserModal()\">ADD USER</BUTTON>";
echo "<DIV id=\"errorMessage\" class=\"errMsg\" />$errorMessage</DIV>";
echo "<DIV id=\"successMessage\" class=\"w3-green\" />$successMessage</DIV>";
echo "</DIV></DIV>";

//Placeholders for modal
echo printAddUserModal();
echo printDeleteUserModal();
echo printUsersTable();
echo "</DIV>"; //end maincontent

require 'footer.php';
?>
