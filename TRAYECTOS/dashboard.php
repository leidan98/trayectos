<?php

require_once 'BD/session.php';
require_once 'CONTROLADOR/DashboardController.php';

verificarSesion();

$dashboardController = new DashboardController();

// Obtener mes y año de la URL o usar actual
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

$datos = $dashboardController->obtenerDatosDashboard($mes, $anio);

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Trayectos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .badge-estado {
            padding: 8px 12px;
            font-size: 0.75rem;
            border-radius: 20px;
        }
        
        .badge-pendiente { background: #ffc107; color: #000; }
        .badge-aprobado { background: #28a745; color: #fff; }
        .badge-rechazado { background: #dc3545; color: #fff; }
        .badge-por-aprobar { background: #17a2b8; color: #fff; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar px-3 py-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-route fa-2x mb-2"></i>
                        <h5>Sistema Trayectos</h5>
                        <small>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-home me-2"></i>Inicio
                            </a>
                        </li>
                        
                        <?php if ($_SESSION['tipo_usuario'] == '1'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#cargarInfo">
                                <i class="fas fa-upload me-2"></i>Carga de Información
                            </a>
                            <div class="collapse" id="cargarInfo">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="trayectos.php">
                                            <i class="fas fa-road me-2"></i>Trayectos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="peajes.php">
                                            <i class="fas fa-coins me-2"></i>Peajes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="parqueadero.php">
                                            <i class="fas fa-parking me-2"></i>Parqueadero
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="trayectos_especiales.php">
                                            <i class="fas fa-star me-2"></i>Trayectos Especiales
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="nuevo_trayecto.php">
                                <i class="fas fa-plus me-2"></i>Nuevo Trayecto
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Contenido principal -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Dashboard - Trayectos Especiales</h2>
                        
                        <div class="d-flex gap-2">
                            <select class="form-select" id="mesSelect" onchange="cambiarPeriodo()">
                                <?php foreach ($meses as $num => $nombre): ?>
                                    <option value="<?php echo $num; ?>" <?php echo $num == $mes ? 'selected' : ''; ?>>
                                        <?php echo $nombre; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select class="form-select" id="anioSelect" onchange="cambiarPeriodo()">
                                <?php for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == $anio ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Tarjetas de resumen -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-route fa-2x text-primary mb-2"></i>
                                    <h5>Total Trayectos</h5>
                                    <h3 class="text-primary"><?php echo $datos['resumen']['total_trayectos']; ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h5>Aprobado</h5>
                                    <h3 class="text-success">$<?php echo number_format($datos['resumen']['total_aprobado'], 0, ',', '.'); ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h5>Sin Aprobar</h5>
                                    <h3 class="text-warning">$<?php echo number_format($datos['resumen']['total_sin_aprobar'], 0, ',', '.'); ?></h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                    <h5>Total General</h5>
                                    <h3 class="text-info">$<?php echo number_format($datos['resumen']['total_general'], 0, ',', '.'); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trayectos Pendientes (solo para admin) -->
                    <?php if ($_SESSION['tipo_usuario'] == '1' && !empty($datos['trayectos_pendientes'])): ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Trayectos Pendientes de Aprobación
                            </h5>
                            <button class="btn btn-light btn-sm" onclick="aprobarTodos()">
                                <i class="fas fa-check-double me-1"></i>Aprobar Todos
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Usuario</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Valor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos['trayectos_pendientes'] as $trayecto): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($trayecto['fecha_servicio'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($trayecto['hora_servicio'])); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['usuario_requiere']); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['origen']); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['destino']); ?></td>
                                            <td>$<?php echo number_format($trayecto['valor_trayecto'], 0, ',', '.'); ?></td>
                                            <td>
                                                <button class="btn btn-success btn-sm" onclick="aprobarTrayecto(<?php echo $trayecto['id_trayecto']; ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="rechazarTrayecto(<?php echo $trayecto['id_trayecto']; ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Tabla de todos los trayectos -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Trayectos de <?php echo $meses[$mes] . ' ' . $anio; ?>
                            </h5>
                            <a href="nuevo_trayecto.php" class="btn btn-light btn-sm">
                                <i class="fas fa-plus me-1"></i>Nuevo Trayecto
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha Solicitud</th>
                                            <th>Fecha Servicio</th>
                                            <th>Hora</th>
                                            <th>Usuario</th>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Valor</th>
                                            <th>Estado</th>
                                            <?php if ($_SESSION['tipo_usuario'] == '1'): ?>
                                            <th>Acciones</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($datos['trayectos'])): ?>
                                        <tr>
                                            <td colspan="<?php echo $_SESSION['tipo_usuario'] == '1' ? '9' : '8'; ?>" class="text-center">
                                                No hay trayectos registrados para este período
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($datos['trayectos'] as $trayecto): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($trayecto['fecha_solicitud'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($trayecto['fecha_servicio'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($trayecto['hora_servicio'])); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['usuario_requiere']); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['origen']); ?></td>
                                            <td><?php echo htmlspecialchars($trayecto['destino']); ?></td>
                                            <td>$<?php echo number_format($trayecto['valor_trayecto'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge badge-estado badge-<?php echo strtolower(str_replace(' ', '-', $trayecto['nombre_estado'])); ?>">
                                                    <?php echo htmlspecialchars($trayecto['nombre_estado']); ?>
                                                </span>
                                            </td>
                                            <?php if ($_SESSION['tipo_usuario'] == '1'): ?>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="verDetalle(<?php echo $trayecto['id_trayecto']; ?>)">
                                                            <i class="fas fa-eye me-2"></i>Ver Detalle
                                                        </a></li>
                                                        <?php if ($trayecto['nombre_estado'] == 'PENDIENTE'): ?>
                                                        <li><a class="dropdown-item text-success" href="#" onclick="aprobarTrayecto(<?php echo $trayecto['id_trayecto']; ?>)">
                                                            <i class="fas fa-check me-2"></i>Aprobar
                                                        </a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="rechazarTrayecto(<?php echo $trayecto['id_trayecto']; ?>)">
                                                            <i class="fas fa-times me-2"></i>Rechazar
                                                        </a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.js"></script>
    
    <script>
        function cambiarPeriodo() {
            const mes = document.getElementById('mesSelect').value;
            const anio = document.getElementById('anioSelect').value;
            window.location.href = `dashboard.php?mes=${mes}&anio=${anio}`;
        }
        
        function aprobarTrayecto(id) {
            Swal.fire({
                title: '¿Aprobar trayecto?',
                text: "Esta acción no se puede deshacer",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    procesarTrayecto(id, 'aprobar');
                }
            });
        }
        
        function rechazarTrayecto(id) {
            Swal.fire({
                title: '¿Rechazar trayecto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    procesarTrayecto(id, 'rechazar');
                }
            });
        }
        
        function procesarTrayecto(id, accion) {
            fetch('procesar_trayecto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_trayecto: id,
                    accion: accion
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.mensaje,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al procesar la solicitud',
                    icon: 'error'
                });
            });
        }
        
        function aprobarTodos() {
            Swal.fire({
                title: '¿Aprobar todos los trayectos pendientes?',
                text: "Esta acción aprobará todos los trayectos mostrados",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar todos',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementar lógica para aprobar todos
                    Swal.fire('Funcionalidad en desarrollo', '', 'info');
                }
            });
        }
        
        function verDetalle(id) {
            // Implementar modal de detalle
            Swal.fire('Funcionalidad en desarrollo', '', 'info');
        }
    </script>
</body>
</html>