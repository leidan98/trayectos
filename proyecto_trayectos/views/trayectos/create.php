<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container" style="max-width: 800px; margin: 2rem auto; padding: 0 1rem;">
    <div class="form-container">
        <div class="form-header">
            <h2>Nuevo trayecto</h2>
            <button type="button" class="close-btn" onclick="window.location.href='<?= BASE_URL ?>/trayectos'">✕</button>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/trayectos/store" id="trayectoForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_solicitud">Fecha Solicitud*</label>
                    <input type="date" id="fecha_solicitud" name="fecha_solicitud" 
                           value="<?= $fecha_solicitud ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Tipo de Usuario</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="tipo_usuario_servicio" value="Empleado" checked>
                            <span class="checkbox-custom">✓</span>
                            Empleado
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tipo_usuario_servicio" value="Externo">
                            <span class="checkbox-custom"></span>
                            Externo
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="usuario_requiere_id">Usuario requiere servicio</label>
                    <select id="usuario_requiere_id" name="usuario_requiere_id" required class="form-control">
                        <option value="">--Seleccione--</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>">
                                <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= $usuario['email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="usuario_aprueba_id">Usuario aprueba</label>
                    <select id="usuario_aprueba_id" name="usuario_aprueba_id" required class="form-control">
                        <option value="">--Seleccione--</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <?php if ($usuario['tipo_usuario'] === 'admin'): ?>
                                <option value="<?= $usuario['id'] ?>">
                                    <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= $usuario['email'] ?>)
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="origen_id">Origen*</label>
                    <select id="origen_id" name="origen_id" required class="form-control">
                        <option value="">--Seleccione--</option>
                        <?php foreach ($origenes as $origen): ?>
                            <option value="<?= $origen['id'] ?>">
                                <?= htmlspecialchars($origen['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="destino_id">Destino*</label>
                    <select id="destino_id" name="destino_id" required class="form-control">
                        <option value="">--Seleccione--</option>
                        <?php foreach ($origenes as $destino): ?>
                            <option value="<?= $destino['id'] ?>">
                                <?= htmlspecialchars($destino['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_servicio">Fecha Servicio*</label>
                    <input type="date" id="fecha_servicio" name="fecha_servicio" 
                           min="<?= date('Y-m-d') ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="hora_servicio">Hora Servicio</label>
                    <div class="time-input-container">
                        <input type="time" id="hora_servicio" name="hora_servicio" 
                               required class="form-control">
                        <span class="time-icon">🕐</span>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    💾 Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.form-header {
    background: #f8f9fa;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
}

.form-header h2 {
    margin: 0;
    color: #343a40;
    font-size: 1.5rem;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-btn:hover {
    color: #343a40;
}

form {
    padding: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 500;
}

.form-control {
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out;
}

.form-control:focus {
    outline: none;
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.radio-group {
    display: flex;
    gap: 1.5rem;
    padding-top: 0.5rem;
}

.radio-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    position: relative;
}

.radio-label input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #28a745;
    border-radius: 3px;
    margin-right: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background: white;
}

.radio-label input[type="radio"]:checked + .checkbox-custom {
    background: #28a745;
}

.time-input-container {
    position: relative;
}

.time-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('trayectoForm').addEventListener('submit', function(e) {
    const origen = document.getElementById('origen_id').value;
    const destino = document.getElementById('destino_id').value;
    
    if (origen === destino) {
        e.preventDefault();
        alert('El origen y destino no pueden ser iguales');
        return false;
    }
});
</script>

</body>
</html>