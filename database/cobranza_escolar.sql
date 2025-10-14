-- Base de datos: cobranza_escolar
CREATE DATABASE IF NOT EXISTS cobranza_escolar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cobranza_escolar;

-- Tabla colegio
CREATE TABLE colegio (
    id_colegio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    nit VARCHAR(32) NOT NULL,
    direccion VARCHAR(180) NULL,
    telefono VARCHAR(30) NULL,
    correo VARCHAR(120) NULL,
    logo VARCHAR(255) NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla sede
CREATE TABLE sede (
    id_sede INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    direccion VARCHAR(180) NULL,
    telefono VARCHAR(30) NULL,
    correo VARCHAR(120) NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_colegio) REFERENCES colegio(id_colegio)
);

-- Tabla usuario
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NULL,
    id_sede INT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    usuario VARCHAR(60) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin_global','admin_colegio','agente') NOT NULL DEFAULT 'agente',
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuario (usuario),
    UNIQUE KEY uq_email (email)
);

-- Tabla modulo_sistema
CREATE TABLE modulo_sistema (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(60) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255) NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla pivote usuario_colegio
CREATE TABLE usuario_colegio (
    id_usuario INT NOT NULL,
    id_colegio INT NOT NULL,
    PRIMARY KEY (id_usuario, id_colegio),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_colegio) REFERENCES colegio(id_colegio) ON DELETE CASCADE
);

-- Tabla pivote usuario_sede
CREATE TABLE usuario_sede (
    id_usuario INT NOT NULL,
    id_sede INT NOT NULL,
    PRIMARY KEY (id_usuario, id_sede),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_sede) REFERENCES sede(id_sede) ON DELETE CASCADE
);

-- Tabla pivote usuario_modulo
CREATE TABLE usuario_modulo (
    id_usuario INT NOT NULL,
    id_modulo INT NOT NULL,
    PRIMARY KEY (id_usuario, id_modulo),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_modulo) REFERENCES modulo_sistema(id_modulo) ON DELETE CASCADE
);

-- Tabla responsable financiero
CREATE TABLE responsable_financiero (
    id_responsable INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    tipo_documento VARCHAR(10) NOT NULL,
    numero_documento VARCHAR(40) NOT NULL,
    telefono VARCHAR(30) NULL,
    correo VARCHAR(120) NULL,
    direccion VARCHAR(180) NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla estudiante
CREATE TABLE estudiante (
    id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    id_responsable INT NOT NULL,
    codigo_estudiante VARCHAR(60) NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    grado VARCHAR(60) NULL,
    curso VARCHAR(60) NULL,
    estado ENUM('activo','inactivo','retirado') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla concepto_deuda
CREATE TABLE concepto_deuda (
    id_concepto INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255) NULL,
    tipo VARCHAR(60) NULL,
    valor_base DECIMAL(12,2) DEFAULT 0,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla periodo
CREATE TABLE periodo (
    id_periodo INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla deuda
CREATE TABLE deuda (
    id_deuda INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    id_estudiante INT NOT NULL,
    id_periodo INT NULL,
    id_concepto INT NULL,
    fecha_generacion DATE NOT NULL,
    valor_inicial DECIMAL(12,2) NOT NULL,
    saldo_actual DECIMAL(12,2) NOT NULL,
    estado ENUM('pendiente','pagado','en_acuerdo') DEFAULT 'pendiente',
    fecha_vencimiento DATE NULL,
    notas TEXT NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla registro_pago
CREATE TABLE registro_pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    id_estudiante INT NOT NULL,
    fecha_pago DATE NOT NULL,
    valor_total DECIMAL(12,2) NOT NULL,
    metodo_pago VARCHAR(60) NULL,
    referencia VARCHAR(120) NULL,
    observaciones VARCHAR(255) NULL,
    ruta_soporte VARCHAR(255) NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla acuerdo_pago
CREATE TABLE acuerdo_pago (
    id_acuerdo INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    id_responsable INT NOT NULL,
    id_estudiante INT NULL,
    monto_total DECIMAL(12,2) NOT NULL,
    cuotas INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    estado ENUM('activo','cerrado','incumplido') DEFAULT 'activo',
    observaciones TEXT NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla cuota_acuerdo
CREATE TABLE cuota_acuerdo (
    id_cuota INT AUTO_INCREMENT PRIMARY KEY,
    id_acuerdo INT NOT NULL,
    numero_cuota INT NOT NULL,
    fecha_pago DATE NOT NULL,
    valor_cuota DECIMAL(12,2) NOT NULL,
    estado ENUM('pendiente','pagada','vencida') DEFAULT 'pendiente',
    fecha_pago_real DATE NULL,
    observaciones VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_acuerdo) REFERENCES acuerdo_pago(id_acuerdo)
);

-- Tabla comunicacion
CREATE TABLE comunicacion (
    id_comunicacion INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    id_responsable INT NOT NULL,
    id_estudiante INT NULL,
    tipo VARCHAR(60) NULL,
    canal VARCHAR(60) NOT NULL,
    asunto VARCHAR(150) NULL,
    mensaje TEXT NOT NULL,
    resultado VARCHAR(255) NULL,
    fecha_envio DATETIME NOT NULL,
    usuario_registro INT NOT NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla carga_masiva
CREATE TABLE carga_masiva (
    id_carga INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    id_sede INT NOT NULL,
    tipo_archivo VARCHAR(60) NOT NULL,
    archivo_original VARCHAR(255) NOT NULL,
    archivo_procesado VARCHAR(255) NULL,
    total_registros INT DEFAULT 0,
    total_errores INT DEFAULT 0,
    resultado VARCHAR(60) NOT NULL,
    mensaje VARCHAR(255) NULL,
    usuario_registro INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla auditoria_usuario
CREATE TABLE auditoria_usuario (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_colegio INT NULL,
    id_sede INT NULL,
    modulo VARCHAR(120) NOT NULL,
    accion VARCHAR(60) NOT NULL,
    detalle TEXT NULL,
    ip VARCHAR(60) NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla configuracion_colegio
CREATE TABLE configuracion_colegio (
    id_configuracion INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    smtp_host VARCHAR(150) NULL,
    smtp_puerto VARCHAR(10) NULL,
    smtp_usuario VARCHAR(150) NULL,
    smtp_password VARCHAR(255) NULL,
    whatsapp_api_key VARCHAR(255) NULL,
    whatsapp_endpoint VARCHAR(255) NULL,
    sms_api_key VARCHAR(255) NULL,
    sms_endpoint VARCHAR(255) NULL,
    logo_path VARCHAR(255) NULL,
    actualizado_por INT NULL,
    fecha_actualizacion DATETIME NULL
);

-- Tabla parametros_sistema
CREATE TABLE parametros_sistema (
    id_parametro INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(120) NOT NULL,
    valor VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NULL,
    id_colegio INT NULL,
    id_sede INT NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos demo
INSERT INTO colegio (nombre, nit, direccion, telefono, correo) VALUES
('Colegio San José', '900123456', 'Cra 10 #20-30', '6011234567', 'contacto@sanjose.edu'),
('Colegio Nuestra Señora', '900987654', 'Calle 45 #12-15', '6017654321', 'info@nuestraseñora.edu');

INSERT INTO sede (id_colegio, nombre, direccion, telefono, correo) VALUES
(1, 'Sede Centro', 'Cra 10 #20-30', '6011234567', 'centro@sanjose.edu'),
(1, 'Sede Norte', 'Av 26 #15-45', '6012223344', 'norte@sanjose.edu'),
(2, 'Sede Principal', 'Calle 45 #12-15', '6017654321', 'principal@nuestraseñora.edu'),
(2, 'Sede Campo', 'Km 3 vía occidente', '6017788990', 'campo@nuestraseñora.edu');

INSERT INTO usuario (id_colegio, id_sede, nombre_completo, email, usuario, password_hash, rol, estado) VALUES
(NULL, NULL, 'Administrador Global', 'admin@demo.com', 'admin', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_global', 'activo'),
(1, NULL, 'Directora General San José', 'directora@sanjose.edu', 'admin_sj', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(2, NULL, 'Director General Nuestra Señora', 'director@nuestraseñora.edu', 'admin_ns', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(1, 1, 'Coordinador Académico Centro', 'coordinador.centro@sanjose.edu', 'coord_centro', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(1, 2, 'Coordinadora Sede Norte', 'coordinadora.norte@sanjose.edu', 'coord_norte', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(2, 3, 'Coordinadora Sede Principal', 'coordinadora.principal@nuestraseñora.edu', 'coord_principal', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(2, 4, 'Coordinador Sede Campo', 'coordinador.campo@nuestraseñora.edu', 'coord_campo', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio', 'activo'),
(1, 1, 'Agente Centro 1', 'agente.centro1@sanjose.edu', 'agente_centro1', '$2y$12$q1rtTLVlzStGU1IZKUC7F.t/M2.na5ZuiOl5J83kjAuylBxIwUekG', 'agente', 'activo'),
(1, 2, 'Agente Norte 1', 'agente.norte1@sanjose.edu', 'agente_norte1', '$2y$12$q1rtTLVlzStGU1IZKUC7F.t/M2.na5ZuiOl5J83kjAuylBxIwUekG', 'agente', 'activo'),
(2, 3, 'Agente Principal 1', 'agente.principal1@nuestraseñora.edu', 'agente_principal1', '$2y$12$q1rtTLVlzStGU1IZKUC7F.t/M2.na5ZuiOl5J83kjAuylBxIwUekG', 'agente', 'activo'),
(2, 4, 'Agente Campo 1', 'agente.campo1@nuestraseñora.edu', 'agente_campo1', '$2y$12$q1rtTLVlzStGU1IZKUC7F.t/M2.na5ZuiOl5J83kjAuylBxIwUekG', 'agente', 'inactivo');

INSERT INTO modulo_sistema (codigo, nombre, descripcion) VALUES
('cobranzas', 'Cobranzas', 'Gestión integral de cartera, pagos, acuerdos y comunicaciones'),
('administracion', 'Administración', 'Parametrización institucional y gestión de maestros'),
('parametrizacion', 'Parametrización', 'Configuración avanzada de parámetros y catálogos');

INSERT INTO usuario_colegio (id_usuario, id_colegio) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 2),
(4, 1),
(5, 1),
(6, 2),
(7, 2),
(8, 1),
(9, 1),
(10, 2),
(11, 2);

INSERT INTO usuario_sede (id_usuario, id_sede) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 2),
(3, 3),
(3, 4),
(4, 1),
(5, 2),
(6, 3),
(7, 4),
(8, 1),
(9, 2),
(10, 3),
(11, 4);

INSERT INTO usuario_modulo (id_usuario, id_modulo) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(9, 1),
(10, 1),
(11, 1);

INSERT INTO responsable_financiero (id_colegio, id_sede, nombre_completo, tipo_documento, numero_documento, telefono, correo, direccion) VALUES
(1, 1, 'María Rodríguez', 'CC', '10203040', '3000000000', 'maria@sanjose.edu', 'Cra 12 #23-45'),
(1, 2, 'Juan García', 'CC', '11223344', '3000000001', 'juan@sanjose.edu', 'Av 45 #12-09'),
(2, 3, 'Ana López', 'CC', '55667788', '3100000002', 'ana@nuestraseñora.edu', 'Calle 50 #20-10'),
(1, 1, 'Claudia Méndez', 'CC', '100200300', '3000000003', 'claudia@sanjose.edu', 'Cra 23 #45-67'),
(1, 1, 'Luis Pardo', 'CC', '10987654', '3000000004', 'luis@sanjose.edu', 'Calle 90 #12-34'),
(1, 2, 'Marcela Gaitán', 'CC', '10111213', '3000000005', 'marcela@sanjose.edu', 'Av 68 #45-12'),
(1, 2, 'Pedro Rivas', 'CC', '10111415', '3000000006', 'pedro@sanjose.edu', 'Cra 33 #55-10'),
(2, 3, 'Julia Herrera', 'CC', '76543210', '3000000007', 'julia@nuestraseñora.edu', 'Calle 65 #10-15'),
(2, 3, 'Diego Campos', 'CC', '88990011', '3000000008', 'diego@nuestraseñora.edu', 'Carrera 7 #54-21'),
(2, 4, 'Patricia León', 'CC', '99001122', '3000000009', 'patricia@nuestraseñora.edu', 'Km 3 vía occidente'),
(2, 4, 'Andrés Molina', 'CC', '11002233', '3000000010', 'andres@nuestraseñora.edu', 'Vereda La Esperanza'),
(1, 1, 'Elena Suárez', 'CC', '12003456', '3000000011', 'elena@sanjose.edu', 'Calle 13 #50-20'),
(1, 2, 'Rafael Jiménez', 'CC', '13004567', '3000000012', 'rafael@sanjose.edu', 'Av 19 #120-45'),
(2, 3, 'Camila Torres', 'CC', '14005678', '3000000013', 'camila@nuestraseñora.edu', 'Calle 70 #30-12'),
(2, 4, 'Hernán Valdez', 'CC', '15006789', '3000000014', 'hernan@nuestraseñora.edu', 'Finca Las Palmas'),
(1, 1, 'Isabel Romero', 'CC', '16007890', '3000000015', 'isabel@sanjose.edu', 'Cra 18 #45-23'),
(1, 2, 'Mauricio Peña', 'CC', '17008901', '3000000016', 'mauricio@sanjose.edu', 'Diag 92 #32-11'),
(2, 3, 'Paula Nieto', 'CC', '18009012', '3000000017', 'paula@nuestraseñora.edu', 'Carrera 14 #56-40'),
(2, 4, 'Jorge Cárdenas', 'CC', '19010123', '3000000018', 'jorge@nuestraseñora.edu', 'Vereda La Primavera'),
(1, 1, 'Nancy Fajardo', 'CC', '20011234', '3000000019', 'nancy@sanjose.edu', 'Calle 26 #18-55');

INSERT INTO estudiante (id_colegio, id_sede, id_responsable, codigo_estudiante, nombre_completo, grado, curso, estado) VALUES
(1, 1, 1, 'SJ-001', 'Carlos Rodríguez', '5°', '5A', 'activo'),
(1, 2, 2, 'SJ-045', 'Laura García', '8°', '8B', 'activo'),
(2, 3, 3, 'NS-020', 'Mateo López', '6°', '6A', 'activo'),
(1, 1, 4, 'SJ-052', 'Valentina Méndez', '4°', '4A', 'activo'),
(1, 1, 5, 'SJ-060', 'Miguel Pardo', '9°', '9B', 'activo'),
(1, 2, 6, 'SJ-071', 'Sara Gaitán', '3°', '3B', 'activo'),
(1, 2, 7, 'SJ-082', 'Tomás Rivas', '7°', '7A', 'activo'),
(2, 3, 8, 'NS-031', 'Sofía Herrera', '5°', '5A', 'activo'),
(2, 3, 9, 'NS-042', 'Samuel Campos', '8°', '8A', 'activo'),
(2, 4, 10, 'NS-053', 'Lucía León', '2°', '2B', 'activo'),
(2, 4, 11, 'NS-064', 'Esteban Molina', '10°', '10A', 'activo'),
(1, 1, 12, 'SJ-093', 'Daniela Suárez', '6°', '6B', 'activo'),
(1, 2, 13, 'SJ-104', 'Sebastián Jiménez', '11°', '11A', 'activo'),
(2, 3, 14, 'NS-075', 'Mariana Torres', '7°', '7B', 'activo'),
(2, 4, 15, 'NS-086', 'Valeria Valdez', '4°', '4B', 'activo'),
(1, 1, 16, 'SJ-115', 'Andrés Romero', '1°', '1A', 'activo'),
(1, 2, 17, 'SJ-126', 'Gabriela Peña', '2°', '2A', 'activo'),
(2, 3, 18, 'NS-097', 'Isabella Nieto', '3°', '3A', 'activo'),
(2, 4, 19, 'NS-108', 'Carlos Cárdenas', '9°', '9A', 'activo'),
(1, 1, 20, 'SJ-137', 'Julieta Fajardo', '5°', '5B', 'inactivo');

INSERT INTO concepto_deuda (id_colegio, nombre, descripcion, tipo, valor_base) VALUES
(1, 'Mensualidad', 'Mensualidad año lectivo', 'recurrente', 350000),
(1, 'Transporte', 'Servicio de ruta escolar', 'servicio', 120000),
(1, 'Almuerzo Escolar', 'Plan de alimentación', 'servicio', 95000),
(1, 'Actividades extracurriculares', 'Clubes y talleres', 'servicio', 80000),
(2, 'Matrícula', 'Matrícula anual', 'único', 450000),
(2, 'Mensualidad', 'Mensualidad año lectivo', 'recurrente', 380000),
(2, 'Transporte', 'Servicio de ruta escolar', 'servicio', 140000),
(2, 'Almuerzo Escolar', 'Plan de alimentación', 'servicio', 90000);

INSERT INTO periodo (id_colegio, nombre, fecha_inicio, fecha_fin) VALUES
(1, '2024-10', '2024-10-01', '2024-10-31'),
(1, '2024-11', '2024-11-01', '2024-11-30'),
(1, '2024-12', '2024-12-01', '2024-12-31'),
(1, '2025-01', '2025-01-01', '2025-01-31'),
(1, '2025-02', '2025-02-01', '2025-02-28'),
(1, '2025-03', '2025-03-01', '2025-03-31'),
(1, '2025-04', '2025-04-01', '2025-04-30'),
(2, '2024-10', '2024-10-01', '2024-10-31'),
(2, '2024-11', '2024-11-01', '2024-11-30'),
(2, '2024-12', '2024-12-01', '2024-12-31'),
(2, '2025-01', '2025-01-01', '2025-01-31'),
(2, '2025-02', '2025-02-01', '2025-02-28'),
(2, '2025-03', '2025-03-01', '2025-03-31'),
(2, '2025-04', '2025-04-01', '2025-04-30');

INSERT INTO deuda (id_colegio, id_sede, id_estudiante, id_periodo, id_concepto, fecha_generacion, valor_inicial, saldo_actual, estado, fecha_vencimiento) VALUES
(1, 1, 1, 4, 1, '2025-01-05', 350000, 350000, 'pendiente', '2025-01-30'),
(1, 1, 1, 5, 1, '2025-02-05', 350000, 0, 'pagado', '2025-02-28'),
(1, 1, 1, 6, 1, '2025-03-05', 350000, 350000, 'pendiente', '2025-03-30'),
(1, 1, 1, 7, 3, '2025-04-02', 95000, 95000, 'pendiente', '2025-04-25'),
(1, 2, 2, 4, 1, '2025-01-06', 350000, 0, 'pagado', '2025-01-31'),
(1, 2, 2, 6, 2, '2025-03-10', 120000, 60000, 'en_acuerdo', '2025-03-31'),
(1, 2, 2, 7, 4, '2025-04-12', 80000, 80000, 'pendiente', '2025-04-28'),
(2, 3, 3, 11, 5, '2025-01-10', 450000, 0, 'pagado', '2025-01-31'),
(2, 3, 3, 12, 7, '2025-02-10', 140000, 0, 'pagado', '2025-02-28'),
(2, 3, 3, 13, 6, '2025-03-12', 380000, 380000, 'pendiente', '2025-03-31'),
(1, 1, 4, 6, 1, '2025-03-08', 350000, 175000, 'en_acuerdo', '2025-03-30'),
(1, 1, 4, 7, 3, '2025-04-08', 95000, 95000, 'pendiente', '2025-04-26'),
(1, 1, 5, 6, 1, '2025-03-15', 350000, 350000, 'pendiente', '2025-03-31'),
(1, 2, 6, 3, 2, '2024-12-10', 120000, 120000, 'pendiente', '2024-12-30'),
(1, 2, 7, 6, 4, '2025-03-05', 80000, 80000, 'pendiente', '2025-03-25'),
(2, 3, 8, 10, 6, '2024-12-05', 380000, 380000, 'pendiente', '2024-12-29'),
(2, 3, 9, 13, 6, '2025-03-03', 380000, 190000, 'en_acuerdo', '2025-03-30'),
(2, 4, 10, 10, 8, '2024-12-15', 90000, 90000, 'pendiente', '2024-12-30'),
(2, 4, 11, 13, 7, '2025-03-10', 140000, 140000, 'pendiente', '2025-03-30'),
(1, 1, 12, 6, 1, '2025-03-12', 350000, 0, 'pagado', '2025-03-29'),
(1, 2, 13, 5, 1, '2025-02-14', 350000, 350000, 'pendiente', '2025-02-28'),
(2, 3, 14, 12, 7, '2025-02-18', 140000, 70000, 'en_acuerdo', '2025-02-28'),
(2, 4, 15, 14, 7, '2025-04-05', 140000, 140000, 'pendiente', '2025-04-30'),
(1, 1, 16, 1, 1, '2024-10-04', 350000, 350000, 'pendiente', '2024-10-30'),
(1, 2, 17, 2, 3, '2024-11-06', 95000, 0, 'pagado', '2024-11-25'),
(2, 3, 18, 9, 8, '2024-11-20', 90000, 90000, 'pendiente', '2024-11-30'),
(2, 4, 19, 12, 7, '2025-02-05', 140000, 140000, 'pendiente', '2025-02-28'),
(1, 1, 20, 6, 4, '2025-03-20', 80000, 80000, 'pendiente', '2025-03-31'),
(1, 1, 5, 3, 2, '2024-12-12', 120000, 0, 'pagado', '2024-12-30'),
(2, 3, 9, 8, 6, '2024-10-12', 380000, 0, 'pagado', '2024-10-30');

INSERT INTO registro_pago (id_colegio, id_sede, id_estudiante, fecha_pago, valor_total, metodo_pago, referencia, ruta_soporte) VALUES
(1, 1, 1, '2025-02-10', 350000, 'transferencia', 'TRX-1101', NULL),
(1, 1, 1, '2025-03-18', 175000, 'tarjeta', 'POS-2210', NULL),
(1, 2, 2, '2025-03-05', 60000, 'transferencia', 'TRX-1300', NULL),
(1, 2, 2, '2025-04-18', 40000, 'efectivo', 'REC-1420', NULL),
(2, 3, 3, '2025-01-20', 450000, 'efectivo', 'CAJA-1123', NULL),
(2, 3, 3, '2025-02-22', 140000, 'transferencia', 'TRX-1502', NULL),
(2, 3, 9, '2025-03-20', 190000, 'transferencia', 'TRX-3300', NULL),
(1, 1, 4, '2025-03-18', 175000, 'transferencia', 'TRX-3410', NULL),
(2, 4, 10, '2024-12-20', 45000, 'efectivo', 'REC-5501', NULL),
(2, 4, 11, '2025-03-28', 70000, 'transferencia', 'TRX-5520', NULL),
(1, 1, 12, '2025-03-25', 350000, 'transferencia', 'TRX-5721', NULL),
(1, 2, 13, '2025-02-20', 150000, 'tarjeta', 'POS-6011', NULL),
(2, 3, 14, '2025-02-25', 70000, 'transferencia', 'TRX-6502', NULL),
(2, 4, 15, '2025-04-18', 60000, 'efectivo', 'REC-7010', NULL),
(1, 1, 16, '2024-10-15', 200000, 'transferencia', 'TRX-7200', NULL),
(1, 2, 17, '2024-11-18', 95000, 'transferencia', 'TRX-7350', NULL),
(2, 3, 18, '2024-11-28', 45000, 'efectivo', 'REC-7800', NULL),
(2, 4, 19, '2025-02-18', 50000, 'transferencia', 'TRX-7900', NULL),
(1, 1, 20, '2025-03-27', 40000, 'transferencia', 'TRX-8001', NULL);

INSERT INTO acuerdo_pago (id_colegio, id_sede, id_responsable, id_estudiante, monto_total, cuotas, fecha_inicio, fecha_fin, estado, observaciones) VALUES
(1, 2, 2, 2, 180000, 3, '2025-03-01', '2025-05-31', 'activo', 'Acuerdo transporte y actividades'),
(1, 1, 4, 4, 350000, 2, '2025-03-05', '2025-04-30', 'activo', 'Plan especial marzo'),
(2, 3, 9, 9, 190000, 2, '2025-03-15', '2025-05-15', 'activo', 'Recaudo pensión parcial'),
(1, 1, 16, 16, 350000, 3, '2024-10-10', '2025-01-10', 'cerrado', 'Acuerdo matrícula inicial');

INSERT INTO cuota_acuerdo (id_acuerdo, numero_cuota, fecha_pago, valor_cuota, estado) VALUES
(1, 1, '2025-03-20', 60000, 'pagada'),
(1, 2, '2025-04-20', 60000, 'pendiente'),
(1, 3, '2025-05-20', 60000, 'pendiente'),
(2, 1, '2025-03-25', 175000, 'pagada'),
(2, 2, '2025-04-25', 175000, 'pendiente'),
(3, 1, '2025-03-30', 95000, 'pendiente'),
(3, 2, '2025-04-30', 95000, 'pendiente'),
(4, 1, '2024-11-10', 120000, 'pagada'),
(4, 2, '2024-12-10', 120000, 'pagada'),
(4, 3, '2025-01-10', 110000, 'pagada');

INSERT INTO comunicacion (id_colegio, id_sede, id_responsable, id_estudiante, tipo, canal, asunto, mensaje, resultado, fecha_envio, usuario_registro) VALUES
(1, 1, 1, 1, 'recordatorio', 'whatsapp', 'Pago pendiente enero', 'Estimado responsable, recuerde el pago de enero.', 'Enviado', '2025-01-20 09:00:00', 2),
(1, 2, 2, 2, 'acuerdo', 'email', 'Detalle acuerdo transporte', 'Adjuntamos detalle del acuerdo.', 'Aceptado', '2025-02-12 10:30:00', 4),
(1, 1, 4, 4, 'seguimiento', 'email', 'Estado acuerdo marzo', 'Se comparte plan de pagos actualizado.', 'Enviado', '2025-03-12 08:30:00', 4),
(1, 2, 2, 2, 'recordatorio', 'llamada', 'Cobro transporte', 'Se realizó llamada de seguimiento.', 'Contestó', '2025-03-05 15:10:00', 8),
(1, 2, 6, 6, 'recordatorio', 'sms', 'Saldo diciembre', 'Mensaje SMS enviado.', 'Enviado', '2024-12-18 09:05:00', 9),
(2, 3, 9, 9, 'seguimiento', 'whatsapp', 'Acuerdo pensión marzo', 'Se comparte estado de cuotas.', 'Enviado', '2025-03-22 10:45:00', 10),
(2, 4, 10, 10, 'recordatorio', 'email', 'Plan alimentación', 'Correo recordatorio enviado.', 'Leído', '2024-12-12 11:20:00', 7),
(2, 4, 11, 11, 'recordatorio', 'whatsapp', 'Transporte marzo', 'Mensaje de seguimiento.', 'Enviado', '2025-03-18 07:50:00', 7),
(1, 1, 16, 16, 'bienvenida', 'email', 'Inicio año', 'Bienvenida a la familia Romero.', 'Leído', '2024-10-05 09:00:00', 2),
(2, 3, 18, 18, 'recordatorio', 'sms', 'Pago noviembre', 'Mensaje automatizado.', 'Entregado', '2024-11-25 12:15:00', 3);

INSERT INTO carga_masiva (id_colegio, id_sede, tipo_archivo, archivo_original, archivo_procesado, total_registros, total_errores, resultado, mensaje, usuario_registro, fecha_registro) VALUES
(1, 1, 'estudiantes', 'cargue_octubre.xlsx', 'cargue_octubre_procesado.xlsx', 120, 3, 'parcial', 'Inconsistencias en columnas opcionales', 2, '2024-10-15 09:30:00'),
(1, 2, 'deudas', 'cargue_norte_noviembre.xlsx', 'cargue_norte_noviembre.xlsx', 80, 0, 'exitoso', 'Importación completada sin novedades', 5, '2024-11-28 14:10:00'),
(2, 3, 'pagos', 'recaudos_principal_marzo.xlsx', 'recaudos_principal_marzo.xlsx', 65, 1, 'parcial', 'Se omitieron registros duplicados', 10, '2025-03-21 16:45:00');

INSERT INTO parametros_sistema (clave, valor, descripcion, id_colegio) VALUES
('color_primario', '#1658a0', 'Color principal de la interfaz', 1),
('dias_recordatorio', '3', 'Enviar recordatorio 3 días antes del vencimiento', 1),
('dias_recordatorio', '4', 'Recordatorio para sedes rurales', 2);

INSERT INTO auditoria_usuario (id_usuario, id_colegio, id_sede, modulo, accion, detalle, ip) VALUES
(1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión administrador global', '127.0.0.1'),
(2, 1, NULL, 'responsables', 'crear', 'Registro de responsable María Rodríguez', '127.0.0.1'),
(4, 1, 1, 'deudas', 'actualizar', 'Ajuste de saldo para estudiante SJ-052', '127.0.0.1'),
(10, 2, 3, 'pagos', 'crear', 'Registro de pago parcial Samuel Campos', '127.0.0.1');
