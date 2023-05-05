console.log("vista de facturacion")



$("#formPrueba").submit((e)=>{

	e.preventDefault();
	$("#idButtonSubmitLoadCfeXml").attr("disabled", true);

	progressBarIdProcess = loadPrograssBar();
	$("#progressbar").modal("show");


	formData = new FormData(document.getElementById("idFormLoadCfeXml"));
	$("#tbodyImportCfeEmitedResponse").empty();
	sendAsyncPostForm("importCfeEmitedXmlPrueba", formData)
	.then((response)=>{
		stopPrograssBar(progressBarIdProcess);
		$('#progressbar').modal("hide");

		$("#idButtonSubmitLoadCfeXml").removeAttr("disabled");
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
	let comprobante = " - ";
	if ( obj.tipoCFE > 0 ){
		comprobante = tableCfeType(obj.tipoCFE) + " "+ obj.serieCFE + " - "+ obj.numeroCFE;
	}

	let row = "<tr><td>"+comprobante+"</td>"
	row += "<td>"+obj.ok+"</td>";
	row += "<td>"+obj.error+"</td></tr>";

	return row;


}