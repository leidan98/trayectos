<?php 

abstract class Controller {
    
    protected function view($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Vista no encontrada: $view");
        }
    }
    
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit();
    }
    
    protected function requireAuth() {
        if (!User::isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if (!User::isAdmin()) {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta sección';
            $this->redirect('/');
        }
    }
    
    protected function setFlashMessage($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    protected function getFlashMessage($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

 ?>