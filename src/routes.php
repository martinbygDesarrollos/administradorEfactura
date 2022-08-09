<?php

use Slim\App;
use Slim\Http\Response;

require_once 'controllers/ctr_companies.php';

return function (App $app) {
    $container = $app->getContainer();
    $companiesController = new ctr_companies();

    $routesUsers = require_once __DIR__ . "/../src/routes/routes_users.php";
    $routesCompanies = require_once __DIR__ . "/../src/routes/routes_companies.php";

    $routesUsers($app);
    $routesCompanies($app);

    //ruta de inicio
    $app->get('/', function ($request, $response, $args) use ($container, $companiesController) {
        $args['version'] = FECHA_ULTIMO_PUSH;
        if ( isset($_SESSION['mailUserLogued']) && isset($_SESSION['rutUserLogued']) ){
            $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            $args['mailUserLogued'] = $_SESSION['mailUserLogued'];

            //aca cargar companies
            $args['companiesList'] = $companiesController->getCompanies()->listResult;
            $rut = $_SESSION['rutUserLogued'];
            //var_dump($args['companiesList']);exit;
            foreach ($args['companiesList'] as $value) {
                if ($value->rut == $rut) {
                    $_SESSION['companieUserLogued'] = $value->razonSocial;
                }
            }

            if ( isset($_SESSION['companieUserLogued']) ){
                $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
            }else{
                $_SESSION['companieUserLogued'] = null;
                $args['companieUserLogued'] = null;
            }
        }else {
            $args['rutUserLogued'] = null;
            $args['mailUserLogued'] = null;
            $args['companieUserLogued'] = null;
        }

        return $this->view->render($response, "companies.twig", $args);
    })->setName("Start");
};
