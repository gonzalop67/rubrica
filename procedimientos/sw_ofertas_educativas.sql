-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 26-07-2026 a las 00:07:58
-- Versión del servidor: 10.11.15-MariaDB-cll-lve
-- Versión de PHP: 8.4.15

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
-- Estructura de tabla para la tabla `sw_ofertas_educativas`
--

CREATE TABLE `sw_ofertas_educativas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `activo` tinyint(1) UNSIGNED NOT NULL,
  `orden` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `intensivo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_ofertas_educativas`
--

INSERT INTO `sw_ofertas_educativas` (`id`, `nombre`, `slug`, `activo`, `orden`, `intensivo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'EPJA TEC NO INTENSIVA', 'epja-tec-no-intensiva', 1, 1, 0, '2026-06-25 17:26:06', '2026-06-26 11:30:14', NULL),
(2, 'BGU INTENSIVO', 'bgu-intensivo', 1, 3, 1, '2026-06-25 17:28:38', '2026-07-24 20:20:48', NULL),
(3, 'EGB SUPERIOR INTENSIVA', 'egb-superior-intensiva', 1, 2, 1, '2026-06-25 17:29:57', '2026-07-24 20:20:43', NULL),
(7, 'EPJA TEC INTENSIVA', 'epja-tec-intensiva', 1, 4, 1, '2026-06-25 17:33:15', '2026-07-19 12:28:34', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_ofertas_educativas`
--
ALTER TABLE `sw_ofertas_educativas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_ofertas_educativas`
--
ALTER TABLE `sw_ofertas_educativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
