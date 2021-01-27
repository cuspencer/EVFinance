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

function printCategoryTable(){
    //select all categories
    $category_select_stmt = "SELECT category_id, child_name, parent_name, parent_id FROM
        (SELECT categories.category_id, categories.category_name AS child_name, category_tree.parent_category AS parent_id
         FROM categories INNER JOIN category_tree WHERE categories.category_id=category_tree.child_category
      AND categories.category_type > 0) AS CHILD,
        (SELECT category_id AS ID, category_name AS parent_name FROM categories) AS PARENT
        WHERE CHILD.parent_id = PARENT.ID
        ORDER BY category_id";

    $results_array = DBwrapper::DBselect($category_select_stmt);

    //create editable form
    $strToReturn = "<DIV id=\"category_table\" class=\"w3-container\">";
    $strToReturn = $strToReturn . "<FORM name=\"editCategoryForm\" id=\"editCategoryForm\" action=\"\" method=\"POST\" autocomplete=\"off\">";
    $strToReturn = $strToReturn . "<table class=\"w3-table-all\">";
    $strToReturn = $strToReturn . "<tr><th>ID</th><th>Name</th><th>Parent Category</th><th>Modify</th></tr>";

    foreach($results_array as $r) {
      $catID = $r['category_id'];
      $catName = $r['child_name'];
      $parentID = $r['parent_id'];
      $parentName = $r['parent_name'];

      $strToReturn .= "<tr id=\"" . $catID . "\">";
      $strToReturn .= "<td class=\"categoryIDLabel\">" . $catID . "</td>";
      $strToReturn .= "<td class=\"categoryNameLabel\">" . $catName . "</td>";
      $strToReturn .= "<td>" . $parentID. " - " . $parentName . "</td>";
      $strToReturn .= "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editCategory($catID)\">create</label>" .
            "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteCategory($catID)\">delete</label></td>";
      $strToReturn .= "</tr>";
    }
    $strToReturn = $strToReturn . "</table></form>";
    $strToReturn = $strToReturn .  "</DIV>"; //end category_table
    return $strToReturn;
}//end function printCategoryTable()

$oldCategoryId = "";
$newCategoryId = "";
$newCategoryName = "";
$parentId = "";
$errorMessage = "";
$successMessage = "";

//Process success and error messages from adding/deleting categories
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
    $errorMessage = "A database error occurred when deleting the category.";
  } else if ($dResult == "1"){
    $successMessage = "Category successfully deleted.";
  }

  if($aResult == "2"){
    $errorMessage = "A database error occurred when adding the category.";
  } else if ($aResult == "1"){
    $successMessage = "Category successfully added.";
  }

}//end GET processing

//process POST data
if ($_SERVER["REQUEST_METHOD"] == "POST"){

  $oldCategoryId = $_POST["oldCategoryId"];
  $newCategoryId = $_POST["childCategoryId"];
  $newCategoryName = test_input($_POST["childCategoryName"]);
  $parentId = $_POST["parentCategoryId"];

  $sqlUpdateTransactions = "";
  $sqlUpdateCategory = "";
  $sqlUpdateParent = "";
  $sqlUpdateChildren = "";
  $successResult = true;
  $successResult2 = true;
  $successResult3 = true;

  //update name and transactions
  if($oldCategoryId == $newCategoryId){
      //if ID didn't change, just update name
      $sqlUpdateCategory = "UPDATE categories SET category_name=\"" . $newCategoryName .
         "\" WHERE category_id=" . $oldCategoryId;
      $successResult = DBwrapper::DBupdate($sqlUpdateCategory);
      if(!$successResult){
        $errorMessage .= "Error updating new name.\n";
      }
  } else{
      $sqlUpdateCategory = "UPDATE categories SET category_name=\"" . $newCategoryName . "\", category_id=" .
        $newCategoryId . " WHERE category_id=" . $oldCategoryId;
      $successResult = DBwrapper::DBupdate($sqlUpdateCategory);

      //update transactions and child categories
      if($successResult){
        $sqlUpdateTransactions = "UPDATE transactions SET category_id=" . $newCategoryId .
          " WHERE category_id=" . $oldCategoryId;
        $successResult2 = DBwrapper::DBupdate($sqlUpdateTransactions);
        if(!$successResult2){
          $errorMessage .= "Error updating transactions.\n";
        }

        $sqlUpdateChildren = "UPDATE category_tree SET parent_category=" . $newCategoryId .
          " WHERE parent_category=" . $oldCategoryId;
        $successResult3 = DBwrapper::DBupdate($sqlUpdateChildren);
        if(!$successResult3){
          $errorMessage .= "Error updating children.\n";
        }
      } else {
        $errorMessage .= "Error updating new ID or name.";
      }
  }//end if-else on new category id

  //update parent relationship
  $sqlUpdateParent = "UPDATE category_tree SET parent_category=" . $parentId .
    ", child_category=" . $newCategoryId . " WHERE child_category=" . $oldCategoryId;
  if($successResult){
    if(!DBwrapper::DBupdate($sqlUpdateParent)){
      $errorMessage .= "Error updating parent category.";
    }
  }
  if($successResult && $successResult2 && $successResult3){
    $successMessage = "Category updated successfully!";
  }
}//end if POST

//add GET to get error/success message from add or delete?


echo "<DIV id=\"maincontent\" class=\"w3-container\">";
echo "<DIV id=\"admin-back-button\" class=\"w3-container\"><BUTTON onclick=\"location.href = 'admin-menu.php';\">";
echo "<span class=\"material-icons\">reply</span>BACK</BUTTON></DIV>";
echo "<DIV id=\"titlearea\" class=\"w3-container\">";
echo "<H3>Category Editor</H3>";
echo "<DIV id=\"addCategoryButtonHolder\" class=\"w3-container\">";
echo "<BUTTON type=\"button\" onclick=\"showAddCategoryModal()\">ADD CATEGORY</BUTTON>";
echo "<DIV id=\"errorMessage\" class=\"errMsg\" />$errorMessage</DIV>";
echo "<DIV id=\"successMessage\" class=\"w3-green\" />$successMessage</DIV>";
echo "</DIV></DIV>";

//Placeholders for modals
echo "<div id=\"confirmCategoryDeleteModal\" class=\"w3-modal\"></DIV>";
echo "<div id=\"addCategoryModal\" class=\"w3-modal\"></DIV>";


echo printCategoryTable();
echo "</DIV>"; //end maincontent

require 'footer.php';
?>
