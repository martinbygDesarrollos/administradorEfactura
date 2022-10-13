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

						$imported = $emitedControler->importXmlEmitedZip($files['nameFileCfeXml']["name"][$index], file_get_contents($files['nameFileCfeXml']["tmp_name"][$index]));
						array_push($comprobantes, $imported);

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

		$comp = array(
			"idEnvio" => 1,
			"xml" => file_get_contents($pathFile)
		);

		return $comp;
	}



	public function importXmlEmitedZip($name, $content){
		var_dump($name, substr($name, 0, -4));exit;

		$response = new stdClass();
		$emitedControler = new ctr_emited();
		$folderPath = dirname(dirname(__DIR__)) . "/public/files/";


		if ( strpos($name, ".zip") !== false ){

			$zip = new ZipArchive();
			$descompressFile = $zip->open($content);
			if($descompressFile === TRUE){
				$zip->extractTo($folderPath.DIRECTORY_SEPARATOR.substr($name, 0, -4));
				$zip->close();
			}

			$listDir = array_diff(scandir($folderPath.DIRECTORY_SEPARATOR.substr($name, 0, -4)), array('..', '.'));
			if(sizeof($listDir) > 0){
				var_dump($listDir);exit;
				foreach ($listDir as $item) {

					if ( $item == "CfeEmitidos"){
						$auxRespuesta = $emitedControler->importXmlEmitedZip($name, $content);
						var_dump($auxRespuesta);
					}
			    }
			}

		}else if ( strpos($name, ".xml") !== false ){

			$comp = array(
				"idEnvio" => 1,
				"xml" => $content
			);

			$response->result = 2;
			$response->objectResult = $comp;
			return $response;

		}




		unlink(dirname(dirname(__DIR__)) . "/public/files/");
		mkdir(dirname(dirname(__DIR__)) . "/public/files/");
		array_push($arrayErrors, "Aún no proceso archivos zip");


	}



}


?>