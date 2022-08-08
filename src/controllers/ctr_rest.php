<?php
require_once '../src/class/send_petition.php';

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

}


?>