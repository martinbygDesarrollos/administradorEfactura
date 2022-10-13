<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_emited.php';

return function (App $app){
    $container = $app->getContainer();
    $emitedController = new ctr_emited();


    $app->post('/resendXml', function ($request, $response, $args) use ($container, $emitedController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $rut = $_SESSION['rutUserLogued'];

            $response = $emitedController->resendXml($rut, $data);
            return json_encode($response);

        }else return json_encode(["result"=>0]);

    });





    $app->post('/getDateLastCfeReceipt', function ($request, $response, $args) use ($container, $emitedController){

        if ( $_SESSION['mailUserLogued'] ){


            $response = file_get_contents('https://sigecom.uy/erp/public/ultcfe.txt');
            return json_encode($response);

        }else return json_encode(["result"=>0]);

    });




    $app->post('/getReportsByCompanie', function ($request, $response, $args) use ($container, $emitedController){

        if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $rut = $_SESSION['rutUserLogued'];
            $periodo = $data['date'];
            $response = $emitedController->getReportsByCompanie($rut);
            return json_encode($response);

        }else return json_encode(["result"=>0]);

    });




    $app->post('/importCfeEmitedXml', function ($request, $response, $args) use ($container, $emitedController){

        if ( $_SESSION['mailUserLogued'] ){

            $responseImport = $emitedController->importCfeEmitedXml($_FILES);
            return json_encode( $responseImport );

        }else return json_encode(["result"=>0]);

    });
}

?>