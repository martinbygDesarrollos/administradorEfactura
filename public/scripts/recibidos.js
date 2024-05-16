tipoCFE = ""
numeroCFE = ""
serieCFE = ""

function getRecibidos(lastId){
    // /company/{{ef_rut}}/cfe/emitidos/
    rutSelected = $("#textRutCompanieSelected").text();
	sendAsyncPost("getRecibidos", {rut: rutSelected, lastId : lastId})
	.then((response)=>{
		if ( response.result == 2 ){
			createRowsToRecibidosTable(response.objectResult.recibidos);
            lastItem = response.objectResult.recibidos[response.objectResult.recibidos.length - 1];
            lastIdRecibidos = lastItem.id
            
		} else if ( response.result == 0 ){
		// 	// window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}

function createRowsToRecibidosTable(lista) {
	lista.forEach(function(comprobante) {
        appendRowRecibido(comprobante);
	})
}

function appendRowRecibido(comprobante){
	let tr = '<tr id="' + comprobante.id + '" onclick="showComprobante(\''+ comprobante.emisor.rut + '\', \''+ comprobante.tipoCFE + '\', \''+ comprobante.serieCFE +'\', \''+ comprobante.numeroCFE +'\' )"> <td style="width: -webkit-fill-available;">';
    emisor = comprobante.emisor.razonSocial;
    fechaHora = formatFecha(comprobante.emision)
    comprobanteTipo = getTypeCFE(comprobante.tipoCFE) + " " + comprobante.serieCFE + comprobante.numeroCFE;

    const total = comprobante.total;

    const formattedTotal = total % 1 !== 0 ? total.toFixed(2) : total + ".00";

    monto = comprobante.tipoMoneda === "UYU" ? "$" + formattedTotal : "U$S" + formattedTotal;
    RowEmisor = '<label style="cursor: pointer;">' + emisor + '</label>'
    RowComprobante = '<label style="cursor: pointer;">' + comprobanteTipo + '</label>'
    RowFecha = '<label style="cursor: pointer;">' + fechaHora + '</label>'
    RowMonto = '<label style="cursor: pointer;">' + monto + '</label>'
	
    row = tr + RowEmisor + RowComprobante + RowFecha + "</td> <td> </td> <td>" + RowMonto + '</td></tr>';
	$("#recibidosTableBody").append(row);
}