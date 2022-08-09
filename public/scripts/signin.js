$(document).ready(()=>{
	console.log("script sign in");
})


function signIn(){
	console.log("comunicar a ormen para el login");
	let correo = $("#inputUser").val();
	let contra = $("#inputPassword").val();

	sendAsyncPost("login", {correo:correo, contra:contra})
	.then(( response )=>{
		console.log("respuesta del login", response);
		if(response.result == 2){
			window.location.href = getSiteURL();
		}
	})
}