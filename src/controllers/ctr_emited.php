<?php

//require_once '../src/class/utils.php';
require_once 'ctr_rest.php';


class ctr_emited{

	public function resendXml( $rut, $data ){

		$restController = new ctr_rest();

		$arrayCfes = array();
  		$arrayCfeData = array(
  			"tipoCFE" => $data['nameSelectTipoCfeSendXml'],
  			"serieCFE" => strtoupper($data['nameInputSerieCfeSendXml']),
  			"numeroCFE" => $data['nameInputNumeroCfeSendXml']
  		);

  		array_push($arrayCfes, $arrayCfeData);

  		$mailsList = str_replace(" ","",$data['nameInputMailsCopySendXml']);
  		$mailsList = explode(",", $mailsList);

  		$dataToSend = array(
  			'cfes' => $arrayCfes,
  			'copyTo' => $mailsList);

		$responseSobre = $restController->sendsobre($rut, $dataToSend);
		return $responseSobre;

	}
}


?>