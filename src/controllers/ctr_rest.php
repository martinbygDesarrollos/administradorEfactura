<?php
require_once '../src/class/send_petition.php';
require_once 'ctr_users.php';


class ctr_rest{

	public function login($user, $password){
		$petitionClass = new sendPetition();

		$petitionResponse = $petitionClass->login($user, $password);
		$petitionResponse = json_decode($petitionResponse);

		if ( $petitionResponse->resultado->codigo == 200 ){
			$petitionResponse->result = 2;
			$petitionResponse->message = "OK";
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

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->getCompanies($tokenRest);
			$petitionResponse = json_decode($petitionResponse);
			$response->result = 2;
			$response->listResult = $petitionResponse;
		}else return $token;

		return $response;
	}



	public function getCompanyData( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->getCompanyData($tokenRest, $rut);
			$petitionResponse = json_decode($petitionResponse);
			$response->result = 2;
			$response->objectResult = $petitionResponse;


			$_SESSION['company'] = $petitionResponse;
		}else return $token;


		return $response;

	}


	public function changeCompanieData($data){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);

		//falta cambiar el logo y cod de suc
		if (!isset($data['codDgi']) || $data['codDgi'] == "" ){
			$response->message = coddgi_no_data;
			return $response;
		}

		if ( $token->result == 2 ){

			$newData = new stdClass();
			$newData->nombreComercial = $data['nombre'];
			$newData->direccion = $data['direccion'];
			$newData->departamento = $data['departamento'];
			$newData->localidad = $data['localidad'];
			$newData->telephone1 = $data['telefono'];
			$newData->telephone2 = $data['telefono2'];
			$newData->email = $data['correo'];
			$newData->website = $data['sitio'];

			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->changeCompanieData($data['rut'], $data['codDgi'], $newData, $tokenRest);
			$petitionResponse = json_decode($petitionResponse);
			$response->result = 2;
			$response->objectResult = $petitionResponse;
		}else return $token;

		return $response;

	}

	public function changeCompanieColors($data){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);

		if (!isset($data['codDgi']) || $data['codDgi'] == "" ){
			$response->message = coddgi_no_data;
			return $response;
		}

		if ( $token->result == 2 ){

			$newData = new stdClass();
			$newData->colorPrimary = $data['color1'];
			$newData->colorSecondary = $data['color2'];


			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->changeCompanieData($data['rut'], $data['codDgi'], $newData, $tokenRest);
			$petitionResponse = json_decode($petitionResponse);
			$response->result = 2;
			$response->objectResult = $petitionResponse;
		}else return $token;

		return $response;

	}


	public function sendsobre( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "error";

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->sendsobre($rut, $data, $tokenRest);
			$petitionResponse = json_decode($petitionResponse);

			if ( $petitionResponse->resultado->codigo == 200 ){
				$response->result = 2;
				$response->message = "ok";
			}else{
				$response->result = 1;
				$response->message = $petitionResponse->resultado->error;
			}
		}else return $token;

		return $response;

	}




	public function statusPendPostulacion( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->aprobarPostulacion($rut, $tokenRest);
			$petitionResponse = json_decode($petitionResponse);

			if ( $petitionResponse->resultado->codigo == 200 ){
				$response->result = 2;
				$response->message = $petitionResponse->resultado->error;
			}else{
				$response->result = 1;
				$response->message = $petitionResponse->resultado->error;
			}
		}else return $token;

		return $response;

	}




	public function statusPendCertificacion( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->aprobarCertificacion($rut, $tokenRest);
			$petitionResponse = json_decode($petitionResponse);

			if ( $petitionResponse->resultado->codigo == 200 ){
				$response->result = 2;
				$response->message = $petitionResponse->resultado->error;
			}else{
				$response->result = 1;
				$response->message = $petitionResponse->resultado->error;
			}
		}else return $token;

		return $response;

	}






	public function loadResolutions( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->cargarResolucion($rut, $tokenRest, $data);
			$petitionResponse = json_decode($petitionResponse);

			if ( $petitionResponse->resultado->codigo == 200 ){
				$response->result = 2;
				$response->message = $petitionResponse->resultado->error;
			}else{
				$response->result = 1;
				$response->message = $petitionResponse->resultado->error;
			}
		}else return $token;

		return $response;

	}


	public function loadImage( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->cargarLogo($rut, $tokenRest, $data);
			$petitionResponse = json_decode($petitionResponse);

			if ( $petitionResponse->resultado->codigo == 200 ){
				$response->result = 2;
				$response->message = $petitionResponse->resultado->error;
			}else{
				$response->result = 1;
				$response->message = $petitionResponse->resultado->error;
			}
		}else return $token;

		return $response;

	}


	public function importCfeEmitedXml( $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->importCfeEmitedXml($_SESSION['rutUserLogued'], $tokenRest, $data);
			if ( isset($petitionResponse) && $petitionResponse != "" ){
				$petitionResponse = json_decode($petitionResponse);
				if ( $petitionResponse->resultado->codigo == 200 ){

					//agregar ["idEnvio"]=>int(651110001890) en la respuesta
					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
					$response->resultadosImportacion = $petitionResponse->resultadosImportacion;

					foreach ($petitionResponse->resultadosImportacion as $result) {
						if ($result->ok == 0){
							$response->result = 1;
							$response->message .= "\n".$result->error;
						}
					}
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
					$response->resultadosImportacion = array();
				}
			}else{
				$response->result = 1;
				$response->message = "No se obtuvo respuesta desde EFactura.";
				$response->resultadosImportacion = array();

			}
		}else return $token;

		return $response;

	}



	public function importCfeReceiptXml( $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->importCfeReceiptXml($_SESSION['rutUserLogued'], $tokenRest, $data);
			if ( isset($petitionResponse) && $petitionResponse != "" ){
				$petitionResponse = json_decode($petitionResponse);
				if ( $petitionResponse->resultado->codigo == 200 ){

					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
					$response->resultadosImportacion = $petitionResponse->resultadosImportacion;

					foreach ($petitionResponse->resultadosImportacion as $result) {
						if ($result->ok == 0){
							$response->result = 1;
							$response->message .= "\n".$result->error;
						}
					}
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else{
				$response->result = 1;
				$response->message = "No se obtuvo respuesta desde EFactura.";
			}
		}else return $token;

		return $response;

	}



	public function getRepresentacionImpresa($rut){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->getRepresentacionImpresa($_SESSION['rutUserLogued'], $tokenRest);
			$result = json_decode($petitionResponse);
			if ($result->resultado->codigo === 200 ){
				$response->result = 2;
				$response->message = "ok";
				$response->objectResult = $result;
				return $response;
			}else{
				error_log("Error al procesar getRepresentacionImpresa " . $rut . ": " .$result->resultado->error);
				$response->result = 1;
				$response->message = "Error al consultar los datos.";
				return $response;
			}
		}
		return $response;
	}


	public function enabledDisabledCompanie($value){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";

		$data = new \stdClass();
		$data->suspender = $value;

		$token = $usersController->getTokenUserLogued($_SESSION['rutUserLogued']);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			$petitionResponse = $petitionClass->enabledDisabledCompanie($_SESSION['rutUserLogued'], $data, $tokenRest);
			$result = json_decode($petitionResponse);
			if ($result->resultado->codigo === 200 ){
				$response->result = 2;
				$response->message = "ok";
				return $response;
			}else{
				error_log("Error al suspender o activar una empresa, función enabledDisabledCompanie " . $_SESSION['rutUserLogued'] . ", valor enviado $value, error: " .$result->resultado->error);
				$response->result = 1;
				$response->message = $result->resultado->error;
				return $response;
			}
		}
		return $response;

	}

}


?>