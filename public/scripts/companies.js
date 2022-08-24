$(document).ready(()=>{
	console.log("script companies");

	$('.containerTable').css('height', '60vh');

	$('.containerTable').on('scroll', function() {

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
		console.log(response);
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
	let row = '<tr><td onclick="selectCompanie('+obj.rut+', '+obj.razonSocial+')" ><a href="'+getSiteURL()+obj.rut+'">'+obj.razonSocial+'</a><br>'+obj.rut+'</td>';
	row += '<td onclick="selectCompanie('+obj.rut+', '+obj.razonSocial+')" >'+obj.estadoDescripcion+'</td>';
	row += '<td onclick="selectCompanie('+obj.rut+', '+obj.razonSocial+')" >comprob vencimiento</td>';
	row += '<td>select con caes</td></tr>';

	return row;
}