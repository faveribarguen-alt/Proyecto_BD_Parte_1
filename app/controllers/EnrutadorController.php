<?php
require_once __DIR__ . '/../models/SolicitudModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class EnrutadorController {

    public function mostrarBitacora() {
        requerirRol([1]); // Solo enrutador

        $modelo = new SolicitudModel();
        $solicitudes = $modelo->obtenerTodasSolicitudes();

        require_once __DIR__ . '/../views/enrutador/bitacora.php';
    }
}
?>
