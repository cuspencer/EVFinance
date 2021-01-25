"use strict";

function addReceiptFields(rType,rAcct){
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
	        document.getElementById("add-receipt-bar").innerHTML = this.responseText;
    	}
	};

	var urlToSend = "add-receipt-helper.php?a="+rAcct+"&t="+rType;

	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();

}//end function addFields(rType)


function addReceiptTypeButtons(rAcct){

	var htmlReceiptTypeButtons = "";

	htmlReceiptTypeButtons += "<label>Expense<input type=\"radio\" name=\"rType\" onclick=\"addReceiptFields(1," +
	rAcct + ")\"/> </label>";
	htmlReceiptTypeButtons += "<label>Income<input type=\"radio\" name=\"rType\" onclick=\"addReceiptFields(2," +
	rAcct+ ")\"/> </label>";
	htmlReceiptTypeButtons += "<label>Internal Transfer<input type=\"radio\" name=\"rType\" onclick=\"addReceiptFields(3," +
	rAcct + ")\"/> </label>";

	document.getElementById("add-receipt-bar").innerHTML = htmlReceiptTypeButtons;

}//end function addReceiptTypeButtons()

/*
 * AJAX replace maincontent with new page of receipts
 * @acctNum - number of the account in the database
 * @pageNum - page number for receipts (zero-indexed?)
 */
function showReceiptPage(acctNum, pageNum){
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
	        document.getElementById("maincontent").innerHTML = this.responseText;
    	}
	};

	var urlToSend = "display-receipt-page.php?acct_id="+acctNum+"&pagenum="+pageNum;

	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();

}//end function showReceiptPage()

function validateAddReceipt(){
	var tAmount = document.getElementById("tAmount").innerHTML;
	//console.log(tAmount);
	return validateAmount(tAmount);
}//end function validateAddReceipt()

function validateAmount(a){
	if ((Number.isNaN(Number(a))) || (a == "")){
		return false;
	}
	else if(Number(a) < 0){
		return false;
		//console.log("Legit Amount");
	}
	return true;
}//end function validateAmount()

function lookupAccount(str) {
  if (str.length==0) {
    document.getElementById("livesearch").innerHTML="";
    document.getElementById("livesearch").style.display = "none";
    return;
  }
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("livesearch").innerHTML=this.responseText;
      document.getElementById("livesearch").style.display = "block";
    }
  }
  xmlhttp.open("GET","account-search.php?q="+str,true);
  xmlhttp.send();
}//end function lookupAccount()

function fillSearch(strChoice) {
	//console.log("Choice: " + strChoice);
	document.getElementById("tOtherAcctName").value = strChoice;
	document.getElementById("livesearch").style.display = "none";
}//end function fillSearch()

function getAccountNumberFromHTML(){
	var acctNum = document.getElementById("hiddenAcctNum").innerHTML;
	return acctNum;
}

function confirmDelete(acctNum,receiptNum){
	var cd = confirm("Delete this receipt?");
	if(cd){
		deleteReceipt(acctNum,receiptNum);
	}
}//end function confirmDelete()

function deleteReceipt(acctNum,receiptNum){

	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
	        showReceiptPage(acctNum, "0");
    	}
	};

	var urlToSend = "delete-receipt.php?r=" + receiptNum;

	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();

}//end function deleteReceipt()

function editReceipt(acctNum,receiptNum){

	var xmlhttp = new XMLHttpRequest();

	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
 			document.getElementById("add-receipt-bar").style.display = "none";
 			document.getElementById(receiptNum).innerHTML = this.responseText;
    	}
	};

	var urlToSend = "edit-receipt-helper.php?r=" + receiptNum;

	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();

}//end function editReceipt()

function getCurrentReceiptPageFromHTML(){
	var pageNum = "0";
	if(document.getElementById("currPageNum") != null){
		var p = parseInt(document.getElementById("currPageNum").innerHTML);
		p = p - 1;
		pageNum = p.toString();
	}
	return pageNum;
}//end function getCurrentReceiptPageFromHTML();

function cancelReceiptEdit(){
	var pageNum = getCurrentReceiptPageFromHTML();
	var acctNum = getAccountNumberFromHTML();
	showReceiptPage(acctNum, pageNum);
}//end function cancelReceiptEdit()

function submitReceiptEdit(){
	document.getElementById('editReceiptForm').submit();
}//end function submitReceiptEdit()
