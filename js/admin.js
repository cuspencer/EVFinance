"use strict";

function categoryInputTest(categoryID,categoryName){

		var categoryNumbers = document.getElementsByClassName("categoryIDLabel");
		var categoryNames = document.getElementsByClassName("categoryNameLabel");
		var errMessage = "";

		//iterate and check vs. other categories
		for(var i=0;i<categoryNumbers.length;i++){
			if(!Number.isInteger(Number(categoryID))){
				errMessage = "ID is not a valid number.";
			} else if(categoryID == categoryNumbers[i].innerHTML){
				errMessage = "ID number " + categoryID + " is already being used. Please choose a different number.";
			} else if(categoryID <= 0){
				errMessage = "ID numbers less than or equal to 0 are not allowed.";
			} else if(categoryID < 10){
				errMessage = "ID numbers under 10 are reserved for system categories.";
			}
		}

		for(var i=0;i<categoryNames.length;i++){
			if(categoryName.localeCompare(categoryNames[i].innerHTML, undefined, { sensitivity: 'accent' }) === 0){
					errMessage = "Category name \"" + categoryName + "\" is already being used. Please choose a different name.";
			}
		}

	return errMessage;
}//end function categoryInputTest()

function clearMessages(){
	document.getElementById("successMessage").style.display = "none";
	document.getElementById("errorMessage").style.display = "none";
}

function editCategory(category_id){
	clearMessages();
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
 			document.getElementById(category_id).innerHTML = this.responseText;
    	}
	};

	var urlToSend = "edit-category-helper.php?cid=" + category_id;

	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();

}//end function editCategory()

function confirmDeleteCategory(category_id){
	clearMessages();
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
 			document.getElementById("confirmDeleteModal").innerHTML = this.responseText;
			document.getElementById("confirmDeleteModal").style.display = "block";
    	}
	};

	var urlToSend = "delete-category-helper.php?cid=" + category_id;
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
}//end function confirmDeleteCategory

function closeConfirmDeleteModal(){
	document.getElementById("confirmDeleteModal").style.display = "none";
}

function closeAddCategoryModal(){
	document.getElementById("addCategoryModal").style.display = "none";
}

function addCategory(){
	clearMessages();
	var newCatID = document.getElementById("addCategoryID").value;
	var newCatName = document.getElementById("addCategoryName").value;
	var errMessage = categoryInputTest(newCatID,newCatName);

	if(errMessage == ""){
		document.getElementById('addCategoryForm').submit();
	}	else {
			document.getElementById('addCategoryModalErrorMessage').innerHTML = "ERROR: " + errMessage;
	}

}//end function addCategory

function showAddCategoryModal(){

	clearMessages();
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
 			document.getElementById("addCategoryModal").innerHTML = this.responseText;
			document.getElementById("addCategoryModal").style.display = "block";
    	}
	};

	var urlToSend = "add-category-helper.php";
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
}//end function showAddCategoryModal

function cancelCategoryEdit(category_id, category_name, parent_id, parent_name){
	//reset table row
	var htmlCategoryReset = "<tr class=\"w3-table\" id=\"" + category_id + "\">";
	htmlCategoryReset += "<td>" + category_id + "</td>";
	htmlCategoryReset += "<td>" + category_name + "</td>";
	htmlCategoryReset += "<td>" + parent_id + " - " + parent_name + "</td>";
	htmlCategoryReset += "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editCategory(" + category_id +
		")\">create</label>" + "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteCategory(" +
		category_id + ")\">delete</label></td>";
	htmlCategoryReset += "</tr>";
	document.getElementById(category_id).innerHTML = htmlCategoryReset;
	document.getElementById('errorMessage').innerHTML = "";
}//end function cancelCategoryEdit()

function submitCategoryEdit(){

	var newCatID = document.getElementById("childCategoryId").value;
	var newCatName = document.getElementById("childCategoryName").value;
	var errMessage = categoryInputTest(newCatID,newCatName);

	if(errMessage == ""){
		if(confirm("All receipts associated with this category will be updated as well.")){
			document.getElementById('editCategoryForm').submit();
		}
	}	else {
			document.getElementById('errorMessage').innerHTML = "ERROR UPDATING CATEGORY: " + errMessage;
	}

}//end function submitCategoryEdit()
