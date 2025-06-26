<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . '/../../../../app/controllers/config.php');

header('Content-Type: application/json');

// Parámetros del frontend
$clienteId  = $_GET['cliente']     ?? '';
$actividades= $_GET['actividades'] ?? '';
$inicio     = $_GET['inicio']      ?? date('Y-m-d', strtotime('-7 days'));
$fin        = $_GET['fin']         ?? date('Y-m-d');

$params = [];
$sql = "SELECT a.id, a.cliente, a.actividad, a.descripcion, a.duracion,
               a.cobrado, a.cobrado_general, a.hora_inicio, a.hora_fin, a.fecha,
               u.nombres AS usuario
        FROM tb_actividades a
        LEFT JOIN tb_usuarios u ON a.id_usuario = u.id_usuarios
        WHERE 1=1";

// Cliente
if (!empty($clienteId)) {
    $stmt = $pdo->prepare("SELECT nombre_cliente, duracion_cliente, precio_cliente, moneda FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cliente) {
        echo json_encode(['registros'=>[], 'barras'=>[], 'totalHoras'=>0, 'totalCobroGeneral'=>0, 'totalCobroActividad'=>0]);
        exit;
    }
    $nombreCliente   = $cliente['nombre_cliente'];
    $precioCliente   = $cliente['precio_cliente'];
    $monedaCliente   = $cliente['moneda'];
    $sql .= " AND cliente = ?";
    $params[] = $nombreCliente;
}

// Filtrar actividades
$listaActividades = array_filter(array_map('trim', explode(',', $actividades)));
if (!empty($listaActividades)) {
    $placeholders = implode(',', array_fill(0, count($listaActividades), '?'));
    $sql .= " AND actividad IN ($placeholders)";
    $params = array_merge($params, $listaActividades);
}

// Rango de fechas
$sql .= " AND fecha BETWEEN ? AND ?";
$params[] = $inicio;
$params[] = $fin;

// Ejecutar
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar salida
$registros = [];
$barrasAgrupadas = [];
$totalSegundos = 0;
$totalCobroGeneral = 0;
$totalCobroActividad = 0;

foreach ($datos as $row) {
    $duracion = (int)$row['duracion'];
    $fecha = substr($row['fecha'], 0, 10);
    $totalSegundos += $duracion;
    $barrasAgrupadas[$fecha] = ($barrasAgrupadas[$fecha] ?? 0) + $duracion;

    // Inicializar
    $precioCobro    = 0;
    $precioUnitario = 0;

    // Cobro general
    if ($row['cobrado_general'] == 1) {
        $precioUnitario = $precioCliente;
        $precioCobro     = ($duracion / 3600) * $precioUnitario;
        $totalCobroGeneral += $precioCobro;
    }

    // Cobro por actividad
    if ($row['cobrado'] == 1) {
        $stmtAct = $pdo->prepare("SELECT precio, moneda FROM actividades WHERE nombre_actividad = ?");
        $stmtAct->execute([$row['actividad']]);
        $actividad = $stmtAct->fetch(PDO::FETCH_ASSOC);
        $precioActividad = $actividad['precio'];
        $precioUnitario  = $precioActividad;
        $precioCobro     = ($duracion / 3600) * $precioUnitario;
        $totalCobroActividad += $precioCobro;
    }

    $registros[] = [
        'id'               => $row['id'],
        'usuario'          => $row['usuario'] ?? '',
        'cliente'          => $row['cliente'],
        'actividad'        => $row['actividad'],
        'fecha'            => $fecha,
        'hora_inicio'      => $row['hora_inicio'],
        'hora_fin'         => $row['hora_fin'],
        'cobrado'          => $row['cobrado'],
        'cobrado_general'  => $row['cobrado_general'],
        'descripcion'      => $row['descripcion'],
        'duracion'         => gmdate('H:i:s', $duracion),
        'precio_unitario'  => round($precioUnitario, 2),
        'cobro_calculado'  => round($precioCobro, 2),
    ];
}

// Armar datos de gráfica
$barras = [];
foreach ($barrasAgrupadas as $f => $seg) {
    $barras[] = ['fecha'=>$f, 'horas'=> round($seg/3600,2)];
}

// Desglose por actividad para torta
$actividadData = [];
$agg = [];
foreach ($datos as $row) {
    $agg[$row['actividad']] = ($agg[$row['actividad']] ?? 0) + (int)$row['duracion'];
}
foreach ($agg as $act => $seg) {
    $actividadData[] = ['actividad'=>$act, 'horas'=> round($seg/3600,2)];
}

// Respuesta final
echo json_encode([
    'registros'            => $registros,
    'barras'               => $barras,
    'totalHoras'           => round($totalSegundos/3600, 2),
    'totalCobroGeneral'    => round($totalCobroGeneral, 2),
    'totalCobroActividad'  => round($totalCobroActividad, 2),
    'porActividad'         => $actividadData,
]);
?>
