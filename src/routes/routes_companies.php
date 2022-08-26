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

		if ( isset($_SESSION['mailUserLogued']) ){
            $args['mailUserLogued'] = $_SESSION['mailUserLogued'];

            //aca cargar companies
            $_SESSION['companiesList'] = $companiesController->getCompanies()->listResult;
            $_SESSION['lastID'] = 0;
            $args['companiesList'] = $_SESSION['companiesList'];

            if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
                $objFirstCompanie = array_pop(array_reverse($args['companiesList']));

                $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
                $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
            }

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

    //ver el perfil/info detallada de la empresa
    $app->get('/empresas/{rut}', function ($request, $response, $args) use ($container, $companiesController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        if ( isset($_SESSION['mailUserLogued']) ){

            $company = $companiesController->getCompaniesData($args['rut'])->objectResult;
            $args["company"] = $company;

            return $this->view->render($response, "companyDetail.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    });




    //cambiar los datos de sesion del usuario
    $app->post('/companies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            $rut = $data['rut'];
            $razonSocial = $data['name'];

            $_SESSION['companieUserLogued'] = $razonSocial;
            $_SESSION['rutUserLogued'] = $rut;

            $response->result = 2;
            return json_encode($response);
        }else {
            $response->result = 1;
            return json_encode($response);
        }
    });



    $app->post('/loadCompanies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;
            $response->companiesList = array_slice($_SESSION['companiesList'],$_SESSION['lastID'],15);

            if ($_SESSION['lastID'] + 15 > count($_SESSION['companiesList'])){
                $_SESSION['lastID'] = count($_SESSION['companiesList']);
            } else
                $_SESSION['lastID'] = $_SESSION['lastID'] + 15;



            $response->lastid = $_SESSION['lastID'];
            return json_encode($response);
        }else {
            $response->result = 1;
            return json_encode($response);
        }
    });
}

?>