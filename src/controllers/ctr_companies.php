<?php

//require_once '../src/class/users.php';
require_once 'ctr_rest.php';


class ctr_companies{

	public function getCompanies(){
		$restController = new ctr_rest();
		$responseCompanies = $restController->getCompanies();
		foreach ($responseCompanies->listResult as $key => $value) {
			if (strlen($value->certificateExpireDate)>0){
				$responseCompanies->listResult[$key]->vencimientos = substr($value->certificateExpireDate,8,2)."/".substr($value->certificateExpireDate,5,2)."/".substr($value->certificateExpireDate,0,4);
			}else $responseCompanies->listResult[$key]->vencimientos = "";
		}

		return $responseCompanies;
	}
}


?>