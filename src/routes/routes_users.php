<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_users.php';
// require_once '../src/controllers/ctr_companies.php';

return function (App $app){
    $container = $app->getContainer();

    $usersController = new ctr_users();
    // $companiesController = new ctr_companies();

	$app->get('/iniciar-sesion', function ($request, $response, $args) use ($container){
		return $this->view->render($response, "signIn.twig", $args);
	})->setName("SignIn");

	// $app->get('/cerrar-session', function ($request, $response, $args) use ($container, $usersController){
    //     if ( $_SESSION['mailUserLogued'] ){
    //         $responseCall = $usersController->logout($_SESSION['mailUserLogued']);
    //         if($responseCall->result == 2){
    //             $_SESSION['mailUserLogued'] = null;
    //             $_SESSION['rutUserLogued'] = null;
    //             $_SESSION['companieUserLogued'] = null;
    //             $_SESSION['companiesList'] = null;
    //             $_SESSION['lastID'] = null;
    //         }
    //     }
	// 	return $response->withRedirect($request->getUri()->getBaseUrl());
	// })->setName("SignOut");

    $app->get('/cerrar-session', function ($request, $response, $args) use ($container, $usersController) {
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
            $responseLogout = $usersController->logout();
            // if($responseLogout->result == 2)
            // session_destroy();
        }
		return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("SignOut");


////////////////////////////////////////////////////////////////////////////////////7

	$app->post('/login', function ($request, $response, $args) use ($usersController){
        $responseCurrentSession = $usersController->validateSession();
		// return json_encode($responseCurrentSession->message);
		if($responseCurrentSession->result != 2){ // O sea que no hay session seteada (Si devuelve 0)
            // var_dump($responseCurrentSession);
            $data = $request->getParams();
            $correo = $data['correo'];
            $contra = $data['contra'];
            $entorno = $data['entorno'];
            // $entorno = null;
            $force = (strtolower($data['force']) === "false") ? false : true;
            // $responseCall = $companiesController->getCompanies($correo);
            // var_dump($entorno);
            // exit;
            // if($responseCall->result == 2){
            $result = $usersController->login($correo, $contra, $entorno, $force);
            // exit;
            return json_encode($result);
            // }
		} else {
            //SI hay una session
            return json_encode($responseCurrentSession);
        }
    });

	$app->post('/loadListUser', function ($request, $response, $args) use ($usersController){ // ARREGLARRRR
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $data['rut'];
            $usersResponse = $usersController->loadListUser($rut);
            if ( $usersResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $usersResponse->objectResult;
				return json_encode($response);
            }else {
                return json_encode($usersResponse);
            }
        }else return json_encode(["result"=>0]);
    });

	$app->post('/loadUserDetails', function ($request, $response, $args) use ($usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

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
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

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
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
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
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $data['rut'];
            $email = $data['email'];
            $pwd = $data['pwd'];
            $usersResponse = $usersController->updateUserPassword($rut, $email, $pwd);
			return json_encode($usersResponse);
        }else return json_encode(["result"=>0]);
    });

}

?>