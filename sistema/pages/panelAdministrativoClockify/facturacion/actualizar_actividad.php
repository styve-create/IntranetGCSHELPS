<?php
include_once(__DIR__ . '/../../../../app/controllers/config.php');


// Siempre responder como JSON
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener y decodificar los datos JSON
$input = json_decode(file_get_contents("php://input"), true);

// Validar campos obligatorios
$camposObligatorios = ['cliente', 'actividad', 'hora_inicio', 'hora_fin', 'fecha', 'cobrado', 'descripcion', 'id', 'cobradoGeneral'];
foreach ($camposObligatorios as $campo) {
    if (!isset($input[$campo])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Falta el campo '$campo'"]);
        exit;
    }
}

try {
    // Paso 1: obtener el nombre del cliente a partir del ID
    $stmtCliente = $pdo->prepare("SELECT nombre_cliente FROM clientes WHERE id_cliente = ?");
    $stmtCliente->execute([$input['cliente']]);
    $clienteNombre = $stmtCliente->fetchColumn();

    if (!$clienteNombre) {
        throw new Exception("Cliente no encontrado con ID " . $input['cliente']);
    }

    // Paso 2: Obtener hora_inicio y hora_fin actuales
    $stmtActual = $pdo->prepare("SELECT hora_inicio, hora_fin FROM tb_actividades WHERE id = ?");
    $stmtActual->execute([$input['id']]);
    $actividadActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

    $horaInicioActual = $actividadActual['hora_inicio'];
    $horaFinActual = $actividadActual['hora_fin'];

    // Paso 3: Detectar si se modificaron las horas
    $modificoHoras = $horaInicioActual !== $input['hora_inicio'] || $horaFinActual !== $input['hora_fin'];

    // Paso 4: Si cambió, calcular duración en segundos
    if ($modificoHoras) {
        $t1 = strtotime($input['hora_inicio']);
        $t2 = strtotime($input['hora_fin']);

        if ($t2 < $t1) {
            throw new Exception("La hora de fin no puede ser anterior a la hora de inicio.");
        }

        $duracionSegundos = $t2 - $t1;

        $sql = "UPDATE tb_actividades SET 
                    cliente = ?, 
                    actividad = ?, 
                    hora_inicio = ?, 
                    hora_fin = ?, 
                    fecha = ?, 
                    cobrado = ?, 
                    cobrado_general = ?,
                    descripcion = ?, 
                    duracion = ?
                WHERE id = ?";

        $params = [
            $clienteNombre,
            $input['actividad'],
            $input['hora_inicio'],
            $input['hora_fin'],
            $input['fecha'],
            $input['cobrado'],
            $input['cobradoGeneral'],
            $input['descripcion'],
            $duracionSegundos,
            $input['id']
        ];
    } else {
        // Sin modificación de horas
        $sql = "UPDATE tb_actividades SET 
                    cliente = ?, 
                    actividad = ?, 
                    hora_inicio = ?, 
                    hora_fin = ?, 
                    fecha = ?, 
                    cobrado = ?, 
                    cobrado_general = ?,
                    descripcion = ?
                WHERE id = ?";

        $params = [
            $clienteNombre,
            $input['actividad'],
            $input['hora_inicio'],
            $input['hora_fin'],
            $input['fecha'],
            $input['cobrado'],
             $input['cobradoGeneral'],
            $input['descripcion'],
            $input['id']
        ];
    }

    // Ejecutar consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar actividad: ' . $e->getMessage()
    ]);
}
?>