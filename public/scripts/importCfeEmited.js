$("#idFormLoadCfeXml").submit((e)=>{

	e.preventDefault();
	$("#idButtonSubmitLoadCfeXml").prop("disabled", true);

	progressBarIdProcess = loadPrograssBar();
	$("#progressbar").modal("show");


	formData = new FormData(document.getElementById("idFormLoadCfeXml"));
	$("#tbodyImportCfeEmitedResponse").empty();
	sendAsyncPostForm("importCfeEmitedXml", formData)
	.then((response)=>{
		stopPrograssBar(progressBarIdProcess);
		$('#progressbar').modal("hide");

		$("#idButtonSubmitLoadCfeXml").prop("disabled", false);
		showReplyMessage(response.result, response.message);
		if ( response.resultadosImportacion ){
			if ( response.resultadosImportacion.length >0 ){

				response.resultadosImportacion.map((element)=>{
					let row = createRowResponseImport(element);

					$("#tbodyImportCfeEmitedResponse").append(row);
				})

			}

		}

	})


});



function createRowResponseImport( obj ){

	let row = "<tr><td>"+obj.error+"</td></tr>";

	return row;


}