-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 15-07-2026 a las 16:07:02
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
-- Estructura de tabla para la tabla `sw_aporte_evaluacion`
--

CREATE TABLE `sw_aporte_evaluacion` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_tipo_aporte` int(11) NOT NULL,
  `ap_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_descripcion` varchar(256) NOT NULL,
  `ap_abreviatura` varchar(8) NOT NULL,
  `ap_tipo` tinyint(4) NOT NULL,
  `ap_estado` varchar(1) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_ponderacion` float DEFAULT NULL,
  `ap_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_paralelo_cierre`
--

CREATE TABLE `sw_aporte_paralelo_cierre` (
  `id_aporte_paralelo_cierre` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) NOT NULL,
  `ar_nombre` varchar(45) NOT NULL,
  `ar_activo` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL,
  `as_nombre` varchar(84) NOT NULL,
  `as_abreviatura` varchar(12) NOT NULL,
  `as_shortname` varchar(45) NOT NULL DEFAULT '',
  `as_curricular` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura_curso`
--

CREATE TABLE `sw_asignatura_curso` (
  `id_asignatura_curso` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ac_orden` int(11) NOT NULL,
  `ac_num_horas` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asistencia_estudiante`
--

CREATE TABLE `sw_asistencia_estudiante` (
  `id_asistencia_estudiante` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `ae_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asistencia_tutor`
--

CREATE TABLE `sw_asistencia_tutor` (
  `id_asistencia_tutor` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `at_fecha` date NOT NULL,
  `at_justificacion` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) NOT NULL,
  `id_curso_inferior` int(11) DEFAULT NULL,
  `id_curso_superior` int(11) DEFAULT NULL,
  `id_periodo_lectivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_autorizacion`
--

CREATE TABLE `sw_autorizacion` (
  `id_autorizacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `fecha_autorizacion` timestamp NOT NULL,
  `autorizado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_calificacion_comportamiento`
--

CREATE TABLE `sw_calificacion_comportamiento` (
  `id_calificacion_comportamiento` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `co_calificacion` int(11) NOT NULL,
  `co_cualitativa` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_categoria_comportamiento`
--

CREATE TABLE `sw_categoria_comportamiento` (
  `id_categoria_comportamiento` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_choices`
--

CREATE TABLE `sw_choices` (
  `id` int(11) NOT NULL,
  `id_question` int(11) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `text` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club`
--

CREATE TABLE `sw_club` (
  `id_club` int(11) NOT NULL,
  `cl_nombre` varchar(32) NOT NULL,
  `cl_abreviatura` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club_docente`
--

CREATE TABLE `sw_club_docente` (
  `id_club_docente` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_comentario_padre` int(11) NOT NULL,
  `id_usuario_para` int(11) DEFAULT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento`
--

CREATE TABLE `sw_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_indice_evaluacion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_inspector`
--

CREATE TABLE `sw_comportamiento_inspector` (
  `id_comportamiento_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_tutor`
--

CREATE TABLE `sw_comportamiento_tutor` (
  `id_comportamiento_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_config_dias_semana`
--

CREATE TABLE `sw_config_dias_semana` (
  `id_config_dias_semana` int(11) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_config_rangos_supletorios`
--

CREATE TABLE `sw_config_rangos_supletorios` (
  `id` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `nota_desde` float NOT NULL,
  `nota_hasta` float NOT NULL,
  `nota_aprobacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_cuestionario`
--

CREATE TABLE `sw_cuestionario` (
  `id_cuestionario` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `titulo` varchar(64) NOT NULL,
  `aciertos` int(11) NOT NULL DEFAULT 0,
  `errores` int(11) NOT NULL DEFAULT 0,
  `puntaje` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_cuestionario_paralelo`
--

CREATE TABLE `sw_cuestionario_paralelo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_category` int(5) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso`
--

CREATE TABLE `sw_curso` (
  `id_curso` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `cu_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cu_shortname` varchar(64) NOT NULL,
  `cu_orden` int(11) NOT NULL,
  `id_curso_superior` int(11) NOT NULL,
  `cu_abreviatura` varchar(16) NOT NULL,
  `es_bach_tecnico` tinyint(1) NOT NULL,
  `es_intensivo` tinyint(1) NOT NULL,
  `es_fin_subnivel` tinyint(1) NOT NULL,
  `quien_inserta_comp` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_genero`
--

CREATE TABLE `sw_def_genero` (
  `id_def_genero` int(11) NOT NULL,
  `dg_nombre` varchar(50) NOT NULL,
  `dg_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_def_nacionalidad`
--

CREATE TABLE `sw_def_nacionalidad` (
  `id_def_nacionalidad` int(11) NOT NULL,
  `dn_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_config_dias_semana` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `ds_nombre` varchar(64) DEFAULT NULL,
  `ds_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_distributivo`
--

CREATE TABLE `sw_distributivo` (
  `id_distributivo` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_malla_curricular` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_equivalencia_supletorios`
--

CREATE TABLE `sw_equivalencia_supletorios` (
  `id_equivalencia_supletorios` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `rango_desde` float NOT NULL,
  `rango_hasta` float NOT NULL,
  `nombre_examen` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_asignatura_cualitativa`
--

CREATE TABLE `sw_escala_asignatura_cualitativa` (
  `id_escala_asignatura_cualitativa` int(11) NOT NULL,
  `nota_minima` float NOT NULL,
  `nota_maxima` float NOT NULL,
  `equivalencia` varchar(1) NOT NULL,
  `descripcion` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_calificaciones`
--

CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ec_cualitativa` varchar(64) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(2) NOT NULL,
  `ec_color` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_cualitativa2` varchar(256) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_proyectos`
--

CREATE TABLE `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL,
  `ec_cualitativa` varchar(256) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) NOT NULL,
  `ec_abreviatura` varchar(2) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_referencial_subnivel`
--

CREATE TABLE `sw_escala_referencial_subnivel` (
  `escala_referencial_id` int(11) NOT NULL,
  `sub_nivel_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_supletorios`
--

CREATE TABLE `sw_escala_supletorios` (
  `id_escala_supletorios` int(11) NOT NULL,
  `nota_minima` float NOT NULL,
  `nota_maxima` float NOT NULL,
  `nota_final` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) NOT NULL,
  `id_tipo_educacion` int(11) NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(50) NOT NULL,
  `es_abreviatura` varchar(50) NOT NULL,
  `es_orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_tipo_documento` int(11) NOT NULL DEFAULT 1,
  `id_def_genero` int(11) NOT NULL DEFAULT 1,
  `id_def_nacionalidad` int(11) NOT NULL DEFAULT 1,
  `es_apellidos` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `es_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_nombre_completo` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_cedula` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_email` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_sector` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_direccion` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_telefono` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_club`
--

CREATE TABLE `sw_estudiante_club` (
  `id_estudiante_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `es_retirado` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_periodo_lectivo`
--

CREATE TABLE `sw_estudiante_periodo_lectivo` (
  `id_estudiante_periodo_lectivo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `es_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_retirado` varchar(1) NOT NULL DEFAULT 'N',
  `nro_matricula` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Disparadores `sw_estudiante_periodo_lectivo`
--
DELIMITER $$
CREATE TRIGGER `tg_update_estudiante_periodo_lectivo` AFTER UPDATE ON `sw_estudiante_periodo_lectivo` FOR EACH ROW UPDATE sw_rubrica_estudiante 
   SET id_paralelo = new.id_paralelo
 WHERE id_estudiante = new.id_estudiante
   AND id_paralelo = old.id_paralelo
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_promedio_parcial`
--

CREATE TABLE `sw_estudiante_promedio_parcial` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `ep_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_prom_anual`
--

CREATE TABLE `sw_estudiante_prom_anual` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ea_promedio` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_prom_quimestral`
--

CREATE TABLE `sw_estudiante_prom_quimestral` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `eq_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_evaluacion_subnivel`
--

CREATE TABLE `sw_evaluacion_subnivel` (
  `id_evaluacion_subnivel` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_exam_category`
--

CREATE TABLE `sw_exam_category` (
  `id` int(5) NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `exam_time_in_minutes` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_exam_results`
--

CREATE TABLE `sw_exam_results` (
  `id` int(5) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_type` varchar(100) NOT NULL,
  `total_question` varchar(10) NOT NULL,
  `correct_answer` varchar(10) NOT NULL,
  `wrong_answer` varchar(10) NOT NULL,
  `exam_time` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_feriado`
--

CREATE TABLE `sw_feriado` (
  `id_feriado` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `fe_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_foro`
--

CREATE TABLE `sw_foro` (
  `id_foro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fo_titulo` varchar(50) NOT NULL,
  `fo_descripcion` varchar(250) NOT NULL,
  `fo_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_habilidad_comportamiento`
--

CREATE TABLE `sw_habilidad_comportamiento` (
  `id_habilidad_comportamiento` int(11) NOT NULL,
  `id_categoria_comportamiento` int(11) NOT NULL,
  `id_tipo_educacion` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `descripcion` text NOT NULL,
  `abreviatura` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario`
--

CREATE TABLE `sw_horario` (
  `id_horario` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario_def`
--

CREATE TABLE `sw_horario_def` (
  `id_horario_def` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ho_titulo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_inicial` date NOT NULL,
  `fecha_final` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: inactivo, 1: activo',
  `nro_horas` tinyint(1) UNSIGNED DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `duracion` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario_examen`
--

CREATE TABLE `sw_horario_examen` (
  `id_horario_examen` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_examen` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `he_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `hc_nombre` varchar(12) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_dia`
--

CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) NOT NULL,
  `id_horario_def` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_inasistencia`
--

CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) NOT NULL,
  `in_abreviatura` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `in_telefono` varchar(64) NOT NULL,
  `in_regimen` varchar(16) NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `in_genero_rector` varchar(1) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_genero_vicerrector` varchar(1) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_genero_secretario` varchar(1) NOT NULL,
  `in_email` varchar(64) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_insumo`
--

CREATE TABLE `sw_insumo` (
  `id_insumo` int(11) NOT NULL,
  `in_nombre` varchar(50) NOT NULL,
  `in_descripcion` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_jornada`
--

CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) NOT NULL,
  `jo_nombre` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_leccionario`
--

CREATE TABLE `sw_leccionario` (
  `id_leccionario` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `lec_fecha` date NOT NULL,
  `lec_unidad` int(11) NOT NULL,
  `lec_tema` varchar(64) NOT NULL,
  `lec_estrategia` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_malla_curricular`
--

CREATE TABLE `sw_malla_curricular` (
  `id_malla_curricular` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `ma_horas_presenciales` int(2) NOT NULL,
  `ma_horas_autonomas` int(2) NOT NULL,
  `ma_horas_tutorias` int(2) NOT NULL,
  `ma_subtotal` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_mensajes`
--

CREATE TABLE `sw_mensajes` (
  `id` int(11) NOT NULL,
  `mensaje_id` int(11) DEFAULT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) DEFAULT NULL,
  `perfil_emisor` varchar(64) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu`
--

CREATE TABLE `sw_menu` (
  `id_menu` int(11) NOT NULL,
  `mnu_texto` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_enlace` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_link` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_nivel` int(11) NOT NULL,
  `mnu_orden` int(11) NOT NULL,
  `mnu_padre` int(11) NOT NULL,
  `mnu_publicado` int(11) NOT NULL,
  `mnu_icono` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu_perfil`
--

CREATE TABLE `sw_menu_perfil` (
  `id_perfil` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_modalidad`
--

CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `mo_nombre` varchar(48) NOT NULL,
  `mo_slug` varchar(50) NOT NULL,
  `mo_activo` tinyint(1) NOT NULL,
  `mo_orden` int(11) NOT NULL,
  `mo_intensivo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_nivel_educacion`
--

CREATE TABLE `sw_nivel_educacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_notas_proyectos`
--

CREATE TABLE `sw_notas_proyectos` (
  `id_notas_proyectos` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_notificacion`
--

CREATE TABLE `sw_notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `notificacion` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_num_aportes_def`
--

CREATE TABLE `sw_num_aportes_def` (
  `limite_inferior` int(11) NOT NULL,
  `limite_superior` int(11) NOT NULL,
  `numero_insumos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_asignatura`
--

CREATE TABLE `sw_paralelo_asignatura` (
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_slug` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pe_orden` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil_permiso`
--

CREATE TABLE `sw_perfil_permiso` (
  `id_perfil` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_estado`
--

CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) NOT NULL,
  `pe_descripcion` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion`
--

CREATE TABLE `sw_periodo_evaluacion` (
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `pe_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_abreviatura` varchar(7) NOT NULL,
  `pe_shortname` varchar(15) NOT NULL,
  `pe_principal` tinyint(4) NOT NULL,
  `pe_ponderacion` float DEFAULT NULL,
  `pe_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion_asignatura`
--

CREATE TABLE `sw_periodo_evaluacion_asignatura` (
  `asignatura_id` int(11) NOT NULL,
  `periodo_evaluacion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion_curso`
--

CREATE TABLE `sw_periodo_evaluacion_curso` (
  `id_periodo_evaluacion_curso` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `pe_ponderacion` float DEFAULT NULL,
  `pc_orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_lectivo`
--

CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `oferta_educativa_id` int(11) NOT NULL,
  `id_periodo_estado` int(11) NOT NULL,
  `id_modalidad` int(11) NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `pe_anio_inicio` int(11) NOT NULL,
  `pe_anio_fin` int(11) NOT NULL,
  `pe_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_fecha_inicio` date NOT NULL,
  `pe_fecha_fin` date NOT NULL,
  `pe_nota_minima` float NOT NULL,
  `pe_nota_maxima` float NOT NULL,
  `pe_nota_aprobacion` float NOT NULL,
  `quien_inserta_comp_id` int(1) UNSIGNED NOT NULL COMMENT '1: Tutor; 2: Docente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_lectivo_sub_periodo`
--

CREATE TABLE `sw_periodo_lectivo_sub_periodo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_sub_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `pe_ponderacion` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_nivel`
--

CREATE TABLE `sw_periodo_nivel` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_nivel_educacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_permiso`
--

CREATE TABLE `sw_permiso` (
  `id_permiso` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_persona`
--

CREATE TABLE `sw_persona` (
  `id_persona` int(11) NOT NULL,
  `dni` varchar(64) NOT NULL,
  `titulo` varchar(8) NOT NULL,
  `descripcion_titulo` varchar(96) NOT NULL,
  `primer_apellido` varchar(32) NOT NULL,
  `segundo_apellido` varchar(32) NOT NULL,
  `primer_nombre` varchar(32) NOT NULL,
  `segundo_nombre` varchar(32) NOT NULL,
  `nombre_corto` varchar(45) NOT NULL,
  `nombre_completo` varchar(64) NOT NULL,
  `genero` enum('Femenino','Masculino') NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_plan_rubrica`
--

CREATE TABLE `sw_plan_rubrica` (
  `id_plan_rubrica` int(11) NOT NULL,
  `pr_tema` varchar(50) NOT NULL,
  `pr_descripcion` varchar(250) NOT NULL,
  `pr_fecha_elab` datetime NOT NULL,
  `pr_fecha_eval` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_promedio_anual`
--

CREATE TABLE `sw_promedio_anual` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `promedio_anual` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_promocion_automatica`
--

CREATE TABLE `sw_promocion_automatica` (
  `id_promocion_automatica` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo_actual` int(11) NOT NULL,
  `id_paralelo_anterior` int(11) NOT NULL,
  `pro_estado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_questions`
--

CREATE TABLE `sw_questions` (
  `id` int(5) NOT NULL,
  `id_category` int(5) NOT NULL,
  `question_no` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `question` varchar(500) NOT NULL,
  `is_correct` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_quien_inserta_comp`
--

CREATE TABLE `sw_quien_inserta_comp` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_representante`
--

CREATE TABLE `sw_representante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `re_apellidos` varchar(32) DEFAULT NULL,
  `re_nombres` varchar(32) DEFAULT NULL,
  `re_nombre_completo` varchar(64) DEFAULT NULL,
  `re_cedula` varchar(10) DEFAULT NULL,
  `re_genero` varchar(1) DEFAULT NULL,
  `re_email` varchar(64) DEFAULT NULL,
  `re_sector` varchar(36) DEFAULT NULL,
  `re_direccion` varchar(64) DEFAULT NULL,
  `re_telefono` varchar(16) DEFAULT NULL,
  `re_observacion` varchar(256) DEFAULT NULL,
  `re_parentesco` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='					';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_respuesta`
--

CREATE TABLE `sw_respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `re_texto` text NOT NULL,
  `re_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `re_autor` int(11) NOT NULL,
  `re_perfil` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_club`
--

CREATE TABLE `sw_rubrica_club` (
  `id_rubrica_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `rc_calificacion` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_cualitativa`
--

CREATE TABLE `sw_rubrica_cualitativa` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `rc_calificacion` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_docente`
--

CREATE TABLE `sw_rubrica_docente` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rd_nombre` varchar(64) NOT NULL,
  `rd_descripcion` varchar(256) NOT NULL,
  `rd_fecha_envio` date NOT NULL,
  `rd_fecha_revision` date NOT NULL,
  `rd_observacion` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_estudiante`
--

CREATE TABLE `sw_rubrica_estudiante` (
  `id_rubrica_estudiante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL,
  `re_calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion`
--

CREATE TABLE `sw_rubrica_evaluacion` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL DEFAULT 1,
  `ru_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_abreviatura` varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_ponderacion` float NOT NULL,
  `ru_descripcion` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_personalizada`
--

CREATE TABLE `sw_rubrica_personalizada` (
  `id_rubrica_personalizada` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rp_tema` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `rp_fec_envio` date NOT NULL,
  `rp_fec_evaluacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_sub_nivel_educacion`
--

CREATE TABLE `sw_sub_nivel_educacion` (
  `id` int(11) UNSIGNED NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `es_bachillerato` int(1) UNSIGNED NOT NULL,
  `orden` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_sub_periodo_evaluacion`
--

CREATE TABLE `sw_sub_periodo_evaluacion` (
  `id_sub_periodo_evaluacion` int(11) UNSIGNED NOT NULL,
  `id_tipo_periodo` int(11) UNSIGNED NOT NULL,
  `pe_nombre` varchar(48) NOT NULL,
  `pe_abreviatura` varchar(16) NOT NULL,
  `pe_ponderacion` float NOT NULL DEFAULT 0,
  `pe_orden` int(2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_supletorios_temp`
--

CREATE TABLE `sw_supletorios_temp` (
  `id` int(11) NOT NULL,
  `id_tipo_periodo` int(1) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tarea`
--

CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tema`
--

CREATE TABLE `sw_tema` (
  `id_tema` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `te_titulo` varchar(50) NOT NULL,
  `te_descripcion` text NOT NULL,
  `te_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) NOT NULL,
  `ta_descripcion` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_documento`
--

CREATE TABLE `sw_tipo_documento` (
  `id_tipo_documento` int(11) NOT NULL,
  `td_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_educacion`
--

CREATE TABLE `sw_tipo_educacion` (
  `id_tipo_educacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `te_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `te_bachillerato` tinyint(11) NOT NULL,
  `te_orden` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL,
  `tp_slug` varchar(45) NOT NULL,
  `tipo_periodo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `persona_id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL DEFAULT 1,
  `us_titulo` varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_titulo_descripcion` varchar(96) NOT NULL,
  `us_apellidos` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
  `us_nombres` varchar(32) NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_email` varchar(64) DEFAULT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_cedula` varchar(20) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_activo` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_valor_mes`
--

CREATE TABLE `sw_valor_mes` (
  `id_valor_mes` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `vm_mes` int(11) NOT NULL,
  `vm_valor` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_versiones`
--

CREATE TABLE `sw_versiones` (
  `id` int(11) NOT NULL,
  `version` varchar(10) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD PRIMARY KEY (`id_aporte_evaluacion`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD PRIMARY KEY (`id_aporte_paralelo_cierre`),
  ADD KEY `sw_aporte_paralelo_cierre_ibfk_1` (`id_aporte_evaluacion`),
  ADD KEY `sw_aporte_paralelo_cierre_ibfk_2` (`id_paralelo`);

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `id_area` (`id_area`);

--
-- Indices de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD PRIMARY KEY (`id_asignatura_curso`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD PRIMARY KEY (`id_asistencia_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_asignatura`,`id_paralelo`,`id_inasistencia`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_inasistencia` (`id_inasistencia`),
  ADD KEY `sw_asistencia_estudiante_ibfk_1` (`id_hora_clase`);

--
-- Indices de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD PRIMARY KEY (`id_asistencia_tutor`),
  ADD KEY `fk_asistencia_tutor_paralelo` (`id_paralelo`),
  ADD KEY `id_asistencia_tutor_inasistencia` (`id_inasistencia`),
  ADD KEY `fk_asistencia_tutor_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `fk_curso_superior_periodo_lectivo_idx` (`id_periodo_lectivo`),
  ADD KEY `fk_curso_inferior_curso_idx` (`id_curso_inferior`),
  ADD KEY `fk_curso_superior_curso_idx` (`id_curso_superior`);

--
-- Indices de la tabla `sw_autorizacion`
--
ALTER TABLE `sw_autorizacion`
  ADD PRIMARY KEY (`id_autorizacion`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  ADD PRIMARY KEY (`id_calificacion_comportamiento`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_estudiante`,`id_aporte_evaluacion`,`id_asignatura`);

--
-- Indices de la tabla `sw_categoria_comportamiento`
--
ALTER TABLE `sw_categoria_comportamiento`
  ADD PRIMARY KEY (`id_categoria_comportamiento`);

--
-- Indices de la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_question` (`id_question`);

--
-- Indices de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  ADD PRIMARY KEY (`id_club_docente`);

--
-- Indices de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Indices de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD PRIMARY KEY (`id_comportamiento_inspector`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_indice_evaluacion` (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  ADD PRIMARY KEY (`id_comportamiento_tutor`);

--
-- Indices de la tabla `sw_config_dias_semana`
--
ALTER TABLE `sw_config_dias_semana`
  ADD PRIMARY KEY (`id_config_dias_semana`);

--
-- Indices de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sw_config_rangos_supletorios_ibfk_1` (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_cuestionario`
--
ALTER TABLE `sw_cuestionario`
  ADD PRIMARY KEY (`id_cuestionario`);

--
-- Indices de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- Indices de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  ADD PRIMARY KEY (`id_def_genero`);

--
-- Indices de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  ADD PRIMARY KEY (`id_def_nacionalidad`);

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `id_config_dias_semana` (`id_config_dias_semana`),
  ADD KEY `sw_dia_semana_ibfk_1` (`id_horario_def`);

--
-- Indices de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD PRIMARY KEY (`id_distributivo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_malla_curricular` (`id_malla_curricular`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `sw_distributivo_ibfk_3` (`id_paralelo`);

--
-- Indices de la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  ADD PRIMARY KEY (`id_equivalencia_supletorios`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_escala_asignatura_cualitativa`
--
ALTER TABLE `sw_escala_asignatura_cualitativa`
  ADD PRIMARY KEY (`id_escala_asignatura_cualitativa`);

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`);

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  ADD PRIMARY KEY (`id_escala_proyectos`);

--
-- Indices de la tabla `sw_escala_referencial`
--
ALTER TABLE `sw_escala_referencial`
  ADD PRIMARY KEY (`id_escala_referencial`);

--
-- Indices de la tabla `sw_escala_referencial_subnivel`
--
ALTER TABLE `sw_escala_referencial_subnivel`
  ADD KEY `escala_referencial_id` (`escala_referencial_id`),
  ADD KEY `sub_nivel_id` (`sub_nivel_id`);

--
-- Indices de la tabla `sw_escala_supletorios`
--
ALTER TABLE `sw_escala_supletorios`
  ADD PRIMARY KEY (`id_escala_supletorios`);

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_def_genero` (`id_def_genero`),
  ADD KEY `id_def_nacionalidad` (`id_def_nacionalidad`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  ADD PRIMARY KEY (`id_estudiante_club`);

--
-- Indices de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD PRIMARY KEY (`id_estudiante_periodo_lectivo`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_periodo_lectivo`,`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  ADD PRIMARY KEY (`id_evaluacion_subnivel`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_exam_category`
--
ALTER TABLE `sw_exam_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_exam_results`
--
ALTER TABLE `sw_exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD PRIMARY KEY (`id_feriado`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  ADD PRIMARY KEY (`id_foro`);

--
-- Indices de la tabla `sw_habilidad_comportamiento`
--
ALTER TABLE `sw_habilidad_comportamiento`
  ADD PRIMARY KEY (`id_habilidad_comportamiento`),
  ADD KEY `id_categoria_comportamiento` (`id_categoria_comportamiento`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_hora_clase` (`id_hora_clase`),
  ADD KEY `sw_horario_ibfk_1` (`id_asignatura`),
  ADD KEY `sw_horario_ibfk_2` (`id_dia_semana`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_horario_def` (`id_horario_def`),
  ADD KEY `sw_horario_ibfk_4` (`id_paralelo`);

--
-- Indices de la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  ADD PRIMARY KEY (`id_horario_def`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  ADD PRIMARY KEY (`id_horario_examen`);

--
-- Indices de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD PRIMARY KEY (`id_hora_clase`),
  ADD KEY `sw_hora_clase_ibfk_1` (`id_horario_def`);

--
-- Indices de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD PRIMARY KEY (`id_hora_dia`),
  ADD KEY `sw_hora_dia_ibfk_1` (`id_dia_semana`),
  ADD KEY `sw_hora_dia_ibfk_2` (`id_hora_clase`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- Indices de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  ADD PRIMARY KEY (`id_inasistencia`);

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indices de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  ADD PRIMARY KEY (`id_leccionario`),
  ADD KEY `fk_leccionario_asignatura` (`id_asignatura`),
  ADD KEY `fk_leccionario_hora_clase` (`id_hora_clase`),
  ADD KEY `fk_leccionario_paralelo` (`id_paralelo`),
  ADD KEY `fk_leccionario_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD PRIMARY KEY (`id_malla_curricular`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_mensajes`
--
ALTER TABLE `sw_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mensaje_id` (`mensaje_id`),
  ADD KEY `emisor_id` (`emisor_id`),
  ADD KEY `receptor_id` (`receptor_id`);

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indices de la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD KEY `fk_menu_perfil_perfil` (`id_perfil`),
  ADD KEY `fk_menu_perfil_menu` (`id_menu`);

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `sw_nivel_educacion`
--
ALTER TABLE `sw_nivel_educacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_notas_proyectos`
--
ALTER TABLE `sw_notas_proyectos`
  ADD PRIMARY KEY (`id_notas_proyectos`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`);

--
-- Indices de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `sw_ofertas_educativas`
--
ALTER TABLE `sw_ofertas_educativas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `sw_paralelo_jornada` (`id_jornada`),
  ADD KEY `sw_paralelo_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  ADD PRIMARY KEY (`id_paralelo_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`);

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  ADD PRIMARY KEY (`id_perfil`),
  ADD UNIQUE KEY `pe_nombre` (`pe_nombre`,`pe_slug`);

--
-- Indices de la tabla `sw_perfil_permiso`
--
ALTER TABLE `sw_perfil_permiso`
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  ADD PRIMARY KEY (`id_periodo_estado`);

--
-- Indices de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `fk_periodo_evaluacion_tipo_periodo` (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_periodo_evaluacion_asignatura`
--
ALTER TABLE `sw_periodo_evaluacion_asignatura`
  ADD KEY `asignatura_id` (`asignatura_id`),
  ADD KEY `periodo_evaluacion_id` (`periodo_evaluacion_id`);

--
-- Indices de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD PRIMARY KEY (`id_periodo_evaluacion_curso`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`),
  ADD KEY `id_institucion` (`id_institucion`),
  ADD KEY `id_modalidad` (`id_modalidad`),
  ADD KEY `id_periodo_estado` (`id_periodo_estado`),
  ADD KEY `quien_inserta_comp_id` (`quien_inserta_comp_id`),
  ADD KEY `oferta_educativa_id` (`oferta_educativa_id`);

--
-- Indices de la tabla `sw_periodo_lectivo_sub_periodo`
--
ALTER TABLE `sw_periodo_lectivo_sub_periodo`
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_sub_periodo_evaluacion` (`id_sub_periodo_evaluacion`);

--
-- Indices de la tabla `sw_periodo_nivel`
--
ALTER TABLE `sw_periodo_nivel`
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_tipo_educacion` (`id_nivel_educacion`);

--
-- Indices de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `sw_persona`
--
ALTER TABLE `sw_persona`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indices de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  ADD PRIMARY KEY (`id_plan_rubrica`);

--
-- Indices de la tabla `sw_promedio_anual`
--
ALTER TABLE `sw_promedio_anual`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  ADD PRIMARY KEY (`id_promocion_automatica`),
  ADD KEY `fk_promocion_automatica_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_category` (`id_category`);

--
-- Indices de la tabla `sw_quien_inserta_comp`
--
ALTER TABLE `sw_quien_inserta_comp`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `fk_representante_estudiante_idx` (`id_estudiante`);

--
-- Indices de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  ADD PRIMARY KEY (`id_rubrica_club`);

--
-- Indices de la tabla `sw_rubrica_docente`
--
ALTER TABLE `sw_rubrica_docente`
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_docente` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD PRIMARY KEY (`id_rubrica_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD PRIMARY KEY (`id_rubrica_evaluacion`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  ADD PRIMARY KEY (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_sub_nivel_educacion`
--
ALTER TABLE `sw_sub_nivel_educacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_sub_periodo_evaluacion`
--
ALTER TABLE `sw_sub_periodo_evaluacion`
  ADD PRIMARY KEY (`id_sub_periodo_evaluacion`),
  ADD KEY `sw_periodo_evaluacion_id_tipo_periodo_foreign` (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_supletorios_temp`
--
ALTER TABLE `sw_supletorios_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  ADD PRIMARY KEY (`id_tema`),
  ADD KEY `id_foro` (`id_foro`);

--
-- Indices de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  ADD PRIMARY KEY (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  ADD PRIMARY KEY (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- Indices de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD PRIMARY KEY (`id_tipo_educacion`),
  ADD KEY `sw_tipo_educacion_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_institucion` (`institucion_id`);

--
-- Indices de la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`),
  ADD KEY `sw_usuario_perfil_ibfk_2` (`id_perfil`);

--
-- Indices de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD PRIMARY KEY (`id_valor_mes`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_versiones`
--
ALTER TABLE `sw_versiones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  MODIFY `id_aporte_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  MODIFY `id_asistencia_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  MODIFY `id_asistencia_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_autorizacion`
--
ALTER TABLE `sw_autorizacion`
  MODIFY `id_autorizacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  MODIFY `id_calificacion_comportamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_categoria_comportamiento`
--
ALTER TABLE `sw_categoria_comportamiento`
  MODIFY `id_categoria_comportamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  MODIFY `id_club_docente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  MODIFY `id_comportamiento_inspector` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  MODIFY `id_comportamiento_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_config_dias_semana`
--
ALTER TABLE `sw_config_dias_semana`
  MODIFY `id_config_dias_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_cuestionario`
--
ALTER TABLE `sw_cuestionario`
  MODIFY `id_cuestionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  MODIFY `id_def_genero` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  MODIFY `id_def_nacionalidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  MODIFY `id_equivalencia_supletorios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_asignatura_cualitativa`
--
ALTER TABLE `sw_escala_asignatura_cualitativa`
  MODIFY `id_escala_asignatura_cualitativa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  MODIFY `id_escala_proyectos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_referencial`
--
ALTER TABLE `sw_escala_referencial`
  MODIFY `id_escala_referencial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_supletorios`
--
ALTER TABLE `sw_escala_supletorios`
  MODIFY `id_escala_supletorios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  MODIFY `id_estudiante_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  MODIFY `id_evaluacion_subnivel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_exam_category`
--
ALTER TABLE `sw_exam_category`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_exam_results`
--
ALTER TABLE `sw_exam_results`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  MODIFY `id_feriado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  MODIFY `id_foro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_habilidad_comportamiento`
--
ALTER TABLE `sw_habilidad_comportamiento`
  MODIFY `id_habilidad_comportamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  MODIFY `id_horario_def` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  MODIFY `id_horario_examen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  MODIFY `id_inasistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  MODIFY `id_leccionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_mensajes`
--
ALTER TABLE `sw_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_nivel_educacion`
--
ALTER TABLE `sw_nivel_educacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_notas_proyectos`
--
ALTER TABLE `sw_notas_proyectos`
  MODIFY `id_notas_proyectos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_ofertas_educativas`
--
ALTER TABLE `sw_ofertas_educativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  MODIFY `id_paralelo_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  MODIFY `id_periodo_estado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  MODIFY `id_periodo_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  MODIFY `id_periodo_evaluacion_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_persona`
--
ALTER TABLE `sw_persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  MODIFY `id_plan_rubrica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_promedio_anual`
--
ALTER TABLE `sw_promedio_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  MODIFY `id_promocion_automatica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_quien_inserta_comp`
--
ALTER TABLE `sw_quien_inserta_comp`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  MODIFY `id_rubrica_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  MODIFY `id_rubrica_personalizada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_sub_nivel_educacion`
--
ALTER TABLE `sw_sub_nivel_educacion`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_sub_periodo_evaluacion`
--
ALTER TABLE `sw_sub_periodo_evaluacion`
  MODIFY `id_sub_periodo_evaluacion` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_supletorios_temp`
--
ALTER TABLE `sw_supletorios_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  MODIFY `id_tipo_aporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  MODIFY `id_valor_mes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_versiones`
--
ALTER TABLE `sw_versiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD CONSTRAINT `sw_aporte_evaluacion_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Filtros para la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD CONSTRAINT `sw_asignatura_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `sw_area` (`id_area`);

--
-- Filtros para la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_1` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_2` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_4` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD CONSTRAINT `fk_asistencia_tutor_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asistencia_tutor_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `id_asistencia_tutor_inasistencia` FOREIGN KEY (`id_inasistencia`) REFERENCES `sw_inasistencia` (`id_inasistencia`);

--
-- Filtros para la tabla `sw_autorizacion`
--
ALTER TABLE `sw_autorizacion`
  ADD CONSTRAINT `sw_autorizacion_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_autorizacion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_autorizacion_ibfk_4` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_autorizacion_ibfk_5` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  ADD CONSTRAINT `sw_choices_ibfk_1` FOREIGN KEY (`id_question`) REFERENCES `sw_questions` (`id`);

--
-- Filtros para la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`),
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_2` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `sw_exam_category` (`id`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD CONSTRAINT `sw_curso_especialidad` FOREIGN KEY (`id_especialidad`) REFERENCES `sw_especialidad` (`id_especialidad`);

--
-- Filtros para la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD CONSTRAINT `sw_dia_semana_ibfk_1` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_dia_semana_ibfk_2` FOREIGN KEY (`id_config_dias_semana`) REFERENCES `sw_config_dias_semana` (`id_config_dias_semana`);

--
-- Filtros para la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD CONSTRAINT `sw_distributivo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_distributivo_ibfk_2` FOREIGN KEY (`id_malla_curricular`) REFERENCES `sw_malla_curricular` (`id_malla_curricular`),
  ADD CONSTRAINT `sw_distributivo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_distributivo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_distributivo_ibfk_5` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  ADD CONSTRAINT `sw_equivalencia_supletorios_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_escala_referencial_subnivel`
--
ALTER TABLE `sw_escala_referencial_subnivel`
  ADD CONSTRAINT `sw_escala_referencial_subnivel_ibfk_1` FOREIGN KEY (`escala_referencial_id`) REFERENCES `sw_escala_referencial` (`id_escala_referencial`),
  ADD CONSTRAINT `sw_escala_referencial_subnivel_ibfk_2` FOREIGN KEY (`sub_nivel_id`) REFERENCES `sw_sub_nivel_educacion` (`id`);

--
-- Filtros para la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD CONSTRAINT `sw_especialidad_tipo_educacion` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD CONSTRAINT `sw_estudiante_ibfk_1` FOREIGN KEY (`id_def_genero`) REFERENCES `sw_def_genero` (`id_def_genero`),
  ADD CONSTRAINT `sw_estudiante_ibfk_2` FOREIGN KEY (`id_def_nacionalidad`) REFERENCES `sw_def_nacionalidad` (`id_def_nacionalidad`),
  ADD CONSTRAINT `sw_estudiante_ibfk_3` FOREIGN KEY (`id_tipo_documento`) REFERENCES `sw_tipo_documento` (`id_tipo_documento`);

--
-- Filtros para la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_2` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_2` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_4` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_5` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`);

--
-- Filtros para la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD CONSTRAINT `sw_feriado_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_habilidad_comportamiento`
--
ALTER TABLE `sw_habilidad_comportamiento`
  ADD CONSTRAINT `sw_habilidad_comportamiento_ibfk_1` FOREIGN KEY (`id_categoria_comportamiento`) REFERENCES `sw_categoria_comportamiento` (`id_categoria_comportamiento`),
  ADD CONSTRAINT `sw_habilidad_comportamiento_ibfk_2` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD CONSTRAINT `sw_horario_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_horario_ibfk_2` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_horario_ibfk_3` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_horario_ibfk_4` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_horario_ibfk_5` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_horario_ibfk_6` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`);

--
-- Filtros para la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  ADD CONSTRAINT `sw_horario_def_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD CONSTRAINT `sw_hora_clase_ibfk_1` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD CONSTRAINT `sw_hora_dia_ibfk_1` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_2` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_3` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD CONSTRAINT `sw_institucion_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  ADD CONSTRAINT `fk_leccionario_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_leccionario_hora_clase` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `fk_leccionario_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `fk_leccionario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD CONSTRAINT `sw_malla_curricular_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_malla_curricular_ibfk_3` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_mensajes`
--
ALTER TABLE `sw_mensajes`
  ADD CONSTRAINT `sw_mensajes_ibfk_2` FOREIGN KEY (`emisor_id`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_mensajes_ibfk_3` FOREIGN KEY (`receptor_id`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD CONSTRAINT `fk_menu_perfil_menu` FOREIGN KEY (`id_menu`) REFERENCES `sw_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_menu_perfil_perfil` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_notas_proyectos`
--
ALTER TABLE `sw_notas_proyectos`
  ADD CONSTRAINT `sw_notas_proyectos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_notas_proyectos_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_notas_proyectos_ibfk_3` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`);

--
-- Filtros para la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD CONSTRAINT `sw_paralelo_curso` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_jornada` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`),
  ADD CONSTRAINT `sw_paralelo_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_perfil_permiso`
--
ALTER TABLE `sw_perfil_permiso`
  ADD CONSTRAINT `sw_perfil_permiso_ibfk_1` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`),
  ADD CONSTRAINT `sw_perfil_permiso_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `sw_permiso` (`id_permiso`);

--
-- Filtros para la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD CONSTRAINT `fk_periodo_evaluacion_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `fk_periodo_evaluacion_tipo_periodo` FOREIGN KEY (`id_tipo_periodo`) REFERENCES `sw_tipo_periodo` (`id_tipo_periodo`);

--
-- Filtros para la tabla `sw_periodo_evaluacion_asignatura`
--
ALTER TABLE `sw_periodo_evaluacion_asignatura`
  ADD CONSTRAINT `sw_periodo_evaluacion_asignatura_ibfk_1` FOREIGN KEY (`asignatura_id`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_periodo_evaluacion_asignatura_ibfk_2` FOREIGN KEY (`periodo_evaluacion_id`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Filtros para la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD CONSTRAINT `sw_periodo_evaluacion_curso_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `sw_modalidad` (`id_modalidad`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_2` FOREIGN KEY (`id_periodo_estado`) REFERENCES `sw_periodo_estado` (`id_periodo_estado`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_3` FOREIGN KEY (`quien_inserta_comp_id`) REFERENCES `sw_quien_inserta_comp` (`id`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_4` FOREIGN KEY (`oferta_educativa_id`) REFERENCES `sw_ofertas_educativas` (`id`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_5` FOREIGN KEY (`oferta_educativa_id`) REFERENCES `sw_ofertas_educativas` (`id`);

--
-- Filtros para la tabla `sw_periodo_lectivo_sub_periodo`
--
ALTER TABLE `sw_periodo_lectivo_sub_periodo`
  ADD CONSTRAINT `sw_periodo_lectivo_sub_periodo_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_periodo_lectivo_sub_periodo_ibfk_2` FOREIGN KEY (`id_sub_periodo_evaluacion`) REFERENCES `sw_sub_periodo_evaluacion` (`id_sub_periodo_evaluacion`);

--
-- Filtros para la tabla `sw_periodo_nivel`
--
ALTER TABLE `sw_periodo_nivel`
  ADD CONSTRAINT `sw_periodo_nivel_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_periodo_nivel_ibfk_2` FOREIGN KEY (`id_nivel_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  ADD CONSTRAINT `fk_promocion_automatica_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  ADD CONSTRAINT `sw_questions_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `sw_exam_category` (`id`);

--
-- Filtros para la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD CONSTRAINT `sw_representante_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD CONSTRAINT `sw_rubrica_estudiante_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_2` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Filtros para la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD CONSTRAINT `sw_tipo_educacion_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD CONSTRAINT `sw_usuario_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `sw_institucion` (`id_institucion`);

--
-- Filtros para la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_2` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD CONSTRAINT `sw_valor_mes_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
