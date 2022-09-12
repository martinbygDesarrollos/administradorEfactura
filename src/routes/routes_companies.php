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


            if ( isset($_SESSION['companiesList']) ){
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
            }
            else{

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



    //seccion de comprobantes enviados, por ahora funcionamiento de reenvio de sobres
    $app->get('/facturacion', function ($request, $response, $args) use ($container, $companiesController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        if ( isset($_SESSION['mailUserLogued']) ){
            return $this->view->render($response, "emited.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    })->setName("Emited");



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
        }else return json_encode(["result"=>0]);
    });



    $app->post('/loadCompanies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $data = $request->getParams();
            /*$lastid = $data['lastid'];

            if ( isset($lastid) && $lastid == 0){
                $_SESSION['lastID'] = $lastid;
            }*/

            $namecompanie = $data['namecompanie'];
            $namecompanieup = strtoupper($data['namecompanie']);


            $companies = $_SESSION['companiesList'];
            $arrayAuxCompanies = array();


            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ($_SESSION['companiesList'] as $key => $value) {
                   $pos = strpos($value->razonSocial, $namecompanie);
                   $pos1 = strpos($value->razonSocial, $namecompanieup);

                    if ($pos !== false || $pos1 !== false) {
                        array_push($arrayAuxCompanies, $value);
                    }
                }

                $companies = $arrayAuxCompanies;
            }


            $response->companiesList = array_slice($companies,$_SESSION['lastID'],15);

            if ($_SESSION['lastID'] + 15 > count($companies)){
                $_SESSION['lastID'] = count($companies);
            } else
                $_SESSION['lastID'] = $_SESSION['lastID'] + 15;



            $response->lastid = $_SESSION['lastID'];
            return json_encode($response);
        }else json_encode(["result"=>0]);
    });


    $app->post('/loadBranchData', function ($request, $response, $args) use ($container, $companiesController){
        $response = new \stdClass();

        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $branchCode = $data['branch'];
            $rut = $data['companie'];

            $companie = $companiesController->getCompaniesData($rut);
            if ( $companie->result == 2 ){
                foreach ($companie->objectResult->sucursales as $key => $value) {
                    if($value->codDGI == $branchCode){
                        $response->result = 2;
                        $response->objectResult = $value;
                        return json_encode($response);
                    }
                }
            }else
                return json_encode($companie);
        }else return json_encode(["result"=>0]);
    });



    $app->post('/getCaesByCompanie', function ($request, $response, $args) use ($container, $companiesController){
        $response = new \stdClass();

        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['companie'];

            $companie = $companiesController->getCompaniesData($rut);
            if ( $companie->result == 2 ){
                if ( isset($companie->objectResult->caes) ){
                    $response->result = 2;
                    $response->listResult = $companie->objectResult->caes;
                }else{
                    $response->result = 2;
                    $response->listResult = array();
                }
                return json_encode($response);
            }else
                return json_encode($companie);
        }else return json_encode(["result"=>0]);
    });




    //editar los datos basicos de la sucursal
    $app->post('/changeCompanieData', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $response = $companiesController->changeCompanieData($data);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });

}

?>