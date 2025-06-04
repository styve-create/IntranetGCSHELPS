<?php

function generarEnlaceRespuesta($formularioId, $accion, $token) {
    $baseUrl = 'https://gcshelps.com/intranet/sistema/pages/inventario/respuesta.php';
    return "{$baseUrl}?formulario={$formularioId}&accion={$accion}&token={$token}";
}
?>