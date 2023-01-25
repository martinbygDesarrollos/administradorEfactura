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

		$comprobantesEmitidos = array();
		$comprobantesRecibidos = array();
		if (strlen($files['nameFileCfeXml']["name"][0]) > 0){

			foreach ($files['nameFileCfeXml']["type"] as $index => $value) {


				if ( $files['nameFileCfeXml']["error"][$index] == 0 ){
					if ($value == "text/xml"){

						$imported = $emitedControler->importXmlEmited( $files['nameFileCfeXml']["tmp_name"][$index] );
						if ( isset($imported) ){
							//array_push($comprobantesEmitidos, $imported->emitidos);
							//array_push($comprobantesRecibidos, $imported->recibidos);

							$comprobantesEmitidos = array_merge($comprobantesEmitidos, $imported->emitidos);
							$comprobantesRecibidos = array_merge($comprobantesRecibidos, $imported->recibidos);
						}
					}
					else if ($value == "application/x-zip-compressed"){
						$imported = $emitedControler->importXmlEmitedZip($files['nameFileCfeXml']["name"][$index], $files['nameFileCfeXml']["tmp_name"][$index]);

						if ( isset($imported) ){
							$comprobantesEmitidos = array_merge($comprobantesEmitidos, $imported->emitidos);
							$comprobantesRecibidos = array_merge($comprobantesRecibidos, $imported->recibidos);
						}

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

		$responseEmited = null;
		$responseReceipt = null;

		if ( count($comprobantesEmitidos) > 0 ){
			$arrayData = array("comprobantes" => $comprobantesEmitidos);
			$responseEmited = $restController->importCfeEmitedXml($arrayData);
			$response->result = 2;
			array_push($arrayErrors, "Comprobantes EMITIDOS procesados.");
		}


		if( count($comprobantesRecibidos) > 0 ){
			$arrayData = array("comprobantes" => $comprobantesRecibidos);
			$responseReceipt = $restController->importCfeReceiptXml($arrayData);
			$response->result = 2;
			array_push($arrayErrors, "Comprobantes RECIBIDOS procesados.");

		}

		if ( isset($responseEmited) ){

			foreach ($responseEmited->resultadosImportacion as $key => $value) {
				if ($value->ok != 0){

					/*$fileName = $emitedControler->addCeroToString( $value->idEnvio, (16 - strlen($value->idEnvio)));

					$file = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$fileName.".xml";

					unlink($file);*/
				}else{
					$response->result = 1;
					array_push($arrayErrors, $value->error);

				}

			}

			$response->message = $arrayErrors;

		}
		if (isset($responseReceipt)){

			foreach ($responseReceipt->resultadosImportacion as $key => $value) {
				if ($value->ok != 0){

					/*$fileName = $emitedControler->addCeroToString( $value->idEnvio, (16 - strlen($value->idEnvio)));

					$file = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$fileName.".xml";

					unlink($file);*/
				}else{
					$response->result = 1;
					array_push($arrayErrors, $value->error);

				}

			}

			$response->message = $arrayErrors;
		}




		if ( count($comprobantesEmitidos) <= 0 && count($comprobantesRecibidos) <= 0 ){

			array_push($arrayErrors, "No se encontraron archivos.");
			$response->result = 1;
			$response->message = $arrayErrors;
			return $response;
		}

		return $response;

	}


	//funcion para procesar archivo xml y obtener contenido para enviar por rest a ormen
	public function importXmlEmited( $pathFile ){
		$response = new stdClass();
		$response->emitidos = array();
		$response->recibidos = array();

		$emitedControler = new ctr_emited();
		$tipoCFE = "";
		$serieCFE = "";
		$numeroCFE = "";
		$data = file_get_contents($pathFile);

		$array = $emitedControler->processDataXml($data);

		$emisor = false;
		$receptor = false;


		if ( isset($array['CFE']) ){
			//var_dump("si tengo cfe");
			foreach ($array['CFE'] as $value) {
				if ( isset($value["Encabezado"]) ){
					$tipoCFE = $value["Encabezado"]['IdDoc']['TipoCFE'];
					$serieCFE = $value["Encabezado"]['IdDoc']['Serie'];
					$numeroCFE = $value["Encabezado"]['IdDoc']['Nro'];

					$rucemisor = $value["Encabezado"]['Emisor']['RUCEmisor'];
					$rucreceptor = $value["Encabezado"]['Receptor']['DocRecep'];
					//$_SESSION['rutUserLogued']
					//"211361090011"
					if ( $rucemisor == $_SESSION['rutUserLogued'] ){ $emisor = true; }
					else if ( $rucreceptor == $_SESSION['rutUserLogued']){ $receptor = true; }
					else return $response;

					error_log("obtener xml de ".$tipoCFE." ".$serieCFE."-".$numeroCFE);
				}
			}

			$idEnvio = $emitedControler->calcIdEnvioCfeXml( $serieCFE, $tipoCFE, $numeroCFE);
			$data = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $data);

			if ( strpos($data, "<?xml") !== false){
				/*$response = new stdClass();
				$response->result = 1;
				$response->message = $pathFile.' contiente etiqueta xml distinta a <xml version="1.0" encoding="utf-8">';*/
				return $response;
			}


			$comp = array(
				"idEnvio" => $idEnvio,
				"xml" => $data
			);

			//crear archivo y guardarlo en public temp
			//$resultCreate = $emitedControler->createTempFile($idEnvio.".xml", $data);

			if ( $emisor ){
				array_push($response->emitidos, $comp);
				return $response;
			}
			else if ( $receptor ){
				array_push($response->recibidos, $comp);
				return $response;
			}
			else return null;


		}else{
			error_log("no me proceso el xml");
			//var_dump($pathFile, $data, $array);
			return null;
		}
	}


	//funcion recursiva que procesa un zip y todos los archivos xml que esten en el zip o en carpeta "CfeEmitidos"
	public function importXmlEmitedZip($name, $content){

		$response = new stdClass();
		$response->emitidos = array();
		$response->recibidos = array();

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

					//if ( $item == "CfeEmitidos" || ( strpos($item, ".xml") !== false ) ){
						$newContent = $folderPath.substr($name, 0, -4).DIRECTORY_SEPARATOR.$item;
						$auxRespuesta = $emitedControler->importXmlEmitedZip($item, $newContent);
						if ( isset($auxRespuesta) ){
							$response->emitidos = array_merge($response->emitidos, $auxRespuesta->emitidos);
							$response->recibidos = array_merge($response->recibidos, $auxRespuesta->recibidos);
						}
					/*}else{
						var_dump("no es cfe emitidos ", $item);
					}*/
			    }
				return $response;

			}

		}else if ( strpos($name, ".xml") !== false ){
			//var_dump("un archivo", $name);
			$comp = $emitedControler->importXmlEmited($content);
			if ( isset($comp) ){
				//array_push($arrayRespuesta, $comp);
				$response->emitidos = array_merge($response->emitidos, $comp->emitidos);
				$response->recibidos = array_merge($response->recibidos, $comp->recibidos);
			}
			//var_dump("archivo xml", $arrayRespuesta);exit;
			//return $arrayRespuesta;
			return $response;

		}else{

			if( is_dir($content)){
				$listDir = array_diff(scandir($content), array('..', '.'));
				//var_dump("en una carpeta ", $listDir);
				if(sizeof($listDir) > 0){
					foreach ($listDir as $item) {

						//if ( $item == "CfeEmitidos" || ( strpos($item, ".xml") !== false ) ){
							$newContent = $content . DIRECTORY_SEPARATOR . $item;
							$auxRespuesta = $emitedControler->importXmlEmitedZip($item, $newContent);
							//var_dump("retornando contenido de carpeta cfeemitidos");
							if ( isset($auxRespuesta) ){

								//$arrayRespuesta = array_merge($arrayRespuesta, $auxRespuesta);
								$response->emitidos = array_merge($response->emitidos, $auxRespuesta->emitidos);
								$response->recibidos = array_merge($response->recibidos, $auxRespuesta->recibidos);
							}
						//}
				    }
					return $response;

				}else{
					//var_dump("no es una carpeta ni zip ni xml ", $name);
					return $response;
				}
			}else{
				return null;
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



	//para importar cfe se necesita enviar un idEnvio que identifique al comprobante
	public function calcIdEnvioCfeXml( $serieCFE, $tipoCFE, $numeroCFE ){

//		<TipoCFE>101</TipoCFE><Serie>A</Serie><Nro>3781</Nro>


		$emitedControler = new ctr_emited();

		$serieCharacters = str_split($serieCFE, 1);
		$idEnvio = "";
		$serie = "";
		$numero = "";


		foreach ($serieCharacters as $character) {
			$serieChar = (string) ord($serieCFE);

			if( (3 - strlen($serieChar)) > 0){
				$serieChar = $emitedControler->addCeroToString( $serieChar, (3 - strlen($serieChar)));
			}
			$serie .= $serieChar;
		}

		if( (6 - strlen($serie)) > 0){
			$serie = $emitedControler->addCeroToString( $serie, (6 - strlen($serie)));
		}

		$idEnvio .= $serie;
		$idEnvio .= (string) $tipoCFE;



		if( (7 - strlen($numeroCFE)) > 0){
			$numero = $emitedControler->addCeroToString( $numeroCFE, (7 - strlen($numeroCFE)));
		}else $numero = $numeroCFE;
		$idEnvio .= (string) $numero;


		return $idEnvio;
	}


	/*el ORD de cada letra de la serie con pad de 3 ceros a la izquierda
	pad de 7 u 8 ceros del numero*/
	public function addCeroToString( $string, $countCeros){
		$newString = "";
		for ($i=0; $i < $countCeros; $i++) {
			$newString .= (string)"0";
		}

		$newString .= $string;
		return $newString;
	}



	//llega un contenido de un comprobante que tenga la etiqueta CFE, que sería un xml pero puede tener un prefijo (namespace) o no y lo convierto en array
	function processDataXml( $data ){



		$xml=simplexml_load_string($data) or die(false);
		if ( $xml ){


			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			$array['CFE'] = $array;
			return $array;
		}


		$xml_simple = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml_simple);
		$array = json_decode($json,TRUE);

		if ( isset($array["CFE"]) ){
			return $array;
		}else{
			$xml_nsad = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'nsAd', true);
			$json_nsad = json_encode($xml_nsad);
			$array = json_decode($json_nsad,TRUE);

			if ( isset($array["CFE"]) ){
				return $array;
			}else{

				$xml_ns0 = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'ns0', true);
				$json_ns0 = json_encode($xml_ns0);
				$array = json_decode($json_ns0,TRUE);

				if ( isset($array["CFE"]) ){
					return $array;
				}else{

					$xml_uycfe = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'uycfe', true);
					$json_uycfe = json_encode($xml_uycfe);
					$array = json_decode($json_uycfe,TRUE);

					if ( isset($array["CFE"]) ){
						return $array;
					}else{
						$xml_xsd = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'xsd', true);
						$json_xsd = json_encode($xml_xsd);
						$array = json_decode($json_xsd,TRUE);

						if ( isset($array["CFE"]) ){
							return $array;
						}else{
							$xml_ds = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'ds', true);
							$json_ds = json_encode($xml_ds);
							$array = json_decode($json_ds,TRUE);

							if ( isset($array["CFE"]) ){
								return $array;
							}
						}
					}
				}
			}
		}

		$xml_ds = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'sobre', true);
		$json_ds = json_encode($xml_ds);
		$array = json_decode($json_ds,TRUE);

		if ( isset($array["CFE"]) ){
			return $array;
		}

		$xml_ds = new SimpleXMLElement($data, LIBXML_NOERROR, false, 'dgicfe', true);
		$json_ds = json_encode($xml_ds);
		$array = json_decode($json_ds,TRUE);

		if ( isset($array["CFE"]) ){
			return $array;
		}

		return array();
	}




	function createTempFile($name, $data){


		//file_put_contents(dirname(dirname(__DIR__)) . "/public/temp/".$name, $data);

	}

}


?>