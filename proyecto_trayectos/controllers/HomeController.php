<?php 

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Trayecto.php';

class HomeController extends Controller {
    
    private $trayectoModel;
    
    public function __construct() {
        $this->trayectoModel = new Trayecto();
    }
    
    public function index() {
        $this->requireAuth();
        
        $user = User::getCurrentUser();
        $corteActual = date('n-Y'); // Formato: mes-año (5-2025)
        
        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'success' => $this->getFlashMessage('success'),
            'error' => $this->getFlashMessage('error'),
            'corte_actual' => $corteActual,
            'resumen' => $this->trayectoModel->getResumenCorte($corteActual)
        ];
        
        if (User::isAdmin()) {
            $this->view('admin/dashboard', $data);
        } else {
            // Redirigir a la vista de trayectos para usuarios jefe
            $this->redirect('/trayectos');
        }
    }
}

 ?>