$(document).ready(()=>{
	$('#containerCompanies').on('scroll', function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 4)) {
			loadCompanies();
		}
	});

})


function selectCompanie( companieRut, companieName ){

	sendAsyncPost("companies", {rut:companieRut, name:companieName})
	.then((response)=>{
		if ( response.result == 2 ){
			$("#indexCompanieWork a").text(companieName);
			$("#indexCompanieName").text(companieName);
		}
	})
}


function loadCompanies(){

	sendAsyncPost("loadCompanies")
	.then((response)=>{
		//console.log(response);
		if ( response.result == 2 ){
			if ( response.companiesList.length > 0 ){
				for (var i = 0; i < response.companiesList.length; i++) {
					row = createRowCompanie(response.companiesList[i]);
					$("#tbodyCompaniesList").append(row);
				}
			}
		}
	})

}


function createRowCompanie(obj){
	console.log(obj);
	tipocae = "";
	if ( obj.tipoCae ){
		tipocae = " "+obj.tipoCae
	}

	comprobante = "";
	if ( obj.comprobante ) {
		comprobante = obj.comprobante;
	}

	vencimiento = "";
	if ( obj.proxVencimiento ) {
		vencimiento = obj.proxVencimiento;
	}


	selectCaes = ""
	if ( obj.caes.length>0 ){
		selectCaes = '<select class="custom-select custom-select-sm shadow-sm">';
		for (var i = 0; i < obj.caes.length; i++) {
			selectCaes += '<option title="Quedan '+obj.caes[i].disponibles+' '+obj.caes[i].tipoDescripcion+'">'+obj.caes[i].disponibles+' '+obj.caes[i].tipoDescAbrev+'</option>';
		}
		selectCaes += '</select>'
	}



	let row = '<tr><td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" ><a href="'+getSiteURL()+'empresas/'+obj.rut+'">'+obj.razonSocial+'</a><br>'+obj.rut+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" >'+obj.estadoDescripcion+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" >'+comprobante+tipocae+' '+ vencimiento+'</td>';
	row += '<td>'+selectCaes+'</td></tr>';

	return row;
}




function loadBranchCompanieData( value, rut  ){
	sendAsyncPost("loadBranchData", {branch:value, companie:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			createRowsToBranchTableInfo(response.objectResult);
		}
	})
}


function createRowsToBranchTableInfo(branch){

	console.log(branch);
	if ( branch.isPrincipal )
		$("#tdBranchDataPrincipal").text("Si");
	else
		$("#tdBranchDataPrincipal").text("No");


	$("#tdBranchDataCodDgi").text(branch.codDGI);
	$("#tdBranchDataNombre").text(branch.nombreComercial);


	if ( branch.logo ){
		$("#tdBranchDataLogo").empty();
		$("#tdBranchDataLogo").append('<img src="data:image/png;base64,'+branch.logo+'" alt="" width="100">')
	}
	else
		$("#tdBranchDataLogo").empty();


	$("#tdBranchDataDireccion").text(branch.direccion);
	$("#tdBranchDataDepto").text(branch.departamento);
	$("#tdBranchDataLocalidad").text(branch.localidad);
	$("#tdBranchDataTel").text(branch.telefono1);
	$("#tdBranchDataTel2").text(branch.telefono2);
	$("#tdBranchDataCorreo").text(branch.email);
	$("#tdBranchDataWeb").text(branch.website);
	$("#tdBranchDataColor").text(branch.colorPrimary);
	$("#tdBranchDataColor2").text(branch.colorSecondary);
}



function loadCaesDetailsCompanies( rut ){
	sendAsyncPost("getCaesByCompanie", {companie:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			console.log(response);
			for (var i = 0; i < response.listResult.length; i++) {
				row = createRowsToCaesTableInfo(response.listResult[i]);
				$("#tbodyCompaniesCaesData").append(row);
			}
		}else $("#tbodyCompaniesCaesData").empty();
	})
}


function createRowsToCaesTableInfo(objCae){

	let row = '<tr><td>'+tableCfeType(objCae.tipoCFE)+'</td>';
	row += '<td>'+ dateTypeHtml(objCae.vencimiento)+'</td>';
	row += '<td>'+objCae.disponibles+'</td>';
	row += '<td>'+objCae.total+'</td></tr>';


	return row;

}