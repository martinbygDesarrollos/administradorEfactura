<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_companies.php';
require_once '../src/controllers/ctr_users.php';

return function (App $app){
    $container = $app->getContainer();
    $companiesController = new ctr_companies();
    $usersController = new ctr_users();

    //ver el perfil/info detallada de la empresa
    $app->get('/empresas/{rut}', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
            $args['sistemSession'] = $responseCurrentSession->currentSession;
            $args['version'] = FECHA_ULTIMO_PUSH;
            // $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
            // $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
            // $args['permisos'] = $_SESSION['permissionsUserLogued'];
            // if ( isset($_SESSION['mailUserLogued']) ){
            // $validateSession = $usersController->validateSession($_SESSION['mailUserLogued'], $_SESSION['tokenLocal']);
            // if ( $validateSession->result == 2 ){

            $company = $companiesController->getCompaniesData($args['rut'])->objectResult;
            // $idSucPrincipal = -1;
            // $sucursalesNew = array();
            // foreach ($company->sucursales as $key => $value) {
            //     if($value->isPrincipal)
            //         $idSucPrincipal = $value->codDGI;
            //     else{
            //         if ($value->codDGI == $idSucPrincipal)
            //             continue;
            //     }
            //     $sucursalesNew[] = $value;
            // }
            // $company->sucursales = $sucursalesNew;
            $args["company"] = $company;

            $args['files'] = false;
            if (count(scandir(dirname(dirname(__DIR__)) . "/public/files/")) > 2) {
                $args['files'] = true;
            }

            // var_dump($company->objectResult->giros);
            // exit;
            return $this->view->render($response, "companyDetail.twig", $args);
        } else return $response->withRedirect($request->getUri()->getBaseUrl());
    });

    $app->get('/email/{rut}', function ($request, $response, $args) use ($container, $companiesController){
            $data = $companiesController->createMailgetCaes($args['rut']);
            $mensaje = "";
            if(isset($data->error))
                // $mensaje = $data->error;
                $mensaje = "";
            else {
                $mensaje = "Buenos días Solicitamos CAEs para la empresa " . $data->razonSocial . "  -  " . $data->rut . "<br><br><br>";
                foreach ($data->caes as $cae) {
                    $formattedPedir = number_format($cae->pedir, 0, '.', '.'); // Format number with thousands separator
                    $numberLength = strlen($formattedPedir);
                    $spaces = str_repeat(' ', 10 - $numberLength); // Adjust '10' based on desired total width
                    $mensaje .= "<p style=\" font-family: monospace; font-size: large;\">" . $formattedPedir . "<span style='white-space: pre;'>" . $spaces . " - </span>" . $cae->tipoCFEText . "</p>";
                }
                $mensaje .= "<br><br><br>Desde ya muchas gracias <br>Saludos";
            }
            return $mensaje;
    });
    
    $app->get('/empresas', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $args['version'] = FECHA_ULTIMO_PUSH;
        $responseCurrentSession = $usersController->validateSession();
        if ( $responseCurrentSession->result == 2 ){
        // if ( isset($_SESSION['mailUserLogued']) ){
            // $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
            $args['sistemSession'] = $responseCurrentSession->currentSession;
            // if( isset($_SESSION['companiesList'] ) ){
                //aca cargar companies
                // $_SESSION['lastID'] = 0;
                //$args['companiesList'] = $_SESSION['companiesList'];
                // if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
                //     $objFirstCompanie = array_pop(array_reverse($_SESSION['companiesList']));

                //     $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
                //     $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
                // }
                // if ( isset($_SESSION['companieUserLogued']) ){
                //     $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
                // }else{
                //     $_SESSION['companieUserLogued'] = null;
                //     $args['companieUserLogued'] = null;
                // }
                // $args["company"] = null;
                $company = $companiesController->getCompaniesData($responseCurrentSession->currentSession->rutUserLogued)->objectResult;
                $args["company"] = $company;
                // if ( isset($_SESSION['rutUserLogued']) ){
                //     $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
                //     $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
                //     $args["company"] = $company;
                // }else{
                //     $_SESSION['rutUserLogued'] = null;
                //     $args['rutUserLogued'] = null;
                // }
            // }else{
            //     //aca cargar companies
            //     $_SESSION['companiesList'] = $companiesController->getCompanies()->listResult;
            //     $_SESSION['lastID'] = 0;
            //     //$args['companiesList'] = $_SESSION['companiesList'];
            //     if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
            //         $objFirstCompanie = array_pop(array_reverse($_SESSION['companiesList']));

            //         $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
            //         $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
            //     }
            //     if ( isset($_SESSION['companieUserLogued']) ){
            //         $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
            //     }else{
            //         $_SESSION['companieUserLogued'] = null;
            //         $args['companieUserLogued'] = null;
            //     }
            //     $args["company"] = null;
            //     if ( isset($_SESSION['rutUserLogued']) ){
            //         $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

            //         $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
            //         $args["company"] = $company;
            //     }else{
            //         $_SESSION['rutUserLogued'] = null;
            //         $args['rutUserLogued'] = null;
            //     }
            // }
        } else {
            // $args['rutUserLogued'] = null;
            // $args['mailUserLogued'] = null;
            // $args['companieUserLogued'] = null;
            return $response->withRedirect($request->getUri()->getBaseUrl());
        }
        return $this->view->render($response, "companies.twig", $args);
    })->setName("Empresas");
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
    $app->post('/companies', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            $rut = $data['rut'];
            $razonSocial = $data['name'];


            $company = $companiesController->getCompaniesData($rut);
            // $_SESSION['companieUserLogued'] = $company->objectResult->razonSocial;
            // $_SESSION['rutUserLogued'] = $company->objectResult->rut;
            // $_SESSION['sistemSession']['companieUserLogued'] = $company->objectResult->razonSocial;
            // $_SESSION['sistemSession']['rutUserLogued'] = $company->objectResult->rut;
            $objectSession = $responseCurrentSession->currentSession;
            $objectSession->rutUserLogued = $company->objectResult->rut;
            $objectSession->companieUserLogued = $company->objectResult->razonSocial;
            $_SESSION['sistemSession'] = $objectSession;
            // var_dump($_SESSION['sistemSession']);
            // return json_encode($_SESSION['sistemSession']);
            // exit;
            // var_dump($company->objectResult->giros);
            // exit;
            return json_encode($company);
        }else return json_encode($responseCurrentSession);
    });

    $app->post('/caesFaltantes', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $response = new \stdClass();
            $companies = array();
            // $caesHabilitados = null;
            $companiesList = $responseCurrentSession->currentSession->companies;
            foreach ( $companiesList as $comp) {
                if ( $comp->estado == 6 ){
                    $gruposCaes = null;
                    $companieDetails = $companiesController->getCompaniesData($comp->rut)->objectResult;
                    $caesHabilitados = caesHabilitados($companieDetails);
                    if(count($caesHabilitados) > 0){
                        if (in_array("dgiResolutionEFac", $caesHabilitados)) {
                            $gruposCaes[] = 101;
                            $gruposCaes[] = 102;
                            $gruposCaes[] = 103;
                            $gruposCaes[] = 111;
                            $gruposCaes[] = 112;
                            $gruposCaes[] = 113;
                        }
                        if (in_array("dgiResolutionERes", $caesHabilitados)) {
                            $gruposCaes[] = 182;
                        }
                        if (in_array("dgiResolutionERem", $caesHabilitados)) {
                            $gruposCaes[] = 181;
                        }
                        if (in_array("dgiResolutionEFacExp", $caesHabilitados)) {
                            $gruposCaes[] = 121;
                            $gruposCaes[] = 122;
                            $gruposCaes[] = 123;
                            $gruposCaes[] = 124;
                        }
                        if (in_array("dgiResolutionCtaAjena", $caesHabilitados)) {
                            $gruposCaes[] = 131;
                            $gruposCaes[] = 132;
                            $gruposCaes[] = 133;
                            $gruposCaes[] = 141;
                            $gruposCaes[] = 142;
                            $gruposCaes[] = 143;
                        }
                        if (in_array("dgiResolutionEBolEntrada", $caesHabilitados)) {
                            $gruposCaes[] = 151;
                            $gruposCaes[] = 152;
                            $gruposCaes[] = 153;
                        }
                    }
                    if(count($gruposCaes) != count($comp->caes)){
                        $auxComp = [
                            "rut" => $comp->rut,
                            "razonSocial" => $comp->razonSocial,
                            "caesHabilitados" => $gruposCaes,
                            "caesDisponibles" => $comp->caes
                        ];
                        $companies[] = $auxComp;
                    }
                }
            }
            $response->companiesWithCaesFaltantes = $companies;
            $response->result = 2;
            return json_encode($response);
        }else return json_encode($responseCurrentSession);
    });

    function caesHabilitados($companieDetails){ // calcula por grupo si faltan miembros (Ej grupo basico = 101,102,103,111,112,113) // Mover esta funcion a un controlador 
        $grupos = [];
        if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != ""){
            $grupos[] = "dgiResolutionEFac";
        }
        if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != ""){
            $grupos[] = "dgiResolutionERes";
        }
        if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != ""){
            $grupos[] = "dgiResolutionERem";
        }
        if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != ""){
            $grupos[] = "dgiResolutionEFacExp";
        }
        if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != ""){
            $grupos[] = "dgiResolutionCtaAjena";
        }
        if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != ""){
            $grupos[] = "dgiResolutionEBolEntrada";
        }
        return $grupos;
    }

    $app->post('/loadCompanies', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $companiesList = $responseCurrentSession->currentSession->companies;
        // if ( $_SESSION['mailUserLogued'] ){
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

                foreach ( $companiesList as $key => $value) {
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

                foreach ( $companiesList as $key => $value) {
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
                $response->companiesList = array_slice($companiesList,$lastid,15);

                if ($lastid + 15 > count($companiesList)){
                    $lastid = count($companiesList);
                } else
                    $lastid = $lastid + 15;
            }






            $response->lastid = $lastid;
            return json_encode($response);
        }else return json_encode(["result"=>2]);
    });




    $app->post('/loadCompaniesByName', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $companiesList = $responseCurrentSession->currentSession->companies;
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $companies = array();

            $data = $request->getParams();
            $namecompanie = $data['name'];
            $namecompanieup = strtoupper($data['name']);

            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ( $companiesList as $key => $value) {
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
        }else json_encode($responseCurrentSession);
    });



    $app->post('/loadCompaniesByStatus', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $companiesList = $responseCurrentSession->currentSession->companies;
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $response->result = 2;

            $companies = array();

            $data = $request->getParams();
            $namecompanie = $data['name'];
            $namecompanieup = strtoupper($data['name']);

            if ( isset($namecompanie) && $namecompanie != "" ){

                foreach ( $companiesList as $key => $value) {
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
        }else json_encode($responseCurrentSession);
    });





    $app->post('/loadBranchData', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $branchCode = $data['branch'];
            $rut = $data['companie'];

            $companie = $companiesController->getCompaniesData($rut);
            // var_dump($companie);
            // exit;
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
        }else return json_encode($responseCurrentSession);
    });



    $app->post('/getCaesByCompanie', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

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
        }else return json_encode($responseCurrentSession);
    });

    $app->post('/deleteBranch', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $currentSession = $responseCurrentSession->currentSession;
        // if ( $_SESSION['mailUserLogued'] ){
            $data = $request->getParams();
            $branch = $data['branch'];
            // No lo uso? uso el de la SESSION?
            $companie = $data['companie'];
            $rut = $currentSession->rutUserLogued;//$_SESSION['rutUserLogued'];

            $responseDeleteBranch = $companiesController->deleteCompanieBranch($rut, $branch);
            if ( $responseDeleteBranch->result == 2 ) {
                $response->result = 2;
                $response->message = $responseDeleteBranch->message;
            } else {
                $response->result = 0;
                $response->message = $responseDeleteBranch->message;
            }
            return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/setPrincipalCompanieBranch', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $currentSession = $responseCurrentSession->currentSession;
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            $branch = $data['branch'];
            $rut = $currentSession->rutUserLogued;

            $company = $companiesController->getCompaniesData($rut)->objectResult;
            $sucPrincipal = new \stdClass();
            $newSucPrincipal = new \stdClass();
            
            foreach ( $company->sucursales as $key => $value) {
                if($value->isPrincipal)
                    $sucPrincipal = $value;
                if($value->codDGI == $branch)
                    $newSucPrincipal = $value;
            }
            // var_dump($sucPrincipal);
            // var_dump($newSucPrincipal);
            // exit;
            $response = $companiesController->setExistingBranchLikePrincipal($sucPrincipal, $newSucPrincipal, $rut);
            
            return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/newCompanieBranch', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $currentSession = $responseCurrentSession->currentSession;
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $data = $request->getParams();
            $rut = $currentSession->rutUserLogued;
            $newCodDGI = $data['codDGI'];
            $company = $companiesController->getCompaniesData($rut)->objectResult;
            $sucPrincipal = new \stdClass();
            $codDGIRepetido = false;

            foreach ( $company->sucursales as $key => $value) {
                if($value->isPrincipal)
                    $sucPrincipal = $value;
                if($value->codDGI == $newCodDGI){
                    $codDGIRepetido = true;
                    break;
                }
            }
            if($codDGIRepetido){
                $response->result = 0;
                $response->message = "Error. Codigo DGI repetido";
                return json_encode($response);
            }

            $data['rut'] = $rut;
            $isPrincipal = $data['isPrincipal'] === 'true'? true: false;
            $data['isTemplate'] = $data['isTemplate'] === 'true'? true: false;
            $response = null;
            if($isPrincipal){
                // echo "Nueva principal";
                $response = $companiesController->newPrincipalCompanieBranch( $sucPrincipal, $data );
            } else {
                // echo "Nueva secundaria";
                $response = $companiesController->newSecondaryCompanieBranch( $data ); 
            }

            if ( $response->result == 2 ) {
                $response->message = "Nueva sucursal creada con exito!";
            } else {
                $response->result = 0;
                $response->message = "Error. No se pudo crear la nueva sucursal";
            }
            return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });




    //editar los datos basicos de la sucursal
    $app->post('/changeCompanieData', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $response = $companiesController->changeCompanieData($data);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });



    $app->post('/changeCompanieColor', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $response = $companiesController->changeCompanieColors($data);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });


    //cambiar estado de la empresa
    $app->post('/changeStatusCompanie', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            $newStatus = $data['newStatus'];
            // $rut = $_SESSION['rutUserLogued'];
            $rut = $responseCurrentSession->currentSession->rutUserLogued;

            $response = $companiesController->changeStatusCompanie($newStatus, $rut);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });




    $app->post('/loadResolutions', function ($request, $response, $args) use ($container, $companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $data = $request->getParams();
            // $rut = $_SESSION['rutUserLogued'];
            $rut = $responseCurrentSession->currentSession->rutUserLogued;
            $response = $companiesController->loadResolutions($rut, $data);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });

    //esta es para obtener los colores y demás que estan guardados actualmente
    $app->post('/representacionimpresa', function ($request, $response, $args) use ($companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            // $rut = $_SESSION['rutUserLogued'];
            $rut = $responseCurrentSession->currentSession->rutUserLogued;
            // var_dump($responseCurrentSession->currentSession->rutUserLogued);
            // exit;
            $sucursal = $request->getParams()['sucursal'];
            $response = $companiesController->getRepresentacionImpresa($rut, $sucursal);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });

    //esta es para MODIFICAR los colores y demás de la rep. impresa
    $app->put('/representacionimpresa', function ($request, $response, $args) use ($companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            // $rut = $_SESSION['rutUserLogued'];
            $rut = $responseCurrentSession->currentSession->rutUserLogued;
            $data = $request->getParams()["data"];
            $response = $companiesController->updateRepresentacionImpresa($rut, $data);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });



    $app->post('/enabledDisabledCompanie', function ($request, $response, $args) use ($companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
            $currentSession = $responseCurrentSession->currentSession;
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();

            $value = $request->getParams()['value'];
            $response = $companiesController->enabledDisabledCompanie($value);

            $company = $companiesController->getCompaniesData($currentSession->rutUserLogued)->objectResult;
            $args["company"] = $company;
            //return $this->view->render($response, "companyDetail.twig", $args);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });



    $app->post('/saveInfoAdicional', function ($request, $response, $args) use ($companiesController, $usersController){
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            $response = new \stdClass();
            $rut = $responseCurrentSession->currentSession->rutUserLogued;//$_SESSION['rutUserLogued'];
            $info = $request->getParams()['info'];

            $response = $companiesController->saveInfoAdicional($info, $rut);
            return json_encode($response);

        }else return json_encode($responseCurrentSession);
    });

    $app->post('/loadListCustomers', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
			
            $customersResponse = $companiesController->loadListCustomers($rut);
			
            if ( $customersResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $customersResponse->objectResult;
				return json_encode($response);
				
            }else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/loadCustomer', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $document = $data['document'];
            $customersResponse = $companiesController->loadCustomer($rut, $document);
			
            if ( $customersResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $customersResponse->objectResult;
				return json_encode($response);
            } else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/saveCustomer', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $customer = $data['data'];
            $saveCustomerResponse = $companiesController->saveCustomer($rut, $customer);
			
            if ( $saveCustomerResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $saveCustomerResponse->objectResult;
				return json_encode($response);
            }else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/newCustomer', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $customer = $data['data'];
            $saveCustomerResponse = $companiesController->newCustomer($rut, $customer);
			// var_dump($saveCustomerResponse);
            if ( $saveCustomerResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $saveCustomerResponse->objectResult;
				// return json_encode($response);
            } else return json_encode($saveCustomerResponse);
            return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/getEmitidos', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $lastId = $data['lastId'];
            $emitidosResponse = $companiesController->getEmitidos($rut, $lastId);
			
            if ( $emitidosResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $emitidosResponse->objectResult;
				return json_encode($response);
            }else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/getRecibidos', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){

            $data = $request->getParams();
            $rut = $data['rut'];
            $lastId = $data['lastId'];
            $recibidosResponse = $companiesController->getRecibidos($rut, $lastId);
			
            if ( $recibidosResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $recibidosResponse->objectResult;
				return json_encode($response);
            }else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/getCFE', function ($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
        if($responseCurrentSession->result == 2){
        // if ( $_SESSION['mailUserLogued'] ){
            
            $data = $request->getParams();
            // var_dump($data);
            $rut = $data['rut'];
            $tipoCFE = $data['tipoCFE'];
            $serieCFE = $data['serieCFE'];
            $numeroCFE = $data['numeroCFE'];
            $RUTEmisor = null;
            if(isset($data['RUTEmisor']))
                $RUTEmisor = $data['RUTEmisor'];
            
            $cfeResponse = $companiesController->getCFE($rut, $RUTEmisor, $tipoCFE, $serieCFE, $numeroCFE);
			
            if ( $cfeResponse->result == 2 ){
				$response->result = 2;
				$response->objectResult = $cfeResponse->objectResult;
				return json_encode($response);
            }else
                return json_encode($response);
        } else return json_encode($responseCurrentSession);
    });

    $app->post('/getQuotes', function($request, $response, $args) use ($companiesController, $usersController){
        $response = new \stdClass();
        $responseCurrentSession = $usersController->validateSession();
		if($responseCurrentSession->result == 2){
            return json_encode($companiesController->getQuotes());
		}else return json_encode($responseCurrentSession);
	});
}

?>