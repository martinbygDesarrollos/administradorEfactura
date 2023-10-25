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

	$app->post('/loadListUser', function ($request, $response, $args) use ($usersController){ // ARREGLARRRR
        $response = new \stdClass();

        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
			
            $usersResponse = $usersController->loadListUser($rut);
			
            if ( $usersResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $usersResponse->objectResult;
				return json_encode($response);
				
            }else
                return json_encode($response);
        }else return json_encode(["result"=>0]);
    });

	$app->post('/loadUserDetails', function ($request, $response, $args) use ($usersController){
        $response = new \stdClass();
        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $email = $data['email'];
			
            $usersResponse = $usersController->loadUser($rut, $email);
			// var_dump($usersResponse);
			// exit;
            if ( $usersResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $usersResponse->objectResult;
				return json_encode($response);
            } else {
                return json_encode($response);
			}
        }else return json_encode(["result"=>0]);
    });
	
	$app->post('/updateUser', function ($request, $response, $args) use ($usersController){
        $response = new \stdClass();
        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $email = $data['email'];
            $name = $data['name'];
			$active = $data['active'] === 'true'? true: false;
            $cellphone = $data['cellphone'];
            $scopes = $data['scopes'];
			// var_dump($email);
			// var_dump($name);
			// var_dump($active);
			// var_dump($cellphone);
			// var_dump($scopes);
			// exit;

            $usersResponse = $usersController->updateUser($rut, $email, $name, $active, $cellphone, $scopes);
			// var_dump($usersResponse);
			// exit;
            // if ( $usersResponse->result == 2 ){
			// 	$response->result = 2;
			// 	$response->objectResult = $usersResponse->objectResult;
			// 	$response->message = $usersResponse->message;
				// return json_encode($response);
            // } else {
                return json_encode($usersResponse);
			// }
        }else return json_encode(["result"=>0]);
    });

	$app->post('/newUser', function ($request, $response, $args) use ($usersController){
        // $response = new \stdClass();
        if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $data['rut'];
            $email = $data['email'];
            $name = $data['name'];
            $cellphone = $data['cellphone'];
            $scopes = $data['scopes'];
            $usersResponse = $usersController->newUser($rut, $email, $name, $cellphone, $scopes);
			return json_encode($usersResponse);
        }else return json_encode(["result"=>0]);
    });
	
	$app->post('/updatePassword', function ($request, $response, $args) use ($usersController){
        if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $data['rut'];
            $email = $data['email'];
            $usersResponse = $usersController->updateUserPassword($rut, $email);
			return json_encode($usersResponse);
        }else return json_encode(["result"=>0]);
    });

}

?>