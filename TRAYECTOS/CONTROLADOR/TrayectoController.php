<?php 
	require_once 'BD/baseDatos.php';
	require_once 'BD/session.php';
	require_once 'MODELO/Trayecto.php';
	require_once 'MODELO/Usuario.php';
	require_once 'MODELO/OrigenDestino.php';

	class TrayectoController {
	    private $db;
	    private $trayecto;
	    private $usuario;
	    private $origenDestino;

	    public function __construct() {
	        $database = new Database();
	        $this->db = $database->getConnection();
	        $this->trayecto = new Trayecto($this->db);
	        $this->usuario = new Usuario($this->db);
	        $this->origenDestino = new OrigenDestino($this->db);
	    }

	    public function obtenerDatosFormulario() {
	        return [
	            'usuarios' => $this->usuario->obtenerTodos(),
	            'origenes_destinos' => $this->origenDestino->obtenerTodos()
	        ];
	    }

	    public function crear($datos) {
	        $this->trayecto->fecha_solicitud = $datos['fecha_solicitud'];
	        $this->trayecto->usuario_requiere_servicio = $datos['usuario_requiere_servicio'];
	        $this->trayecto->tipo_usuario = $datos['tipo_usuario'];
	        $this->trayecto->origen = $datos['origen'];
	        $this->trayecto->destino = $datos['destino'];
	        $this->trayecto->fecha_servicio = $datos['fecha_servicio'];
	        $this->trayecto->hora_servicio = $datos['hora_servicio'];
	        $this->trayecto->valor_trayecto = $datos['valor_trayecto'];

	        return $this->trayecto->crear();
	    }

	    public function obtenerTrayectos($mes, $anio) {
	        return $this->trayecto->obtenerPorMes($mes, $anio);
	    }

	    public function obtenerTrayectosPendientes($mes, $anio) {
	        return $this->trayecto->obtenerPendientes($mes, $anio);
	    }

	    public function aprobarTrayecto($id_trayecto, $usuario_aprueba) {
	        return $this->trayecto->aprobarRechazar($id_trayecto, $usuario_aprueba, 'APROBAR');
	    }

	    public function rechazarTrayecto($id_trayecto, $usuario_aprueba) {
	        return $this->trayecto->aprobarRechazar($id_trayecto, $usuario_aprueba, 'RECHAZAR');
	    }

	    public function obtenerResumen($mes, $anio) {
	        return $this->trayecto->obtenerResumenMontos($mes, $anio);
	    }
	}

?>