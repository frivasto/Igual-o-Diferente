-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 19-02-2012 a las 18:19:41
-- Versión del servidor: 5.5.16
-- Versión de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `juego_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `decision`
--

CREATE TABLE IF NOT EXISTS `decision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `relacionmesavideo_id` int(11) DEFAULT NULL,
  `tiempo` time DEFAULT NULL,
  `respuesta` tinyint(1) DEFAULT '0',
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `relacionmesavideo_id_idx` (`relacionmesavideo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiqueta`
--

CREATE TABLE IF NOT EXISTS `etiqueta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `texto` varchar(255) DEFAULT NULL,
  `ponderacion` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instancia_etiqueta`
--

CREATE TABLE IF NOT EXISTS `instancia_etiqueta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `relacionmesavideo_id` int(11) DEFAULT NULL,
  `texto` varchar(255) DEFAULT NULL,
  `tiempo` time DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `relacionmesavideo_id_idx` (`relacionmesavideo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intervalo`
--

CREATE TABLE IF NOT EXISTS `intervalo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `video_id` int(11) DEFAULT NULL,
  `inicio` time DEFAULT NULL,
  `fin` time DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `video_id_idx` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugador`
--

CREATE TABLE IF NOT EXISTS `jugador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE IF NOT EXISTS `mesa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `jugador1_id` int(11) DEFAULT NULL,
  `jugador2_id` int(11) DEFAULT NULL,
  `tiempo` time DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `jugador2_id_idx` (`jugador2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `relacion_mesa_video`
--

CREATE TABLE IF NOT EXISTS `relacion_mesa_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `mesa_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `jugador_id` int(11) DEFAULT NULL,
  `intervalo_id` int(11) DEFAULT NULL,
  `respuesta_real` tinyint(1) DEFAULT '0',
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `mesa_id_idx` (`mesa_id`),
  KEY `video_id_idx` (`video_id`),
  KEY `jugador_id_idx` (`jugador_id`),
  KEY `intervalo_id_idx` (`intervalo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `eliminado` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `decision`
--
ALTER TABLE `decision`
  ADD CONSTRAINT `decision_relacionmesavideo_id_relacion_mesa_video_id` FOREIGN KEY (`relacionmesavideo_id`) REFERENCES `relacion_mesa_video` (`id`);

--
-- Filtros para la tabla `instancia_etiqueta`
--
ALTER TABLE `instancia_etiqueta`
  ADD CONSTRAINT `instancia_etiqueta_relacionmesavideo_id_relacion_mesa_video_id` FOREIGN KEY (`relacionmesavideo_id`) REFERENCES `relacion_mesa_video` (`id`);

--
-- Filtros para la tabla `intervalo`
--
ALTER TABLE `intervalo`
  ADD CONSTRAINT `intervalo_video_id_video_id` FOREIGN KEY (`video_id`) REFERENCES `video` (`id`);

--
-- Filtros para la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD CONSTRAINT `mesa_jugador2_id_jugador_id` FOREIGN KEY (`jugador2_id`) REFERENCES `jugador` (`id`);

--
-- Filtros para la tabla `relacion_mesa_video`
--
ALTER TABLE `relacion_mesa_video`
  ADD CONSTRAINT `relacion_mesa_video_intervalo_id_intervalo_id` FOREIGN KEY (`intervalo_id`) REFERENCES `intervalo` (`id`),
  ADD CONSTRAINT `relacion_mesa_video_jugador_id_jugador_id` FOREIGN KEY (`jugador_id`) REFERENCES `jugador` (`id`),
  ADD CONSTRAINT `relacion_mesa_video_mesa_id_mesa_id` FOREIGN KEY (`mesa_id`) REFERENCES `mesa` (`id`),
  ADD CONSTRAINT `relacion_mesa_video_video_id_video_id` FOREIGN KEY (`video_id`) REFERENCES `video` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
