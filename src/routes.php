<?php

use Slim\App;
use Slim\Http\Response;

require_once 'controllers/ctr_companies.php';
require_once 'controllers/ctr_users.php';

return function (App $app) {
    $container = $app->getContainer();
    $companiesController = new ctr_companies();
    $usersController = new ctr_users();

    $routesUsers = require_once __DIR__ . "/../src/routes/routes_users.php";
    $routesCompanies = require_once __DIR__ . "/../src/routes/routes_companies.php";
    $routesEmited = require_once __DIR__ . "/../src/routes/routes_emited.php";
    $routesReceipt = require_once __DIR__ . "/../src/routes/routes_receipt.php";
    $routesUsersCompanie = require_once __DIR__ . "/../src/routes/routes_users_companie.php";

    $routesUsers($app);
    $routesCompanies($app);
    $routesEmited($app);
    $routesReceipt($app);
    $routesUsersCompanie($app);


    //ruta de inicio
    // $app->get('/', function ($request, $response, $args) use ($container, $companiesController) {
    //     $args['version'] = FECHA_ULTIMO_PUSH;
    //     if ( isset($_SESSION['mailUserLogued']) ){
    //         $args['mailUserLogued'] = $_SESSION['mailUserLogued'];


    //         if( isset($_SESSION['companiesList'] ) ){
    //             //aca cargar companies
    //             $_SESSION['lastID'] = 0;
    //             //$args['companiesList'] = $_SESSION['companiesList'];

    //             if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
    //                 $objFirstCompanie = array_pop(array_reverse($_SESSION['companiesList']));

    //                 $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
    //                 $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
    //             }

    //             if ( isset($_SESSION['companieUserLogued']) ){
    //                 $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
    //             }else{
    //                 $_SESSION['companieUserLogued'] = null;
    //                 $args['companieUserLogued'] = null;
    //             }

    //             $args["company"] = null;
    //             if ( isset($_SESSION['rutUserLogued']) ){
    //                 $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

    //                 $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
    //                 $args["company"] = $company;

    //             }else{
    //                 $_SESSION['rutUserLogued'] = null;
    //                 $args['rutUserLogued'] = null;
    //             }


    //         }else{
    //             //aca cargar companies
    //             $_SESSION['companiesList'] = $companiesController->getCompanies()->listResult;
    //             $_SESSION['lastID'] = 0;
    //             //$args['companiesList'] = $_SESSION['companiesList'];

    //             if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
    //                 $objFirstCompanie = array_pop(array_reverse($_SESSION['companiesList']));

    //                 $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
    //                 $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
    //             }

    //             if ( isset($_SESSION['companieUserLogued']) ){
    //                 $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
    //             }else{
    //                 $_SESSION['companieUserLogued'] = null;
    //                 $args['companieUserLogued'] = null;
    //             }

    //             $args["company"] = null;
    //             if ( isset($_SESSION['rutUserLogued']) ){
    //                 $args['rutUserLogued'] = $_SESSION['rutUserLogued'];

    //                 $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
    //                 $args["company"] = $company;

    //             }else{
    //                 $_SESSION['rutUserLogued'] = null;
    //                 $args['rutUserLogued'] = null;
    //             }


    //         }

    //     }else {
    //         $args['rutUserLogued'] = null;
    //         $args['mailUserLogued'] = null;
    //         $args['companieUserLogued'] = null;
    //     }

    //     return $this->view->render($response, "companies.twig", $args);
    // })->setName("Start");

    // $app->get('/', function ($request, $response, $args) use ($container, $companiesController) {
    //     $args['version'] = FECHA_ULTIMO_PUSH;
    //     $args['mailUserLogued'] = $_SESSION['mailUserLogued'];
    //     $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
    //     $args["company"] = null;
    //     if ( isset($_SESSION['rutUserLogued']) ){
    //         $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
            
    //         $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
    //         $args["company"] = $company;
            
    //     }else{
    //         $_SESSION['rutUserLogued'] = null;
    //         $args['rutUserLogued'] = null;
    //     }
    //     $companiesList = $companiesController->getCompanies()->listResult;
    //     if( !isset($_SESSION['companiesList'] ) ){
    //         //aca cargar companies
    //         $_SESSION['companiesList'] = $companiesList;
    //     }
    //     $args['companiesCount'] = count($companiesList);
    //     $companiesType = array();
    //     $companiesHabilitadas = array();
    //     $emisorHabilitadoCount = 0;
    //     $pendientePostulacionCount = 0;
    //     $pendienteAprobacionCount = 0;
    //     $pendienteCertificacionCount = 0;
    //     $pendienteResolucionCount = 0;
    //     $emisorNoHabilitadoCount = 0;
    //     $enEsperaComenzarCount = 0;
    //     $pendienteUsuarioCount = 0;

    //     foreach ($companiesList as $comp) {
    //         switch ($comp->estadoDescripcion) {
    //             case 'Emisor habilitado':
    //                 $emisorHabilitadoCount++;
    //                 break;
    //             case 'Pendiente postulación':
    //                 $pendientePostulacionCount++;
    //                 break;
    //             case 'Pendiente aprobación':
    //                 $pendienteAprobacionCount++;
    //                 break;
    //             case 'Pendiente certificación':
    //                 $pendienteCertificacionCount++;
    //                 break;
    //             case 'Pendiente resolución':
    //                 $pendienteResolucionCount++;
    //                 break;
    //             case 'Emisor no habilitado':
    //                 $emisorNoHabilitadoCount++;
    //                 break;
    //             case 'En espera para comenzar':
    //                 $enEsperaComenzarCount++;
    //                 break;
    //             case 'Pendiente usuario':
    //                 $pendienteUsuarioCount++;
    //                 break;
    //             default:
    //                 // Handle any other cases here
    //                 break;
    //         }
    //         if ( $comp->estado == 6 ){ // Si es Emisor habilitado
    //             $expireDateCertificados = null;
    //             $expireDateCertificadosSoon = false;
    //             $expireCAEsSoon = array();
    //             $pocosCaes = null;
                
    //             foreach ($comp->caes as $cae) {
    //                 $dateTime = new DateTime($cae->vencimiento);
    //                 $date = $dateTime->format('Ymd');
    //                 if(!$expireDateCAEs)
    //                     $expireDateCAEs = $date;
    //                 if (isset($date) && $date != ""){
    //                     $caeAux = [
    //                         "expireDate" => $expireDateCAEs,
    //                         "expireType" => $cae->tipoCFE
    //                     ];
    //                     // $expireCAEs[] = $caeAux;
    //                     $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
    //                     if($date <= $nextMonth){
    //                         $expireCAEsSoon[] = $caeAux;
    //                     }
    //                     if (isset($cae->total) && isset($cae->disponibles)) {
    //                         $totalCAEs = $cae->total;
    //                         $disponiblesCAEs = $cae->disponibles;
    //                         // Verificar si la cantidad disponibles es menos del 10% del total
    //                         if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.1)) {
    //                             // $estimadoPedir = cuantosCaesPedir($empresa->rut, $cae->tipoCFE);
    //                             $pocosCaesAux = [
    //                                 'tipoCFE' => $cae->tipoCFE,
    //                                 // 'usados' => $estimadoPedir->usadosEnDosAños,
    //                                 // 'pedir' => $estimadoPedir->cantCaesPedir,
    //                                 'disponibles' => $cae->disponibles,
    //                                 'total' => $cae->total,
    //                                 'disponiblesPorcentaje' => intval((($disponiblesCAEs / $totalCAEs) * 100) , 10)
    //                             ];
    //                         $pocosCaes[] = $pocosCaesAux;
    //                         }
    //                     }
    //                 }
    //             }

    //             if (isset($comp->certificateExpireDate) && $comp->certificateExpireDate !== "") {
    //                 $dateTime = new DateTime($comp->certificateExpireDate);
    //                 $certExpireDate = $dateTime->format('Ymd');
                    
    //                 $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
    //                 if($certExpireDate <= $nextMonth){
    //                     $expireDateCertificados = substr($certExpireDate, 6, 2) . "/" . substr($certExpireDate, 4, 2) . "/" . substr($certExpireDate, 0, 4) ;
    //                     $expireDateCertificadosSoon = true;
    //                 }
    //             }
    //             // Sort $expireCAEsSoon array by 'expireDate'
    //             usort($expireCAEsSoon, 'compareByExpireDate');
                
    //             $auxComp = [
    //                 "rut" => $comp->rut,
    //                 "razonSocial" => $comp->razonSocial,
    //                 "expireDateCertificados" => $expireDateCertificados,
    //                 "pocosCaes" => $pocosCaes,
    //                 "expireCAEsSoon" => $expireCAEsSoon,
    //                 "expireDateCertificadosSoon" => $expireDateCertificadosSoon
    //             ];
    //             $companiesHabilitadas[] = $auxComp;
    //         }
    //     }

    //     // Store the counts in the $companiesType array
    //     $companiesType['Emisor habilitado'] = $emisorHabilitadoCount;
    //     $companiesType['Pendiente postulacion'] = $pendientePostulacionCount;
    //     $companiesType['Pendiente aprobacion'] = $pendienteAprobacionCount;
    //     $companiesType['Pendiente certificacion'] = $pendienteCertificacionCount;
    //     $companiesType['Pendiente resolucion'] = $pendienteResolucionCount;
    //     $companiesType['Emisor no Habilitado'] = $emisorNoHabilitadoCount;
    //     $companiesType['En espera para comenzar'] = $enEsperaComenzarCount;
    //     $companiesType['Pendiente usuario'] = $pendienteUsuarioCount;
    //     $args['companiesType'] = $companiesType;

    //     $args['companiesList'] = $_SESSION['companiesList'];
    //     $args['companiesHabilitadas'] = $companiesHabilitadas;

        
    //     // var_dump($args['companiesList']);
    //     // var_dump($companiesHabilitadas);
    //     // exit;
    //     if( isset($_SESSION['companiesList'] ) ){
    //         if ( !isset($_SESSION['companieUserLogued']) && !isset($_SESSION['rutUserLogued'])){
    //             $objFirstCompanie = array_pop(array_reverse($_SESSION['companiesList']));

    //             $_SESSION['companieUserLogued'] = $objFirstCompanie->razonSocial;
    //             $_SESSION['rutUserLogued'] = $objFirstCompanie->rut;
    //         }
    //         if ( isset($_SESSION['companieUserLogued']) ){
    //             $args['companieUserLogued'] = $_SESSION['companieUserLogued'];
    //         }else{
    //             $_SESSION['companieUserLogued'] = null;
    //             $args['companieUserLogued'] = null;
    //         }
    //         $args["company"] = null;
    //         if ( isset($_SESSION['rutUserLogued']) ){
    //             $args['rutUserLogued'] = $_SESSION['rutUserLogued'];
    //             $company = $companiesController->getCompaniesData($_SESSION['rutUserLogued'])->objectResult;
    //             $args["company"] = $company;
    //         }else{
    //             $_SESSION['rutUserLogued'] = null;
    //             $args['rutUserLogued'] = null;
    //         }
    //     } else {
    //         $args['rutUserLogued'] = null;
    //         $args['mailUserLogued'] = null;
    //         $args['companieUserLogued'] = null;
    //     }
    //     // if ( isset($_SESSION['mailUserLogued']) ){
    //     //     return $this->view->render($response, "resumen.twig", $args);
    //     // }
    //     return $this->view->render($response, "resumen.twig", $args);
    //     // return $response->withRedirect($request->getUri()->getBaseUrl());
    // })->setName("Start");

    $app->get('/', function ($request, $response, $args) use ($container, $companiesController, $usersController) {
        if(isset($_SESSION['sistemSession'])){
            // var_dump($_SESSION['sistemSession']);
            // exit;
            $responseCurrentSession = $usersController->validateSession();
            $args['version'] = FECHA_ULTIMO_PUSH;
            if($responseCurrentSession->result == 2){
                $args['sistemSession'] = $responseCurrentSession->currentSession;
                $company = $companiesController->getCompaniesData($responseCurrentSession->currentSession->rutUserLogued)->objectResult;
                $args["company"] = $company;
                $companiesList = $responseCurrentSession->currentSession->companies;
                $args['companiesCount'] = count($companiesList);
                $companiesType = array();
                $companiesHabilitadas = array();
                $emisorHabilitadoCount = 0;
                $pendientePostulacionCount = 0;
                $pendienteAprobacionCount = 0;
                $pendienteCertificacionCount = 0;
                $pendienteResolucionCount = 0;
                $emisorNoHabilitadoCount = 0;
                $enEsperaComenzarCount = 0;
                $pendienteUsuarioCount = 0;

                foreach ($companiesList as $comp) {
                    switch ($comp->estadoDescripcion) {
                        case 'Emisor habilitado':
                            $emisorHabilitadoCount++;
                            break;
                        case 'Pendiente postulación':
                            $pendientePostulacionCount++;
                            break;
                        case 'Pendiente aprobación':
                            $pendienteAprobacionCount++;
                            break;
                        case 'Pendiente certificación':
                            $pendienteCertificacionCount++;
                            break;
                        case 'Pendiente resolución':
                            $pendienteResolucionCount++;
                            break;
                        case 'Emisor no habilitado':
                            $emisorNoHabilitadoCount++;
                            break;
                        case 'En espera para comenzar':
                            $enEsperaComenzarCount++;
                            break;
                        case 'Pendiente usuario':
                            $pendienteUsuarioCount++;
                            break;
                        default:
                            // Handle any other cases here
                            break;
                    }
                    if ( $comp->estado == 6 ){ // Si es Emisor habilitado
                        $expireDateCertificados = null;
                        $expireDateCertificadosSoon = false;
                        $expireCAEsSoon = array();
                        $pocosCaes = null;
                        // $gruposCaes = null; // TEST PARA SABER QUE CAES FALTAN ========================
                        // $caesHabilitados = null; // TEST PARA SABER QUE CAES FALTAN ========================
                        
                        foreach ($comp->caes as $cae) {
                            $dateTime = new DateTime($cae->vencimiento);
                            $date = $dateTime->format('Ymd');
                            if(!$expireDateCAEs)
                                $expireDateCAEs = $date;
                            if (isset($date) && $date != ""){
                                $caeAux = [
                                    "expireDate" => $expireDateCAEs,
                                    "expireType" => $cae->tipoCFE
                                ];
                                // $expireCAEs[] = $caeAux;
                                $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
                                if($date <= $nextMonth){
                                    $expireCAEsSoon[] = $caeAux;
                                }
                                if (isset($cae->total) && isset($cae->disponibles)) {
                                    $totalCAEs = $cae->total;
                                    $disponiblesCAEs = $cae->disponibles;
                                    // Verificar si la cantidad disponibles es menos del 10% del total
                                    if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.1)) {
                                        // $estimadoPedir = cuantosCaesPedir($empresa->rut, $cae->tipoCFE);
                                        $pocosCaesAux = [
                                            'tipoCFE' => $cae->tipoCFE,
                                            // 'usados' => $estimadoPedir->usadosEnDosAños,
                                            // 'pedir' => $estimadoPedir->cantCaesPedir,
                                            'disponibles' => $cae->disponibles,
                                            'total' => $cae->total,
                                            'disponiblesPorcentaje' => intval((($disponiblesCAEs / $totalCAEs) * 100) , 10)
                                        ];
                                    $pocosCaes[] = $pocosCaesAux;
                                    }
                                }
                            }
                        }
                        // // TEST PARA SABER QUE CAES FALTAN ========================
                        // $companieDetails = $companiesController->getCompaniesData($comp->rut)->objectResult;
                        // $caesHabilitados = caesHabilitados($companieDetails);
                        // if(count($caesHabilitados) > 0){ // verifico que si falta algun cae de los grupos
                        //     if (in_array("dgiResolutionEFac", $caesHabilitados)) {
                        //         // $pedirResolutionEFac = true;
                        //         $gruposCaes[] = 101;
                        //         $gruposCaes[] = 102;
                        //         $gruposCaes[] = 103;
                        //         $gruposCaes[] = 111;
                        //         $gruposCaes[] = 112;
                        //         $gruposCaes[] = 113;
                        //     }
                        //     if (in_array("dgiResolutionERes", $caesHabilitados)) {
                        //         // $pedirResolutionERes = true;
                        //         $gruposCaes[] = 182;
                        //     }
                        //     if (in_array("dgiResolutionERem", $caesHabilitados)) {
                        //         // $pedirResolutionERem = true;
                        //         $gruposCaes[] = 181;
                        //     }
                        //     if (in_array("dgiResolutionEFacExp", $caesHabilitados)) {
                        //         // $pedirResolutionEFacExp = true;
                        //         $gruposCaes[] = 121;
                        //         $gruposCaes[] = 122;
                        //         $gruposCaes[] = 123;
                        //         $gruposCaes[] = 124;
                        //     }
                        //     if (in_array("dgiResolutionCtaAjena", $caesHabilitados)) {
                        //         // $pedirResolutionCtaAjena = true;
                        //         $gruposCaes[] = 131;
                        //         $gruposCaes[] = 132;
                        //         $gruposCaes[] = 133;
                        //         $gruposCaes[] = 141;
                        //         $gruposCaes[] = 142;
                        //         $gruposCaes[] = 143;
                        //     }
                        //     if (in_array("dgiResolutionEBolEntrada", $caesHabilitados)) {
                        //         // $pedirResolutionEBolEntrada = true;
                        //         $gruposCaes[] = 151;
                        //         $gruposCaes[] = 152;
                        //         $gruposCaes[] = 153;
                        //     }
                        // }
                        // $hasCaesFaltantes = false;
                        // if(count($gruposCaes) != count($comp->caes))
                        //     $hasCaesFaltantes = true;
                        // // TEST PARA SABER QUE CAES FALTAN ========================

                        if (isset($comp->certificateExpireDate) && $comp->certificateExpireDate !== "") {
                            $dateTime = new DateTime($comp->certificateExpireDate);
                            $certExpireDate = $dateTime->format('Ymd');
                            
                            $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
                            if($certExpireDate <= $nextMonth){
                                $expireDateCertificados = substr($certExpireDate, 6, 2) . "/" . substr($certExpireDate, 4, 2) . "/" . substr($certExpireDate, 0, 4) ;
                                $expireDateCertificadosSoon = true;
                            }
                        }
                        // Sort $expireCAEsSoon array by 'expireDate'
                        usort($expireCAEsSoon, 'compareByExpireDate');
                        
                        $auxComp = [
                            "rut" => $comp->rut,
                            "razonSocial" => $comp->razonSocial,
                            "expireDateCertificados" => $expireDateCertificados,
                            "pocosCaes" => $pocosCaes,
                            "expireCAEsSoon" => $expireCAEsSoon,
                            "expireDateCertificadosSoon" => $expireDateCertificadosSoon
                            // "caesHabilitados" => $gruposCaes, // TEST PARA SABER QUE CAES FALTAN ========================
                            // "caesDisponibles" => $comp->caes, // TEST PARA SABER QUE CAES FALTAN ========================
                            // "hasCaesFaltantes" => $hasCaesFaltantes // TEST PARA SABER QUE CAES FALTAN ========================
                        ];
                        $companiesHabilitadas[] = $auxComp;
                    }
                }

                // Store the counts in the $companiesType array
                $companiesType['Emisor habilitado'] = $emisorHabilitadoCount;
                $companiesType['Pendiente postulacion'] = $pendientePostulacionCount;
                $companiesType['Pendiente aprobacion'] = $pendienteAprobacionCount;
                $companiesType['Pendiente certificacion'] = $pendienteCertificacionCount;
                $companiesType['Pendiente resolucion'] = $pendienteResolucionCount;
                $companiesType['Emisor no Habilitado'] = $emisorNoHabilitadoCount;
                $companiesType['En espera para comenzar'] = $enEsperaComenzarCount;
                $companiesType['Pendiente usuario'] = $pendienteUsuarioCount;
                $args['companiesType'] = $companiesType;

                // $args['companiesList'] = $responseCurrentSession->currentSession->companies;
                $args['companiesHabilitadas'] = $companiesHabilitadas;
                
                return $this->view->render($response, "resumen.twig", $args);
                // return $response->withStatus(302)->withHeader('Location', 'home');
            } else {
                return $response->withStatus(302)->withHeader('Location', 'iniciar-sesion');
            }
        } else {
            return $response->withStatus(302)->withHeader('Location', 'iniciar-sesion');
        }
    })->setName("Start");

    // Define a custom comparison function for usort()
    function compareByExpireDate($a, $b) {
        return strtotime($a['expireDate']) - strtotime($b['expireDate']);
    }

    // function caesHabilitados($companieDetails){ // calcula por grupo si faltan miembros (Ej grupo basico = 101,102,103,111,112,113)
    //     $grupos = [];
    //     if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != ""){
    //         $grupos[] = "dgiResolutionEFac";
    //     }
    //     if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != ""){
    //         $grupos[] = "dgiResolutionERes";
    //     }
    //     if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != ""){
    //         $grupos[] = "dgiResolutionERem";
    //     }
    //     if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != ""){
    //         $grupos[] = "dgiResolutionEFacExp";
    //     }
    //     if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != ""){
    //         $grupos[] = "dgiResolutionCtaAjena";
    //     }
    //     if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != ""){
    //         $grupos[] = "dgiResolutionEBolEntrada";
    //     }
    //     return $grupos;
    // }
};
