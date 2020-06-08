<div id="left-sidebar" class="w3-bar-block">
  <a href="#" class="w3-bar-item w3-button">My Profile</a>
  <a href="view-accounts.php" class="w3-bar-item w3-button">Account Info</a>
  <?php 
  if($_SESSION['userRole'] != "3"){
    echo"<a href=\"reports.php\" class=\"w3-bar-item w3-button\">Reports</a>";
  }
  ?>
</div>