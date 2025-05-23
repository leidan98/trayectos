<?php 


require_once 'Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function login($username) {
        try {
            $stmt = $this->db->callProcedure('sp_login', [$username]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public function getActiveUsers() {
        try {
            $stmt = $this->db->callProcedure('sp_obtener_usuarios_activos');
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
    }
    
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'nombre_completo' => $_SESSION['nombre_completo'],
                'tipo_usuario' => $_SESSION['tipo_usuario'],
                'email' => $_SESSION['email']
            ];
        }
        return null;
    }
    
    public static function logout() {
        session_destroy();
    }
}

 ?>