<?php

require_once '../src/class/users.php';
require_once 'ctr_rest.php';


class ctr_users{

	public function login( $mail, $password ){
		$time_start = microtime(true);
		$restController = new ctr_rest();
		$usersClass = new users();
		$response = new \stdClass();
		$response->result = 1;

		$responseLogin = $restController->login($mail, $password); //hago el login contra ormen
		if ( $responseLogin->result == 2 ){ //si los tres datos que recibo son correctos entonces los guardo local
			$token = $responseLogin->token;
			$responseGetUser = $usersClass->getUserByMail($mail);
			if ( $responseGetUser->result == 2 ){
				//setear token
				$response = $usersClass->updateToken($mail, $token);
			}
			elseif ( $responseGetUser->result == 1 ){
				$response = $usersClass->insertUser($mail, $token);
			}else return $responseGetUser;
		}else return $responseLogin;


		if ( $response->result == 2 ){
			$_SESSION['mailUserLogued'] = $mail;
		}else{
			$_SESSION['mailUserLogued'] = null;
		}



		$time_end = microtime(true);
		$time = sprintf("%01.3f",$time_end - $time_start);
		error_log("login tiempo: ".$time);
		return $response;
	}


	public function getTokenUserLogued(){
		$usersClass = new users();

		$mail = $_SESSION['mailUserLogued'];

		$response = $usersClass->getToken( $mail );
		return $response;
	}
}


?>