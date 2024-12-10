<?php

class sendPetition{
	
	public function ping(){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "ping", null, null, $entorno);
	}

	public function login($user, $password, $entorno){
		$thisClass = new sendPetition();

		$data = array(
			"credenciales" => array(
				"user" => $user,
				"clave" => $password
			)
		);

		return $thisClass->prepareAndSendCurl("POST", "login", null, $data, $entorno);
	}

	public function getCompanies($token, $entorno){
		$thisClass = new sendPetition();
		
		return $thisClass->prepareAndSendCurl("GET", "companies", $token, null, $entorno);
	}
	
	public function getCountEmitidos($token, $rut, $FROM, $TO, $CFE, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "company/$rut/cfe/emitidos/count?From=".$FROM."&To=".$TO."&Type=$CFE", $token, null, $entorno);
	}


	public function getCompanyData($token, $rut, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."?CustomFields=*&IncludeLogo=1", $token, null, $entorno);
	}
	
	public function getListUser( $token, $rut, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "users/".$rut, $token, null, $entorno);
	}

	public function getListCustomers( $token, $rut, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "customers/".$rut, $token, null, $entorno);
	}

	public function getCustomer( $token, $rut, $document , $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "customers/" . $rut . "/".$document , $token, null, $entorno);
	}

	public function saveCustomer( $token, $rut, $customer, $document , $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "customers/" . $rut . "/".$document , $token, $customer, $entorno);
	}

	public function newCustomer( $token, $rut, $customer, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "customers/" . $rut , $token, $customer, $entorno);
	}

	public function getEmitidos( $token, $rut, $lastId, $entorno){
		if(!isset($lastId)) $lastId = null;
		$thisClass = new sendPetition();
		$lastId = $lastId ? "?LastId=$lastId" . "&" : "?";
		
		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."/cfe/emitidos" . $lastId . "PageSize=30", $token, null, $entorno);
	}

	public function getRecibidos( $token, $rut, $lastId, $entorno){
		if(!isset($lastId)) $lastId = null;
		$thisClass = new sendPetition();
		$lastId = $lastId ? "?LastId=$lastId" . "&" : "?";
		
		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."/cfe/received" . $lastId . "PageSize=30", $token, null, $entorno);
	}

	public function getCFE($token, $rut, $tipoCFE, $serieCFE, $numeroCFE, $entorno){
		$thisClass = new sendPetition();

		// return $thisClass->prepareAndSendCurl("GET", "company/" . $rut . "/cfe?seriecfe=" . $serieCFE . "&numerocfe=" . $numeroCFE . "&conrepresentacionimpresa=1&formatorepresentacionimpresa=application%2Fjson;template=a4&tipocfe=" . $tipoCFE, $token, null);
		return $thisClass->prepareAndSendCurl("GET", "company/" . $rut . "/cfe?ConRepresentacionImpresa=1&FormatoRepresentacionImpresa=application%2Fjson&NumeroCFE=" . $numeroCFE . "&SerieCFE=" . $serieCFE . "&TipoCFE=" . $tipoCFE, $token, null, $entorno);
	}

	public function consultarCFE($token, $rut, $tipoCFE, $serieCFE, $rutEmisor, $numeroCFE, $repImpresa, $formatImpresion, $entorno){
		$thisClass = new sendPetition();
		// $rutEmisor = null;
		// $repImpresa = 1;
		// $formatImpresion = "text/html;template=A5Vertical";
		if(is_null($rutEmisor) || $rutEmisor == "")
			$rutEmisor = "";
		else
			$rutEmisor = '&RUTEmisor=' . $rutEmisor;

		$url = "company/" . $rut . "/cfe?tipocfe=". $tipoCFE . "&seriecfe=" . $serieCFE . "&numerocfe=" . $numeroCFE . "&conrepresentacionimpresa=" . $repImpresa . "&formatorepresentacionimpresa=" . $formatImpresion . $rutEmisor;
		return $thisClass->prepareAndSendCurl("GET", $url, $token, null, $entorno);
	}

	public function getUser( $token, $rut, $email, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "users/".$rut."/". $email, $token, null, $entorno);
	}

	public function updateUser( $token, $rut, $data, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("PUT", "users/".$rut."/". $data->email, $token, $data, $entorno);
	}

	public function newUser( $token, $rut, $data, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("POST", "users/".$rut, $token, $data, $entorno);
	}
	
	public function updateUserPassword( $token, $rut, $data, $email, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("PUT", "users/".$rut."/".$email."/password", $token, $data, $entorno);
	}


	public function changeCompanieData($rut, $codBranch, $data, $token, $entorno){

		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PATCH", "company/".$rut."/sucursal/".$codBranch, $token, $data, $entorno);
	}



	public function sendsobre ($rut, $data, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/sendsobre", $token, $data, $entorno);
	}



	//se cambia de estado pasa de PENDIENTE_POSTULACIÓN a PENDIENTE_CERTIFICACIÓN
	public function aprobarPostulacion ($rut, $token, $entorno){
		$thisClass = new sendPetition();
		$data = new \stdClass();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/approve", $token, $data, $entorno);
	}



	//cambio de estado de PENDIENTE_CERTIFICACIÓN a PENDIENTE_RESOLUCIÓN
	public function aprobarCertificacion ($rut, $token, $entorno){
		$thisClass = new sendPetition();
		$data = new \stdClass();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/certificate", $token, $data, $entorno);
	}



	//cambia el estado de la empresa a suspendida
	public function suspenderEmpresa ($rut, $data, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/suspend", $token, $data, $entorno);
	}


	//cambia el estado de la empresa a EN_ESPERA_PARA_COMENZAR y cargar datos a las resoluciones
	public function cargarResolucion ($rut, $token, $data, $entorno){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/loadresolution", $token, $data, $entorno);
	}



	public function cargarLogo ($rut, $token, $data, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/logo", $token, $data, $entorno);
	}


	public function importCfeEmitedXml($rut, $token, $data, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/import/xml", $token, $data, $entorno);
	}

	public function importCfeReceiptXml($rut, $token, $data, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/import/xml?cfesrecibidos=1", $token, $data, $entorno);
	}


	public function getRepresentacionImpresa( $rut, $token , $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."/representacionimpresa", $token, null, $entorno);
	}

	public function updateRepresentacionImpresa($rut, $tokenRest, $data, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/representacionimpresa", $tokenRest, $data, $entorno);
	}


	public function enabledDisabledCompanie($rut, $data, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/suspend", $token, $data, $entorno);
	}


	public function deleteCompanieBranch($rut, $branch, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("DELETE", "company/".$rut."/sucursal/".$branch, $token, null, $entorno);
	}

	public function setPrincipalCompanieBranch($rut, $data, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/sucursalprincipal", $token, $data, $entorno);
	}


	public function saveInfoAdicional($token, $rut, $data, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/infoAdicional", $token, $data, $entorno);
	}

	public function getEmisorData($ruc){
		$urlMethod = 'https://ws.byg.uy/emisores/?RUC=' . $ruc;
		$curlPetition = curl_init();
		curl_setopt($curlPetition, CURLOPT_URL, $urlMethod);
		curl_setopt($curlPetition, CURLOPT_GET, true);
		curl_setopt($curlPetition, CURLOPT_RETURNTRANSFER, true);
		$responseCurl = curl_exec($curlPetition);
		curl_close($curlPetition);
		return $responseCurl;
	}

	public function obtenerCotizacion($dateFrom, $dateTo, $typeCoin, $token, $entorno){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", 'currency?Currency=' . $typeCoin . '&From=' . $dateFrom . '&To=' . $dateTo, $token, null, $entorno);
	}

	public function prepareAndSendCurl($typeMethod, $method, $token, $data, $entorno = null){
		$thisClass = new sendPetition();
		$curlPetition = null;
		$baseUrl = ($entorno === 'test') ? URL_REST_TEST : URL_REST_PROD;
		// echo "<br> $baseUrl <br>";
		$curlPetition = curl_init($baseUrl . $method);
    	curl_setopt($curlPetition, CURLOPT_URL, $baseUrl . $method);
		error_log("URL_REST USED: " . $baseUrl . " $method");
		// $curlPetition = curl_init(URL_REST . $method);
		// curl_setopt($curlPetition, CURLOPT_URL, URL_REST . $method);
		curl_setopt($curlPetition, CURLOPT_URL, $baseUrl . $method);


		if($typeMethod == "POST"){
			curl_setopt($curlPetition, CURLOPT_POST, true);
			curl_setopt($curlPetition, CURLOPT_POSTFIELDS, json_encode($data));
		}else if($typeMethod == "PUT"){
			curl_setopt($curlPetition, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($curlPetition, CURLOPT_POSTFIELDS, json_encode($data));
		}else if($typeMethod == "PATCH"){
			curl_setopt($curlPetition, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($curlPetition, CURLOPT_POSTFIELDS, json_encode($data));
		} else if ($typeMethod == "DELETE") {
			curl_setopt($curlPetition, CURLOPT_CUSTOMREQUEST, "DELETE");
		}

		curl_setopt($curlPetition, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlPetition, CURLOPT_HTTPHEADER, $thisClass->getHeader($typeMethod, $token));
		$responseCurl =  curl_exec($curlPetition);
		// var_dump($responseCurl);
		curl_close($curlPetition);
		return $responseCurl;
	}

	public function getHeader($typeMethod, $token){
		if(!is_null($token))
			$token = "Authorization: Bearer " . $token;

		if($typeMethod == "POST"){
			return array("Accept: aplication/json", $token, "Content-Type: application/json");
		}else if($typeMethod == "PUT"){
			return array("Accept: aplication/json", $token, "Content-Type: application/json");
		}
		else if($typeMethod == "PATCH"){
			return array("Accept: aplication/json", $token, "Content-Type: application/json");
		}else{
			return array("Accept: aplication/json", $token);
		}
	}
}