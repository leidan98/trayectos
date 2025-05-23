<?php
// procesar_trayecto.php

require_once 'config/session.php';
require_once 'controllers/TrayectoController.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Verificar sesión y permisos de admin
verificarSesion();
if ($_SESSION['tipo_usuario'] != '1') {
    http_response_code(403);
    echo json_encode(['success' => false, 'mensaje' => 'No tiene permisos para realizar esta acción']);
    exit();
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id_trayecto']) || !isset($input['accion'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
    exit();
}

$id_trayecto = (int)$input['id_trayecto'];
$accion = strtoupper(trim($input['accion']));
$usuario_aprueba = $_SESSION['usuario_id'];

// Validar acción
if (!in_array($accion, ['APROBAR', 'RECHAZAR'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
    exit();
}

try {
    $trayectoController = new TrayectoController();
    
    if ($accion === 'APROBAR') {
        $resultado = $trayectoController->aprobarTrayecto($id_trayecto, $usuario_aprueba);
    } else {
        $resultado = $trayectoController->rechazarTrayecto($id_trayecto, $usuario_aprueba);
    }
    
    if ($resultado['resultado'] == 1) {
        echo json_encode([
            'success' => true,
            'mensaje' => $resultado['mensaje']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => $resultado['mensaje']
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error en procesar_trayecto.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error interno del servidor'
    ]);
}
?>

<?php
// logout.php

require_once 'config/session.php';

limpiarSesion();
?>

<?php
// .htaccess

# RewriteEngine On
# RewriteCond %{REQUEST_FILENAME} !-f
# RewriteCond %{REQUEST_FILENAME} !-d
# RewriteRule ^([^?]*) index.php?route=$1 [L,QSA]

# Configuración de PHP
# php_value upload_max_filesize 10M
# php_value post_max_size 10M
# php_value memory_limit 256M

# Seguridad
# Options -Indexes
# Header always set X-Content-Type-Options nosniff
# Header always set X-Frame-Options DENY
# Header always set X-XSS-Protection "1; mode=block"
?>

<?php
// README.md
/*
# Sistema de Trayectos Especiales

Sistema web desarrollado en PHP con arquitectura MVC para la gestión de trayectos especiales, con autenticación de usuarios y aprobación de solicitudes.

## Características

- **Arquitectura MVC**: Modelo-Vista-Controlador para mejor organización del código
- **Autenticación**: Sistema de login con dos tipos de usuario (Admin y Jefe)
- **Gestión de Trayectos**: Creación, consulta y aprobación de solicitudes
- **Procedimientos Almacenados**: Todas las operaciones de base de datos utilizan stored procedures
- **Interfaz Responsiva**: Diseño adaptable usando Bootstrap 5
- **AJAX**: Procesamiento asíncrono para mejor experiencia de usuario

## Estructura del Proyecto

```
sistema_trayectos/
├── config/
│   ├── database.php          # Configuración de base de datos
│   └── session.php           # Manejo de sesiones
├── controllers/
│   ├── AuthController.php    # Controlador de autenticación
│   ├── TrayectoController.php # Controlador de trayectos
│   └── DashboardController.php # Controlador del dashboard
├── models/
│   ├── Usuario.php           # Modelo de usuarios
│   ├── Trayecto.php         # Modelo de trayectos
│   ├── OrigenDestino.php    # Modelo de lugares
│   └── Estado.php           # Modelo de estados
├── views/
│   ├── index.php            # Login
│   ├── dashboard.php        # Panel principal
│   ├── nuevo_trayecto.php   # Formulario de trayectos
│   └── procesar_trayecto.php # Procesador AJAX
└── database/
    └── estructura.sql       # Script de base de datos
```

## Instalación

1. **Requisitos previos**:
   - PHP 7.4 o superior
   - MySQL 5.7 o superior
   - Servidor web (Apache/Nginx)
   - phpMyAdmin (opcional)

2. **Configurar base de datos**:
   - Crear base de datos `sistema_trayectos`
   - Ejecutar el script SQL proporcionado
   - Configurar credenciales en `config/database.php`

3. **Configurar aplicación**:
   - Subir archivos al servidor web
   - Ajustar permisos de carpetas si es necesario
   - Verificar configuración de PHP (extensión PDO MySQL)

## Uso

### Usuarios por defecto:
- **Administrador**: admin@sistema.com / password
- **Jefe**: jefe@sistema.com / password

### Funcionalidades principales:

1. **Login**: Autenticación con validación de credenciales
2. **Dashboard**: Visualización de trayectos por mes con filtros
3. **Nuevo Trayecto**: Formulario para crear solicitudes
4. **Aprobación**: Sistema de aprobación/rechazo para administradores
5. **Reportes**: Resumen de montos aprobados y pendientes

### Tipos de Usuario:
- **Admin (1)**: Acceso completo, puede aprobar/rechazar solicitudes
- **Jefe (2)**: Puede crear y ver trayectos, pero no aprobar

## Base de Datos

### Tablas principales:
- `Tbl_Users`: Usuarios del sistema
- `Tbl_Trayectos`: Solicitudes de trayectos
- `Tbl_OrigenDestino`: Catálogo de lugares
- `Tbl_Estados`: Estados de las solicitudes

### Procedimientos almacenados:
- `SP_AutenticarUsuario`: Validación de login
- `SP_CrearTrayecto`: Creación de solicitudes
- `SP_ObtenerTrayectosPorMes`: Consulta por período
- `SP_AprobarRechazarTrayecto`: Cambio de estado
- `SP_ObtenerResumenMontos`: Estadísticas financieras

## Seguridad

- Contraseñas hasheadas con SHA-256
- Validación de sesiones en todas las páginas
- Prepared statements para prevenir SQL injection
- Validación de permisos por tipo de usuario
- Sanitización de datos de entrada

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+, MySQL, PDO
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Librerías**: Font Awesome, SweetAlert2
- **Arquitectura**: MVC (Modelo-Vista-Controlador)

## Personalización

Para personalizar el sistema:

1. **Agregar nuevos orígenes/destinos**: Insertar en `Tbl_OrigenDestino`
2. **Modificar estados**: Actualizar `Tbl_Estados`
3. **Cambiar validaciones**: Editar procedimientos almacenados
4. **Personalizar interfaz**: Modificar archivos CSS y plantillas

## Soporte

Para reportar problemas o solicitar funcionalidades:
1. Verificar logs de error de PHP
2. Revisar conexión a base de datos
3. Validar permisos de usuario
4. Consultar documentación de MySQL

## Licencia

Este sistema está desarrollado como proyecto académico/empresarial.
Todos los derechos reservados.
*/
?>