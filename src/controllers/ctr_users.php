<?php

require_once '../src/class/users.php';
require_once 'ctr_rest.php';
require_once 'ctr_companies.php';


class ctr_users{

	// public function login( $mail, $password ){
	// 	$time_start = microtime(true);
	// 	$restController = new ctr_rest();
	// 	$usersClass = new users();
	// 	$response = new \stdClass();
	// 	$response->result = 1;

	// 	$responseLogin = $restController->login($mail, $password); //hago el login contra ormen
	// 	if ( $responseLogin->result == 2 ){ //si los tres datos que recibo son correctos entonces los guardo local
	// 		$token = $responseLogin->token;
	// 		$responseGetUser = $usersClass->getUserByMail($mail);
	// 		if ( $responseGetUser->result == 2 ){
	// 			//setear token
	// 			$response = $usersClass->updateToken($mail, $token);
	// 		}
	// 		elseif ( $responseGetUser->result == 1 ){
	// 			$response = $usersClass->insertUser($mail, $token);
	// 		}else return $responseGetUser;
	// 	}else return $responseLogin;


	// 	if ( $response->result == 2 ){
	// 		$_SESSION['mailUserLogued'] = $mail;
	// 		$_SESSION['permissionsUserLogued'] = $usersClass->getPermissionsByMail($mail);
	// 	}else{
	// 		$_SESSION['mailUserLogued'] = null;
	// 		$_SESSION['permissionsUserLogued'] = null;
	// 	}



	// 	$time_end = microtime(true);
	// 	$time = sprintf("%01.3f",$time_end - $time_start);
	// 	error_log("login tiempo: ".$time);
	// 	return $response;
	// }

	public function login( $mail, $password, $entorno, $force = false){
		$restController = new ctr_rest();
		$companieController = new ctr_companies();
		$usersClass = new users();
		$response = new \stdClass();
		$response->result = 1;
		$responseCompanies = null;
		
		// var_dump($mail, $password, $entorno, $force);
		$responseLogin = $restController->login($mail, $password, $entorno); //hago el login contra 
		if ( $responseLogin->result == 2 ){ //si los tres datos que recibo son correctos entonces los guardo local
			// echo "A";
			// $responseCompanies = $restController->getCompanies($mail);
			// var_dump($responseCompanies->listResult);
			// exit;
			$token = $responseLogin->token;
			$responseGetUser = $usersClass->getUserByMail($mail);
			if ( $responseGetUser->result == 2 ){// Usuario encontrado
				// echo "B";
				$responseUpdateToken = $usersClass->updateToken($mail, $token); // actualizo el token de ormen
				if ( $responseUpdateToken->result == 2 ){
					$responseCompanies = $companieController->getCompanies($mail, $entorno);
					// echo "C";
					if(isset($responseGetUser->objectResult->tokenLocal)){ // Usuario ya esta logeado en otra parte
						// echo "D";
						if($force) { // No se forza la sesion
							// echo "E";
							return $usersClass->setNewTokenAndSession($mail, $responseCompanies->listResult, $entorno);
							// $responseUpdatedToken = $usersClass->updateTokenLocal($mail, $newTokenLocal);
							// $response = $usersClass->updateLastActivity($mail); // CAMBIAR ESTO 
						} else { // forzar iniciar sesion nueva
							// echo "F";
							$fechaStr = $responseGetUser->objectResult->tokenFecha;
							// $date = new DateTime();
							// $date->modify('-1 day');
							// $yesterday = $date->format('Ymd');
							// var_dump($yesterday);
							$response->result = 1;
							// if (substr($fechaStr, 0, 8) == date('Ymd')) // HOY
							// 	$lastActivityDate = 'hoy a las ' . substr($fechaStr, 8, 2) . ":" . substr($fechaStr, 10, 2) . ":" . substr($fechaStr, 12, 2);
							// else if (substr($fechaStr, 0, 8) == $yesterday)
							// 	$lastActivityDate = 'ayer a las ' . substr($fechaStr, 8, 2) . ":" . substr($fechaStr, 10, 2) . ":" . substr($fechaStr, 12, 2);
							// else
							// 	$lastActivityDate = $this->setFormatBarDateTime($fechaStr);
							$response->message = "La sesión de $mail se encuentra activa (última actividad el " . substr($fechaStr, 6, 2) . "/" .  substr($fechaStr, 4, 2) . "/" . substr($fechaStr, 0, 4) . " a las " . substr($fechaStr, 8, 2) . ":" . substr($fechaStr, 10, 2) . ":" . substr($fechaStr, 12, 2) . ")";
							$response->activa = true;
							return $response;
						}
					} else {
						return $usersClass->setNewTokenAndSession($mail, $responseCompanies->listResult, $entorno);
						// $response = $usersClass->updateTokenLocal($mail, $newTokenLocal);
						// if($response->result == 2)
						// 	$response = $usersClass->updateLastActivity($mail); // CAMBIAR ESTO 
					}
				} else {
					return $responseUpdateToken;
				} 
			} else {
				return $responseGetUser;
			} 
		} else {
			return $responseLogin;
		}	
		// if ( $response->result == 2 ){
		// 	$_SESSION['mailUserLogued'] = $mail;
		// 	$_SESSION['permissionsUserLogued'] = $usersClass->getPermissionsByMail($mail);
		// 	$_SESSION['tokenLocal'] = $newTokenLocal;
		// }else{
		// 	$_SESSION['mailUserLogued'] = null;
		// 	$_SESSION['permissionsUserLogued'] = null;
		// 	// $_SESSION['tokenLocal'] = null;
		// }
		return $response;
	}

	public function logout(){
		$usersClass = new users();
		$response = new \stdClass();
		$response->result = 1;
		if(isset($_SESSION['sistemSession'])){
			$currentSession = $_SESSION['sistemSession'];
			$response = $usersClass->updateTokenLocal($currentSession->email, null);
			if($response->result == 2)
				session_destroy();
		}
		return $response;
	}

	public function validateSession(){
		$usersClass = new users();
		$response = new \stdClass();
		$response->result = 1;

		if(isset($_SESSION['sistemSession'])){
			$currentSession = $_SESSION['sistemSession'];
			$responseGetUser = $usersClass->getUserByMail($currentSession->email);
			if ( $responseGetUser->result == 2 ){
				if(strcmp($currentSession->tokenLocal, $responseGetUser->objectResult->tokenLocal) == 0){
					$responseUpdateActivity = $usersClass->updateLastActivity($responseGetUser->objectResult->idUsuario);
					// var_dump($responseUpdateActivity);
					if ( $responseUpdateActivity->result == 2 ){
						$response->result = 2;
						$response->currentSession = $currentSession;
					} else { // tambien es valida pero no pudo actualizar la fechaToken
						$response->result = 2;
						$response->currentSession = $currentSession;
					}
				} else {
					$response->result = 0;
					$response->message = "La sesión del usuario caducó por favor vuelva a ingresar.";
				}
			} else {
				$response->result = 0;
				$response->message = "La sesión detectada no es valida, por favor vuelva a ingresar.";
			}
		} else {
			$response->result = 0;
			$response->message = "Actulamente no hay una sesión activa en el sistema.";
		}
		return $response;
	}


	public function getTokenUserLogued($email){
		$usersClass = new users();

		// $mail = $_SESSION['mailUserLogued'];

		$response = $usersClass->getToken( $email );
		return $response;
	}

	public function loadListUser($rut){
		$restController = new ctr_rest();
		// $usersClass = new users();

		$responseUsers = $restController->loadListUser( $rut );
		
		return $responseUsers;
	}

	public function loadUser($rut, $email){
		$restController = new ctr_rest();
		// $usersClass = new users();

		$responseUser = $restController->loadUser( $rut, $email );
		
		return $responseUser;
	}

	public function updateUser($rut, $email, $name, $active, $cellphone, $scopes){
		$restController = new ctr_rest();
		
		$responseUser = $restController->updateUser($rut, $email, $name, $active, $cellphone, $scopes);
		return $responseUser;
	}

	public function newUser($rut, $email, $name, $cellphone, $scopes){
		$restController = new ctr_rest();
		
		$responseUser = $restController->newUser($rut, $email, $name, $cellphone, $scopes);
		return $responseUser;
	}
	// updateUserPassword($rut, $email)
	public function updateUserPassword($rut, $email, $pwd){
		$restController = new ctr_rest();
		
		$responseUser = $restController->updateUserPassword($rut, $email, $pwd);
		return $responseUser;
	}
}


?>