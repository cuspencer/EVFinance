<?php
session_start();

require 'DBwrapper.php';

$categoryID = "";
$categoryName = "";
$parentID = "";
$parentName = "";

//get category ID from AJAX request
if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $categoryID = $_GET["cid"];
}

$category_select_stmt = "SELECT category_id, child_name, parent_name, parent_id FROM
    (SELECT categories.category_id, categories.category_name AS child_name, category_tree.parent_category AS parent_id
     FROM categories INNER JOIN category_tree WHERE categories.category_id=category_tree.child_category
  AND categories.category_type > 0) AS CHILD,
    (SELECT category_id AS ID, category_name AS parent_name FROM categories) AS PARENT
    WHERE CHILD.parent_id = PARENT.ID AND CHILD.category_id=$categoryID";

$results_array = DBwrapper::DBselect($category_select_stmt);
$categoryName = $results_array[0]['child_name'];
$parentID = $results_array[0]['parent_id'];
$parentName = $results_array[0]['parent_name'];


//Create Editable Row
echo "<tr class=\"w3-table\" id=\"" . $categoryID . "\">";
echo "<input type=\"hidden\" id=\"oldCategoryId\" name=\"oldCategoryId\" value=\"" . $categoryID . "\"/>";
echo "<td><input type=\"text\" required id=\"childCategoryId\" name=\"childCategoryId\" size=\"4\" value=\"" . $categoryID . "\"/></td>";
echo "<td><input type=\"text\" required id=\"childCategoryName\" name=\"childCategoryName\" size=\"50\" value=\"" . $categoryName . "\" /></td>";

//Category Select Item
$category_list_stmt = "SELECT category_id, category_name FROM categories WHERE category_id !=" . $categoryID;
$parent_category_array = DBwrapper::DBselect($category_list_stmt);
echo "<td><select style=\"font-size:14px;\" required id=\"parentCategoryId\" name=\"parentCategoryId\">";

//loop and add category options
foreach ($parent_category_array as $r){

    echo "<option value=" . $r['category_id'];
    if($r['category_id'] == $parentID){
           echo " selected";
    }
    echo ">" . $r['category_id'] . " - " . $r['category_name'] . "</option>";
}
echo "</td>";

//Cancel
echo "<td><label title=\"cancel\" class=\"material-icons\" onclick=\"cancelCategoryEdit($categoryID, '$categoryName', $parentID, '$parentName')\">close</label>";

//Submit
echo "<label title=\"submit\" class=\"material-icons\" onclick=\"submitCategoryEdit()\">done</label></td></tr>";

?>
