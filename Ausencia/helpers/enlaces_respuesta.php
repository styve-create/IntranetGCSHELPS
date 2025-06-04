<?php
function construirEnlacesRespuesta($numero_formulario, $etapa) {
    $baseUrl = 'https://gcshelps.com/intranet/Ausencia/respuesta.php';

    $url_aprobar = "$baseUrl?form=$numero_formulario&accion=aprobar&etapa=$etapa";
    $url_rechazar = "$baseUrl?form=$numero_formulario&accion=rechazar&etapa=$etapa";

    return [$url_aprobar, $url_rechazar];
}
