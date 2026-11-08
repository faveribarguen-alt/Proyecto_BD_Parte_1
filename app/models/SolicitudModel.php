<?php
require_once __DIR__ . '/../config/conexion.php';

class SolicitudModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para obtener la lista de barrios y sus comunas para el <select>
    public function obtenerBarrios() {
        $query = "SELECT b.id_barrio, b.nombre, c.nombre as comuna 
                  FROM barrios b 
                  INNER JOIN comunas c ON b.id_comuna = c.id_comuna 
                  ORDER BY c.nombre, b.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para guardar la solicitud usando TCL (Transacciones)
    public function guardarSolicitud($datos) {
        try {
            // TCL: Iniciamos la transacción (BEGIN TRANSACTION)
            $this->conn->beginTransaction();

            // DML: Consulta preparada para insertar datos
            $query = "INSERT INTO solicitudes 
                      (tipo_documento, numero_documento, nombres, apellidos, celular, direccion, id_barrio, latitud, longitud, id_usuario_ventanilla) 
                      VALUES 
                      (:tipo_doc, :num_doc, :nombres, :apellidos, :celular, :direccion, :id_barrio, :latitud, :longitud, :id_usuario)";
            
            $stmt = $this->conn->prepare($query);
            
            // Asignamos los valores de forma segura
            $stmt->bindParam(':tipo_doc', $datos['tipo_documento']);
            $stmt->bindParam(':num_doc', $datos['numero_documento']);
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':celular', $datos['celular']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':id_barrio', $datos['id_barrio']);
            $stmt->bindParam(':latitud', $datos['latitud']);
            $stmt->bindParam(':longitud', $datos['longitud']);
            $stmt->bindParam(':id_usuario', $datos['id_usuario_ventanilla']); // Quien esté logueado

            $stmt->execute();

            // TCL: Si todo salió bien, confirmamos los cambios (COMMIT)
            $this->conn->commit();
            
            return true;

        } catch (PDOException $e) {
            // TCL: Si hubo algún error, revertimos todo para no guardar basura (ROLLBACK)
            $this->conn->rollBack();
            // Solo para modo desarrollo, luego lo cambiamos a un mensaje amigable
            die("Error al guardar: " . $e->getMessage()); 
            return false;
        }
    }
}
?>