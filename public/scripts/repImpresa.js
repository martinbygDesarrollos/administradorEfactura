function getColorsInfo(){
	sendAsyncPost("representacionimpresa")
	.then( response =>{
		$('#progressbar').modal("hide");
		if(response.result === 2){
			loadDataStyles(response.objectResult);
		}else if(response.result === 1){
			showMessage(response.result, "No se encontraron datos.")
		}
		else{
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

}


function loadDataStyles(obj){

	console.log(obj);
	$("#inputCompColorTamEmisor").val(obj.emitterNameSize);
	$("#inputCompColorDescription").val(obj.colorColumnOdd);
	$("#inputCompColorPrecio").val(obj.colorColumnEven);
	$("#inputCompColorTextoTotal").val(obj.textColorColumnPrimary);

	$("#inputCompColorDetailLineStyle").val(obj.detailLineStyle);
	$("#inputCompColorDetailLineWidth").val(obj.detailLineWidth);
	$("#inputCompColorDetailLineColor").val(obj.detailLineColor);

}


//deshacer todos los cambios de colores
function companieColorsDefault(){



}


function saveCompanieColors(){

	$("#inputCompColorTamEmisor").val(obj.emitterNameSize);
	$("#inputCompColorDescription").val(obj.colorColumnOdd);
	$("#inputCompColorPrecio").val(obj.colorColumnEven);
	$("#inputCompColorTextoTotal").val(obj.textColorColumnPrimary);

	$("#inputCompColorDetailLineStyle").val(obj.detailLineStyle);
	$("#inputCompColorDetailLineWidth").val(obj.detailLineWidth);
	$("#inputCompColorDetailLineColor").val(obj.detailLineColor);

	let data = {
	    "colorColumnOdd": colorDesc,
	    "colorColumnEven": colorPrecio,
	    "detailLineWidth": linesWidth
	    "detailLineColor": null, //color de la linea que separa las filas de detalle
	    "detailLineStyle": "dashed", //diseño de la linea que separa las filas de detalle ej solid, dashed
	    "textColorColumnPrimary": null, //color del texto de la columna total y #
	    "emitterNameSize": "1.1em", //tamaño del nombre de quien emite
	    "hideDueDate": "",
	    "hideExchangeRate": "0",
	    "showIdHeaders": "0",
	    "language": "es"
	}
	sendAsyncPut("representacionimpresa", {data:data})
	.then( response =>{
		$('#progressbar').modal("hide");
		if(response.result === 2){
			loadDataStyles(response.objectResult);
		}else if(response.result === 1){
			showMessage(response.result, "No se encontraron datos.")
		}
		else{
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

}