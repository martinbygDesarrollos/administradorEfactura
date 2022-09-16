<?php

use Slim\App;
use Slim\Http\Response;

require_once 'controllers/ctr_companies.php';

return function (App $app) {
    $container = $app->getContainer();
    $companiesController = new ctr_companies();

    $routesUsers = require_once __DIR__ . "/../src/routes/routes_users.php";
    $routesCompanies = require_once __DIR__ . "/../src/routes/routes_companies.php";
    $routesEmited = require_once __DIR__ . "/../src/routes/routes_emited.php";

    $routesUsers($app);
    $routesCompanies($app);
    $routesEmited($app);

    //ruta de inicio
    $app->get('/', function ($request, $response, $args) use ($container, $companiesController) {
        $args['version'] = FECHA_ULTIMO_PUSH;
        if ( isset($_SESSION['mailUserLogued']) ){
            $args['mailUserLogued'] = $_SESSION['mailUserLogued'];


            if( isset($_SESSION['companiesList'] ) ){
                //aca cargar companies
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

                $args["company"] = null;
                if ( isset($_SESSION['rutUserLogued']) ){
                    $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

                    $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
                    $args["company"] = $company;

                }else{
                    $_SESSION['rutUserLogued'] = null;
                    $args['rutUserLogued'] = null;
                }


            }else{
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

                $args["company"] = null;
                if ( isset($_SESSION['rutUserLogued']) ){
                    $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

                    $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
                    $args["company"] = $company;

                }else{
                    $_SESSION['rutUserLogued'] = null;
                    $args['rutUserLogued'] = null;
                }


            }

        }else {
            $args['rutUserLogued'] = null;
            $args['mailUserLogued'] = null;
            $args['companieUserLogued'] = null;
        }

        return $this->view->render($response, "companies.twig", $args);
    })->setName("Start");
};
