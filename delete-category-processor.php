<?php
session_start();

require 'DBwrapper.php';

$categoryID = "";
$replacementID = "";

//get receipt num from form
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $categoryID = $_POST["categoryID"];
    $replacementID = $_POST["replacementCategoryID"];
}

$sqlUpdateCategory = "UPDATE transactions SET category_id=" . $replacementID
  . " WHERE category_id=" . $categoryID;

$sqlDeleteCategory = "DELETE FROM category_tree WHERE child_category=" . $categoryID;
$sqlDeleteCategory2 = "DELETE FROM categories WHERE category_id=" . $categoryID;

$result = DBwrapper::DBupdate($sqlUpdateCategory);
$result = $result && DBwrapper::DBupdate($sqlDeleteCategory);
$result = $result && DBwrapper::DBupdate($sqlDeleteCategory2);

$deleted = 2;

if($result){
  $deleted = 1;
}

header("Location: category-admin.php?deleted=$deleted");

?>
