/*
 * When a text field is changed, checks the current value against default value.
 * The submit is enabled or disabled accordingly.
 */
function enableSubmit(){
	var fname = document.getElementById("fname");
	var lname = document.getElementById("lname");
	var email = document.getElementById("email");
	
	if((fname.value.trim() != fname.defaultValue) || (lname.value.trim() != lname.defaultValue) || (email.value.trim() != email.defaultValue)){
		document.getElementById("submitButton").disabled = false;
	}else{
		document.getElementById("submitButton").disabled = true;
	}
}


function closePasswordModal(){
	document.getElementById("passwordModal").style.display = "none";
	document.getElementById("oldPassword").value = "";
	document.getElementById("newPassword1").value = "";
	document.getElementById("newPassword2").value = "";
	document.getElementById("passwordError").innerHTML = "";
}

function showPasswordModal(){
	document.getElementById("passwordModal").style.display = "block";
}

function submitPassword(){
	var oldPass = document.getElementById("oldPassword").value;
	var newPass1 = document.getElementById("newPassword1").value;
	var newPass2 = document.getElementById("newPassword2").value;
		
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.onreadystatechange = function() {
 		if (this.readyState == 4 && this.status == 200) {
	        document.getElementById("passwordFormHolder").innerHTML = this.responseText;
    	}
	};

	var urlToSend = "password-change.php?old="+oldPass+"&new1="+newPass1+"&new2="+newPass2;
	xmlhttp.open("GET",urlToSend,true);
	xmlhttp.send();
}