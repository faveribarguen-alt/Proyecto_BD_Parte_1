<?php
require_once __DIR__ . '/../config/conexion.php';

// Una "carga" es la hoja de ruta: el grupo de solicitudes asignado a un encuestador
class CargaModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Solicitudes que todavía no están en ninguna hoja de ruta
    public function contarPendientes() {
        $query = "SELECT COUNT(*) FROM solicitudes WHERE estado = 'radicada' AND id_carga IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerEncuestadores() {
        $query = "SELECT id_usuario, nombres, apellidos
                  FROM usuarios
                  WHERE id_rol = 3 AND estado = 'activo'
                  ORDER BY nombres, apellidos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea la hoja de ruta con hasta $limite solicitudes, las más urgentes primero.
    // Todo ocurre en una transacción: o se guarda completo o no se guarda nada.
    public function generarCarga($idEncuestador, $idEnrutador, $fechaVisita, $limite = 20) {
        try {
            // TCL: inicio de la transacción
            $this->conn->beginTransaction();

            // El encuestador debe existir, estar activo y tener el rol 3
            $stmt = $this->conn->prepare(
                "SELECT id_usuario FROM usuarios WHERE id_usuario = :id AND id_rol = 3 AND estado = 'activo'"
            );
            $stmt->bindValue(':id', $idEncuestador, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt->fetch()) {
                $this->conn->rollBack();
                return ['ok' => false, 'error' => 'El encuestador seleccionado no es válido.'];
            }

            // Solicitudes pendientes: primero las que vencen antes, agrupadas por comuna y barrio.
            // FOR UPDATE las bloquea para que otro enrutador no las tome al mismo tiempo.
            $stmt = $this->conn->prepare(
                "SELECT s.id_solicitud
                 FROM solicitudes s
                 INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                 INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                 WHERE s.estado = 'radicada' AND s.id_carga IS NULL
                 ORDER BY s.fecha_limite ASC, b.id_comuna ASC, b.id_barrio ASC, s.id_solicitud ASC
                 LIMIT :limite
                 FOR UPDATE"
            );
            $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
            $stmt->execute();
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($ids) === 0) {
                $this->conn->rollBack();
                return ['ok' => false, 'error' => 'No hay solicitudes pendientes para asignar.'];
            }

            // DML: se crea la hoja de ruta
            $stmt = $this->conn->prepare(
                "INSERT INTO cargas (id_encuestador, id_enrutador, fecha_visita, estado)
                 VALUES (:enc, :enr, :fecha, 'programada')"
            );
            $stmt->bindValue(':enc', $idEncuestador, PDO::PARAM_INT);
            $stmt->bindValue(':enr', $idEnrutador, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $fechaVisita);
            $stmt->execute();
            $idCarga = (int) $this->conn->lastInsertId();

            // DML: cada solicitud queda ligada a la hoja y pasa a "asignada", con su registro en el historial
            $upd = $this->conn->prepare(
                "UPDATE solicitudes SET id_carga = :carga, estado = 'asignada' WHERE id_solicitud = :sol"
            );
            $hist = $this->conn->prepare(
                "INSERT INTO historial_estados (id_solicitud, estado, id_usuario) VALUES (:sol, 'asignada', :usr)"
            );
            foreach ($ids as $idSolicitud) {
                $upd->bindValue(':carga', $idCarga, PDO::PARAM_INT);
                $upd->bindValue(':sol', (int) $idSolicitud, PDO::PARAM_INT);
                $upd->execute();

                $hist->bindValue(':sol', (int) $idSolicitud, PDO::PARAM_INT);
                $hist->bindValue(':usr', $idEnrutador, PDO::PARAM_INT);
                $hist->execute();
            }

            // TCL: todo salió bien, se confirma
            $this->conn->commit();
            return ['ok' => true, 'id_carga' => $idCarga, 'cantidad' => count($ids)];

        } catch (PDOException $e) {
            // TCL: algo falló, se deshace todo
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log($e->getMessage());
            return ['ok' => false, 'error' => 'No se pudo generar la hoja de ruta. Intenta de nuevo.'];
        }
    }

    // Lista de hojas de ruta para el enrutador
    public function listarCargas() {
        $query = "SELECT c.id_carga, c.fecha_visita, c.estado, c.creada_en, c.entregada_en,
                         CONCAT(u.nombres, ' ', u.apellidos) AS encuestador,
                         COUNT(s.id_solicitud) AS total
                  FROM cargas c
                  INNER JOIN usuarios u ON c.id_encuestador = u.id_usuario
                  LEFT JOIN solicitudes s ON s.id_carga = c.id_carga
                  GROUP BY c.id_carga, c.fecha_visita, c.estado, c.creada_en, c.entregada_en, u.nombres, u.apellidos
                  ORDER BY c.creada_en DESC, c.id_carga DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Datos de control de una hoja de ruta
    public function obtenerCarga($idCarga) {
        $query = "SELECT c.id_carga, c.id_encuestador, c.fecha_visita, c.estado, c.creada_en, c.entregada_en,
                         CONCAT(ue.nombres, ' ', ue.apellidos) AS encuestador,
                         ue.id_punto AS punto_encuestador,
                         p.nombre AS punto,
                         CONCAT(ur.nombres, ' ', ur.apellidos) AS enrutador,
                         CONCAT(ud.nombres, ' ', ud.apellidos) AS entregada_por
                  FROM cargas c
                  INNER JOIN usuarios ue ON c.id_encuestador = ue.id_usuario
                  INNER JOIN usuarios ur ON c.id_enrutador = ur.id_usuario
                  LEFT JOIN usuarios ud ON c.id_usuario_entrega = ud.id_usuario
                  LEFT JOIN puntos_atencion p ON ue.id_punto = p.id_punto
                  WHERE c.id_carga = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Las solicitudes (visitas) que trae una hoja de ruta
    public function obtenerSolicitudesDeCarga($idCarga) {
        $query = "SELECT s.id_solicitud, s.radicado, s.fecha_limite,
                         so.nombres, so.apellidos, so.telefono, so.direccion,
                         b.nombre AS barrio, cm.nombre AS comuna,
                         g.latitud, g.longitud
                  FROM solicitudes s
                  INNER JOIN solicitantes so ON s.id_solicitante = so.id_solicitante
                  INNER JOIN barrios b ON so.id_barrio = b.id_barrio
                  INNER JOIN comunas cm ON b.id_comuna = cm.id_comuna
                  LEFT JOIN georreferenciacion g ON s.id_solicitud = g.id_solicitud
                  WHERE s.id_carga = :id
                  ORDER BY cm.id_comuna, b.nombre, so.direccion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hojas de ruta de los encuestadores de un punto de atención (para que ventanilla las entregue)
    public function listarPorPunto($idPunto) {
        $query = "SELECT c.id_carga, c.fecha_visita, c.estado, c.entregada_en,
                         (c.fecha_visita <= CURDATE()) AS puede_entregar,
                         CONCAT(u.nombres, ' ', u.apellidos) AS encuestador,
                         (SELECT COUNT(*) FROM solicitudes s WHERE s.id_carga = c.id_carga) AS total
                  FROM cargas c
                  INNER JOIN usuarios u ON c.id_encuestador = u.id_usuario
                  WHERE u.id_punto = :punto AND c.estado IN ('programada', 'en_campo')
                  ORDER BY c.fecha_visita DESC, c.id_carga DESC
                  LIMIT 50";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ventanilla entrega la hoja: solo si es de su punto, está programada y ya llegó la fecha de visita
    public function marcarEntregada($idCarga, $idUsuario, $idPunto) {
        $query = "UPDATE cargas c
                  INNER JOIN usuarios ue ON c.id_encuestador = ue.id_usuario
                  SET c.estado = 'en_campo', c.entregada_en = NOW(), c.id_usuario_entrega = :usuario
                  WHERE c.id_carga = :id
                    AND c.estado = 'programada'
                    AND c.fecha_visita <= CURDATE()
                    AND ue.id_punto = :punto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id', $idCarga, PDO::PARAM_INT);
        $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Hojas de ruta que Ventanilla puede entregar hoy
public function contarHojasListasEntrega($idPunto) {

    $query = "SELECT COUNT(*)
              FROM cargas c
              INNER JOIN usuarios u
                ON c.id_encuestador = u.id_usuario
              WHERE u.id_punto = :punto
                AND c.estado = 'programada'
                AND c.fecha_visita <= CURDATE()";

    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':punto', $idPunto, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}


// Cantidad de hojas de ruta programadas
public function contarHojasProgramadas() {

    $query = "SELECT COUNT(*)
              FROM cargas
              WHERE estado = 'programada'";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}


// Hojas de ruta recientes para el dashboard del Enrutador
public function obtenerHojasRecientes() {

    $query = "SELECT
                c.id_carga,
                c.fecha_visita,
                c.estado,
                c.creada_en,
                CONCAT(u.nombres, ' ', u.apellidos) AS encuestador,
                COUNT(s.id_solicitud) AS total_solicitudes

            FROM cargas c

            INNER JOIN usuarios u
            ON c.id_encuestador = u.id_usuario

            LEFT JOIN solicitudes s
            ON s.id_carga = c.id_carga

            GROUP BY
            c.id_carga,
            c.fecha_visita,
            c.estado,
            c.creada_en,
            u.nombres,
            u.apellidos

            ORDER BY c.creada_en DESC, c.id_carga DESC

            LIMIT 5";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // ==========================================
    // DISTRIBUCIÓN AUTOMÁTICA DE VISITAS
    // ==========================================

    // Calcular distancia en kilómetros entre dos coordenadas.
    private function calcularDistanciaKm($lat1, $lon1, $lat2, $lon2) {

        $radioTierra = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($lat1))
           * cos(deg2rad($lat2))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }


    // Generar una vista previa de la distribución.
    // Este método NO modifica la base de datos.
    public function previsualizarDistribucion($fechaVisita) {

        // Validar que la fecha tenga formato correcto.
        $fecha = DateTime::createFromFormat('!Y-m-d', $fechaVisita);

        if (!$fecha || $fecha->format('Y-m-d') !== $fechaVisita) {
            return [
                'ok' => false,
                'error' => 'La fecha seleccionada no es válida.'
            ];
        }

        try {

            // 1. Consultar puntos de atención activos con coordenadas.
            $queryPuntos = "SELECT
                                id_punto,
                                nombre,
                                latitud,
                                longitud
                            FROM puntos_atencion
                            WHERE activo = 1
                              AND latitud IS NOT NULL
                              AND longitud IS NOT NULL";

            $stmtPuntos = $this->conn->prepare($queryPuntos);
            $stmtPuntos->execute();

            $puntos = $stmtPuntos->fetchAll(PDO::FETCH_ASSOC);

            if (empty($puntos)) {
                return [
                    'ok' => false,
                    'error' => 'No hay puntos de atención activos con coordenadas.'
                ];
            }


            // 2. Consultar encuestadores activos y su punto de atención.
            $queryEncuestadores = "SELECT
                                        u.id_usuario,
                                        u.id_punto,
                                        u.nombres,
                                        u.apellidos,
                                        p.nombre AS punto
                                   FROM usuarios u
                                   INNER JOIN puntos_atencion p
                                       ON u.id_punto = p.id_punto
                                   WHERE u.id_rol = 3
                                     AND u.estado = 'activo'
                                     AND p.activo = 1
                                   ORDER BY u.nombres, u.apellidos";

            $stmtEncuestadores = $this->conn->prepare($queryEncuestadores);
            $stmtEncuestadores->execute();

            $encuestadores = $stmtEncuestadores->fetchAll(PDO::FETCH_ASSOC);


            // 3. Consultar la cantidad de visitas que ya tiene cada
            // encuestador para la fecha seleccionada, separadas por zona.
            $queryCapacidad = "SELECT
                                    c.id_encuestador,
                                    cm.zona,
                                    COUNT(s.id_solicitud) AS total
                               FROM cargas c
                               INNER JOIN solicitudes s
                                   ON s.id_carga = c.id_carga
                               INNER JOIN solicitantes so
                                   ON s.id_solicitante = so.id_solicitante
                               INNER JOIN barrios b
                                   ON so.id_barrio = b.id_barrio
                               INNER JOIN comunas cm
                                   ON b.id_comuna = cm.id_comuna
                               WHERE c.fecha_visita = :fecha
                               GROUP BY c.id_encuestador, cm.zona";

            $stmtCapacidad = $this->conn->prepare($queryCapacidad);
            $stmtCapacidad->bindValue(':fecha', $fechaVisita);
            $stmtCapacidad->execute();

            $capacidadesExistentes = $stmtCapacidad->fetchAll(PDO::FETCH_ASSOC);


            // Guardar los contadores actuales por encuestador y zona.
            $contador = [];

            foreach ($capacidadesExistentes as $registro) {
                $idEncuestador = (int) $registro['id_encuestador'];
                $zona = $registro['zona'];

                $contador[$idEncuestador][$zona] = (int) $registro['total'];
            }


            // 4. Consultar solicitudes pendientes con ubicación.
            $querySolicitudes = "SELECT
                                    s.id_solicitud,
                                    s.radicado,
                                    s.fecha_limite,
                                    s.id_punto_asignado,
                                    so.nombres,
                                    so.apellidos,
                                    so.direccion,
                                    b.nombre AS barrio,
                                    cm.nombre AS comuna,
                                    cm.zona,
                                    g.latitud,
                                    g.longitud
                                 FROM solicitudes s
                                 INNER JOIN solicitantes so
                                     ON s.id_solicitante = so.id_solicitante
                                 INNER JOIN barrios b
                                     ON so.id_barrio = b.id_barrio
                                 INNER JOIN comunas cm
                                     ON b.id_comuna = cm.id_comuna
                                 LEFT JOIN georreferenciacion g
                                     ON s.id_solicitud = g.id_solicitud
                                 WHERE s.estado = 'radicada'
                                   AND s.id_carga IS NULL
                                 ORDER BY s.fecha_limite ASC, s.id_solicitud ASC";

            $stmtSolicitudes = $this->conn->prepare($querySolicitudes);
            $stmtSolicitudes->execute();

            $solicitudes = $stmtSolicitudes->fetchAll(PDO::FETCH_ASSOC);


            // 5. Preparar la lista de resultados.
            $asignaciones = [];
            $excepciones = [];

            // Capacidad máxima diaria por zona.
            $limites = [
                'urbana' => 10,
                'rural' => 5
            ];


            // 6. Procesar cada solicitud pendiente.
            foreach ($solicitudes as $solicitud) {

                // Sin coordenadas no se puede calcular proximidad.
                if (
                    $solicitud['latitud'] === null ||
                    $solicitud['longitud'] === null
                ) {
                    $excepciones[] = [
                        'id_solicitud' => (int) $solicitud['id_solicitud'],
                        'radicado' => $solicitud['radicado'],
                        'motivo' => 'La solicitud no tiene coordenadas geográficas.'
                    ];

                    continue;
                }

                $zona = $solicitud['zona'];

                if (!isset($limites[$zona])) {
                    $excepciones[] = [
                        'id_solicitud' => (int) $solicitud['id_solicitud'],
                        'radicado' => $solicitud['radicado'],
                        'motivo' => 'La zona de la solicitud no tiene un límite de visitas configurado.'
                    ];

                    continue;
                }


                // Calcular distancia desde la solicitud hasta cada punto.
                $puntosCercanos = [];

                foreach ($puntos as $punto) {

                    $distancia = $this->calcularDistanciaKm(
                        (float) $solicitud['latitud'],
                        (float) $solicitud['longitud'],
                        (float) $punto['latitud'],
                        (float) $punto['longitud']
                    );

                    $puntosCercanos[] = [
                        'id_punto' => (int) $punto['id_punto'],
                        'nombre' => $punto['nombre'],
                        'distancia' => $distancia
                    ];
                }


                // Ordenar los puntos desde el más cercano hasta el más lejano.
                usort($puntosCercanos, function ($a, $b) {
                    return $a['distancia'] <=> $b['distancia'];
                });


                // Si el Enrutador ya definió manualmente un punto asignado,
                // se intenta primero con ese punto.
                $idPuntoManual = $solicitud['id_punto_asignado'];

                if ($idPuntoManual !== null) {

                    usort($puntosCercanos, function ($a, $b) use ($idPuntoManual) {

                        if ($a['id_punto'] === (int) $idPuntoManual) {
                            return -1;
                        }

                        if ($b['id_punto'] === (int) $idPuntoManual) {
                            return 1;
                        }

                        return $a['distancia'] <=> $b['distancia'];
                    });
                }


                $encuestadorElegido = null;
                $puntoElegido = null;
                $distanciaElegida = null;
                $menorCarga = PHP_INT_MAX;


                // Buscar encuestadores comenzando por el punto más cercano.
                // Si no tienen capacidad, se intenta con el siguiente punto.
                foreach ($puntosCercanos as $puntoCercano) {

                    $candidatos = [];

                    foreach ($encuestadores as $encuestador) {

                        if ((int) $encuestador['id_punto'] !== $puntoCercano['id_punto']) {
                            continue;
                        }

                        $idUsuario = (int) $encuestador['id_usuario'];
                        $visitasActuales = $contador[$idUsuario][$zona] ?? 0;

                        if ($visitasActuales >= $limites[$zona]) {
                            continue;
                        }

                        $candidatos[] = [
                            'datos' => $encuestador,
                            'visitas' => $visitasActuales
                        ];
                    }


                    // Elegir al encuestador con menor carga de esa zona.
                    if (!empty($candidatos)) {

                        usort($candidatos, function ($a, $b) {
                            return $a['visitas'] <=> $b['visitas'];
                        });

                        $encuestadorElegido = $candidatos[0]['datos'];
                        $puntoElegido = $puntoCercano;
                        $distanciaElegida = $puntoCercano['distancia'];

                        break;
                    }
                }


                // No se encontró un encuestador con capacidad.
                if ($encuestadorElegido === null) {

                    $excepciones[] = [
                        'id_solicitud' => (int) $solicitud['id_solicitud'],
                        'radicado' => $solicitud['radicado'],
                        'motivo' => 'No hay encuestadores disponibles con capacidad para esta zona.'
                    ];

                    continue;
                }


                // Registrar la asignación en memoria.
                $idUsuarioElegido = (int) $encuestadorElegido['id_usuario'];

                $contador[$idUsuarioElegido][$zona] =
                    ($contador[$idUsuarioElegido][$zona] ?? 0) + 1;


                $asignaciones[] = [
                    'id_solicitud' => (int) $solicitud['id_solicitud'],
                    'radicado' => $solicitud['radicado'],
                    'nombres' => $solicitud['nombres'],
                    'apellidos' => $solicitud['apellidos'],
                    'direccion' => $solicitud['direccion'],
                    'barrio' => $solicitud['barrio'],
                    'comuna' => $solicitud['comuna'],
                    'zona' => $zona,
                    'fecha_limite' => $solicitud['fecha_limite'],
                    'id_encuestador' => $idUsuarioElegido,
                    'encuestador' => $encuestadorElegido['nombres'] . ' ' . $encuestadorElegido['apellidos'],
                    'id_punto' => $puntoElegido['id_punto'],
                    'punto' => $puntoElegido['nombre'],
                    'distancia_km' => round($distanciaElegida, 2),
                    'visitas_asignadas' => $contador[$idUsuarioElegido][$zona],
                    'limite_zona' => $limites[$zona]
                ];
            }


            // 7. Resumen de la vista previa.
            return [
                'ok' => true,
                'fecha' => $fechaVisita,
                'asignaciones' => $asignaciones,
                'excepciones' => $excepciones,
                'encuestadores' => $encuestadores,
                'puntos' => $puntos,
                'resumen' => [
                    'pendientes' => count($solicitudes),
                    'asignadas' => count($asignaciones),
                    'excepciones' => count($excepciones)
                ]
            ];

        } catch (PDOException $e) {

            error_log('Error en previsualizarDistribucion: ' . $e->getMessage());

            return [
                'ok' => false,
                'error' => 'No fue posible calcular la distribución. Revisa los registros del sistema.'
            ];
        }
    }


    // ==========================================
    // CONFIRMAR DISTRIBUCIÓN DE VISITAS
    // ==========================================

    public function confirmarDistribucion($asignaciones, $fechaVisita, $idEnrutador) {

        // Validar fecha.
        $fecha = DateTime::createFromFormat('!Y-m-d', $fechaVisita);

        if (!$fecha || $fecha->format('Y-m-d') !== $fechaVisita) {
            return [
                'ok' => false,
                'error' => 'La fecha de visita no es válida.'
            ];
        }

        if (!is_array($asignaciones) || empty($asignaciones)) {
            return [
                'ok' => false,
                'error' => 'No hay asignaciones para confirmar.'
            ];
        }

        $limites = [
            'urbana' => 10,
            'rural' => 5
        ];

        try {

            $this->conn->beginTransaction();

            // 1. Verificar que el usuario que confirma sea un Enrutador activo.
            $stmt = $this->conn->prepare(
                "SELECT id_usuario
                 FROM usuarios
                 WHERE id_usuario = :id
                   AND id_rol = 1
                   AND estado = 'activo'
                 FOR UPDATE"
            );

            $stmt->execute([':id' => (int) $idEnrutador]);

            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('El usuario Enrutador no es válido o está inactivo.');
            }


            // 2. Validar la estructura de las asignaciones recibidas.
            $solicitudesRecibidas = [];
            $encuestadoresRecibidos = [];
            $puntosRecibidos = [];

            foreach ($asignaciones as $idSolicitud => $datos) {

                $idSolicitud = filter_var($idSolicitud, FILTER_VALIDATE_INT);
                $idEncuestador = filter_var(
                    $datos['id_encuestador'] ?? null,
                    FILTER_VALIDATE_INT
                );
                $idPunto = filter_var(
                    $datos['id_punto'] ?? null,
                    FILTER_VALIDATE_INT
                );

                if (
                    !$idSolicitud || $idSolicitud <= 0 ||
                    !$idEncuestador || $idEncuestador <= 0 ||
                    !$idPunto || $idPunto <= 0
                ) {
                    throw new Exception('Hay datos inválidos en las asignaciones.');
                }

                if (isset($solicitudesRecibidas[$idSolicitud])) {
                    throw new Exception('Una solicitud aparece más de una vez.');
                }

                $solicitudesRecibidas[$idSolicitud] = [
                    'id_encuestador' => $idEncuestador,
                    'id_punto' => $idPunto
                ];

                $encuestadoresRecibidos[$idEncuestador] = $idEncuestador;
                $puntosRecibidos[$idPunto] = $idPunto;
            }

            sort($encuestadoresRecibidos);
            sort($puntosRecibidos);

            $idsSolicitudes = array_keys($solicitudesRecibidas);


            // 3. Bloquear las solicitudes y comprobar que todavía estén pendientes.
            $marcadores = implode(',', array_fill(0, count($idsSolicitudes), '?'));

            $querySolicitudes = "
                SELECT
                    s.id_solicitud,
                    s.estado,
                    s.id_carga,
                    cm.zona
                FROM solicitudes s
                INNER JOIN solicitantes so
                    ON s.id_solicitante = so.id_solicitante
                INNER JOIN barrios b
                    ON so.id_barrio = b.id_barrio
                INNER JOIN comunas cm
                    ON b.id_comuna = cm.id_comuna
                WHERE s.id_solicitud IN ($marcadores)
                FOR UPDATE
            ";

            $stmt = $this->conn->prepare($querySolicitudes);
            $stmt->execute($idsSolicitudes);

            $solicitudesBD = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($solicitudesBD) !== count($idsSolicitudes)) {
                throw new Exception('Una o varias solicitudes ya no existen.');
            }

            $solicitudesValidas = [];

            foreach ($solicitudesBD as $solicitud) {

                $id = (int) $solicitud['id_solicitud'];

                if (
                    $solicitud['estado'] !== 'radicada' ||
                    $solicitud['id_carga'] !== null
                ) {
                    throw new Exception(
                        'La solicitud ' . $id . ' ya fue asignada o modificada. Calcula nuevamente la distribución.'
                    );
                }

                if (!isset($limites[$solicitud['zona']])) {
                    throw new Exception(
                        'La solicitud ' . $id . ' tiene una zona no válida.'
                    );
                }

                $solicitudesValidas[$id] = $solicitud;
            }


            // 4. Bloquear y validar los encuestadores.
            $marcadoresEncuestadores = implode(
                ',',
                array_fill(0, count($encuestadoresRecibidos), '?')
            );

            $queryEncuestadores = "
                SELECT u.id_usuario
                FROM usuarios u
                INNER JOIN puntos_atencion p
                    ON u.id_punto = p.id_punto
                WHERE u.id_usuario IN ($marcadoresEncuestadores)
                  AND u.id_rol = 3
                  AND u.estado = 'activo'
                  AND p.activo = 1
                ORDER BY u.id_usuario
                FOR UPDATE
            ";

            $stmt = $this->conn->prepare($queryEncuestadores);
            $stmt->execute($encuestadoresRecibidos);

            $encuestadoresValidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $encuestadoresValidos = array_map(
                'intval',
                $encuestadoresValidos
            );

            if ($encuestadoresValidos !== $encuestadoresRecibidos) {
                throw new Exception(
                    'Uno o varios encuestadores ya no están disponibles.'
                );
            }


            // 5. Verificar que los puntos seleccionados sigan activos
            // y tengan coordenadas.
            $marcadoresPuntos = implode(
                ',',
                array_fill(0, count($puntosRecibidos), '?')
            );

            $queryPuntos = "
                SELECT id_punto
                FROM puntos_atencion
                WHERE id_punto IN ($marcadoresPuntos)
                  AND activo = 1
                  AND latitud IS NOT NULL
                  AND longitud IS NOT NULL
                ORDER BY id_punto
            ";

            $stmt = $this->conn->prepare($queryPuntos);
            $stmt->execute($puntosRecibidos);

            $puntosValidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $puntosValidos = array_map('intval', $puntosValidos);

            if ($puntosValidos !== $puntosRecibidos) {
                throw new Exception(
                    'Uno o varios puntos de atención ya no están disponibles.'
                );
            }


            // 6. Consultar las visitas que ya tiene cada encuestador
            // en la fecha seleccionada, separadas por zona.
            $queryCapacidad = "
                SELECT
                    c.id_encuestador,
                    cm.zona,
                    COUNT(s.id_solicitud) AS total
                FROM cargas c
                INNER JOIN solicitudes s
                    ON s.id_carga = c.id_carga
                INNER JOIN solicitantes so
                    ON s.id_solicitante = so.id_solicitante
                INNER JOIN barrios b
                    ON so.id_barrio = b.id_barrio
                INNER JOIN comunas cm
                    ON b.id_comuna = cm.id_comuna
                WHERE c.fecha_visita = :fecha
                  AND c.id_encuestador IN ($marcadoresEncuestadores)
                GROUP BY c.id_encuestador, cm.zona
            ";

            $stmt = $this->conn->prepare($queryCapacidad);

            $marcadoresCapacidad = implode(
                ',',
                array_fill(0, count($encuestadoresRecibidos), '?')
            );

            $queryCapacidad = "
                SELECT
                    c.id_encuestador,
                    cm.zona,
                    COUNT(s.id_solicitud) AS total
                FROM cargas c
                INNER JOIN solicitudes s
                    ON s.id_carga = c.id_carga
                INNER JOIN solicitantes so
                    ON s.id_solicitante = so.id_solicitante
                INNER JOIN barrios b
                    ON so.id_barrio = b.id_barrio
                INNER JOIN comunas cm
                    ON b.id_comuna = cm.id_comuna
                WHERE c.fecha_visita = ?
                AND c.id_encuestador IN ($marcadoresCapacidad)
                GROUP BY c.id_encuestador, cm.zona
            ";

            $stmt = $this->conn->prepare($queryCapacidad);

            $stmt->execute(
                array_merge(
                    [$fechaVisita],
                    $encuestadoresRecibidos
                )
            );
            $capacidades = [];

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $registro) {

                $id = (int) $registro['id_encuestador'];
                $zona = $registro['zona'];

                $capacidades[$id][$zona] = (int) $registro['total'];
            }


            // 7. Sumar las nuevas asignaciones a las capacidades existentes.
            $nuevasPorEncuestador = [];

            foreach ($solicitudesValidas as $idSolicitud => $solicitud) {

                $datosAsignacion = $solicitudesRecibidas[$idSolicitud];

                $idEncuestador = $datosAsignacion['id_encuestador'];
                $zona = $solicitud['zona'];

                if (!isset($nuevasPorEncuestador[$idEncuestador])) {
                    $nuevasPorEncuestador[$idEncuestador] = [];
                }

                if (!isset($nuevasPorEncuestador[$idEncuestador][$zona])) {
                    $nuevasPorEncuestador[$idEncuestador][$zona] = 0;
                }

                $nuevasPorEncuestador[$idEncuestador][$zona]++;

                $total = ($capacidades[$idEncuestador][$zona] ?? 0)
                       + $nuevasPorEncuestador[$idEncuestador][$zona];

                if ($total > $limites[$zona]) {
                    throw new Exception(
                        'El encuestador ' . $idEncuestador .
                        ' supera el límite de visitas para la zona ' . $zona . '.'
                    );
                }
            }


            // 8. Crear una hoja de ruta por encuestador.
            $insertarCarga = $this->conn->prepare(
                "INSERT INTO cargas
                    (id_encuestador, id_enrutador, fecha_visita, estado)
                 VALUES
                    (:encuestador, :enrutador, :fecha, 'programada')"
            );

            $cargasCreadas = [];

            foreach ($encuestadoresRecibidos as $idEncuestador) {

                if (!isset($nuevasPorEncuestador[$idEncuestador])) {
                    continue;
                }

                $insertarCarga->execute([
                    ':encuestador' => $idEncuestador,
                    ':enrutador' => (int) $idEnrutador,
                    ':fecha' => $fechaVisita
                ]);

                $cargasCreadas[$idEncuestador] = (int) $this->conn->lastInsertId();
            }


            // 9. Actualizar las solicitudes y guardar el historial.
            $actualizarSolicitud = $this->conn->prepare(
                "UPDATE solicitudes
                 SET id_carga = :carga,
                     id_punto_asignado = :punto,
                     estado = 'asignada'
                 WHERE id_solicitud = :solicitud
                   AND estado = 'radicada'
                   AND id_carga IS NULL"
            );

            $insertarHistorial = $this->conn->prepare(
                "INSERT INTO historial_estados
                    (id_solicitud, estado, id_usuario)
                 VALUES
                    (:solicitud, 'asignada', :usuario)"
            );

            foreach ($solicitudesRecibidas as $idSolicitud => $datos) {

                $idEncuestador = $datos['id_encuestador'];
                $idCarga = $cargasCreadas[$idEncuestador];

                $actualizarSolicitud->execute([
                    ':carga' => $idCarga,
                    ':punto' => $datos['id_punto'],
                    ':solicitud' => $idSolicitud
                ]);

                if ($actualizarSolicitud->rowCount() !== 1) {
                    throw new Exception(
                        'No se pudo actualizar la solicitud ' . $idSolicitud . '.'
                    );
                }

                $insertarHistorial->execute([
                    ':solicitud' => $idSolicitud,
                    ':usuario' => (int) $idEnrutador
                ]);
            }


            // 10. Confirmar todos los cambios.
            $this->conn->commit();

            return [
                'ok' => true,
                'cantidad' => count($solicitudesRecibidas),
                'cargas' => $cargasCreadas,
                'mensaje' => 'La distribución se confirmó correctamente.'
            ];

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            error_log(
                'Error en confirmarDistribucion: ' . $e->getMessage()
            );

            return [
                'ok' => false,
                'error' => $e instanceof PDOException
                    ? 'No fue posible guardar la distribución. Revisa los registros del sistema.'
                    : $e->getMessage()
            ];
        }
    }


}
?>
