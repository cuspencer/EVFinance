<?php
session_start();

require 'DBwrapper.php';

$categoryID = "";
$categoryName = "";
$parentID = "";
$parentLevel = "";

//get new category info from form
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $categoryID = $_POST["addCategoryID"];
    $categoryName = $_POST["addCategoryName"];
    $parentID = $_POST["parentCategoryID"];
}

$sqlParentLevelQuery = "SELECT category_type FROM categories WHERE category_id=" . $parentID;

$parentLevel = DBwrapper::DBselect($sqlParentLevelQuery)[0]['category_type'];
$categoryType = $parentLevel + 1;

$sqlCategoryInsert = "INSERT INTO categories (category_id, category_name, category_type) VALUES (" .
  $categoryID . ",\"" . $categoryName . "\"," . $categoryType .")";
$success = DBwrapper::DBupdate($sqlCategoryInsert);

$sqlCategoryTreeInsert = "INSERT INTO category_tree (parent_category, child_category) VALUES (" .
  $parentID . "," . $categoryID . ")";
$success = $success && DBwrapper::DBupdate($sqlCategoryTreeInsert);

$added = 2;

if($success){
  $added = 1;
}

header("Location: category-admin.php?added=$added");

?>
