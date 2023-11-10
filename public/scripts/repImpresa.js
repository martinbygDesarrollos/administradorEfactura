//funciones sin nombre
$("#formUpdateRepImpresa").on( "submit", function( event ) {
	event.preventDefault();
	console.log("toda accion del submit cancelado");
	saveCompanieColors( null )
	.then((response)=>{
		showMessage(response.result, response.message);
	})
});



//cuando se emite el evento de mostrar el modal buscar los datos del pdf a mostrar
$('#modalPreviewVoucher').on('show.bs.modal', function (e) {
	//e.preventDefault();
	let tamEmisor = "1px";
	if ($("#inputCompColorTamEmisor").val() > 0 )
		tamEmisor = $("#inputCompColorTamEmisor").val() + $("#inputCompColorTamEmisorUnidadMedida").val()

	let descColor = $("#inputCompColorDescColor").val();
	let colorPrecio = $("#inputCompColorPrecioColor").val();
	let totalColor = $("#inputCompColorTextoTotalColor").val();
	let lineStyle = $("#inputCompColorDetailLineStyle").val() || "solid";
	let detailLineColor = $("#inputCompColorDetailLineColor").val();

	let detailLineWidth = "1px";
	if ($("#inputCompColorDetailLineWidth").val() > 0 )
		detailLineWidth = $("#inputCompColorDetailLineWidth").val() + $("#inputCompColorDetailLineWidthUnidadMedida").val()

	const data = new URLSearchParams({
	    tamEmisor: tamEmisor,
	    descColor: descColor,
	    colorPrecio:colorPrecio,
	    totalColor:totalColor,
	    lineStyle:lineStyle,
	    detailLineColor:detailLineColor,
	    detailLineWidth:detailLineWidth
	})

	fetch("../voucherExample.php?" + data)
		.then((res) => {
			if (res.status != 200)
				return false;

			return res.text()
		})
		.then((html) => {
			if(html){
				const iFrame = document.getElementById("iframePreviewVoucher");

				var dstDoc = iFrame.contentDocument || iFrame.contentWindow.document;
				dstDoc.write(html);
				dstDoc.close();
			}
		})
		.catch((e) => showMessage(1, "Error al mostrar la vista previa"));
	})


//obtener valores de la representacion impresa de la empresa
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

//cargar los valores de la representación impresa actual en los inputs y colorpicker
//si los datos que vienen son null, se cargan los colores blancos y gris por defecto
function loadDataStyles(obj){

	if (obj.emitterNameSize){
		let emitterNameSize = obj.emitterNameSize
		let onlyNumber = emitterNameSize.replace(/[a-zA-Z]/g, "")
		let onlyText = emitterNameSize.replace(/[0-9.]/g, "")
		$("#inputCompColorTamEmisor").val(onlyNumber);
		$("#inputCompColorTamEmisorUnidadMedida").val(onlyText)
	}

	if(obj.colorColumnOdd){
		$("#inputCompColorDescColor").val(obj.colorColumnOdd);
		$("#inputCompColorDescription").val(obj.colorColumnOdd);
	}else
		$("#inputCompColorDescColor").val("#eeeeee");

	if (obj.colorColumnEven){
		$("#inputCompColorPrecioColor").val(obj.colorColumnEven);
		$("#inputCompColorPrecio").val(obj.colorColumnEven);
	}else
		$("#inputCompColorPrecioColor").val("#dddddd");

	if (obj.textColorColumnPrimary){
		$("#inputCompColorTextoTotalColor").val(obj.textColorColumnPrimary);
		$("#inputCompColorTextoTotal").val(obj.textColorColumnPrimary);
	}else
		$("#inputCompColorTextoTotalColor").val("#ffffff");

	if(obj.detailLineStyle)
		$("#inputCompColorDetailLineStyle").val(obj.detailLineStyle);

	if(obj.detailLineWidth){
		let num = obj.detailLineWidth.replace(/\D/g, "")
		$("#inputCompColorDetailLineWidth").val(num);
	}

	if(obj.detailLineColor){
		$("#inputCompColorDetailLineColor").val(obj.detailLineColor);
		$("#inputCompColorDetailLine").val(obj.detailLineColor);
	}else
		$("#inputCompColorDetailLineColor").val("#ffffff");

}

//deshacer todos los cambios de colores
function companieColorsUndo(){

	getColorsInfo()
	datachangedRepImpresa();

	document.getElementById("btnComColorUndo").disabled = true;
	document.getElementById("btnComColorUndo").hidden = true;

}

//deshacer todos los cambios de colores
function companieColorsDefault(){

	$("#inputCompColorTamEmisor").val("");
	$("#inputCompColorTamEmisorUnidadMedida").val("px")
	$("#inputCompColorDescription").val("#eeeeee");//#eeeeee
	$("#inputCompColorDescColor").val("#eeeeee"); //#eeeeee
	$("#inputCompColorPrecio").val("#dddddd");//#dddddd
	$("#inputCompColorPrecioColor").val("#dddddd"); //#dddddd
	$("#inputCompColorTextoTotal").val("#ffffff");//#ffffff
	$("#inputCompColorTextoTotalColor").val("#ffffff");//#ffffff
	$("#inputCompColorDetailLineStyle").val("solid"); //solid
	$("#inputCompColorDetailLineWidth").val("1"); //1px
	$("#inputCompColorDetailLineWidthUnidadMedida").val("px")
	$("#inputCompColorDetailLineColor").val("#ffffff");//#ffffff
	$("#inputCompColorDetailLine").val("#ffffff");//#ffffff

	datachangedRepImpresa();

}


//si viene data puede ser que
function saveCompanieColors( data ){

	return new Promise((resolve, reject)=>{

		if ( !data ){
			data = getFormValues()
		}

		console.log(data);
		sendAsyncPut("representacionimpresa", {data:data})
		.then( response =>{
			resolve(response)
		})
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
	document.getElementById("btnComColorUndo").disabled = false;
	document.getElementById("btnComColorUndo").hidden = false;
}


function getFormValues(){
	let sizeEmitterNameValue = $("#inputCompColorTamEmisor").val() > 0 ? $("#inputCompColorTamEmisor").val() : null;
	if (sizeEmitterNameValue > 0){
		sizeEmitterNameValue += $("#inputCompColorTamEmisorUnidadMedida").val() ? $("#inputCompColorTamEmisorUnidadMedida").val() : "px";
	}

	let colorDesc = $("#inputCompColorDescription").val() ? $("#inputCompColorDescription").val() : null;
	let colorPrecio = $("#inputCompColorPrecio").val() ? $("#inputCompColorPrecio").val() : null;
	let colorTotal = $("#inputCompColorTextoTotal").val() ? $("#inputCompColorTextoTotal").val() : null;

	let linesStyle = $("#inputCompColorDetailLineStyle").val() ? $("#inputCompColorDetailLineStyle").val() : null;
	let linesWidthValue = $("#inputCompColorDetailLineWidth").val() ? $("#inputCompColorDetailLineWidth").val() : null;
	if (linesWidthValue > 0){
		linesWidthValue += $("#inputCompColorDetailLineWidthUnidadMedida").val() ? $("#inputCompColorDetailLineWidthUnidadMedida").val() : "px";
	}

	let linesColor = $("#inputCompColorDetailLine").val() ? $("#inputCompColorDetailLine").val() : null;

	data = {
	    "colorColumnOdd": colorDesc,
	    "colorColumnEven": colorPrecio,
	    "detailLineWidth": linesWidthValue,
	    "detailLineColor": linesColor,
	    "detailLineStyle": linesStyle,
	    "textColorColumnPrimary": colorTotal,
	    "emitterNameSize": sizeEmitterNameValue
	}

	return data;
}