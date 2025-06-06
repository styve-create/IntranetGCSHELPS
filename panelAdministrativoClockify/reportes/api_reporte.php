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
$sql = "SELECT a.id, a.cliente, a.actividad, a.descripcion, a.duracion, a.cobrado, a.cobrado_general, a.hora_inicio, a.hora_fin, a.fecha, u.nombres AS usuario
        FROM tb_actividades a
        LEFT JOIN tb_usuarios u ON a.id_usuario = u.id_usuarios
        WHERE 1=1";

// 🔍 Buscar el nombre del cliente si se recibió un ID
if (!empty($clienteId)) {
    $stmt = $pdo->prepare("SELECT nombre_cliente FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$clienteId]);
    $nombreCliente = $stmt->fetchColumn();

    if ($nombreCliente) {
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

foreach ($datos as $row) {
    $duracion = (int)$row['duracion'];
    $fecha = substr($row['fecha'], 0, 10);
    $totalSegundos += $duracion;

    $barrasAgrupadas[$fecha] = ($barrasAgrupadas[$fecha] ?? 0) + $duracion;

    $registros[] = [
        'id' => $row['id'],
        'usuario' => $row['usuario'] ?? '',
        'cliente' => $row['cliente'],
        'actividad' => $row['actividad'],
        'fecha' => $fecha,
        'hora_inicio' => $row['hora_inicio'],
        'hora_fin' => $row['hora_fin'],
        'cobrado' => $row['cobrado'],
        'cobrado_general' => $row['cobrado_general'],
        'descripcion' => $row['descripcion'],
        'duracion' => gmdate('H:i:s', $duracion)
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
    'porActividad' => $actividadData
]);

