<?php
// Requerir la librería de Dompdf
require_once(__DIR__ . '/../../../../librerias/vendor/autoload.php');

use Dompdf\Dompdf;
$dompdf = new Dompdf;

// Recibir los datos enviados por el frontend
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si los datos fueron recibidos correctamente
if (!$data) {
    log_index1("ERROR: No se recibieron datos válidos.");
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos válidos']);
    exit;
}

// Extraer los datos
$totalHoras = $data['totalHoras'];
$totalCobro = $data['totalCobro'];
$totalCobroGeneral = $data['totalCobroGeneral'];
$totalCobroActividad = $data['totalCobroActividad'];
$imgDataBarras = $data['imgDataBarras'];
$imgDataCircular = $data['imgDataCircular'];
$registros = $data['registros'];  
$fechaInicio = $data['inicio'] ?? null;
$fechaFin    = $data['fin']    ?? null;

// 1) Reagrupar POR ACTIVIDAD
$actividades_acumuladas = [];
$total_segundos = 0;
foreach ($registros as $r) {
    $seg = strtotime($r['duracion']) - strtotime('TODAY');
    $act = $r['actividad'];
    if (!isset($actividades_acumuladas[$act])) {
        $actividades_acumuladas[$act] = 0;
    }
    $actividades_acumuladas[$act] += $seg;
    $total_segundos += $seg;
}

// Convertir la duración de segundos a horas y minutos para mostrar
function formatDuration($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return sprintf("%02d:%02d", $hours, $minutes);
}

if ($fechaInicio) {
    // viene en "YYYY-MM-DD", así que podemos usar new DateTime()
    $d1 = new DateTime($fechaInicio);
    $fi = $d1->format('m/d/Y');
} else {
    $fi = '';
}

if ($fechaFin) {
    $d2 = new DateTime($fechaFin);
    $ff = $d2->format('m/d/Y');
} else {
    $ff = '';
}
$cliente = !empty($registros) ? htmlspecialchars($registros[0]['cliente']) : '';

// Construyo el HTML
$html = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
:root {
  --step-1: clamp(0.3686rem, 0.6197rem - 0.3249vw, 0.5549rem);
  --step-2: clamp(0.4608rem, 0.6812rem - 0.2843vw, 0.6243rem);
  --step-3: clamp(0.576rem, 0.775rem - 0.234vw, 0.725rem);
  --step-4: clamp(0.720rem, 0.900rem - 0.180vw, 0.900rem);
}

.header-table { 
  margin-bottom: var(--step-4); 
}
.header-left h1 {
  font-size: var(--step-3);
  margin: 0 0 var(--step-1) 0;
}
.header-left .dates {
  font-size: var(--step-2);
  color: #555;
}

/* El círculo del logo */
.logo-placeholder {
  width: 120px;
  height: 120px;
  border: 2px solid #666;
  border-radius: 50%;
  display: inline-block;
  box-sizing: border-box;
}

/* Centrar el texto de dentro */
.logo-inner {
  display: table;
  width: 100%;
  height: 100%;
  text-align: center;
}
.logo-inner > div {
  display: table-row;
}
.logo-line1, .logo-line2, .logo-line3 {
  display: table-cell;
  vertical-align: middle;
}
.logo-line1 { font-size: calc(var(--step-2) * 0.9); }
.logo-line2 { font-size: calc(var(--step-3) * 1.2); font-weight: bold; }
.logo-line3 { font-size: calc(var(--step-2) * 0.8); text-transform: uppercase; }

.summary {
  display: flex;
  gap: var(--step-4);
  margin: var(--step-4) 0;
  align-items: baseline;
}
.summary span { font-size: var(--step-3); }
.summary span strong { margin-right: var(--step-2); }

.chart { text-align: center; margin-bottom: var(--step-4); }
.chart img { width: 80%; height: auto; }

.client-name {
  font-size: var(--step-3);
  margin-bottom: var(--step-3);
}

.activities-list { margin-top: var(--step-3); }
.activity-block {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--step-2) 0;
  border-bottom: 1px solid #ccc;
}
.activity-block span { font-size: var(--step-3); }
.activity-block span strong { margin-right: var(--step-1); }

  </style>
</head>
<body>


<table class="header-table" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td class="header-left" valign="middle">
      <h1>Summary report</h1>
      <div class="dates">' . $fi . ' – ' . $ff . '</div>
    </td>
    <td class="header-right" valign="middle" align="right">
      <div class="logo-placeholder">
        <div class="logo-inner">
          <div class="logo-line1">Your</div>
          <div class="logo-line2">LOGO</div>
          <div class="logo-line3">-GOES HERE-</div>
        </div>
      </div>
    </td>
  </tr>
</table>

<br>
  <div class="summary">
    <span><strong>Total Hours:</strong> '       . htmlspecialchars($totalHoras)    . '</span>
    <span><strong>Billable Hours:</strong> '    . htmlspecialchars($totalCobroGeneral) . '</span>
    <span><strong>Total Amount (USD):</strong> ' . number_format($totalCobro, 2)     . '</span>
  </div>
<br>
  <div class="chart">
    <img src="' . $imgDataBarras . '" alt="Bar Chart">
  </div>
<br>
  <div class="client-name"><strong>Client:</strong> ' . $cliente . '</div>
<br>
  <div class="activities-list">';
      foreach ($actividades_acumuladas as $act => $secs) {
          $dur = formatDuration($secs);
          $pct = $total_segundos
                 ? round($secs / $total_segundos * 100, 1)
                 : 0;
          $html .= '
      <div class="activity-block">
       <span><strong>Task Name:</strong> ' . htmlspecialchars($act) . '</span>
        <span><strong>Time Spent:</strong> ' . $dur . '</span>
        <span><strong>Total %:</strong> ' . $pct . '%</span>
      </div><br>';
      }
$html .= '
  </div>
</body>
</html>';



// Cargar el HTML en Dompdf
$dompdf->loadHtml($html);

// Establecer tamaño de papel y orientación
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Obtener el contenido del PDF generado
$pdfContent = $dompdf->output();

// Verificar si el contenido del PDF está vacío
if (empty($pdfContent)) {
    echo "ERROR: El contenido del PDF está vacío.";
    exit;
}

// Enviar el contenido del PDF para la descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_de_tiempos.pdf"');
echo $pdfContent;
exit;
?>