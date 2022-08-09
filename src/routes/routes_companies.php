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
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];

		if ( isset($_SESSION['mailUserLogued']) ){

            //aca cargar companies
            $args['companiesList'] = $companiesController->getCompanies()->listResult;
            //var_dump($args['companiesList']);exit;
            $_SESSION['companieUserLogued'] = $args['companiesList'][0]->razonSocial;
            $_SESSION['rutUserLogued'] = $args['companiesList'][0]->rut;

            if ( isset($_SESSION['companieUserLogued']) ){
                $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
            }else{
                $_SESSION['companieUserLogued'] = null;
                $args['companieUserLogued'] = null;
            }


            if ( isset($_SESSION['rutUserLogued']) ){
                $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            }else{
                $_SESSION['rutUserLogued'] = null;
                $args['rutUserLogued'] = null;
            }

			return $this->view->render($response, "companies.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Companies");

    $app->get('/empresas/{rut}', function ($request, $response, $args) use ($container, $companiesController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        var_dump($args['rut']);
    });





    $app->post('/companies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $data['rut'];
            //$result = $companiesController->getCompanieByRut($rut);
            return json_encode("ok");
        }else return $response->withRedirect($request->getUri()->getBaseUrl());

    });

}

?>