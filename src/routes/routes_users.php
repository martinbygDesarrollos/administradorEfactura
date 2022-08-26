<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_users.php';

return function (App $app){
    $container = $app->getContainer();

    $usersController = new ctr_users();

	$app->get('/iniciar-sesion', function ($request, $response, $args) use ($container){

		return $this->view->render($response, "signIn.twig", $args);
	})->setName("SignIn");

	$app->get('/cerrar-session', function ($request, $response, $args){
		$_SESSION['mailUserLogued'] = null;
		$_SESSION['rutUserLogued'] = null;
		$_SESSION['companieUserLogued'] = null;
		$_SESSION['companiesList'] = null;
		$_SESSION['lastID'] = null;
		return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("SignOut");

////////////////////////////////////////////////////////////////////////////////////7

	$app->post('/login', function ($request, $response, $args) use ($usersController){
		$data = $request->getParams();
		$correo = $data['correo'];
		$contra = $data['contra'];

		$result = $usersController->login($correo, $contra);
		return json_encode($result);
	});

}

?>