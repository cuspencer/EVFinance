<?php
session_start();

require 'DBwrapper.php';

$categoryArray = array();

$sqlReplacementCategories = "SELECT category_id, category_name FROM categories";
$categoryArray = DBwrapper::DBselect($sqlReplacementCategories);

//CREATE DELETE CONFIRM MODAL
echo "<div class=\"w3-modal-content largeModal\">";
echo "<form id=\"addCategoryForm\" action=\"add-category-processor.php\" method=\"POST\">";
echo "<header class=\"w3-container w3-light-green\">";
echo "<button  type=\"reset\" onclick=\"closeAddCategoryModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
echo "<h3>Add a Category</h3>";
echo "<h6>Note: A Category's ID is used to order categories in a report.</h6>";
echo "<div class=\"errMsg\" id=\"addCategoryModalErrorMessage\"></div>";
echo "</header>";

//Drop-down lists of possible replacements
echo "<div class=\"w3-container\">";
echo "<table>";
echo "<tr><td>Name:</td><td><input type=\"text\" size=\"25\" id=\"addCategoryName\" name=\"addCategoryName\" required/></td></tr>";
echo "<tr><td>ID:</td><td><input type=\"text\" size=\"6\"  id=\"addCategoryID\" name=\"addCategoryID\" required/></td></tr>";
echo "<tr><td>Nest under:</td>";

//loop and add parent category options
echo "<td><select style=\"font-size:14px;\" required id=\"parentCategoryID\" name=\"parentCategoryID\">";
foreach ($categoryArray as $c){
  echo "<option value=" . $c['category_id'] . ">" . $c['category_id'] . " - " . $c['category_name'] . "</option>";
}
echo "</select></td></tr></table>";
echo "</div>";

echo "<footer class=\"w3-container w3-light-green\">";
echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeAddCategoryModal()\">Cancel</button>";
echo "<DIV class=\"w3-button\" onclick=\"addCategory()\">Add Category</DIV>";
echo "</footer>";
echo "</form></div>";
