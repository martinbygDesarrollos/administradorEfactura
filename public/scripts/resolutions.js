$(document).ready(()=>{

	$("#emitterDate").val(getCurrentDate());

})




$("#idFormLoadResolutions").submit((e)=>{
	e.preventDefault();

	if($("#statusCompanieSelect").val() == "3"){
        mostrarLoader(true)
		console.log("Esta en modo pendiente de aprobacion");
        // Use an async immediately invoked function expression (IIFE)
        (async () => {
            try {
                // Wait for first state change
                const firstStateChange = await cambiarEstado(4);
                // const firstStateChange = await cambiarEstadoTEST(1);
                if (firstStateChange) {
                    // If first state change is successful, proceed with second
                    const secondStateChange = await cambiarEstado(5);
                    // const secondStateChange = await cambiarEstadoTEST(2);
                    if (!secondStateChange) {
                        console.error("Failed to change state to 5");
                        mostrarLoader(false)
                        return; // Stop further execution if second state change fails
                    }
                } else {
                    mostrarLoader(false)
                    console.error("Failed to change state to 4");
                    return; // Stop further execution if first state change fails
                }

                // Proceed with form submission if both state changes are successful
                const formData = new FormData(document.getElementById("idFormLoadResolutions"));
                const response = await sendAsyncPostForm("loadResolutions", formData);
                // const response = await sendAsyncPostFormTEST("https://6756e08bc0a427baf94aca59.mockapi.io/test/call1", formData);
                
                console.log(response);
                showMessage(response.result, response.message);
                
				// const formData = new FormData(document.getElementById("idFormLoadResolutions"));
				// sendAsyncPostForm("loadResolutions", formData)
				// .then((response)=>{
				// 	console.log(response);
				// 	$("#modalLoadResolutions").modal("hide");
				// 	showMessage(response.result, response.message);

				// })
				// console.log("submit de las resoluciones");
                mostrarLoader(false)

            } catch (error) {
                console.error("Error in form submission process:", error);
                showMessage(0, "An error occurred during processing");
                mostrarLoader(false)
            }
        })();
	} else {
        mostrarLoader(true)
		const formData = new FormData(document.getElementById("idFormLoadResolutions"));
		sendAsyncPostForm("loadResolutions", formData)
		.then((response)=>{
			console.log(response);
			// $("#modalLoadResolutions").modal("hide");
			showMessage(response.result, response.message);
            mostrarLoader(false)
		})
		console.log("submit de las resoluciones");
	}

})


function changeDataFormResolutions(){

	$("#buttonFormLoadResolutions").removeAttr("disabled");

}

function cambiarEstado(newValue) {
    return sendAsyncPost("changeStatusCompanie", {newStatus: newValue })
        .then((response) => {
            return response.result == 2;
        })
        .catch((error) => {
            console.error("Error changing state:", error);
            return false;
        });
}

// function cambiarEstadoTEST(newValue) {
//     return sendAsyncPostTEST("https://6756e08bc0a427baf94aca59.mockapi.io/test/call" + newValue , {newStatus: newValue })
//         .then((response) => {
//             return response.newStatus == newValue;
//         })
//         .catch((error) => {
//             console.error("Error changing state:", error);
//             return false;
//         });
// }