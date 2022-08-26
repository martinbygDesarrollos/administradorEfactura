<?php

require_once '../src/class/utils.php';
require_once 'ctr_rest.php';


class ctr_companies{

	public function getCompanies(){
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
			$responseCompanies->listResult[$key]->logo = "";
			$responseCompanies->listResult[$key]->proxVencDescr = $expireDate;

			$auxExpireDate = "";
			if ( strlen($expireDate) >0 )
				$auxExpireDate = substr($expireDate,6,2)."/".substr($expireDate,4,2)."/".substr($expireDate,0,4);

			$responseCompanies->listResult[$key]->proxVencimiento = $auxExpireDate;
			$responseCompanies->listResult[$key]->comprobante = $expireDateVoucher;
			$responseCompanies->listResult[$key]->tipoCae = $expireDateCaeType;

		}

		$responseCompanies->listResult[1]->proxVencDescr = "";
		$responseCompanies->listResult[1]->proxVencimiento = "";
		$responseCompanies->listResult[1]->comprobante = "";
		$responseCompanies->listResult[1]->tipoCae = "";
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

	//de lo que esta en memoria
	public function getBranchCompanieData($branchCode, $companie){
		$response = new \stdClass();
		$response->result = 1;
		$response->sucursal = array();

		foreach ($_SESSION['companiesList'] as $key => $value) {
			if ( $value->rut == $companie ){
				foreach ($value->sucursales as $index => $sucursal) {
					if ($sucursal->codDGI == $branchCode) {
						$response->result = 2;
						$response->sucursal = $sucursal;
					}
				}
			}

		}
		return $response;
	}
}


?>