<?php

require_once '../src/connection/open_connection.php';


class users{

	public function insertUser($mail, $token){
		$dataBaseClass = new DataBase();
		$sql = "INSERT INTO `usuarios` (`correo`, `tokenRest`) VALUES (?,?)";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $mail, $token), "BOOLE");
		return $response;
	}

	public function getUserByMail($mail){
		$dataBaseClass = new DataBase();
		$sql = "SELECT * FROM `usuarios` WHERE `correo`= ?";
		$response = $dataBaseClass->sendQuery($sql, array('s', $mail), "OBJECT");
		return $response;
	}

	public function updateToken($mail, $token){
		$dataBaseClass = new DataBase();
		$sql = "UPDATE `usuarios` SET `tokenRest`= ? WHERE `correo`= ?";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $token, $mail), "BOOLE");
		return $response;
	}

	public function getToken($mail){
		$dataBaseClass = new DataBase();
		$sql = "SELECT tokenRest FROM `usuarios` WHERE correo = ?";
		$response = $dataBaseClass->sendQuery($sql, array('s', $mail), "OBJECT");
		return $response;
	}
}