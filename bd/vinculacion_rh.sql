-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-07-2025 a las 22:57:33
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
-- Base de datos: `vinculacion_rh`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspirantes`
--

CREATE TABLE `aspirantes` (
  `id_aspirante` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_regional` int(11) NOT NULL,
  `fecha_inicio_proceso` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Activo','Contratado','Rechazado') NOT NULL DEFAULT 'Activo',
  `estado_proceso` enum('Activo','Contratado','Rechazado','En Pausa') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contiene la información personal y de asignación de cada aspirante.';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id_documento` int(11) NOT NULL,
  `id_aspirante` int(11) NOT NULL,
  `nombre_documento` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL COMMENT 'Ruta física del archivo en el servidor',
  `fecha_carga` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Almacena los documentos adjuntos por los aspirantes.';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Almacena las empresas principales como Arpesod y Finansueños.';

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `nombre_empresa`) VALUES
(1, 'Arpesod'),
(2, 'Finansueños');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_regional`
--

CREATE TABLE `empresa_regional` (
  `id_empresa` int(11) NOT NULL,
  `id_regional` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de enlace para la relación N:M entre empresas y regionales.';

--
-- Volcado de datos para la tabla `empresa_regional`
--

INSERT INTO `empresa_regional` (`id_empresa`, `id_regional`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fases_maestras`
--

CREATE TABLE `fases_maestras` (
  `id_fase` int(11) NOT NULL,
  `nombre_fase` varchar(150) NOT NULL,
  `orden` int(11) NOT NULL COMMENT 'Para mostrar las fases en secuencia correcta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de todas las posibles fases del proceso de selección.';

--
-- Volcado de datos para la tabla `fases_maestras`
--

INSERT INTO `fases_maestras` (`id_fase`, `nombre_fase`, `orden`) VALUES
(1, 'Envio Solicitud Regional a RRHH', 1),
(2, 'Respuesta 1 Solicitud RRHH a Regional ', 2),
(3, 'Respuesta 1 Solicitud Regional a RRHH', 3),
(4, 'Solicitud Datos postulado RRHH a Regional', 4),
(5, 'Soliciutd Examen de Ingreso RRHH a SST', 5),
(6, 'Respuesta solicitud datos postulado regional a RRHH', 6),
(7, 'Respuesta solicitud examen de ingreso de SST a RRHH', 7),
(8, 'Solicitud afiliacion ARL RRHH a SST', 8),
(9, 'Contratacion', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regionales`
--

CREATE TABLE `regionales` (
  `id_regional` int(11) NOT NULL,
  `nombre_regional` varchar(100) NOT NULL,
  `id_zona` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Almacena todas las sedes regionales.';

--
-- Volcado de datos para la tabla `regionales`
--

INSERT INTO `regionales` (`id_regional`, `nombre_regional`, `id_zona`) VALUES
(1, 'Popayán', 1),
(2, 'Bordo', 1),
(3, 'Santander', 1),
(4, 'Valle', 1),
(5, 'Huila', 2),
(6, 'Pasto', 2),
(7, 'Túquerres', 2),
(8, 'Nacional', 3),
(9, 'Ambienta', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_fases`
--

CREATE TABLE `seguimiento_fases` (
  `id_seguimiento` int(11) NOT NULL,
  `id_aspirante` int(11) NOT NULL,
  `id_fase` int(11) NOT NULL,
  `cumplio` tinyint(1) DEFAULT 0,
  `fecha_cumplimiento` date DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registra el avance de cada aspirante por cada una de las fases.';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `id_zona` int(11) NOT NULL,
  `nombre_zona` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Almacena las zonas geográficas como Norte y Sur.';

--
-- Volcado de datos para la tabla `zonas`
--

INSERT INTO `zonas` (`id_zona`, `nombre_zona`) VALUES
(3, 'Administrativa'),
(1, 'Norte'),
(2, 'Sur');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  ADD PRIMARY KEY (`id_aspirante`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `id_empresa` (`id_empresa`),
  ADD KEY `id_regional` (`id_regional`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `id_aspirante` (`id_aspirante`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `nombre_empresa` (`nombre_empresa`);

--
-- Indices de la tabla `empresa_regional`
--
ALTER TABLE `empresa_regional`
  ADD PRIMARY KEY (`id_empresa`,`id_regional`),
  ADD KEY `id_regional` (`id_regional`);

--
-- Indices de la tabla `fases_maestras`
--
ALTER TABLE `fases_maestras`
  ADD PRIMARY KEY (`id_fase`);

--
-- Indices de la tabla `regionales`
--
ALTER TABLE `regionales`
  ADD PRIMARY KEY (`id_regional`),
  ADD UNIQUE KEY `nombre_regional` (`nombre_regional`),
  ADD KEY `id_zona` (`id_zona`);

--
-- Indices de la tabla `seguimiento_fases`
--
ALTER TABLE `seguimiento_fases`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD UNIQUE KEY `aspirante_fase_unica` (`id_aspirante`,`id_fase`),
  ADD KEY `id_fase` (`id_fase`);

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`id_zona`),
  ADD UNIQUE KEY `nombre_zona` (`nombre_zona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  MODIFY `id_aspirante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `fases_maestras`
--
ALTER TABLE `fases_maestras`
  MODIFY `id_fase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `regionales`
--
ALTER TABLE `regionales`
  MODIFY `id_regional` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `seguimiento_fases`
--
ALTER TABLE `seguimiento_fases`
  MODIFY `id_seguimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  ADD CONSTRAINT `aspirantes_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`),
  ADD CONSTRAINT `aspirantes_ibfk_2` FOREIGN KEY (`id_regional`) REFERENCES `regionales` (`id_regional`);

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`id_aspirante`) REFERENCES `aspirantes` (`id_aspirante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_regional`
--
ALTER TABLE `empresa_regional`
  ADD CONSTRAINT `empresa_regional_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_regional_ibfk_2` FOREIGN KEY (`id_regional`) REFERENCES `regionales` (`id_regional`) ON DELETE CASCADE;

--
-- Filtros para la tabla `regionales`
--
ALTER TABLE `regionales`
  ADD CONSTRAINT `regionales_ibfk_1` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`) ON DELETE SET NULL;

--
-- Filtros para la tabla `seguimiento_fases`
--
ALTER TABLE `seguimiento_fases`
  ADD CONSTRAINT `seguimiento_fases_ibfk_1` FOREIGN KEY (`id_aspirante`) REFERENCES `aspirantes` (`id_aspirante`) ON DELETE CASCADE,
  ADD CONSTRAINT `seguimiento_fases_ibfk_2` FOREIGN KEY (`id_fase`) REFERENCES `fases_maestras` (`id_fase`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
