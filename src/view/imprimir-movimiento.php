<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL . "movimientos");
    exit;
}
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        // Rutas y tamaños de las imágenes
        $logoLeft = __DIR__. '/../../img/ayacu.png';
        $logoRight = __DIR__ . '/../../img/reg.png';
        $logoWidth = 40;
        $logoHeight = 40;

        // Posiciones
        $yPos = 8;
        $leftX = 15;
        $rightX = $this->getPageWidth() - $logoWidth - 15; // margen derecho

        // Imagen izquierda
        if (file_exists($logoLeft)) {
            $this->Image($logoLeft, $leftX, $yPos, $logoWidth, $logoHeight);
        }

        // Imagen derecha
        if (file_exists($logoRight)) {
            $this->Image($logoRight, $rightX, $yPos, $logoWidth, $logoHeight);
        }

        // Texto centrado en el encabezado
        $this->SetY(10);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');

        // Espacio después del encabezado
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 5, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 1, 'C');
        
    }
}
// Petición cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=$ruta[1]",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
    exit;
}else{
  $respuesta = json_decode($response);
// Crear contenido HTML del cuerpo del PDF
$contenido_pdf = '
<style>
  body {
    font-family: Arial, sans-serif;
    font-size: 10pt;
  }
  .datos p {
    margin: 5px 0;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  th, td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
  }
    .p{
    text-align: center; font-weight: bold;

    }
</style>
<p class="p"> PAPELETA DE ROTACIÓN DE BIENES</p>

<div class="datos">
 <p><strong>ENTIDAD:</strong> GOBIERNO REGIONAL DE AYACUCHO</p>
  <p><strong>ENTIDAD:</strong> DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</p>
  <p><strong>ÁREA:</strong> OFICINA DE ADMINISTRACIÓN</p>
  <p><strong>ORIGEN:</strong> ' . $respuesta->amb_origen->codigo . ' - ' . $respuesta->amb_origen->detalle . '</p>
  <p><strong>DESTINO:</strong> ' . $respuesta->amb_destino->codigo . ' - ' . $respuesta->amb_destino->detalle . '</p>
  <p><strong>MOTIVO (*):</strong> ' . $respuesta->movimiento->descripcion . '</p>
</div>

<table>
  <thead>
    <tr>
      <th>ITEM</th>
      <th>CÓDIGO PATRIMONIAL</th>
      <th>NOMBRE DEL BIEN</th>
      <th>MARCA</th>
      <th>COLOR</th>
      <th>MODELO</th>
      <th>ESTADO</th>
    </tr>
  </thead>
  <tbody>';

if (!empty($respuesta->detalle)) {
    $contador = 1;
    foreach ($respuesta->detalle as $bien) {
        $contenido_pdf .= "<tr>";
        $contenido_pdf .= "<td>{$contador}</td>";
        $contenido_pdf .= "<td>{$bien->cod_patrimonial}</td>";
        $contenido_pdf .= "<td>{$bien->denominacion}</td>";
        $contenido_pdf .= "<td>{$bien->marca}</td>";
        $contenido_pdf .= "<td>{$bien->color}</td>";
        $contenido_pdf .= "<td>{$bien->modelo}</td>";
        $contenido_pdf .= "<td>{$bien->estado}</td>";
        $contenido_pdf .= "</tr>";
        $contador++;
    }
} else {
    $contenido_pdf .= "<tr><td colspan='7'>No hay datos para mostrar</td></tr>";
}


$contenido_pdf .= '</tbody>
</table>';


$fechaMovimiento = new DateTime($respuesta->movimiento->fecha_registro);
$meses = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
];
$dia = $fechaMovimiento->format('d');
$mes = $meses[(int)$fechaMovimiento->format('m')];
$anio = $fechaMovimiento->format('Y');


$contenido_pdf .= '</tbody>
</table>';


$fechaMovimiento = new DateTime($respuesta->movimiento->fecha_registro);
$meses = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
];
$dia = $fechaMovimiento->format('d');
$mes = $meses[(int)$fechaMovimiento->format('m')];
$anio = $fechaMovimiento->format('Y');
$contenido_pdf .= "
<br><br>
<div align='right'>Ayacucho, $dia de $mes del $anio</div>

c

<p style='text-align: center;'>------------------------------                                                             ------------------------------</p>  <br>   
<p style='text-align: center;'>ENTREGUÉ CONFORME                                                          RECIBÍ CONFORME</p>
";




// Crear el PDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DRE Ayacucho');
$pdf->SetTitle('Papeleta de Rotación de Bienes');
$pdf->SetMargins(15, 35, 15); // márgenes: izquierda, arriba, derecha
$pdf->SetAutoPageBreak(TRUE, 25); // margen inferior
$pdf->AddPage();

$pdf->writeHTML($contenido_pdf, true, false, true, false, '');
ob_end_clean();
$pdf->Output('reporte_movimiento.pdf', 'I');
}



?>