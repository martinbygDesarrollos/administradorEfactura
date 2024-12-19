<?php

require_once '../src/class/utils.php';
require_once '../src/utils/validate.php';
require_once 'ctr_rest.php';


class ctr_companies{

	public function getCompanies($email, $entorno){
		$companieController = new ctr_companies();
		$restController = new ctr_rest();
		$utilClass = new utils();
		$responseCompanies = $restController->getCompanies($email, $entorno);


		foreach ($responseCompanies->listResult as $key => $value) { //recorriendo empresas
			$expireDate = null;
			$expireDateVoucher = null;
			$expireDateCaeType = null;

			if ( isset($value->certificateExpireDate) && $value->certificateExpireDate != "" ){
				$certExpireDate = substr($value->certificateExpireDate,0,4).substr($value->certificateExpireDate,5,2).substr($value->certificateExpireDate,8,2);

				$expireDate = $certExpireDate;
				$expireDateVoucher = "Certificado";
				$expireDateCaeType = 0;
			}

			foreach ($value->caes as $keyCae => $cae) { //recorriendo los caes de x empresa

				if ( isset($expireDate) && $expireDate != ""  ){
					$expireDateCae = $cae->vencimiento;
					$date = substr($expireDateCae,0,4).substr($expireDateCae,5,2).substr($expireDateCae,8,2);

					if ( $date < $expireDate ){
						$duplicatedCae = false;
						foreach ($value->caes as $cae2) {
							if ( $cae->tipoCFE == $cae2->tipoCFE && $cae2->vencimiento > $cae->vencimiento ){
								$duplicatedCae = true;
							}
						}

						if ( !$duplicatedCae ){
							$expireDate = $date;
							$expireDateVoucher = "CAE";
							$expireDateCaeType = $utilClass->tableCfeType($cae->tipoCFE);
						}

					}

				}else {
					$expireDateCae = $cae->vencimiento;
					$date = substr($expireDateCae,0,4).substr($expireDateCae,5,2).substr($expireDateCae,8,2);

					$expireDate = $date;
					$expireDateVoucher = "CAE";
					$expireDateCaeType = $utilClass->tableCfeType($cae->tipoCFE);
				}

				$value->caes[$keyCae]->vencCae = substr($cae->vencimiento,8,2)."/".substr($cae->vencimiento,5,2)."/".substr($cae->vencimiento,0,4);
				$value->caes[$keyCae]->tipoDescripcion = $utilClass->tableCfeType($cae->tipoCFE);
				$value->caes[$keyCae]->tipoDescAbrev = substr($utilClass->tableCfeType($cae->tipoCFE), 0, 12);

				if ( strlen($value->caes[$keyCae]->tipoDescripcion) > 12  ){
					$value->caes[$keyCae]->tipoDescAbrev = $value->caes[$keyCae]->tipoDescAbrev . "...";
				}

			}

			//ver de que color se muestra la fila según el vencimiento del certificado dgi 60 dias marcar en naranja 20 dias rojo
			$expireColor = new stdClass();
			$expireColor->color = null;
			$expireColor->title = null;
			if ($value->estado == 6){
				$expireColor = $companieController->expireColorWarning($expireDate);
			}

			$responseCompanies->listResult[$key]->logo = "";
			$responseCompanies->listResult[$key]->proxVencDescr = $expireDate;

			$auxExpireDate = "";
			if ( strlen($expireDate ?? "") >0 )
				$auxExpireDate = substr($expireDate,6,2)."/".substr($expireDate,4,2)."/".substr($expireDate,0,4);

			$responseCompanies->listResult[$key]->proxVencimiento = $auxExpireDate;
			$responseCompanies->listResult[$key]->comprobante = $expireDateVoucher;
			$responseCompanies->listResult[$key]->tipoCae = $expireDateCaeType;
			$responseCompanies->listResult[$key]->expireDateColor = $expireColor;

		}

		/*$responseCompanies->listResult[1]->proxVencDescr = "";
		$responseCompanies->listResult[1]->proxVencimiento = "";
		$responseCompanies->listResult[1]->comprobante = "";
		$responseCompanies->listResult[1]->tipoCae = "";*/
		$responseCompanies->listResult = $this->companiesOrders( $responseCompanies->listResult );
		return $responseCompanies;
	}


	public function companiesOrders( $arrayCompanies ){

		// Función de comparación
		function cmp($a, $b) {
		    if ($a->estado == 6 && $b->estado != 6) return -1;
		    if ($a->estado != 6 && $b->estado == 6) return 1;
		    if ($a->proxVencDescr != "" && $b->proxVencDescr == "") return -1;
		    if ($a->proxVencDescr == "" && $b->proxVencDescr != "") return 1;
		    if(isset($a->proxVencDescr) && $a->proxVencDescr != "" && isset($b->proxVencDescr) && $b->proxVencDescr != "" && $a->proxVencDescr < $b->proxVencDescr)
		    	return -1;
		    else return 1;

		}

		uasort($arrayCompanies, 'cmp');

		return $arrayCompanies;
	}



	public function getCompaniesData( $rut ){

		$restController = new ctr_rest();

		$responseCompany = $restController->getCompanyData( $rut );
		return $responseCompany;

	}



	public function changeCompanieData( $data ){

		$restController = new ctr_rest();

		$responseCompanies = $restController->changeCompanieData($data);
		return $responseCompanies;

	}


	public function changeCompanieColors( $data ){

		$restController = new ctr_rest();

		$responseCompanies = $restController->changeCompanieColors($data);
		return $responseCompanies;

	}


	public function changeStatusCompanie($newStatus, $rut){

		$companieController = new ctr_companies();
		$response = new stdClass();
		$response->result = 1;
		$response->message = "No se ha podido procesar el cambio de estado";


		switch ($newStatus) {
			case 4:
				$response = $companieController->changeStatusToPendPostulacion($rut);
				return $response;
			case 5:
				$response = $companieController->changeStatusToPendCertificacion($rut);
				return $response;
			default:
				return $response;

		}

	}


	public function changeStatusToPendPostulacion( $rut ){

		$restController = new ctr_rest();

		$responseCompanies = $restController->statusPendPostulacion($rut);
		return $responseCompanies;

	}



	public function changeStatusToPendCertificacion( $rut ){

		$restController = new ctr_rest();

		$responseCompanies = $restController->statusPendCertificacion($rut);
		return $responseCompanies;

	}




	public function loadResolutions( $rut, $data ){

		$restController = new ctr_rest();

		$data["emitterDate"] = substr($data["emitterDate"],8,2)."/".substr($data["emitterDate"],5,2)."/".substr($data["emitterDate"],0,4);


		$responseCompanies = $restController->loadResolutions($rut, $data);
		return $responseCompanies;

	}




	public function loadImage( $rut, $data ){

		$restController = new ctr_rest();
		$responseCompanies = $restController->loadImage($rut, $data);
		return $responseCompanies;

	}


	public function getRepresentacionImpresa( $rut, $sucursal){

		$restController = new ctr_rest();
		$data = $restController->getRepresentacionImpresa($rut);
		if ($sucursal > 0){
			$dataBranches = $restController->getCompanyData( $rut );
			if ($dataBranches->result == 2){
				foreach ($dataBranches->objectResult->customFields as $customFile) {
					if( $customFile->key == "cfe.infoAdicional"){
						$data->objectResult->infoAdicional = $customFile->value;
					}
				}

				foreach ($dataBranches->objectResult->sucursales as $suc) {
					if($suc->codDGI == $sucursal){
						$data->objectResult->colorPrincipal = $suc->colorPrimary;
						$data->objectResult->colorSecundario = $suc->colorSecondary;
					}
				}

			}

		}
		return $data;

	}

	public function updateRepresentacionImpresa( $rut, $data ){

		$restController = new ctr_rest();
		$data = $restController->updateRepresentacionImpresa($rut, $data);
		return $data;

	}


	public function enabledDisabledCompanie($value){
		$restController = new ctr_rest();
		$data = $restController->enabledDisabledCompanie($value);
		return $data;
	}

	public function enableDisableCompany($rut, $status){ // Esta funcion es para pasar a habilitado o deshabiltado a la empresa
		$restController = new ctr_rest();
		$data = null;
		if($status == 1){
			$data = $restController->enableCompany($rut);
		} else {
			$data = $restController->disableCompany($rut);
		}
		return $data;
	}

	public function deleteCompanieBranch($rut, $branch){
		$restController = new ctr_rest();
		$data = $restController->deleteCompanieBranch($rut, $branch);
		return $data;
	}


	public function setExistingBranchLikePrincipal($sucPrincipal, $newSucPrincipal, $rut){ // CORREGIR, BORRA LA ANTERIOR BRANCH No
		$companieController = new ctr_companies();
		$restController = new ctr_rest();
		//PASOS
		// 21 elimino la sucursal que quiero setear como principal
			$response = $companieController->deleteCompanieBranch($rut, $newSucPrincipal->codDGI);
			if($response->result == 2){
				// 2 actualizo los datos de la principal con los de la eliminada
				$data = array (
					nombre => $newSucPrincipal->nombreComercial,
					direccion => $newSucPrincipal->direccion,
					departamento => $newSucPrincipal->departamento,
					localidad => $newSucPrincipal->localidad,
					telefono => $newSucPrincipal->telephone1,
					telefono2 => $newSucPrincipal->telephone2,
					correo => $newSucPrincipal->email,
					sitio => $newSucPrincipal->website,
					codDgi => $sucPrincipal->codDGI,
					rut => $rut
				);
				$response = $companieController->changeCompanieData( $data );
				if($response->result == 2){
					// 3 seteo la nueva sucursal principal con el DGI de la que queria setear como principal(solo cambia el codDGI a la que ya esta como principal)
					$response = $restController->setPrincipalCompanieBranch($rut, $newSucPrincipal->codDGI);
					if($response->result == 2){
						// 4 creo una nueva sucursal secundaria con todos los datos de la principal anterior (incluso el codDGI)
						$data2 = array (
							nombre => $sucPrincipal->nombreComercial,
							direccion => $sucPrincipal->direccion,
							departamento => $sucPrincipal->departamento,
							localidad => $sucPrincipal->localidad,
							telefono => $sucPrincipal->telephone1,
							telefono2 => $sucPrincipal->telephone2,
							correo => $sucPrincipal->email,
							sitio => $sucPrincipal->website,
							codDgi => $sucPrincipal->codDGI,
							rut => $rut
						);
						$response = $companieController->changeCompanieData( $data2 );
						if($response->result == 2){
							$response->message = "Nueva sucursal establecida como principal";
						}
					}

				}
			}
		return $response;
	}

	public function newSecondaryCompanieBranch( $data ){
		$companieController = new ctr_companies();
		$response = new \stdClass();
		$newData = array(
			nombre => $data['nombre'],
			direccion => $data['direccion'],
			departamento => $data['departamento'],
			localidad => $data['localidad'],
			telefono => $data['telefono'],
			telefono2 => $data['telefono2'],
			correo => $data['correo'],
			sitio => $data['sitio'],
			codDgi => $data['codDGI'],
			isTemplate => $data['isTemplate'],
			rut => $data['rut']
		);
		$response = $companieController->changeCompanieData( $newData );
		if($response->result == 2){
			$response->message = "Nueva sucursal creada con exito!";
		}
		return $response;
	}

	public function newPrincipalCompanieBranch($sucPrincipal, $data){
		$companieController = new ctr_companies();
		$restController = new ctr_rest();
		$response = new \stdClass();
		$newDGI = $data['codDGI'];
		// PASOS:
		// NEW 1 Consulto la principal y me quedo los DATOS

		// isPrincipal
		// nombre
		// direccion
		// departamento
		// localidad
		// telefono
		// telefono2
		// correo
		// sitio
		// codDGI
		// isTemplate

		// NEW 2 actualizo la principal con los datos de la nueva
		$newData = array(
			nombre => $data['nombre'],
			direccion => $data['direccion'],
			departamento => $data['departamento'],
			localidad => $data['localidad'],
			telefono => $data['telefono'],
			telefono2 => $data['telefono2'],
			correo => $data['correo'],
			sitio => $data['sitio'],
			codDgi => $sucPrincipal->codDGI,
			rut => $data['rut']
		);
		$response = $companieController->changeCompanieData( $newData );
		if ( $response->result == 2 ) {
			// NEW 3 seteo la sucursal principal con el DGI de la nueva(solo cambia el codDGI a la que ya esta como principal)
			$response = $restController->setPrincipalCompanieBranch($data['rut'], $newDGI);
			if ( $response->result == 2 ) {
				// NEW 4 creo una nueva sucursal secundaria con todos los datos de la principal anterior (incluso el codDGI)
				$data2 = array (
					nombre => $sucPrincipal->nombreComercial,
					direccion => $sucPrincipal->direccion,
					departamento => $sucPrincipal->departamento,
					localidad => $sucPrincipal->localidad,
					telefono => $sucPrincipal->telephone1,
					telefono2 => $sucPrincipal->telephone2,
					correo => $sucPrincipal->email,
					sitio => $sucPrincipal->website,
					codDgi => $sucPrincipal->codDGI,
					rut => $data['rut']
				);
				$response = $companieController->changeCompanieData( $data2 );
				if($response->result == 2){
					$response->message = "Nueva sucursal principal creada con exito!";
				}
			}
		}
		return $response;
	}

	public function saveInfoAdicional($value, $rut){
		$restController = new ctr_rest();
		return $restController->saveInfoAdicional($value, $rut);
	}

	public function createMailgetCaes($rut){ // Esta funcion busca un archivo que se genera junto con el cron cuando se envia el mail empresas con CAEs por vencer
		$response = new \stdClass();
		$restController = new ctr_rest();
		$companieDetails = $restController->getCompanyData( $rut )->objectResult;
		// var_dump($companieDetails);
		// echo "<br>";
		$caesParaPedir = $this->whichCaesToOrder($companieDetails);
		// var_dump($caesParaPedir);
		// exit;
		if (count($caesParaPedir) > 0) {
			$caes = [];
			foreach ($caesParaPedir as $cae){
				$TO = date("YmdHis");
				
				$expireThisMonthBool = $this->venceEsteMes($cae);
				$dateMinusTwoYears = null;
				$monthsLeft = 0;
				$hoy = new DateTime(); // Current date
				$expireDate = new DateTime($cae->vencimiento); // Target date
				
				if($expireThisMonthBool == true) {// Si expira este mes los meses para el calculo son 24, calculo simple
					$dateMinusTwoYears = (clone $hoy)->modify('-2 years');
				} else { // En este caso debo calcular los meses desde la ultima vez que se pidio
					$interval = $hoy->diff($expireDate, true);
					$monthsLeft = $interval->y * 12 + $interval->m;
					$dateMinusTwoYears = (clone $expireDate)->modify('-2 years');
				}
				
				$FROM = $dateMinusTwoYears->format('YmdHis');

				$responseEmitidos = $restController->getCountEmitidos( $rut, $FROM, $TO, $cae->tipoCFE )->objectResult;
				// var_dump($rut);
				// var_dump($FROM);
				// var_dump($TO);
				// var_dump($cae->tipoCFE);
				// var_dump($responseEmitidos);
				// exit;
				$usados = isset($responseEmitidos->cfeCount) ? $responseEmitidos->cfeCount : 0;
		
				$mesesTranscurridos = 24 - $monthsLeft;
				$usadosPorMes = $usados / $mesesTranscurridos;
				$cantCaesPedir = $usadosPorMes * 1.1 * 24;
				if ($cantCaesPedir <= 100) {
					$cantCaesPedir = 100;
				} elseif ($cantCaesPedir <= 1000) {
					$cantCaesPedir = (int)ceil($cantCaesPedir / 100) * 100;
				} else {
					$cantCaesPedir = (int)ceil($cantCaesPedir / 1000) * 1000;
				}
				$caes[] = (object) ['tipoCFE' => $cae->tipoCFE, 'pedir' => $cantCaesPedir, 'usados' => $usados, 'usadosPorMes' => $usadosPorMes, 'razon' => $cae->razon, 'vencimiento' => $cae->vencimiento, 'tipoCFEText' => $this->cfeTypeText($cae->tipoCFE) ];
			}
			$response->caes = $caes;
		}
		$response->rut = $rut;
		$response->razonSocial = $companieDetails->razonSocial;
		return $response;
	}

	public function venceEsteMes($cae){ // calcula si vence dentro de los proximo 30 dias
		if (isset($cae->vencimiento) && $cae->vencimiento != ""){
			// echo substr($cae->vencimiento, 0, 10);
			$dateTime = new DateTime(substr($cae->vencimiento, 0, 10));
			$date = $dateTime->format('Ymd');
			$nextMonth = date('Ymd', strtotime("+1 month"));
			if($date <= $nextMonth){
				return true;
			}
		}
		return false;
	}

	public function quedanPocos($cae){ // calcula si quedan menos del 10%
		$totalCAEs = $cae->total;
		$disponiblesCAEs = $cae->disponibles;
		if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.1)) {
			return true;
		}
		return false;
	}
	
	public function findByTipoCFE($caes, $tipoCFE) {
		$filtered = array_filter($caes, function($cae) use ($tipoCFE) {
			return $cae->tipoCFE == $tipoCFE;
		});
		// Return the first item found or null if none found
		return !empty($filtered) ? array_values($filtered)[0] : null;
	}

	public function gruposIncompleto($companieDetails){ // calcula por grupo si faltan miembros (Ej grupo basico = 101,102,103,111,112,113)
		$grupos = [];
		if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != ""){
			$requiredTipos = [101, 102, 103, 111, 112, 113];
			$foundTipos = [];
			foreach ($companieDetails->caes as $cae) {
				if (in_array($cae->tipoCFE, $requiredTipos)) {
					$foundTipos[] = $cae->tipoCFE;
				}
			}
			// Ensure all required tipos are found
			foreach ($requiredTipos as $tipo) {
				if (!in_array($tipo, $foundTipos)) {
					$grupos[] = "dgiResolutionEFac";
				}
			}
		}
		if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != ""){
			$required = 182;
			$found = false;
			foreach ($companieDetails->caes as $cae) {
				if ($cae->tipoCFE == $required) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$grupos[] = "dgiResolutionERes";
			}
		}
		if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != ""){
			$required = 181;
			$found = false;
			foreach ($companieDetails->caes as $cae) {
				if ($cae->tipoCFE == $required) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$grupos[] = "dgiResolutionERem";
			}
		}
		if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != ""){
			$requiredTipos = [121, 122, 123, 124];
			$foundTipos = [];
			foreach ($companieDetails->caes as $cae) {
				if (in_array($cae->tipoCFE, $requiredTipos)) {
					$foundTipos[] = $cae->tipoCFE;
				}
			}
			// Ensure all required tipos are found
			foreach ($requiredTipos as $tipo) {
				if (!in_array($tipo, $foundTipos)) {
					$grupos[] = "dgiResolutionEFacExp";
				}
			}
		}
		if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != ""){
			$requiredTipos = [131, 132, 133, 141, 142, 143];
			$foundTipos = [];
			foreach ($companieDetails->caes as $cae) {
				if (in_array($cae->tipoCFE, $requiredTipos)) {
					$foundTipos[] = $cae->tipoCFE;
				}
			}
			// Ensure all required tipos are found
			foreach ($requiredTipos as $tipo) {
				if (!in_array($tipo, $foundTipos)) {
					$grupos[] = "dgiResolutionCtaAjena";
				}
			}
		}
		if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != ""){
			$requiredTipos = [151, 152, 153];
			$foundTipos = [];
			foreach ($companieDetails->caes as $cae) {
				if (in_array($cae->tipoCFE, $requiredTipos)) {
					$foundTipos[] = $cae->tipoCFE;
				}
			}
			// Ensure all required tipos are found
			foreach ($requiredTipos as $tipo) {
				if (!in_array($tipo, $foundTipos)) {
					$grupos[] = "dgiResolutionEBolEntrada";
				}
			}
		}
		return $grupos;
	}

	public function whichCaesToOrder($companieDetails) {
		$pedirCAEs = [];
		$pedirResolutionEFac = false;
		$pedirResolutionERes = false;
		$pedirResolutionERem = false;
		$pedirResolutionEFacExp = false;
		$pedirResolutionCtaAjena = false;
		$pedirResolutionEBolEntrada = false;
		
		$modifiedCaes = [];
		if(!isset($companieDetails->caes)){
			return $pedirCAEs;
		}
		if(count($companieDetails->caes) <= 0){
			$pedirResolutionEFac = true;
			$pedirResolutionERes = true;
			$pedirResolutionERem = true;
			$pedirResolutionEFacExp = true;
			$pedirResolutionCtaAjena = true;
			$pedirResolutionEBolEntrada = true;
			foreach ($companieDetails->caes as $cae) {
				$cae->razon = 'NO QUEDAN';
				$modifiedCaes[] = $cae;
			}
		} else {
			foreach ($companieDetails->caes as $cae) { // verificar los caes por grupo (si vence este mes o quedan pocos del grupo y la empresa tiene la resolucion pido todo el grupo)
				// var_dump($cae);
				// echo "<br>";
				if ($this->venceEsteMes($cae) || $this->quedanPocos($cae)) {
					// echo "VENCE AHORA O POCOS <br>";
					if($this->venceEsteMes($cae)) // DE NUEVO
						$cae->razon = 'VENCE PRONTO';
					else
						$cae->razon = 'QUEDAN POCOS';
	
					if ($cae->tipoCFE == 101 || $cae->tipoCFE == 102 || $cae->tipoCFE == 103 || $cae->tipoCFE == 111 || $cae->tipoCFE == 112 || $cae->tipoCFE == 113)
						$pedirResolutionEFac = true;
					else if ($cae->tipoCFE == 182)
						$pedirResolutionERes = true;
					else if ($cae->tipoCFE == 181)
						$pedirResolutionERem = true;
					else if ($cae->tipoCFE == 121 || $cae->tipoCFE == 122 || $cae->tipoCFE == 123 || $cae->tipoCFE == 124 )
						$pedirResolutionEFacExp = true;
					else if ($cae->tipoCFE == 131 || $cae->tipoCFE == 132 || $cae->tipoCFE == 133 || $cae->tipoCFE == 141 || $cae->tipoCFE == 142 || $cae->tipoCFE == 143)
						$pedirResolutionCtaAjena = true;
					else if ($cae->tipoCFE == 151 || $cae->tipoCFE == 152 || $cae->tipoCFE == 153)
						$pedirResolutionEBolEntrada = true;
					$modifiedCaes[] = $cae;
				}// else {
					// echo "NO VENCE <br>";
				// }
			}
			$grupoIncompleto = $this->gruposIncompleto($companieDetails);
			if(count($grupoIncompleto) > 0){ // verifico que si falta algun cae de los grupos
				if (in_array("dgiResolutionEFac", $grupoIncompleto)) {
					$pedirResolutionEFac = true;
				}
				if (in_array("dgiResolutionERes", $grupoIncompleto)) {
					$pedirResolutionERes = true;
				}
				if (in_array("dgiResolutionERem", $grupoIncompleto)) {
					$pedirResolutionERem = true;
				}
				if (in_array("dgiResolutionEFacExp", $grupoIncompleto)) {
					$pedirResolutionEFacExp = true;
				}
				if (in_array("dgiResolutionCtaAjena", $grupoIncompleto)) {
					$pedirResolutionCtaAjena = true;
				}
				if (in_array("dgiResolutionEBolEntrada", $grupoIncompleto)) {
					$pedirResolutionEBolEntrada = true;
				}
			}
		}
		if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != "" && $pedirResolutionEFac){ // Debe tener las 6 basicas
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 101)) ? $cae : (object) ['tipoCFE' => 101, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 102)) ? $cae : (object) ['tipoCFE' => 102, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 103)) ? $cae : (object) ['tipoCFE' => 103, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 111)) ? $cae : (object) ['tipoCFE' => 111, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 112)) ? $cae : (object) ['tipoCFE' => 112, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 113)) ? $cae : (object) ['tipoCFE' => 113, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
		}
		if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != "" && $pedirResolutionERes){ // Debe tener la de Resguardo
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 182)) ? $cae : (object) ['tipoCFE' => 182, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN',  'disponibles' => 0, 'total' => 0];
		}
		if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != "" && $pedirResolutionERem){ // Debe tener la de Remito
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 181)) ? $cae : (object) ['tipoCFE' => 181, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN',  'disponibles' => 0, 'total' => 0];
		}
		if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != "" && $pedirResolutionEFacExp){ // Debe tener las 4 de Exportacion
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 121)) ? $cae : (object) ['tipoCFE' => 121, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 122)) ? $cae : (object) ['tipoCFE' => 122, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 123)) ? $cae : (object) ['tipoCFE' => 123, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 124)) ? $cae : (object) ['tipoCFE' => 124, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
		}
		if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != "" && $pedirResolutionCtaAjena){ // Debe tener las 6 de Cuenta Ajena
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 131)) ? $cae : (object) ['tipoCFE' => 131, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 132)) ? $cae : (object) ['tipoCFE' => 132, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 133)) ? $cae : (object) ['tipoCFE' => 133, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 141)) ? $cae : (object) ['tipoCFE' => 141, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 142)) ? $cae : (object) ['tipoCFE' => 142, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 143)) ? $cae : (object) ['tipoCFE' => 143, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
		}
		if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != "" && $pedirResolutionEBolEntrada){ // Debe tener las 3 de Boleta de entrada
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 151)) ? $cae : (object) ['tipoCFE' => 151, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 152)) ? $cae : (object) ['tipoCFE' => 152, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
			$pedirCAEs[] = ($cae = $this->findByTipoCFE($modifiedCaes, 153)) ? $cae : (object) ['tipoCFE' => 153, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
		}
		return $pedirCAEs;
	}

	public function cfeTypeText($typeCode) {
		switch ($typeCode) {
			case 101: return "e-Ticket";
			case 102: return "Nota de Crédito de e-Ticket ";
			case 103: return "Nota de Débito de e-Ticket Nota Débito";
			case 111: return "e-Factura";
			case 112: return "Nota de Crédito de e-Factura";
			case 113: return "Nota de Débito de e-Factura";
			case 121: return "e-Factura Exportación";
			case 122: return "Nota de Crédito de e-Factura Exportación";
			case 123: return "Nota de Débito de e-Factura Exportación";
			case 124: return "e-Remito Exportación";
			case 131: return "e-Ticket Venta por Cuenta Ajena";
			case 132: return "Nota de Crédito de e-Ticket Venta por Cuenta Ajena";
			case 133: return "Nota de Débito de e-Ticket Venta por Cuenta Ajena";
			case 141: return "e-Factura Venta por Cuenta Ajena";
			case 142: return "Nota de Crédito de e-Factura Venta por Cuenta Ajena";
			case 143: return "Nota de Débito de e-Factura Venta por Cuenta Ajena";
			case 151: return "e-Boleta Entrada";
			case 152: return "Nota de Crédito de e-Boleta de entrada";
			case 153: return "Nota de Débito de e-Boleta de entrada";
			case 181: return "e-Remito";
			case 182: return "e-Resguardo";
			default: return "";
		}
    }

	public function expireColorWarning($expireDate){
		$expireInfo = new stdClass();
		$expireInfo->color = null;
		$expireInfo->title = null;

		if (isset($expireDate) && $expireDate != ""){
			$nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
			$twentyDays = date('Ymd',  strtotime("+ 15 days" , strtotime(date("Ymd"))));

			if($expireDate < date("Ymd")){
				$expireInfo->color = "#F44336"; //rojos
				$expireInfo->title = "Expiró";
			}elseif($expireDate <= $twentyDays){
				$expireInfo->color = "#F44336"; //rojo
				$expireInfo->title = "Expira en menos de 15 días";
			}elseif ($expireDate > $twentyDays && $expireDate <= $nextMonth) {
				$expireInfo->color = "#FF9800"; //naranja
				$expireInfo->title = "Expira en menos de 30 días";
			}else{
				$expireInfo->color = null;
				$expireInfo->title = null;
			}
		}

		return $expireInfo;
	}

	public function loadListCustomers($rut){
		$restController = new ctr_rest();

		$responseCustomers = $restController->loadListCustomers( $rut );
		
		return $responseCustomers;
	}

	public function loadCustomer($rut, $document){
		$restController = new ctr_rest();

		$responseCustomers = $restController->loadCustomer($rut, $document);
		
		return $responseCustomers;
	}

	public function saveCustomer($rut, $customer){
		$restController = new ctr_rest();
		if($customer['notificationMethods'][0] == "0")
			$customer['notificationMethods'] = [];
		else
			$customer['notificationMethods'] = [1];

		if(!isset($customer['contacts']))
			$customer['contacts'] = [];
		$responseCustomers = $restController->saveCustomer($rut, $customer, $customer['document']);
		
		return $responseCustomers;
	}

	public function newCustomer($rut, $customer){
		$restController = new ctr_rest();
		$validate = new validate();
		if($customer['notificationMethods'][0] == "0")
			$customer['notificationMethods'] = [];
		else
			$customer['notificationMethods'] = [1];

		if(!isset($customer['contacts']))
			$customer['contacts'] = [];

		// echo "LLEGA AL BASICO";
		if( strlen($customer['document']) < 9 && $validate->validateCI($customer['document'])){
			// echo "ES CI";
			$customer['documentType'] = 3; // 2 es RUT 3 es CI
		} else if (strlen($customer['document']) > 10 && strlen($customer['document']) < 13 && $validate->validateRUT($customer['document'])->result == 2){
			// echo "ES RUT";
			$customer['documentType'] = 2; // 2 es RUT 3 es CI
		} else {
			// echo "ES ERROR";
			return ["result"=>0];
		}
		// echo $customer['documentType'];
		$responseCustomers = $restController->newCustomer($rut, $customer);
		
		return $responseCustomers;
	}

	public function getEmitidos($rut, $lastId){
		$restController = new ctr_rest();
		if($lastId == "") $lastId = null;
		$responseEmitidos = $restController->getEmitidos( $rut, $lastId );
		
		return $responseEmitidos;
	}

	public function getRecibidos($rut, $lastId){
		$restController = new ctr_rest();
		if($lastId == "") $lastId = null;
		$responseRecibidos = $restController->getRecibidos( $rut, $lastId );
		
		return $responseRecibidos;
	}
	
	public function getCFE($rut, $RUTEmisor, $tipoCFE, $serieCFE, $numeroCFE){
		$restController = new ctr_rest();

		$responseCFE = $restController->getCFE($rut, $RUTEmisor, $tipoCFE, $serieCFE, $numeroCFE);
		
		return $responseCFE;
	}

	//obtiene la cotizacion de varias monedas
	public function getQuotes(){
		$restController = new ctr_rest();
		$response = new \stdClass();

		$currentDate = date('Y-m-d');
		$error = false;
		$responseRestUSD = $restController->obtenerCotizacion($currentDate, $currentDate, "USD");
		if($responseRestUSD->result == 2){
			$response->USD =  bcdiv($responseRestUSD->currentQuote, '1', 4);
		} else
			$error = true;
		
		$responseRestUI = $restController->obtenerCotizacion($currentDate, $currentDate, "UI");
		if($responseRestUI->result == 2){
			$response->UI =  bcdiv($responseRestUI->currentQuote, '1', 4);
		} else
			$error = true;

		$responseRestEUR = $restController->obtenerCotizacion($currentDate, $currentDate, "EUR");
		if($responseRestEUR->result == 2){
			$response->EUR = bcdiv($responseRestEUR->currentQuote, '1', 4);
		} else
			$error = true;
		if(!$error)
			$response->result = 2;
		else
			$response->result = 0;
		return $response;
	}

}


?>