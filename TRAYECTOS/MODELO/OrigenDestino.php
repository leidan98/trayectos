<?php 
class OrigenDestino {
    private $conn;
    private $table_name = "Tbl_OrigenDestino";

    public $id_origen_destino;
    public $nombre;
    public $descripcion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos() {
        try {
            $stmt = $this->conn->prepare("CALL SP_ObtenerOrigenDestino()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
 ?>