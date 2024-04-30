<?php

require_once '../src/class/utils.php';
require_once '../src/utils/validate.php';
require_once 'ctr_rest.php';


class ctr_companies{

	public function getCompanies(){
		$companieController = new ctr_companies();
		$restController = new ctr_rest();
		$utilClass = new utils();
		$responseCompanies = $restController->getCompanies();


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
			if ( strlen($expireDate) >0 )
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

}


?>