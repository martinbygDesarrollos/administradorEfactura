var dataIsChanged = false;
var infoAdicionalIsChanged = false;
var dataColorBranches = false;
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
	infoAdicionalIsChanged = false;
	dataColorBranches = false;


	getDateLastCfeReceipt();

	getReportsByCompanie();

	horizontalnav = $("#idDivHorizontalNav").height();
	footer = $("#sticky-footer").height();

	contentHeight = $(document).height() - horizontalnav - footer -2;

	$("#idDivcontentCompanie").css('height', contentHeight);
	$("#idDivcontentCompanie").css('max-height', contentHeight);


	$("#containerCompanies").css('height', contentHeight);
	$("#containerCompanies").css('max-height', contentHeight);

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

function searchEmisores(){
	ruc = $('#emisoresSearchBar').val();
	console.log(ruc)
	loadEmisores(ruc);
}

function verMas(){
	if($('#btnShowHide').text() == "Ver más...") {
		$(".hide").removeClass("hide").addClass("show");
		$('#btnShowHide').text("Ver menos...")
		
	} else {
		$('#btnShowHide').text("Ver más...")
		$(".show").removeClass("show").addClass("hide");
	}
}

function verMasEmpresasConPocosCAEs(){
	if($('#btnShowHideEmpresasConPocosCAEs').text() == "Ver más...") {
		$("#sectionEmpresasConPocosCAEs").removeClass("hide").addClass("show");
		$('#btnShowHideEmpresasConPocosCAEs').text("Ver menos...")
	} else {
		$('#btnShowHideEmpresasConPocosCAEs').text("Ver más...")
		$("#sectionEmpresasConPocosCAEs").removeClass("show").addClass("hide");
	}
}

function verMasEmpresasConCAEsPorVencer(){
	if($('#btnShowHideEmpresasConCAEsPorVencer').text() == "Ver más...") {
		$("#sectionEmpresasConCAEsPorVencer").removeClass("hide").addClass("show");
		$('#btnShowHideEmpresasConCAEsPorVencer').text("Ver menos...")
	} else {
		$('#btnShowHideEmpresasConCAEsPorVencer').text("Ver más...")
		$("#sectionEmpresasConCAEsPorVencer").removeClass("show").addClass("hide");
	}
}

function verMasEmpresasConCertificadosPorVencer(){
	if($('#btnShowHideEmpresasConCertificadosPorVencer').text() == "Ver más...") {
		$("#sectionEmpresasConCertificadosPorVencer").removeClass("hide").addClass("show");
		$('#btnShowHideEmpresasConCertificadosPorVencer').text("Ver menos...")
	} else {
		$('#btnShowHideEmpresasConCertificadosPorVencer').text("Ver más...")
		$("#sectionEmpresasConCertificadosPorVencer").removeClass("show").addClass("hide");
	}
}

function loadEmisores(ruc){
	sendAsyncPost("loadEmisores", {ruc:ruc})
	.then((response)=>{
		if ( response.result == 2 ){
			$('#ruc').val(response.objectResult.RUC);
			$('#denominacion').val(response.objectResult.DENOMINACION);
			$('#mail').val(response.objectResult.MAIL);
			$('#contacto').val(response.objectResult.MAIL_CONTACTO_TECNICO);
			
			$('#inicio').val(response.objectResult.FECHA_INICIO);
			$('#fin').val(response.objectResult.FECHA_FIN);
			$('#finTransicion').val(response.objectResult.FECHA_FIN_TRANSICION);
			$('#webService').val(response.objectResult.URL_WEBSERVICE);
		} else {
			showReplyMessage(response.result, response.message);
			$('#modalButtonResponseCancel').attr("disabled", true);
			$('#modalButtonResponse').click(function(){
				$('#modalResponse').modal('hide');
			});
			$('#ruc').val("");
			$('#denominacion').val("");
			$('#mail').val("");
			$('#contacto').val("");
			
			$('#inicio').val("");
			$('#fin').val("");
			$('#finTransicion').val("");
			$('#webService').val("");
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

	expireDate = "";
	expireDateTitle = "";
	if(obj.expireDateColor.color){
		expireDate = '<i class="fas fa-exclamation m-2 fa-lg" style="color:'+obj.expireDateColor.color+';"></i>'+comprobante+tipocae+' '+ vencimiento;
		expireDateTitle = obj.expireDateColor.title;
	}else{
		expireDate = comprobante+tipocae+' '+ vencimiento;
	}

	let row = '<tr><td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" ><a href="'+getSiteURL()+'empresas/'+obj.rut+'">'+obj.razonSocial+'</a><br>'+obj.rut+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" >'+estado+'</td>';
	row += '<td onclick="selectCompanie(`'+obj.rut+'`, `'+obj.razonSocial+'`)" title="'+expireDateTitle+'" >'+expireDate+'</td>';
	row += '<td>'+selectCaes+'</td></tr>';

	return row;
}


function loadUsersCompanie(  rut  ){
	sendAsyncPost("loadListUser", {rut:rut})
	.then((response)=>{
		if ( response.result == 2 ){
			$("#usersTableBody tr").remove();
			createRowsToUsersTable(response.objectResult.users);

		}else if ( response.result == 0 ){
			window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}

function createRowsToUsersTable(users) {
	users.forEach(function(user) {
		appendRow(user);
	})
	
}

function appendRow(user){
	// const randomColor = Math.floor(Math.random()*16777215).toString(16);
	let colors = ["#a89e9d", "#7ab4c2", "#37a398"];//#a89e9d
	let randomColor = colors[Math.floor(Math.random() * colors.length)];
	let row = '<tr>';
	let firstLetter = user.name.charAt(0);

	let td0 = '<td class="w-10" id="tdImgUser"> <div class="img-user" style="background-color:' + randomColor + '">' + firstLetter +  '</div> </td>';
	let td1 = '<td class="w-20"> <label class="showModalUser" onclick="showModalUser(\''+ user.email +'\')">' + user.name + '</label> </td>';
	let td2 = '<td class="w-40"> <a href="mailto:'+ user.email +'">' + user.email + '</a> </td>';
	let td3 = '<td class="w-15">';
	if(user.active){
		td3 += '<label class="user-state activo"> ACTIVO </label> </td>';
	} else {
		td3 += '<label class="user-state inactivo"> INACTIVO </label> </td>';
	}
	let fecha = "";
	let hora = "";
	let td4 = '<td class="w-15"> <label > - </label> </td>';
	// Split the string twice
	if(user.lastActivity != null) {
		fecha = user.lastActivity.split("T");
		hora = fecha[1].split("Z");
		hora = hora[0].split(".");
		td4 = '<td class="w-15"> <label >' + fecha[0].replaceAll("-", "/") + " " + hora[0] + '</label> </td>';
	}
	row += td0 + td1 + td2 + td3 + td4;
	row += '</tr>';
	$("#usersTableBody").append(row);
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

function loadUserDetails( email ){
	rutSelected = $("#textRutCompanieSelected").text();
	sendAsyncPost("loadUserDetails", {email: email, rut: rutSelected})
	.then((response)=>{
		if ( response.result == 2 ){
			// Array of hex colors
			var colors = ["#a89e9d", "#7ab4c2", "#37a398"];
			var randomColor = colors[Math.floor(Math.random() * colors.length)];
			$('#modalUserImg').css('background-color', randomColor);
			// Initialize an array to store key-value pairs
			var keyValueArray = [];
			if($.inArray("owner", response.objectResult.scopes) != -1) keyValueArray.push("owner");
			if($.inArray("cfe:read", response.objectResult.scopes) != -1) keyValueArray.push("cfe_read");
			if($.inArray("cfe:write", response.objectResult.scopes) != -1) keyValueArray.push("cfe_write");
			if($.inArray("customers", response.objectResult.scopes) != -1) keyValueArray.push("customers");
			if($.inArray("cae:read", response.objectResult.scopes) != -1) keyValueArray.push("cae_read");
			if($.inArray("cae:write", response.objectResult.scopes) != -1) keyValueArray.push("cae_write");
			if($.inArray("users", response.objectResult.scopes) != -1) keyValueArray.push("users");
			if($.inArray("export_application/vnd.ms-excel", response.objectResult.scopes) != -1) keyValueArray.push("export_application_excel");
			if($.inArray("export_text/xml", response.objectResult.scopes) != -1) keyValueArray.push("export_text_xml");
			if($.inArray("export_application/pdf", response.objectResult.scopes) != -1) keyValueArray.push("export_application_pdf");
			if($.inArray("certificate", response.objectResult.scopes) != -1) keyValueArray.push("certificate");
			if($.inArray("forms:2181", response.objectResult.scopes) != -1) keyValueArray.push("forms_2181");
			if($.inArray("reports", response.objectResult.scopes) != -1) keyValueArray.push("reports");
			
			$("#usersPermisos input[type='checkbox']").prop('checked', false);
			// Iterate through the array and check the checkboxes with matching IDs
			$.each(keyValueArray, function(index, value) {
				// Construct the ID selector
				var idSelector = "checkboxInputOverride_" + value;
				// Use the selector to find the checkbox and set it to "checked"
				$('#' + idSelector).prop("checked", true);
			});
			if(keyValueArray.length == 13)
				$('#permisos').prop("checked", true);
			else 
				$('#permisos').prop("checked", false);

		
			// console.log(owner);
			$('#modalUserImg').html(response.objectResult.name.charAt(0));
			$('#modalUserName').text(response.objectResult.name);
			$('#modalUserEmail').text(response.objectResult.email);
			if(response.objectResult.active){
				$('#modalUserState').removeClass();
				$('#modalUserState').addClass('user-state activo');
				$('#modalUserState').text("ACTIVO")
			} else {
				$('#modalUserState').removeClass();
				$('#modalUserState').addClass('user-state inactivo');
				$('#modalUserState').text("INACTIVO")
			}
		}else if ( response.result == 0 ){
			// window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}

function selectAllPermisos(checkbox){
	const checkboxes = $(":checkbox[id^='checkboxInputOverride']");
		if (checkbox.checked) {
		// Check if any of the checkboxes are already checked
		const allChecked = checkboxes.filter(":checked").length === checkboxes.length;
		// Toggle the checkboxes
		checkboxes.prop("checked", !allChecked);
		// $(":checkbox[id^='checkboxInputOverride']").prop("checked", true);
	} else {
		checkboxes.prop("checked", false);
	}
}
function newUser_selectAllPermisos(checkbox){
	const checkboxes = $(":checkbox[id^='newUser_checkboxInputOverride']");
	if (checkbox.checked) {
		// Check if any of the checkboxes are already checked
		const allChecked = checkboxes.filter(":checked").length === checkboxes.length;
		// Toggle the checkboxes
		checkboxes.prop("checked", !allChecked);
		// $(":checkbox[id^='checkboxInputOverride']").prop("checked", true);
	} else {
		checkboxes.prop("checked", false);
	}
}

function changeUserState(){
	modalUserState
	if($('#modalUserState').text() == "ACTIVO") {
		$('#modalUserState').removeClass();
		$('#modalUserState').addClass('user-state inactivo');
		$('#modalUserState').text("INACTIVO")
	} else {
		$('#modalUserState').removeClass();
		$('#modalUserState').addClass('user-state activo');
		$('#modalUserState').text("ACTIVO")
	}
}

function showModalUser(email){
	loadUserDetails(email);
	$('#modalShowUser').modal();
}

function showModalNewUser(){
	$('#modalNewUser').modal();
	$("#newUser_name").val("");
	$("#newUser_email").val("");
}

function showModalNewPwd(){
	$('#modalShowUser').modal("hide");
	$('#modalNewPwd').modal();
	$('#newPwd').val("")
	$('#newPwd2').val("")
	$('#modalNewPwdTitle').text("Cambio de contraseña para " + $('#modalUserEmail').text());
	$('#modalNewPwdTitle').data('email', $('#modalUserEmail').text());
}

$('#modalNewPwd').on('shown.bs.modal', function() {
	$('#newPwd').focus();
	$('#passwordMessage').html("");
	$('#passwordMessage').css("background-color", "#fff");
	$('#buttonModalChangePwd').prop("disabled", true);
})

function validatePassword() {
	let password1 = $('#newPwd').val();
	let password2 = $('#newPwd2').val();

	if (password1 === password2) {
		// Passwords match
		if($('#newPwd2').val().length > 3) {
			$('#passwordMessage').html("La contraseña coincide!");
			$('#passwordMessage').css("background-color", "#2bb449");
			$('#buttonModalChangePwd').prop("disabled", false);
		} else {
			$('#buttonModalChangePwd').prop("disabled", true);
			$('#passwordMessage').html("La contraseña debe tener al menos 4 caracteres!");
			$('#passwordMessage').css("background-color", "#ffc107");
		}
	} else {
		// Passwords do not match
		$('#passwordMessage').html("Contraseñas distintas!");
		$('#passwordMessage').css("background-color", "#ffc107");
		$('#buttonModalChangePwd').prop("disabled", true);
	}
}

$('#buttonModalChangePwd').click(function(){
	let rutSelected = $("#textRutCompanieSelected").text();
	let email = $('#modalNewPwdTitle').data('email');
	sendAsyncPost("updatePassword", {email: email, rut: rutSelected})
	.then((response)=>{
		if (response.result == 2){
			$("#modalNewPwd").modal("hide");
			showMessage(2, response.message);
			// window.location.reload();
		}else if ( response.result == 0 ){
			// console.log(response);
			$("#modalNewPwd").modal("hide");
			showMessage(0, response.message);
		}
	})
});

$('#buttonModalSaveUser').click(function(){
	let rutSelected = $("#textRutCompanieSelected").text();
	let email = $("#modalUserEmail").text();
	let name = $("#modalUserName").text();
	let active;
	if($('#modalUserState').text() == "ACTIVO")
		active = true;
	else
		active = false;
	let cellphone = "";
	let scopes = [];
	// Loop through checkboxes in the table's tbody
	$('#usersPermisos input[id^="checkboxInputOverride"]').each(function(index) {
		if ($(this).is(':checked')) {
			// Get the data-format attribute value and push it into the array
  			const dataFormatValue = $(this).data('format');
			scopes.push(dataFormatValue);
		}
	});
	sendAsyncPost("updateUser", {email: email, name: name, active: active, cellphone:cellphone, scopes: scopes, rut: rutSelected})
	.then((response)=>{
		if (response.result == 2){
			$("#modalShowUser").modal("hide");
			showMessage(2, response.message);
			// console.log(response);
			window.location.reload();
		}else if ( response.result == 0 ){
			// console.log(response);
			$("#modalShowUser").modal("hide");
			showMessage(0, response.message);
		}
	})
});

$('#buttonModalNewUser').click(function(){
	let rutSelected = $("#textRutCompanieSelected").text();
	let email = $("#newUser_email").val();
	let name = $("#newUser_name").val();
	let cellphone = "";
	let scopes = [];
	// Loop through checkboxes in the table's tbody
	$('#newUser_usersPermisos input[id^="newUser_checkboxInputOverride"]').each(function(index) {
		if ($(this).is(':checked')) {
			// Get the data-format attribute value and push it into the array
  			const dataFormatValue = $(this).data('format');
			scopes.push(dataFormatValue);
		}
	});
	sendAsyncPost("newUser", {email: email, name: name, cellphone:cellphone, scopes: scopes, rut: rutSelected})
	.then((response)=>{
		if (response.result == 2){
			// console.log(response);
			$("#modalNewUser").modal("hide");
			showMessage(2, response.message);
			window.location.reload();
		} else if ( response.result == 0 ){
			// console.log(response);
			$("#modalNewUser").modal("hide");
			showMessage(0, response.message);
		}
	})
});


$('#modalNewUser').on('hidden.bs.modal', function() {
	$('#newUser_name').val("");
	$('#newUser_email').val("");
	const checkboxes = $(":checkbox[id^='newUser_checkboxInputOverride']");
	$('#newUser_permisos').prop("checked", false);
	checkboxes.prop("checked", false);
})



function createRowsToBranchTableInfo(branch){
	if ( branch.isPrincipal ) {
		$("#tdBranchDataPrincipal").prop('checked', true);
		$("#buttonDeleteBranchCompanieDetails").attr("disabled", "disabled");
		$("#buttonSetPrincipalBranchCompanieDetails").attr("disabled", "disabled");
	} else {
		$("#tdBranchDataPrincipal").prop('checked', false);
		$("#buttonDeleteBranchCompanieDetails").removeAttr("disabled");
		if ( !branch.isTemplate )
			$("#buttonSetPrincipalBranchCompanieDetails").removeAttr("disabled");
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
	if($('#BranchDataDGIModal').val() >= 90){
		$("#templateContainer").removeClass("d-none");
	}
	dataIsChanged = true;
	$("#buttonSubmitCompanieDetails").removeAttr("disabled");
}


function searchCompaniesFromList(text){
	if ( text.length >= 3 ){
		$("#tbodyCompaniesList").empty();
		lastid = 0;
		textToSearch = text
		loadCompanies();
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
	$('#modalSetMainBranch').modal();
});

$('#buttonConfirmSetMainBranch').click(function(){
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
	if(!$('#templateContainer').hasClass("d-none")){
		$('#templateContainer').addClass("d-none");
		$('#BranchDataTemplateModal').prop( "checked", false );
	}
	$('#BranchDataNombreModal').focus();
})

$('#modalNewSucursal').on('hidden.bs.modal', function() {
	$('#BranchDataNombreModal').val("");
	$('#BranchDataDireccionModal').val("");
	$('#BranchDataDeptoModal').val("");
	$('#BranchDataLocalidadModal').val("");
	$('#BranchDataTelModal').val("");
	$('#BranchDataTel2Modal').val("");
	$('#BranchDataCorreoModal').val("");
	$('#BranchDataWebModal').val("");
	$('#BranchDataDGIModal').val("");
	$('#BranchDataTemplateModal').val("");
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
	let codDGI = $('#BranchDataDGIModal').val();
	let isTemplate = false;
	isTemplate = $('#BranchDataTemplateModal').is(':checked');


	let principal = false;
	// POR AHORA NO SE MANDA
	if ($('#option1').prop('checked')) { // Principal
		principal = true;
	} else if ($('#option2').prop('checked')) { // Secundaria
		principal = false; 
	}

	sendAsyncPost("newCompanieBranch", {isPrincipal: principal, nombre: nombre, direccion: direccion, departamento: departamento, localidad: localidad, telefono: telefono, telefono2: telefono2, correo: correo, sitio: sitio, codDGI: codDGI, isTemplate: isTemplate})
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
}
