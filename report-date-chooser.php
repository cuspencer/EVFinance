<?php
session_start();
require 'DBwrapper.php';

$earliestDateString = "";
$earliestDateYear = "";
$earliestDateMonth = "";

$monthsArray = array('Zero Month', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 
    'September', 'October', 'November', 'December');


$reportType = $_GET['reportType']; //reportType 1=annual 2=monthly

$dateQuery = "select trans_date FROM transactions ORDER BY trans_date ASC LIMIT 1 ";
$earliestDateString = DBwrapper::DBselect($dateQuery)[0]['trans_date'];

//select year
$earliestDateYear = date("Y", strtotime($earliestDateString));
$earliestDateMonth = date("m", strtotime($earliestDateString));


$thisMonth = (int)date("m");
$thisYear = (int)date("Y");
$tempMonth = (int)$earliestDateMonth;
$tempYear = (int)$earliestDateYear;

//create years selector

echo "<DIV id=\"yearSelector\">";

if($reportType == 1){
    echo "<select required id=\"reportYear\" name=\"reportYear\">";
}else{
    echo "<select required id=\"reportYear\" name=\"reportYear\" onchange=\"showYear(" .$tempYear .",". $thisYear .",this.value)\">";
}
echo "<option value=\"\" selected disabled>Select a year...</option>";

while ($tempYear <= $thisYear){
    echo "<option value=\"" . $tempYear . "\">" . $tempYear . "</option>";
    $tempYear ++;
}
    
echo "</SELECT></DIV>";


  
//create months
if($reportType == "2"){
    $tempMonth = (int)$earliestDateMonth;
    $tempYear = (int)$earliestDateYear;
    echo "<INPUT type=\"hidden\" id=\"reportMonth\" name=\"reportMonth\" value=\"0\"/>";
    do{
        //echo "<DIV id=\"months" . $tempYear . "\" style=\"display:none\">";
        echo "<select name=\"monthsOf" . $tempYear . "\" id=\"months" . $tempYear . "\" style=\"display:none\" " . 
        "onchange=\"monthSelected(this.value)\">";
        echo "<option value=\"\" selected disabled>Select a month...</option>";
        while($tempMonth < 13){
            if(($tempYear == $thisYear)&&($tempMonth>=$thisMonth)){
                break;
            }   
            //echo $monthsArray[$tempMonth] . " " . $tempYear ."<BR>";
            echo "<option value=\"" . $tempMonth . "\">" . $monthsArray[$tempMonth] . "</option>";
            $tempMonth++;
        }
        echo "</SELECT>";
        //echo "</DIV>";
        $tempYear ++;
        $tempMonth = 1;
    }while ($tempYear <= $thisYear);
}
    

//SUBMIT BUTTON
echo "<INPUT type=\"submit\" value=\"GO\"/>";

?>