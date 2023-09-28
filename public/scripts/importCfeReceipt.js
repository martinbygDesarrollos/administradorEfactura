$("#idFormLoadCfeXmlReceipt").submit((e)=>{

	e.preventDefault();
	$("#submitLoadCfeReceipt").prop("disabled", true);

	progressBarIdProcess = loadPrograssBar();
	$("#progressbar").modal("show");


	formData = new FormData(document.getElementById("idFormLoadCfeXmlReceipt"));
	$("#tbodyImportCfeReceiptResponse").empty();
	sendAsyncPostForm("importCfeReceiptXml", formData)
	.then((response)=>{
		stopPrograssBar(progressBarIdProcess);
		$('#progressbar').modal("hide");

		$("#submitLoadCfeReceipt").prop("disabled", false);
		showReplyMessage(response.result, response.message);
		if ( response.resultadosImportacion ){
			if ( response.resultadosImportacion.length >0 ){

				response.resultadosImportacion.map((element)=>{
					let row = createRowResponseImport(element);

					$("#tbodyImportCfeReceiptResponse").append(row);
				})

			}

		}

	})


});