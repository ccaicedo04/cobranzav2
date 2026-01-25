-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2025 a las 13:19:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cobranza_escolar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acuerdo_pago`
--

CREATE TABLE `acuerdo_pago` (
  `id_acuerdo` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','cerrado','incumplido') DEFAULT 'activo',
  `observaciones` text DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `acuerdo_pago`
--

INSERT INTO `acuerdo_pago` (`id_acuerdo`, `id_colegio`, `id_sede`, `id_responsable`, `id_estudiante`, `monto_total`, `cuotas`, `fecha_inicio`, `fecha_fin`, `estado`, `observaciones`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 2, 2, 540000.00, 3, '2025-07-01', '2025-09-30', 'activo', 'Plan de normalización mensualidad 2025', 0, '2025-11-24 00:43:29'),
(2, 1, 2, 13, 15, 460000.00, 2, '2025-07-05', '2025-08-31', 'activo', 'Compromiso transporte y alimentación', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_usuario`
--

CREATE TABLE `auditoria_usuario` (
  `id_auditoria` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_colegio` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `modulo` varchar(120) NOT NULL,
  `accion` varchar(60) NOT NULL,
  `detalle` text DEFAULT NULL,
  `ip` varchar(60) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria_usuario`
--

INSERT INTO `auditoria_usuario` (`id_auditoria`, `id_usuario`, `id_colegio`, `id_sede`, `modulo`, `accion`, `detalle`, `ip`, `fecha_registro`) VALUES
(1, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión administrador general', '127.0.0.1', '2025-11-24 00:43:29'),
(2, 2, 1, NULL, 'configuracion', 'actualizar', 'Actualización credenciales SMTP', '127.0.0.1', '2025-11-24 00:43:29'),
(3, 3, 1, 1, 'comunicaciones', 'registrar', 'Envió correo recordatorio cartera', '127.0.0.1', '2025-11-24 00:43:29'),
(4, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 00:43:44'),
(5, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:44:57'),
(6, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:45:41'),
(7, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:46:32'),
(8, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión EMAIL (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:47:18'),
(9, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión EMAIL (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:48:56'),
(10, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión EMAIL (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 00:55:53'),
(11, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:01:11'),
(12, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:03:01'),
(13, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:06:41'),
(14, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:09:19'),
(15, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:09:37'),
(16, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:09:50'),
(17, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:10:04'),
(18, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:10:20'),
(19, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:10:38'),
(20, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:10:47'),
(21, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:10:54'),
(22, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:14:40'),
(23, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:20:56'),
(24, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:22:46'),
(25, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:22:57'),
(26, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:24:07'),
(27, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 01:24:19'),
(28, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 01:24:28'),
(29, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:24:47'),
(30, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:24:57'),
(31, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:25:23'),
(32, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:27:06'),
(33, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:27:36'),
(34, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:27:49'),
(35, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Castillo Castro Jozep Evans', '::1', '2025-11-24 01:28:42'),
(36, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:28:54'),
(37, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:30:32'),
(38, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:30:43'),
(39, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:33:47'),
(40, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:34:14'),
(41, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:36:16'),
(42, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:39:09'),
(43, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:39:22'),
(44, 1, 1, 1, 'responsables', 'actualizar', 'Actualización de responsable: Castillo Castro Jozep Evans', '::1', '2025-11-24 01:41:09'),
(45, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión EMAIL (enviado) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:41:24'),
(46, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:46:09'),
(47, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:48:36'),
(48, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:49:33'),
(49, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:50:44'),
(50, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 01:53:04'),
(51, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:53:26'),
(52, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (error) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:54:20'),
(53, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (enviado) para Cobos Arevalo Jenny Juliana', '::1', '2025-11-24 01:57:03'),
(54, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (enviado) para Castillo Castro Jozep Evans', '::1', '2025-11-24 01:59:20'),
(55, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 02:21:05'),
(56, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 02:21:10'),
(57, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 02:33:58'),
(58, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 02:34:07'),
(59, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 02:34:49'),
(60, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 02:37:21'),
(61, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 02:37:28'),
(62, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión LLAMADA (registrado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 02:40:25'),
(63, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 02:47:00'),
(64, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 02:47:05'),
(65, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 03:32:55'),
(66, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 03:33:02'),
(67, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 03:33:45'),
(68, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión WHATSAPP (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 03:34:32'),
(69, 1, 1, 1, 'comunicaciones', 'registrar', 'Gestión SMS (enviado) para Balcazar Neira Gloria Amanda', '::1', '2025-11-24 03:34:48'),
(70, 1, 1, 1, 'autenticacion', 'logout', 'Cierre de sesión', '::1', '2025-11-24 03:48:59'),
(71, 1, NULL, NULL, 'autenticacion', 'login', 'Inicio de sesión exitoso', '::1', '2025-11-24 03:59:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carga_masiva`
--

CREATE TABLE `carga_masiva` (
  `id_carga` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `tipo_archivo` varchar(60) NOT NULL,
  `archivo_original` varchar(255) NOT NULL,
  `archivo_procesado` varchar(255) DEFAULT NULL,
  `total_registros` int(11) DEFAULT 0,
  `total_errores` int(11) DEFAULT 0,
  `resultado` varchar(60) NOT NULL,
  `mensaje` varchar(255) DEFAULT NULL,
  `usuario_registro` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carga_masiva`
--

INSERT INTO `carga_masiva` (`id_carga`, `id_colegio`, `id_sede`, `tipo_archivo`, `archivo_original`, `archivo_procesado`, `total_registros`, `total_errores`, `resultado`, `mensaje`, `usuario_registro`, `fecha_registro`) VALUES
(1, 1, 1, 'deudas', 'cargue_octubre_2024.xlsx', 'cargue_octubre_2024.xlsx', 25, 1, 'exitoso', 'Base inicial octubre integrada', 2, '2024-10-02 13:45:00'),
(2, 1, 1, 'deudas', 'cargue_noviembre_2024.xlsx', 'cargue_noviembre_2024.xlsx', 25, 0, 'exitoso', 'Actualización mensual noviembre', 2, '2024-11-01 14:05:00'),
(3, 1, 2, 'deudas', 'cargue_diciembre_2024.xlsx', 'cargue_diciembre_2024.xlsx', 27, 2, 'parcial', 'Se detectaron responsables sin correo', 3, '2024-12-02 13:55:00'),
(4, 1, 2, 'deudas', 'cargue_enero_2025.xlsx', 'cargue_enero_2025.xlsx', 27, 0, 'exitoso', 'Base enero consolidada', 2, '2025-01-06 13:50:00'),
(5, 1, 2, 'deudas', 'cargue_junio_2025.xlsx', 'cargue_junio_2025.xlsx', 25, 0, 'exitoso', 'Cartera junio cargada para gestión', 3, '2025-06-01 12:40:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colegio`
--

CREATE TABLE `colegio` (
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `nit` varchar(32) NOT NULL,
  `direccion` varchar(180) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `colegio`
--

INSERT INTO `colegio` (`id_colegio`, `nombre`, `nit`, `direccion`, `telefono`, `correo`, `logo`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 'Colegio Bilingüe Campestre Principado de Mónaco', '901999888-1', 'Km 4 vía Cota-Chía', '6011234567', 'contacto@principadomonaco.edu.co', 'logos/principado-monaco.png', 'activo', 0, '2025-11-24 00:43:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicacion`
--

CREATE TABLE `comunicacion` (
  `id_comunicacion` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_plantilla` int(11) DEFAULT NULL,
  `tipo` varchar(60) DEFAULT NULL,
  `canal` varchar(60) NOT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `resultado` varchar(255) DEFAULT NULL,
  `fecha_envio` datetime NOT NULL,
  `usuario_registro` int(11) NOT NULL,
  `estado_envio` enum('pendiente','enviado','error','registrado') DEFAULT 'pendiente',
  `detalle_envio` varchar(255) DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comunicacion`
--

INSERT INTO `comunicacion` (`id_comunicacion`, `id_colegio`, `id_sede`, `id_responsable`, `id_estudiante`, `id_plantilla`, `tipo`, `canal`, `asunto`, `mensaje`, `resultado`, `fecha_envio`, `usuario_registro`, `estado_envio`, `detalle_envio`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 1, 1, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-03 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(2, 1, 1, 2, 2, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-04 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(3, 1, 1, 3, 3, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: COMPROMISO DE PAGO. Seguimiento: COMPROMISO DE PAGO. Compromiso: SI', 'COMPROMISO DE PAGO', '2025-06-05 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(4, 1, 1, 4, 4, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-06 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(5, 1, 1, 5, 5, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-07 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(6, 1, 1, 6, 6, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-08 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(7, 1, 1, 7, 7, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-09 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(8, 1, 1, 8, 8, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-10 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(9, 1, 1, 9, 9, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: RETIRADO DEL COLEGIO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-11 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(10, 1, 1, 10, 10, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-12 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(11, 1, 1, 11, 11, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-13 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(12, 1, 1, 11, 12, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-14 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(13, 1, 1, 12, 13, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-15 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(14, 1, 2, 13, 14, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-16 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(15, 1, 2, 13, 15, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-17 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(16, 1, 2, 14, 16, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-18 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(17, 1, 2, 15, 17, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-19 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(18, 1, 2, 16, 18, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-20 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(19, 1, 2, 17, 19, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-21 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(20, 1, 2, 17, 20, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-22 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(21, 1, 2, 18, 21, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-03 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(22, 1, 2, 19, 22, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 'CONTACTO DIRECTO', '2025-06-04 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(23, 1, 2, 20, 23, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 'ESPERA DE RESPUESTA', '2025-06-05 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(24, 1, 2, 21, 24, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-06 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(25, 1, 2, 22, 25, 1, 'seguimiento', 'email', 'Seguimiento cartera Jun 2025', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 'EN SEGUIMIENTO', '2025-06-07 08:30:00', 3, 'enviado', 'Migrado dataset Principado', 0, '2025-11-24 00:43:29'),
(26, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 19:45:41', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 401', 0, '2025-11-24 00:45:41'),
(27, 1, 1, 9, 9, 3, 'gestion', 'email', 'Seguimiento compromiso Arbelaez Balcazar Johan Mauricio', '<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Colegio Bilingüe Campestre Principado de Mónaco</title></head><body style=\"margin:0;background:#e2e8f0;font-family:Inter,Arial,sans-serif;\"><div style=\"max-width:720px;margin:32px auto;background:#ffffff;border-radius:18px;box-shadow:0 24px 60px rgba(15,47,90,.18);overflow:hidden;\"><div style=\"background:#102f5a;color:#ffffff;padding:24px 32px;border-radius:18px 18px 0 0;\"><img src=\"/cobranzav2/public/logos/principado-monaco.png\" alt=\"Colegio Bilingüe Campestre Principado de Mónaco\" style=\"height:48px;margin-bottom:12px;display:block;\"><div style=\"font-size:20px;font-weight:700;letter-spacing:.3px;\">Colegio Bilingüe Campestre Principado de Mónaco</div><div style=\"font-size:14px;opacity:.85;\">Sede Bogotá</div></div><div style=\"padding:32px;font-size:15px;line-height:1.6;color:#1f2937;\"><p>Buen día Balcazar Neira Gloria Amanda,</p>\r\n        <p>Confirmamos el compromiso de pago asociado a Arbelaez Balcazar Johan Mauricio con fecha objetivo 2025-07-11. Este mensaje busca acompañar el cumplimiento del acuerdo registrado.</p>\r\n        <p>Si requieres modificar la fecha o el valor acordado, responde a este correo o comunícate con el área financiera.</p>\r\n        <p>Equipo de cartera — Colegio Bilingüe Campestre Principado de Mónaco (Bogotá)</p></div><div style=\"background:#f1f5f9;padding:24px 32px;border-radius:0 0 18px 18px;font-size:13px;color:#475569;\"><div style=\"font-weight:600;margin-bottom:6px;\">Área de cartera — Colegio Bilingüe Campestre Principado de Mónaco</div><div>Dirección: Cra 12 #145-30, Bogotá</div><div>Teléfono: 6015102020</div><div>Correo: bogota@principadomonaco.edu.co</div><div style=\"margin-top:10px;font-size:12px;opacity:.7;\">Mensaje generado desde la plataforma de cobranzas.</div></div></div></body></html>', 'Error al enviar correo', '2025-11-23 19:47:18', 1, 'error', 'Error SMTP (535): 535-5.7.8 Username and Password not accepted. For more information, go to | 535 5.7.8  https://support.google.com/mail/?p=BadCredentials 00721157ae682-78a79925b99sm39515227b3.36 - gsmtp. Verifica el usuario y la contraseña o genera una c', 0, '2025-11-24 00:47:18'),
(28, 1, 1, 9, 9, 3, 'gestion', 'email', 'Seguimiento compromiso Arbelaez Balcazar Johan Mauricio', '<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Colegio Bilingüe Campestre Principado de Mónaco</title></head><body style=\"margin:0;background:#e2e8f0;font-family:Inter,Arial,sans-serif;\"><div style=\"max-width:720px;margin:32px auto;background:#ffffff;border-radius:18px;box-shadow:0 24px 60px rgba(15,47,90,.18);overflow:hidden;\"><div style=\"background:#102f5a;color:#ffffff;padding:24px 32px;border-radius:18px 18px 0 0;\"><img src=\"/cobranzav2/public/logos/principado-monaco.png\" alt=\"Colegio Bilingüe Campestre Principado de Mónaco\" style=\"height:48px;margin-bottom:12px;display:block;\"><div style=\"font-size:20px;font-weight:700;letter-spacing:.3px;\">Colegio Bilingüe Campestre Principado de Mónaco</div><div style=\"font-size:14px;opacity:.85;\">Sede Bogotá</div></div><div style=\"padding:32px;font-size:15px;line-height:1.6;color:#1f2937;\"><p>Buen día Balcazar Neira Gloria Amanda,</p>\r\n        <p>Confirmamos el compromiso de pago asociado a Arbelaez Balcazar Johan Mauricio con fecha objetivo 2025-07-11. Este mensaje busca acompañar el cumplimiento del acuerdo registrado.</p>\r\n        <p>Si requieres modificar la fecha o el valor acordado, responde a este correo o comunícate con el área financiera.</p>\r\n        <p>Equipo de cartera — Colegio Bilingüe Campestre Principado de Mónaco (Bogotá)</p></div><div style=\"background:#f1f5f9;padding:24px 32px;border-radius:0 0 18px 18px;font-size:13px;color:#475569;\"><div style=\"font-weight:600;margin-bottom:6px;\">Área de cartera — Colegio Bilingüe Campestre Principado de Mónaco</div><div>Dirección: Cra 12 #145-30, Bogotá</div><div>Teléfono: 6015102020</div><div>Correo: bogota@principadomonaco.edu.co</div><div style=\"margin-top:10px;font-size:12px;opacity:.7;\">Mensaje generado desde la plataforma de cobranzas.</div></div></div></body></html>', 'Error al enviar correo', '2025-11-23 19:48:56', 1, 'error', 'Error SMTP (535): 535-5.7.8 Username and Password not accepted. For more information, go to | 535 5.7.8  https://support.google.com/mail/?p=BadCredentials 00721157ae682-78a798a7f19sm39409707b3.20 - gsmtp. Verifica el usuario y la contraseña o genera una c', 0, '2025-11-24 00:48:56'),
(29, 1, 1, 9, 9, 1, 'gestion', 'email', 'Recordatorio de pago — Arbelaez Balcazar Johan Mauricio', '<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Colegio Bilingüe Campestre Principado de Mónaco</title></head><body style=\"margin:0;background:#e2e8f0;font-family:Inter,Arial,sans-serif;\"><div style=\"max-width:720px;margin:32px auto;background:#ffffff;border-radius:18px;box-shadow:0 24px 60px rgba(15,47,90,.18);overflow:hidden;\"><div style=\"background:#102f5a;color:#ffffff;padding:24px 32px;border-radius:18px 18px 0 0;\"><img src=\"/cobranzav2/public/logos/principado-monaco.png\" alt=\"Colegio Bilingüe Campestre Principado de Mónaco\" style=\"height:48px;margin-bottom:12px;display:block;\"><div style=\"font-size:20px;font-weight:700;letter-spacing:.3px;\">Colegio Bilingüe Campestre Principado de Mónaco</div><div style=\"font-size:14px;opacity:.85;\">Sede Bogotá</div></div><div style=\"padding:32px;font-size:15px;line-height:1.6;color:#1f2937;\"><p>Estimado(a) Balcazar Neira Gloria Amanda,</p>\r\n        <p>De manera atenta le informamos que el saldo pendiente de Arbelaez Balcazar Johan Mauricio corresponde a <strong>$ 2.131.000</strong> con vencimiento el <strong>2025-07-11</strong>.</p>\r\n        <p>Le invitamos a realizar el pago oportunamente para mantener los beneficios académicos activos. Puede comunicarse con nosotros al 6015102020 para ampliar la información.</p>\r\n        <p>Atentamente,<br><strong>Colegio Bilingüe Campestre Principado de Mónaco</strong><br>Sede Bogotá</p></div><div style=\"background:#f1f5f9;padding:24px 32px;border-radius:0 0 18px 18px;font-size:13px;color:#475569;\"><div style=\"font-weight:600;margin-bottom:6px;\">Área de cartera — Colegio Bilingüe Campestre Principado de Mónaco</div><div>Dirección: Cra 12 #145-30, Bogotá</div><div>Teléfono: 6015102020</div><div>Correo: bogota@principadomonaco.edu.co</div><div style=\"margin-top:10px;font-size:12px;opacity:.7;\">Mensaje generado desde la plataforma de cobranzas.</div></div></div></body></html>', 'Correo enviado', '2025-11-23 19:55:53', 1, 'enviado', 'Correo enviado correctamente el 2025-11-23 19:55', 0, '2025-11-24 00:55:53'),
(30, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:01:11', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 401', 0, '2025-11-24 01:01:11'),
(31, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:03:01', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 401', 0, '2025-11-24 01:03:01'),
(32, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:06:41', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:06:41'),
(33, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:09:37', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:09:37'),
(34, 1, 1, 9, 9, 4, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, esperamos que estés bien. Te recordamos el compromiso de pago para Arbelaez Balcazar Johan Mauricio con fecha 2025-07-11. Si necesitas apoyo contáctanos al 6015102020.', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:09:50', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:09:50'),
(35, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:10:20', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:10:20'),
(36, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:10:47', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:10:47'),
(37, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:10:54', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400', 0, '2025-11-24 01:10:54'),
(38, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:14:40', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address', 0, '2025-11-24 01:14:40'),
(39, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:20:56', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address Verifica que el remitente corresponda al número de WhatsApp habilitado por Twilio (sandbox \"whatsapp:+14155238886\" o un número aproba', 0, '2025-11-24 01:20:56'),
(40, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:22:46', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address Verifica que el remitente corresponda al número de WhatsApp habilitado por Twilio (sandbox \"whatsapp:+14155238886\" o un número aproba', 0, '2025-11-24 01:22:46'),
(41, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:22:57', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address Verifica que el remitente corresponda al número de WhatsApp habilitado por Twilio (sandbox \"whatsapp:+14155238886\" o un número aproba', 0, '2025-11-24 01:22:57'),
(42, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:24:07', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address Verifica que el remitente corresponda al número de WhatsApp habilitado por Twilio (sandbox \"whatsapp:+14155238886\" o un número aproba', 0, '2025-11-24 01:24:07'),
(43, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:24:57', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Twilio could not find a Channel with the specified From address Verifica que el remitente corresponda al número de WhatsApp habilitado por Twilio (sandbox \"whatsapp:+14155238886\" o un número aproba', 0, '2025-11-24 01:24:57'),
(44, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:25:23', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Message cannot have the same To and From for account AC20721067f213f23d24dc2e550556fb52', 0, '2025-11-24 01:25:23'),
(45, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:27:06', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMb25055b007fbee7f1aba82f61c845970)', 0, '2025-11-24 01:27:06'),
(46, 1, 1, 9, 9, 4, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, esperamos que estés bien. Te recordamos el compromiso de pago para Arbelaez Balcazar Johan Mauricio con fecha 2025-07-11. Si necesitas apoyo contáctanos al 6015102020.', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:27:49', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMcd60cfae075835f83126dd2e8e4c0caf)', 0, '2025-11-24 01:27:49'),
(47, 1, 1, 2, 2, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Castillo Castro Jozep Evans, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Castillo Figueroa Juana Valeria es $ 1.540.250 con vencimiento 2025-07-04. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:28:54', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMb2ea6978adfc42c91bbcf6cc8abb1b7d)', 0, '2025-11-24 01:28:54'),
(48, 1, 1, 6, 6, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Cobos Arevalo Jenny Juliana, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Forero Cobos Antonio es $ 1.179.200 con vencimiento 2025-07-08. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:30:43', 1, 'enviado', 'Mensaje enviado por Twilio (SID SM767d39b8ea2e9011cc7be35934363c81)', 0, '2025-11-24 01:30:43'),
(49, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:33:47', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMb6f40c851272ee92b100dd6b729ac3e1)', 0, '2025-11-24 01:33:47'),
(50, 1, 1, 6, 6, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Cobos Arevalo Jenny Juliana, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Forero Cobos Antonio es $ 1.179.200 con vencimiento 2025-07-08. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:34:14', 1, 'enviado', 'Mensaje enviado por Twilio (SID SM9c1f97a4cee55a835877ac3cb9c868e8)', 0, '2025-11-24 01:34:14'),
(51, 1, 1, 6, 6, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Cobos Arevalo Jenny Juliana, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Forero Cobos Antonio es $ 1.179.200 con vencimiento 2025-07-08. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:36:16', 1, 'enviado', 'Mensaje enviado por Twilio (SID SM55153da668ce7933fc19bd2ea8fd1e69)', 0, '2025-11-24 01:36:16'),
(52, 1, 1, 2, 2, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Castillo Castro Jozep Evans, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Castillo Figueroa Juana Valeria es $ 1.540.250 con vencimiento 2025-07-04. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:39:09', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMeb0e76fca9ade4f356875d036234af90)', 0, '2025-11-24 01:39:09'),
(53, 1, 1, 2, 2, 4, 'gestion', 'whatsapp', 'prueba', 'Hola Castillo Castro Jozep Evans, esperamos que estés bien. Te recordamos el compromiso de pago para Castillo Figueroa Juana Valeria con fecha 2025-07-04. Si necesitas apoyo contáctanos al 6015102020.', 'Mensaje enviado vía Whatsapp', '2025-11-23 20:39:22', 1, 'enviado', 'Mensaje enviado por Twilio (SID SM9c6337f789c6dad412e8d45a4ccdc4be)', 0, '2025-11-24 01:39:22'),
(54, 1, 1, 2, 2, 1, 'gestion', 'email', 'Recordatorio de pago — Castillo Figueroa Juana Valeria', '<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Colegio Bilingüe Campestre Principado de Mónaco</title></head><body style=\"margin:0;background:#e2e8f0;font-family:Inter,Arial,sans-serif;\"><div style=\"max-width:720px;margin:32px auto;background:#ffffff;border-radius:18px;box-shadow:0 24px 60px rgba(15,47,90,.18);overflow:hidden;\"><div style=\"background:#102f5a;color:#ffffff;padding:24px 32px;border-radius:18px 18px 0 0;\"><img src=\"/cobranzav2/public/logos/principado-monaco.png\" alt=\"Colegio Bilingüe Campestre Principado de Mónaco\" style=\"height:48px;margin-bottom:12px;display:block;\"><div style=\"font-size:20px;font-weight:700;letter-spacing:.3px;\">Colegio Bilingüe Campestre Principado de Mónaco</div><div style=\"font-size:14px;opacity:.85;\">Sede Bogotá</div></div><div style=\"padding:32px;font-size:15px;line-height:1.6;color:#1f2937;\"><p>Estimado(a) Castillo Castro Jozep Evans,</p>\r\n        <p>De manera atenta le informamos que el saldo pendiente de Castillo Figueroa Juana Valeria corresponde a <strong>$ 1.540.250</strong> con vencimiento el <strong>2025-07-04</strong>.</p>\r\n        <p>Le invitamos a realizar el pago oportunamente para mantener los beneficios académicos activos. Puede comunicarse con nosotros al 6015102020 para ampliar la información.</p>\r\n        <p>Atentamente,<br><strong>Colegio Bilingüe Campestre Principado de Mónaco</strong><br>Sede Bogotá</p></div><div style=\"background:#f1f5f9;padding:24px 32px;border-radius:0 0 18px 18px;font-size:13px;color:#475569;\"><div style=\"font-weight:600;margin-bottom:6px;\">Área de cartera — Colegio Bilingüe Campestre Principado de Mónaco</div><div>Dirección: Cra 12 #145-30, Bogotá</div><div>Teléfono: 6015102020</div><div>Correo: bogota@principadomonaco.edu.co</div><div style=\"margin-top:10px;font-size:12px;opacity:.7;\">Mensaje generado desde la plataforma de cobranzas.</div></div></div></body></html>', 'Correo enviado', '2025-11-23 20:41:24', 1, 'enviado', 'Correo enviado correctamente el 2025-11-23 20:41', 0, '2025-11-24 01:41:24'),
(55, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:46:09', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Mismatch between the \'From\' number +14155238886 and the account AC20721067f213f23d24dc2e550556fb52', 0, '2025-11-24 01:46:09'),
(56, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:48:36', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: \'To\' and \'From\' number cannot be the same: +57310277XXXX', 0, '2025-11-24 01:48:36'),
(57, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:49:33', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: \'To\' and \'From\' number cannot be the same: +57310277XXXX', 0, '2025-11-24 01:49:33'),
(58, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:50:44', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: Mismatch between the \'From\' number +18777804236 and the account AC20721067f213f23d24dc2e550556fb52', 0, '2025-11-24 01:50:44'),
(59, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:53:04', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMae6291a25f3fbd6455e3a6b9b0b7855f)', 0, '2025-11-24 01:53:04'),
(60, 1, 1, 6, 6, 5, 'gestion', 'sms', 'prueba', 'Cobos Arevalo Jenny Juliana: saldo $ 1.179.200 de Forero Cobos Antonio vence 2025-07-08. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:53:26', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: The number +57321225XXXX is unverified. Trial accounts cannot send messages to unverified numbers; verify +57321225XXXX at twilio.com/user/account/phone-numbers/verified, or purchase a Twilio numbe', 0, '2025-11-24 01:53:26'),
(61, 1, 1, 2, 2, 5, 'gestion', 'sms', 'prueba', 'Castillo Castro Jozep Evans: saldo $ 1.540.250 de Castillo Figueroa Juana Valeria vence 2025-07-04. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:54:20', 1, 'error', 'Twilio rechazó el envío del mensaje. Código: 400 Detalle: The number +57319291XXXX is unverified. Trial accounts cannot send messages to unverified numbers; verify +57319291XXXX at twilio.com/user/account/phone-numbers/verified, or purchase a Twilio numbe', 0, '2025-11-24 01:54:20'),
(62, 1, 1, 6, 6, 5, 'gestion', 'sms', 'prueba', 'Cobos Arevalo Jenny Juliana: saldo $ 1.179.200 de Forero Cobos Antonio vence 2025-07-08. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:57:03', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMd328800a7f4ed49e582df72bb8c7e8ec)', 0, '2025-11-24 01:57:03'),
(63, 1, 1, 2, 2, 5, 'gestion', 'sms', 'prueba', 'Castillo Castro Jozep Evans: saldo $ 1.540.250 de Castillo Figueroa Juana Valeria vence 2025-07-04. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 20:59:20', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMfd33b2d1e520a9922a94cddacb286a82)', 0, '2025-11-24 01:59:20'),
(64, 1, 1, 9, 9, 6, 'gestion', 'llamada', 'prueba', 'Presentación: Buenos días/tardes Balcazar Neira Gloria Amanda, te habla el área de cartera de Colegio Bilingüe Campestre Principado de Mónaco.\r\nObjetivo: confirmar el estado del pago de Arbelaez Balcazar Johan Mauricio con saldo $ 2.131.000.\r\nAcciones: registra compromisos, dudas y fecha estimada de pago.\r\nCierre: agradece su tiempo y recuerda nuestros canales de atención 6015102020.', 'Registro de llamada manual', '2025-11-23 21:40:25', 1, 'registrado', 'Gestión registrada en canal LLAMADA', 0, '2025-11-24 02:40:25'),
(65, 1, 1, 9, 9, 2, 'gestion', 'whatsapp', 'prueba', 'Hola Balcazar Neira Gloria Amanda, te contactamos de Colegio Bilingüe Campestre Principado de Mónaco. El saldo de Arbelaez Balcazar Johan Mauricio es $ 2.131.000 con vencimiento 2025-07-11. ¿Te apoyamos con algún detalle?', 'Mensaje enviado vía Whatsapp', '2025-11-23 22:34:32', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMd364da26757c6ae9d6d405cea4926435)', 0, '2025-11-24 03:34:32'),
(66, 1, 1, 9, 9, 5, 'gestion', 'sms', 'prueba', 'Balcazar Neira Gloria Amanda: saldo $ 2.131.000 de Arbelaez Balcazar Johan Mauricio vence 2025-07-11. Escríbenos al 6015102020.', 'Mensaje enviado vía Sms', '2025-11-23 22:34:48', 1, 'enviado', 'Mensaje enviado por Twilio (SID SMee73a3e84220a28bb50d19fe31ecf928)', 0, '2025-11-24 03:34:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicacion_adjunto`
--

CREATE TABLE `comunicacion_adjunto` (
  `id_adjunto` int(11) NOT NULL,
  `id_comunicacion` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `tipo` varchar(150) DEFAULT NULL,
  `tamano` bigint(20) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `concepto_deuda`
--

CREATE TABLE `concepto_deuda` (
  `id_concepto` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` varchar(60) DEFAULT NULL,
  `valor_base` decimal(12,2) DEFAULT 0.00,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `concepto_deuda`
--

INSERT INTO `concepto_deuda` (`id_concepto`, `id_colegio`, `nombre`, `descripcion`, `tipo`, `valor_base`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 1, 'Pensión escolar 2025', 'Valor referencial de la pensión anual', 'recurrente', 1250000.00, 'activo', 0, '2025-11-24 00:43:29'),
(2, 1, 'Servicios complementarios', 'Alimentación, transporte y actividades', 'servicio', 320000.00, 'activo', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_colegio`
--

CREATE TABLE `configuracion_colegio` (
  `id_configuracion` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `smtp_host` varchar(150) DEFAULT NULL,
  `smtp_puerto` varchar(10) DEFAULT NULL,
  `smtp_usuario` varchar(150) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `whatsapp_api_key` varchar(255) DEFAULT NULL,
  `whatsapp_endpoint` varchar(255) DEFAULT NULL,
  `sms_api_key` varchar(255) DEFAULT NULL,
  `sms_endpoint` varchar(255) DEFAULT NULL,
  `twilio_account_sid` varchar(64) DEFAULT NULL,
  `twilio_auth_token` varchar(128) DEFAULT NULL,
  `twilio_whatsapp_from` varchar(32) DEFAULT NULL,
  `twilio_whatsapp_template_sid` varchar(64) DEFAULT NULL,
  `twilio_sms_from` varchar(32) DEFAULT NULL,
  `twilio_default_country` varchar(8) DEFAULT NULL,
  `twilio_status_callback` varchar(255) DEFAULT NULL,
  `twilio_incoming_webhook` varchar(255) DEFAULT NULL,
  `dashboard_top_responsables` int(11) DEFAULT 5,
  `dashboard_meses_cartera` int(11) DEFAULT 6,
  `logo_path` varchar(255) DEFAULT NULL,
  `actualizado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_colegio`
--

INSERT INTO `configuracion_colegio` (`id_configuracion`, `id_colegio`, `smtp_host`, `smtp_puerto`, `smtp_usuario`, `smtp_password`, `whatsapp_api_key`, `whatsapp_endpoint`, `sms_api_key`, `sms_endpoint`, `twilio_account_sid`, `twilio_auth_token`, `twilio_whatsapp_from`, `twilio_whatsapp_template_sid`, `twilio_sms_from`, `twilio_default_country`, `twilio_status_callback`, `twilio_incoming_webhook`, `dashboard_top_responsables`, `dashboard_meses_cartera`, `logo_path`, `actualizado_por`, `fecha_actualizacion`) VALUES
(1, 1, 'smtp.gmail.com', '587', 'carlos.quinones@lm-technology.com.co', 'ahiu codf pjlb dcra', '', '', '', '', 'AC20721067f213f23d24dc2e550556fb52', 'e6effe03a14ad5a0efa88784a3c9c37f', '+1 4155238886', '', '+12566374335', '+57', '', 'https://timberwolf-mastiff-9776.twil.io/demo-reply', 5, 6, NULL, 1, '2025-11-23 20:52:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuota_acuerdo`
--

CREATE TABLE `cuota_acuerdo` (
  `id_cuota` int(11) NOT NULL,
  `id_acuerdo` int(11) NOT NULL,
  `numero_cuota` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `valor_cuota` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','pagada','vencida') DEFAULT 'pendiente',
  `fecha_pago_real` date DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuota_acuerdo`
--

INSERT INTO `cuota_acuerdo` (`id_cuota`, `id_acuerdo`, `numero_cuota`, `fecha_pago`, `valor_cuota`, `estado`, `fecha_pago_real`, `observaciones`, `creado_en`) VALUES
(1, 1, 1, '2025-07-15', 180000.00, 'pendiente', NULL, NULL, '2025-11-24 00:43:29'),
(2, 1, 2, '2025-08-15', 180000.00, 'pendiente', NULL, NULL, '2025-11-24 00:43:29'),
(3, 1, 3, '2025-09-15', 180000.00, 'pendiente', NULL, NULL, '2025-11-24 00:43:29'),
(4, 2, 1, '2025-07-20', 230000.00, 'pendiente', NULL, NULL, '2025-11-24 00:43:29'),
(5, 2, 2, '2025-08-20', 230000.00, 'pendiente', NULL, NULL, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deuda`
--

CREATE TABLE `deuda` (
  `id_deuda` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `id_concepto` int(11) DEFAULT NULL,
  `fecha_generacion` date NOT NULL,
  `valor_inicial` decimal(12,2) NOT NULL,
  `saldo_actual` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','pagado','en_acuerdo') DEFAULT 'pendiente',
  `fecha_vencimiento` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `deuda`
--

INSERT INTO `deuda` (`id_deuda`, `id_colegio`, `id_sede`, `id_estudiante`, `id_periodo`, `id_concepto`, `fecha_generacion`, `valor_inicial`, `saldo_actual`, `estado`, `fecha_vencimiento`, `notas`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 1, 1, 1, '2025-06-03', 975125.00, 975125.00, 'pendiente', '2025-07-03', 'Gestion: CONTACTO DIRECTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(2, 1, 1, 2, 1, 1, '2025-06-04', 1540250.00, 1540250.00, 'pendiente', '2025-07-04', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(3, 1, 1, 3, 1, 1, '2025-06-05', 2224250.00, 2224250.00, 'pendiente', '2025-07-05', 'Gestion: COMPROMISO DE PAGO. Seguimiento: COMPROMISO DE PAGO. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(4, 1, 1, 4, 1, 1, '2025-06-06', 2379200.00, 2379200.00, 'pendiente', '2025-07-06', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(5, 1, 1, 5, 1, 1, '2025-06-07', 985600.00, 985600.00, 'pendiente', '2025-07-07', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(6, 1, 1, 6, 1, 1, '2025-06-08', 1179200.00, 1179200.00, 'pendiente', '2025-07-08', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(7, 1, 1, 7, 1, 1, '2025-06-09', 2259500.00, 2259500.00, 'pendiente', '2025-07-09', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(8, 1, 1, 8, 1, 1, '2025-06-10', 3406560.00, 3406560.00, 'pendiente', '2025-07-10', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(9, 1, 1, 9, 1, 1, '2025-06-11', 2131000.00, 2131000.00, 'pendiente', '2025-07-11', 'Gestion: CONTACTO DIRECTO. Seguimiento: RETIRADO DEL COLEGIO. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(10, 1, 1, 10, 1, 1, '2025-06-12', 1077800.00, 1077800.00, 'pendiente', '2025-07-12', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(11, 1, 1, 11, 1, 1, '2025-06-13', 1723000.00, 1723000.00, 'pendiente', '2025-07-13', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(12, 1, 1, 12, 1, 1, '2025-06-14', 1723000.00, 1723000.00, 'pendiente', '2025-07-14', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(13, 1, 1, 13, 1, 1, '2025-06-15', 953300.00, 953300.00, 'pendiente', '2025-07-15', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(14, 1, 2, 14, 1, 1, '2025-06-16', 1006500.00, 1006500.00, 'pendiente', '2025-07-16', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(15, 1, 2, 15, 1, 1, '2025-06-17', 946650.00, 946650.00, 'pendiente', '2025-07-17', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(16, 1, 2, 16, 1, 1, '2025-06-18', 5462199.00, 5462199.00, 'pendiente', '2025-07-18', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(17, 1, 2, 17, 1, 1, '2025-06-19', 1723000.00, 1723000.00, 'pendiente', '2025-07-19', 'Gestion: EN SEGUIMIENTO. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(18, 1, 2, 18, 1, 1, '2025-06-20', 1321100.00, 1321100.00, 'pendiente', '2025-07-20', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(19, 1, 2, 19, 1, 1, '2025-06-21', 1721784.00, 1721784.00, 'pendiente', '2025-07-21', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(20, 1, 2, 20, 1, 1, '2025-06-22', 2263160.00, 2263160.00, 'pendiente', '2025-07-22', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(21, 1, 2, 21, 1, 1, '2025-06-03', 1280950.00, 1280950.00, 'pendiente', '2025-07-03', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(22, 1, 2, 22, 1, 1, '2025-06-04', 1278000.00, 1278000.00, 'pendiente', '2025-07-04', 'Gestion: CONTACTO DIRECTO. Seguimiento: EN SEGUIMIENTO. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(23, 1, 2, 23, 1, 1, '2025-06-05', 3371983.00, 3371983.00, 'pendiente', '2025-07-05', 'Gestion: ESPERA DE RESPUESTA. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(24, 1, 2, 24, 1, 1, '2025-06-06', 5107797.00, 5107797.00, 'pendiente', '2025-07-06', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29'),
(25, 1, 2, 25, 1, 1, '2025-06-07', 4731950.00, 4731950.00, 'pendiente', '2025-07-07', 'Gestion: EN SEGUIMIENTO. Seguimiento: ENVIO DE NOTIFICACION. Compromiso: SI', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `codigo_estudiante` varchar(60) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `grado` varchar(60) DEFAULT NULL,
  `curso` varchar(60) DEFAULT NULL,
  `estado` enum('activo','inactivo','retirado') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiante`
--

INSERT INTO `estudiante` (`id_estudiante`, `id_colegio`, `id_sede`, `id_responsable`, `codigo_estudiante`, `nombre_completo`, `grado`, `curso`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 1, '10176', 'Villalobos Chavista Samuel', 'Grado 1', '1B', 'activo', 0, '2025-11-24 00:43:29'),
(2, 1, 1, 2, '10130', 'Castillo Figueroa Juana Valeria', 'Grado 5', '5B', 'activo', 0, '2025-11-24 00:43:29'),
(3, 1, 1, 3, '10119', 'Pineda Blanco Juan Esteban', 'Grado 5', '5A', 'activo', 0, '2025-11-24 00:43:29'),
(4, 1, 1, 4, '10099', 'Garcia Medina Geronimo', 'Grado 4', '4B', 'activo', 0, '2025-11-24 00:43:29'),
(5, 1, 1, 5, '10102', 'Maneiro Bolivar Yoneiker Alejandro', 'Grado 4', '4A', 'activo', 0, '2025-11-24 00:43:29'),
(6, 1, 1, 6, '10179', 'Forero Cobos Antonio', 'Grado 3', '3A', 'activo', 0, '2025-11-24 00:43:29'),
(7, 1, 1, 7, '10047', 'Millan Lima Matias', 'Grado 2', '2A', 'activo', 0, '2025-11-24 00:43:29'),
(8, 1, 1, 8, '10214', 'Guevara Matiz Gabriel Ricardo', 'Grado 1', '1A', 'activo', 0, '2025-11-24 00:43:29'),
(9, 1, 1, 9, '10050', 'Arbelaez Balcazar Johan Mauricio', 'Grado 3', '3B', 'retirado', 0, '2025-11-24 00:43:29'),
(10, 1, 1, 10, '10195', 'Ruiz Ruiz Daniel Leonardo', 'Grado 4', '4B', 'activo', 0, '2025-11-24 00:43:29'),
(11, 1, 1, 11, '10215', 'Duarte Arino Maximo', 'Preescolar', 'JD', 'activo', 0, '2025-11-24 00:43:29'),
(12, 1, 1, 11, '10216', 'Duarte Arino Maximiliano', 'Grado 3', '3A', 'activo', 0, '2025-11-24 00:43:29'),
(13, 1, 1, 12, '10023', 'Toro Aristizabal Pablo Andres', 'Grado 1', '1A', 'activo', 0, '2025-11-24 00:43:29'),
(14, 1, 2, 13, '10237', 'Yousef Leon Shaker Salim', 'Preescolar', 'PJ', 'activo', 0, '2025-11-24 00:43:29'),
(15, 1, 2, 13, '10238', 'Yousef Leon Sami Zahid', 'Grado 5', '5A', 'activo', 0, '2025-11-24 00:43:29'),
(16, 1, 2, 14, '10086', 'Marulanda Vargas Juanita', 'Grado 4', '4A', 'activo', 0, '2025-11-24 00:43:29'),
(17, 1, 2, 15, '10211', 'Franco Jimenez Franz', 'Grado 3', '3A', 'activo', 0, '2025-11-24 00:43:29'),
(18, 1, 2, 16, '10235', 'Zamora Sanchez Luciana', 'Preescolar', 'TR', 'activo', 0, '2025-11-24 00:43:29'),
(19, 1, 2, 17, '10225', 'Lopez Heredia Johan Stephan', 'Preescolar', 'JD', 'activo', 0, '2025-11-24 00:43:29'),
(20, 1, 2, 17, '10224', 'Lopez Heredia Gabriel Mathias', 'Grado 2', '2A', 'activo', 0, '2025-11-24 00:43:29'),
(21, 1, 2, 18, '10120', 'Pineda Ruiz Matias', 'Grado 5', '5B', 'activo', 0, '2025-11-24 00:43:29'),
(22, 1, 2, 19, '10052', 'Avendano Medrano Jacobo', 'Grado 3', '3A', 'activo', 0, '2025-11-24 00:43:29'),
(23, 1, 2, 20, '10124', 'Tovar Leon Maria Alejandra', 'Grado 5', '5A', 'activo', 0, '2025-11-24 00:43:29'),
(24, 1, 2, 21, '10184', 'Gomez Gutierrez Salome', 'Grado 1', '1A', 'activo', 0, '2025-11-24 00:43:29'),
(25, 1, 2, 22, '10060', 'Gamboa Cordoba Julian Ameobi', 'Grado 3', '3A', 'activo', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_sistema`
--

CREATE TABLE `modulo_sistema` (
  `id_modulo` int(11) NOT NULL,
  `codigo` varchar(60) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulo_sistema`
--

INSERT INTO `modulo_sistema` (`id_modulo`, `codigo`, `nombre`, `descripcion`, `estado`, `creado_en`) VALUES
(1, 'cobranzas', 'Gestión de cobranzas', 'Seguimiento integral de cartera y comunicaciones', 'activo', '2025-11-24 00:43:28'),
(2, 'administracion', 'Administración institucional', 'Gestión de sedes, usuarios y catálogos', 'activo', '2025-11-24 00:43:28'),
(3, 'parametrizacion', 'Parametrización avanzada', 'Configuraciones, plantillas y parámetros del sistema', 'activo', '2025-11-24 00:43:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros_sistema`
--

CREATE TABLE `parametros_sistema` (
  `id_parametro` int(11) NOT NULL,
  `clave` varchar(120) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_colegio` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `parametros_sistema`
--

INSERT INTO `parametros_sistema` (`id_parametro`, `clave`, `valor`, `descripcion`, `id_colegio`, `id_sede`, `eliminado`, `creado_en`) VALUES
(1, 'color_primario', '#0f325d', 'Color institucional principal', 1, NULL, 0, '2025-11-24 00:43:29'),
(2, 'color_secundario', '#f5a524', 'Color de énfasis para alertas', 1, NULL, 0, '2025-11-24 00:43:29'),
(3, 'dias_recordatorio', '5', 'Días antes del vencimiento para alertar', 1, NULL, 0, '2025-11-24 00:43:29'),
(4, 'correo_respuesta', 'recaudos@principadomonaco.edu.co', 'Correo remitente para notificaciones', 1, NULL, 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodo`
--

CREATE TABLE `periodo` (
  `id_periodo` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `periodo`
--

INSERT INTO `periodo` (`id_periodo`, `id_colegio`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 1, 'Cartera 2025', '2025-01-01', '2025-12-31', 'activo', 0, '2025-11-24 00:43:29'),
(2, 1, 'Junio 2025', '2025-06-01', '2025-06-30', 'activo', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantilla_comunicacion`
--

CREATE TABLE `plantilla_comunicacion` (
  `id_plantilla` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `canal` enum('email','whatsapp','sms','llamada') NOT NULL DEFAULT 'email',
  `descripcion` varchar(255) DEFAULT NULL,
  `asunto_default` varchar(180) DEFAULT NULL,
  `cuerpo_html` mediumtext NOT NULL,
  `variables` text DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_por` int(11) DEFAULT NULL,
  `actualizado_por` int(11) DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `plantilla_comunicacion`
--

INSERT INTO `plantilla_comunicacion` (`id_plantilla`, `id_colegio`, `nombre`, `canal`, `descripcion`, `asunto_default`, `cuerpo_html`, `variables`, `estado`, `eliminado`, `creado_por`, `actualizado_por`, `fecha_actualizacion`, `creado_en`) VALUES
(1, 1, 'Recordatorio de pago pendiente', 'email', 'Correo formal recordando el saldo pendiente y fecha de vencimiento.', 'Recordatorio de pago — {{estudiante_nombre}}', '<p>Estimado(a) {{responsable_nombre}},</p>\n        <p>De manera atenta le informamos que el saldo pendiente de {{estudiante_nombre}} corresponde a <strong>{{saldo_pendiente}}</strong> con vencimiento el <strong>{{fecha_vencimiento}}</strong>.</p>\n        <p>Le invitamos a realizar el pago oportunamente para mantener los beneficios académicos activos. Puede comunicarse con nosotros al {{telefono_contacto}} para ampliar la información.</p>\n        <p>Atentamente,<br><strong>{{colegio_nombre}}</strong><br>Sede {{sede_nombre}}</p>', 'responsable_nombre,estudiante_nombre,saldo_pendiente,fecha_vencimiento,colegio_nombre,sede_nombre,telefono_contacto', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29'),
(2, 1, 'Mensaje corto WhatsApp', 'whatsapp', 'Plantilla corta para contacto inmediato por WhatsApp.', NULL, 'Hola {{responsable_nombre}}, te escribimos del {{colegio_nombre}}.\nEl saldo pendiente de {{estudiante_nombre}} es de {{saldo_pendiente}} con vencimiento {{fecha_vencimiento}}.\nSi ya realizaste el pago, por favor ignora este mensaje.', 'responsable_nombre,colegio_nombre,estudiante_nombre,saldo_pendiente,fecha_vencimiento', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29'),
(3, 1, 'Seguimiento compromiso de pago', 'email', 'Correo de seguimiento cuando existe un compromiso registrado.', 'Seguimiento compromiso {{estudiante_nombre}}', '<p>Buen día {{responsable_nombre}},</p>\n        <p>Confirmamos el compromiso de pago asociado a {{estudiante_nombre}} con fecha objetivo {{fecha_vencimiento}}. Este mensaje busca acompañar el cumplimiento del acuerdo registrado.</p>\n        <p>Si requieres modificar la fecha o el valor acordado, responde a este correo o comunícate con el área financiera.</p>\n        <p>Equipo de cartera — {{colegio_nombre}} ({{sede_nombre}})</p>', 'responsable_nombre,estudiante_nombre,fecha_vencimiento,colegio_nombre,sede_nombre', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29'),
(4, 1, 'WhatsApp seguimiento compromiso', 'whatsapp', 'Mensaje cordial para confirmar compromisos previos.', NULL, 'Hola {{responsable_nombre}}, esperamos que estés bien. Te recordamos el compromiso de pago para {{estudiante_nombre}} con fecha {{fecha_vencimiento}}. Si necesitas apoyo contáctanos al {{telefono_contacto}}.', 'responsable_nombre,estudiante_nombre,fecha_vencimiento,telefono_contacto', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29'),
(5, 1, 'SMS alerta vencimiento', 'sms', 'Texto corto para vencimientos inmediatos.', NULL, '{{responsable_nombre}}: saldo {{saldo_pendiente}} de {{estudiante_nombre}} vence {{fecha_vencimiento}}. Escríbenos al {{telefono_contacto}}.', 'responsable_nombre,saldo_pendiente,estudiante_nombre,fecha_vencimiento,telefono_contacto', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29'),
(6, 1, 'Guion llamada verificación', 'llamada', 'Guion orientador para registrar llamadas manuales.', NULL, 'Presentación: Buenos días/tardes {{responsable_nombre}}, te habla el área de cartera de {{colegio_nombre}}.\nObjetivo: confirmar el estado del pago de {{estudiante_nombre}} con saldo {{saldo_pendiente}}.\nAcciones: registra compromisos, dudas y fecha estimada de pago.\nCierre: agradece su tiempo y recuerda nuestros canales de atención {{telefono_contacto}}.', 'responsable_nombre,colegio_nombre,estudiante_nombre,saldo_pendiente,telefono_contacto', 'activo', 0, 2, NULL, NULL, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pago`
--

CREATE TABLE `registro_pago` (
  `id_pago` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `valor_total` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(60) DEFAULT NULL,
  `referencia` varchar(120) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `ruta_soporte` varchar(255) DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `registro_pago`
--

INSERT INTO `registro_pago` (`id_pago`, `id_colegio`, `id_sede`, `id_estudiante`, `fecha_pago`, `valor_total`, `metodo_pago`, `referencia`, `observaciones`, `ruta_soporte`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 1, '2025-06-18', 300000.00, 'transferencia', 'PM-20250618-01', NULL, 'uploads/soportes/PM-20250618-01.pdf', 0, '2025-11-24 00:43:29'),
(2, 1, 1, 6, '2025-06-19', 180000.00, 'transferencia', 'PM-20250619-06', NULL, 'uploads/soportes/PM-20250619-06.pdf', 0, '2025-11-24 00:43:29'),
(3, 1, 2, 14, '2025-06-21', 220000.00, 'tarjeta', 'PM-20250621-14', NULL, 'uploads/soportes/PM-20250621-14.pdf', 0, '2025-11-24 00:43:29'),
(4, 1, 2, 18, '2025-05-30', 150000.00, 'transferencia', 'PM-20250530-18', NULL, 'uploads/soportes/PM-20250530-18.pdf', 0, '2025-11-24 00:43:29'),
(5, 1, 2, 21, '2025-06-15', 210000.00, 'transferencia', 'PM-20250615-21', NULL, 'uploads/soportes/PM-20250615-21.pdf', 0, '2025-11-24 00:43:29'),
(6, 1, 2, 22, '2025-06-16', 120000.00, 'transferencia', 'PM-20250616-22', NULL, 'uploads/soportes/PM-20250616-22.pdf', 0, '2025-11-24 00:43:29'),
(7, 1, 2, 24, '2025-06-22', 320000.00, 'transferencia', 'PM-20250622-24', NULL, 'uploads/soportes/PM-20250622-24.pdf', 0, '2025-11-24 00:43:29'),
(8, 1, 2, 25, '2025-06-23', 250000.00, 'transferencia', 'PM-20250623-25', NULL, 'uploads/soportes/PM-20250623-25.pdf', 0, '2025-11-24 00:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responsable_financiero`
--

CREATE TABLE `responsable_financiero` (
  `id_responsable` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `tipo_documento` varchar(10) NOT NULL,
  `numero_documento` varchar(40) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `direccion` varchar(180) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `responsable_financiero`
--

INSERT INTO `responsable_financiero` (`id_responsable`, `id_colegio`, `id_sede`, `nombre_completo`, `tipo_documento`, `numero_documento`, `telefono`, `correo`, `direccion`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 1, 1, 'Villalobos Munoz Fernando', 'CC', '80073011', '+57 308 007 3011', 'villalobos.munoz.fernando@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(2, 1, 1, 'Castillo Castro Jozep Evans', 'CC', '79955368', '+57 3192919829', 'jleonardo.mejia10@gmail.com', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(3, 1, 1, 'Pineda Pachon Julio Cesar', 'CC', '79335279', '+57 307 933 5279', 'pineda.pachon.julio.cesar@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(4, 1, 1, 'Medina Arevalo Angelica', 'CC', '60288036', '+57 306 028 8036', 'medina.arevalo.angelica@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(5, 1, 1, 'Maneiro Fermin Manuel Valdemar', 'CC', '5709969', '+57 300 570 9969', 'maneiro.fermin.manuel.valdemar@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(6, 1, 1, 'Cobos Arevalo Jenny Juliana', 'CC', '53118047', '+573212259737', 'cobos.arevalo.jenny.juliana@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(7, 1, 1, 'Lima Gonzalez Carolina', 'CC', '52715742', '+57 305 271 5742', 'lima.gonzalez.carolina@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(8, 1, 1, 'Matiz Perez Adriana Patricia', 'CC', '52493163', '+57 305 249 3163', 'matiz.perez.adriana.patricia@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(9, 1, 1, 'Balcazar Neira Gloria Amanda', 'CC', '41060961', '+57 3102778273', 'ccaicedo19@outlook.es', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(10, 1, 1, 'Ruiz Jose Hector', 'CC', '17006214', '+57 301 700 6214', 'ruiz.jose.hector@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(11, 1, 1, 'Duarte Hinojosa Naisir', 'CC', '12644133', '+57 301 264 4133', 'duarte.hinojosa.naisir@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(12, 1, 1, 'Toro Aristizabal Navi Yuliana', 'CC', '1128434202', '+57 312 843 4202', 'toro.aristizabal.navi.yuliana@familias-principado.edu.co', 'Cra 12 #145-30, Bogotá', 'activo', 0, '2025-11-24 00:43:28'),
(13, 1, 2, 'Leon Pinzon Danna Catalina', 'CC', '1121863888', '+57 312 186 3888', 'leon.pinzon.danna.catalina@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(14, 1, 2, 'Marulanda Vargas Claudia Rocio', 'CC', '1111198791', '+57 311 119 8791', 'marulanda.vargas.claudia.rocio@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(15, 1, 2, 'Franco Berrocal Yonatan David', 'CC', '1067910830', '+57 306 791 0830', 'franco.berrocal.yonatan.david@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(16, 1, 2, 'Sanchez Rios Luz Argenis', 'CC', '1033750035', '+57 303 375 0035', 'sanchez.rios.luz.argenis@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(17, 1, 2, 'Heredia Enciso Paula Carolina', 'CC', '1032448391', '+57 303 244 8391', 'heredia.enciso.paula.carolina@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(18, 1, 2, 'Pineda Pardo Fabio Andres', 'CC', '1022339392', '+57 302 233 9392', 'pineda.pardo.fabio.andres@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(19, 1, 2, 'Avendano Maldonado David Andres', 'CC', '1020735931', '+57 302 073 5931', 'avendano.maldonado.david.andres@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(20, 1, 2, 'Leon Valencia Kelly Johanna', 'CC', '1015431483', '+57 301 543 1483', 'leon.valencia.kelly.johanna@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(21, 1, 2, 'Gomez Julian Felipe', 'CC', '1015408023', '+57 301 540 8023', 'gomez.julian.felipe@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28'),
(22, 1, 2, 'Cordoba Palacios Ana Dayana', 'CC', '1003968947', '+57 300 396 8947', 'cordoba.palacios.ana.dayana@familias-principado.edu.co', 'Km 4 vía Cota-Chía, Cota', 'activo', 0, '2025-11-24 00:43:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sede`
--

CREATE TABLE `sede` (
  `id_sede` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `direccion` varchar(180) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sede`
--

INSERT INTO `sede` (`id_sede`, `id_colegio`, `nombre`, `direccion`, `telefono`, `correo`, `estado`, `eliminado`, `creado_en`) VALUES
(1, 1, 'Bogotá', 'Cra 12 #145-30, Bogotá', '6015102020', 'bogota@principadomonaco.edu.co', 'activo', 0, '2025-11-24 00:43:28'),
(2, 1, 'Cota', 'Km 4 vía Cota-Chía, Cota', '6015103030', 'cota@principadomonaco.edu.co', 'activo', 0, '2025-11-24 00:43:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_colegio` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `usuario` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin_global','admin_colegio','agente') NOT NULL DEFAULT 'agente',
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `eliminado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_colegio`, `id_sede`, `nombre_completo`, `email`, `usuario`, `password_hash`, `rol`, `estado`, `eliminado`, `creado_en`) VALUES
(1, NULL, NULL, 'Administrador General', 'admin@principadomonaco.edu.co', 'admin', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'admin_global', 'activo', 0, '2025-11-24 00:43:28'),
(2, 1, NULL, 'Coordinadora Financiera', 'financiera@principadomonaco.edu.co', 'admin_colegio', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'admin_colegio', 'activo', 0, '2025-11-24 00:43:28'),
(3, 1, 1, 'Agente Cartera Bogotá', 'cartera.bogota@principadomonaco.edu.co', 'agente_bogota', '$2y$12$q3Qs/YGCbyzw5WV0RAd32OsQ5BNJLn1ycE0Hn9Fswe9W5VV3B.URW', 'agente', 'activo', 0, '2025-11-24 00:43:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_colegio`
--

CREATE TABLE `usuario_colegio` (
  `id_usuario` int(11) NOT NULL,
  `id_colegio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_colegio`
--

INSERT INTO `usuario_colegio` (`id_usuario`, `id_colegio`) VALUES
(1, 1),
(2, 1),
(3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_modulo`
--

CREATE TABLE `usuario_modulo` (
  `id_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_modulo`
--

INSERT INTO `usuario_modulo` (`id_usuario`, `id_modulo`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_sede`
--

CREATE TABLE `usuario_sede` (
  `id_usuario` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_sede`
--

INSERT INTO `usuario_sede` (`id_usuario`, `id_sede`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acuerdo_pago`
--
ALTER TABLE `acuerdo_pago`
  ADD PRIMARY KEY (`id_acuerdo`);

--
-- Indices de la tabla `auditoria_usuario`
--
ALTER TABLE `auditoria_usuario`
  ADD PRIMARY KEY (`id_auditoria`);

--
-- Indices de la tabla `carga_masiva`
--
ALTER TABLE `carga_masiva`
  ADD PRIMARY KEY (`id_carga`);

--
-- Indices de la tabla `colegio`
--
ALTER TABLE `colegio`
  ADD PRIMARY KEY (`id_colegio`);

--
-- Indices de la tabla `comunicacion`
--
ALTER TABLE `comunicacion`
  ADD PRIMARY KEY (`id_comunicacion`);

--
-- Indices de la tabla `comunicacion_adjunto`
--
ALTER TABLE `comunicacion_adjunto`
  ADD PRIMARY KEY (`id_adjunto`),
  ADD KEY `id_comunicacion` (`id_comunicacion`);

--
-- Indices de la tabla `concepto_deuda`
--
ALTER TABLE `concepto_deuda`
  ADD PRIMARY KEY (`id_concepto`);

--
-- Indices de la tabla `configuracion_colegio`
--
ALTER TABLE `configuracion_colegio`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `cuota_acuerdo`
--
ALTER TABLE `cuota_acuerdo`
  ADD PRIMARY KEY (`id_cuota`),
  ADD KEY `id_acuerdo` (`id_acuerdo`);

--
-- Indices de la tabla `deuda`
--
ALTER TABLE `deuda`
  ADD PRIMARY KEY (`id_deuda`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`id_estudiante`);

--
-- Indices de la tabla `modulo_sistema`
--
ALTER TABLE `modulo_sistema`
  ADD PRIMARY KEY (`id_modulo`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `parametros_sistema`
--
ALTER TABLE `parametros_sistema`
  ADD PRIMARY KEY (`id_parametro`);

--
-- Indices de la tabla `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`id_periodo`);

--
-- Indices de la tabla `plantilla_comunicacion`
--
ALTER TABLE `plantilla_comunicacion`
  ADD PRIMARY KEY (`id_plantilla`);

--
-- Indices de la tabla `registro_pago`
--
ALTER TABLE `registro_pago`
  ADD PRIMARY KEY (`id_pago`);

--
-- Indices de la tabla `responsable_financiero`
--
ALTER TABLE `responsable_financiero`
  ADD PRIMARY KEY (`id_responsable`);

--
-- Indices de la tabla `sede`
--
ALTER TABLE `sede`
  ADD PRIMARY KEY (`id_sede`),
  ADD KEY `id_colegio` (`id_colegio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuario` (`usuario`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indices de la tabla `usuario_colegio`
--
ALTER TABLE `usuario_colegio`
  ADD PRIMARY KEY (`id_usuario`,`id_colegio`),
  ADD KEY `id_colegio` (`id_colegio`);

--
-- Indices de la tabla `usuario_modulo`
--
ALTER TABLE `usuario_modulo`
  ADD PRIMARY KEY (`id_usuario`,`id_modulo`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `usuario_sede`
--
ALTER TABLE `usuario_sede`
  ADD PRIMARY KEY (`id_usuario`,`id_sede`),
  ADD KEY `id_sede` (`id_sede`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acuerdo_pago`
--
ALTER TABLE `acuerdo_pago`
  MODIFY `id_acuerdo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `auditoria_usuario`
--
ALTER TABLE `auditoria_usuario`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `carga_masiva`
--
ALTER TABLE `carga_masiva`
  MODIFY `id_carga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `colegio`
--
ALTER TABLE `colegio`
  MODIFY `id_colegio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `comunicacion`
--
ALTER TABLE `comunicacion`
  MODIFY `id_comunicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `comunicacion_adjunto`
--
ALTER TABLE `comunicacion_adjunto`
  MODIFY `id_adjunto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `concepto_deuda`
--
ALTER TABLE `concepto_deuda`
  MODIFY `id_concepto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracion_colegio`
--
ALTER TABLE `configuracion_colegio`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cuota_acuerdo`
--
ALTER TABLE `cuota_acuerdo`
  MODIFY `id_cuota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `deuda`
--
ALTER TABLE `deuda`
  MODIFY `id_deuda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `modulo_sistema`
--
ALTER TABLE `modulo_sistema`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `parametros_sistema`
--
ALTER TABLE `parametros_sistema`
  MODIFY `id_parametro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `periodo`
--
ALTER TABLE `periodo`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `plantilla_comunicacion`
--
ALTER TABLE `plantilla_comunicacion`
  MODIFY `id_plantilla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `registro_pago`
--
ALTER TABLE `registro_pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `responsable_financiero`
--
ALTER TABLE `responsable_financiero`
  MODIFY `id_responsable` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `sede`
--
ALTER TABLE `sede`
  MODIFY `id_sede` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comunicacion_adjunto`
--
ALTER TABLE `comunicacion_adjunto`
  ADD CONSTRAINT `comunicacion_adjunto_ibfk_1` FOREIGN KEY (`id_comunicacion`) REFERENCES `comunicacion` (`id_comunicacion`);

--
-- Filtros para la tabla `cuota_acuerdo`
--
ALTER TABLE `cuota_acuerdo`
  ADD CONSTRAINT `cuota_acuerdo_ibfk_1` FOREIGN KEY (`id_acuerdo`) REFERENCES `acuerdo_pago` (`id_acuerdo`);

--
-- Filtros para la tabla `sede`
--
ALTER TABLE `sede`
  ADD CONSTRAINT `sede_ibfk_1` FOREIGN KEY (`id_colegio`) REFERENCES `colegio` (`id_colegio`);

--
-- Filtros para la tabla `usuario_colegio`
--
ALTER TABLE `usuario_colegio`
  ADD CONSTRAINT `usuario_colegio_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_colegio_ibfk_2` FOREIGN KEY (`id_colegio`) REFERENCES `colegio` (`id_colegio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_modulo`
--
ALTER TABLE `usuario_modulo`
  ADD CONSTRAINT `usuario_modulo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_modulo_ibfk_2` FOREIGN KEY (`id_modulo`) REFERENCES `modulo_sistema` (`id_modulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_sede`
--
ALTER TABLE `usuario_sede`
  ADD CONSTRAINT `usuario_sede_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_sede_ibfk_2` FOREIGN KEY (`id_sede`) REFERENCES `sede` (`id_sede`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
