<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema' ?> - Trayectos Especiales</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .navbar {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }
        
        .user-type {
            background-color: #28a745;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .user-type.admin {
            background-color: #dc3545;
        }
        
        .btn-logout {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        
        .btn-logout:hover {
            background-color: #c82333;
        }
        
        .sidebar {
            background-color: #f8f9fa;
            width: 250px;
            min-height: calc(100vh - 60px);
            padding: 1rem;
            position: fixed;
            left: 0;
            top: 60px;
            border-right: 1px solid #dee2e6;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-item {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-link {
            display: block;
            padding: 0.75rem 1rem;
            color: #495057;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .sidebar-link:hover {
            background-color: #e9ecef;
            color: #343a40;
        }
        
        .sidebar-link.active {
            background-color: #007bff;
            color: white;
        }
        
        .sidebar-header {
            font-weight: bold;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: calc(100vh - 60px);
        }
        
        .page-header {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        
        .breadcrumb {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .breadcrumb li:not(:last-child)::after {
            content: '/';
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="<?= BASE_URL ?>/" class="navbar-brand">
                🚗 Sistema de Trayectos Especiales
            </a>
            
            <div class="user-info">
                <span>👤 <?= htmlspecialchars($user['nombre_completo']) ?></span>
                <span class="user-type <?= $user['tipo_usuario'] ?>">
                    <?= strtoupper($user['tipo_usuario']) ?>
                </span>
                <a href="<?= BASE_URL ?>/logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>