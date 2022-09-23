var dataIsChanged = false;
var companiesList = null;

var lastid = 0;
var textToSearch = null;


$(document).ready(()=>{
	$('#containerCompanies').on('scroll', function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 4)) {
			loadCompanies();
		}
	});

	dataIsChanged = false;


	getDateLastCfeReceipt();

})


function getDateLastCfeReceipt(){

	sendAsyncPost("getDateLastCfeReceipt")
	.then((response)=>{
		console.log(response);

		$("#dateLastCfeReceipt").text(response);
	})
}




$("#formCompanieDetails").submit((e)=>{

	e.preventDefault();
	//document.getElementById("formCompanieDetails")
	//console.log("submit formulario al crear");

	var formData = new FormData(document.getElementById("formCompanieDetails"));
	let rutSelected = $("#textRutCompanieSelected").text();


	formData.append("rut", rutSelected);

	sendAsyncPostForm("changeCompanieData", formData)
	.then(( response )=>{
		console.log(response);
		if (response.result == 2){
			window.location.reload();
		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

})







function selectCompanie( companieRut, companieName ){

	sendAsyncPost("companies", {rut:companieRut, name:companieName})
	.then((response)=>{
		console.log(response);
		if ( response.result == 2 ){
			$("#indexCompanieWork a").text(response.objectResult.razonSocial);
			$("#indexCompanieName").text(response.objectResult.razonSocial);

			$("#textRutCompanieSelected").text(response.objectResult.rut);


			let status = getButtonByStatus(response.objectResult.estado);
			$("#statusCompanieSelected").empty();
			$("#statusCompanieSelected").append(status);

		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}


function loadCompanies(){

	sendAsyncPost("loadCompanies", {lastid:lastid})
	.then((response)=>{
		if ( response.result == 2 ){
			companiesList = response.companiesList;

			if ( response.lastid != lastid ){
				lastid = response.lastid;
				if ( response.companiesList ){
					for (var i = 0; i < response.companiesList.length; i++) {
						row = createRowCompanie(response.companiesList[i]);
						$("#tbodyCompaniesList").append(row);
					}
				}

			}
		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

}


function createRowCompanie(obj){
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

	let estado = '<span class="badge">'+obj.estadoDescripcion+'</span>';
	if (obj.estado == 2 || obj.estado == 7) {
		estado = '<span class="badge" style="background-color: #F44336">'+obj.estadoDescripcion+'</span>';
	}
	else if ( obj.estado == 3 ) {
		estado = '<span class="badge" style="background-color: #FF9800">'+obj.estadoDescripcion+'</span>';
	}
	else if (obj.estado == 4 || obj.estado == 6) {
		estado = '<span class="badge" style="background-color: #4CAF50">'+obj.estadoDescripcion+'</span>';
	}
	else if (obj.estado == 5) {
		estado = '<span class="badge" style="background-color: blue">'+obj.estadoDescripcion+'</span>';
	}


	let row = '<tr><td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" ><a href="'+getSiteURL()+'empresas/'+obj.rut+'">'+obj.razonSocial+'</a><br>'+obj.rut+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" >'+estado+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" >'+comprobante+tipocae+' '+ vencimiento+'</td>';
	row += '<td>'+selectCaes+'</td></tr>';

	return row;
}




function loadBranchCompanieData( value, rut  ){
	sendAsyncPost("loadBranchData", {branch:value, companie:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			createRowsToBranchTableInfo(response.objectResult);
		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}


function createRowsToBranchTableInfo(branch){

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
	$("#tdBranchDataTel").val(branch.telephone1);
	$("#tdBranchDataTel2").val(branch.telephone2);
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
			//console.log(response);
			for (var i = 0; i < response.listResult.length; i++) {
				row = createRowsToCaesTableInfo(response.listResult[i]);
				$("#tbodyCompaniesCaesData").append(row);
			}
		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
		else $("#tbodyCompaniesCaesData").empty();
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
	$("#buttonSubmitCompanieDetails").removeAttr("disabled");
}

function changeColorpickerPrincipal (value){

	datachanged();

	$("#tdBranchDataColorpicker").val(value);
	$("#tdBranchDataColor").val(value);
}


function changeColorpickerSec (value){

	datachanged();

	$("#tdBranchDataColorpicker2").val(value);
	$("#tdBranchDataColor2").val(value);
}


function searchCompaniesFromList(text){
	if ( text.length >= 3 ){
		$("#tbodyCompaniesList").empty();
		sendAsyncPost("loadCompaniesByName", {name:text})
		.then((response)=>{
			if ( response.result == 2 ){
				companiesList = response.companiesList;
				if ( response.companiesList ){
					for (var i = 0; i < response.companiesList.length; i++) {

						row = createRowCompanie(response.companiesList[i]);
						$("#tbodyCompaniesList").append(row);
					}
				}
			}else if ( response.result == 0 ){
				window.location.href = getSiteURL() + "cerrar-session";
			}
		})
	}else{
		$("#tbodyCompaniesList").empty();
		lastid = 0;
		loadCompanies();
	}
}