$(document).ready(()=>{

	$("#emitterDate").val(getCurrentDate());

})




$("#idFormLoadResolutions").submit((e)=>{
	e.preventDefault();


	const formData = new FormData(document.getElementById("idFormLoadResolutions"));
	sendAsyncPostForm("loadResolutions", formData)
	.then((response)=>{
		console.log(response);
		$("#modalLoadResolutions").modal("hide");
		showMessage(response.result, response.message);

	})
	console.log("submit de las resoluciones");
})


function changeDataFormResolutions(){

	$("#buttonFormLoadResolutions").removeAttr("disabled");

}