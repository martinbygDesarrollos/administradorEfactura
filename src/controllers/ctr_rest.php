<?php
require_once '../src/class/send_petition.php';
require_once 'ctr_users.php';


class ctr_rest{

	public function login($user, $password, $entorno){
		$petitionClass = new sendPetition();

		$petitionResponse = $petitionClass->login($user, $password, $entorno);
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



	public function getCompanies($email, $entorno){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->listResult = array();

		$token = $usersController->getTokenUserLogued($email);
		if ( $token->result == 2 ){
			$tokenRest = $token->objectResult->tokenRest;
			// error_log($entorno);
			$petitionResponse = $petitionClass->getCompanies($tokenRest, $entorno);
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

		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				// var_dump($tokenRest);
				// error_log($_SESSION['sistemSession']->entorno);
				$petitionResponse = $petitionClass->getCompanyData($tokenRest, $rut, $_SESSION['sistemSession']->entorno);
				// var_dump($petitionResponse);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
				// $_SESSION['company'] = $petitionResponse;
			}else return $token;
		}

		return $response;

	}
	public function getCountEmitidos( $rut, $FROM, $TO, $CFE ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getCountEmitidos($tokenRest, $rut, $FROM, $TO, $CFE, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
				// $_SESSION['company'] = $petitionResponse;
			}else return $token;
		}

		return $response;

	}

	public function loadEmisores($ruc){

		$petitionClass = new sendPetition();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$petitionResponse = $petitionClass->getEmisorData($ruc);
		$petitionResponse = json_decode($petitionResponse);
		if($petitionResponse){
			$response->result = 2;
			$response->objectResult = $petitionResponse;
		} else {
			$response->result = 1;
		}
		return $response;
	}


	public function changeCompanieData($data){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);

			//falta cambiar el logo y cod de suc
			if (!isset($data['codDgi']) || $data['codDgi'] == "" ){
				$response->message = coddgi_no_data;
				return $response;
			}

			if ( $token->result == 2 ){

				/*$newData = new stdClass();
				$newData->nombreComercial = $data['nombre'];
				$newData->direccion = $data['direccion'];
				$newData->departamento = $data['departamento'];
				$newData->localidad = $data['localidad'];
				$newData->telephone1 = $data['telefono'];
				$newData->telephone2 = $data['telefono2'];
				$newData->email = $data['correo'];
				$newData->website = $data['sitio'];
				if(isset($data['isTemplate'])){
					$newData->isTemplate = $data['isTemplate'];
				}*/

				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->changeCompanieData($data['rut'], $data['codDgi'], $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}

		return $response;

	}

	public function changeCompanieColors($data){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);

			if (!isset($data['codDgi']) || $data['codDgi'] == "" ){
				$response->message = coddgi_no_data;
				return $response;
			}

			if ( $token->result == 2 ){

				$newData = new stdClass();
				$newData->colorPrimary = $data['color1'];
				$newData->colorSecondary = $data['color2'];


				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->changeCompanieData($data['rut'], $data['codDgi'], $newData, $tokenRest, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;

	}


	public function sendsobre( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "error";
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->sendsobre($rut, $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);

				if ( $petitionResponse->resultado->codigo == 200 ){
					$response->result = 2;
					$response->message = "ok";
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else return $token;
		}
		return $response;

	}




	public function statusPendPostulacion( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->aprobarPostulacion($rut, $tokenRest, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);

				if ( $petitionResponse->resultado->codigo == 200 ){
					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else return $token;
		}
		return $response;

	}




	public function statusPendCertificacion( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->aprobarCertificacion($rut, $tokenRest, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);

				if ( $petitionResponse->resultado->codigo == 200 ){
					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else return $token;
		}
		return $response;

	}






	public function loadResolutions( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->cargarResolucion($rut, $tokenRest, $data, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);

				if ( $petitionResponse->resultado->codigo == 200 ){
					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else return $token;
		}
		return $response;

	}


	public function loadImage( $rut, $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->cargarLogo($rut, $tokenRest, $data, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);

				if ( $petitionResponse->resultado->codigo == 200 ){
					$response->result = 2;
					$response->message = $petitionResponse->resultado->error;
				}else{
					$response->result = 1;
					$response->message = $petitionResponse->resultado->error;
				}
			}else return $token;
		}
		return $response;

	}


	public function importCfeEmitedXml( $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->importCfeEmitedXml($responseCurrentSession->currentSession->rutUserLogued, $tokenRest, $data, $_SESSION['sistemSession']->entorno);
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
		}
		return $response;

	}



	public function importCfeReceiptXml( $data ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->importCfeReceiptXml($responseCurrentSession->currentSession->rutUserLogued, $tokenRest, $data, $_SESSION['sistemSession']->entorno);
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
		}
		return $response;

	}



	public function getRepresentacionImpresa($rut){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";

		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getRepresentacionImpresa($rut, $tokenRest, $_SESSION['sistemSession']->entorno);
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
		}
		return $response;
	}

	public function updateRepresentacionImpresa($rut, $data){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->updateRepresentacionImpresa($rut, $tokenRest, $data, $_SESSION['sistemSession']->entorno);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "ok";
					$response->objectResult = $result;
					return $response;
				}else{
					error_log("Error al procesar updateRepresentacionImpresa " . $rut . ": " .$result->resultado->error);
					$response->result = 1;
					$response->message = "Error al modificar los datos: ".$result->resultado->error;
					return $response;
				}
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
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->enabledDisabledCompanie($responseCurrentSession->currentSession->rutUserLogued, $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "ok";
					return $response;
				}else{
					error_log("Error al suspender o activar una empresa, función enabledDisabledCompanie " . $responseCurrentSession->currentSession->rutUserLogued . ", valor enviado $value, error: " .$result->resultado->error);
					$response->result = 1;
					$response->message = $result->resultado->error;
					return $response;
				}
			}
		}
		return $response;

	}

	public function enableCompany($rut){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";

		$data = new \stdClass();
		$data->enable = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->enableCompanie($rut, $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "ok";
					return $response;
				}else{
					error_log("Error al habilitar la empresa, función enableCompany " . $responseCurrentSession->currentSession->rutUserLogued . ", error: " .$result->resultado->error);
					$response->result = 1;
					$response->message = $result->resultado->error;
					return $response;
				}
			}
		}
		return $response;
	}

	public function disableCompany($rut){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->message = "No se pudo obtener respuesta.";

		$data = new \stdClass();
		$data->disable = 1;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->disableCompanie($rut, $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "ok";
					return $response;
				}else{
					error_log("Error al deshabilitar la empresa, función disableCompany " . $responseCurrentSession->currentSession->rutUserLogued . ", error: " .$result->resultado->error);
					$response->result = 1;
					$response->message = $result->resultado->error;
					return $response;
				}
			}
		}
		return $response;
	}


	public function deleteCompanieBranch($rut, $branch){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;

		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->deleteCompanieBranch($rut, $branch, $tokenRest, $_SESSION['sistemSession']->entorno);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "Sucursal eliminada con exito!";
					// return $response;
				}else{
					error_log("Error al eliminar una sucursal, función deleteCompanieBranch " . $responseCurrentSession->currentSession->rutUserLogued . ", valor enviado $branch, error: " .$result->resultado->error);
					$response->result = 1;
					$response->message = $result->resultado->error;
					// return $response;
				}
				// $response->objectResult = $petitionResponse;
			} else return $token;
		}
		return $response;

	}

	public function setPrincipalCompanieBranch($rut, $branch){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$data = new \stdClass();
		$data->sucursalPrincipal = $branch;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->setPrincipalCompanieBranch($rut, $data, $tokenRest, $_SESSION['sistemSession']->entorno);
				// $petitionResponse = $petitionClass->enabledDisabledCompanie($_SESSION['rutUserLogued'], $data, $tokenRest);
				$result = json_decode($petitionResponse);
				if ($result->resultado->codigo === 200 ){
					$response->result = 2;
					$response->message = "Sucursal cambiada a principal con exito!";
				}else{
					error_log("Error al cambiar sucursal a principal, función setPrincipalCompanieBranch " . $_SESSION['rutUserLogued'] . ", valor enviado $data, error: " .$result->resultado->error);
					$response->result = 1;
					$response->message = $result->resultado->error;
				}
			}
		}
		return $response;

	}


	public function loadListUser( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getListUser( $tokenRest, $rut, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function loadListCustomers( $rut ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getListCustomers( $tokenRest, $rut, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function loadCustomer($rut, $document){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getCustomer( $tokenRest, $rut, $document , $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}	
		return $response;
	}

	public function saveCustomer($rut, $customer, $document){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->saveCustomer( $tokenRest, $rut, $customer, $document , $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function newCustomer($rut, $customer){
		// var_dump($customer);

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->newCustomer( $tokenRest, $rut, $customer , $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				// var_dump($petitionResponse);
				// exit;
				// if($petitionResponse->resultado->codigo != 200){
				// 	$response->result = 0;
				// } else {
				$response->result = 2;
				// }
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		// var_dump($response);
		// exit;
		return $response;
	}

	public function getEmitidos( $rut, $lastId ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getEmitidos( $tokenRest, $rut, $lastId, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult->emitidos = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function getRecibidos( $rut, $lastId ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getRecibidos( $tokenRest, $rut, $lastId, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult->recibidos = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function getCFE($rut, $RUTEmisor, $tipoCFE, $serieCFE, $numeroCFE){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				// $petitionResponse = $petitionClass->getCFE($tokenRest, $rut, $tipoCFE, $serieCFE, $numeroCFE);
				$petitionResponse = $petitionClass->consultarCFE($tokenRest, $rut, $tipoCFE, $serieCFE, $RUTEmisor, $numeroCFE, 1, "text/html;template=A5Vertical", $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function loadUser( $rut, $email ){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->getUser( $tokenRest, $rut, $email, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				$response->result = 2;
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}

	public function updateUser($rut, $email, $name, $active, $cellphone, $scopes){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$data = new \stdClass();
		$data->email = $email;
		$data->name = $name;
		$data->active = $active;
		$data->cellphone = $cellphone;
		$data->scopes = $scopes;

		// var_dump($data);
		// exit;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->updateUser($tokenRest, $rut, $data, $_SESSION['sistemSession']->entorno);
				// $petitionResponse = $petitionClass->updateUser($tokenRest, $rut, $email, $name, $active, $cellphone, $scopes);
				// $petitionResponse = $petitionClass->getUser( $tokenRest, $rut, $email);
				$petitionResponse = json_decode($petitionResponse);
				// var_dump($petitionResponse);
				// exit;
				if(isset($petitionResponse->resultado->error)) {// Hubo algun error
					$response->result = 0;
					$response->message = $petitionResponse->resultado->error;
				} else {
					$response->result = 2;
					$response->message = "Usuario actualizado con exito!";
				}
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}
	
	public function updateUserPassword($rut, $email, $pwd){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$data = new \stdClass();
		$data->newPassword = $pwd;
		$data->requireChange = false;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->updateUserPassword($tokenRest, $rut, $data, $email, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				if($petitionResponse->resultado->error != "OK") {// Hubo algun error
					$response->result = 0;
					$response->message = $petitionResponse->resultado->error;
				} else {
					$response->result = 2;
					$response->message = "Contraseña actualizada con exito!";
				}
				$response->objectResult = $petitionResponse;
			}else return $token;
		}
		return $response;
	}
	
	public function newUser($rut, $email, $name, $cellphone, $scopes){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$data = new \stdClass();
		$data->email = $email;
		$data->name = $name;
		$data->active = true;
		$data->cellphone = $cellphone;
		$data->scopes = $scopes;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->newUser($tokenRest, $rut, $data, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				// var_dump($petitionResponse);
				// exit;
				if($petitionResponse->resultado->error != 'OK') {// Hubo algun error
					$response->result = 0;
					$response->message = $petitionResponse->resultado->error;
				} else {
					$response->result = 2;
					$response->message = "Usuario nuevo agregado con exito!";
				}
				$response->objectResult = $petitionResponse;
			} else return $token;
		}
		return $response;
	}


	public function saveInfoAdicional($value, $rut){

		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();

		$response->result = 1;
		$response->objectResult = new \stdClass();

		$data = new \stdClass();
		$data->infoAdicional = $value;
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$petitionResponse = $petitionClass->saveInfoAdicional($tokenRest, $rut, $data, $_SESSION['sistemSession']->entorno);
				$petitionResponse = json_decode($petitionResponse);
				if($petitionResponse->resultado->error != 'OK') {// Hubo algun error
					$response->result = 0;
					$response->message = $petitionResponse->resultado->error;
				} else {
					$response->result = 2;
					$response->message = "Informacion adicional agregada con éxito!";
				}
				$response->objectResult = $petitionResponse;
			} else return $token;
		}
		return $response;
	}

	public function obtenerCotizacion($dateFrom, $dateTo, $typeCoin){
		$petitionClass = new sendPetition();
		$usersController = new ctr_users();
		$response = new \stdClass();
		$responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
			$token = $usersController->getTokenUserLogued($responseCurrentSession->currentSession->email);
			if ( $token->result == 2 ){
				$tokenRest = $token->objectResult->tokenRest;
				$responseRest = json_decode($petitionClass->obtenerCotizacion($dateFrom, $dateTo, $typeCoin, $tokenRest, $_SESSION['sistemSession']->entorno));
				if(isset($responseRest->resultado)){
					if($responseRest->resultado->codigo == 200){
						$response->result = 2;
						$response->currentQuote = $responseRest->currencies[0]->tcv;
					}else{
						$response->result = 0;
						$response->message = "Ocurrió un error, REST no retorno una cotización valida.";
					}
				}else{
					$response->result = 0;
					$response->message = "Ocurrió un error y REST no retorno un resultado.";
				}
			} else return $token;
		}
		return $response;
	}

}


?>