numberOfRows = 0;
textToSearch = "";
//obtener el listado de clientes
function loadCustomers(){
	rutSelected = $("#textRutCompanieSelected").text();
	sendAsyncPost("loadListCustomers", {rut: rutSelected})
	.then((response)=>{
		if ( response.result == 2 ){
			$("#customersTableBody tr").remove();
			createRowsToCustomersTable(response.objectResult.customers, 20);
	
		} else if ( response.result == 0 ){
			// window.location.href = getSiteURL() + "cerrar-session";
		}
	})
}

function createRowsToCustomersTable(customers, cantidad) {
	var index = 0;
	customers.forEach(function(customer) {
		if(cantidad >= numberOfRows)
			appendRowCustomer(customer, 'show', index);
		else 
			appendRowCustomer(customer, 'hide', index);
		index += 1;
	})
	console.log("numero de rows: " + numberOfRows)
}

function appendRowCustomer(customer, estado, index){
	if(estado == 'show')
		numberOfRows += 1;
	let row = '<tr id="' + customer.document + '" class="' + estado + '" onclick="showModalCustomer(\''+ customer.document +'\')" >';
	let td1 = '<td> <label class="showModalCustomer" >' + customer.name + '</label> </td>';
	let td2 = '<td> <label style="cursor: pointer;">' + customer.document + '</label> </td>';
	let correos = '';
	let telefonos = '';
	let td3 = '<td> ';
	let td4 = '<td> ';
	if(customer.contacts){
		customer.contacts.forEach(function(contact) {
			if(contact.contactType == 1){
				if(correos != '')
					correos += ', ' + contact.value;
				else 
					correos += contact.value;
			}
			// td3 = '<td> <a onclick="return false;" href="mailto:' + contact.value +'">' + contact.value + '</a> </td>';
			if(contact.contactType == 5 || contact.contactType == 6){
				if(telefonos != '')
					telefonos += ', ' + contact.value;
				else 
					telefonos += contact.value;
			}
			// telefonos += contact.value;
			// td4 = '<td> ' + contact.value + '</a> </td>';
		})
	}
	if(correos != '')
		td3 += '<a onclick="return false;" href="mailto:' + correos +'">' + correos + '</a> </td>'
	else
		td3+= '</td>'

	if(telefonos != '')
		td4 +=  telefonos + '</a> </td>';
	else
		td4+= '</td>'
	row += td1 + td2 + td3 + td4;
	row += '</tr>';
	$("#customersTableBody").append(row);
}

function updateTable(tabla){
	var index = 0;
	var goal = numberOfRows + 20;
	console.log(textToSearch)
	if(textToSearch == ""){
		$('#' + tabla).find('tr').each(function() {
			if($(this).hasClass( "hide" )){
				if( index <= goal){
					$(this).removeClass().addClass("show")
				} else {
					numberOfRows = goal;
					return false;
				}
			}
			index += 1;
		});
	}
}

function evaluateSearch() {
    textToSearch = $('#clientesSearchBar').val().trim();
	console.log(textToSearch)
    if (textToSearch.length >= 4) {
        $('#clientesSearchButton').click();
    } else {
		numberOfRows = 0;
		updateTable('customersTableBody')
	}
}

function searchClientes() {
    textToSearch = $('#clientesSearchBar').val().toLowerCase() || "";
	console.log(textToSearch.length)
	if(textToSearch.length >= 4){
		$('#customersTableBody').find('tr').each(function() {
			var tr = $(this);
			var found = false;
			tr.find('td').each(function() {
				var tdText = $(this).text().toLowerCase();
				if (tdText.includes(textToSearch)) {
					found = true;
					return false; // exit loop if found in this td
				}
			});
			
			if (found) {
				tr.removeClass().addClass("show"); // show the row if found
			} else {
				tr.removeClass().addClass("hide"); // hide the row if not found
			}
		});
	} else if(textToSearch == ""){
		numberOfRows = 0;
		updateTable('customersTableBody')
	}
}

function mostrarLoader(valor){
	if(valor){
		$('.loaderback').css('display', 'block')
		$('.loader').css('display', 'block')
	} else {
		$('.loaderback').css('display', 'none')
		$('.loader').css('display', 'none')
	}
}

function showModalCustomer(document){
	mostrarLoader(true)
	rutSelected = $("#textRutCompanieSelected").text();
	sendAsyncPost("loadCustomer", {rut: rutSelected, document: document})
	.then((response)=>{
		if ( response.result == 2 ){
			console.log(response);
			createRowsToModal(response.objectResult)
			if(response.objectResult.notificationMethods.length !== 0)
				$(":checkbox[id^='inputSendAuto']").prop("checked", true);
			else 
				$(":checkbox[id^='inputSendAuto']").prop("checked", false);
			$("#modalShowCustomer").attr("data-documentType", response.objectResult.documentType)
			mostrarLoader(false)
			$('#modalShowCustomer').modal();
		} else if ( response.result == 0 ){
			// window.location.href = getSiteURL() + "cerrar-session";
			mostrarLoader(false)
		} else {
			mostrarLoader(false)
		}
	})
	// var tr = $('#customersTableBody').find('tr#'+document);
}

function showModalNewCustomer(){
	$('#modalCustomerBodyNewCustomer').empty()
	$("#listContactsNewCustomer").addClass("hide")
	$("#listContactsNewCustomer").empty()
	$('#docNewCustomer').val("")
	$('#nameNewCustomer').val("")
	$(":checkbox[id^='inputSendAutoNewCustomer']").prop("checked", false);
	$('#modalNewCustomer').modal();
	// return;
}

function createRowsToModal(object){
	console.log(object)
	$('#modalCustomerBody').empty()
	$("#listContacts").addClass("hide")
	$("#listContacts").empty()
	$('#docCustomer').val(object.document)
	$('#nameCustomer').val(object.name)
	var index = 0;
	object.contacts.forEach(function(contacto) {
		appendRowContact(contacto, index, 'modalCustomerBody');
		index ++;
	})
}

function appendRowContact(contacto, index, modal){
	id = "";
	text = "";
	switch (contacto.contactType) {
		case 1: // Correo electronico
			id = "emailCustomer" + index
			text = "Correo electrónico"
			break;
		case 5: // Telefono fijo
			id = "telCustomer" + index
			text = "Teléfono fijo"
			break;
		case 6: // Celular
			id = "celCustomer" + index
			text = "Celular"
			break;
		case 7: // FAX
			id = "faxCustomer" + index
			text = "Fax"
			break;
		case 8: // OTRO
			id = "otroCustomer" + index
			text = "Otro"
			break;
		default: // NO SE
			break;
	}
	row = "<div class=\"row mt-2\" style=\"align-items: center; \"><div class=\"col-4\"> <label for=\"" + id + "\">" + text + " </label></div> <div class=\"col-8\" style=\"display: flex; flex-wrap: nowrap;\"> <input class=\"form-control\" id=\"" + id + "\" type=\"text\" value=\"" + contacto.value + "\" /> <button onclick=\"this.parentNode.parentNode.remove()\" type=\"button\" title=\"Eliminar contacto\" class=\"btn btn-warning shadow-sm ml-2\" tabindex=\"5\" style=\"border-radius: 50%;\"><i class=\"fas fa-trash\"></i></button> </div> </div>"
	$('#' + modal).append(row);
}

function saveCustomer(){
	rutSelected = $("#textRutCompanieSelected").text();
	documento = $("#docCustomer").val().trim();
	nombre = $("#nameCustomer").val().trim() || "";
	documentType = $("#modalShowCustomer").attr("data-documentType")
	notificationMethods = [];
	const contacts = [];

	$('#modalCustomerBody .row').each(function() {
        const label = $(this).find('label').text().trim();
        const input = $(this).find('input');
        const contactType = getContactType(label);
        const value = input.val().trim();
        
        // Add contact to the contacts array
        contacts.push({ contactType, value });
    });

	if($(":checkbox[id^='inputSendAuto']").prop("checked"))
		notificationMethods = [1];
	else 
		notificationMethods = [0];

	const customerObject = {
		"document": documento,
		"documentType": documentType,
		"name": nombre,
		"notificationMethods": notificationMethods,
		"contacts": contacts
	};
	console.log(customerObject);

	sendAsyncPost("saveCustomer", {rut: rutSelected, data: customerObject})
	.then((response)=>{
		message = "";
		if ( response.result == 2 ){
			// console.log(response);
			message = "Cliente guardado con exito!";
			var element = $('#customersTableBody').find('tr[id="' + documento + '"]');
			element.find('.showModalCustomer').text(nombre);
			
			// Filter out email addresses
			const emails = contacts.filter(contact => contact.contactType == 1).map(contact => contact.value);
			
			// Join the emails with a comma
			const emailString = emails.join(', ');
			
			// Filter out email addresses
			const numbers = contacts.filter(contact => contact.contactType == 5 || contact.contactType == 6).map(contact => contact.value);
			
			// Join the emails with a comma
			const numberString = numbers.join(', ');
			
			element.find('a').text(emailString).attr('href', 'mailto:' + emailString + '\'');
			element.find('td:last-child').text(numberString);

		} else if ( response.result == 0 ){
			message = "Error. No se pudo guardar el cliente";
			// console.log(response);
		}

		$('#modalShowCustomer').modal('hide');
		showReplyMessage(response.result, message);
		$('#modalButtonResponseCancel').attr("disabled", true);
		$( "#modalButtonResponse" ).off( "click");
		$('#modalButtonResponse').click(function(){
			$('#modalResponse').modal('hide');
		});
	})
}

function newCustomer(){
	rutSelected = $("#textRutCompanieSelected").text();
	documento = $("#docNewCustomer").val().trim();
	nombre = $("#nameNewCustomer").val().trim() || "";
	// documentType = 3
	notificationMethods = [];
	const contacts = [];

	$('#modalCustomerBodyNewCustomer .row').each(function() {
        const label = $(this).find('label').text().trim();
        const input = $(this).find('input');
        const contactType = getContactType(label);
        const value = input.val().trim();
        
        // Add contact to the contacts array
        contacts.push({ contactType, value });
    });

	if($(":checkbox[id^='inputSendAutoNewCustomer']").prop("checked"))
		notificationMethods = [1];
	else
		notificationMethods = [0];

	const customerObject = {
		"document": documento,
		"name": nombre,
		"notificationMethods": notificationMethods,
		"contacts": contacts
	};
	console.log(customerObject);

	sendAsyncPost("loadCustomer", {rut: rutSelected, document: documento})
	.then((response)=>{
		message = "";
		if ( response.result == 2 ){
			console.log(response);
			// message = "Cliente guardado con exito!";
			if ( response.objectResult.document ){ // cliente ya existe
				console.log(response);
				message = "Error. Ya existe un cliente con ese documento";
				$('#modalNewCustomer').modal('hide');
				showReplyMessage(response.result, message);
				$('#modalButtonResponseCancel').attr("disabled", true);
				$( "#modalButtonResponse" ).off( "click");
				$('#modalButtonResponse').click(function(){
					$('#modalResponse').modal('hide');
					$('#modalNewCustomer').modal();
				});
			} else { // cliente no existe
				console.log("Cliente no existe")
				sendAsyncPost("newCustomer", {rut: rutSelected, data: customerObject})
				.then((response)=>{
					console.log(response);
					if ( response.result == 2 ){
						message = "Cliente creado con éxito!";
						appendCustomer(customerObject);
					} else {
						message = "Error. No se pudo guardar el cliente";
						// console.log(response);
					}
					$('#modalNewCustomer').modal('hide');
					showReplyMessage(response.result, message);
					$('#modalButtonResponseCancel').attr("disabled", true);
					$("#modalButtonResponse" ).off( "click");
					$('#modalButtonResponse').click(function(){
						$('#modalResponse').modal('hide');
						if ( response.result != 2 ) $('#modalNewCustomer').modal();
					});
				})
			}
		} else if ( response.result == 0 ){
			message = "Error. No se pudo guardar el cliente";
			// console.log(response);
			$('#modalNewCustomer').modal('hide');
			showReplyMessage(response.result, message);
			$('#modalButtonResponseCancel').attr("disabled", true);
			$("#modalButtonResponse" ).off( "click");
			$('#modalButtonResponse').click(function(){
				$('#modalResponse').modal('hide');
			});
		}

	})
}

function getContactType(label) {
    switch (label) {
        case 'Correo electrónico':
            return 1;
        case 'Teléfono fijo':
            return 5;
        case 'Celular':
            return 6;
        case 'Fax':
            return 7;
        case 'Otro':
            return 8;
        default:
            return -1; 
    }
}

function loadOptions(target, modal){
	$("#" + target).toggleClass("hide");
	var option1 = $("<option style='cursor:pointer;' class='option' onclick='createNewContact(this.value, \"" + target + "\", \"" + modal + "\")'>").attr("value", "1").text('Correo electrónico');
	var option2 = $("<option style='cursor:pointer;' class='option' onclick='createNewContact(this.value, \"" + target + "\", \"" + modal + "\")'>").attr("value", "5").text('Teléfono fijo');
	var option3 = $("<option style='cursor:pointer;' class='option' onclick='createNewContact(this.value, \"" + target + "\", \"" + modal + "\")'>").attr("value", "6").text('Celular');
	var option4 = $("<option style='cursor:pointer;' class='option' onclick='createNewContact(this.value, \"" + target + "\", \"" + modal + "\")'>").attr("value", "7").text('Fax');
	var option5 = $("<option style='cursor:pointer;' class='option' onclick='createNewContact(this.value, \"" + target + "\", \"" + modal + "\")'>").attr("value", "8").text('Otro');
	$("#" + target).empty();
    $("#" + target).append(option1);
    $("#" + target).append(option2);
    $("#" + target).append(option3);
    $("#" + target).append(option4);
    $("#" + target).append(option5);
}

function appendCustomer(object){
	let tr = "<tr id=\"" + object.document + "\" class=\"show\" onclick=\"showModalCustomer('" + object.document + "')\">"
	let tdName = "<td> <label class=\"showModalCustomer\">" + object.name + "</label> </td>"
	let tdDocumento = "<td> <label style=\"cursor: pointer;\">" + object.document + "</label> </td>"
	let tdCorreo = ""
	let tdTelefono = ""
	if(object.contacts){
		object.contacts.forEach(function(contact) {
			if(contact.contactType == 1)
				tdCorreo = "<td> <a onclick=\"return false;\" href=\"mailto:" + contact.value + "\">empresa@testttt.com</a> </td>"
			if(contact.contactType == 5)
				tdTelefono = "<td>" + contact.value + "</td>"
		})
	}
	tr = tr + tdName + tdDocumento;
	if(tdCorreo != "")
		tr = tr + tdCorreo;
	else
		tr = tr + "<td> </td>";

	if(tdTelefono != "")
		tr = tr + tdTelefono;
	else
		tr = tr + "<td> </td>";
	tr = tr + "</tr>";
	$("#customersTableBody").append(tr);
	// console.log(tr);
}

function createNewContact(value, target, modal){
	console.log(value)
	var contacto = {
		"contactType": value - 0,
		"value": ""
	}
	console.log(contacto);
	var rowCount = $("#" + modal + " .row").length;
	appendRowContact(contacto, rowCount, modal)
	$("#" + target).empty();
	$("#" + target).toggleClass("hide");
}

function infoUpdated(doc, name, idButton){
	if(doc.length >= 1 && name.length >= 1)
		$('#' + idButton).attr('disabled', false)
	else 
		$('#' + idButton).attr('disabled', true)

}