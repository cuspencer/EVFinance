<div id="left-sidebar" class="w3-bar-block">
  <a href="my-profile.php" class="w3-bar-item w3-button">My Profile</a>
  <a href="view-accounts.php" class="w3-bar-item w3-button">Account Info</a>
  <?php 
  if($_SESSION['userRole'] != "3"){
    echo"<a href=\"reports.php\" class=\"w3-bar-item w3-button\">Reports</a>";
  }
  if($_SESSION['userRole'] == "1"){
    echo"<a href=\"set-passwords.php\" class=\"w3-bar-item w3-button\">Set Passwords</a>";
  }
  ?>
</div>