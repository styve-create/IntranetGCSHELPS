<?php
require_once(__DIR__ . '/../../../app/controllers/config.php');

$id = $_GET['id'] ?? null;
if (!$id) exit;

$stmt = $pdo->prepare("SELECT * FROM formularios_asignacion WHERE id = ?");
$stmt->execute([$id]);
$formulario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$formulario) exit;

// Decodificar equipos y seriales
$equipos = json_decode($formulario['equipos'], true) ?? [];
$seriales = json_decode($formulario['seriales'], true) ?? [];

?>
<tr data-id="<?= $formulario['id'] ?>">
  <td><?= htmlspecialchars($formulario['numero_formulario']) ?></td>
  <td><?= date('Y-m-d', strtotime($formulario['fecha_registro'])) ?></td>
  <td><?= htmlspecialchars($formulario['nombre']) ?></td>
  <td><?= htmlspecialchars($formulario['documento']) ?></td>
  <td>
    <button class="btn btn-info btn-sm ver-detalle" 
            data-formulario='<?= json_encode($formulario, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
      <i class="fas fa-eye"></i> Ver
    </button>
  </td>
  <td>
    <a href="#" class="btn btn-warning enlaceDinamicos" data-link="inventarioActualizarRegistros" data-id="<?= $formulario['id'] ?>">
      <i class="fas fa-edit"></i> Actualizar
    </a>
  </td>
  <td>
    <button class="btn btn-success btn-recibir"
      data-id="<?= $formulario['id'] ?>"
      data-equipos='<?= htmlspecialchars(json_encode($equipos)) ?>'
      data-seriales='<?= htmlspecialchars(json_encode($seriales)) ?>'>
      <i class="fas fa-box-open"></i> Recibir
    </button>
  </td>
</tr>