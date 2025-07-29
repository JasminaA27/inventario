<?php

// Petición cURL
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=imprimir_movimientos&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] ,
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
}

$respuesta = json_decode($response);
$movimientos = $respuesta->movimientos;

// Incluir TCPDF y crear clase extendida
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


// Crear contenido HTML del cuerpo del PDF
$contenido_pdf = '
<br></br>

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
<p class="p"> MOVIMIENTOS</p>



<table>
  <thead>
    <tr>
      <th>ITEM</th>
      <th>ID_AMBIENTE_ORIGEN</th>
      <th>ID_AMBIENTE_DESTINO</th>
      <th>ID_USUARIO_REGISTRO</th>
      <th>FECHA_REGISTRO</th>
      <th>DESCRIPCIÓN</th>
      <th>ID_IES</th>
    </tr>
  </thead>
  <tbody>';

$contador = 1;
foreach ($movimientos as $movimiento) {
    $contenido_pdf .= "<tr>";
    $contenido_pdf .= "<td>{$contador}</td>";
    $contenido_pdf .= "<td>{$movimiento->origen}</td>";
    $contenido_pdf .= "<td>{$movimiento->destino}</td>";
    $contenido_pdf .= "<td>{$movimiento->usuario}</td>";
    $contenido_pdf .= "<td>{$movimiento->fecha_registro}</td>";
    $contenido_pdf .= "<td>{$movimiento->descripcion}</td>";
    $contenido_pdf .= "<td>{$movimiento->institucion}</td>";
    $contenido_pdf .= "</tr>";
    $contador++;
}

$contenido_pdf .= '</tbody>
</table>';

date_default_timezone_set('America/Lima');
$fechaActual = new DateTime();  // Fecha y hora actual en Lima
$meses = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
];
$dia = $fechaActual->format('d');
$mes = $meses[(int)$fechaActual->format('m')];
$anio = $fechaActual->format('Y');

$contenido_pdf .= '</tbody>
</table>';



// Crear el PDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DRE Ayacucho');
$pdf->SetTitle('MOVIMIENTOS');
$pdf->SetMargins(15, 35, 15); // márgenes: izquierda, arriba, derecha
$pdf->SetAutoPageBreak(TRUE, 25); // margen inferior
$pdf->AddPage();
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

//firma
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, 'Ayacucho, '.$dia.' de '.$mes.' del '.$anio, 0, 1, 'R');

$pdf->Ln(20);
$pdf->Cell(85, 5, '------------------------------', 0, 0, 'C');
$pdf->Cell(85, 5, '------------------------------', 0, 1, 'C');

$pdf->Cell(85, 5, 'ENTREGUÉ CONFORME', 0, 0, 'C');
$pdf->Cell(85, 5, 'RECIBÍ CONFORME', 0, 1, 'C');
ob_end_clean();
$pdf->Output('reporte_movimiento.pdf', 'I');
?>