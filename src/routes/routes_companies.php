<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_companies.php';

return function (App $app){
    $container = $app->getContainer();
    $companiesController = new ctr_companies();

    //ver el perfil/info detallada de la empresa
    $app->get('/empresas/{rut}', function ($request, $response, $args) use ($container, $companiesController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        $args['permisos'] = $_SESSION['permissionsUserLogued'];
        if ( isset($_SESSION['mailUserLogued']) ){

            $company = $companiesController->getCompaniesData($args['rut'])->objectResult;
            $args["company"] = $company;

            $args['files'] = false;
            if (count(scandir(dirname(dirname(__DIR__)) . "/public/files/")) > 2) {
                $args['files'] = true;
            }


            return $this->view->render($response, "companyDetail.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    });


    /*

    //ver el perfil/info detallada de la empresa
    $app->get('/empresas/{rut}', function ($request, $response, $args) use ($container, $companiesController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
        $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
        $args['permisos'] = $_SESSION['permissionsUserLogued'];
        if ( isset($_SESSION['mailUserLogued']) ){

            $company = $companiesController->getCompaniesData($args['rut'])->objectResult;
            $args["company"] = $company;

            $args['files'] = 0;
            $archivosemitidos = scandir(dirname(dirname(__DIR__)) . "/public/temp/".$_SESSION["rutUserLogued"]."emit");
            $cantArchivosemitidos = 0;

            $archivosrecibidos = scandir(dirname(dirname(__DIR__)) . "/public/temp/".$_SESSION["rutUserLogued"]."receipt");
            $cantArchivosrecibidos = 0;

            if ( $archivosemitidos ){

                if ( count($archivosemitidos) > 2 ) {
                    $names_files = array();

                    foreach ($archivosemitidos as $key => $value) {
                        if ( !is_dir($value) ){
                            array_push($names_files, $value);
                        }
                    }

                    $args['names_files_emited'] = $names_files;
                    $cantArchivosemitidos = count($archivosemitidos)-2;
                }

            }

            if ( $archivosrecibidos ){
                if ( count($archivosrecibidos) > 2 ) {
                    $names_files = array();

                    foreach ($archivosrecibidos as $key => $value) {
                        if ( !is_dir($value) ){
                            array_push($names_files, $value);
                        }
                    }

                    $args['names_files_receipt'] = $names_files;
                    $cantArchivosrecibidos = count($archivosrecibidos) -2;

                }
            }


            $args['files'] = $cantArchivosemitidos + $cantArchivosrecibidos;
            return $this->view->render($response, "companyDetail.twig", $args);
        }else return $response->withRedirect($request->getUri()->getBaseUrl());
    });

    */

    //cambiar los datos de sesion del usuario
    $app->post('/companies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            $rut = $data['rut'];
            $razonSocial = $data['name'];


            $company = $companiesController->getCompaniesData($rut);
            $_SESSION['companieUserLogued'] = $company->objectResult->razonSocial;
            $_SESSION['rutUserLogued'] = $company->objectResult->rut;

            return json_encode($company);
        }else return json_encode(["result"=>0]);
    });



    $app->post('/loadCompanies', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $data = $request->getParams();
            $lastid = $data['lastid'];

            $filter = $data['filter'];
            $namecompanie = $filter[0];
            $namecompanieup = strtoupper($filter[0]);

            $status = $filter[1];$arrayStatus = array();
            if ( isset($status) && $status != "" ){
                $arrayStatus = explode(", ", $status);
            }


            $companies = array();

            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ( $_SESSION['companiesList'] as $key => $value) {
                   $pos = strpos($value->razonSocial, $namecompanie);
                   $pos1 = strpos($value->razonSocial, $namecompanieup);
                   $pos2 = strpos($value->rut, $namecompanie);


                    if ($pos !== false || $pos1 !== false || $pos2 !== false) {

                        if ( isset($status) && $status != "" ){
                            $posStatus = strpos($value->estado, $status);

                            if ($posStatus !== false){
                                array_push($companies, $value);

                            }

                        }else{
                            array_push($companies, $value);
                        }
                    }
                }

                $response->companiesList = array_slice($companies,$lastid,15);


                if ($lastid + 15 > count($companies)){
                    $lastid = count($companies);
                } else
                    $lastid = $lastid + 15;
            }
            else if ( isset($arrayStatus) && count($arrayStatus) >0 ){

                foreach ( $_SESSION['companiesList'] as $key => $value) {
                    foreach ($arrayStatus as $valStatus) {
                        $posStatus = strpos($value->estado, $valStatus);

                        if ($posStatus !== false){
                            array_push($companies, $value);

                        }
                    }


                }

                $response->companiesList = array_slice($companies,$lastid,15);


                if ($lastid + 15 > count($companies)){
                    $lastid = count($companies);
                } else
                    $lastid = $lastid + 15;

            }
            else{
                $response->companiesList = array_slice($_SESSION['companiesList'],$lastid,15);

                if ($lastid + 15 > count($_SESSION['companiesList'])){
                    $lastid = count($_SESSION['companiesList']);
                } else
                    $lastid = $lastid + 15;
            }






            $response->lastid = $lastid;
            return json_encode($response);
        }else return json_encode(["result"=>2]);
    });




    $app->post('/loadCompaniesByName', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $companies = array();

            $data = $request->getParams();
            $namecompanie = $data['name'];
            $namecompanieup = strtoupper($data['name']);

            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ( $_SESSION['companiesList'] as $key => $value) {
                   $pos = strpos($value->razonSocial, $namecompanie);
                   $pos1 = strpos($value->razonSocial, $namecompanieup);
                   $pos2 = strpos($value->rut, $namecompanie);


                    if ($pos !== false || $pos1 !== false || $pos2 !== false) {
                        array_push($companies, $value);
                    }
                }
            }


            $response->companiesList = $companies;
            return json_encode($response);
        }else json_encode(["result"=>0]);
    });



    $app->post('/loadCompaniesByStatus', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $companies = array();

            $data = $request->getParams();
            $namecompanie = $data['name'];
            $namecompanieup = strtoupper($data['name']);

            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ( $_SESSION['companiesList'] as $key => $value) {
                   $pos = strpos($value->razonSocial, $namecompanie);
                   $pos1 = strpos($value->razonSocial, $namecompanieup);
                   $pos2 = strpos($value->rut, $namecompanie);


                    if ($pos !== false || $pos1 !== false || $pos2 !== false) {
                        array_push($companies, $value);
                    }
                }
            }


            $response->companiesList = $companies;
            return json_encode($response);
        }else json_encode(["result"=>0]);
    });





    $app->post('/loadBranchData', function ($request, $response, $args) use ($container, $companiesController){
        $response = new \stdClass();

        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $branchCode = $data['branch'];
            $rut = $data['companie'];

            $companie = $companiesController->getCompaniesData($rut);
            if ( $companie->result == 2 ){
                foreach ($companie->objectResult->sucursales as $key => $value) {
                    if($value->codDGI == $branchCode){
                        $response->result = 2;
                        $response->objectResult = $value;
                        return json_encode($response);
                    }
                }
            }else
                return json_encode($companie);
        }else return json_encode(["result"=>0]);
    });



    $app->post('/getCaesByCompanie', function ($request, $response, $args) use ($container, $companiesController){
        $response = new \stdClass();

        if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['companie'];

            $companie = $companiesController->getCompaniesData($rut);
            if ( $companie->result == 2 ){
                if ( isset($companie->objectResult->caes) ){
                    $response->result = 2;
                    $response->listResult = $companie->objectResult->caes;
                }else{
                    $response->result = 2;
                    $response->listResult = array();
                }
                return json_encode($response);
            }else
                return json_encode($companie);
        }else return json_encode(["result"=>0]);
    });




    //editar los datos basicos de la sucursal
    $app->post('/changeCompanieData', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $response = $companiesController->changeCompanieData($data);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });



    $app->post('/changeCompanieColor', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $response = $companiesController->changeCompanieColors($data);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });


    //cambiar estado de la empresa
    $app->post('/changeStatusCompanie', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $newStatus = $data['newStatus'];
            $rut = $_SESSION['rutUserLogued'];


            $response = $companiesController->changeStatusCompanie($newStatus, $rut);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });




    $app->post('/loadResolutions', function ($request, $response, $args) use ($container, $companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $rut = $_SESSION['rutUserLogued'];

            $response = $companiesController->loadResolutions($rut, $data);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });


    $app->post('/representacionimpresa', function ($request, $response, $args) use ($companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $rut = $_SESSION['rutUserLogued'];
            var_dump("este es el post");

            $response = $companiesController->getRepresentacionImpresa($rut);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });


    $app->put('/representacionimpresa', function ($request, $response, $args) use ($companiesController){

        if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $rut = $_SESSION['rutUserLogued'];

            var_dump("este es el put");

            $response = $companiesController->getRepresentacionImpresa($rut);
            return json_encode($response);

        }else return json_encode(["result"=>0]);
    });

}

?>