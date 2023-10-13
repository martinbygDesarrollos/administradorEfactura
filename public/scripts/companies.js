var dataIsChanged = false;
var companiesList = null;

var lastid = 0;
var textToSearch = null;

var statusFilter = null;


$(document).ready(()=>{
	$('#containerCompanies').on('scroll', function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 4)) {
			loadCompanies();
		}
	});

	dataIsChanged = false;


	getDateLastCfeReceipt();

	getReportsByCompanie();

	horizontalnav = $("#idDivHorizontalNav").height();
	footer = $("#sticky-footer").height();

	contentHeight = $(document).height() - horizontalnav - footer;

	$("#idDivcontentCompanie").css('height', contentHeight);
	$("#idDivcontentCompanie").css('max-height', contentHeight);

})


async function getDateLastCfeReceipt(){

	sendAsyncPost("getDateLastCfeReceipt")
	.then((response)=>{
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
	formData.append("codDgi", $("#selectBranchCompanieDetails").val());


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



$("#formCompanieColors").submit((e)=>{

	e.preventDefault();

	var formData = new FormData(document.getElementById("formCompanieColors"));
	let rutSelected = $("#textRutCompanieSelected").text();


	formData.append("rut", rutSelected);
	formData.append("codDgi", $("#selectBranchCompanieDetails").val());


	sendAsyncPostForm("changeCompanieColor", formData)
	.then(( response )=>{
		console.log(response);
		if (response.result == 2){
			window.location.reload();
		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})

});




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

	filter = [textToSearch,statusFilter];

	sendAsyncPost("loadCompanies", {lastid:lastid, filter:filter})
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
	if ( branch.isPrincipal ) {
		console.log("principal");
		$("#tdBranchDataPrincipal").prop('checked', true);
		$("#buttonDeleteBranchCompanieDetails").attr("disabled", "disabled");
		// $("#buttonSetPrincipalBranchCompanieDetails").attr("disabled", "disabled");
	} else {
		console.log("sucursal");
		$("#tdBranchDataPrincipal").prop('checked', false);
		$("#buttonDeleteBranchCompanieDetails").removeAttr("disabled");
		// $("#buttonSetPrincipalBranchCompanieDetails").removeAttr("disabled");
	}

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


//habilita el botón del formulario que está en principal
function datachanged(){
	dataIsChanged = true;
	$("#buttonSubmitCompanieDetails").removeAttr("disabled");
}


function searchCompaniesFromList(text){
	if ( text.length >= 3 ){
		$("#tbodyCompaniesList").empty();
		lastid = 0;
		textToSearch = text
		loadCompanies();
		/*sendAsyncPost("loadCompaniesByName", {name:text})
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
		})*/
	}else{
		textToSearch = "";
		$("#tbodyCompaniesList").empty();
		lastid = 0;
		loadCompanies();
	}
}



function selectAllOpFilter (idForm){

	event.stopPropagation();
	$('#'+idForm+' div input').each(function(index, element) {
		element.checked = true;
   	})

}


function unselectOpFilter(idForm){

	event.stopPropagation();
	$('#'+idForm+' div input').each(function(index, element) {
		element.checked = false;
   	})

}


function formFilter(idForm){
	statusFilter = "";

	$('#'+idForm+' div input').each(function(index, element) {
		if (element.checked){
			statusFilter += element.value + ", ";
		}
   	})

	statusFilter = statusFilter.substr( 0, statusFilter.length -2);
	if ( statusFilter.length > 0 ){
		lastid = 0;
		$('#tbodyCompaniesList').empty();
		loadCompanies();

	}else $('#tbodyCompaniesList').empty();

}

$('#buttonDeleteBranchCompanieDetails').click(function(){
	$('#modalDeleteBranch').modal();
});

$('#buttonConfirmDeleteBranch').click(function(){
	branchSelected = $("#selectBranchCompanieDetails").val();
	rutSelected = $("#textRutCompanieSelected").text();
	deleteBranchCompanie(branchSelected, rutSelected);
});

function deleteBranchCompanie( branch, rut  ){
	sendAsyncPost("deleteBranch", {branch:branch, companie:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			showMessage(response.result, response.message);
			window.location.reload();
		}else if ( response.result == 0 ){	
			showMessage(response.result, response.message);
		}
	})
}

$('#buttonSetPrincipalBranchCompanieDetails').click(function(){
	branchSelected = $("#selectBranchCompanieDetails").val();
	rutSelected = $("#textRutCompanieSelected").text();
	setPrincipalBranch(branchSelected, rutSelected);
});

function setPrincipalBranch( branch, rut  ){
	sendAsyncPost("setPrincipalCompanieBranch", {branch:branch, companie:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			showMessage(response.result, response.message);
			window.location.reload();
		}else if ( response.result == 0 ){
			showMessage(response.result, response.message);
		}
	})
}

function createNewSucursalModal(){
	$('#modalNewSucursal').modal();
}
$('#modalNewSucursal').on('shown.bs.modal', function() {
	$('#BranchDataNombreModal').focus();
})

$('#buttonModalNewSucursal').click(function(){

	let nombre = $('#BranchDataNombreModal').val();
	let direccion = $('#BranchDataDireccionModal').val();
	let departamento = $('#BranchDataDeptoModal').val();
	let localidad = $('#BranchDataLocalidadModal').val();
	let telefono = $('#BranchDataTelModal').val();
	let telefono2 = $('#BranchDataTel2Modal').val();
	let correo = $('#BranchDataCorreoModal').val();
	let sitio = $('#BranchDataWebModal').val();

	let principal = false;
	// POR AHORA NO SE MANDA
	// if ($('#option1').prop('checked')) { // Principal
	// 	principal = true;
	// } else if ($('#option2').prop('checked')) { // Secundaria
	// 	principal = false; 
	// }

	sendAsyncPost("newCompanieBranch", {isPrincipal: principal, nombre: nombre, direccion: direccion, departamento: departamento, localidad: localidad, telefono: telefono, telefono2: telefono2, correo: correo, sitio: sitio})
	.then((response)=>{
		if ( response.result == 2 ){
			showMessage(response.result, response.message);
			window.location.reload();
		}else if ( response.result == 0 ){
			showMessage(response.result, response.message);
		}
	})
});


async function getReportsByCompanie(){

	/*let periodo = $("idSelectDateReports");
	sendAsyncPost("getReportsByCompanie", {date:periodo})
	.then((response)=>{
		console.log(response);

		if (response.result == 2){


			for (const [key, value] of Object.entries(response.objectResult)) {
				$("#tbodyCompaniesReports").append("<tr><td>"+tableCfeType(key)+"</td><td>"+value+"</td></tr>");
			}

		}else{
			$("#tbodyCompaniesReports").empty();
		}
	})*/
}




/*

async function selectAllFilesXml(value){
	$("#tbodyCfeFilesImported tr").map((index, tr)=>{
		if (index <= 99){
			tr.getElementsByTagName("input")[0].checked = value;
		}
	})
}



async function sendFilesXml(){

	//poner la pantalla de carga

	$("#tbodyCfeFilesImported tr").map((index, tr)=>{

		if (tr.getElementsByTagName("input")[0].checked){

			let trid = tr.id.split("_");
			sendAsyncPost("sendImportCfeEmitedXml", {folder:trid[0], name:trid[1]})
			.then((response)=>{
				console.log(response);
			})
		}
	})

}

*/