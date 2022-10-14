<?php

require_once '../src/class/vouchers.php';
require_once 'ctr_rest.php';


class ctr_emited{

	public function resendXml( $rut, $data ){

		$restController = new ctr_rest();
		$responseSobre = new \stdClass();

		if ( isset($data) ){
			$lengthCfes = $data['numberCfes'];

			$arrayCfes = array();

			for ($i=0; $i < $lengthCfes; $i++) {
				$arrayCfeData = array(
		  			"tipoCFE" => $data['nameSelectTipoCfeSendXml'.$i],
		  			"serieCFE" => strtoupper($data['nameInputSerieCfeSendXml'.$i]),
		  			"numeroCFE" => $data['nameInputNumeroCfeSendXml'.$i]
		  		);

		  		array_push($arrayCfes, $arrayCfeData);
			}


	  		$mailsList = str_replace(" ","",$data['nameInputMailsCopySendXml']);
	  		$mailsList = explode(",", $mailsList);

	  		$dataToSend = array(
	  			'cfes' => $arrayCfes,
	  			'copyTo' => $mailsList);

			$responseSobre = $restController->sendsobre($rut, $dataToSend);
			return $responseSobre;

		}else{
			$responseSobre->result = 1;
			$responseSobre->message = sobre_no_data;
			return $responseSobre;
		}

	}

	public function getReportsByCompanie($rut){
		$vouchersClass = new vouchers();

		//var_dump("procesar datos del uso de los comprobantes");

		$responseCompany = $vouchersClass->getReportsByCompanie( $rut );
		return $responseCompany;

	}




	public function importCfeEmitedXml($files){  //$files puede ser un archivo solo o multiple .zip o .xml
		$emitedControler = new ctr_emited();
		$restController = new ctr_rest();
		$response = new \stdClass();
		$arrayErrors = array();

		$comprobantes = array();
		if (strlen($files['nameFileCfeXml']["name"][0]) > 0){

			foreach ($files['nameFileCfeXml']["type"] as $index => $value) {


				if ( $files['nameFileCfeXml']["error"][$index] == 0 ){
					if ($value == "text/xml"){

						$imported = $emitedControler->importXmlEmited( $files['nameFileCfeXml']["tmp_name"][$index] );
						array_push($comprobantes, $imported);

					}
					else if ($value == "application/x-zip-compressed"){
						$imported = $emitedControler->importXmlEmitedZip($files['nameFileCfeXml']["name"][$index], $files['nameFileCfeXml']["tmp_name"][$index]);

						$comprobantes = array_merge($comprobantes, $imported);
						$emitedControler->clearFolderPath(['public', 'files']);
						mkdir(dirname(dirname(__DIR__)) . "/public/files/");

					}
				}else{
					array_push($arrayErrors, "Archivo ".$files['nameFileCfeXml']["name"][$index]." error ".$files['nameFileCfeXml']["error"][$index]);
				}

			}

		}else{

			array_push($arrayErrors, "No se encontraron archivos.");
			$response->result = 1;
			$response->message = $arrayErrors;
			return $response;
		}


		if ( count($comprobantes) > 0 ){
			$arrayData = array("comprobantes" => $comprobantes);
			$response = $restController->importCfeEmitedXml($arrayData);

		}else{

			array_push($arrayErrors, "No se encontraron archivos.");
			$response->result = 1;
			$response->message = $arrayErrors;
			return $response;
		}

		return $response;

	}



	public function importXmlEmited( $pathFile ){
		$emitedControler = new ctr_emited();

		$data = file_get_contents($pathFile);

		$xml=simplexml_load_string($data) or die(false);
		if ( $xml ){
			$tipoCFE = (int)$xml->CFE->eFact->Encabezado->IdDoc->TipoCFE;
			$serieCFE = (string)$xml->CFE->eFact->Encabezado->IdDoc->Serie;
			$numeroCFE = (int)$xml->CFE->eFact->Encabezado->IdDoc->Nro;
		}

		$idEnvio = $emitedControler->calcIdEnvioCfeXml( $serieCFE, $tipoCFE, $numeroCFE);
		$data = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $data);

		if ( strpos($data, "<?xml") !== false){
			$response = new stdClass();
			$response->result = 1;
			$response->message = $pathFile.' contiente etiqueta xml distinta a <?xml version="1.0" encoding="utf-8"?>';
			return $response;
		}


		$comp = array(
			"idEnvio" => $idEnvio,
			"xml" => $data
		);

		return $comp;
	}



	public function importXmlEmitedZip($name, $content){

		$response = new stdClass();
		$emitedControler = new ctr_emited();


		$arrayRespuesta = array();
		$folderPath = dirname(dirname(__DIR__)) . "/public/files/";


		if ( strpos($name, ".zip") !== false ){

			$zip = new ZipArchive();
			$descompressFile = $zip->open($content);
			if($descompressFile === TRUE){
				$zip->extractTo($folderPath.substr($name, 0, -4).DIRECTORY_SEPARATOR);
				$zip->close();
			}

			$listDir = array_diff(scandir($folderPath.DIRECTORY_SEPARATOR.substr($name, 0, -4)), array('..', '.'));
			if(sizeof($listDir) > 0){
				foreach ($listDir as $item) {

					if ( $item == "CfeEmitidos" || ( strpos($item, ".xml") !== false ) ){
						$newContent = $folderPath.substr($name, 0, -4).DIRECTORY_SEPARATOR.$item;
						$auxRespuesta = $emitedControler->importXmlEmitedZip($item, $newContent);
						$arrayRespuesta = array_merge($arrayRespuesta, $auxRespuesta);
					}/*else{
						var_dump("no es cfe emitidos ", $item);
					}*/
			    }
				return $arrayRespuesta;

			}

		}else if ( strpos($name, ".xml") !== false ){
			//var_dump("un archivo", $name);
			$comp = $emitedControler->importXmlEmited($content);

			array_push($arrayRespuesta, $comp);
			//var_dump("archivo xml", $arrayRespuesta);exit;
			return $arrayRespuesta;

		}else{

			$listDir = array_diff(scandir($content), array('..', '.'));
			//var_dump("en una carpeta ", $listDir);
			if(sizeof($listDir) > 0){
				foreach ($listDir as $item) {

					if ( $item == "CfeEmitidos" || ( strpos($item, ".xml") !== false ) ){
						$newContent = $content . DIRECTORY_SEPARATOR . $item;
						$auxRespuesta = $emitedControler->importXmlEmitedZip($item, $newContent);
						//var_dump("retornando contenido de carpeta cfeemitidos");
						$arrayRespuesta = array_merge($arrayRespuesta, $auxRespuesta);
					}
			    }
				return $arrayRespuesta;

			}else{
				//var_dump("no es una carpeta ni zip ni xml ", $name);
				return $arrayRespuesta;
			}
		}
	}





	//FUNCION QUE BORRA TODO EL CONTENIDO DE UNA CARPETA QUE SE PASA POR PARAMETRO EN FORMATO DE ARRAY
	//EJ ['public', 'files', 'contratos']
	public function clearFolderPath($path){

		$emitedControler = new ctr_emited();

		$dir = dirname(dirname(__DIR__)); // "C:\xampp\htdocs\administradorEfactura"
		foreach ($path as $value) {
			$dir .= DIRECTORY_SEPARATOR . $value;
		}

		if (!file_exists($dir)) {
	        return true;
	    }

	    if (!is_dir($dir)) {
	        return unlink($dir);
	    }

	    foreach (scandir($dir) as $item) {
	        if ($item == '.' || $item == '..') {
	            continue;
	        }

	        array_push($path, $item);
	        //var_dump($path);
	        if (!$emitedControler->clearFolderPath($path)) {
	            return false;
	        }else {
	        	array_pop($path);
	    		//var_dump("pop",$path);
	        }
	    }
	    return rmdir($dir);
	}




	public function calcIdEnvioCfeXml( $serieCFE, $tipoCFE, $numeroCFE){

		$serieCharacters = str_split($serieCFE, 1);
		$idEnvio = "";
		foreach ($serieCharacters as $character) {
			$idEnvio .= (int)"".ord($serieCFE);
		}


		$idEnvio .= (int) $tipoCFE;
		$idEnvio .= (int) $numeroCFE;

		return $idEnvio;
	}

}


?>