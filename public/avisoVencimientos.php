<?php

require_once '..\src\config.php';
printf("notificando vencimientos por correo//");

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
$resultado = file_get_contents(URL_REST.'companies', false, $contexto);//rest companies
$resultCompanies = json_decode($resultado);
$numberOfComp = 0;
$tableBody = "";
foreach ($resultCompanies as $companie) {

    if ( $companie->estado == 6 ){

        $expireInfo = companieExpire( $companie);
        $addCompanie = expireColorWarning($expireInfo->expireDate);

        if ($addCompanie){
            $numberOfComp ++;
            $tableBody .= appendCompanieToTable($companie, $expireInfo);
        }

    }

}

$to = MAIL_ADMINISTRACION.",".MAIL_GUILLERMO;
$subject = "$numberOfComp empresas con CAE o Certificado por vencer";
$header  = "MIME-Version: 1.0\r\nContent-type:text/html; charset=UTF-8";
$body = createMail($tableBody);

$resultSendMail = mail($to, $subject, $body, $header);
if ($resultSendMail)
    printf("email enviado");
else
    printf("error al enviar el email");


function createMail($tableBody){

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
        <table style="width:70%">
        <tbody>
        <tr>
        <th>Empresa</th>
        <th>Fecha vencimiento</th>
        </tr>'. $tableBody . '
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



function expireColorWarning($expireDate){

    if (isset($expireDate) && $expireDate != ""){
        $nextMonth = date('Ymd',  strtotime("+ 1 month" , strtotime(date("Ymd"))));

        if($expireDate <= $nextMonth){
            return true;
        }
    }

    return false;
}

?>