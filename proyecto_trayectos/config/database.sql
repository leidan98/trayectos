-- Crear base de datos
CREATE DATABASE IF NOT EXISTS trayectos_especiales;
USE trayectos_especiales;

-- Tabla de Estados
CREATE TABLE IF NOT EXISTS Tbl_Estados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE
);

-- Insertar estados predefinidos
INSERT INTO Tbl_Estados (nombre_estado) VALUES 
('PENDIENTE'),
('POR APROBAR'),
('APROBADO'),
('RECHAZADO');

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS Tbl_Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('admin', 'jefe') NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuarios de prueba
INSERT INTO Tbl_Users (username, password, tipo_usuario, nombre_completo, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrador Principal', 'admin@gmail.com'),
('jefe1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jefe', 'Leidy Silva', 'leisilva@gmail.com'),
('jefe2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jefe', 'Daniela Morales', 'danimor@gmail.com');
-- Contraseña por defecto: password

-- Tabla de Origen/Destino
CREATE TABLE IF NOT EXISTS Tbl_OrigenDestino (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('AEROPUERTO', 'TERMINAL', 'EMPRESA', 'RESIDENCIA', 'OTRO') NOT NULL,
    direccion VARCHAR(255),
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar algunos orígenes y destinos
INSERT INTO Tbl_OrigenDestino (nombre, tipo, direccion) VALUES
('AEROPUERTO', 'AEROPUERTO', 'Aeropuerto Internacional'),
('BQT COTA', 'EMPRESA', 'Cota, Cundinamarca'),
('CAJICA', 'EMPRESA', 'Cajicá, Cundinamarca'),
('TERMINAL NORTE', 'TERMINAL', 'Terminal de Transporte Norte'),
('TERMINAL SUR', 'TERMINAL', 'Terminal de Transporte Sur');

-- Tabla de Trayectos
CREATE TABLE IF NOT EXISTS Tbl_Trayectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_solicitud DATE NOT NULL,
    tipo_usuario_servicio ENUM('Empleado', 'Externo') NOT NULL,
    usuario_requiere_id INT NOT NULL,
    usuario_aprueba_id INT,
    origen_id INT NOT NULL,
    destino_id INT NOT NULL,
    fecha_servicio DATE NOT NULL,
    hora_servicio TIME NOT NULL,
    valor_trayecto DECIMAL(10,2) DEFAULT 0,
    valor_total DECIMAL(10,2) DEFAULT 0,
    estado_id INT DEFAULT 1,
    corte VARCHAR(10),
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_requiere_id) REFERENCES Tbl_Users(id),
    FOREIGN KEY (usuario_aprueba_id) REFERENCES Tbl_Users(id),
    FOREIGN KEY (origen_id) REFERENCES Tbl_OrigenDestino(id),
    FOREIGN KEY (destino_id) REFERENCES Tbl_OrigenDestino(id),
    FOREIGN KEY (estado_id) REFERENCES Tbl_Estados(id),
    UNIQUE KEY unique_trayecto (usuario_requiere_id, fecha_servicio, hora_servicio)
);

-- PROCEDIMIENTOS ALMACENADOS

DELIMITER //

-- Procedimiento para login
CREATE PROCEDURE sp_login(
    IN p_username VARCHAR(50)
)
BEGIN
    SELECT id, username, password, tipo_usuario, nombre_completo, email 
    FROM Tbl_Users 
    WHERE username = p_username AND estado = TRUE;
END //

-- Procedimiento para crear nuevo trayecto
CREATE PROCEDURE sp_crear_trayecto(
    IN p_fecha_solicitud DATE,
    IN p_tipo_usuario_servicio VARCHAR(20),
    IN p_usuario_requiere_id INT,
    IN p_usuario_aprueba_id INT,
    IN p_origen_id INT,
    IN p_destino_id INT,
    IN p_fecha_servicio DATE,
    IN p_hora_servicio TIME,
    IN p_valor_trayecto DECIMAL(10,2),
    IN p_corte VARCHAR(10)
)
BEGIN
    DECLARE v_count INT;
    DECLARE v_mensaje VARCHAR(255);
    
    -- Verificar duplicados
    SELECT COUNT(*) INTO v_count
    FROM Tbl_Trayectos
    WHERE usuario_requiere_id = p_usuario_requiere_id
    AND fecha_servicio = p_fecha_servicio
    AND hora_servicio = p_hora_servicio;
    
    IF v_count > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Ya existe un trayecto registrado para este usuario en la misma fecha y hora';
    ELSE
        INSERT INTO Tbl_Trayectos (
            fecha_solicitud, tipo_usuario_servicio, usuario_requiere_id,
            usuario_aprueba_id, origen_id, destino_id, fecha_servicio,
            hora_servicio, valor_trayecto, valor_total, estado_id, corte
        ) VALUES (
            p_fecha_solicitud, p_tipo_usuario_servicio, p_usuario_requiere_id,
            p_usuario_aprueba_id, p_origen_id, p_destino_id, p_fecha_servicio,
            p_hora_servicio, p_valor_trayecto, p_valor_trayecto, 1, p_corte
        );
        
        SELECT LAST_INSERT_ID() as id, 'Trayecto creado exitosamente' as mensaje;
    END IF;
END //

-- Procedimiento para listar trayectos por corte
CREATE PROCEDURE sp_listar_trayectos_corte(
    IN p_corte VARCHAR(10)
)
BEGIN
    SELECT 
        t.id,
        t.fecha_solicitud,
        t.fecha_servicio,
        t.hora_servicio,
        ur.email as usuario_requiere,
        ua.email as usuario_aprueba,
        o.nombre as origen,
        d.nombre as destino,
        t.valor_trayecto,
        t.valor_total,
        e.nombre_estado as estado,
        t.tipo_usuario_servicio
    FROM Tbl_Trayectos t
    INNER JOIN Tbl_Users ur ON t.usuario_requiere_id = ur.id
    LEFT JOIN Tbl_Users ua ON t.usuario_aprueba_id = ua.id
    INNER JOIN Tbl_OrigenDestino o ON t.origen_id = o.id
    INNER JOIN Tbl_OrigenDestino d ON t.destino_id = d.id
    INNER JOIN Tbl_Estados e ON t.estado_id = e.id
    WHERE t.corte = p_corte
    ORDER BY t.fecha_servicio DESC, t.hora_servicio DESC;
END //

-- Procedimiento para aprobar trayecto
CREATE PROCEDURE sp_aprobar_trayecto(
    IN p_trayecto_id INT,
    IN p_aprobar BOOLEAN
)
BEGIN
    DECLARE v_nuevo_estado INT;
    
    -- Si aprueba = true -> APROBADO (3), si no -> RECHAZADO (4)
    SET v_nuevo_estado = IF(p_aprobar = TRUE, 3, 4);
    
    UPDATE Tbl_Trayectos 
    SET estado_id = v_nuevo_estado
    WHERE id = p_trayecto_id AND estado_id = 2; -- Solo si está en POR APROBAR
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Procedimiento para cambiar estado de PENDIENTE a POR APROBAR
CREATE PROCEDURE sp_enviar_aprobacion(
    IN p_trayecto_id INT
)
BEGIN
    UPDATE Tbl_Trayectos 
    SET estado_id = 2
    WHERE id = p_trayecto_id AND estado_id = 1; -- Solo si está PENDIENTE
    
    SELECT ROW_COUNT() as affected_rows;
END //

-- Procedimiento para obtener usuarios activos
CREATE PROCEDURE sp_obtener_usuarios_activos()
BEGIN
    SELECT id, nombre_completo, email, tipo_usuario
    FROM Tbl_Users
    WHERE estado = TRUE
    ORDER BY nombre_completo;
END //

-- Procedimiento para obtener origenes y destinos activos
CREATE PROCEDURE sp_obtener_origenes_destinos()
BEGIN
    SELECT id, nombre, tipo
    FROM Tbl_OrigenDestino
    WHERE estado = TRUE
    ORDER BY nombre;
END //

-- Procedimiento para obtener resumen por corte
CREATE PROCEDURE sp_resumen_corte(
    IN p_corte VARCHAR(10)
)
BEGIN
    SELECT 
        COUNT(CASE WHEN estado_id = 3 THEN 1 END) as aprobados,
        COUNT(CASE WHEN estado_id != 3 THEN 1 END) as sin_aprobar,
        SUM(CASE WHEN estado_id = 3 THEN valor_total ELSE 0 END) as total_aprobado,
        SUM(CASE WHEN estado_id != 3 THEN valor_total ELSE 0 END) as total_sin_aprobar
    FROM Tbl_Trayectos
    WHERE corte = p_corte;
END //

DELIMITER ;