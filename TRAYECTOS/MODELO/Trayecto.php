<?php 
	class Trayecto {
    private $conn;
    private $table_name = "Tbl_Trayectos";

    public $id_trayecto;
    public $fecha_solicitud;
    public $usuario_requiere_servicio;
    public $usuario_aprueba;
    public $tipo_usuario;
    public $origen;
    public $destino;
    public $fecha_servicio;
    public $hora_servicio;
    public $valor_trayecto;
    public $valor_total;
    public $id_estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        try {
            $stmt = $this->conn->prepare("CALL SP_CrearTrayecto(?, ?, ?, ?, ?, ?, ?, ?, @resultado, @mensaje)");
            
            $stmt->bindParam(1, $this->fecha_solicitud);
            $stmt->bindParam(2, $this->usuario_requiere_servicio);
            $stmt->bindParam(3, $this->tipo_usuario);
            $stmt->bindParam(4, $this->origen);
            $stmt->bindParam(5, $this->destino);
            $stmt->bindParam(6, $this->fecha_servicio);
            $stmt->bindParam(7, $this->hora_servicio);
            $stmt->bindParam(8, $this->valor_trayecto);
            
            $stmt->execute();

            // Obtener los valores de salida
            $result = $this->conn->query("SELECT @resultado as resultado, @mensaje as mensaje")->fetch();
            
            return $result;
        } catch (PDOException $e) {
            return [
                'resultado' => 0,
                'mensaje' => 'Error al crear el trayecto: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerPorMes($mes, $anio) {
        try {
            $stmt = $this->conn->prepare("CALL SP_ObtenerTrayectosPorMes(?, ?)");
            $stmt->bindParam(1, $mes, PDO::PARAM_INT);
            $stmt->bindParam(2, $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerPendientes($mes, $anio) {
        try {
            $stmt = $this->conn->prepare("CALL SP_ObtenerTrayectosPendientes(?, ?)");
            $stmt->bindParam(1, $mes, PDO::PARAM_INT);
            $stmt->bindParam(2, $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function aprobarRechazar($id_trayecto, $usuario_aprueba, $accion) {
        try {
            $stmt = $this->conn->prepare("CALL SP_AprobarRechazarTrayecto(?, ?, ?, @resultado, @mensaje)");
            
            $stmt->bindParam(1, $id_trayecto, PDO::PARAM_INT);
            $stmt->bindParam(2, $usuario_aprueba, PDO::PARAM_INT);
            $stmt->bindParam(3, $accion);
            
            $stmt->execute();

            // Obtener los valores de salida
            $result = $this->conn->query("SELECT @resultado as resultado, @mensaje as mensaje")->fetch();
            
            return $result;
        } catch (PDOException $e) {
            return [
                'resultado' => 0,
                'mensaje' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerResumenMontos($mes, $anio) {
        try {
            $stmt = $this->conn->prepare("CALL SP_ObtenerResumenMontos(?, ?)");
            $stmt->bindParam(1, $mes, PDO::PARAM_INT);
            $stmt->bindParam(2, $anio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'total_trayectos' => 0,
                'total_aprobado' => 0,
                'total_sin_aprobar' => 0,
                'total_general' => 0
            ];
        }
    }
}

?>