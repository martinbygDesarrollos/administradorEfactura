$(document).ready(()=>{
	console.log("script companies");
})


function selectCompanie( companieRut ){
	console.log("cambiar empresa a ", companieRut);
	sendAsyncPost("companies", {rut:companieRut})
	.then((response)=>{
		console.log(response);
	})
}