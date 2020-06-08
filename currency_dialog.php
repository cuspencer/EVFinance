<?php session_start(); ?>


<div id="currencyModal" class="w3-modal">
<div class="w3-modal-content">
  <form id="currencyForm" method="POST">
    <header class="w3-container w3-teal">
      <span onclick="document.getElementById('currencyModal').style.display='none'"
      class="w3-button w3-display-topright">&times;</span>
      <h2>Update Exchange Rates</h2>
    </header>

<?php 
require 'DBwrapper.php';

//select all currencies
$sqlCurrencySelect = "SELECT * FROM currencies";
$currencyArray = DBwrapper::DBselect($sqlCurrencySelect);

echo "<div class=\"w3-container\">";

foreach($currencyArray as $c){
    $eRate = $c['exchange_rate'];
    
    echo "<div class=\"exchangeRateDisplay\">";
    echo "<span>" . $c['name'] . "</span>";
    echo "<input type=\"text\" class=\"currInput\" name=\"" . $c['short'] . "\" value=\"" . $eRate . "\"/>";
    echo "</div>";
}//end foreach



echo "</div>";
?>
  
  
    <footer class="w3-container w3-teal">
      <button type="button" class="modalbtn cancelbtn" 
      onclick="document.getElementById('currencyModal').style.display='none'">Cancel</button>
      <button type="button" class="modalbtn updatebtn">Update</button>
    </footer>
  </form>
</div>
</div>

