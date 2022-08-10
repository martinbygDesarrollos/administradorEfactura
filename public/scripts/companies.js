$(document).ready(()=>{
	console.log("script companies");
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