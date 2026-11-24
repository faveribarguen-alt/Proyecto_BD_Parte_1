<?php
require_once __DIR__ . '/../models/DashboardModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';

class DashboardController {

    public function mostrarDashboard() {
        requerirRol([5]); // Solo administrador

        $modelo = new DashboardModel();
        $totales = $modelo->totales();
        $ultimosSieteDias = $modelo->ultimosSieteDias();
        $porEstado = $modelo->porEstado();
        $porComuna = $modelo->porComuna();

        require __DIR__ . '/../views/dashboard/IndexDashboard.php';
    }
}