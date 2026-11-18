<?php
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';
require_once __DIR__ . '/../libs/fpdf/fpdf.php';

class InformeController {

    // Genera el PDF con el resumen de solicitudes de un período
    public function resumenPdf() {
        requerirRol([5]); // Solo administrador

        $desde = $_GET['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? '';

        if (!$this->fechaValida($desde) || !$this->fechaValida($hasta) || $desde > $hasta) {
            guardarMensaje('error', 'Elige un rango de fechas válido.');
            header('Location: index.php?action=informes');
            exit();
        }

        $modelo = new InformeModel();
        $porEstado = $modelo->solicitudesPorEstado($desde, $hasta);
        $porPunto = $modelo->solicitudesPorPunto($desde, $hasta);

        $pdf = new FPDF();
        $pdf->AddPage();

        // Título y datos del informe
        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->Cell(0, 10, $this->t('Resumen de solicitudes'), 0, 1);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->Cell(0, 7, $this->t("Período: $desde a $hasta"), 0, 1);
        $pdf->Cell(0, 7, $this->t('Generado el ' . date('d/m/Y H:i') . ' por ' . ($_SESSION['nombre_completo'] ?? '')), 0, 1);
        $pdf->Ln(6);

        // Tabla 1: solicitudes por estado
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->Cell(0, 8, $this->t('Solicitudes por estado'), 0, 1);

        $this->encabezadoTabla($pdf, 'Estado', 'Total');
        $suma = 0;
        foreach ($porEstado as $fila) {
            $pdf->Cell(130, 8, $this->t(strtoupper($fila['estado'])), 1);
            $pdf->Cell(40, 8, $fila['total'], 1, 1, 'C');
            $suma += $fila['total'];
        }
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->Cell(130, 8, 'TOTAL', 1);
        $pdf->Cell(40, 8, $suma, 1, 1, 'C');
        $pdf->Ln(8);

        // Tabla 2: solicitudes por punto de atención
        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->Cell(0, 8, $this->t('Solicitudes por punto de atención'), 0, 1);

        $this->encabezadoTabla($pdf, 'Punto de atención', 'Total');
        // recorre $porPunto con un foreach, igual que en la tabla de arriba.
        // Cada fila trae $fila['nombre'] y $fila['total'].
        foreach ($porPunto as $fila) {
            $pdf->Cell(130, 8, $this->t($fila['nombre']), 1);
            $pdf->Cell(40, 8, $fila['total'], 1, 1, 'C');
        }

        $pdf->Output('I', 'resumen_solicitudes.pdf');
    }

    // Genera el PDF de solicitudes vencidas o por vencer en los próximos N días
    public function porVencerPdf() {
        requerirRol([5]); // Solo administrador

        $dias = filter_var($_GET['dias'] ?? '', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 30]
        ]);

        if ($dias === false) {
            guardarMensaje('error', 'Los días deben ser un número entre 1 y 30.');
            header('Location: index.php?action=informes');
            exit();
        }

        $modelo = new InformeModel();
        $solicitudes = $modelo->solicitudesPorVencer($dias);

        // Hoja horizontal, porque la tabla tiene varias columnas
        $pdf = new FPDF('L');
        $pdf->AddPage();

        $pdf->SetFont('Helvetica', 'B', 18);
        $pdf->Cell(0, 10, $this->t('Solicitudes vencidas o por vencer'), 0, 1);
        $pdf->SetFont('Helvetica', '', 11);
        $pdf->Cell(0, 7, $this->t("Vencen en los próximos $dias días o ya vencieron"), 0, 1);
        $pdf->Cell(0, 7, $this->t('Generado el ' . date('d/m/Y H:i') . ' por ' . ($_SESSION['nombre_completo'] ?? '')), 0, 1);
        $pdf->Ln(6);

        // Encabezado de la tabla
        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->SetFillColor(0, 86, 179);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(45, 8, 'Radicado', 1, 0, 'L', true);
        $pdf->Cell(65, 8, 'Ciudadano', 1, 0, 'L', true);
        $pdf->Cell(65, 8, $this->t('Punto de atención'), 1, 0, 'L', true);
        $pdf->Cell(30, 8, 'Estado', 1, 0, 'L', true);
        $pdf->Cell(30, 8, 'Vence', 1, 0, 'C', true);
        $pdf->Cell(40, 8, $this->t('Días restantes'), 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', 10);

        foreach ($solicitudes as $fila) {
            $pdf->Cell(45, 8, $fila['radicado'], 1);
            $pdf->Cell(65, 8, $this->t($fila['nombres'] . ' ' . $fila['apellidos']), 1);
            $pdf->Cell(65, 8, $this->t($fila['punto']), 1);
            $pdf->Cell(30, 8, $this->t($fila['estado']), 1);
            $pdf->Cell(30, 8, $fila['fecha_limite'], 1, 0, 'C');
            $pdf->Cell(40, 8, $fila['dias_restantes'], 1, 1, 'C');

        }

        if (count($solicitudes) === 0) {
            $pdf->Cell(0, 10, $this->t('No hay solicitudes en este rango.'), 1, 1, 'C');
        }

        $pdf->Output('I', 'solicitudes_por_vencer.pdf');
    }

    // Dibuja la fila azul con los títulos de una tabla de dos columnas
    private function encabezadoTabla($pdf, $titulo1, $titulo2) {
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetFillColor(0, 86, 179);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(130, 8, $this->t($titulo1), 1, 0, 'L', true);
        $pdf->Cell(40, 8, $this->t($titulo2), 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', 11);
    }

    // Comprueba que una fecha tenga el formato AAAA-MM-DD y exista
    private function fechaValida($fecha) {
        $d = DateTime::createFromFormat('!Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }

    // FPDF no entiende UTF-8: convertimos el texto antes de escribirlo
    private function t($texto) {
        return mb_convert_encoding((string) $texto, 'ISO-8859-1', 'UTF-8');
    }

    public function mostrarInformes() {
        requerirRol([5]); // Solo administrador

        $inicioMes = (new DateTime('first day of this month'))->format('Y-m-d');
        $hoy = (new DateTime('today'))->format('Y-m-d');

        require __DIR__ . '/../views/informes/index.php';
    } 
}