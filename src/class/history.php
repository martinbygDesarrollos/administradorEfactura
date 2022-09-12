<?php

require_once '../src/connection/open_connection.php';


class history{

	public function newLogHistory(){

		$dataBaseClass = new DataBase();
		$sql = "INSERT INTO `historial` (`fechahora`, `idUsuario`, `detalle`) VALUES (?,?,?)";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $mail, $token), "BOOLE");
		return $response;

	}

}


?>