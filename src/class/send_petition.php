<?php

class sendPetition{

	public function status($rut, $token){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "status?rut=" . $rut, $token, null,);
	}
	
	public function ping(){
		$thisClass = new sendPetition();
		return $thisClass->prepareAndSendCurl("GET", "ping", null, null);
	}

	public function login($rut, $user, $password){
		$thisClass = new sendPetition();

		$data = array(
			"credenciales" => array(
				"user" => $user,
				"clave" => $password
			)
		);

		return $thisClass->prepareAndSendCurl("POST", "login", null, $data);
	}

	public function getEmpresa($rut, $token){
		$thisClass = new sendPetition();

		return $thisClass->prepareAndSendCurl("GET", "company/" . $rut, $token, null);
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
		}else{
			return array("Accept: aplication/json", $token);
		}
	}
}