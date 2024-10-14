function getSiteURL(){
	let url = window.location.href;
	if(url.includes("localhost") || url.includes("intranet.gargano"))
		return '/administradorEfactura/public/';
	else
		return '/';
}



function tableCfeType(type){

	switch (type) {
		case 101: return "e-Ticket";
		case 102: return "Nota de crédito de e-Ticket";
		case 103: return "Nota de débito de e-Ticket";
		case 111: return "e-Factura";
		case 112: return "Nota de crédito de e-Factura";
		case 113: return "Nota de débito de e-Factura";
		case 121: return "e-Factura Exportación";
		case 122: return "Nota de crédito de e-Factura Exportación";
		case 123: return "Nota de débito de e-Factura Exportación";
		case 124: return "e-Remito de Exportación";
		case 131: return "e-Ticket Venta por Cuenta Ajena";
		case 132: return "Nota de crédito de e-Ticket Venta por Cuenta Ajena";
		case 133: return "Nota de débito de e-Ticket Venta por Cuenta Ajena";
		case 141: return "e-Factura Venta por Cuenta Ajena";
		case 142: return "Nota de crédito de e-Factura Venta por Cuenta Ajena";
		case 143: return "Nota de débito de e-Factura Venta por Cuenta Ajena";
		case 151: return "e-Boleta de entrada";
		case 152: return "Nota de crédito de e-Boleta de entrada";
		case 153: return "Nota de débito de e-Boleta de entrada";
		case 181: return "e-Remito";
		case 182: return "e-Resguardo";
		default: return "";
	}


}


function dateTypeHtml( date ){ // entrada 2023-02-04T23:24:21.142-03:00 salida 04/02/2023
	newDate = date.substr(8, 2)+"/"+date.substr(5,2)+"/"+date.substr(2,2);
	return newDate;
}


function showMessage(result, message){
	// console.log("funcion de los mensajes SIN ACCIOn");
	// Get the snackbar DIV
	var x = document.getElementById("snackbar");
	// console.log(x)
	var y = document.getElementById("snackbarText");
	// Add the "show" class to DIV
	y.textContent = message;

	if (result == 0){
		// console.log("result 0");
		x.className = "show";
		x.style = "background-color: #F44336DD;";

	}
	else if( result == 1 ){
		// console.log("result 1");
		x.className = "show";
		x.style = "background-color: #FF9800DD;";
		setTimeout(function(){ x.className = "fade" }, 30000);
	}
	else if( result == 2 ){
		// console.log("result 2");
		x.className = "show";
		x.style = "background-color: #4CAF50DD;";
		setTimeout(function(){ x.className = "fade"; x.style = ""; }, 10000);
	}


	$('#snackbarButton').click(function(){
		// console.log(x);
		x.className = "fade";
		x.style = "";
	});
}

function showMessageWithAction(result, message, correo, contra){
	// console.log("funcion de los mensajes PERO CON ACCIOn");
	// Get the snackbar DIV
	var x = document.getElementById("snackbarWithAction");
	// console.log(x)
	var y = document.getElementById("snackbarWithActionText");

	// var z = document.getElementById("snackbarWithActionButton");
	// Add the "show" class to DIV
	y.textContent = message;

	if (result == 0){
		// console.log("result 0");
		x.className = "show";
		x.style = "background-color: #F44336DD;";

	}
	else if( result == 1 ){
		// console.log("result 1");
		x.className = "show";
		x.style = "background-color: #FF9800DD; z-index: 999;";
		setTimeout(function(){ x.className = "fade" }, 30000);
	}
	else if( result == 2 ){
		// console.log("result 2");
		x.className = "show";
		x.style = "background-color: #4CAF50DD;";
		// z.style = ;
		setTimeout(function(){ x.className = "fade"; x.style = ""; }, 10000);
	}

	$('#snackbarWithActionButton').click(function(){
		let entorno = $("#entornoSelect").val();
		sendAsyncPost("login", {correo: correo, contra: contra, force: true, entorno: entorno})
		.then(( response )=>{
			if(response.result == 2){
				// document.getElementById("buttonConfirm").disabled = false;
				window.location.href = getSiteURL();
			}else if ( response.result == 0 ){
				// document.getElementById("buttonConfirm").disabled = false;
				window.location.href = getSiteURL() + "cerrar-session";
			}else{
				// document.getElementById("buttonConfirm").disabled = false;
				// showMessage(response.result, response.message);
				if(response.activa)
					showMessageWithAction(response.result, response.message, correo, contra);
				else
					showMessage(response.result, response.message, correo, contra);
			}
		})
		// console.log(x);
		x.className = "fade";
		x.style = "";
	});

	$('#snackbarWithActionClose').click(function(){
		// console.log(x);
		x.className = "fade";
		x.style = "";
	});
}



function showReplyMessage(typeColour, message, buttonsucces, buttoncancel){


	$("#modalButtonResponseCancel").text("Cancelar");
	if ( buttoncancel )
		$("#modalButtonResponseCancel").text(buttoncancel);

	$("#modalButtonResponse").text("Ok");
	if ( buttonsucces )
		$("#modalButtonResponse").text(buttonsucces);


	$('#modalButtonResponseCancel').click(function(){
		$('#modalResponse').modal('hide');
	});

	$('#modalColourResponse').removeClass('alert-success');
	$('#modalColourResponse').removeClass('alert-warning');
	$('#modalColourResponse').removeClass('alert-danger');

	if(typeColour == 0)
		$('#modalColourResponse').addClass('alert-danger');
	else if(typeColour == 2)
		$('#modalColourResponse').addClass('alert-success');
	else if(typeColour == 1)
		$('#modalColourResponse').addClass('alert-warning');

	if ( message.length > 250 ){
		message = message.substring(0, 250);
		message += "...";
	}

	message = message.replaceAll("\n", "<br><br>");


	if ( typeof message == "object"){

		let newMessage = "";
		message.forEach(element => {

			newMessage += element+'<br>';
		});
		$('#modalMessageResponse').html(newMessage);
	}else $('#modalMessageResponse').html(message);

	$("#modalResponse").modal();
}



function statusDescription( valueStatus ){

	switch (valueStatus) {
		case 1: return "Pendiente usuario";
		case 2: return "Pendiente postulación";
		case 3: return "Pendiente aprobación"; //ff9800
		case 4: return "Pendiente certificación"; //4CAF50 4CAF50
		case 5: return "Pendiente resolución";
		case 6: return "Emisor habilitado";
		case 7: return "Emisor no habilitado"; //F44336
		case 8: return "En espera para comenzar";
	}

}


function getButtonByStatus( valueStatus ){

	switch (valueStatus) {
		case 1: return '<span class="badge">Pendiente usuario</span>';
		case 2: return '<span class="badge" style="background-color: #F44336">Pendiente postulación</span>';
		case 3: return '<span class="badge" style="background-color: #FF9800">Pendiente aprobación</span>'; //ff9800
		case 4: return '<span class="badge" style="background-color: #4CAF50">Pendiente certificación</span>'; //4CAF50 4CAF50
		case 5: return '<span class="badge" style="background-color: blue">Pendiente resolución</span>';
		case 6: return '<span class="badge" style="background-color: #4CAF50">Emisor habilitado</span>';
		case 7: return '<span class="badge" style="background-color: #F44336">Emisor no habilitado</span>'; //F44336
		case 8: return '<span class="badge">En espera para comenzar</span>';
	}

}


function getCurrentDate(){
	var today = new Date();
	var date = null;
	var day = null;
	var month = null;
	var year = null;

	day = today.getDate();
	month = today.getMonth()+1;
	year = today.getFullYear();

	if( day.toString().length == 1 ){
		day = '0'+today.getDate();
	}

	if( month.toString().length == 1 ) {
		month = '0'+(today.getMonth()+1)
	}

	date = year+'-'+month+'-'+day;
	return date;
}



function backInit(){

	$('.containerTable').scrollTop(0);
	$('.tableCustomScroll').scrollTop(0);
	document.body.scrollTop = 0;
  	document.documentElement.scrollTop = 0;

}

function setQuotes(){
	sendAsyncPost("getQuotes", null)
	.then((response)=>{
		if ( response.result == 2 ){
			console.log(response)
			$('#textEURO').text(response.EUR)
			$('#textUSD').text(response.USD)
			$('#textUI').text(response.UI)
		} else if ( response.result == 0 ){
			console.log("Imposible obtener las cotizaciones de monedas.")
		}
	})
}