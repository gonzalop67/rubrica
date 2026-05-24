-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-05-2020 a las 00:39:31
-- Versión del servidor: 10.1.44-MariaDB-cll-lve
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_nom_rector`, `in_nom_vicerrector`, `in_nom_secretario`, `in_url`, `in_logo`, `in_amie`, `in_ciudad`) VALUES
(1, 'UNIDAD EDUCATIVA PCEI FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256311/2254818', 'DR. RAMIRO CASTILLO', 'Lic. Rómulo Mejía', 'Lic. Alicia Salazar O.', 'http://colegionocturnosalamanca.com', 'logo_salamanca.gif', '17H00215', 'Quito D.M.');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
