<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_companies.php';

return function (App $app){
    $container = $app->getContainer();
    $companiesController = new ctr_companies();


	$app->get('/empresas', function ($request, $response, $args) use ($container, $companiesController){
		$args['version'] = FECHA_ULTIMO_PUSH;
        $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = null;

		if ( isset($_SESSION['rutUserLogued']) && isset($_SESSION['mailUserLogued']) ){

			//aca pedir datos de companies
            $args['companiesList'] = $companiesController->getCompanies()->listResult;
           	foreach ($args['companiesList'] as $value) {
                if ($value->rut == $rut) {
                    $_SESSION['companieUserLogued'] = $value->razonSocial;
                    $args['companieUserLogued'] = $value->razonSocial;
                }else {
                	$_SESSION['companieUserLogued'] = null;
                    $args['companieUserLogued'] = null;
                }
            }


			return $this->view->render($response, "companies.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Companies");

}

?>