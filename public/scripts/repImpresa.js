function getColorsInfo(){
	sendAsyncPost("representacionimpresa")
	.then( response =>{
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


function saveCompanieColors( data ){

	if ( !data ){
		let sizeEmitterName = $("#inputCompColorTamEmisor").val();
		let colorDesc = $("#inputCompColorDescription").val();
		let colorPrecio = $("#inputCompColorPrecio").val();
		let colorTotal = $("#inputCompColorTextoTotal").val();

		let linesStyle = $("#inputCompColorDetailLineStyle").val();
		let linesWidth = $("#inputCompColorDetailLineWidth").val();
		let linesColor = $("#inputCompColorDetailLineColor").val();

		let data = {
		    "colorColumnOdd": colorDesc,
		    "colorColumnEven": colorPrecio,
		    "detailLineWidth": linesWidth,
		    "detailLineColor": linesColor,
		    "detailLineStyle": linesStyle,
		    "textColorColumnPrimary": colorTotal,
		    "emitterNameSize": sizeEmitterName
		}
	}

	sendAsyncPut("representacionimpresa", {data:data})
	.then( response =>{
		console.log(response);
	})

}