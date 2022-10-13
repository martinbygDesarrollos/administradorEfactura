console.log("vista de facturacion")

$("#idFormLoadCfeXml").submit((e)=>{

	e.preventDefault();
	$("#idButtonSubmitLoadCfeXml").attr("disabled", true);



	formData = new FormData(document.getElementById("idFormLoadCfeXml"));

	sendAsyncPostForm("importCfeEmitedXml", formData)
	.then((response)=>{
		$("#idButtonSubmitLoadCfeXml").removeAttr("disabled");

		console.log(response);
		showMessage(response.result, response.message);
	})


});