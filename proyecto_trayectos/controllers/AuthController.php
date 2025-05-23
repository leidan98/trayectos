<?php 

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if (User::isLoggedIn()) {
            $this->redirect('/');
        }
        
        $data = [
            'title' => 'Iniciar Sesión',
            'error' => $this->getFlashMessage('error')
        ];
        
        $this->view('auth/login', $data);
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $this->setFlashMessage('error', 'Por favor complete todos los campos');
            $this->redirect('/login');
        }
        
        $user = $this->userModel->login($username);
        
        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_completo'] = $user['nombre_completo'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            $_SESSION['email'] = $user['email'];
            
            $this->setFlashMessage('success', 'Bienvenido ' . $user['nombre_completo']);
            $this->redirect('/');
        } else {
            $this->setFlashMessage('error', 'Usuario o contraseña incorrectos');
            $this->redirect('/login');
        }
    }
    
    public function logout() {
        User::logout();
        $this->redirect('/login');
    }
}

 ?>