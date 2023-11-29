<?php

require_once '..\src\config.php';
//var_dump("<pre>");
//printf("notificando vencimientos por correo//");

$resultCompanies = enviarConsulta('companies');
$numberOfComp = 0;
$tableBody = "";
$tableBodyCaes = "";
foreach ($resultCompanies as $companie) {

    if ( $companie->estado == 6 ){

        $expireInfo = companieExpire( $companie);
        $addCompanie = expireThisMonth($expireInfo->expireDate);

        if ($addCompanie){
            $numberOfComp ++;
            $tableBody .= appendCompanieToTable($companie, $expireInfo);
        }

        $listadoCaes = pocosCaes($companie);
        if (count($listadoCaes) >0 ){
            $tableBodyCaes .= appendCompanieToTableCaes($companie, $listadoCaes);
        }

    }

}

$to = MAIL_AVISOVENCIMIENTOS;
$subject = "$numberOfComp empresas con CAE o Certificado por vencer";
$header  = "MIME-Version: 1.0\r\nContent-type:text/html; charset=UTF-8";
$body = createMail($tableBody, $tableBodyCaes);
var_dump($body);exit;
/*$resultSendMail = mail($to, $subject, $body, $header);
if ($resultSendMail)
    printf("email enviado");
else
    printf("error al enviar el email");*/



function createMail($tableBody, $tableBodyCaes){

        $mail = '<html>
        <head>
        <title>Empresas con CAE o Certificado por vencer</title>
        <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            color: black;
            font-size:20px;
        }
        </style>
        </head>
        <body>
        <div align="center">
        <h5>Vencimientos de CAEs o Certificados</h5>
        <table style="width:70%">
        <tbody>
        <tr>
        <th>Empresa</th>
        <th>Fecha vencimiento</th>
        </tr>'. $tableBody . '
        </tbody>
        </table>
        </div>
        <div align="center">
        <h5>Cantidad de CAEs disponibles</h5>
        <table style="width:70%">
        <tbody>
        <tr>
        <th>Empresa</th>
        <th>Tipo CFE</th>
        <th>Pedir</th>
        <th>Disponibles(%)</th>
        <th>Disponibles</th>
        <th>Total</th>
        </tr>'. $tableBodyCaes . '
        </tbody>
        </table>
        </div>
        </body>
        </html>';

    return $mail;
}

function appendCompanieToTable($companie, $expireInfo){

    $dateTime = new DateTime($expireInfo->expireDate);
    $expireDate = $dateTime->format('d/m/Y');

    $row = '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'</td>';

    if ($expireInfo->expireDateCaeType > 0 )
        $row .= '<td>'.$expireInfo->expireDateVoucher.' '. $expireInfo->expireDateCaeType . ' ' . $expireDate.'</td>';
    else
        $row .= '<td>'.$expireInfo->expireDateVoucher.' ' . $expireDate.'</td>';
    $row .= '</tr>';
    return $row;


}


function appendCompanieToTableCaes($companie, $infoCaes){

    $row = "";
    foreach ($infoCaes as $cae) {

        $row .= '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'</td>';
        $row .= '<td>'.$cae['tipoCFE'].'</td>';
        $row .= '<td>'.$cae['pedir'].'</td>';
        $row .= '<td>'.$cae['disponiblesPorcentaje'].'</td>';
        $row .= '<td>'.$cae['disponibles'].'</td>';
        $row .= '<td>'.$cae['total'].'</td>';
        $row .= '</tr>';
    }

    return $row;


}



function companieExpire($companie) {
    $response = new stdClass();
    $expireDate = null;
    $expireDateVoucher = null;
    $expireDateCaeType = null;

    if (isset($companie->certificateExpireDate) && $companie->certificateExpireDate !== "") {

        $dateTime = new DateTime($companie->certificateExpireDate);
        $certExpireDate = $dateTime->format('Ymd');

        $expireDate = $certExpireDate;
        $expireDateVoucher = "Certificado";
        $expireDateCaeType = 0;
    }

    foreach ($companie->caes as $cae) {
        $dateTime = new DateTime($cae->vencimiento);
        $date = $dateTime->format('Ymd');

        if (isset($expireDate) && $expireDate !== "") {
            if ($date < $expireDate && !$this->isDuplicatedCae($cae, $companie->caes)) {
                $expireDate = $date;
                $expireDateVoucher = "CAE";
                $expireDateCaeType = $utilClass->tableCfeType($cae->tipoCFE);
            }
        } else {
            $expireDate = $date;
            $expireDateVoucher = "CAE";
            $expireDateCaeType = $utilClass->tableCfeType($cae->tipoCFE);
        }
    }

    $response->expireDate = $expireDate;
    $response->expireDateVoucher = $expireDateVoucher;
    $response->expireDateCaeType = $expireDateCaeType;

    return $response;
}


function isDuplicatedCae($cae, $caes) {
    foreach ($caes as $cae2) {
        if ($cae->tipoCFE == $cae2->tipoCFE && $cae2->vencimiento > $cae->vencimiento) {
            return true;
        }
    }

    return false;
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



function pocosCaes($empresa){
    $pocosCaes = [];
    if (isset($empresa->caes) && is_array($empresa->caes)) {

        foreach ($empresa->caes as $cae) {
            $caeDuplicated = isDuplicatedCae($cae, $empresa->caes);

            if (!$caeDuplicated){
                if (isset($cae->total) && isset($cae->disponibles)) {
                    $totalCAEs = $cae->total;
                    $disponiblesCAEs = $cae->disponibles;

                    // Verificar si la cantidad disponibles es menos del 5% del total
                    if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.05)) {
                        $estimadoPedir = cuantosCaesPedir($empresa->rut, $cae->tipoCFE);
                        $pocosCaes[] = array(
                            'tipoCFE' => $cae->tipoCFE,
                            'pedir' => $estimadoPedir,
                            'disponibles' => $cae->disponibles,
                            'total' => $cae->total,
                            'disponiblesPorcentaje' => intval((($disponiblesCAEs / $totalCAEs) * 100) , 10)
                        );
                    }
                }
            }
        }
    }

    return $pocosCaes;
}


function cuantosCaesPedir($rut, $type){


    define("FROM", date("YmdHis", strtotime("-2 year", strtotime(date("YmdHis")))));
    define("TO", date("YmdHis"));
    //20221029173623
    //20221129173623
    define("PAGE_SIZE", 500);

    $emitidos = enviarConsulta("company/$rut/cfe/emitidos?From=".FROM."&To=".TO."&Type=$type&PageSize=".PAGE_SIZE);
    $usadosEnDosAños = count($emitidos);

    if ( count($emitidos) == PAGE_SIZE ){
        do {
            $lastId = end($emitidos)->id;
            $emitidos = enviarConsulta("company/$rut/cfe/emitidos?LastId=".$lastId."&From=".FROM."&To=".TO."&Type=$type&PageSize=".PAGE_SIZE);
            $usadosEnDosAños += count($emitidos);
        } while (count($emitidos) === PAGE_SIZE);
    }

    $cantCaesPedir = ( round($usadosEnDosAños / 500) * 500 ) == 0 ? 500 : round($usadosEnDosAños / 500) * 500;
    return $cantCaesPedir;
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

?>