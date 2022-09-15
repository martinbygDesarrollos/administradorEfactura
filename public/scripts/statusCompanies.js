console.log("statusCompanies");


function changestatusCompanie( newStatus, currentStatus ){

	newStatus = parseInt(newStatus);
	currentStatus = parseInt(currentStatus);

	let currentStatusLabel = statusDescription(currentStatus);
	let newStatusLabel = statusDescription(newStatus);

	console.log(newStatus, currentStatus);
	showReplyMessage(1, "Pasar de '"+currentStatusLabel+"' a '"+newStatusLabel+"'.<br>¿Confirma el cambio de estado?", "SI", "No");

	$('#modalButtonResponse').click(function(){

		sendAsyncPost("changeStatusCompanie",{newStatus:newStatus})
		.then((response)=>{
			console.log(response);
			if ( response.result == 0 ){
				window.location.href = getSiteURL() + "cerrar-session";
			}
		})

	});

}