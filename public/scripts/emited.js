console.log("vista de facturacion")

$("#idFormEmited").submit((e)=>{

	e.preventDefault();
	$("#buttonSubmitEmitedForm").attr("disabled", true);


	let serie = $("#idInputSerieCfeSendXml").val();
	let numero = $("#idInputNumeroCfeSendXml").val();
	let mail = $("#idInputMailsCopySendXml").val();


	let numberCfes = $("#rowsToInsertCfeData").children().length;

	if ( serie && numero ){

		formData = new FormData(document.getElementById("idFormEmited"));
		formData.append("numberCfes", numberCfes);


		sendAsyncPostForm("resendXml", formData)
		.then((response)=>{

			$("#buttonSubmitEmitedForm").removeAttr("disabled");

			showMessage(response.result , response.message);

			if ( response.result == 0 ){
				window.location.href = getSiteURL() + "cerrar-session";
			}
		})
	}else{
		$("#buttonSubmitEmitedForm").removeAttr("disabled");
	}

});



function addRowToInsertCfeData(){

	let rowNumber = $("#rowsToInsertCfeData").children().length;
	let newRow = '<div class="form-row" ><div class="form__group field w-25 m-2"><select class="form__field" id="" name="nameSelectTipoCfeSendXml'+rowNumber+'"><option value="111" selected>E-Factura</option><option value="101" >E-Ticket</option></select></div>';

	newRow += '<div class="form__group field m-2"><input type="text" id="" name="nameInputSerieCfeSendXml'+rowNumber+'" class="form__field input_uppercase" placeholder="Serie" required/></div>'

	newRow += '<div class="form__group field m-2"><input type="text" id="" name="nameInputNumeroCfeSendXml'+rowNumber+'" class="form__field" placeholder="Número" required/></div></div>';

	$("#rowsToInsertCfeData").append(newRow);

}