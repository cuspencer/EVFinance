<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
}
if($_SESSION['userRole'] == "3"){ //test this!
    alert("NO ACCESS");
    header("Location: login.php");
}
?>

<?php require 'header.php'?>
<?php require 'DBwrapper.php'?>

<div id="mainbody">
<?php require 'left_nav.php'?>

<div id="maincontent">

<?php

//report type - monthly or annual, AJAX populate by earliest trans
echo "<FORM ACTION=\"report-viewer.php\" METHOD=\"GET\">";
echo "<DIV id=\"reportTypeChooser\" class=\"w3-container\">"; 
echo "<H3>What type of report?</H3><BR>";
echo "<label>Annual<input type=\"radio\" name=\"reportType\" value=\"1\" onclick=\"setReportType(this.value)\"/> </label>";
echo "<label>Monthly<input type=\"radio\" name=\"reportType\" value=\"2\" onclick=\"setReportType(this.value)\"/> </label>";
echo "</DIV>";

//DIV for AJAX results
echo "<DIV id=\"reportSelector\" class=\"w3-container\"></DIV>"; 
echo "</FORM>";
?>

</div>
</div>
</body>
<?php require 'footer.php' ?>