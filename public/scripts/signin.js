$(document).ready(()=>{
	console.log("script sign in");
})


function signIn(){
	console.log("comunicar a ormen para el login");

	let rut = $("#inputRut").val();
	let correo = $("#inputUser").val();
	let contra = $("#inputPassword").val();

	sendAsyncPost("login", {rut:rut, correo:correo, contra:contra})
	.then(( response )=>{
		console.log("respuesta del login", response);
		if(response.result == 2){
			window.location.href = getSiteURL();
		}

		//else showReplyMessage(response.result, response.message, "Iniciar sesión", null);
	})
}


function loadRuts(){
	console.log("pidiendo ruts");
}