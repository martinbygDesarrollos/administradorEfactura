<?php
// require_once '../src/config.php';
require_once '/homeX/sigecom/public_html/administradorEfactura/src/config.php';


$resultCompanies = enviarConsulta('companies');
$numberOfComp = 0;
$tableBodyCert = "";
$arrayCert = array();
$tableBodyCaes = "";
$arrayCaes = array();
$tableBodyPocosCaes = "";
$arrayPocosCaes = array();

foreach ($resultCompanies as $companie) {
    
    if ( $companie->estado == 6 ){
        $companieDetails = enviarConsulta('company/' . $companie->rut . '');

        $expireInfo = companieExpire( $companie);
        
        $addCompanieToCertificados = expireThisMonth($expireInfo->expireDateCertificados);

        if ($addCompanieToCertificados){ // SI el certificado expira este mes lo agrego a la tabla
            $arrayCert[] = array(
                "row"=>appendCompanieToTable($companie, $expireInfo->expireDateCertificados),
                "orderParam"=>$expireInfo->expireDateCertificados
            );
        }
        
        $caesParaPedir = whichCaesToOrder($companieDetails);
        $cantidadCaesParaPedir = [];
        if(count($caesParaPedir) > 0){
            $cantidadCaesParaPedir = howManyCaesToOrder($companieDetails->rut, $caesParaPedir);
            usort($cantidadCaesParaPedir, function($a, $b) {
                return strtotime($a->vencimiento) - strtotime($b->vencimiento);
            });
            $arrayCaes[] = array(
                "row"=>appendCompanieToTableCaes($companieDetails, $cantidadCaesParaPedir),
                "orderParam"=>formatYYYYMMDDDate2($cantidadCaesParaPedir[0]->vencimiento)
            );
        }

        // IMPRESION DE INFORMACION -----------------------------------------------------------------------------------
        // $resoluciones = "";
        // $resolutions = [];
        // if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != ""){
        //     $resolutions[] = "dgiResolutionEFac";
        // }
        // if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != ""){
        //     $resolutions[] = "dgiResolutionERes";
        // }
        // if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != ""){
        //     $resolutions[] = "dgiResolutionERem";
        // }
        // if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != ""){
        //     $resolutions[] = "dgiResolutionEFacExp";
        // }
        // if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != ""){
        //     $resolutions[] = "dgiResolutionCtaAjena";
        // }
        // if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != ""){
        //     $resolutions[] = "dgiResolutionEBolEntrada";
        // }
        // $resoluciones = implode(", ", $resolutions);

        // echo "<br> <p style=\"color: blue; margin: 0; font-size: x-large; font-weight: bold;\"> $companieDetails->rut  $companieDetails->razonSocial Resoluciones: $resoluciones </p>";
        // foreach ($companieDetails->caes as $cae) {
        //     $venc = formatBarsDate($cae->vencimiento);
        //     $porcentaje = intval($cae->disponibles / $cae->total * 100);
        //     if($porcentaje <= 10 || venceEsteMes($cae)){
        //         echo "<p style=\"color: red; margin: 0\"> $cae->tipoCFE: Disponibles: $cae->disponibles ($porcentaje %), Vence: $venc </p>";
        //     } else {
        //         echo "$cae->tipoCFE, Disponibles: $cae->disponibles ($porcentaje %), Vence: $venc <br>";
        //     }
        // }   
        // if(count($caesParaPedir) > 0){
        //     echo "<p style=\"color: black; margin: 0; font-weight: bold;\"> PEDIR: ";
        //     $length = count($caesParaPedir);
        //     foreach($cantidadCaesParaPedir as $index => $cae){
        //         echo "$cae->tipoCFE => $cae->pedir";
        //         if ($index < $length - 1) {
        //             echo ", ";
        //         }
        //     }
        //     echo "</p>";
        // }
        // IMPRESION DE INFORMACION -----------------------------------------------------------------------------------
    }

}

function howManyCaesToOrder($rut, $caesParaPedir){
    $caes = [];
    foreach ($caesParaPedir as $cae){
        $TO = date("YmdHis");
        $expireThisMonthBool = venceEsteMes($cae);
        $dateMinusTwoYears = null;
        $monthsLeft = 0;
        $hoy = new DateTime(); // Current date
        $expireDate = new DateTime($cae->vencimiento); // Target date
        if($expireThisMonthBool == true) {// Si expira este mes los meses para el calculo son 24, calculo simple
            $dateMinusTwoYears = (clone $hoy)->modify('-2 years');
        } else { // En este caso debo calcular los meses desde la ultima vez que se pidio
            $interval = $hoy->diff($expireDate, true);
            $monthsLeft = $interval->y * 12 + $interval->m;
            $dateMinusTwoYears = (clone $expireDate)->modify('-2 years');
        }
        $FROM = $dateMinusTwoYears->format('YmdHis');
        $response = enviarConsulta("company/$rut/cfe/emitidos/count?From=".$FROM."&To=".$TO."&Type=$cae->tipoCFE");
        $usados = isset($response->cfeCount) ? $response->cfeCount : 0;
        $mesesTranscurridos = 24 - $monthsLeft;
        $usadosPorMes = $usados / $mesesTranscurridos;
        $cantCaesPedir = $usadosPorMes * 1.1 * 24;
        if($cantCaesPedir <= 100 )
            $cantCaesPedir = 100;
        else
            $cantCaesPedir = (int)ceil($cantCaesPedir / 100) * 100;
        $caes[] = (object) ['tipoCFE' => $cae->tipoCFE, 'pedir' => $cantCaesPedir, 'usados' => $usados, 'usadosPorMes' => $usadosPorMes, 'razon' => $cae->razon, 'vencimiento' => $cae->vencimiento ];
    }
    return $caes;
}

function formatBarsDate($date){ // FROM YYYY-MM-DD to DD/MM/YYYY
    return substr($date, 8, 2) . "/" . substr($date, 5, 2) . "/" . substr($date, 0, 4);
}

function formatBarsDate2($date){ // FROM DD-MM-YYYY to DD/MM/YYYY
    return substr($date, 0, 2) . "/" . substr($date, 3, 2) . "/" . substr($date, 6, 4);
}

function formatYYYYMMDDDate1($date){ // FROM YYYY-MM-DD to DDMMAAAA
    return substr($date, 8, 2) . substr($date, 5, 2) . substr($date, 0, 4);
}

function formatYYYYMMDDDate2($date){ // FROM YYYY-MM-DD to YYYYMMAA
    return substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);
}

function venceEsteMes($cae){ // calcula si vence dentro de los proximo 30 dias
    if (isset($cae->vencimiento) && $cae->vencimiento != ""){
        $dateTime = new DateTime($cae->vencimiento);
        $date = $dateTime->format('Ymd');
        $nextMonth = date('Ymd', strtotime("+1 month"));
        if($date <= $nextMonth){
            return true;
        }
    }
    return false;
}

function gruposIncompleto($companieDetails){ // calcula por grupo si faltan miembros (Ej grupo basico = 101,102,103,111,112,113)
    $grupos = [];
    if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != ""){
        $requiredTipos = [101, 102, 103, 111, 112, 113];
        $foundTipos = [];
        foreach ($companieDetails->caes as $cae) {
            if (in_array($cae->tipoCFE, $requiredTipos)) {
                $foundTipos[] = $cae->tipoCFE;
            }
        }
        foreach ($requiredTipos as $tipo) {
            if (!in_array($tipo, $foundTipos)) {
                $grupos[] = "dgiResolutionEFac";
            }
        }
    }
    if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != ""){
        $required = 182;
        $found = false;
        foreach ($companieDetails->caes as $cae) {
            if ($cae->tipoCFE == $required) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $grupos[] = "dgiResolutionERes";
        }
    }
    if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != ""){
        $required = 181;
        $found = false;
        foreach ($companieDetails->caes as $cae) {
            if ($cae->tipoCFE == $required) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $grupos[] = "dgiResolutionERem";
        }
    }
    if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != ""){
        $requiredTipos = [121, 122, 123, 124];
        $foundTipos = [];
        foreach ($companieDetails->caes as $cae) {
            if (in_array($cae->tipoCFE, $requiredTipos)) {
                $foundTipos[] = $cae->tipoCFE;
            }
        }
        foreach ($requiredTipos as $tipo) {
            if (!in_array($tipo, $foundTipos)) {
                $grupos[] = "dgiResolutionEFacExp";
            }
        }
    }
    if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != ""){
        $requiredTipos = [131, 132, 133, 141, 142, 143];
        $foundTipos = [];
        foreach ($companieDetails->caes as $cae) {
            if (in_array($cae->tipoCFE, $requiredTipos)) {
                $foundTipos[] = $cae->tipoCFE;
            }
        }
        foreach ($requiredTipos as $tipo) {
            if (!in_array($tipo, $foundTipos)) {
                $grupos[] = "dgiResolutionCtaAjena";
            }
        }
    }
    if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != ""){
        $requiredTipos = [151, 152, 153];
        $foundTipos = [];
        foreach ($companieDetails->caes as $cae) {
            if (in_array($cae->tipoCFE, $requiredTipos)) {
                $foundTipos[] = $cae->tipoCFE;
            }
        }
        foreach ($requiredTipos as $tipo) {
            if (!in_array($tipo, $foundTipos)) {
                $grupos[] = "dgiResolutionEBolEntrada";
            }
        }
    }
    return $grupos;
}

function quedanPocos($cae){ // calcula si quedan menos del 10%
    $totalCAEs = $cae->total;
    $disponiblesCAEs = $cae->disponibles;
    if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.1)) {
        return true;
    }
    return false;
}

function findByTipoCFE($caes, $tipoCFE) {
    $filtered = array_filter($caes, function($cae) use ($tipoCFE) {
        return $cae->tipoCFE == $tipoCFE;
    });
    return !empty($filtered) ? array_values($filtered)[0] : null;
}

function whichCaesToOrder($companieDetails) {
    $pedirCAEs = [];
    $pedirResolutionEFac = false;
    $pedirResolutionERes = false;
    $pedirResolutionERem = false;
    $pedirResolutionEFacExp = false;
    $pedirResolutionCtaAjena = false;
    $pedirResolutionEBolEntrada = false;
    
    $modifiedCaes = [];

    if(count($companieDetails->caes) <= 0){
        $pedirResolutionEFac = true;
        $pedirResolutionERes = true;
        $pedirResolutionERem = true;
        $pedirResolutionEFacExp = true;
        $pedirResolutionCtaAjena = true;
        $pedirResolutionEBolEntrada = true;
        foreach ($companieDetails->caes as $cae) {
            $cae->razon = 'NO QUEDAN';
            $modifiedCaes[] = $cae;
        }
    } else {
        foreach ($companieDetails->caes as $cae) { // verificar los caes por grupo (si vence este mes o quedan pocos del grupo y la empresa tiene la resolucion pido todo el grupo)
            if (venceEsteMes($cae) || quedanPocos($cae)) {
                if(venceEsteMes($cae)) // DE NUEVO
                    $cae->razon = 'VENCE PRONTO';
                else
                    $cae->razon = 'QUEDAN POCOS';

                if ($cae->tipoCFE == 101 || $cae->tipoCFE == 102 || $cae->tipoCFE == 103 || $cae->tipoCFE == 111 || $cae->tipoCFE == 112 || $cae->tipoCFE == 113)
                    $pedirResolutionEFac = true;
                else if ($cae->tipoCFE == 182)
                    $pedirResolutionERes = true;
                else if ($cae->tipoCFE == 181)
                    $pedirResolutionERem = true;
                else if ($cae->tipoCFE == 121 || $cae->tipoCFE == 122 || $cae->tipoCFE == 123 || $cae->tipoCFE == 124 )
                    $pedirResolutionEFacExp = true;
                else if ($cae->tipoCFE == 131 || $cae->tipoCFE == 132 || $cae->tipoCFE == 133 || $cae->tipoCFE == 141 || $cae->tipoCFE == 142 || $cae->tipoCFE == 143)
                    $pedirResolutionCtaAjena = true;
                else if ($cae->tipoCFE == 151 || $cae->tipoCFE == 152 || $cae->tipoCFE == 153)
                    $pedirResolutionEBolEntrada = true;
                $modifiedCaes[] = $cae;
            }
        }
        $grupoIncompleto = gruposIncompleto($companieDetails);
        if(count($grupoIncompleto) > 0){ // verifico que si falta algun cae de los grupos
            if (in_array("dgiResolutionEFac", $grupoIncompleto)) {
                $pedirResolutionEFac = true;
            }
            if (in_array("dgiResolutionERes", $grupoIncompleto)) {
                $pedirResolutionERes = true;
            }
            if (in_array("dgiResolutionERem", $grupoIncompleto)) {
                $pedirResolutionERem = true;
            }
            if (in_array("dgiResolutionEFacExp", $grupoIncompleto)) {
                $pedirResolutionEFacExp = true;
            }
            if (in_array("dgiResolutionCtaAjena", $grupoIncompleto)) {
                $pedirResolutionCtaAjena = true;
            }
            if (in_array("dgiResolutionEBolEntrada", $grupoIncompleto)) {
                $pedirResolutionEBolEntrada = true;
            }
        }
    }
    if(isset($companieDetails->dgiResolutionEFac) && $companieDetails->dgiResolutionEFac != "" && $pedirResolutionEFac){ // Debe tener las 6 basicas
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 101)) ? $cae : (object) ['tipoCFE' => 101, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 102)) ? $cae : (object) ['tipoCFE' => 102, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 103)) ? $cae : (object) ['tipoCFE' => 103, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 111)) ? $cae : (object) ['tipoCFE' => 111, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 112)) ? $cae : (object) ['tipoCFE' => 112, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 113)) ? $cae : (object) ['tipoCFE' => 113, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
    }
    if(isset($companieDetails->dgiResolutionERes) && $companieDetails->dgiResolutionERes != "" && $pedirResolutionERes){ // Debe tener la de Resguardo
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 182)) ? $cae : (object) ['tipoCFE' => 182, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN',  'disponibles' => 0, 'total' => 0];
    }
    if(isset($companieDetails->dgiResolutionERem) && $companieDetails->dgiResolutionERem != "" && $pedirResolutionERem){ // Debe tener la de Remito
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 181)) ? $cae : (object) ['tipoCFE' => 181, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN',  'disponibles' => 0, 'total' => 0];
    }
    if(isset($companieDetails->dgiResolutionEFacExp) && $companieDetails->dgiResolutionEFacExp != "" && $pedirResolutionEFacExp){ // Debe tener las 4 de Exportacion
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 121)) ? $cae : (object) ['tipoCFE' => 121, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 122)) ? $cae : (object) ['tipoCFE' => 122, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 123)) ? $cae : (object) ['tipoCFE' => 123, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 124)) ? $cae : (object) ['tipoCFE' => 124, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
    }
    if(isset($companieDetails->dgiResolutionCtaAjena) && $companieDetails->dgiResolutionCtaAjena != "" && $pedirResolutionCtaAjena){ // Debe tener las 6 de Cuenta Ajena
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 131)) ? $cae : (object) ['tipoCFE' => 131, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 132)) ? $cae : (object) ['tipoCFE' => 132, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 133)) ? $cae : (object) ['tipoCFE' => 133, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 141)) ? $cae : (object) ['tipoCFE' => 141, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 142)) ? $cae : (object) ['tipoCFE' => 142, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 143)) ? $cae : (object) ['tipoCFE' => 143, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
    }
    if(isset($companieDetails->dgiResolutionEBolEntrada) && $companieDetails->dgiResolutionEBolEntrada != "" && $pedirResolutionEBolEntrada){ // Debe tener las 3 de Boleta de entrada
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 151)) ? $cae : (object) ['tipoCFE' => 151, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 152)) ? $cae : (object) ['tipoCFE' => 152, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
        $pedirCAEs[] = ($cae = findByTipoCFE($modifiedCaes, 153)) ? $cae : (object) ['tipoCFE' => 153, 'vencimiento' => date('Y-m-d'), 'razon' => 'NO QUEDAN', 'disponibles' => 0, 'total' => 0];
    }
    return $pedirCAEs;
}

$newArrayCert = orderTable($arrayCert);
foreach($newArrayCert as $cert){
    $tableBodyCert .= $cert["row"];
}

$newArrayCaes = orderTable2($arrayCaes);
foreach ($newArrayCaes as $cae) {
    $tableBodyCaes .= $cae["row"];
}

$to = MAIL_AVISOVENCIMIENTOS;
$subject = "$numberOfComp empresas con CAE o Certificado por vencer";
$header  = "MIME-Version: 1.0\r\nContent-type:text/html; charset=UTF-8";
$body = createMail($tableBodyCert, $tableBodyCaes);

// var_dump($body);
// exit;

$resultSendMail = mail($to, $subject, $body, $header);
if (!$resultSendMail)
    echo date("d/m/Y H:i")." Error al enviar el email con próximos vencimientos.";

function createMail($tableBodyCert, $tableBodyCaes){

        $mail = '<html>
        <head>
        <title>Empresas con CAE o Certificado por vencer</title>
        <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            color: black;
            font-size:18px;
        }
        </style>
        </head>
        <body>
        <div align="center">
        <h3>Vencimientos certificados</h3>
        <table style="width:70%">
        <tbody>
        <tr>
        <th style="width:50%;" >Empresa</th>
        <th>Fecha vencimiento</th>
        </tr>'. $tableBodyCert . '
        </tbody>
        </table>
        </div>
        <div align="center">
        <h3> CAEs para pedir </h3>
        <table style="width:70%">
        <tbody>
        <tr>
        <th style="width:150px;" >RUT</th>
        <th style="width:40%;" >Empresa</th>
        <th>CAE</th>
        <th style="width:150px;">Vencimiento</th>
        </tr>'. $tableBodyCaes . '
        </tbody>
        </table>
        </div>
        </body>
        </html>';

    return $mail;
}

function appendCompanieToTable($companie, $expireDate){

    $dateTime = new DateTime($expireDate);
    $expireDate2 = $dateTime->format('d/m/Y');

    $row = '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'</td>';
    $row .= '<td>' . $expireDate2.'</td>';
    $row .= '</tr>';
    return $row;

}

function appendCompanieToTableCaes($companie, $caes){
    $row = "<tr>";
    $rowRut = '<td>' . $companie->rut . '</td>';
    $rowCompanie = '<td>' . $companie->razonSocial . '<a href="https://admin.sigecom.uy/email/' . $companie->rut . '" target="_blank" title="Obtener mail pre-formateado para pedido de CAEs"> @ </a></td>';
    $rowRazonTD = "<td> ";
    $rowRazon = [];
    // $rowCaesTD = "<td> ";
    $rowCaes = [];
    $index = 0;
    foreach ($caes as $cae) {
        $rowRazon[] = $cae->tipoCFE;
        $rowCaes[] = formatBarsDate($cae->vencimiento);
    }
    $rowRazonTD .= implode(', ', $rowRazon) . "</td>";
    // $rowCaesTD .= implode('<br>', $rowCaes) . "</td>";
    $row .= $rowRut . $rowCompanie . $rowRazonTD . '<td>' . $rowCaes[0] . '</td> </tr>';
    return $row;
}


function appendCompanieToTablePocosCaes($companie, $infoCaes){

    $row = "";
    foreach ($infoCaes as $cae) {

        $row .= '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'<a href="https://admin.sigecom.uy/email/' . $companie->rut .'" target="_blank" title="Obtener mail pre-formateado para pedido de CAEs"> @ </a></td>';
        $row .= '<td>'.$cae['tipoCFE'].'</td>';
        $row .= '<td>'.$cae['usados'].'</td>';
        $row .= '<td>'.$cae['pedir'].'</td>';
        $row .= '<td>'.$cae['disponiblesPorcentaje'].' % </td>';
        $row .= '<td>'.$cae['disponibles'].'</td>';
        $row .= '<td>'.$cae['total'].'</td>';
        $row .= '</tr>';
    }
    return $row;

}

function companieExpire($companie) {
    $response = new stdClass();
    $expireDate = null;
    $expireCAEs = [];
    $expireDateCertificados = null;
    $expireDateCAEs = null;

    if (isset($companie->certificateExpireDate) && $companie->certificateExpireDate !== "") {
        $dateTime = new DateTime($companie->certificateExpireDate);
        $certExpireDate = $dateTime->format('Ymd');

        $expireDate = $certExpireDate;

        $expireDateCertificados = $certExpireDate;

    }

    foreach ($companie->caes as $cae) {
        $dateTime = new DateTime($cae->vencimiento);
        $date = $dateTime->format('Ymd');
        if(!$expireDateCAEs)
            $expireDateCAEs = $date;

        if (expireThisMonth($date)){
            $caeAux = [
                "expireDate" => $expireDateCAEs,
                "expireTypeText" => tableCfeType($cae->tipoCFE),
                "expireType" => $cae->tipoCFE
            ];
            $expireCAEs[] = $caeAux;
        }
    }
    $response->expireDateCertificados = $expireDateCertificados;
    $response->expireCAEs = $expireCAEs;

    return $response;
}

function expireThisMonth($expireDate){

    if (isset($expireDate) && $expireDate != ""){
        $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));
        if($expireDate <= $nextMonth){
            return true;
        }
    }

    return false;
}

function enviarConsulta($path){
    //CONEXIÓN BD
    $connection = new mysqli(DB_HOST, DB_USR, DB_PASS, DB_DB) or die("No se puede conectar con la Base de Datos");
    $connection->set_charset("utf8");
    if($connection){
        $query = $connection->prepare("SELECT tokenRest FROM `usuarios` WHERE correo = 'guillermo@gargano.com.uy'");
        $query->execute();
        $result = $query->get_result();
        $tokenRest = $result->fetch_object()->tokenRest;
    }


    $opciones = array('http' =>
        array(
            'method'  => 'GET',
            'header'  => array("Accept: aplication/json", "Authorization: Bearer " . $tokenRest),
        )
    );

    $contexto = stream_context_create($opciones);
    $resultado = file_get_contents(URL_REST.$path, false, $contexto);//rest companies
    $resultCompanies = json_decode($resultado);

    return $resultCompanies;
}

function tableCfeType($typeCode){

    switch ($typeCode) {
        case 101: return "e-Ticket";
        case 102: return "Nota de crédito de e-Ticket";
        case 103: return "Nota de débito de e-Ticket";
        case 111: return "e-Factura";
        case 112: return "Nota de crédito de e-Factura";
        case 113: return "Nota de débito de e-Factura";
        case 121: return "e-Factura Exportación";
        case 122: return "Nota de crédito de e-Factura Exportación";
        case 123: return "Nota de débito de e-Factura Exportación";
        case 124: return "e-Remito de Exportación";
        case 131: return "e-Ticket Venta por Cuenta Ajena";
        case 132: return "Nota de crédito de e-Ticket Venta por Cuenta Ajena";
        case 133: return "Nota de débito de e-Ticket Venta por Cuenta Ajena";
        case 141: return "e-Factura Venta por Cuenta Ajena";
        case 142: return "Nota de crédito de e-Factura Venta por Cuenta Ajena";
        case 143: return "Nota de débito de e-Factura Venta por Cuenta Ajena";
        case 151: return "e-Boleta de entrada";
        case 152: return "Nota de crédito de e-Boleta de entrada";
        case 153: return "Nota de débito de e-Boleta de entrada";
        case 181: return "e-Remito";
        case 182: return "e-Resguardo";
        default: return "";
    }


}


function orderTable($arrayCert){
    uasort($arrayCert, function($a, $b) {
        if ($a["orderParam"] > $b["orderParam"]) return 1;
        else return -1;
    });
    return $arrayCert;
}

function orderTable2($array){
    usort($array, function($a, $b) {
        return $a["orderParam"] <=> $b["orderParam"];
    });
    return $array;
}

?>