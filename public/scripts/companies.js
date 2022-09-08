var dataIsChanged = false;





$(document).ready(()=>{
	$('#containerCompanies').on('scroll', function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 4)) {
			loadCompanies();
		}
	});

	dataIsChanged = false;
})







$("#formCompanieDetails").submit((e)=>{

	e.preventDefault();

	console.log("submit formulario al crear");
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
		$("#tdBranchDataPrincipal").prop('checked', true);
	else
		$("#tdBranchDataPrincipal").prop('checked', false);


	$("#tdBranchDataCodDgi").val(branch.codDGI);
	$("#tdBranchDataNombre").val(branch.nombreComercial);


	if ( branch.logo ){
		$("#tdBranchDataLogo").empty();
		$("#tdBranchDataLogo").append('<img src="data:image/png;base64,'+branch.logo+'" alt="" width="100">')
	}
	else{
		$("#tdBranchDataLogo").empty();
		$("#tdBranchDataLogo").append('<p>Sin logo</p>')
	}


	$("#tdBranchDataDireccion").val(branch.direccion);
	$("#tdBranchDataDepto").val(branch.departamento);
	$("#tdBranchDataLocalidad").val(branch.localidad);
	$("#tdBranchDataTel").val(branch.telefono1);
	$("#tdBranchDataTel2").val(branch.telefono2);
	$("#tdBranchDataCorreo").val(branch.email);
	$("#tdBranchDataWeb").val(branch.website);

	$("#tdBranchDataColor").val(branch.colorPrimary);
	$("#tdBranchDataColorpicker").val(branch.colorPrimary);

	$("#tdBranchDataColor2").val(branch.colorSecondary);
	$("#tdBranchDataColorpicker2").val(branch.colorSecondary);
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

function datachanged(){
	dataIsChanged = true;
	//$("#buttonSubmitCompanieDetails").removeAttr("disabled");
}

function changeColorpickerPrincipal (){

	datachanged();

	colorSelected = $("#tdBranchDataColorpicker").val();
	$("#tdBranchDataColor").val(colorSelected);
}


function changeColorpickerSec (){

	datachanged();

	colorSelected = $("#tdBranchDataColorpicker2").val();
	$("#tdBranchDataColor2").val(colorSelected);
}