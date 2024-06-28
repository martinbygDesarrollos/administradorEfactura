<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_emited.php';
require_once '../src/controllers/ctr_companies.php';
require_once '../src/controllers/ctr_users.php';


return function (App $app){
    $container = $app->getContainer();
    $emitedController = new ctr_emited();
    $companiesController = new ctr_companies();
    $usersController = new ctr_users();



    $app->get('/emitidos', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
            $args['version'] = FECHA_ULTIMO_PUSH;
            $args['sistemSession'] = $responseCurrentSession->currentSession;
            // $args['mailUserLogued'] = $responseCurrentSession->currentSession->mailUserLogued;// $_SESSION['mailUserLogued'];
            // $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
            // $args["company"] = null;
            // if ( isset($_SESSION['rutUserLogued']) ){
            // $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            // $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
            $company = $companiesController->getCompaniesData($responseCurrentSession->currentSession->mailUserLogued)->objectResult;
            $args["company"] = $company;
            // }else{
            //     $_SESSION['rutUserLogued'] = null;
            //     $args['rutUserLogued'] = null;
            // }
            // if ( isset($_SESSION['mailUserLogued']) )
            return $this->view->render($response, "listemited.twig", $args);
        } else return $response->withRedirect($request->getUri()->getBaseUrl());
    })->setName("Emitidos");


    ///////////////////////////////////////////////////////////////////////////////////77


    $app->post('/resendXml', function ($request, $response, $args) use ($container, $emitedController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            // $rut = $_SESSION['rutUserLogued'];
            $rut = $responseCurrentSession->currentSession->mailUserLogued;
            $response = $emitedController->resendXml($rut, $data);
            return json_encode($response);
        }else return json_encode($responseCurrentSession);

    });





    $app->post('/getDateLastCfeReceipt', function ($request, $response, $args) use ($container, $emitedController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = file_get_contents('https://sigecom.uy/erp/public/ultcfe.txt');
            return json_encode($response);
        }else return json_encode($responseCurrentSession);

    });




    $app->post('/getReportsByCompanie', function ($request, $response, $args) use ($container, $emitedController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $_SESSION['rutUserLogued'];
            $periodo = $data['date'];
            $response = $emitedController->getReportsByCompanie($rut);
            return json_encode($response);
        }else return json_encode($responseCurrentSession);

    });




    $app->post('/importCfeEmitedXml', function ($request, $response, $args) use ($container, $emitedController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $responseImport = $emitedController->importCfeEmitedXml($_FILES);
            return json_encode( $responseImport );

        }else return json_encode($responseCurrentSession);

    });


    $app->post('/loadCompanyEmitidos', function (){// NO SE USA AL PARECER
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $responseImport = $emitedController->importCfeEmitedXml($_FILES);
            return json_encode( $responseImport );

        }else return json_encode($responseCurrentSession);
    });

    
    /*
    
    $app->post('/sendImportCfeEmitedXml', function ($request) use ($emitedController){
        
        if ( $_SESSION['mailUserLogued'] ){
            
            $data = $request->getParams();
            $folder = $_SESSION['rutUserLogued'].$data['folder'];
            $file = $data['file'];
            //$responseImport = $emitedController->importCfeEmitedXml($_FILES);
            return json_encode( ["result"=>2] );
            
        }else return json_encode(["result"=>0]);
    });
    
    
    */
    $app->get('/emisores', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
            $args['version'] = FECHA_ULTIMO_PUSH;
            $args['sistemSession'] = $responseCurrentSession->currentSession;
            $company = $companiesController->getCompaniesData($responseCurrentSession->currentSession->mailUserLogued)->objectResult;
            $args["company"] = $company;

            return $this->view->render($response, "emisores.twig", $args);
        // $args['version'] = FECHA_ULTIMO_PUSH;
        // $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        // $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        // $args["company"] = null;
        // if ( isset($_SESSION['rutUserLogued']) ){
        //     $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            
        //     $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
        //     $args["company"] = $company;
            
        // }else{
        //     $_SESSION['rutUserLogued'] = null;
        //     $args['rutUserLogued'] = null;
        // }
        // if ( isset($_SESSION['mailUserLogued']) ){
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    })->setName("Emisores");

    $app->post('/loadEmisores', function ($request, $response, $args) use ($container, $emitedController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $ruc = $data['ruc'];
            // var_dump($data);
            $responseEmisores = $emitedController->loadEmisores($ruc);
            return json_encode( $responseEmisores );

        }else return json_encode($responseCurrentSession);
    });
    
}
    ?>