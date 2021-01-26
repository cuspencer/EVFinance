"use strict";

function accountInputTest(newAccountName, newAccountBalance){
	var acctNames = document.getElementsByClassName("accountNameLabel");
	var errMessage = "";

	if(newAccountName != ""){
		for(var i=0;i<acctNames.length;i++){
			if(newAccountName.localeCompare(acctNames[i].innerHTML, undefined, { sensitivity: 'accent' }) === 0){
				errMessage = "Account name \"" + newAccountName + "\" is already being used. Please choose a different name. ";
			}
		}
	} else{
		errMessage = " Account name is blank.";
	}

	//test for valid numerical input
	if(!/^-?\d*([.]\d{1,5})?$/.test(newAccountBalance)){
		errMessage += "Improper input for account balance."
	}

	return errMessage;
}//end function accountInputTest

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

function submitAccountEdit(){
	clearMessages();
	var accountName = document.getElementById("editAccountName").value;
	var accountBalance = document.getElementById("editAccountBalance").value;

	var errMessage = accountInputTest(accountName, accountBalance);

	if(errMessage == ""){
		document.getElementById('editAccountForm').submit();
	}	else {
		document.getElementById('errorMessage').innerHTML = "ERROR: " + errMessage;
		document.getElementById("errorMessage").style.display = "block";
	}

}

function editAccount(account_id){
	clearMessages();
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById(account_id).innerHTML = this.responseText;
			}
	};

	var urlToSend = "edit-account-helper.php?a=" + account_id;
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
}//end function editAccount()

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
 			document.getElementById("confirmCategoryDeleteModal").innerHTML = this.responseText;
			document.getElementById("confirmCategoryDeleteModal").style.display = "block";
    	}
	};

	var urlToSend = "delete-category-helper.php?cid=" + category_id;
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
}//end function confirmDeleteCategory

function closeConfirmDeleteModal(){
	document.getElementById("confirmCategoryDeleteModal").style.display = "none";
}

function showAddAccountModal(){
	clearMessages();
	document.getElementById("addAccountModal").style.display = "block";
}

function closeAddAccountModal(){
	document.getElementById('addAccountModalErrorMessage').innerHTML = "";
	document.getElementById("addAccountModal").style.display = "none";
}

function closeAddCategoryModal(){
	document.getElementById('addCategoryModalErrorMessage').innerHTML = "";
	document.getElementById("addCategoryModal").style.display = "none";
}

function addAccount(){
	var accountName = document.getElementById("newAccountName").value;
	var accountBalance = document.getElementById("newAccountBalance").value;
	var errMessage = accountInputTest(accountName, accountBalance);

	if(errMessage == ""){
		document.getElementById('addAccountForm').submit();
	}	else {
			document.getElementById('addAccountModalErrorMessage').innerHTML = "ERROR: " + errMessage;
	}
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
	htmlCategoryReset += "<td class=\"categoryIDLabel\">" + category_id + "</td>";
	htmlCategoryReset += "<td class=\"categoryNameLabel\">" + category_name + "</td>";
	htmlCategoryReset += "<td>" + parent_id + " - " + parent_name + "</td>";
	htmlCategoryReset += "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editCategory(" + category_id +
		")\">create</label>" + "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteCategory(" +
		category_id + ")\">delete</label></td>";
	htmlCategoryReset += "</tr>";
	document.getElementById(category_id).innerHTML = htmlCategoryReset;
	document.getElementById('errorMessage').innerHTML = "";
}//end function cancelCategoryEdit()

function cancelAccountEdit(account_id, account_name, account_type, account_balance, account_active){
	clearMessages();
	var currencySymbol = document.getElementById('currencySymbol').value;

	//reset table row
	var htmlCategoryReset = "<tr class=\"w3-table\" id=\"" + account_id + "\">";
	htmlCategoryReset += "<td class=\"accountNameLabel\">" + account_name + "</td>";
	if(account_type == 1){
		htmlCategoryReset += "<td>Cash Account</td>";
	}else{
		htmlCategoryReset += "<td>Bank Account</td>";
	}
	htmlCategoryReset += "<td>" + currencySymbol + " " + account_balance + "</td>";
	if(account_active == 1){
		htmlCategoryReset += "<td><input type=\"checkbox\" name=\"accountActive\" disabled checked/></td>";
	}else{
		htmlCategoryReset += "<td><input type=\"checkbox\" name=\"accountActive\" disabled/></td>";
	}
	htmlCategoryReset += "<td><label title=\"edit\" class=\"material-icons\" onclick=\"editAccount(" + account_id +
		")\">create</label></td>";
		// + "<label title=\"delete\" class=\"material-icons\" onclick=\"confirmDeleteAccount(" +
		//account_id + ")\">delete</label></td>";
	htmlCategoryReset += "</tr>";
	document.getElementById(account_id).innerHTML = htmlCategoryReset;
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
