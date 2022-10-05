<?php


echo "cron guardar datos de consumo de cfe por mes";

require_once '../src/config.php';
//CONEXIÓN BD
$connection = new mysqli(DB_HOST, DB_USR, DB_PASS, DB_DB) or die("No se puede conectar con la Base de Datos");
$connection->set_charset("utf8");
if($connection){
	echo nl2br("Conexión base de datos\n");
	$query = $connection->prepare("SELECT tokenRest FROM `usuarios` WHERE correo = 'desarrollo@byg.uy'");
	$query->execute();
	$result = $query->get_result();
	$tokenRest = $result->fetch_object()->tokenRest;
}

$arrayCompanies = array(); //todos los ruts


$opciones = array('http' =>
    array(
        'method'  => 'GET',
        'header'  => array("Accept: aplication/json", "Authorization: Bearer " . $tokenRest),
    )
);


$contexto = stream_context_create($opciones);
$resultado = file_get_contents(URL_REST.'companies', false, $contexto);//rest companies
$resultCompanies = json_decode($resultado);
echo nl2br("Se obtienen los datos de ".count($resultCompanies)." empresas\n");
foreach( $resultCompanies as $value ){
	array_push($arrayCompanies, array("rut"=>$value->rut, "estado"=>$value->estado));
}

$from = date('Ymd', strtotime("- 1 month" , strtotime(date("Y-m")."-01")));
$to = date('Ymt', strtotime("- 1 month" , strtotime(date("Y-m-d"))));

echo nl2br("Período de tiempo a consultar ".$from." - ".$to."\n");

foreach ($arrayCompanies as $companie) {

	if ( $companie['estado'] == 6 ){

		echo nl2br("Empresa ".$companie["rut"]."\n");

		$ticket = 0;//case 101: return "e-Ticket";
		$ncticket = 0; //case 102: return "Nota de crédito de e-Ticket";
		$ndticket = 0; //case 103: return "Nota de débito de e-Ticket";
		$ticketconti = 0;//case 201: return "e-Ticket Contingencia";
		$factura = 0; //case 111: return "e-Factura";
		$ncfactura = 0; //case 112: return "Nota de crédito de e-Factura";
		$ndfactura = 0; //case 113: return "Nota de débito de e-Factura";
		$facturaconti = 0; //case 211: return "e-Factura Contingencia";
		$facturaexp = 0; //case 121: return "e-Factura Exportación";
		$ncfacturaexp = 0; //case 122: return "Nota de crédito de e-Factura Exportación";
		$ndfacturaexp = 0; //case 123: return "Nota de débito de e-Factura Exportación";
		$remitoexp = 0; //case 124: return "e-Remito de Exportación";
		$ticketctaaje = 0; //case 131: return "e-Ticket Venta por Cuenta Ajena";
		$ncticketctaaje = 0; //case 132: return "Nota de crédito de e-Ticket Venta por Cuenta Ajena";
		$ndticketctaaje = 0; //case 133: return "Nota de débito de e-Ticket Venta por Cuenta Ajena";
		$facturactaaje = 0; //case 141: return "e-Factura Venta por Cuenta Ajena";
		$ncfacturactaaje = 0; //case 142: return "Nota de crédito de e-Factura Venta por Cuenta Ajena";
		$ndfaturactaaje = 0; //case 143: return "Nota de débito de e-Factura Venta por Cuenta Ajena";
		$boletaentrada = 0; //case 151: return "e-Boleta de entrada";
		$ncboletaentrada = 0; //case 152: return "Nota de crédito de e-Boleta de entrada";
		$ndboletaentrada = 0; //case 153: return "Nota de débito de e-Boleta de entrada";
		$remito = 0; //case 181: return "e-Remito";
		$resguardo = 0; //case 182: return "e-Resguardo";
		$otros = 0;

		$vouchersList = file_get_contents(URL_REST.'reports/'.$companie["rut"].'?Detallado=1&Desde='.$from.'&Hasta='.$to, false, $contexto);

		$vouchersList = json_decode($vouchersList);
		foreach ($vouchersList->reportes as $dia) {

			if( count($dia->detalle) > 0 ){


				foreach ($dia->detalle as $voucher) {
					switch ($voucher->tipoCFE) {
						case 101: $ticket = $ticket+1; break;
						case 102: $ncticket = $ncticket+1; break;
						case 103: $ndticket = $ndticket+1; break;
						case 201: $ticketconti = $ticketconti+1; break;
						//case 202: $ticketconti = $ticketconti+1; break;
						case 111: $factura = $factura+1; break;
						case 112: $ncfactura = $ncfactura+1; break;
						case 113: $ndfactura = $ndfactura+1; break;
						case 211: $facturaconti = $facturaconti+1; break;
						//case 212: $facturaconti = $facturaconti+1; break;
						case 121: $facturaexp = $facturaexp+1; break;
						case 122: $ncfacturaexp = $ncfacturaexp+1; break;
						case 123: $ndfacturaexp = $ndfacturaexp+1; break;
						case 124: $remitoexp = $remitoexp+1; break;
						case 131: $ticketctaaje = $ticketctaaje+1; break;
						case 132: $ncticketctaaje = $ncticketctaaje+1; break;
						case 133: $ndticketctaaje = $ndticketctaaje+1; break;
						case 141: $facturactaaje = $facturactaaje+1; break;
						case 142: $ncfacturactaaje = $ncfacturactaaje+1; break;
						case 143: $ndfaturactaaje = $ndfaturactaaje+1; break;
						case 151: $boletaentrada = $boletaentrada+1; break;
						case 152: $ncboletaentrada = $ncboletaentrada+1; break;
						case 153: $ndboletaentrada = $ndboletaentrada+1; break;
						case 181: $remito = $remito+1; break;
						case 182: $resguardo = $resguardo+1; break;
						default: $otros = $otros+1; brak;


					}
				}

			}

		}

		$arrayCountVouchers = array(
			"101" => $ticket,
			"102" => $ncticket,
			"103" => $ndticket,
			"201" => $ticketconti,
			"111" => $factura,
			"112" => $ncfactura,
			"113" => $ndfactura,
			"211" => $facturaconti,
			"121" => $facturaexp,
			"122" => $ncfacturaexp,
			"123" => $ndfacturaexp,
			"124" => $remitoexp,
			"131" => $ticketctaaje,
			"132" => $ncticketctaaje,
			"133" => $ndticketctaaje,
			"141" => $facturactaaje,
			"142" => $ncfacturactaaje,
			"143" => $ndfaturactaaje,
			"151" => $boletaentrada,
			"152" => $ncboletaentrada,
			"153" => $ndboletaentrada,
			"181" => $remito,
			"182" => $resguardo,
			"otros" => $otros
		);

		$vouchersJson = json_encode($arrayCountVouchers);
		$sql = "INSERT INTO `uso_cfes` (`rut`, `datos`, `periodo`) VALUES (".$companie['rut'].", '".$vouchersJson."', '202209')";
		$query = $connection->prepare($sql);
		$query->execute();
		$result = $query->get_result();
		if($result !== false){
			echo nl2br("Consulta que se intentó hacer ".$sql." resultado obtenido ".$result."\n");
		}
		echo nl2br("Procesada\n");
		echo nl2br("---------------------------------------\n");

	}
}


echo nl2br("Cerrando conexión\n");
mysqli_close($connection);
echo nl2br("Saliendo\n");

exit;
?>