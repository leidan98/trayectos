<?php 
require_once 'BD/baseDatos.php';
require_once 'BD/session.php';
require_once 'MODELO/Usuario.php';

class AuthController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function login($email, $password) {
        $result = $this->usuario->autenticar($email, $password);
        
        if ($result) {
            $_SESSION['usuario_id'] = $result['id_usuario'];
            $_SESSION['nombre_usuario'] = $result['nombre_usuario'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['tipo_usuario'] = $result['tipo_usuario'];
            
            return [
                'success' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'redirect' => 'dashboard.php'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Credenciales incorrectas'
            ];
        }
    }

    public function logout() {
        limpiarSesion();
    }
}
 ?>