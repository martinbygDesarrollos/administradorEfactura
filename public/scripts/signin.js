$(document).ready(()=>{
	console.log("script sign in");
})


function signIn(){
	document.getElementById("buttonConfirm").disabled = true;
	let correo = $("#inputUser").val();
	let contra = $("#inputPassword").val();

	sendAsyncPost("login", {correo:correo, contra:contra})
	.then(( response )=>{
		if(response.result == 2){
			document.getElementById("buttonConfirm").disabled = false;
			window.location.href = getSiteURL();
		}else if ( response.result == 0 ){
			document.getElementById("buttonConfirm").disabled = false;
			window.location.href = getSiteURL() + "cerrar-session";
		}else{
			document.getElementById("buttonConfirm").disabled = false;
			showMessage(response.result, response.message);
		}
	})
}