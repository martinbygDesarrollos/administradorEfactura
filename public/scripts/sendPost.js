function sendAsyncPost(nombreFuncion, parametros){
	return new Promise( function(resolve, reject){
		$.ajax({
			async: true,
			url: getSiteURL() + nombreFuncion,
			type: "POST",
			data: parametros,
			success: function (response) {
				response = response.trim();
				var response = jQuery.parseJSON(response);
				resolve(response);
			},
			error: function (response) {
				result = "error"
			},
		});
	});
}



function sendAsyncPostForm(nombreFuncion, formData){
	return new Promise( function(resolve, reject){
		$.ajax({
			async: true,
			url: getSiteURL() + nombreFuncion,
			type: "POST",
			data: formData,
			success: function (response) {
				response = response.trim();
				var response = jQuery.parseJSON(response);
				resolve(response);
			},
			error: function (response) {
				reject(response.status, response.statusText);
			},
			cache: false,
	        contentType: false,
	        processData: false
		});
	});
}