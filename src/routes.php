<?php

use Slim\App;
use Slim\Http\Response;

//require_once '../src/controllers/ctr_users.php';

return function (App $app) {
    $container = $app->getContainer();

    $routesUsers = require_once __DIR__ . "/../src/routes/routes_users.php";

    $routesUsers($app);

    //ruta de inicio
    $app->get('/', function ($request, $response, $args) use ($container) {
        $args['version'] = FECHA_ULTIMO_PUSH;
        $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];

        return $this->view->render($response, "index.twig", $args);
    })->setName("Start");
};
