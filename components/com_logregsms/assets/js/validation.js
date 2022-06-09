function commonPermitted(e) {
	var arr = [
		8, // backspace
		9, // tab
		16, // shift
		17, // ctrl
		35, // end
		36, // home
		37, // left
		39, // right
		46, // del
		13, // Enter
		// you may want to permit enter/return here too
	];
	if (arr.indexOf(e.keyCode) != -1)
		return true;
	if (e.ctrlKey && ['a', 'c', 'v', 'x'].indexOf(String.fromCharCode(e.keyCode).toLowerCase()))
		return true;
	return;
}

// Prevent entering nothing but numbers
function numberValidate(e) {

	var theEvent = e || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode(key);
	if (commonPermitted(e)) return;

	var regex = /^[0-9]/;
	if (!regex.test(key)) {
		theEvent.returnValue = false;
		if (theEvent.preventDefault) theEvent.preventDefault();
	}
}

// Converting Persian Numbers to English
function CleanString(str) {
	str = str.trim();
	str = str.replaceAll('۰', '0');
	str = str.replaceAll('۱', '1');
	str = str.replaceAll('۲', '2');
	str = str.replaceAll('۳', '3');
	str = str.replaceAll('۴', '4');
	str = str.replaceAll('۵', '5');
	str = str.replaceAll('۶', '6');
	str = str.replaceAll('۷', '7');
	str = str.replaceAll('۸', '8');
	str = str.replaceAll('۹', '9');
	return str;
}

// Checking correct mobile number
function CheckPhone(number_id) {
	let mobileNumberInput = document.getElementById(number_id);
	number = CleanString(mobileNumberInput.value);
	mobileNumberInput.value = number;
	if (number.length !== 11) {
		UIkit.notification({message: 'تعداد ارقام شماره موبایل صحیح نیست', status: 'danger', pos: 'bottom-left'});
		return false;
	}
	else if (((number.charAt(0) + number.charAt(1)) !== "09") && ((number.charAt(0) + number.charAt(1)) !== "۰۹")) {
		UIkit.notification({message: 'شماره شما باید با 09 شروع شود', status: 'danger', pos: 'bottom-left'});
		return false;
	}
	return true;
}

function ValidationCodeForm() {
	var err = 0;

	code = document.getElementById("codenum");
	code.value = CleanString(code.value);

	if (!code.value) {
		DisplayMessage('faild', 'لطفا کد مربوطه را وارد کنید');
		err++;
		code.focus();
	}

	if (err > 0) {
		return false;
	} else {
		jQuery('button.formSubmit').addClass('add_in_progress');
		return true;
	}
}

function CheckEmail(email) {
	emailVal = email.value;
	if (emailVal != "") {
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
		if (!re.test(emailVal)) {
			DisplayMessage('faild', "ایمیل نامعتبر است");
			//jQuery("#jform_email").focus();
			return false;
		}
	}
	else {
		if (email.getAttribute('required') == "required") {
			DisplayMessage('faild', "ایمیل را وارد نمایید.");
			return false;
		}
	}
	return true;
}

function ValidationRegistrationForm() {

	err = 0;
	email = document.getElementById("email");
	if (email) {
		if (!CheckEmail(email)) {
			err++;
		}
	}

	password = document.getElementById("password");
	if (password) {
		if (!password.value) {
			DisplayMessage('faild', "رمز ورود خود را وارد کنید");
			err++;
		}
	}

	if (err > 0) {
		return false;
	}

	return true;
}

// Validating Form
function ValidationMobileForm() {
	let err = 0;
	let mobile = document.getElementById("mobilenum");
	if (!CheckPhone("mobilenum")) {
		err++;
		mobile.focus();
	}
	if (err > 0) {
		return false;
	} else {
		jQuery('button.formSubmit').addClass('add_in_progress');
		return true;
	}
}

/*
function ValidationMobileModuleForm() {
	var err = 0;

	mobile = document.getElementById("mobilenum_m");
	if (!CheckPhone("mobilenum_m")) {
		err++;
		mobile.focus();
	}

	if (err > 0) {
		return false;
	}
}

function DisplayMessage(type, text) {
	if (type == "success") {
		text = "<div class='alert alert-success'>" + text + "</div>";
	} else if (type == "faild") {
		text = "<div class='alert alert-warning'>" + text + "</div>";
	}

	jQuery('#MessageP .message_text').html(text);
	var inst = jQuery("[data-remodal-id=MessageP]").remodal();
	inst.open();

}

function validateOnlyDigit(evt) {
	var theEvent = evt || window.event;

	// Handle paste
	if (theEvent.type === 'paste') {
		key = event.clipboardData.getData('text/plain');
	} else {
		// Handle key press
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode(key);
	}
	var regex = /[0-9]|\./;
	if (!regex.test(key)) {
		theEvent.returnValue = false;
		if (theEvent.preventDefault) theEvent.preventDefault();
	}
}
*/