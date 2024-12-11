function changestatusCompanie( newStatus, currentStatus ){
	newStatus = parseInt(newStatus);
	currentStatus = parseInt(currentStatus);
	
	if(newStatus == 1 || newStatus == 2 || newStatus == 6 || newStatus == 7 || newStatus == 8)
		return;

	let currentStatusLabel = statusDescription(currentStatus);
	let newStatusLabel = statusDescription(newStatus);

	console.log(newStatus, currentStatus);

	showReplyMessage(1, "Pasar de '"+currentStatusLabel+"' a '"+newStatusLabel+"'.<br>¿Confirma el cambio de estado?", "SI", "No");
	$('#modalButtonResponse').click(function(){
		$('#modalButtonResponse').attr("disabled", "");

		sendAsyncPost("changeStatusCompanie",{newStatus:newStatus})
		.then((response)=>{
			$('#modalButtonResponse').removeAttr("disabled");
			$('#modalResponse').modal('hide');
			showMessage(response.result, response.message);
		})

	});

}


function suspenderActivarEmpresa(value){

	console.log("suspenderActivarEmpresa", value);
	sendAsyncPost("enabledDisabledCompanie",{value:value})
	.then((response)=>{
		showMessage(response.result, response.message);
		setTimeout(() => {
			window.location.reload();
		}, 500);

	})

}

function habilitarEmpresa(rut){
	sendAsyncPost("enableDisableCompany",{rut:rut,status:1})
		.then((response)=>{
			showMessage(response.result, response.message);
			if (response.result == 2){
				window.location.reload();
			}
		})
}

function deshabilitarEmpresa(rut){
	sendAsyncPost("enableDisableCompany",{rut:rut,status:0})
	.then((response)=>{
		showMessage(response.result, response.message);
		if (response.result == 2){
			window.location.reload();
		}
	})
}