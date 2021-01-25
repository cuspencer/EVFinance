<?php
session_start();

require 'DBwrapper.php';

$categoryID = "";
$categoryName = "";
$replacementCategoryArray = array();
$dependentCategoryArray = array();
$replacementID = "";

//get category ID from AJAX request
if ($_SERVER["REQUEST_METHOD"] == "GET"){

  $categoryID = $_GET["cid"];

  $sqlReplacementCategories = "SELECT category_id, category_name FROM categories WHERE category_id !=" . $categoryID;
  $replacementCategoryArray = DBwrapper::DBselect($sqlReplacementCategories);

  $sqlCategoryInfo = "SELECT category_name FROM categories WHERE category_id =" . $categoryID;
  $categoryName = DBwrapper::DBselect($sqlCategoryInfo)[0]['category_name'];

  $sqlCategoryDependencies = "SELECT child_category FROM category_tree WHERE parent_category=" . $categoryID;
  $dependentCategoryArray = DBwrapper::DBselect($sqlCategoryDependencies);

  //CREATE DELETE CONFIRM MODAL
  echo "<div class=\"w3-modal-content largeModal\">";
  echo "<form id=\"confirmDeleteForm\" action=\"delete-category-processor.php\" method=\"POST\">";
  echo "<header class=\"w3-container w3-light-green\">";
  echo "<button  type=\"reset\" onclick=\"closeConfirmDeleteModal()\" class=\"w3-button w3-display-topright\">&times;</button>";
  echo "<h3>Deleting \"" . $categoryName . "\"</h3>";
  echo "<h6>Choose replacement category for associated receipts:</h6>";
  echo "</header>";

  //Drop-down lists of possible replacements
  echo "<div class=\"w3-container\">";
  if(sizeof($dependentCategoryArray) > 0){
    echo "<DIV class=\"w3-container errMsg\">"
    . "<H4>This category has dependent sub-categories. Please edit those categories first.</H4></DIV>";
  } else{
    echo "<DIV class=\"w3-container\"><H4>Replacement category for receipts</H4>";
    echo "<INPUT type=\"hidden\" name=\"categoryID\" value=\"" . $categoryID . "\"/>";
    echo "<select style=\"font-size:14px;\" required id=\"replacementCategoryID\" name=\"replacementCategoryID\">";

    //loop and add category options
    foreach ($replacementCategoryArray as $r){
      if($r['category_id'] != $categoryID){
        echo "<option value=" . $r['category_id'] . ">" . $r['category_id'] . " - " . $r['category_name'] . "</option>";
      }
    }
    echo "</select></div>";
  }

  echo "</div>";

  echo "<footer class=\"w3-container w3-light-green\">";
  echo "<button type=\"reset\" class=\"w3-button\" onclick=\"closeConfirmDeleteModal()\">Cancel</button>";
  if(sizeof($dependentCategoryArray) == 0){
    echo "<button type=\"submit\" class=\"w3-button\">Update and Delete</button>";
  }
  echo "</footer>";
  echo "</form></div>";
}//end if HTTP GET
