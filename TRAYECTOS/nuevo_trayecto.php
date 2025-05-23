<?php
// nuevo_trayecto.php

require_once 'config/session.php';
require_once 'controllers/TrayectoController.php';

verificarSesion();

$trayectoController = new TrayectoController();
$datosFormulario = $trayectoController->obtenerDatosFormulario();

$mensaje = '';
$tipo_mensaje = '';

if ($_POST) {
    $datos = [
        'fecha_solicitud' => date('Y-m-d'),
        'usuario_requiere_servicio' => $_POST['usuario_requiere_servicio'],
        'tipo_usuario' => $_POST['tipo_usuario'],
        'origen' => $_POST['origen'],
        'destino' => $_POST['destino'],
        'fecha_servicio' => $_POST['fecha_servicio'],
        'hora_servicio' => $_POST['hora_servicio'],
        'valor_trayecto' => floatval($_POST['valor_trayecto'])
    ];
    
    $resultado = $trayectoController->crear($datos);
    
    if ($resultado['resultado'] > 0) {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'success';
        // Limpiar el formulario
        $_POST = [];
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Trayecto - Sistema de Trayectos</title>
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
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .required::after {
            content: " *";
            color: red;
        }
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
                            <a class="nav-link" href="dashboard.php">
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
                                        <a class="nav-link active" href="nuevo_trayecto.php">
                                            <i class="fas fa-star me-2"></i>Trayectos Especiales
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="nuevo_trayecto.php">
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
                        <h2>Nuevo Trayecto Especial</h2>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje == 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?php echo $tipo_mensaje == 'error' ? 'exclamation-triangle' : 'check'; ?> me-2"></i>
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Formulario de Solicitud
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" id="formTrayecto">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_solicitud" class="form-label required">Fecha Solicitud</label>
                                        <input type="date" class="form-control" id="fecha_solicitud" name="fecha_solicitud" 
                                               value="<?php echo date('Y-m-d'); ?>" readonly>
                                        <div class="form-text">La fecha de solicitud es automática</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Tipo de Usuario</label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tipo_usuario" 
                                                       id="empleado" value="Empleado" 
                                                       <?php echo (!isset($_POST['tipo_usuario']) || $_POST['tipo_usuario'] == 'Empleado') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="empleado">
                                                    <i class="fas fa-user-tie me-1"></i>Empleado
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tipo_usuario" 
                                                       id="externo" value="Externo"
                                                       <?php echo (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'Externo') ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="externo">
                                                    <i class="fas fa-user me-1"></i>Externo
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="usuario_requiere_servicio" class="form-label required">Usuario que requiere servicio</label>
                                        <select class="form-select" id="usuario_requiere_servicio" name="usuario_requiere_servicio" required>
                                            <option value="">--Seleccione--</option>
                                            <?php foreach ($datosFormulario['usuarios'] as $usuario): ?>
                                                <option value="<?php echo $usuario['id_usuario']; ?>"
                                                        <?php echo (isset($_POST['usuario_requiere_servicio']) && $_POST['usuario_requiere_servicio'] == $usuario['id_usuario']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($usuario['nombre_usuario']) . ' (' . htmlspecialchars($usuario['email']) . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="usuario_aprueba" class="form-label">Usuario que aprueba</label>
                                        <select class="form-select" id="usuario_aprueba" name="usuario_aprueba" disabled>
                                            <option value="">--Se asigna automáticamente--</option>
                                        </select>
                                        <div class="form-text">Se asignará automáticamente cuando se apruebe</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="origen" class="form-label required">Origen</label>
                                        <select class="form-select" id="origen" name="origen" required>
                                            <option value="">--Seleccione--</option>
                                            <?php foreach ($datosFormulario['origenes_destinos'] as $lugar): ?>
                                                <option value="<?php echo $lugar['id_origen_destino']; ?>"
                                                        <?php echo (isset($_POST['origen']) && $_POST['origen'] == $lugar['id_origen_destino']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($lugar['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="destino" class="form-label required">Destino</label>
                                        <select class="form-select" id="destino" name="destino" required>
                                            <option value="">--Seleccione--</option>
                                            <?php foreach ($datosFormulario['origenes_destinos'] as $lugar): ?>
                                                <option value="<?php echo $lugar['id_origen_destino']; ?>"
                                                        <?php echo (isset($_POST['destino']) && $_POST['destino'] == $lugar['id_origen_destino']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($lugar['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_servicio" class="form-label required">Fecha Servicio</label>
                                        <input type="date" class="form-control" id="fecha_servicio" name="fecha_servicio" 
                                               min="<?php echo date('Y-m-d'); ?>" required
                                               value="<?php echo isset($_POST['fecha_servicio']) ? $_POST['fecha_servicio'] : ''; ?>">
                                        <div class="form-text">No se pueden seleccionar fechas anteriores a hoy</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="hora_servicio" class="form-label required">Hora Servicio</label>
                                        <input type="time" class="form-control" id="hora_servicio" name="hora_servicio" required
                                               value="<?php echo isset($_POST['hora_servicio']) ? $_POST['hora_servicio'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="valor_trayecto" class="form-label required">Valor del Trayecto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="valor_trayecto" name="valor_trayecto" 
                                                   min="0" step="1000" required
                                                   value="<?php echo isset($_POST['valor_trayecto']) ? $_POST['valor_trayecto'] : ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" value="PENDIENTE" readonly>
                                        <div class="form-text">El estado inicial es siempre PENDIENTE</div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Información importante:</h6>
                                    <ul class="mb-0">
                                        <li>Al guardar la solicitud, el sistema validará que no se haya registrado el mismo trayecto para la misma fecha y hora.</li>
                                        <li>La solicitud quedará en estado <strong>PENDIENTE</strong>.</li>
                                        <li>Un administrador debe aprobar la solicitud para que cambie a estado <strong>APROBADO</strong>.</li>
                                        <li>Si la solicitud es rechazada, cambiará a estado <strong>RECHAZADO</strong>.</li>
                                    </ul>
                                </div>
                                
                                <div class="d-flex gap-3 justify-content-end">
                                    <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                                        <i class="fas fa-broom me-2"></i>Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Trayecto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.js"></script>
    
    <script>
        // Validación del formulario
        document.getElementById('formTrayecto').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar que origen y destino sean diferentes
            const origen = document.getElementById('origen').value;
            const destino = document.getElementById('destino').value;
            
            if (origen === destino && origen !== '') {
                Swal.fire({
                    title: 'Error de validación',
                    text: 'El origen y destino no pueden ser iguales',
                    icon: 'error'
                });
                return;
            }
            
            // Validar fecha futura
            const fechaServicio = document.getElementById('fecha_servicio').value;
            const fechaHoy = new Date().toISOString().split('T')[0];
            
            if (fechaServicio < fechaHoy) {
                Swal.fire({
                    title: 'Error de validación',
                    text: 'La fecha de servicio no puede ser anterior a hoy',
                    icon: 'error'
                });
                return;
            }
            
            // Confirmar envío
            Swal.fire({
                title: '¿Confirmar solicitud?',
                text: "Se creará una nueva solicitud de trayecto",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, crear solicitud',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
        
        function limpiarFormulario() {
            Swal.fire({
                title: '¿Limpiar formulario?',
                text: "Se perderán todos los datos ingresados",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formTrayecto').reset();
                    document.getElementById('fecha_solicitud').value = '<?php echo date('Y-m-d'); ?>';
                    document.getElementById('empleado').checked = true;
                }
            });
        }
        
        // Actualizar valor total cuando cambie el valor del trayecto
        document.getElementById('valor_trayecto').addEventListener('change', function() {
            // En este caso, valor_total = valor_trayecto
            // Podrías agregar lógica adicional aquí si fuera necesario
        });
        
        // Deshabilitar origen seleccionado en destino y viceversa
        document.getElementById('origen').addEventListener('change', function() {
            const destinoSelect = document.getElementById('destino');
            const origenValue = this.value;
            
            // Habilitar todas las opciones primero
            Array.from(destinoSelect.options).forEach(option => {
                option.disabled = false;
            });
            
            // Deshabilitar la opción seleccionada en origen
            if (origenValue) {
                Array.from(destinoSelect.options).forEach(option => {
                    if (option.value === origenValue) {
                        option.disabled = true;
                    }
                });
                
                // Si el destino actualmente seleccionado es igual al origen, resetear
                if (destinoSelect.value === origenValue) {
                    destinoSelect.value = '';
                }
            }
        });
        
        document.getElementById('destino').addEventListener('change', function() {
            const origenSelect = document.getElementById('origen');
            const destinoValue = this.value;
            
            // Habilitar todas las opciones primero
            Array.from(origenSelect.options).forEach(option => {
                option.disabled = false;
            });
            
            // Deshabilitar la opción seleccionada en destino
            if (destinoValue) {
                Array.from(origenSelect.options).forEach(option => {
                    if (option.value === destinoValue) {
                        option.disabled = true;
                    }
                });
                
                // Si el origen actualmente seleccionado es igual al destino, resetear
                if (origenSelect.value === destinoValue) {
                    origenSelect.value = '';
                }
            }
        });
    </script>
</body>
</html>