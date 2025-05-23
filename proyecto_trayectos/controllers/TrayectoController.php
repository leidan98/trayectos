<?php 

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Trayecto.php';
require_once __DIR__ . '/../models/OrigenDestino.php';

class TrayectoController extends Controller {
    
    private $trayectoModel;
    private $userModel;
    private $origenDestinoModel;
    
    public function __construct() {
        $this->trayectoModel = new Trayecto();
        $this->userModel = new User();
        $this->origenDestinoModel = new OrigenDestino();
    }
    
    public function index() {
        $this->requireAuth();
        
        $corte = $_GET['corte'] ?? date('n-Y');
        $trayectos = $this->trayectoModel->getByCorte($corte);
        $resumen = $this->trayectoModel->getResumenCorte($corte);
        
        $data = [
            'title' => 'Trayectos Especiales',
            'user' => User::getCurrentUser(),
            'trayectos' => $trayectos,
            'corte' => $corte,
            'resumen' => $resumen,
            'success' => $this->getFlashMessage('success'),
            'error' => $this->getFlashMessage('error')
        ];
        
        $this->view('trayectos/index', $data);
    }
    
    public function create() {
        $this->requireAuth();
        
        $usuarios = $this->userModel->getActiveUsers();
        $origenes = $this->origenDestinoModel->getAll();
        
        $data = [
            'title' => 'Nuevo Trayecto',
            'user' => User::getCurrentUser(),
            'usuarios' => $usuarios,
            'origenes' => $origenes,
            'fecha_solicitud' => date('Y-m-d'),
            'error' => $this->getFlashMessage('error')
        ];
        
        $this->view('trayectos/create', $data);
    }
    
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/trayectos');
        }
        
        try {
            $data = [
                'fecha_solicitud' => $_POST['fecha_solicitud'],
                'tipo_usuario_servicio' => $_POST['tipo_usuario_servicio'],
                'usuario_requiere_id' => $_POST['usuario_requiere_id'],
                'usuario_aprueba_id' => $_POST['usuario_aprueba_id'],
                'origen_id' => $_POST['origen_id'],
                'destino_id' => $_POST['destino_id'],
                'fecha_servicio' => $_POST['fecha_servicio'],
                'hora_servicio' => $_POST['hora_servicio'],
                'corte' => date('n-Y', strtotime($_POST['fecha_servicio']))
            ];
            
            // Validaciones
            if ($data['origen_id'] == $data['destino_id']) {
                throw new Exception('El origen y destino no pueden ser iguales');
            }
            
            if (strtotime($data['fecha_servicio']) < strtotime('today')) {
                throw new Exception('La fecha de servicio no puede ser anterior a hoy');
            }
            
            $result = $this->trayectoModel->create($data);
            
            if ($result) {
                // Enviar a aprobación si es admin
                if (User::isAdmin()) {
                    $this->trayectoModel->enviarAprobacion($result['id']);
                }
                
                $this->setFlashMessage('success', 'Trayecto creado exitosamente');
                $this->redirect('/trayectos');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/trayectos/create');
        }
    }
    
    public function approve() {
        $this->requireAdmin();
        
        $corte = $_GET['corte'] ?? date('n-Y');
        $trayectos = $this->trayectoModel->getByCorte($corte);
        
        // Filtrar solo los que están en estado POR APROBAR
        $trayectosPorAprobar = array_filter($trayectos, function($t) {
            return $t['estado'] == 'POR APROBAR';
        });
        
        $data = [
            'title' => 'Aprobar Trayectos',
            'user' => User::getCurrentUser(),
            'trayectos' => $trayectosPorAprobar,
            'corte' => $corte,
            'success' => $this->getFlashMessage('success'),
            'error' => $this->getFlashMessage('error')
        ];
        
        $this->view('trayectos/approve', $data);
    }
    
    public function processApproval() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/trayectos/approve');
        }
        
        $trayectoId = $_POST['trayecto_id'] ?? null;
        $accion = $_POST['accion'] ?? null;
        
        if ($trayectoId && $accion) {
            $aprobar = ($accion === 'aprobar');
            
            if ($this->trayectoModel->aprobar($trayectoId, $aprobar)) {
                $mensaje = $aprobar ? 'Trayecto aprobado' : 'Trayecto rechazado';
                $this->setFlashMessage('success', $mensaje);
            } else {
                $this->setFlashMessage('error', 'Error al procesar la aprobación');
            }
        }
        
        $this->redirect('/trayectos/approve');
    }
    
    public function confirmAll() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Método no permitido'], 405);
        }
        
        $trayectos = json_decode($_POST['trayectos'] ?? '[]', true);
        $procesados = 0;
        
        foreach ($trayectos as $id) {
            if ($this->trayectoModel->aprobar($id, true)) {
                $procesados++;
            }
        }
        
        $this->jsonResponse([
            'success' => true,
            'message' => "$procesados trayectos aprobados exitosamente"
        ]);
    }
}

 ?>