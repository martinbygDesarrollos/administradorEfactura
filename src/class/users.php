<?php

require_once '../src/connection/open_connection.php';


class users{

	public function insertUser($rut, $mail, $token){
		$dataBaseClass = new DataBase();
		$sql = "INSERT INTO `usuarios` (`correo`, `tokenRest`, `rutEmpresa`) VALUES (?,?,?)";
		$response = $dataBaseClass->sendQuery($sql, array('sss', $mail, $token, $rut), "BOOLE");
		return $response;
	}

	public function getUserByMail($mail, $rut){
		$dataBaseClass = new DataBase();
		$sql = "SELECT * FROM `usuarios` WHERE `correo`= ? AND `rutEmpresa`= ?";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $mail, $rut), "BOOLE");
		return $response;
	}

	public function updateToken($rut, $mail, $token){
		$dataBaseClass = new DataBase();
		$sql = "UPDATE `usuarios` SET `tokenRest`= ? WHERE `correo`= ? AND `rutEmpresa`= ?";
		$response = $dataBaseClass->sendQuery($sql, array('sss', $token, $mail, $rut), "BOOLE");
		return $response;
	}
}