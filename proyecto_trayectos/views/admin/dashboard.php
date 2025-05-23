<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="sidebar">
    <div class="sidebar-header">
        🏠 Inicio
    </div>
    
    <div class="sidebar-header" style="margin-top: 1rem;">
        📥 Carga de Información
    </div>
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="<?= BASE_URL ?>/trayectos" class="sidebar-link">
                📍 Trayectos
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                🎫 Peajes
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                ✈️ Parqueadero
            </a>
        </li>
        <li class="sidebar-item">
            <a href="<?= BASE_URL ?>/trayectos" class="sidebar-link" style="background-color: #fff3cd; color: #856404;">
                🚗 Trayectos Especiales
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Dashboard Administrativo</h1>
        <ul class="breadcrumb">
            <li>Inicio</li>
            <li>Dashboard</li>
        </ul>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #28a745;">
                ✓
            </div>
            <div class="stat-content">
                <h3>Aprobados</h3>
                <p class="stat-number"><?= $resumen['aprobados'] ?></p>
                <p class="stat-value">$<?= number_format($resumen['total_aprobado'], 0, ',', '.') ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #ffc107;">
                ⏳
            </div>
            <div class="stat-content">
                <h3>Sin Aprobar</h3>
                <p class="stat-number"><?= $resumen['sin_aprobar'] ?></p>
                <p class="stat-value">$<?= number_format($resumen['total_sin_aprobar'], 0, ',', '.') ?></p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #007bff;">
                📅
            </div>
            <div class="stat-content">
                <h3>Corte Actual</h3>
                <p class="stat-number"><?= $corte_actual ?></p>
                <p class="stat-value">Total: $<?= number_format($resumen['total_aprobado'] + $resumen['total_sin_aprobar'], 0, ',', '.') ?></p>
            </div>
        </div>
    </div>
    
    <div class="quick-actions">
        <h2>Acciones Rápidas</h2>
        <div class="action-buttons">
            <a href="<?= BASE_URL ?>/trayectos/create" class="btn btn-primary">
                ➕ Nuevo Trayecto
            </a>
            <a href="<?= BASE_URL ?>/trayectos/approve" class="btn btn-warning">
                📋 Aprobar Trayectos
            </a>
            <a href="<?= BASE_URL ?>/trayectos" class="btn btn-info">
                📊 Ver Todos los Trayectos
            </a>
        </div>
    </div>
</div>

<style>
.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-content h3 {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1rem;
    color: #6c757d;
}

.quick-actions {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quick-actions h2 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: #343a40;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}
</style>

</body>
</html>