<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

//require_once '../src/controllers/ctr_users.php';
require_once '../src/controllers/ctr_companies.php';
require_once '../src/controllers/ctr_receipt.php';

return function (App $app){
    $container = $app->getContainer();

    //$usersController = new ctr_users();
    $companiesController = new ctr_companies();
    $receiptController = new ctr_receipt();


	$app->get('/recibidos', function ($request, $response, $args) use ($container, $companiesController){

		$args['version'] = FECHA_ULTIMO_PUSH;
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        $args["company"] = null;
        if ( isset($_SESSION['rutUserLogued']) ){
            $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

            $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
            $args["company"] = $company;

        }else{
            $_SESSION['rutUserLogued'] = null;
            $args['rutUserLogued'] = null;
        }


        if ( isset($_SESSION['mailUserLogued']) ){
            return $this->view->render($response, "receipt.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Recibidos");


    $app->post('/importCfeReceiptXml', function ($request, $response, $args) use ($container, $receiptController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            return json_encode($receiptController->importCfeReceiptXml($_FILES));

        }else return json_encode(["result"=>0]);

    });

}

?>