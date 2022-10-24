console.log("vista de facturacion")

$("#idFormLoadCfeXml").submit((e)=>{

	e.preventDefault();
	$("#idButtonSubmitLoadCfeXml").attr("disabled", true);

	progressBarIdProcess = loadPrograssBar();
	$("#progressbar").modal("show");


	formData = new FormData(document.getElementById("idFormLoadCfeXml"));

	sendAsyncPostForm("importCfeEmitedXml", formData)
	.then((response)=>{
		stopPrograssBar(progressBarIdProcess);
		$('#progressbar').modal("hide");

		$("#idButtonSubmitLoadCfeXml").removeAttr("disabled");

		showMessage(response.result, response.message);
	})


});