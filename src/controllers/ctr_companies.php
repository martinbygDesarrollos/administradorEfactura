<?php

//require_once '../src/class/users.php';
require_once 'ctr_rest.php';


class ctr_companies{

	public function getCompanies(){
		$restController = new ctr_rest();
		$responseCompanies = $restController->getCompanies();
		return $responseCompanies;
	}
}


?>