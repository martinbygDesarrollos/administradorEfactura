<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

//require_once '../src/controllers/ctr_users.php';
require_once '../src/controllers/ctr_companies.php';


return function (App $app){
    $container = $app->getContainer();
    $companiesController = new ctr_companies();



    $app->get('/usuarios', function ($request, $response, $args) use ($container, $companiesController){
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
            return $this->view->render($response, "usersCompanie.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    })->setName("Usuarios");

}

?>