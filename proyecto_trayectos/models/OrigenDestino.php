<?php 


require_once 'Database.php';

class OrigenDestino {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        try {
            $stmt = $this->db->callProcedure('sp_obtener_origenes_destinos');
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM Tbl_OrigenDestino WHERE id = ? AND estado = TRUE");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getActiveLocations() {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, nombre, tipo FROM Tbl_OrigenDestino WHERE estado = TRUE ORDER BY nombre");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

 ?>