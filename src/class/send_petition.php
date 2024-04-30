<?php

class sendPetition{
	
	public function ping(){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "ping", null, null);
	}

	public function login($user, $password){
		$thisClass = new sendPetition();

		$data = array(
			"credenciales" => array(
				"user" => $user,
				"clave" => $password
			)
		);

		return $thisClass->prepareAndSendCurl("POST", "login", null, $data);
	}

	public function getCompanies($token){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "companies", $token, null);
	}


	public function getCompanyData($token, $rut){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."?CustomFields=*&IncludeLogo=1", $token, null);
	}
	
	public function getListUser( $token, $rut){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "users/".$rut, $token, null);
	}

	public function getListCustomers( $token, $rut){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "customers/".$rut, $token, null);
	}

	public function getCustomer( $token, $rut, $document ){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "customers/" . $rut . "/".$document , $token, null);
	}

	public function saveCustomer( $token, $rut, $customer, $document ){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "customers/" . $rut . "/".$document , $token, $customer);
	}

	public function newCustomer( $token, $rut, $customer){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "customers/" . $rut , $token, $customer);
	}

	public function getUser( $token, $rut, $email){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "users/".$rut."/". $email, $token, null);
	}

	public function updateUser( $token, $rut, $data){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("PUT", "users/".$rut."/". $data->email, $token, $data);
	}

	public function newUser( $token, $rut, $data){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("POST", "users/".$rut, $token, $data);
	}
	
	public function updateUserPassword( $token, $rut, $data, $email){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("PUT", "users/".$rut."/".$email."/password", $token, $data);
	}


	public function changeCompanieData($rut, $codBranch, $data, $token){

		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PATCH", "company/".$rut."/sucursal/".$codBranch, $token, $data);
	}



	public function sendsobre ($rut, $data, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/sendsobre", $token, $data);
	}



	//se cambia de estado pasa de PENDIENTE_POSTULACIÓN a PENDIENTE_CERTIFICACIÓN
	public function aprobarPostulacion ($rut, $token){
		$thisClass = new sendPetition();
		$data = new \stdClass();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/approve", $token, $data);
	}



	//cambio de estado de PENDIENTE_CERTIFICACIÓN a PENDIENTE_RESOLUCIÓN
	public function aprobarCertificacion ($rut, $token){
		$thisClass = new sendPetition();
		$data = new \stdClass();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/certificate", $token, $data);
	}



	//cambia el estado de la empresa a suspendida
	public function suspenderEmpresa ($rut, $data, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/suspend", $token, $data);
	}


	//cambia el estado de la empresa a EN_ESPERA_PARA_COMENZAR y cargar datos a las resoluciones
	public function cargarResolucion ($rut, $token, $data){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/loadresolution", $token, $data);
	}



	public function cargarLogo ($rut, $token, $data){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/logo", $token, $data);
	}


	public function importCfeEmitedXml($rut, $token, $data){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/import/xml", $token, $data);
	}

	public function importCfeReceiptXml($rut, $token, $data){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/import/xml?cfesrecibidos=1", $token, $data);
	}


	public function getRepresentacionImpresa( $rut, $token ){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "company/".$rut."/representacionimpresa", $token, null);
	}

	public function updateRepresentacionImpresa($rut, $tokenRest, $data){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/representacionimpresa", $tokenRest, $data);
	}


	public function enabledDisabledCompanie($rut, $data, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/suspend", $token, $data);
	}


	public function deleteCompanieBranch($rut, $branch, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("DELETE", "company/".$rut."/sucursal/".$branch, $token, null);
	}

	public function setPrincipalCompanieBranch($rut, $data, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/sucursalprincipal", $token, $data);
	}


	public function saveInfoAdicional($token, $rut, $data){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PUT", "company/".$rut."/infoAdicional", $token, $data);
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

	public function prepareAndSendCurl($typeMethod, $method, $token, $data){
		$thisClass = new sendPetition();
		$curlPetition = curl_init(URL_REST . $method);
		curl_setopt($curlPetition, CURLOPT_URL, URL_REST . $method);


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