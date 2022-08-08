function getSiteURL(){
	let url = window.location.href;
	if(url.includes("localhost") || url.includes("intranet.gargano"))
		return '/administradorEfactura/public/';
	else
		return '/';
}

function validateRut(rut){
	var lengthRut = rut.length;
	if (( lengthRut < 10 || lengthRut > 12) || !$.isNumeric(rut)){
		return false;
	}

	if (!/^([0-9])*$/.test(rut)){
		return false;
	}

	var rutDigitVerify = rut.substr((rut.length -1), 1);
	var rutNumber = rut.substr(0, (rut.length -1));

	var total = 0;
	var factors = [2,3,4,5,6,7,8,9,2,3,4];

	j = 0;

	for(i = (rut.length -2); i >= 0; i--){
		total += (factors[j] * rut.substr(i, 1));
		j++;
	}

	var digitVerify = 11 - (total % 11);
	if(digitVerify == 11) digitVerify = 0;
	else if(digitVerify == 10) digitVerify = 1;
	return digitVerify == rutDigitVerify;
}

function validateCI(ci){
	ci = ci.replace(/\D/g, '');

	var dig = ci[ci.length - 1];
	ci = ci.replace(/[0-9]$/, '');
	return (dig == validation_digit(ci));
}

function validation_digit(ci){
	var a = 0;
	var i = 0;
	if(ci.length <= 6){
		for(i = ci.length; i < 7; i++){
			ci = '0' + ci;
		}
	}
	for(i = 0; i < 7; i++){
		a += (parseInt("2987634"[i]) * parseInt(ci[i])) % 10;
	}
	if(a%10 === 0){
		return 0;
	}else{
		return 10 - a % 10;
	}
}