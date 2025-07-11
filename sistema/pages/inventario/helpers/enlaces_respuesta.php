<?php

function generarEnlaceRespuesta($formularioId, $accion, $token, $validador = null) {
    $baseUrl = 'https://gcshelps.com/intranet/sistema/pages/inventario/respuesta.php';
    
    $url = "{$baseUrl}?formulario={$formularioId}&accion={$accion}&token={$token}";
    
    if ($validador) {
        $url .= "&validador={$validador}";
    }

    return $url;
}
?>