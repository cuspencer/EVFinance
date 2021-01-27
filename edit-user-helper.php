<?php
session_start();

require 'DBwrapper.php';

//get userID from AJAX request
$userID = $_GET["u"];

//get user info to populate fields
$sqlUserSelect = "SELECT * FROM users WHERE user_id=" . $userID;
$user_array = DBwrapper::DBselect($sqlUserSelect);
$userFName = $user_array[0]['fname'];
$userLName = $user_array[0]['lname'];
$userEmail = $user_array[0]['email'];
$userRole = $user_array[0]['user_role'];

//Create Editable Row
echo "<tr class=\"w3-table\" id=\"" . $userID . "\">";
echo "<input type=\"hidden\" name=\"userID\" value=\"$userID\"/>";
echo "<td><input type=\"text\" size=\"10\" required id=\"userFName\" name=\"userFName\" value=\"$userFName\"></td>";
echo "<td><input type=\"text\" size=\"15\" required id=\"userLName\" name=\"userLName\" value=\"$userLName\"></td>";
echo "<td><input type=\"text\" size=\"30\" required id=\"userEmail\" name=\"userEmail\" value=\"$userEmail\"></td>";
echo "<td><select id=\"userRole\" name=\"userRole\">";
if($userRole == 3){
  echo "<option value=\"3\" selected>User</option>";
  echo "<option value=\"2\">Moderator</option>";
  echo "<option value=\"1\">Admin</option>";
}else if($userRole == 2){
  echo "<option value=\"3\">User</option>";
  echo "<option value=\"2\" selected>Moderator</option>";
  echo "<option value=\"1\">Admin</option>";
}else{
  echo "<option value=\"3\">User</option>";
  echo "<option value=\"2\">Moderator</option>";
  echo "<option value=\"1\" selected>Admin</option>";
}
echo "</select></td>";

//Cancel
echo "<td><label title=\"cancel\" class=\"material-icons\" onclick=\"cancelUserEdit($userID, '$userFName', '$userLName', $userRole, '$userEmail')\">close</label>";

//Submit
echo "<label title=\"submit\" class=\"material-icons\" onclick=\"submitUserEdit()\">done</label></td></tr>";

?>
