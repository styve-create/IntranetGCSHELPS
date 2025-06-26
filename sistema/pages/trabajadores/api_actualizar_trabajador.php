<?php
include_once(__DIR__ . '/../../../app/controllers/config.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
  echo json_encode(['success' => false, 'message' => 'ID de trabajador no proporcionado.']);
  exit;
}

$campos_fecha = ['fecha_expedicion', 'fecha_nacimiento', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual'];
foreach ($campos_fecha as $campo) {
 if (!empty($data[$campo])) {
  $data[$campo] = date('Y-m-d', strtotime($data[$campo]));
} else {
  $data[$campo] = null;
}
}

$campos = [
  'nombre_completo', 'grupo_sanguineo', 'estado_civil', 'edad', 'tipo_documento',
  'numero_documento', 'fecha_expedicion', 'lugar_expedicion', 'fecha_nacimiento', 'lugar_nacimiento',
  'nivel_estudio', 'profesion', 'domicilio', 'ciudad', 'celular', 'email',
  'cuenta_bancaria', 'banco', 'tipo_cuenta', 'tipo_contrato', 'horas_contratadas',
  'salario_basico', 'auxilio_transporte', 'eps', 'codigo_pension', 'codigo_cesantias',
  'estado', 'fecha_ingreso_gcs', 'fecha_retiro_gcs', 'finalizacion_contractual',
  'tipo_retiro', 'cargo_certificado', 'nombre_contacto_emergencia', 'numero_contacto_emergencia'
];

$valores = [];
foreach ($campos as $campo) {
    $valor = $data[$campo] ?? null;

    // Si el campo es de fecha y viene vacío o null como string, guardamos NULL real
    if (in_array($campo, $campos_fecha) && (empty($valor) || strtolower($valor) === 'null')) {
        $valor = null;
    }

    $valores[$campo] = $valor;
}

try {
  $pdo->beginTransaction();
  
  $sql = "UPDATE trabajadores SET " . implode(', ', array_map(fn($campo) => "$campo = ?", array_keys($valores))) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([...array_values($valores), $id]);

  $stmt_tc = $pdo->prepare("UPDATE trabajadores_campanas SET puesto_id = ? WHERE trabajador_id = ?");
  $stmt_tc->execute([$data['cargo'], $id]);

  $pdo->commit();
  echo json_encode(['success' => true, 'message' => 'Trabajador actualizado correctamente.']);
} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>