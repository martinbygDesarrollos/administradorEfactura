console.log("vista de facturacion")

$("#idFormEmited").submit((e)=>{

	e.preventDefault();
	$("#buttonSubmitEmitedForm").attr("disabled", true);


	let serie = $("#idInputSerieCfeSendXml").val();
	let numero = $("#idInputNumeroCfeSendXml").val();
	let mail = $("#idInputMailsCopySendXml").val();

	if ( serie && numero && mail ){

		formData = new FormData(document.getElementById("idFormEmited"));

		sendAsyncPostForm("resendXml", formData)
		.then((response)=>{
			$("#buttonSubmitEmitedForm").removeAttr("disabled");
			showMessage(response.message);
			if ( response.result == 0 ){
				window.location.href = getSiteURL() + "cerrar-session";
			}
		})

	}

});