<?php

require_once '../src/class/users.php';
require_once 'ctr_rest.php';


class ctr_users{

	public function login( $rut, $mail, $password ){
		$restController = new ctr_rest();
		$usersClass = new users();
		$response = new \stdClass();
		$response->result = 1;

		$responseLogin = $restController->login($rut, $mail, $password); //hago el login contra ormen
		if ( $responseLogin->result == 2 ){ //si los tres datos que recibo son correctos entonces los guardo local
			$token = $responseLogin->token;
			$responseGetUser = $usersClass->getUserByMail($mail, $rut);
			if ( $responseGetUser->result == 2 ){
				//setear token
				$response = $usersClass->updateToken($rut, $mail, $token);
			}
			elseif ( $responseGetUser->result == 1 ){
				//crear usuario con ese rut de empresa y ese token
				$response = $usersClass->insertUser($rut, $mail, $token);
			}else return $responseGetUser;
		}else return $responseLogin;


		if ( $response->result == 2 ){
			$_SESSION['mailUserLogued'] = $mail;
			$_SESSION['rutUserLogued'] = $rut;
		}else{
			$_SESSION['mailUserLogued'] = null;
			$_SESSION['rutUserLogued'] = null;
		}

		return $response;
	}


	public function getTokenUserLogued(){
		$usersClass = new users();

		$rut = $_SESSION['rutUserLogued'];
		$mail = $_SESSION['mailUserLogued'];

		$response = $usersClass->getToken( $rut, $mail );
		return $response;
	}
}


?>