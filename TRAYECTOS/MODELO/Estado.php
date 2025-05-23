<?php 
	class Estado {
    private $conn;
    private $table_name = "Tbl_Estados";

    public $id_estado;
    public $nombre_estado;
    public $descripcion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE activo = true ORDER BY id_estado";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>