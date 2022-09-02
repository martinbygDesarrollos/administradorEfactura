function getSiteURL(){
	let url = window.location.href;
	if(url.includes("localhost") || url.includes("intranet.gargano"))
		return '/administradorEfactura/public/';
	else
		return '/';
}



function tableCfeType(type){

	switch (type) {
		case 101: return "e-Ticket";
		case 102: return "Nota de crédito de e-Ticket";
		case 103: return "Nota de débito de e-Ticket";
		case 111: return "e-Factura";
		case 112: return "Nota de crédito de e-Factura";
		case 113: return "Nota de débito de e-Factura";
		case 121: return "e-Factura Exportación";
		case 122: return "Nota de crédito de e-Factura Exportación";
		case 123: return "Nota de débito de e-Factura Exportación";
		case 124: return "e-Remito de Exportación";
		case 131: return "e-Ticket Venta por Cuenta Ajena";
		case 132: return "Nota de crédito de e-Ticket Venta por Cuenta Ajena";
		case 133: return "Nota de débito de e-Ticket Venta por Cuenta Ajena";
		case 141: return "e-Factura Venta por Cuenta Ajena";
		case 142: return "Nota de crédito de e-Factura Venta por Cuenta Ajena";
		case 143: return "Nota de débito de e-Factura Venta por Cuenta Ajena";
		case 151: return "e-Boleta de entrada";
		case 152: return "Nota de crédito de e-Boleta de entrada";
		case 153: return "Nota de débito de e-Boleta de entrada";
		case 181: return "e-Remito";
		case 182: return "e-Resguardo";
		default: return "";
	}


}


function dateTypeHtml( date ){ // entrada 2023-02-04T23:24:21.142-03:00 salida 04/02/2023
	newDate = date.substr(8, 2)+"/"+date.substr(5,2)+"/"+date.substr(2,2);
	return newDate;
}