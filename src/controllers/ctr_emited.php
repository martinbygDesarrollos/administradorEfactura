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

		$arrayData = array("comprobantes" => $comprobantes);

		$response = $restController->importCfeEmitedXml($arrayData);
		return $response;

	}



	public function importXmlEmited( $pathFile ){

		$comp = array(
			"idEnvio" => 1,
			"xml" => file_get_contents($pathFile)
		);

		return $comp;
	}
}


?>