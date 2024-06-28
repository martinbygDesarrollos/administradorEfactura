$(document).ready(()=>{
	// console.log("script sign in");
})


function signIn(){
	document.getElementById("buttonConfirm").disabled = true;
	let correo = $("#inputUser").val();
	let contra = $("#inputPassword").val();
	// console.log(correo)
	// console.log(contra)
	sendAsyncPost("login", {correo:correo, contra:contra, force:false})
	.then(( response )=>{
		// console.log(response.result);
		// return;
		if(response.result == 2){
			document.getElementById("buttonConfirm").disabled = false;
			window.location.href = getSiteURL();
		} else if ( response.result == 0 ){
			document.getElementById("buttonConfirm").disabled = false;
			window.location.href = getSiteURL() + "cerrar-session";
		} else {
			document.getElementById("buttonConfirm").disabled = false;
			if(response.activa)
				showMessageWithAction(response.result, response.message, correo, contra);
			else
				showMessage(response.result, response.message);
		}
	})
}