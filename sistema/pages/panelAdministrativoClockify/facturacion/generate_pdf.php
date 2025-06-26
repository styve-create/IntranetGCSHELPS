<?php
// generate_pdf.php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../librerias/vendor/autoload.php';  // Ajusta la ruta a tu autoload

use Dompdf\Dompdf;

// 1) Leer JSON del body
$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}

// 2) Extraer datos (coloca validaciones según necesites)
$invoiceNumber      = htmlspecialchars($payload['invoiceNumber']);
$fechaInicio        = htmlspecialchars($payload['fechaInicio']);
$fechaFin           = htmlspecialchars($payload['fechaFin']);
$billFrom           = htmlspecialchars($payload['billFrom']);
$billTo             = htmlspecialchars($payload['billTo']);
$discountPct        = floatval($payload['discountPct']);
$notes              = nl2br(htmlspecialchars($payload['notes']));
$items              = $payload['items'];  // array de {actividad, descripcion, duracion, precioUnitario, subtotal}

// 3) Construir el HTML de la factura
$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    .header, .totals { width: 100%; margin-bottom: 20px; }
    .header td { vertical-align: top; }
    .items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items th, .items td { border: 1px solid #555; padding: 4px; }
    .right { text-align: right; }
    .notes { margin-top: 30px; }
  </style>
</head>
<body>
  <table class="header">
    <tr>
      <td>
        <strong>Invoice #: </strong> ' . $invoiceNumber . '<br>
        <strong>From:</strong><br>' . nl2br($billFrom) . '
      </td>
      <td class="right">
        <strong>Bill To:</strong><br>' . nl2br($billTo) . '<br>
        <strong>Period:</strong><br>' . $fechaInicio . ' – ' . $fechaFin . '
      </td>
    </tr>
  </table>

  <table class="items">
    <thead>
      <tr>
        <th>Actividad</th><th>Descripción</th>
        <th class="right">Duración</th><th class="right">Precio Unit.</th>
        <th class="right">Subtotal</th>
      </tr>
    </thead>
    <tbody>';
foreach ($items as $it) {
    $html .= '<tr>
      <td>'.htmlspecialchars($it['actividad']).'</td>
      <td>'.htmlspecialchars($it['descripcion']).'</td>
      <td class="right">'.htmlspecialchars($it['duracion']).'</td>
      <td class="right">'.number_format($it['precioUnitario'],2).' USD</td>
      <td class="right">'.number_format($it['subtotal'],2).' USD</td>
    </tr>';
}
$html .= '</tbody>
  </table>';

$subtotal = array_sum(array_map(fn($i)=>$i['subtotal'], $items));
$discountAmount = $subtotal * ($discountPct/100);
$total = $subtotal - $discountAmount;

$html .= '
  <table class="totals">
    <tr><td></td>
        <td class="right">Subtotal:</td>
        <td class="right">'.number_format($subtotal,2).' USD</td></tr>
    <tr><td></td>
        <td class="right">Discount ('.number_format($discountPct,1).' %):</td>
        <td class="right">- '.number_format($discountAmount,2).' USD</td></tr>
    <tr><td></td>
        <td class="right"><strong>Total:</strong></td>
        <td class="right"><strong>'.number_format($total,2).' USD</strong></td></tr>
  </table>';

if ($notes) {
  $html .= '<div class="notes"><strong>Notas:</strong><br>'.$notes.'</div>';
}

$html .= '</body></html>';

// 4) Crear PDF con Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();

// 5) Devolver PDF
header('Content-Type: application/pdf');
echo $dompdf->output();