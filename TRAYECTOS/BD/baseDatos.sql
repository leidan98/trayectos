CREATE DATABASE IF NOT EXISTS sistema_trayectos;
USE sistema_trayectos;

-- Tabla de Estados
CREATE TABLE Tbl_Estados (
    id_estado INT PRIMARY KEY AUTO_INCREMENT,
    nombre_estado VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Usuarios
CREATE TABLE Tbl_Users (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('1', '2') NOT NULL COMMENT '1=admin, 2=jefe',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de Origen y Destino
CREATE TABLE Tbl_OrigenDestino (
    id_origen_destino INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Trayectos
CREATE TABLE Tbl_Trayectos (
    id_trayecto INT PRIMARY KEY AUTO_INCREMENT,
    fecha_solicitud DATE NOT NULL,
    usuario_requiere_servicio INT NOT NULL,
    usuario_aprueba INT NULL,
    tipo_usuario ENUM('Empleado', 'Externo') DEFAULT 'Empleado',
    origen INT NOT NULL,
    destino INT NOT NULL,
    fecha_servicio DATE NOT NULL,
    hora_servicio TIME NOT NULL,
    valor_trayecto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    id_estado INT NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_requiere_servicio) REFERENCES Tbl_Users(id_usuario),
    FOREIGN KEY (usuario_aprueba) REFERENCES Tbl_Users(id_usuario),
    FOREIGN KEY (origen) REFERENCES Tbl_OrigenDestino(id_origen_destino),
    FOREIGN KEY (destino) REFERENCES Tbl_OrigenDestino(id_origen_destino),
    FOREIGN KEY (id_estado) REFERENCES Tbl_Estados(id_estado)
);

-- Insertar datos iniciales
INSERT INTO Tbl_Estados (nombre_estado, descripcion) VALUES
('PENDIENTE', 'Solicitud pendiente de aprobación'),
('POR APROBAR', 'En proceso de aprobación'),
('APROBADO', 'Solicitud aprobada'),
('RECHAZADO', 'Solicitud rechazada');

INSERT INTO Tbl_OrigenDestino (nombre) VALUES
('AEROPUERTO'),
('BQT COTA'),
('CAJICA'),
('CENTRO COMERCIAL'),
('HOTEL');

-- Usuario administrador por defecto
INSERT INTO Tbl_Users (nombre_usuario, email, password, tipo_usuario) VALUES
('Administrador', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1'),
('Jefe Operaciones', 'jefe@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2');

-- PROCEDIMIENTOS ALMACENADOS

DELIMITER //

-- Procedimiento para autenticar usuario
CREATE PROCEDURE SP_AutenticarUsuario(
    IN p_email VARCHAR(150),
    IN p_password VARCHAR(255)
)
BEGIN
    SELECT 
        id_usuario,
        nombre_usuario,
        email,
        tipo_usuario,
        activo
    FROM Tbl_Users 
    WHERE email = p_email 
    AND password = p_password 
    AND activo = TRUE;
END //

-- Procedimiento para obtener usuarios activos
CREATE PROCEDURE SP_ObtenerUsuarios()
BEGIN
    SELECT 
        id_usuario,
        nombre_usuario,
        email,
        tipo_usuario,
        activo
    FROM Tbl_Users 
    WHERE activo = TRUE
    ORDER BY nombre_usuario;
END //

-- Procedimiento para obtener orígenes y destinos activos
CREATE PROCEDURE SP_ObtenerOrigenDestino()
BEGIN
    SELECT 
        id_origen_destino,
        nombre,
        descripcion,
        activo
    FROM Tbl_OrigenDestino 
    WHERE activo = TRUE
    ORDER BY nombre;
END //

-- Procedimiento para crear nuevo trayecto
CREATE PROCEDURE SP_CrearTrayecto(
    IN p_fecha_solicitud DATE,
    IN p_usuario_requiere INT,
    IN p_tipo_usuario VARCHAR(20),
    IN p_origen INT,
    IN p_destino INT,
    IN p_fecha_servicio DATE,
    IN p_hora_servicio TIME,
    IN p_valor_trayecto DECIMAL(10,2),
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_resultado = 0;
        SET p_mensaje = 'Error al crear el trayecto';
    END;

    START TRANSACTION;

    -- Verificar si ya existe el mismo trayecto
    SELECT COUNT(*) INTO v_existe
    FROM Tbl_Trayectos
    WHERE usuario_requiere_servicio = p_usuario_requiere
    AND fecha_servicio = p_fecha_servicio
    AND hora_servicio = p_hora_servicio
    AND id_estado IN (1, 2, 3); -- PENDIENTE, POR APROBAR, APROBADO

    IF v_existe > 0 THEN
        SET p_resultado = 0;
        SET p_mensaje = 'Ya existe un trayecto registrado para esta fecha y hora';
        ROLLBACK;
    ELSE
        INSERT INTO Tbl_Trayectos (
            fecha_solicitud,
            usuario_requiere_servicio,
            tipo_usuario,
            origen,
            destino,
            fecha_servicio,
            hora_servicio,
            valor_trayecto,
            valor_total,
            id_estado
        ) VALUES (
            p_fecha_solicitud,
            p_usuario_requiere,
            p_tipo_usuario,
            p_origen,
            p_destino,
            p_fecha_servicio,
            p_hora_servicio,
            p_valor_trayecto,
            p_valor_trayecto,
            1 -- PENDIENTE
        );

        SET p_resultado = LAST_INSERT_ID();
        SET p_mensaje = 'Trayecto creado exitosamente';
        COMMIT;
    END IF;
END //

-- Procedimiento para obtener trayectos por mes
CREATE PROCEDURE SP_ObtenerTrayectosPorMes(
    IN p_mes INT,
    IN p_anio INT
)
BEGIN
    SELECT 
        t.id_trayecto,
        t.fecha_solicitud,
        t.fecha_servicio,
        t.hora_servicio,
        u1.nombre_usuario as usuario_requiere,
        u1.email as email_requiere,
        u2.nombre_usuario as usuario_aprueba,
        u2.email as email_aprueba,
        o.nombre as origen,
        d.nombre as destino,
        t.valor_trayecto,
        t.valor_total,
        e.nombre_estado,
        t.tipo_usuario
    FROM Tbl_Trayectos t
    INNER JOIN Tbl_Users u1 ON t.usuario_requiere_servicio = u1.id_usuario
    LEFT JOIN Tbl_Users u2 ON t.usuario_aprueba = u2.id_usuario
    INNER JOIN Tbl_OrigenDestino o ON t.origen = o.id_origen_destino
    INNER JOIN Tbl_OrigenDestino d ON t.destino = d.id_origen_destino
    INNER JOIN Tbl_Estados e ON t.id_estado = e.id_estado
    WHERE MONTH(t.fecha_servicio) = p_mes 
    AND YEAR(t.fecha_servicio) = p_anio
    ORDER BY t.fecha_servicio, t.hora_servicio;
END //

-- Procedimiento para obtener trayectos pendientes
CREATE PROCEDURE SP_ObtenerTrayectosPendientes(
    IN p_mes INT,
    IN p_anio INT
)
BEGIN
    SELECT 
        t.id_trayecto,
        t.fecha_solicitud,
        t.fecha_servicio,
        t.hora_servicio,
        u1.nombre_usuario as usuario_requiere,
        u1.email as email_requiere,
        o.nombre as origen,
        d.nombre as destino,
        t.valor_trayecto,
        t.valor_total,
        t.tipo_usuario
    FROM Tbl_Trayectos t
    INNER JOIN Tbl_Users u1 ON t.usuario_requiere_servicio = u1.id_usuario
    INNER JOIN Tbl_OrigenDestino o ON t.origen = o.id_origen_destino
    INNER JOIN Tbl_OrigenDestino d ON t.destino = d.id_origen_destino
    WHERE t.id_estado = 1 -- PENDIENTE
    AND MONTH(t.fecha_servicio) = p_mes 
    AND YEAR(t.fecha_servicio) = p_anio
    ORDER BY t.fecha_servicio, t.hora_servicio;
END //

-- Procedimiento para aprobar/rechazar trayecto
CREATE PROCEDURE SP_AprobarRechazarTrayecto(
    IN p_id_trayecto INT,
    IN p_usuario_aprueba INT,
    IN p_accion VARCHAR(20), -- 'APROBAR' o 'RECHAZAR'
    OUT p_resultado INT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_nuevo_estado INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_resultado = 0;
        SET p_mensaje = 'Error al procesar la solicitud';
    END;

    START TRANSACTION;

    IF p_accion = 'APROBAR' THEN
        SET v_nuevo_estado = 3; -- APROBADO
        SET p_mensaje = 'Trayecto aprobado exitosamente';
    ELSE
        SET v_nuevo_estado = 4; -- RECHAZADO
        SET p_mensaje = 'Trayecto rechazado';
    END IF;

    UPDATE Tbl_Trayectos 
    SET id_estado = v_nuevo_estado,
        usuario_aprueba = p_usuario_aprueba
    WHERE id_trayecto = p_id_trayecto;

    IF ROW_COUNT() > 0 THEN
        SET p_resultado = 1;
        COMMIT;
    ELSE
        SET p_resultado = 0;
        SET p_mensaje = 'No se encontró el trayecto especificado';
        ROLLBACK;
    END IF;
END //

-- Procedimiento para obtener resumen de montos
CREATE PROCEDURE SP_ObtenerResumenMontos(
    IN p_mes INT,
    IN p_anio INT
)
BEGIN
    SELECT 
        COUNT(*) as total_trayectos,
        SUM(CASE WHEN id_estado = 3 THEN valor_total ELSE 0 END) as total_aprobado,
        SUM(CASE WHEN id_estado != 3 THEN valor_total ELSE 0 END) as total_sin_aprobar,
        SUM(valor_total) as total_general
    FROM Tbl_Trayectos
    WHERE MONTH(fecha_servicio) = p_mes 
    AND YEAR(fecha_servicio) = p_anio;
END //

DELIMITER ;