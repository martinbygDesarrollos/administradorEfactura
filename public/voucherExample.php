<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <style type="text/css">

            pre {
                font-family: Arial, sans-serif;
                white-space: pre-wrap;
            }

            .border-secondary {
                border-left: 6px solid <?php echo $_GET["colorSecundario"];?>;
            }

            .detail-line {
                padding: 3px 10px;
                border-bottom: <?php echo $_GET["detailLineWidth"]." ".$_GET["lineStyle"]." ".$_GET["detailLineColor"]; ?>;
            }

            .column-primary {
                color: <?php echo $_GET["totalColor"];?>;
                background: <?php echo $_GET["colorPrincipal"];?>;
            }

            .column-odd {
              background-color: <?php echo $_GET["descColor"];?>; }

            .column-even {
                background-color: <?php echo $_GET["colorPrecio"];?>; }

            .warning {
                color: #9F6000 !important;
                background-color: #FEEFB3;
                padding: 4px;
                width: 90%;
                margin: 10px 22px;
                font-family: Arial, sans-serif;
                text-align: center;
                vertical-align: center;
                border: 1px solid #f0dd93;
            }

            body {
                position: relative;
                width: 100%;
                max-width: 24cm;   
                margin: 0px auto; 
                color: #555555;
                font-family: Arial, sans-serif;
                font-size: 9pt;
            }
        </style>

    </head>

  <body>
<center><div class="warning">
                Este comprobante fue generado en un ambiente de pruebas y carece de validez legal
            </div></center><br><div style="overflow: auto; width: 100%; padding-bottom: 20px; padding-top: 8px; margin-bottom: 20px; border-bottom: 1px solid #AAAAAA;">
    <div style="float: left;">
      <img src="">
    </div>
    <div style="float: left; margin-left: 8px;">
      <h2 style="font-size: <?php echo $_GET["tamEmisor"];?>; font-weight: normal; margin: 0;">NOMBRE EMPRESA EMISORA</h2>
      <div><strong>Nombre sucursal</strong></div>
      <div>DIRECCIÓN, CALLE 1111</div>
      <div></div>
      <div><a style="color: #0087C3; text-decoration: none;" href="mailto:correo@prueba">correo@prueba</a></div>
    </div>
    <div style="float: right; text-align: right;">
      <h2 style="font-size: 1.4em; font-weight: normal; margin: 0;">RUT 123456789000</h2><div>EFactura A1</div><div><strong>CONTADO</strong></div>
    </div>
  </div>  
    <table style="overflow: visible; width: 100%; page-break-inside:auto;">
      <tr>
        <td class="border-secondary" style="padding-left: 6px;">
          <div>RUT COMPRADOR:</div>
          <h2 style="font-size: 1.2em; font-weight: normal; margin: 0;">123456789001</h2>
          <div style="font-size: 1.2em">NOMBRE EMPRESA COMPRADOR</div>
          <div>DIRECCIÓN, CALLE 1234, LOCALIDAD</div>
        </td>
        <td style="text-align: right; color: #444444; font-size:1.05em;">
          <div>Fecha de emisi&oacute;n: 01/01/2000</div></td>
      </tr>
    </table>
  
    <table cellspacing="0" cellpadding="0" style="width: 100%; margin-top:20px; margin-bottom: 20px;">
      <thead>
        <tr>
          <th class="detail-line column-primary" style="white-space: nowrap; font-weight: normal; padding: 10px;">#</th>
          <th class="detail-line column-odd" style="white-space: nowrap; font-weight: normal; padding: 10px; text-align: left;">DESCRIPCI&Oacute;N </th>
          <th class="detail-line column-even" id="thEvenVoucherPreview" style="white-space: nowrap; font-weight: normal; padding: 10px;">P. UNITARIO</th>
          <th class="detail-line column-odd" style="white-space: nowrap; font-weight: normal; padding: 10px;">CANTIDAD</th>
          <th class="detail-line column-primary" style="white-space: nowrap; font-weight: normal; padding: 10px;">TOTAL</th></tr></thead><tbody>

            <tr style="page-break-inside: avoid;">
              <td class="detail-line column-primary" style=s"text-align: center;font-size:1.1em;">1</td>
              <td class="detail-line column-odd" style="text-align: left; color:#505050;">
                <span style="margin: 0 0 0.1em 0;font-size:1.05em">Artículo</span>
                <span></span>
              </td><td class="detail-line column-even" id="tdEvenVoucherPreview" style="text-align: right; color:#505050;">0.00</td>
              <td class="detail-line column-odd" style="text-align: right; color:#505050;">1 </td>
              <td class="detail-line column-primary" style="text-align: right;">0.00</td>
            </tr>
            <tr style="page-break-inside: avoid;">
              <td class="detail-line column-primary" style=s"text-align: center;font-size:1.1em;">1</td>
              <td class="detail-line column-odd" style="text-align: left; color:#505050;">
                <span style="margin: 0 0 0.1em 0;font-size:1.05em">Artículo</span>
                <span></span>
              </td><td class="detail-line column-even" id="tdEvenVoucherPreview" style="text-align: right; color:#505050;">0.00</td>
              <td class="detail-line column-odd" style="text-align: right; color:#505050;">1 </td>
              <td class="detail-line column-primary" style="text-align: right;">0.00</td>
            </tr>
            <tr style="page-break-inside: avoid;">
              <td class="detail-line column-primary" style=s"text-align: center;font-size:1.1em;">1</td>
              <td class="detail-line column-odd" style="text-align: left; color:#505050;">
                <span style="margin: 0 0 0.1em 0;font-size:1.05em">Artículo</span>
                <span></span>
              </td><td class="detail-line column-even" id="tdEvenVoucherPreview" style="text-align: right; color:#505050;">0.00</td>
              <td class="detail-line column-odd" style="text-align: right; color:#505050;">1 </td>
              <td class="detail-line column-primary" style="text-align: right;">0.00</td>
            </tr>

          <tr style="page-break-inside: avoid;">
<td colspan="2"></td><td colspan="3"><table style="width:100%" cellspacing="0" cellpadding="0"><tr>
          <td style="padding: 10px 20px;
            border-bottom: none;font-size: 0.8em;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong>GRAVADO TASA B&Aacute;SICA</strong></td>
          <td style="padding: 10px 20px;
            border-bottom: none;
            text-align: right;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong><span style="float:left">$&nbsp;</span><span style="float:right;">0.00</span></strong></td>
        </tr><tr>
          <td style="padding: 10px 20px;
            border-bottom: none;font-size: 0.8em;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong>IVA TASA B&Aacute;SICA</strong></td>
          <td style="padding: 10px 20px;
            border-bottom: none;
            text-align: right;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong><span style="float:left">$&nbsp;</span><span style="float:right;">0.00</span></strong></td>
        </tr><tr>
          <td style="padding: 10px 20px;
            border-bottom: none;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong>TOTAL</strong></td>
          <td style="padding: 10px 20px;
            border-bottom: none;
            text-align: right;
            white-space: nowrap; 
            border-top: 1px solid #AAAAAA;"><strong><span style="float:left">$&nbsp;</span><span style="float:right;">0.00</span></strong></td>
        </tr></table></td></tr></tbody><tr><td>&nbsp;</td></tr><tr style="page-break-inside: avoid;"></tr>
    <tfoot style="display: table-footer-group;"><tr><td style="padding-top:6px;" colspan="5">

    <?php if($_GET["infoAdicional"] != "") { ?>
    <table class="border-secondary" style="width: 100%; margin-bottom: 6px;">
      <tr style="page-break-inside: avoid;">
        <td colspan="5">
          <span style="color:#0087C3;">INFORMACIÓN ADICIONAL</span><br>
          <pre style="white-space: pre-wrap;"><?php echo $_GET["infoAdicional"];?></pre>
        </td>
      </tr>
    </table>
    <?php } ?>

    <table class="border-secondary" style="width: 100%;">
      <tr>
        <td style="text-align: center;">
          <img src="data:image/png;base64,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"/>
        </td>
        <td>
          <div>Fecha emisor: 01/01/2000</div>
          <div>Puede verificar comprobante en<br>www.efactura.com.uy</div>
          <div>IVA al dia</div>
          <div><br>C&oacute;d. de seguridad: pwMKvs</div>
        </td>
        <td style="text-align: right; color: #777777;">
          <div>CAE: 00000000001</div>
          <div>Rango: A1 - A1000000</div>
          <div style="border:1px solid; min-width:2cm; min-height:1cm; text-align: center; margin-top: 10px;">
          Fecha de vencimiento CAE<br>01/01/2023</div>
        </td>
      </tr>
    </table></td></tr></tfoot></table></body></html>