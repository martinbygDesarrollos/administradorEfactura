<?php

use Slim\App;
use Slim\Http\Response;

//require_once '../src/controllers/ctr_users.php';

return function (App $app) {
    $container = $app->getContainer();

    $routesUsers = require_once __DIR__ . "/../src/routes/routes_users.php";
    $routesCompanies = require_once __DIR__ . "/../src/routes/routes_companies.php";

    $routesUsers($app);
    $routesCompanies($app);

    //ruta de inicio
    $app->get('/', function ($request, $response, $args) use ($container) {
        $args['version'] = FECHA_ULTIMO_PUSH;
        if ( isset($_SESSION['mailUserLogued']) && isset($_SESSION['rutUserLogued']) ){
            $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        }else {
            $args['rutUserLogued'] = null;
            $args['mailUserLogued'] = null;
        }


        return $this->view->render($response, "companies.twig", $args);
    })->setName("Start");
};
