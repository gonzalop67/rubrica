-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-10-2025 a las 21:47:43
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
-- Estructura de tabla para la tabla `sw_escala_referencial`
--

CREATE TABLE `sw_escala_referencial` (
  `id_escala_referencial` int(11) NOT NULL,
  `nota_cuantitativa` tinyint(2) NOT NULL,
  `ref_cualitativa` varchar(2) NOT NULL,
  `descripcion_ref` varchar(500) NOT NULL,
  `equivalencia_subnivel` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_escala_referencial`
--

INSERT INTO `sw_escala_referencial` (`id_escala_referencial`, `nota_cuantitativa`, `ref_cualitativa`, `descripcion_ref`, `equivalencia_subnivel`) VALUES
(1, 10, 'A+', 'Demuestra dominio y comprensión de las habilidades,\r\nconocimientos y procedimientos desarrollados con capacidad para aplicarlos en situaciones prácticas y de la cotidianidad, simples y complejas, de forma independiente y colectiva.', 'Destreza o aprendizaje alcanzado'),
(2, 9, 'A-', 'Demuestra comprensión de habilidades y conocimientos\r\ndesarrollados para aplicarlos en situaciones de la cotidianidad, de forma independiente y colectiva.', 'Destreza o aprendizaje alcanzado'),
(3, 8, 'B+', 'Aplica sus habilidades en situaciones comunes y predecibles, simples y no complejas de forma independiente. Evidencia habilidades para trabajar en equipo y de manera colaborativa, siguiendo instrucciones.', 'Alcanza los aprendizajes'),
(4, 7, 'B-', 'Realiza tareas y/o actividades de forma independiente y en ciertas ocasiones de forma colaborativa, sobre la base de la comprensión de los aprendizajes desarrollados. Evidencia habilidades para trabajar de manera individual y en equipo con ciertas limitaciones para seguir instrucciones.', 'Alcanza los aprendizajes'),
(5, 6, 'C+', 'Demuestra habilidades, conocimientos y procedimientos de manera integral, predominando el trabajo individual al trabajo en equipo. Requiere apoyo del equipo docente para el aprendizaje mediado.', 'Está próximo a alcanzar'),
(6, 5, 'C-', 'Resuelve tareas o actividades simples sobre la base de sus habilidades, conocimientos y procedimientos adquiridos. Evidencia habilidades básicas de trabajo en equipo. Requiere apoyo del equipo docente para el aprendizaje mediado.', 'Está próximo a alcanzar'),
(7, 4, 'D+', 'Resuelve tareas o actividades simples sobre la base de sus habilidades, conocimientos y procedimientos adquiridos guiados con el docente. Evidencia limitaciones para trabajar en equipo.', 'Está próximo a alcanzar'),
(8, 3, 'D-', 'Realiza tareas simples, demuestra dificultad para seguir instrucciones y completarlas, requiriendo la orientación frecuente del equipo docente.', 'No alcanza los\r\naprendizajes'),
(9, 2, 'E+', 'Demuestra dificultades para desarrollar tareas simples y complejas a partir de las habilidades, conocimientos y procedimientos. Requiere intervención permanente del equipo docente para la culminación de sus tareas.', 'No alcanza los aprendizajes'),
(10, 1, 'E-', 'Expone un desarrollo inicial de conocimientos, habilidades y procedimientos para aplicarlos. Requiere intervención inmediata y apoyo continuo del equipo docente para completar las tareas o actividades asignadas.', 'No alcanza los aprendizajes');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_escala_referencial`
--
ALTER TABLE `sw_escala_referencial`
  ADD PRIMARY KEY (`id_escala_referencial`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_escala_referencial`
--
ALTER TABLE `sw_escala_referencial`
  MODIFY `id_escala_referencial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
