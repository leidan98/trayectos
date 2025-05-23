<?php
// index.php

require_once 'BD/session.php';
require_once 'CONTROLADOR/AuthController.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_POST) {
    $authController = new AuthController();
    $resultado = $authController->login($_POST['email'], $_POST['password']);
    
    if ($resultado['success']) {
        header("Location: " . $resultado['redirect']);
        exit();
    } else {
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = 'error';
    }
}

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Trayectos Especiales - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-form {
            padding: 2rem;
        }
        
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        
        .btn-login:hover {
            opacity: 0.9;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="login-header">
                        <h2><i class="fas fa-user-shield me-2"></i>Iniciar Sesión</h2>
                        <p class="mb-0">Sistema de Trayectos Especiales</p>
                    </div>
                    
                    <div class="login-form">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="Ingrese su correo electrónico">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Ingrese su contraseña">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="forgot-password.php" class="text-decoration-none">¿Olvidó su contraseña?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Deshabilitar el envío del formulario si hay campos inválidos
        (function() {
            'use strict';
            
            // Obtener todos los formularios a los que queremos aplicar estilos de validación de Bootstrap
            var forms = document.querySelectorAll('.needs-validation');
            
            // Bucle sobre ellos y evitar el envío
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>
</html>