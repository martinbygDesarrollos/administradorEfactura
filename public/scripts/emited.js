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

	let options = '<option value="101" >E-Ticket</option><option value="102" >N. crédito E-Ticket</option><option value="103" >N. débito E-Ticket</option><option value="111" selected>E-Factura</option><option value="112" >N. crédito E-Factura</option><option value="113" >N. débito E-Factura</option><option value="121" >E-Factura Exportación</option><option value="122" >N. crédito E-Factura Exportación</option><option value="123" >N. débito E-Factura Exportación</option><option value="124" >E-Remito de Exportación</option><option value="131" >E-Ticket Venta por Cuenta Ajena</option><option value="132" >N. crédito E-Ticket Venta por Cuenta Ajena</option><option value="133" >N. débito E-Ticket Venta por Cuenta Ajena</option><option value="141" >E-Factura Venta por Cuenta Ajena</option><option value="142" >N. crédito E-Factura Venta por Cuenta Ajena</option><option value="143" >N. débito E-Factura Venta por Cuenta Ajena</option><option value="151" >E-Boleta de entrada</option><option value="152" >N. crédito E-Boleta de entrada</option><option value="153" >N. débito E-Boleta de entrada</option><option value="181" >E-Remito</option><option value="182" >E-Resguardo</option>';

	let rowNumber = $("#rowsToInsertCfeData").children().length;
	let newRow = '<div class="form-row" ><div class="form__group field w-25 m-2"><select class="form__field" id="" name="nameSelectTipoCfeSendXml'+rowNumber+'">'+options+'</select></div>';

	newRow += '<div class="form__group field m-2"><input type="text" id="" name="nameInputSerieCfeSendXml'+rowNumber+'" class="form__field input_uppercase" placeholder="Serie" required/></div>'

	newRow += '<div class="form__group field m-2"><input type="text" id="" name="nameInputNumeroCfeSendXml'+rowNumber+'" class="form__field" placeholder="Número" required/></div></div>';

	$("#rowsToInsertCfeData").append(newRow);

}