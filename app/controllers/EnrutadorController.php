<?php
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class EnrutadorController {

    // Bitácora del día: por defecto hoy, o la fecha que elija el enrutador
    public function mostrarBitacora() {
        requerirRol([1]); // Solo enrutador

        $fecha = $_GET['fecha'] ?? '';
        $fechaObj = DateTime::createFromFormat('!Y-m-d', $fecha);
        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            $fecha = (new DateTime('today'))->format('Y-m-d');
        }

        $modelo = new BitacoraModel();
        $solicitudes = $modelo->obtenerSolicitudesDelDia($fecha);

        require_once __DIR__ . '/../views/enrutador/bitacora.php';
    }
}
?>
