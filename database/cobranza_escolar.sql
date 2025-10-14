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
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

INSERT INTO usuario (id_colegio, id_sede, nombre_completo, email, usuario, password_hash, rol) VALUES
(NULL, NULL, 'Administrador Global', 'admin@demo.com', 'admin', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_global'),
(1, 1, 'Coordinador San José', 'admin1@sanjose.edu', 'admin1', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio'),
(2, 3, 'Coordinador Nuestra Señora', 'admin2@nuestraseñora.edu', 'admin2', '$2y$12$hHXFsDlS0/2j.nGkQB.i3OuuiKSSoIAdT/uyEyZ3ThjW3Xbkfnvhq', 'admin_colegio'),
(1, 2, 'Agente de Cartera', 'agente1@sanjose.edu', 'agente1', '$2y$12$q1rtTLVlzStGU1IZKUC7F.t/M2.na5ZuiOl5J83kjAuylBxIwUekG', 'agente');

INSERT INTO responsable_financiero (id_colegio, id_sede, nombre_completo, tipo_documento, numero_documento, telefono, correo, direccion) VALUES
(1, 1, 'María Rodríguez', 'CC', '10203040', '3000000000', 'maria@sanjose.edu', 'Cra 12 #23-45'),
(1, 2, 'Juan García', 'CC', '11223344', '3000000001', 'juan@sanjose.edu', 'Av 45 #12-09'),
(2, 3, 'Ana López', 'CC', '55667788', '3100000002', 'ana@nuestraseñora.edu', 'Calle 50 #20-10');

INSERT INTO estudiante (id_colegio, id_sede, id_responsable, codigo_estudiante, nombre_completo, grado, curso) VALUES
(1, 1, 1, 'SJ-001', 'Carlos Rodríguez', '5°', '5A'),
(1, 2, 2, 'SJ-045', 'Laura García', '8°', '8B'),
(2, 3, 3, 'NS-020', 'Mateo López', '6°', '6A');

INSERT INTO concepto_deuda (id_colegio, nombre, descripcion, tipo, valor_base) VALUES
(1, 'Mensualidad', 'Mensualidad año lectivo', 'recurrente', 350000),
(1, 'Transporte', 'Servicio de ruta escolar', 'servicio', 120000),
(2, 'Matrícula', 'Matrícula anual', 'único', 450000);

INSERT INTO periodo (id_colegio, nombre, fecha_inicio, fecha_fin) VALUES
(1, '2025-01', '2025-01-01', '2025-01-31'),
(1, '2025-02', '2025-02-01', '2025-02-28'),
(2, '2025-01', '2025-01-01', '2025-01-31');

INSERT INTO deuda (id_colegio, id_sede, id_estudiante, id_periodo, id_concepto, fecha_generacion, valor_inicial, saldo_actual, estado, fecha_vencimiento)
VALUES
(1, 1, 1, 1, 1, '2025-01-05', 350000, 350000, 'pendiente', '2025-01-30'),
(1, 2, 2, 2, 2, '2025-02-05', 120000, 60000, 'en_acuerdo', '2025-02-28'),
(2, 3, 3, 3, 3, '2025-01-10', 450000, 0, 'pagado', '2025-01-31');

INSERT INTO registro_pago (id_colegio, id_sede, id_estudiante, fecha_pago, valor_total, metodo_pago, referencia)
VALUES
(1, 2, 2, '2025-02-20', 60000, 'transferencia', 'TRX-9087'),
(2, 3, 3, '2025-01-20', 450000, 'efectivo', 'CAJA-1123');

INSERT INTO acuerdo_pago (id_colegio, id_sede, id_responsable, id_estudiante, monto_total, cuotas, fecha_inicio, fecha_fin, estado, observaciones)
VALUES
(1, 2, 2, 2, 120000, 3, '2025-02-10', '2025-04-30', 'activo', 'Acuerdo por transporte febrero');

INSERT INTO cuota_acuerdo (id_acuerdo, numero_cuota, fecha_pago, valor_cuota, estado)
VALUES
(1, 1, '2025-02-25', 40000, 'pagada'),
(1, 2, '2025-03-25', 40000, 'pendiente'),
(1, 3, '2025-04-25', 40000, 'pendiente');

INSERT INTO comunicacion (id_colegio, id_sede, id_responsable, id_estudiante, tipo, canal, asunto, mensaje, resultado, fecha_envio, usuario_registro)
VALUES
(1, 1, 1, 1, 'recordatorio', 'whatsapp', 'Pago pendiente enero', 'Estimado responsable, recuerde el pago de enero.', 'Enviado', '2025-01-20 09:00:00', 1),
(1, 2, 2, 2, 'acuerdo', 'email', 'Detalle acuerdo transporte', 'Adjuntamos detalle del acuerdo.', 'Aceptado', '2025-02-12 10:30:00', 2);

INSERT INTO parametros_sistema (clave, valor, descripcion, id_colegio)
VALUES
('color_primario', '#1658a0', 'Color principal de la interfaz', 1),
('dias_recordatorio', '3', 'Enviar recordatorio 3 días antes del vencimiento', 1);

INSERT INTO auditoria_usuario (id_usuario, id_colegio, id_sede, modulo, accion, detalle, ip)
VALUES
(1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión administrador global', '127.0.0.1'),
(2, 1, 1, 'responsables', 'crear', 'Creación responsable María', '127.0.0.1');
