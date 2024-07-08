tipoCFE = ""
numeroCFE = ""
serieCFE = ""

function getEmitidos(lastId){
    // /company/{{ef_rut}}/cfe/emitidos/
    rutSelected = $("#textRutCompanieSelected").text();
    // console.log(lastIdEmitidos);
	sendAsyncPost("getEmitidos", {rut: rutSelected, lastId : lastId})
	.then((response)=>{
        // console.log(response)
		if ( response.result == 2 ){
			createRowsToEmitidosTable(response.objectResult.emitidos);
            lastItem = response.objectResult.emitidos[response.objectResult.emitidos.length - 1];
            // console.log(lastItem)
            lastIdEmitidos = lastItem.id
            
		} else if ( response.result == 0 ){
		// 	// window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}

function createRowsToEmitidosTable(lista) {
	lista.forEach(function(comprobante) {
        appendRowEmitido(comprobante);
	})
}

function appendRowEmitido(comprobante){
	let tr = '<tr id="' + comprobante.id + '" onclick="showComprobante('+ null +', ' + comprobante.tipoCFE + ', \''+ comprobante.serieCFE +'\', '+ comprobante.numeroCFE +' )"> <td style="width: -webkit-fill-available;">';
    consumidor = comprobante.receptor.nombre ? comprobante.receptor.nombre : "CONSUMIDOR FINAL";
    fechaHora = formatFecha(comprobante.emision)
    comprobanteTipo = getTypeCFE(comprobante.tipoCFE) + " " + comprobante.serieCFE + comprobante.numeroCFE;

    const total = comprobante.total;

    const formattedTotal = total % 1 !== 0 ? total.toFixed(2) : total + ".00";

    monto = comprobante.tipoMoneda === "UYU" ? "$" + formattedTotal : "U$S" + formattedTotal;
    RowConsumidor = '<label style="cursor: pointer;">' + consumidor + '</label>'
    RowComprobante = '<label style="cursor: pointer;">' + comprobanteTipo + '</label>'
    RowFecha = '<label style="cursor: pointer;">' + fechaHora + '</label>'
    RowMonto = '<label style="cursor: pointer;">' + monto + '</label>'
	
    row = tr + RowConsumidor + RowComprobante + RowFecha + "</td> <td> </td> <td>" + RowMonto + '</td></tr>';
	$("#emitidosTableBody").append(row);
}

function getTypeCFE($typeCode){
    switch ($typeCode) {
        case 101: return "e-Ticket";
        case 102: return "Nota de crédito de e-Ticket";
        case 103: return "Nota de débito de e-Ticket";
        case 111: return "e-Factura";
        case 112: return "Nota de crédito de e-Factura";
        case 113: return "Nota de débito de e-Factura";
        case 121: return "e-Factura Exportación";
        case 122: return "Nota de crédito de e-Factura Exportación";
        case 123: return "Nota de débito de e-Factura Exportación";
        case 124: return "e-Remito de Exportación";
        case 131: return "e-Ticket Venta por Cuenta Ajena";
        case 132: return "Nota de crédito de e-Ticket Venta por Cuenta Ajena";
        case 133: return "Nota de débito de e-Ticket Venta por Cuenta Ajena";
        case 141: return "e-Factura Venta por Cuenta Ajena";
        case 142: return "Nota de crédito de e-Factura Venta por Cuenta Ajena";
        case 143: return "Nota de débito de e-Factura Venta por Cuenta Ajena";
        case 151: return "e-Boleta de entrada";
        case 152: return "Nota de crédito de e-Boleta de entrada";
        case 153: return "Nota de débito de e-Boleta de entrada";
        case 181: return "e-Remito";
        case 182: return "e-Resguardo";
        default: return "";
    }
}

function formatFecha(fecha){ // AAAA MM DD HH MM SS
    // Extract date components
    const year = fecha.substr(0, 4);
    const month = fecha.substr(4, 2);
    const day = fecha.substr(6, 2);
    const hour = fecha.substr(8, 2);
    const minute = fecha.substr(10, 2);
    const second = fecha.substr(12, 2);

    // Format the date and time
    const formattedDate = `${day}/${month}/${year}`;
    const formattedTime = `${hour}:${minute}`;

    // Return formatted date and time
    return `${formattedDate} ${formattedTime}`;
}

function showComprobante(RUTEmisor, tipo, serie, numero){
    mostrarLoader(true)
	sendAsyncPost("getCFE", {rut: rutSelected, RUTEmisor: RUTEmisor, tipoCFE: tipo, serieCFE: serie, numeroCFE: numero})
	.then((response)=>{
        // console.log(response)
		if ( response.result == 2 ){
            let iFrame = document.getElementById("iframeVoucherRecibed");
            var dstDoc = iFrame.contentDocument || iFrame.contentWindow.document;
            // Remove the img src with base64 data
            let cleanedHtml = response.objectResult.representacionImpresa;
            if (cleanedHtml.includes("<img src=\"data:image/png;base64,")) {
                cleanedHtml = cleanedHtml.replace(/<img src="data:image\/png;base64,.*?">/g, '');
            }
            // cleanedHtml = response.objectResult.representacionImpresa.replace(/<img src="data:image\/png;base64,.*?">/g, '');

            dstDoc.write(cleanedHtml);
            dstDoc.close();
            if(RUTEmisor && RUTEmisor != "")
                $('#labelUser').parent().css('display', 'none')
            else
                $('#labelUser').parent().css('display', 'inline-block')
            $('#labelHora').text(convertDateTime(response.objectResult.fechaGeneracion))
            $('#labelUser').text(response.objectResult.usuarioNombre)
            $('#labelSucursal').text("Sucursal " + response.objectResult.sucursal)
            mostrarLoader(false)
            $("#modalVoucherRecibed").modal();
        } else if ( response.result == 0 ){
        	window.location.href = getSiteURL() + "cerrar-session";
        }
	})
}
function convertDateTime(inputStr) {
    try {
        // Parse the input string
        const dt = new Date(inputStr);

        // Get the date components
        const day = ('0' + dt.getDate()).slice(-2);
        const month = ('0' + (dt.getMonth() + 1)).slice(-2); // Months are zero-indexed
        const year = dt.getFullYear();

        // Get the time components
        const hours = ('0' + dt.getHours()).slice(-2);
        const minutes = ('0' + dt.getMinutes()).slice(-2);

        // Format the date and time
        const formattedDateTime = `${day}/${month}/${year} ${hours}:${minutes}`;
        return formattedDateTime;
    } catch (error) {
        return "Invalid datetime format";
    }
}