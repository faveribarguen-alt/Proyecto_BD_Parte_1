<?php
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../config/AuthHelper.php';
require_once __DIR__ . '/../models/SolicitudModel.php';
require_once __DIR__ . '/../models/CargaModel.php';
require_once __DIR__ . '/../models/PuntoModel.php';

class EnrutadorController {

    public function mostrarBitacora() {
        requerirRol([1]);

        $fecha = $_GET['fecha'] ?? '';
        $fechaObj = DateTime::createFromFormat('!Y-m-d', $fecha);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            $fecha = (new DateTime('today'))->format('Y-m-d');
        }

        $modelo = new BitacoraModel();
        $solicitudes = $modelo->obtenerSolicitudesDelDia($fecha);

        require_once __DIR__ . '/../views/enrutador/bitacora.php';
    }

    public function mostrarInicio() {
        requerirRol([1]);

        $solicitudModel = new SolicitudModel();
        $cargaModel = new CargaModel();

        $resumen = $solicitudModel->obtenerResumenEnrutador();
        $solicitudesRecientes = $solicitudModel->obtenerSolicitudesRecientesEnrutador();
        $hojasProgramadas = $cargaModel->contarHojasProgramadas();
        $hojasRecientes = $cargaModel->obtenerHojasRecientes();

        require_once __DIR__ . '/../views/enrutador/inicio.php';
    }

    // GEORREFERENCIACIÓN
    public function mostrarGeorreferenciacion() {
        requerirRol([1]);

        $solicitudModel = new SolicitudModel();
        $puntoModel = new PuntoModel();

        $solicitudes = $solicitudModel->obtenerDatosGeorreferenciacion();
        $puntos = $puntoModel->obtenerPuntos();

        $asignadas = $solicitudModel->asignarPuntosAutomaticamente();

        if ($asignadas > 0) {
            $solicitudes = $solicitudModel->obtenerDatosGeorreferenciacion();
        }

        require_once __DIR__ . '/../views/enrutador/georreferenciacion.php';
    }


    // DISTRIBUCIÓN DE VISITAS
    public function mostrarDistribucionVisitas() {
        requerirRol([1]);

        // Obtener la fecha seleccionada.
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        // Validar el formato de la fecha.
        $fechaObj = DateTime::createFromFormat('!Y-m-d', $fecha);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            $fecha = date('Y-m-d');
        }

        // Consultar la distribución automática.
        $cargaModel = new CargaModel();

        $resultado = $cargaModel->previsualizarDistribucion($fecha);



        // Enviar los resultados a la vista.
        $asignaciones = $resultado['asignaciones'] ?? [];
        $excepciones = $resultado['excepciones'] ?? [];

        // Listas necesarias para permitir modificaciones manuales.
        $encuestadores = $resultado['encuestadores'] ?? [];
        $puntos = $resultado['puntos'] ?? [];

        $resumen = $resultado['resumen'] ?? [
            'pendientes' => 0,
            'asignadas' => 0,
            'excepciones' => 0
        ];

        $errorDistribucion = $resultado['error'] ?? null;

        require_once __DIR__ . '/../views/enrutador/distribucion_visitas.php';
    }


    // CONFIRMAR DISTRIBUCIÓN DE VISITAS
    public function confirmarDistribucion() {
        requerirRol([1]);

        // Solo permitir solicitudes POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=distribucion_visitas');
            exit();
        }

        // Validar protección CSRF.
        if (!validarCsrf()) {
            guardarMensaje('error', 'La solicitud no es válida. Intenta nuevamente.');

            header('Location: index.php?action=distribucion_visitas');
            exit();
        }

        // Obtener y validar la fecha.
        $fecha = $_POST['fecha'] ?? '';

        $fechaObj = DateTime::createFromFormat('!Y-m-d', $fecha);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            guardarMensaje('error', 'La fecha seleccionada no es válida.');

            header('Location: index.php?action=distribucion_visitas');
            exit();
        }

        // Obtener las asignaciones modificadas por el Enrutador.
        $asignaciones = $_POST['asignaciones'] ?? [];

        if (!is_array($asignaciones) || empty($asignaciones)) {
            guardarMensaje('error', 'No hay asignaciones para confirmar.');

            header('Location: index.php?action=distribucion_visitas&fecha=' . urlencode($fecha));
            exit();
        }

        // Identificar al Enrutador que realiza la operación.
        $idEnrutador = (int) $_SESSION['id_usuario'];

        // Confirmar mediante el modelo.
        $cargaModel = new CargaModel();

        $resultado = $cargaModel->confirmarDistribucion(
            $asignaciones,
            $fecha,
            $idEnrutador
        );

        // Mostrar el resultado.
        if (!empty($resultado['ok'])) {
            guardarMensaje(
                'ok',
                $resultado['mensaje'] ?? 'La distribución se confirmó correctamente.'
            );
        } else {
            guardarMensaje(
                'error',
                $resultado['mensaje'] ?? $resultado['error'] ?? 'No fue posible confirmar la distribución.'
            );
        }

        // Regresar a la distribución de la fecha seleccionada.
        header(
            'Location: index.php?action=distribucion_visitas&fecha=' . urlencode($fecha)
        );
        exit();
    }

}

?>


