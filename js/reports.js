
/*
 * AJAX call to report-date-chooser
 * 
 */
function setReportType(reportType){
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
	        document.getElementById("reportSelector").innerHTML = this.responseText;
    	}
	};

	var urlToSend = "report-date-chooser.php?reportType="+reportType;
	
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
	
}//end function setReportType()

/*
 * Hides the report month selector all years and shows the given year
 */
function showYear(firstYear, thisYear, yearToShow){
	//hide all years
	//show one year
	var htmlString = "";
	
	if(firstYear != thisYear){
		for(var i=firstYear;i<=thisYear;i++){
			htmlString = "months" + i;
			//console.log(htmlString);
			document.getElementById(htmlString).style.display = "none";
			document.getElementById(htmlString).required = false;
			document.getElementById(htmlString).value = "";
		}
	}
	
	htmlString = "months" + yearToShow;
	document.getElementById(htmlString).required = true;
	document.getElementById(htmlString).style.display = "block";
	document.getElementById("reportMonth").value = 0;
	
}//end function showYear()


function monthSelected(monthNumber){
	document.getElementById("reportMonth").value = monthNumber;
}//end function monthSelected()


function setCurrencyType(currSymbol, exchRate){
	var reportAmounts = document.getElementsByClassName("report-amount");
	var currencySymbols = document.getElementsByClassName("currency-symbol");
	var currencyChoosers = document.getElementsByClassName("currency-chooser");

	console.log("Exchange Rate: " +exchRate);

	//iterate and divide, replace symbols
	for(var i=0;i<reportAmounts.length;i++){
		var amt = parseFloat(reportAmounts[i].innerHTML) / exchRate;
		reportAmounts[i].innerHTML = amt.toFixed(2); 
	}
	
	for(var i=0;i<currencyChoosers.length;i++){
		var amt = parseFloat(currencyChoosers[i].value) / exchRate;
		currencyChoosers[i].value = amt.toFixed(8); 
	}
	
	for(var i=0;i<currencySymbols.length;i++){
		currencySymbols[i].innerHTML = currSymbol;
		currencySymbols[i].style.paddingRight = "8px";
	}
	
	
}//end function setCurrencyType()


function printReportCSS(){
	printDivCSS = new String ('<link href="myprintstyle.css" rel="stylesheet" type="text/css">');
    window.frames["print_frame"].document.body.innerHTML=printDivCSS + document.getElementById(divId).innerHTML;
    window.frames["print_frame"].window.focus();
    window.frames["print_frame"].window.print();
    window.frames["print_frame"].window.focus(); setTimeout(function() { window.frames["print_frame"].window.print(); }, 0);
}


function printReportEasy(){
	
	var divElements = document.getElementById("report-block").innerHTML;

	//Get the HTML of whole page
	var oldPage = document.body.innerHTML;

	//Reset the pages HTML with divs HTML only

	document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";

	//Print Page
	window.print();

	//Restore orignal HTML
	document.body.innerHTML = oldPage;	
}


function printReportArea(){
    var printContent = document.getElementById("report-block");
    var WinPrint = window.open('', '', 'width=900,height=650');
    WinPrint.document.write(printContent.innerHTML);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}//end function printReport()

function closeCurrencyModal(){
	document.getElementById("currencyModal").style.display = "none";
}

function showCurrencyModal(){
	document.getElementById("currencyModal").style.display = "block";
}