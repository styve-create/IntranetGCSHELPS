<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$campos_fecha = ['fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'];

function formatearFechaMySQL($fecha) {
    if (!$fecha) return null;
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    return $fechaObj ? $fechaObj->format('Y-m-d') : null;
}

$campos = [
    'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
    'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
    'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
    'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
    'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
    'estado', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual', 'tipo_retiro',
    'cargo_certificado', 'nombre_contacto_emergencia', 'numero_contacto_emergencia'
];

$valores = [];
foreach ($campos as $campo) {
    $valores[$campo] = in_array($campo, $campos_fecha) ? formatearFechaMySQL($data[$campo] ?? null) : ($data[$campo] ?? null);
}

if (empty($valores['nombre_completo']) || empty($valores['numero_documento']) || empty($data['cargo']) || empty($data['campana'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM tb_campanas WHERE nombre = ?");
$stmt->execute([$data['campana']]);
$id_campana = $stmt->fetchColumn();

if (!$id_campana) {
    echo json_encode(['success' => false, 'message' => 'Campaña no válida']);
    exit;
}

$columns = implode(', ', array_keys($valores));
$placeholders = implode(', ', array_fill(0, count($valores), '?'));
$sql = "INSERT INTO trabajadores ($columns) VALUES ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_values($valores));

$trabajador_id = $pdo->lastInsertId();
$stmt_rel = $pdo->prepare("INSERT INTO trabajadores_campanas (trabajador_id, campana_id, puesto_id) VALUES (?, ?, ?)");
$stmt_rel->execute([$trabajador_id, $id_campana, $data['cargo']]);

echo json_encode(['success' => true, 'message' => 'Trabajador creado exitosamente']);
exit;
?>