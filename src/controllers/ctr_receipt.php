<?php

require_once 'ctr_rest.php';
require_once 'ctr_emited.php';


class ctr_receipt{

	public function importCfeReceiptXml($files){
		$emitedControler = new ctr_emited();
		$restController = new ctr_rest();
		$response = new \stdClass();
		$arrayErrors = array();

		if (strlen($files['nameFileCfeXml']["name"][0]) > 0){ //no se cargaron archivos
			$comprobantesRecibidos = $emitedControler->procesarComprobantesXml($files);
		}else{

			array_push($arrayErrors, "No se encontraron archivos.");
			$response->result = 1;
			$response->message = $arrayErrors;
			return $response;
		}

		$responseReceipt = null;

		if ( count($comprobantesRecibidos) > 0 ){
			$arrayData = array("comprobantes" => $comprobantesRecibidos);
			$responseReceipt = $restController->importCfeReceiptXml($arrayData);

			$response = $responseReceipt;
		}else{
			array_push($arrayErrors, "No se encontraron archivos.");
			$response->result = 1;
			$response->message = $arrayErrors;
			return $response;
		}

		return $response;

	}
}
