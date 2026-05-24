-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-10-2024 a las 19:39:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

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
-- Estructura de tabla para la tabla `sw_por_hacer`
--

CREATE TABLE `sw_por_hacer` (
  `id` int(11) NOT NULL,
  `tarea` text NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_por_hacer`
--

INSERT INTO `sw_por_hacer` (`id`, `tarea`, `hecho`, `fecha`) VALUES
(26, 'Cerrar Periodos Lectivos accediendo al último id_periodo_lectivo de acuerdo a la modalidad (No comparando el pe_anio_inicio!)', 0, '2023-09-25 04:15:00'),
(20, 'CREAR UNA PAGINA DE CONFIGURACIÓN INICIAL DEL AÑO LECTIVO.', 0, '2023-08-04 06:26:34'),
(21, 'PERSONALIZAR LOS COMENTARIOS POR USUARIO (MENSAJES NO LEIDOS Y LEIDOS)', 0, '2023-08-04 06:35:58'),
(16, 'Cambiar el login para que despliegue los periodos lectivos agrupados según la modalidad.', 1, '2023-08-04 14:22:45'),
(4, 'Arreglar el Crud de Usuarios: No actualiza bien, no controla bien si no se pasa el archivo de imagen, cambiar a la versión de lista múltiple para los perfiles del usuario.', 0, '2023-07-31 09:53:12'),
(19, 'UNIFICAR LOS CRUD PARA LAS TABLAS sw_asignatura_curso Y sw_malla_curricular, y modificar todo el código en los cuales se utilicen esas tablas.', 0, '2023-08-04 06:25:11'),
(24, 'Construir el menú mediante AJAX, de acuerdo al perfil del usuario.', 0, '2023-08-13 15:48:08'),
(25, 'Cambiar el CRUD de Usuarios para que quede igual a la versión MVC.', 1, '2023-09-25 04:14:13'),
(9, 'Cambiar el sistema de acuerdo a las reformas realizadas a la LOEI. Agosto de 2023.', 0, '2023-07-31 11:09:42'),
(11, 'Crear una carpeta \"application\" con las subcarpetas \"controllers\", \"libraries\", \"models\" y \"views\" para organizar el código.', 0, '2023-08-03 20:20:11'),
(12, 'Cambiar el campo fecha de la tabla sw_tarea de timestamp a date.', 0, '2023-08-03 20:40:44'),
(27, 'Reemplazar el CRUD Asociar Asignaturas Cursos por el CRUD Definición de Mallas Curriculares... ya no habrá la necesidad de la tabla sw_asignaturas_cursos... este cambio es intenso porque abarca cambiar en todos los lugares que se utilice la tabla sw_asignaturas_cursos', 0, '2024-09-30 06:42:50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_por_hacer`
--
ALTER TABLE `sw_por_hacer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_por_hacer`
--
ALTER TABLE `sw_por_hacer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
