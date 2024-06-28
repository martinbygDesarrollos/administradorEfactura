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
	
	function getPermissionsByMail(){
		$dataBaseClass = new DataBase();
		$sql = "SELECT permisos FROM `usuarios` WHERE correo = ?";
		$response = $dataBaseClass->sendQuery($sql, array('s', $mail), "OBJECT");
		if ($response->result == 2 ){
			return $response->objectResult->permisos;
		}else return null;
	}

	// TESTING TESTING TESTING TESTING TESTING ##############################################
	
	public function insertUserLocal($mail, $token){
		$dataBaseClass = new DataBase();
		$sql = "INSERT INTO `usuarios` (`correo`, `tokenLocal`) VALUES (?,?)";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $mail, $token), "BOOLE");
		return $response;
	}

	public function updateLastActivity($id){
		$dataBaseClass = new DataBase();
		$date = date("YmdHis");
		// echo "AAAAAAAAA";
		// $sql = "UPDATE `usuarios` SET `tokenFecha`= ? WHERE `correo`= ?";
		$sqlTest = "UPDATE usuarios SET tokenFecha = ? WHERE idUsuario = ?;";
		$response = $dataBaseClass->sendQuery($sqlTest, array('si', $date, $id), "BOOLE");
		// var_dump($date);
		// echo "=";
		// var_dump($response);
		// echo "AAAAAAAAA";
		return $response;
	}

	public function updateTokenLocal($mail, $newToken){
		$dataBaseClass = new DataBase();
		$sql = "UPDATE usuarios SET tokenLocal = ? WHERE correo = ?";
		$response = $dataBaseClass->sendQuery($sql, array('ss', $newToken, $mail), "BOOLE");
		return $response;
	}

	public function removeTokenLocal($mail){
		$dataBaseClass = new DataBase();
		$sql = "UPDATE `usuarios` SET `tokenLocal`= ? WHERE `correo`= ?";
		$response = $dataBaseClass->sendQuery($sql, array('ss', null, $mail), "BOOLE");
		return $response;
	}

	public function setNewTokenAndSession($email, $companiesList){
		$dbClass = new DataBase();
		$newTokenLocal = bin2hex(random_bytes((100 - (100 % 2)) / 2));
		$responseQuery = $this->updateTokenLocal($email, $newTokenLocal);
		if($responseQuery->result == 2){
			$responseQuery = $this->getUserByMail($email);
			if($responseQuery->result == 2){
				$objectSession = new \stdClass();
				$objectSession->email = $responseQuery->objectResult->correo;
				$objectSession->tokenLocal = $responseQuery->objectResult->tokenLocal;
				$objectSession->permisos = $responseQuery->objectResult->permisos;
				// foreach ($companiesList as $key => $row) {
				// 	// var_dump($row);
				// 	$caes = array();
				// 	foreach($row->caes as $key => $cae){
				// 		// var_dump($cae->vencimiento);
				// 		// var_dump($cae->tipoCFE);
				// 		// var_dump($cae->total);
				// 		// var_dump($cae->disponibles);
				// 		// echo "-------------";
				// 		$caes[] = $cae;
				// 	}
				// 	$certificateExpireDate = "";
				// 	if(isset($row->certificateExpireDate))
				// 		$certificateExpireDate = $row->certificateExpireDate;
				// 	$companies[] = [
				// 		'certificateExpireDate' => $certificateExpireDate,
				// 		'estadoDescripcion' => $row->estadoDescripcion, 
				// 		'rut' => $row->rut, 
				// 		'razonSocial' => $row->razonSocial, 
				// 		'estado' => $row->estado,
				// 		'caes' => $caes
				// 	];
				// }
				// $objectSession->companies = $companies;
				
				$objectSession->companies = $companiesList;
				$objFirstCompanie = array_pop(array_reverse($objectSession->companies));
				$objectSession->rutUserLogued = $objFirstCompanie->rut;
				$objectSession->companieUserLogued = $objFirstCompanie->razonSocial;

				$_SESSION['sistemSession'] = $objectSession;
				unset($responseQuery->objectResult);
			} else {
				$responseQuery->message = "Un error interno no permitio iniciar sesión con este usuario.";
			}
		} else {
			$responseQuery->message = "Un error interno no permitio iniciar sesión con este usuario.";
		}
		return $responseQuery;
	}


}