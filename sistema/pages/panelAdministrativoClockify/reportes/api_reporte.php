<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');

header('Content-Type: application/json');

// Parámetros del frontend
$clienteId = $_GET['cliente'] ?? '';
$actividades = $_GET['actividades'] ?? '';
$inicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-7 days'));
$fin = $_GET['fin'] ?? date('Y-m-d');

$params = [];
$sql = "SELECT 
    a.id,
    a.id_usuario,
    a.cliente,
    c.id_cliente    AS cliente_id,     
    a.actividad,
    a.descripcion,
    a.duracion,
    a.cobrado,
    a.cobrado_general,
    a.hora_inicio,
    a.hora_fin,
    a.fecha,
    u.nombres       AS usuario
FROM tb_actividades a
LEFT JOIN clientes c 
  ON a.cliente = c.nombre_cliente     
LEFT JOIN tb_usuarios u 
  ON a.id_usuario = u.id_usuarios
WHERE 1=1";

// 🔍 Buscar el nombre del cliente si se recibió un ID
if (!empty($clienteId)) {
    $stmt = $pdo->prepare("SELECT nombre_cliente, duracion_cliente, precio_cliente, moneda FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $nombreCliente = $cliente['nombre_cliente'];
        $duracionCliente = $cliente['duracion_cliente']; // Duración por cliente en segundos
        $precioCliente = $cliente['precio_cliente']; // Precio por hora general del cliente
        $monedaCliente = $cliente['moneda']; // Moneda del cliente (USD o COP)
        $sql .= " AND cliente = ?";
        $params[] = $nombreCliente;
    } else {
        // ID inválido: retornar vacío
        echo json_encode(['registros' => [], 'barras' => [], 'totalHoras' => 0]);
        exit;
    }
}

// 🔍 Filtrar por actividades múltiples
$listaActividades = array_filter(array_map('trim', explode(',', $actividades)));
if (!empty($listaActividades)) {
    $placeholders = implode(',', array_fill(0, count($listaActividades), '?'));
    $sql .= " AND actividad IN ($placeholders)";
    $params = array_merge($params, $listaActividades);
}

// 🔍 Rango de fechas
$sql .= " AND fecha BETWEEN ? AND ?";
$params[] = $inicio;
$params[] = $fin;

// Ejecutar consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar salida
$registros = [];
$barrasAgrupadas = [];
$totalSegundos = 0;
$totalCobroGeneral = 0;  // Total cobro general
$totalCobroActividad = 0; // Total cobro por actividades

foreach ($datos as $row) {
    $duracion = (int)$row['duracion'];
    $fecha = substr($row['fecha'], 0, 10);
    $totalSegundos += $duracion;

    // Generar barras agrupadas
    $barrasAgrupadas[$fecha] = ($barrasAgrupadas[$fecha] ?? 0) + $duracion;

    // Cálculo del cobro
    $precioCobro = 0;

    // Si es cobro general
    if ($row['cobrado_general'] == 1) {
        // Cobro general se calcula con la duración total y el precio del cliente
        if ($monedaCliente == 'COP') {
            // Si es COP, calculamos el precio total
            $precioCobro = ($duracion / 3600) * $precioCliente; // Convertimos segundos a horas
        } else {
            // Si es USD, usamos el precio directamente
            $precioCobro = ($duracion / 3600) * $precioCliente; // Convertimos segundos a horas
        }
        $totalCobroGeneral += $precioCobro; // Acumulamos el cobro general
    }

    // Si es cobro por actividad
    if ($row['cobrado'] == 1) {
        // Consultamos el precio de la actividad
        $stmtActividad = $pdo->prepare("SELECT precio, moneda FROM actividades WHERE nombre_actividad = ?");
        $stmtActividad->execute([$row['actividad']]);
        $actividad = $stmtActividad->fetch(PDO::FETCH_ASSOC);

        $precioActividad = $actividad['precio'];
        $monedaActividad = $actividad['moneda'];

        // Calcular cobro por actividad
        if ($monedaActividad == 'COP') {
            // Cobro en COP
            $precioCobro = ($duracion / 3600) * $precioActividad; // Convertimos segundos a horas
        } else {
            // Cobro en USD
            $precioCobro = ($duracion / 3600) * $precioActividad; // Convertimos segundos a horas
        }
        $totalCobroActividad += $precioCobro; // Acumulamos el cobro de la actividad
    }

    // Agregar el registro con el precio calculado
  $registros[] = [
    'id'               => $row['id'],
    'usuario_id'       => $row['id_usuario'],
    'usuario'          => $row['usuario'],
    'cliente'          => $row['cliente'],         // nombre
    'cliente_id'       => $row['cliente_id'],      // ID recién traído
    'actividad'        => $row['actividad'],
    'fecha'            => $fecha,
    'hora_inicio'      => $row['hora_inicio'],
    'hora_fin'         => $row['hora_fin'],
    'cobrado'          => $row['cobrado'],
    'cobrado_general'  => $row['cobrado_general'],
    'descripcion'      => $row['descripcion'],
    'duracion'         => gmdate('H:i:s', $duracion),
    'cobro_calculado'  => $precioCobro
];
}

// Generar datos para gráfica
$barras = [];
foreach ($barrasAgrupadas as $fecha => $segundos) {
    $barras[] = [
        'fecha' => $fecha,
        'horas' => round($segundos / 3600, 2)
    ];
}

// Desglose por actividad (para gráfico de torta)
$actividadesGrafico = [];
foreach ($datos as $row) {
    $act = $row['actividad'];
    $seg = (int)$row['duracion'];
    $actividadesGrafico[$act] = ($actividadesGrafico[$act] ?? 0) + $seg;
}

// Convertir segundos a horas
$actividadData = [];
foreach ($actividadesGrafico as $nombre => $segundos) {
    $actividadData[] = [
        'actividad' => $nombre,
        'horas' => round($segundos / 3600, 2)
    ];
}

// Agregar al JSON final:
echo json_encode([
    'registros' => $registros,
    'barras' => $barras,
    'totalHoras' => round($totalSegundos / 3600, 2),
    'totalCobroGeneral' => round($totalCobroGeneral, 2),
    'totalCobroActividad' => round($totalCobroActividad, 2),
    'porActividad' => $actividadData
]);
?>
