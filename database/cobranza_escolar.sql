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
    id_plantilla INT NULL,
    tipo VARCHAR(60) NULL,
    canal VARCHAR(60) NOT NULL,
    asunto VARCHAR(150) NULL,
    mensaje TEXT NOT NULL,
    resultado VARCHAR(255) NULL,
    fecha_envio DATETIME NOT NULL,
    usuario_registro INT NOT NULL,
    estado_envio ENUM('pendiente','enviado','error','registrado') DEFAULT 'pendiente',
    detalle_envio VARCHAR(255) NULL,
    eliminado TINYINT(1) DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla plantilla_comunicacion
CREATE TABLE plantilla_comunicacion (
    id_plantilla INT AUTO_INCREMENT PRIMARY KEY,
    id_colegio INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    canal ENUM('email','whatsapp','sms','llamada') NOT NULL DEFAULT 'email',
    descripcion VARCHAR(255) NULL,
    asunto_default VARCHAR(180) NULL,
    cuerpo_html MEDIUMTEXT NOT NULL,
    variables TEXT NULL,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    eliminado TINYINT(1) DEFAULT 0,
    creado_por INT NULL,
    actualizado_por INT NULL,
    fecha_actualizacion DATETIME NULL,
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
INSERT INTO colegio (nombre, nit, direccion, telefono, correo, logo, estado, eliminado) VALUES
('Colegio Bilingüe Campestre Principado de Mónaco', '901999888-1', 'Km 4 vía Cota-Chía', '6011234567', 'contacto@principadomonaco.edu.co', 'logos/principado-monaco.png', 'activo', 0);

INSERT INTO sede (id_colegio, nombre, direccion, telefono, correo, estado, eliminado) VALUES
(1, 'Bogotá', 'Cra 12 #145-30, Bogotá', '6015102020', 'bogota@principadomonaco.edu.co', 'activo', 0),
(1, 'Cota', 'Km 4 vía Cota-Chía, Cota', '6015103030', 'cota@principadomonaco.edu.co', 'activo', 0);

INSERT INTO usuario (id_colegio, id_sede, nombre_completo, email, usuario, password_hash, rol, estado) VALUES
(NULL, NULL, 'Administrador General', 'admin@principadomonaco.edu.co', 'admin', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'admin_global', 'activo'),
(1, NULL, 'Coordinadora Financiera', 'financiera@principadomonaco.edu.co', 'admin_colegio', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'admin_colegio', 'activo'),
(1, 1, 'Agente Cartera Bogotá', 'cartera.bogota@principadomonaco.edu.co', 'agente_bogota', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'agente', 'activo');

INSERT INTO modulo_sistema (codigo, nombre, descripcion) VALUES
('cobranzas', 'Gestión de cobranzas', 'Seguimiento integral de cartera y comunicaciones'),
('administracion', 'Administración institucional', 'Gestión de sedes, usuarios y catálogos'),
('parametrizacion', 'Parametrización avanzada', 'Configuraciones, plantillas y parámetros del sistema');

INSERT INTO usuario_colegio (id_usuario, id_colegio) VALUES
(1, 1),
(2, 1),
(3, 1);

INSERT INTO usuario_sede (id_usuario, id_sede) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1);

INSERT INTO usuario_modulo (id_usuario, id_modulo) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1);

INSERT INTO configuracion_colegio (id_colegio, smtp_host, smtp_puerto, smtp_usuario, smtp_password, whatsapp_api_key, whatsapp_endpoint, sms_api_key, sms_endpoint, logo_path, actualizado_por, fecha_actualizacion) VALUES
(1, 'smtp.gmail.co', '587', 'carlos.quinones@lm-technology.com.co', 'Carlitos 2025*', NULL, NULL, NULL, NULL, 'logos/principado-monaco.png', 1, CURRENT_TIMESTAMP);

INSERT INTO responsable_financiero (id_colegio, id_sede, nombre_completo, tipo_documento, numero_documento, telefono, correo, direccion, estado, eliminado) VALUES
(1, 1, 'Villalobos Munoz Fernando', 'CC', '80073011', '+57 308 007 3011', 'villalobos.munoz.fernando@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Castillo Castro Jozep Evans', 'CC', '79955368', '+57 307 995 5368', 'castillo.castro.jozep.evans@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Pineda Pachon Julio Cesar', 'CC', '79335279', '+57 307 933 5279', 'pineda.pachon.julio.cesar@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Medina Arevalo Angelica', 'CC', '60288036', '+57 306 028 8036', 'medina.arevalo.angelica@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Maneiro Fermin Manuel Valdemar', 'CC', '5709969', '+57 300 570 9969', 'maneiro.fermin.manuel.valdemar@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Cobos Arevalo Jenny Juliana', 'CC', '53118047', '+57 305 311 8047', 'cobos.arevalo.jenny.juliana@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Lima Gonzalez Carolina', 'CC', '52715742', '+57 305 271 5742', 'lima.gonzalez.carolina@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Matiz Perez Adriana Patricia', 'CC', '52493163', '+57 305 249 3163', 'matiz.perez.adriana.patricia@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Balcazar Neira Gloria Amanda', 'CC', '41060961', '+57 304 106 0961', 'balcazar.neira.gloria.amanda@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Ruiz Jose Hector', 'CC', '17006214', '+57 301 700 6214', 'ruiz.jose.hector@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Duarte Hinojosa Naisir', 'CC', '12644133', '+57 301 264 4133', 'duarte.hinojosa.naisir@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 1, 'Toro Aristizabal Navi Yuliana', 'CC', '1128434202', '+57 312 843 4202', 'toro.aristizabal.navi.yuliana@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0),
(1, 2, 'Leon Pinzon Danna Catalina', 'CC', '1121863888', '+57 312 186 3888', 'leon.pinzon.danna.catalina@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Marulanda Vargas Claudia Rocio', 'CC', '1111198791', '+57 311 119 8791', 'marulanda.vargas.claudia.rocio@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Franco Berrocal Yonatan David', 'CC', '1067910830', '+57 306 791 0830', 'franco.berrocal.yonatan.david@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Sanchez Rios Luz Argenis', 'CC', '1033750035', '+57 303 375 0035', 'sanchez.rios.luz.argenis@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Heredia Enciso Paula Carolina', 'CC', '1032448391', '+57 303 244 8391', 'heredia.enciso.paula.carolina@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Pineda Pardo Fabio Andres', 'CC', '1022339392', '+57 302 233 9392', 'pineda.pardo.fabio.andres@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Avendano Maldonado David Andres', 'CC', '1020735931', '+57 302 073 5931', 'avendano.maldonado.david.andres@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Leon Valencia Kelly Johanna', 'CC', '1015431483', '+57 301 543 1483', 'leon.valencia.kelly.johanna@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Gomez Julian Felipe', 'CC', '1015408023', '+57 301 540 8023', 'gomez.julian.felipe@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0),
(1, 2, 'Cordoba Palacios Ana Dayana', 'CC', '1003968947', '+57 300 396 8947', 'cordoba.palacios.ana.dayana@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0);

INSERT INTO estudiante (id_colegio, id_sede, id_responsable, codigo_estudiante, nombre_completo, grado, curso, estado, eliminado) VALUES
(1, 1, 1, '10176', 'Villalobos Chavista Samuel', 'Grado 1', '1B', 'activo', 0),
(1, 1, 2, '10130', 'Castillo Figueroa Juana Valeria', 'Grado 5', '5B', 'activo', 0),
(1, 1, 3, '10119', 'Pineda Blanco Juan Esteban', 'Grado 5', '5A', 'activo', 0),
(1, 1, 4, '10099', 'Garcia Medina Geronimo', 'Grado 4', '4B', 'activo', 0),
(1, 1, 5, '10102', 'Maneiro Bolivar Yoneiker Alejandro', 'Grado 4', '4A', 'activo', 0),
(1, 1, 6, '10179', 'Forero Cobos Antonio', 'Grado 3', '3A', 'activo', 0),
(1, 1, 7, '10047', 'Millan Lima Matias', 'Grado 2', '2A', 'activo', 0),
(1, 1, 8, '10214', 'Guevara Matiz Gabriel Ricardo', 'Grado 1', '1A', 'activo', 0),
(1, 1, 9, '10050', 'Arbelaez Balcazar Johan Mauricio', 'Grado 3', '3B', 'retirado', 0),
(1, 1, 10, '10195', 'Ruiz Ruiz Daniel Leonardo', 'Grado 4', '4B', 'activo', 0),
(1, 1, 11, '10215', 'Duarte Arino Maximo', 'Preescolar', 'JD', 'activo', 0),
(1, 1, 11, '10216', 'Duarte Arino Maximiliano', 'Grado 3', '3A', 'activo', 0),
(1, 1, 12, '10023', 'Toro Aristizabal Pablo Andres', 'Grado 1', '1A', 'activo', 0),
(1, 2, 13, '10237', 'Yousef Leon Shaker Salim', 'Preescolar', 'PJ', 'activo', 0),
(1, 2, 13, '10238', 'Yousef Leon Sami Zahid', 'Grado 5', '5A', 'activo', 0),
(1, 2, 14, '10086', 'Marulanda Vargas Juanita', 'Grado 4', '4A', 'activo', 0),
(1, 2, 15, '10211', 'Franco Jimenez Franz', 'Grado 3', '3A', 'activo', 0),
(1, 2, 16, '10235', 'Zamora Sanchez Luciana', 'Preescolar', 'TR', 'activo', 0),
(1, 2, 17, '10225', 'Lopez Heredia Johan Stephan', 'Preescolar', 'JD', 'activo', 0),
(1, 2, 17, '10224', 'Lopez Heredia Gabriel Mathias', 'Grado 2', '2A', 'activo', 0),
(1, 2, 18, '10120', 'Pineda Ruiz Matias', 'Grado 5', '5B', 'activo', 0),
(1, 2, 19, '10052', 'Avendano Medrano Jacobo', 'Grado 3', '3A', 'activo', 0),
(1, 2, 20, '10124', 'Tovar Leon Maria Alejandra', 'Grado 5', '5A', 'activo', 0),
(1, 2, 21, '10184', 'Gomez Gutierrez Salome', 'Grado 1', '1A', 'activo', 0),
(1, 2, 22, '10060', 'Gamboa Cordoba Julian Ameobi', 'Grado 3', '3A', 'activo', 0);

INSERT INTO concepto_deuda (id_colegio, nombre, descripcion, tipo, valor_base, estado, eliminado) VALUES
(1, 'Pensión escolar 2025', 'Valor referencial de la pensión anual', 'recurrente', 1250000, 'activo', 0),
(1, 'Servicios complementarios', 'Alimentación, transporte y actividades', 'servicio', 320000, 'activo', 0);

INSERT INTO periodo (id_colegio, nombre, fecha_inicio, fecha_fin, estado, eliminado) VALUES
(1, 'Cartera 2025', '2025-01-01', '2025-12-31', 'activo', 0),
(1, 'Junio 2025', '2025-06-01', '2025-06-30', 'activo', 0);

INSERT INTO deuda (id_colegio, id_sede, id_estudiante, id_periodo, id_concepto, fecha_generacion, valor_inicial, saldo_actual, estado, fecha_vencimiento, notas, eliminado) VALUES
(1, 1, 1, 1, 1, '2025-06-03', 975125.00, 975125.00, 'pendiente', '2025-07-03', 'Gestion: CONTACTO DIRECTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 1, 2, 1, 1, '2025-06-04', 1540250.00, 1540250.00, 'pendiente', '2025-07-04', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 1, 3, 1, 1, '2025-06-05', 2224250.00, 2224250.00, 'pendiente', '2025-07-05', 'Gestion: COMPROMISO DE PAGO. Seguimiento: COMPROMISO DE PAGO. Compromiso: SI', 0),
(1, 1, 4, 1, 1, '2025-06-06', 2379200.00, 2379200.00, 'pendiente', '2025-07-06', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 1, 5, 1, 1, '2025-06-07', 985600.00, 985600.00, 'pendiente', '2025-07-07', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 1, 6, 1, 1, '2025-06-08', 1179200.00, 1179200.00, 'pendiente', '2025-07-08', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 1, 7, 1, 1, '2025-06-09', 2259500.00, 2259500.00, 'pendiente', '2025-07-09', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 0),
(1, 1, 8, 1, 1, '2025-06-10', 3406560.00, 3406560.00, 'pendiente', '2025-07-10', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 1, 9, 1, 1, '2025-06-11', 2131000.00, 2131000.00, 'pendiente', '2025-07-11', 'Gestion: CONTACTO DIRECTO. Seguimiento: RETIRADO DEL COLEGIO. Compromiso: SI', 0),
(1, 1, 10, 1, 1, '2025-06-12', 1077800.00, 1077800.00, 'pendiente', '2025-07-12', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 1, 11, 1, 1, '2025-06-13', 1723000.00, 1723000.00, 'pendiente', '2025-07-13', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 1, 12, 1, 1, '2025-06-14', 1723000.00, 1723000.00, 'pendiente', '2025-07-14', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 1, 13, 1, 1, '2025-06-15', 953300.00, 953300.00, 'pendiente', '2025-07-15', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 2, 14, 1, 1, '2025-06-16', 1006500.00, 1006500.00, 'pendiente', '2025-07-16', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 15, 1, 1, '2025-06-17', 946650.00, 946650.00, 'pendiente', '2025-07-17', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 16, 1, 1, '2025-06-18', 5462199.00, 5462199.00, 'pendiente', '2025-07-18', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 17, 1, 1, '2025-06-19', 1723000.00, 1723000.00, 'pendiente', '2025-07-19', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 0),
(1, 2, 18, 1, 1, '2025-06-20', 1321100.00, 1321100.00, 'pendiente', '2025-07-20', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 19, 1, 1, '2025-06-21', 1721784.00, 1721784.00, 'pendiente', '2025-07-21', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 20, 1, 1, '2025-06-22', 2263160.00, 2263160.00, 'pendiente', '2025-07-22', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 21, 1, 1, '2025-06-03', 1280950.00, 1280950.00, 'pendiente', '2025-07-03', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 2, 22, 1, 1, '2025-06-04', 1278000.00, 1278000.00, 'pendiente', '2025-07-04', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 0),
(1, 2, 23, 1, 1, '2025-06-05', 3371983.00, 3371983.00, 'pendiente', '2025-07-05', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0),
(1, 2, 24, 1, 1, '2025-06-06', 5107797.00, 5107797.00, 'pendiente', '2025-07-06', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0),
(1, 2, 25, 1, 1, '2025-06-07', 4731950.00, 4731950.00, 'pendiente', '2025-07-07', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0);

INSERT INTO registro_pago (id_colegio, id_sede, id_estudiante, fecha_pago, valor_total, metodo_pago, referencia, ruta_soporte) VALUES
(1, 1, 1, '2025-06-18', 300000.00, 'transferencia', 'PM-20250618-01', 'uploads/soportes/PM-20250618-01.pdf'),
(1, 1, 6, '2025-06-19', 180000.00, 'transferencia', 'PM-20250619-06', 'uploads/soportes/PM-20250619-06.pdf'),
(1, 2, 14, '2025-06-21', 220000.00, 'tarjeta', 'PM-20250621-14', 'uploads/soportes/PM-20250621-14.pdf'),
(1, 2, 18, '2025-05-30', 150000.00, 'transferencia', 'PM-20250530-18', 'uploads/soportes/PM-20250530-18.pdf'),
(1, 2, 21, '2025-06-15', 210000.00, 'transferencia', 'PM-20250615-21', 'uploads/soportes/PM-20250615-21.pdf'),
(1, 2, 22, '2025-06-16', 120000.00, 'transferencia', 'PM-20250616-22', 'uploads/soportes/PM-20250616-22.pdf'),
(1, 2, 24, '2025-06-22', 320000.00, 'transferencia', 'PM-20250622-24', 'uploads/soportes/PM-20250622-24.pdf'),
(1, 2, 25, '2025-06-23', 250000.00, 'transferencia', 'PM-20250623-25', 'uploads/soportes/PM-20250623-25.pdf');

INSERT INTO acuerdo_pago (id_colegio, id_sede, id_responsable, id_estudiante, monto_total, cuotas, fecha_inicio, fecha_fin, estado, observaciones) VALUES
(1, 1, 2, 2, 540000.00, 3, '2025-07-01', '2025-09-30', 'activo', 'Plan de normalización mensualidad 2025'),
(1, 2, 13, 15, 460000.00, 2, '2025-07-05', '2025-08-31', 'activo', 'Compromiso transporte y alimentación');

INSERT INTO cuota_acuerdo (id_acuerdo, numero_cuota, fecha_pago, valor_cuota, estado) VALUES
(1, 1, '2025-07-15', 180000.00, 'pendiente'),
(1, 2, '2025-08-15', 180000.00, 'pendiente'),
(1, 3, '2025-09-15', 180000.00, 'pendiente'),
(2, 1, '2025-07-20', 230000.00, 'pendiente'),
(2, 2, '2025-08-20', 230000.00, 'pendiente');

INSERT INTO comunicacion (id_colegio, id_sede, id_responsable, id_estudiante, id_plantilla, tipo, canal, asunto, mensaje, resultado, fecha_envio, usuario_registro, estado_envio, detalle_envio, eliminado) VALUES
(1, 1, 1, 1, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-03 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 2, 2, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-04 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 3, 3, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: COMPROMISO DE PAGO. Seguimiento: COMPROMISO DE PAGO. Compromiso: SI', 'COMPROMISO DE PAGO', '2025-06-05 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 4, 4, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-06 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 5, 5, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-07 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 6, 6, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-08 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 7, 7, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-09 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 8, 8, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-10 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 9, 9, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: RETIRADO DEL COLEGIO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-11 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 10, 10, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-12 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 11, 11, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-13 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 11, 12, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-14 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 1, 12, 13, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-15 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 13, 14, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-16 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 13, 15, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-17 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 14, 16, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-18 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 15, 17, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-19 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 16, 18, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-20 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 17, 19, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-21 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 17, 20, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-22 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 18, 21, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-03 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 19, 22, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-04 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 20, 23, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-05 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 21, 24, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-06 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0),
(1, 2, 22, 25, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-07 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0);

INSERT INTO plantilla_comunicacion (id_colegio, nombre, canal, descripcion, asunto_default, cuerpo_html, variables, estado, eliminado, creado_por) VALUES
(1, 'Recordatorio de pago pendiente', 'email', 'Correo formal recordando el saldo pendiente y fecha de vencimiento.', 'Recordatorio de pago — {{estudiante_nombre}}', '<p>Estimado(a) {{responsable_nombre}},</p>
        <p>De manera atenta le informamos que el saldo pendiente de {{estudiante_nombre}} corresponde a <strong>{{saldo_pendiente}}</strong> con vencimiento el <strong>{{fecha_vencimiento}}</strong>.</p>
        <p>Le invitamos a realizar el pago oportunamente para mantener los beneficios académicos activos. Puede comunicarse con nosotros al {{telefono_contacto}} para ampliar la información.</p>
        <p>Atentamente,<br><strong>{{colegio_nombre}}</strong><br>Sede {{sede_nombre}}</p>', 'responsable_nombre,estudiante_nombre,saldo_pendiente,fecha_vencimiento,colegio_nombre,sede_nombre,telefono_contacto', 'activo', 0, 2),
(1, 'Mensaje corto WhatsApp', 'whatsapp', 'Plantilla corta para contacto inmediato por WhatsApp.', NULL, 'Hola {{responsable_nombre}}, te contactamos de {{colegio_nombre}}. El saldo de {{estudiante_nombre}} es {{saldo_pendiente}} con vencimiento {{fecha_vencimiento}}. ¿Te apoyamos con algún detalle?', 'responsable_nombre,estudiante_nombre,saldo_pendiente,fecha_vencimiento', 'activo', 0, 2),
(1, 'Seguimiento compromiso de pago', 'email', 'Correo de seguimiento cuando existe un compromiso registrado.', 'Seguimiento compromiso {{estudiante_nombre}}', '<p>Buen día {{responsable_nombre}},</p>
        <p>Confirmamos el compromiso de pago asociado a {{estudiante_nombre}} con fecha objetivo {{fecha_vencimiento}}. Este mensaje busca acompañar el cumplimiento del acuerdo registrado.</p>
        <p>Si requieres modificar la fecha o el valor acordado, responde a este correo o comunícate con el área financiera.</p>
        <p>Equipo de cartera — {{colegio_nombre}} ({{sede_nombre}})</p>', 'responsable_nombre,estudiante_nombre,fecha_vencimiento,colegio_nombre,sede_nombre', 'activo', 0, 2);

INSERT INTO carga_masiva (id_colegio, id_sede, tipo_archivo, archivo_original, archivo_procesado, total_registros, total_errores, resultado, mensaje, usuario_registro, fecha_registro) VALUES
(1, 1, 'deudas', 'cargue_octubre_2024.xlsx', 'cargue_octubre_2024.xlsx', 25, 1, 'exitoso', 'Base inicial octubre integrada', 2, '2024-10-02 08:45:00'),
(1, 1, 'deudas', 'cargue_noviembre_2024.xlsx', 'cargue_noviembre_2024.xlsx', 25, 0, 'exitoso', 'Actualización mensual noviembre', 2, '2024-11-01 09:05:00'),
(1, 2, 'deudas', 'cargue_diciembre_2024.xlsx', 'cargue_diciembre_2024.xlsx', 27, 2, 'parcial', 'Se detectaron responsables sin correo', 3, '2024-12-02 08:55:00'),
(1, 2, 'deudas', 'cargue_enero_2025.xlsx', 'cargue_enero_2025.xlsx', 27, 0, 'exitoso', 'Base enero consolidada', 2, '2025-01-06 08:50:00'),
(1, 2, 'deudas', 'cargue_junio_2025.xlsx', 'cargue_junio_2025.xlsx', 25, 0, 'exitoso', 'Cartera junio cargada para gestión', 3, '2025-06-01 07:40:00');

INSERT INTO parametros_sistema (clave, valor, descripcion, id_colegio) VALUES
('color_primario', '#0f325d', 'Color institucional principal', 1),
('color_secundario', '#f5a524', 'Color de énfasis para alertas', 1),
('dias_recordatorio', '5', 'Días antes del vencimiento para alertar', 1),
('correo_respuesta', 'recaudos@principadomonaco.edu.co', 'Correo remitente para notificaciones', 1);

INSERT INTO auditoria_usuario (id_usuario, id_colegio, id_sede, modulo, accion, detalle, ip) VALUES
(1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión administrador general', '127.0.0.1'),
(2, 1, NULL, 'configuracion', 'actualizar', 'Actualización credenciales SMTP', '127.0.0.1'),
(3, 1, 1, 'comunicaciones', 'registrar', 'Envió correo recordatorio cartera', '127.0.0.1');
