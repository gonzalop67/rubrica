-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-07-2026 a las 14:08:44
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
-- Base de datos: `siae_2026`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `slug`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Crear Usuario', 'crear-usuario', 'Puede insertar nuevos usuarios.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(2, 'Actualizar Usuario', 'actualizar-usuario', 'Puede actualizar usuarios.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(3, 'Eliminar Usuario', 'eliminar-usuario', 'Puede eliminar usuarios.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(4, 'Listar Usuarios', 'listar-usuarios', 'Puede ver el listado de usuarios.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(5, 'Crear Rol', 'crear-rol', 'Puede insertar nuevos roles.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(6, 'Actualizar Rol', 'actualizar-rol', 'Puede actualizar roles.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(7, 'Eliminar Rol', 'eliminar-rol', 'Puede eliminar roles.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(8, 'Listar Roles', 'listar-roles', 'Puede ver el listado de roles.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(9, 'Crear Permiso', 'crear-permiso', 'Puede crear nuevos permisos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(10, 'Actualizar Permiso', 'actualizar-permiso', 'Puede actualizar permisos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(11, 'Eliminar Permiso', 'eliminar-permiso', 'Puede eliminar permisos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(12, 'Listar Permisos', 'listar-permisos', 'Puede ver el listado de permisos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(13, 'Ver Módulos del Administrador', 'ver-modulos-del-administrador', 'Sólo el rol con este permiso tendrá acceso a este menú.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(14, 'Listar Menús', 'listar-menus', 'Puede ver el listado de menús.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(15, 'Admin Dashboard', 'admin-dashboard', 'Permite ver el dashboard del panel del Administrador.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(16, 'Ver Módulos Académico', 'ver-modulos-academico', 'Permite ver el menú de los módulos de administración académica.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(17, 'Listar Niveles Académicos', 'listar-niveles-academicos', 'Permite ver el menú para el crud de niveles académicos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(18, 'Listar Subniveles Académicos', 'listar-subniveles-academicos', 'Permite ver el menú para el crud de subniveles académicos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(19, 'Listar Cursos', 'listar-cursos', 'Permite ver el menú para el crud de cursos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(20, 'Listar Asignaturas', 'listar-asignaturas', 'Permite ver el menú para el crud de asignaturas.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL),
(21, 'Definición de Periodos Académicos', 'definicion-de-periodos-academicos', 'Permite ver el menú para la interfaz de definición de los periodos académicos.', '2026-07-21 14:10:17', '2026-07-21 14:10:17', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
