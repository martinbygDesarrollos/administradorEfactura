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



	public function changeCompanieData($rut, $codBranch, $data, $token){

		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("PATCH", "company/".$rut."/sucursal/".$codBranch, $token, $data);
	}



	public function sendsobre ($rut, $data, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("POST", "company/".$rut."/cfe/sendsobre", $token, $data);
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