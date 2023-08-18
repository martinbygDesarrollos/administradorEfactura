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
			console.log();



		}else if(response.result === 1){
			showReplyMessage(1, "No se encontraron datos.", "ok");
		}
		else{
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

}


colorColumnEven
colorColumnOdd
detailLineColor
detailLineStyle
detailLineWidth
emitterNameSize
hideDueDate
hideExchangeRate
language