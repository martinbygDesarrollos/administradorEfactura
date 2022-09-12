<?php

//require_once '../src/class/utils.php';
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
}


?>