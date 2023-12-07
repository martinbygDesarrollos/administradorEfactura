<?php
require_once '../src/config.php';


$resultCompanies = enviarConsulta('companies');
$numberOfComp = 0;
$tableBodyCert = "";
$tableBodyCaes = "";
$tableBodyPocosCaes = "";
foreach ($resultCompanies as $companie) {

    if ( $companie->estado == 6 ){
        $expireInfo = companieExpire( $companie);
        $addCompanie = expireThisMonth($expireInfo->expireDate);

        if ($addCompanie){
            $numberOfComp ++;
            if ($expireInfo->expireDateVoucher === "Certificado")
                $tableBodyCert .= appendCompanieToTable($companie, $expireInfo);
            else
                $tableBodyCaes .= appendCompanieToTableCaes($companie, $expireInfo);
        }

        $listadoCaes = pocosCaes($companie);
        if (count($listadoCaes) >0 ){
            $tableBodyPocosCaes .= appendCompanieToTablePocosCaes($companie, $listadoCaes);
        }

    }

}

$to = MAIL_AVISOVENCIMIENTOS;
$subject = "$numberOfComp empresas con CAE o Certificado por vencer";
$header  = "MIME-Version: 1.0\r\nContent-type:text/html; charset=UTF-8";
$body = createMail($tableBodyCert, $tableBodyCaes, $tableBodyPocosCaes);
var_dump($body);exit;
/*$resultSendMail = mail($to, $subject, $body, $header);
if ($resultSendMail)
    printf("email enviado");
else
    printf("error al enviar el email");*/



function createMail($tableBodyCert, $tableBodyCaes, $tableBodyPocosCaes){

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
        <h3>Vencimientos CAEs</h3>
        <table style="width:70%">
        <tbody>
        <tr>
        <th style="width:50%;" >Empresa</th>
        <th style="width:25%;" >Tipo CFE</th>
        <th>Fecha vencimiento</th>
        </tr>'. $tableBodyCaes . '
        </tbody>
        </table>
        </div>
        <div align="center">
        <h3>Cantidad de CAEs disponibles</h3>
        <table style="width:70%">
        <tbody>
        <tr>
        <th style="width:25%;" >Empresa</th>
        <th>Tipo CFE</th>
        <th>Usados en 2 años</th>
        <th>Pedir</th>
        <th>Disponibles(%)</th>
        <th>Disponibles</th>
        <th>Total</th>
        </tr>'. $tableBodyPocosCaes . '
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
    $row .= '<td>' . $expireDate.'</td>';
    $row .= '</tr>';
    return $row;


}

function appendCompanieToTableCaes($companie, $expireInfo){
    $dateTime = new DateTime($expireInfo->expireDate);
    $expireDate = $dateTime->format('d/m/Y');

    $row = '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'</td>';
    $row .= '<td>CAE '. $expireInfo->expireDateCaeType . '</td>';
    $row .= '<td>'. $expireDate . '</td>';
    $row .= '</tr>';
    return $row;
}


function appendCompanieToTablePocosCaes($companie, $infoCaes){

    $row = "";
    foreach ($infoCaes as $cae) {

        $row .= '<tr><td>'.$companie->razonSocial.'<br>'.$companie->rut.'</td>';
        $row .= '<td>'.$cae['tipoCFE'].'</td>';
        $row .= '<td>'.$cae['usados'].'</td>';
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
            if ($date < $expireDate && !isDuplicatedCae($cae, $companie->caes)) {
                $expireDate = $date;
                $expireDateVoucher = "CAE";
                $expireDateCaeType = tableCfeType($cae->tipoCFE);
            }
        } else {
            $expireDate = $date;
            $expireDateVoucher = "CAE";
            $expireDateCaeType = tableCfeType($cae->tipoCFE);
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

                    // Verificar si la cantidad disponibles es menos del 10% del total
                    if ($totalCAEs > 0 && (($disponiblesCAEs / $totalCAEs) < 0.2)) {
                        $estimadoPedir = cuantosCaesPedir($empresa->rut, $cae->tipoCFE);
                        $pocosCaes[] = array(
                            'tipoCFE' => $cae->tipoCFE,
                            'usados' => $estimadoPedir->usadosEnDosAños,
                            'pedir' => $estimadoPedir->cantCaesPedir,
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

    $response = stdClass();
    $response->cantCaesPedir = 0;
    $response->usadosEnDosAños = 0;

    define("FROM", date("YmdHis", strtotime("-2 year", strtotime(date("YmdHis")))));
    define("TO", date("YmdHis"));
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

    $response->cantCaesPedir = $cantCaesPedir;
    $response->usadosEnDosAños = $usadosEnDosAños;
    return $response;
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

?>