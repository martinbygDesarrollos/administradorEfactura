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

	if (obj.emitterNameSize)
		$("#inputCompColorTamEmisor").val(obj.emitterNameSize);

	if(obj.colorColumnOdd)
		$("#inputCompColorDescription").val(obj.colorColumnOdd);

	if (obj.colorColumnEven)
		$("#inputCompColorPrecio").val(obj.colorColumnEven);

	if (obj.textColorColumnPrimary)
		$("#inputCompColorTextoTotal").val(obj.textColorColumnPrimary);

	if(obj.detailLineStyle)
		$("#inputCompColorDetailLineStyle").val(obj.detailLineStyle);

	if(obj.detailLineWidth)
		$("#inputCompColorDetailLineWidth").val(obj.detailLineWidth);

	if(obj.detailLineColor)
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

function assignColor(value, idInput){

	$(idInput).val(value);
	datachangedRepImpresa();
}



//habilita el botón del formulario de representacion impresa
function datachangedRepImpresa(){
	dataIsChanged = true;
	$("#buttonSubmitCompanieColors").removeAttr("disabled");
}