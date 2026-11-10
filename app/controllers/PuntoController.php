<?php
require_once __DIR__ . '/../models/PuntoModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class PuntoController {

    // Mapa y lista de los puntos de atención (los ven todos los perfiles)
    public function mostrarPuntos() {
        requerirRol([1, 2, 3, 4, 5]);

        $modelo = new PuntoModel();
        $puntos = $modelo->obtenerPuntos();

        require_once __DIR__ . '/../views/puntos/mapa.php';
    }
}
?>
