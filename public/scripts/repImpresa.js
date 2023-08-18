function getColorsInfo(){
	const progressBarIdProcess = loadPrograssBar();
	$("#progressbar").modal("show");
	console.log("get colors");
	sendAsyncPost("representacionimpresa")
	.then( response =>{
		stopPrograssBar(progressBarIdProcess);
		$('#progressbar').modal("hide");
		if(response.result === 2){
			console.log(response)
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





}

/*
"colorColumnOdd": "#FFFFFF", //columnas descripcion y cantidad
    "colorColumnEven": null, //columnas precio unitario
    "detailLineWidth": null, //ancho de la linea que separa las filas de detalle ej "1px"
    "detailLineColor": null, //color de la linea que separa las filas de detalle
    "detailLineStyle": "dashed", //diseño de la linea que separa las filas de detalle ej solid, dashed
    "textColorColumnPrimary": null, //color del texto de la columna total y #
    "emitterNameSize": "1.1em", //tamaño del nombre de quien emite
    */