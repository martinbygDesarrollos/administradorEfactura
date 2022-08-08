<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app){
    $container = $app->getContainer();

	$app->get('/empresas', function ($request, $response, $args) use ($container){
		$args['version'] = FECHA_ULTIMO_PUSH;
        $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];

		if ( isset($_SESSION['rutUserLogued']) && isset($_SESSION['mailUserLogued']) ){
			return $this->view->render($response, "business.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Business");

}

?>