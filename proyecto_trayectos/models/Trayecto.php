<?php 


require_once 'Database.php';

class Trayecto {
    private $db;
    
    // Valores de trayectos predefinidos
    private $tarifas = [
        'AEROPUERTO-BQT COTA' => 250000,
        'BQT COTA-AEROPUERTO' => 250000,
        'BQT COTA-CAJICA' => 125000,
        'CAJICA-BQT COTA' => 125000,
        // Agregar más tarifas según necesidad
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        try {
            // Calcular valor del trayecto
            $origen = $data['origen_id'];
            $destino = $data['destino_id'];
            $valor = $this->calcularValorTrayecto($origen, $destino);
            
            $stmt = $this->db->callProcedure('sp_crear_trayecto', [
                $data['fecha_solicitud'],
                $data['tipo_usuario_servicio'],
                $data['usuario_requiere_id'],
                $data['usuario_aprueba_id'],
                $data['origen_id'],
                $data['destino_id'],
                $data['fecha_servicio'],
                $data['hora_servicio'],
                $valor,
                $data['corte']
            ]);
            
            return $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function getByCorte($corte) {
        try {
            $stmt = $this->db->callProcedure('sp_listar_trayectos_corte', [$corte]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function aprobar($trayectoId, $aprobar) {
        try {
            $stmt = $this->db->callProcedure('sp_aprobar_trayecto', [$trayectoId, $aprobar]);
            $result = $stmt->fetch();
            return $result['affected_rows'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function enviarAprobacion($trayectoId) {
        try {
            $stmt = $this->db->callProcedure('sp_enviar_aprobacion', [$trayectoId]);
            $result = $stmt->fetch();
            return $result['affected_rows'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function getResumenCorte($corte) {
        try {
            $stmt = $this->db->callProcedure('sp_resumen_corte', [$corte]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return [
                'aprobados' => 0,
                'sin_aprobar' => 0,
                'total_aprobado' => 0,
                'total_sin_aprobar' => 0
            ];
        }
    }
    
    private function calcularValorTrayecto($origenId, $destinoId) {
        // Aquí deberías obtener los nombres de origen y destino de la BD
        // Por simplicidad, usaré un valor por defecto
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("SELECT nombre FROM Tbl_OrigenDestino WHERE id = ?");
        $stmt->execute([$origenId]);
        $origen = $stmt->fetchColumn();
        
        $stmt->execute([$destinoId]);
        $destino = $stmt->fetchColumn();
        
        $ruta = $origen . '-' . $destino;
        
        return isset($this->tarifas[$ruta]) ? $this->tarifas[$ruta] : 150000; // Valor por defecto
    }
    
    public function getEstadosPorCorte($corte) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("
            SELECT 
                e.nombre_estado,
                COUNT(t.id) as cantidad
            FROM Tbl_Trayectos t
            INNER JOIN Tbl_Estados e ON t.estado_id = e.id
            WHERE t.corte = ?
            GROUP BY e.id, e.nombre_estado
        ");
        $stmt->execute([$corte]);
        return $stmt->fetchAll();
    }
}

 ?>