<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid" style="padding: 2rem;">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="page-title">📍 Trayectos Especiales</h1>
                <p style="color: #6c757d;">Carga de Trayectos Especiales para el corte <?= $corte ?></p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <form method="GET" action="<?= BASE_URL ?>/trayectos" style="display: flex; gap: 0.5rem;">
                    <input type="month" name="corte" value="<?= date('Y-m', strtotime($corte . '-01')) ?>" 
                           class="form-control" onchange="this.form.submit()">
                </form>
                <a href="<?= BASE_URL ?>/trayectos/create" class="btn btn-primary">
                    ➕ Nuevo Trayecto
                </a>
            </div>
        </div>
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
    
    <div class="summary-cards">
        <div class="summary-card">
            <h4>Aprobado: $<?= number_format($resumen['total_aprobado'], 0, ',', '.') ?></h4>
        </div>
        <div class="summary-card">
            <h4>Sin Aprobar: $<?= number_format($resumen['total_sin_aprobar'], 0, ',', '.') ?></h4>
        </div>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Usuario aprueba</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha Servicio</th>
                    <th>Hora Servicio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trayectos as $trayecto): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($trayecto['fecha_solicitud'])) ?></td>
                    <td><?= htmlspecialchars($trayecto['usuario_requiere']) ?></td>
                    <td><?= htmlspecialchars($trayecto['usuario_aprueba'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($trayecto['origen']) ?></td>
                    <td><?= htmlspecialchars($trayecto['destino']) ?></td>
                    <td><?= date('d/m/Y', strtotime($trayecto['fecha_servicio'])) ?></td>
                    <td><?= date('H:i', strtotime($trayecto['hora_servicio'])) ?></td>
                    <td>
                        <span class="estado-badge estado-<?= str_replace(' ', '-', strtolower($trayecto['estado'])) ?>">
                            <?= $trayecto['estado'] ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-action" onclick="verDetalles(<?= $trayecto['id'] ?>)">
                            👁️ Ver
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($trayectos)): ?>
            <div class="empty-state">
                <p>No hay trayectos registrados para este corte.</p>
                <a href="<?= BASE_URL ?>/trayectos/create" class="btn btn-primary">
                    Crear primer trayecto
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="table-footer">
        <p>Mostrando <?= count($trayectos) ?> de <?= count($trayectos) ?> entradas</p>
        <div class="pagination">
            <button class="page-btn" disabled>Ant.</button>
            <span class="page-number">1</span>
            <button class="page-btn" disabled>Sig.</button>
        </div>
    </div>
</div>

<style>
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

.form-control {
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
}

.summary-cards {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.summary-card h4 {
    margin: 0;
    color: #343a40;
}

.table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #f8f9fa;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.data-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #dee2e6;
}

.data-table tr:hover {
    background-color: #f8f9fa;
}

.estado-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
}

.estado-pendiente {
    background-color: #e3f2fd;
    color: #1976d2;
}

.estado-por-aprobar {
    background-color: #fff3cd;
    color: #856404;
}

.estado-aprobado {
    background-color: #d4edda;
    color: #155724;
}

.estado-rechazado {
    background-color: #f8d7da;
    color: #721c24;
}

.btn-action {
    background: #007bff;
    color: white;
    border: none;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-action:hover {
    background: #0056b3;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.page-btn {
    padding: 0.25rem 0.75rem;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-number {
    padding: 0.25rem 0.75rem;
    background: #007bff;
    color: white;
    border-radius: 4px;
}
</style>

<script>
function verDetalles(id) {
    alert('Ver detalles del trayecto #' + id);
}
</script>

</body>
</html>