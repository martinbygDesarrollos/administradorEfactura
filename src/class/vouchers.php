<?php

require_once '../src/connection/open_connection.php';


class vouchers{

	public function getReportsByCompanie($rut){
		$dataBaseClass = new DataBase();
		$sql = "SELECT * FROM `uso_cfes` WHERE `rut` = ?";
		$response = $dataBaseClass->sendQuery($sql, array('s', $rut), "OBJECT");
		return $response;
	}
}