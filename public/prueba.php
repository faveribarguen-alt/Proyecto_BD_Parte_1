<?php
require __DIR__ . '/../app/models/DashboardModel.php';

$modelo = new DashboardModel();
echo '<pre>';
print_r($modelo->totales());
print_r($modelo->ultimosSieteDias());
print_r($modelo->porEstado());
print_r($modelo->porComuna());