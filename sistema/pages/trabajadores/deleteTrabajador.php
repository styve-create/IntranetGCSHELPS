<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../analisisRecursos.php');

include_once(__DIR__ . '/../../../app/controllers/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

$id = $_POST['id'] ?? null;
$confirmado = isset($_POST['confirmar']) && $_POST['confirmar'] == 'true';

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nombre_completo, fecha_retiro_falcon, fecha_retiro_gcs FROM trabajadores WHERE id = ?");
    $stmt->execute([$id]);
    $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trabajador) {
        echo json_encode(['status' => 'error', 'message' => 'Trabajador no encontrado.']);
        exit;
    }

    $nombre = $trabajador['nombre_completo'];
    $retiroFalcon = $trabajador['fecha_retiro_falcon'];
    $retiroGcs = $trabajador['fecha_retiro_gcs'];

    $fechasValidas = array_filter([$retiroFalcon, $retiroGcs]);
    $advertencia = '';
    $mostrarAdvertencia = false;

    if (!empty($fechasValidas)) {
        $fechaRetiroMasReciente = max($fechasValidas);
        $fechaRetiro = new DateTime($fechaRetiroMasReciente);
        $limite = (clone $fechaRetiro)->modify('+3 years');
        $hoy = new DateTime();

        if ($hoy < $limite && !$confirmado) {
            // Mostrar advertencia antes de eliminar
            $advertencia = "⚠️ El trabajador \"$nombre\" se retiro el " . $fechaRetiro->format('Y-m-d') .
                ". Segun politica, no debería eliminarse antes del " . $limite->format('Y-m-d') . 
                ". ¿Deseas continuar?";
            echo json_encode([
                'status' => 'advertencia',
                'advertencia' => $advertencia
            ]);
            exit;
        }
    }

    // Si confirmado o no hay restricción, se procede con la eliminación
    $stmt = $pdo->prepare("DELETE FROM trabajadores WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Trabajador eliminado correctamente.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo eliminar el trabajador.'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}

?>