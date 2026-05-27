-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2026 a las 16:08:41
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
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `sw_permiso` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `sw_permiso` (`id_permiso`, `nombre`, `slug`, `descripcion`) VALUES
(1, 'Crear Usuario', 'crear-usuario', 'Puede insertar nuevos usuarios'),
(2, 'Actualizar Usuario', 'actualizar-usuario', 'Puede actualizar usuarios'),
(3, 'Eliminar Usuario', 'eliminar-usuario', 'Puede eliminar usuarios'),
(4, 'Ver Usuarios', 'usuarios', 'Puede ver el listado de usuarios'),
(5, 'Crear Rol', 'crear-rol', 'Puede insertar nuevos roles'),
(6, 'Actualizar Rol', 'actualizar-rol', 'Puede actualizar roles'),
(7, 'Eliminar Rol', 'eliminar-rol', 'Puede eliminar roles'),
(8, 'Ver Roles', 'roles', 'Puede ver el listado de roles'),
(9, 'Crear Permiso', 'crear-permiso', 'Puede crear nuevos permisos'),
(10, 'Actualizar Permiso', 'actualizar-permiso', 'Puede actualizar permisos'),
(11, 'Eliminar Permiso', 'eliminar-permiso', 'Puede eliminar permisos'),
(12, 'Ver Permisos', 'permisos', 'Puede ver el listado de permisos');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `sw_permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `sw_permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
