<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid" style="padding: 2rem;">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="page-title">📋 Aprobaciones de Trayectos especiales para el corte <?= $corte ?></h1>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <form method="GET" action="<?= BASE_URL ?>/trayectos/approve" style="display: flex; gap: 0.5rem;">
                    <input type="month" name="corte" value="<?= date('Y-m', strtotime($corte . '-01')) ?>" 
                           class="form-control" onchange="this.form.submit()">
                </form>
                <button onclick="aprobarTodos()" class="btn btn-success" <?= empty($trayectos) ? 'disabled' : '' ?>>
                    ✓ Aprobar
                </button>
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
    
    <div class="tabs">
        <button class="tab active">Pendientes</button>
        <button class="tab" onclick="window.location.href='<?= BASE_URL ?>/trayectos'">Aprobadas</button>
        <button class="tab" onclick="window.location.href='<?= BASE_URL ?>/trayectos'">Rechazadas</button>
    </div>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                    </th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Usuario</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Valor Trayecto</th>
                    <th>Valor Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trayectos as $trayecto): ?>
                <tr>
                    <td>
                        <input type="checkbox" class="trayecto-check" value="<?= $trayecto['id'] ?>">
                    </td>
                    <td><?= date('Y-m-d', strtotime($trayecto['fecha_servicio'])) ?></td>
                    <td><?= date('H:i', strtotime($trayecto['hora_servicio'])) ?></td>
                    <td><?= htmlspecialchars($trayecto['usuario_requiere']) ?></td>
                    <td><?= htmlspecialchars($trayecto['origen']) ?></td>
                    <td><?= htmlspecialchars($trayecto['destino']) ?></td>
                    <td>$<?= number_format($trayecto['valor_trayecto'], 0, ',', '.') ?></td>
                    <td>$<?= number_format($trayecto['valor_total'], 0, ',', '.') ?></td>
                    <td>
                        <div class="action-buttons">
                            <form method="POST" action="<?= BASE_URL ?>/trayectos/process-approval" style="display: inline;">
                                <input type="hidden" name="trayecto_id" value="<?= $trayecto['id'] ?>">
                                <input type="hidden" name="accion" value="aprobar">
                                <button type="submit" class="btn-small btn-approve" title="Aprobar">
                                    ✓
                                </button>
                            </form>
                            <form method="POST" action="<?= BASE_URL ?>/trayectos/process-approval" style="display: inline;">
                                <input type="hidden" name="trayecto_id" value="<?= $trayecto['id'] ?>">
                                <input type="hidden" name="accion" value="rechazar">
                                <button type="submit" class="btn-small btn-reject" title="Rechazar">
                                    ✗
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($trayectos)): ?>
            <div class="empty-state">
                <p>No hay trayectos pendientes de aprobación para este corte.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($trayectos)): ?>
    <div class="table-footer">
        <div class="summary-info">
            <span class="warning-icon">⚠️</span>
            <span>Total sin aprobar: $<?= number_format(array_sum(array_column($trayectos, 'valor_total')), 0, ',', '.') ?></span>
        </div>
        <p>Mostrando 1 a <?= count($trayectos) ?> de <?= count($trayectos) ?> entradas</p>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmación -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>❓ Alerta</h3>
        </div>
        <div class="modal-body">
            <p>¿Está seguro de aprobar los trayectos especiales?</p>
        </div>
        <div class="modal-footer">
            <button onclick="confirmarAprobacion()" class="btn btn-primary">SI</button>
            <button onclick="cerrarModal()" class="btn btn-secondary">NO</button>
        </div>
    </div>
</div>

<style>
.tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #dee2e6;
}

.tab {
    background: none;
    border: none;
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    color: #6c757d;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.tab.active {
    color: #ffc107;
    border-bottom-color: #ffc107;
}

.tab:hover:not(.active) {
    color: #495057;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-small {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-approve:hover {
    background: #218838;
}

.btn-reject {
    background: #dc3545;
    color: white;
}

.btn-reject:hover {
    background: #c82333;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover:not(:disabled) {
    background: #218838;
}

.btn-success:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.summary-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #856404;
    background: #fff3cd;
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.warning-icon {
    font-size: 1.2rem;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 0;
    border-radius: 8px;
    width: 400px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
}

.modal-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-body {
    padding: 1.5rem;
    text-align: center;
}

.modal-footer {
    display: flex;
    justify-content: center;
    gap: 1rem;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}
</style>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.trayecto-check');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function aprobarTodos() {
    const checkboxes = document.querySelectorAll('.trayecto-check:checked');
    if (checkboxes.length === 0) {
        alert('Por favor seleccione al menos un trayecto para aprobar');
        return;
    }
    
    document.getElementById('confirmModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

function confirmarAprobacion() {
    const checkboxes = document.querySelectorAll('.trayecto-check:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    fetch('<?= BASE_URL ?>/trayectos/confirm-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'trayectos=' + JSON.stringify(ids)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(error => {
        alert('Error al procesar la solicitud');
    });
    
    cerrarModal();
}

// Cerrar modal si se hace click fuera
window.onclick = function(event) {
    const modal = document.getElementById('confirmModal');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>

</body>
</html>