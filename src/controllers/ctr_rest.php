<?php
require_once '../src/class/send_petition.php';
require_once 'ctr_users.php';


class ctr_rest{

	public function login($rut, $user, $password){
		$petitionClass = new sendPetition();

		$petitionResponse = $petitionClass->login($rut, $user, $password);
		$petitionResponse = json_decode($petitionResponse);

		if ( $petitionResponse->resultado->codigo == 200 ){
			$petitionResponse->result = 2;
		}else {
			$petitionResponse->result = 1;
			$petitionResponse->message = $petitionResponse->resultado->error;
		}

		return $petitionResponse;
	}



	public function getCompanies(){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->listResult = array();

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued'] , $_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->getCompanies($tokenRest);
			$petitionResponse = json_decode($petitionResponse);
			$response->result = 2;
			$response->listResult = $petitionResponse;
		}else return $token;

		return $response;
	}
}


?>