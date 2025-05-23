<?php 
	require_once 'BD/baseDatos.php';
	require_once 'BD/session.php';
	require_once 'CONTROLADOR/TrayectoController.php';

	class DashboardController {
	    private $trayectoController;

	    public function __construct() {
	        $this->trayectoController = new TrayectoController();
	    }

	    public function obtenerDatosDashboard($mes = null, $anio = null) {
	        if ($mes === null) $mes = date('n');
	        if ($anio === null) $anio = date('Y');

	        $datos = [
	            'mes_actual' => $mes,
	            'anio_actual' => $anio,
	            'resumen' => $this->trayectoController->obtenerResumen($mes, $anio),
	            'trayectos' => $this->trayectoController->obtenerTrayectos($mes, $anio)
	        ];

	        // Si es admin, obtener también los pendientes
	        if ($_SESSION['tipo_usuario'] == '1') {
	            $datos['trayectos_pendientes'] = $this->trayectoController->obtenerTrayectosPendientes($mes, $anio);
	        }

	        return $datos;
	    }
	}
?>