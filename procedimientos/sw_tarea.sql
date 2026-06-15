-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 07-06-2026 a las 23:07:48
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
-- Estructura de tabla para la tabla `sw_tarea`
--

CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_tarea`
--

INSERT INTO `sw_tarea` (`id`, `tarea`, `hecho`, `fecha`, `deleted_at`) VALUES
(42, 'Cambiar la interfaz de Especificaciones/Definir Escalas.', 0, '2018-08-11 21:02:38', NULL),
(6, 'Implementar el subsistema de estadísticas para el perfil de autoridad. Idea: 4 de abril de 2018', 0, '2018-04-05 13:31:33', NULL),
(5, 'Revisar el Reporte de Comportamiento de Parciales en el Perfil de Inspector. Inicio 3 de abril de 2018.', 0, '2018-04-10 15:41:27', NULL),
(35, 'REVISAR LA EDICIÓN DEL COMPORTAMIENTO DE PARCIALES EN EL PERFIL DE INSPECTOR.', 0, '2018-06-22 16:39:42', NULL),
(32, 'Implementar la opción de consultar el horario semanal de clases en el perfil de tutor. Idea: 10-jun-2018.', 0, '2018-06-14 00:22:09', NULL),
(34, 'Cambiar el reporte de comportamiento anual de Tutores. Inicio: 19-jun-2018.', 0, '2018-06-19 19:19:26', NULL),
(11, 'Falta codificar el funcionamiento de hacer clic en el casillero de Autorepresentado en Datos del Representante.', 0, '2018-04-05 15:14:09', NULL),
(38, 'REVISAR EL NOMBRE DE php_excel/reporte_quimestral.php para que incluya el nombre del quimestre.', 0, '2018-07-13 12:09:26', NULL),
(23, 'Cambiar los reportes de calificaciones para los estudiantes para que también se vea el número de faltas injustificadas por asignatura.', 0, '2018-04-27 23:35:25', NULL),
(37, 'CAMBIAR EL REPORTE ANUAL DE COMPORTAMIENTO DE INSPECCIÓN, PARA QUE SE VEA EL COMPORTAMIENTO ASIGNADO POR LOS DOCENTES. 3-jul-2018', 0, '2018-07-03 14:28:04', NULL),
(56, 'Cambiar el reporte de quimestre desde la opción de Reportes/Quimestrales en el perfil de Secretaría, para que incluya los estudiantes que no han dado el examen quimestral (S/E).', 0, '2018-10-01 11:41:06', NULL),
(58, 'Corregir el cálculo de días laborados, es decir, pasar como parámetro la fecha seleccionada, y no que calcule hasta la fecha actual.', 0, '2018-10-08 11:41:11', NULL),
(60, 'Completar la codificación de Estadísticas/Aprobados por Paralelo en el perfil Autoridad. Falta implementar el botón \"Generar Informe\".', 0, '2019-03-02 12:09:38', NULL),
(65, 'Cambiar todos los reportes para que se \"desplieguen\" las asignaturas CUALITATIVAS. Ya está PARCIALES en Secretaría.', 0, '2018-11-05 14:03:47', NULL),
(70, 'Implementar los reportes de promedios de parciales por docente y por área en el perfil de autoridad.', 0, '2019-01-17 17:05:04', NULL),
(80, 'Implementar Inscripciones al nuevo año escolar. Fase 1: Cambiar la creación de nuevos periodos lectivos (No tiene que cerrar los supletorios y validar nuevo periodo).', 0, '2019-05-19 22:26:54', NULL),
(78, 'Realizar la documentación del SIAE', 0, '2019-03-02 12:08:38', NULL),
(98, 'Cambiar el Cierre de Períodos para que sea por estudiante (ESTE CAMBIO ES DEMASIADO INTENSO!).', 0, '2020-07-20 21:38:19', NULL),
(82, 'Corregir el reporte de asistencia en el perfil de docente. Eliminar la condición de id_dia_semana de la tabla sw_hora_clase.', 0, '2019-06-04 20:22:16', NULL),
(115, 'UNIFICAR LOS CRUD PARA LAS TABLAS sw_asignatura_curso Y sw_malla_curricular, y modificar todo el código en los cuales se utilicen esas tablas.', 0, '2022-06-26 04:53:36', NULL),
(86, 'Crear una carpeta \"aplicacion\" con las subcarpetas \"controladores\", \"librerias\", \"modelos\" y \"vistas\" para organizar el código.', 0, '2019-08-19 21:44:33', NULL),
(118, 'Construir el menú mediante AJAX, de acuerdo al perfil del usuario.', 0, '2023-08-13 15:45:55', NULL),
(101, 'CREAR UNA PAGINA DE CONFIGURACIÓN INICIAL DEL SISTEMA DE EVALUACIÓN DEL PERIODO LECTIVO.', 0, '2026-03-12 12:54:00', NULL),
(124, 'Cambiar el código de Ver Reporte de Ingreso de Calificaciones Parciales para que determine si es reporte de parcial o de examen de sub periodo.', 0, '2024-01-09 21:02:33', NULL),
(103, 'Comprobar que la fecha para registro de asistencia esté en el rango del año lectivo en curso.', 0, '2020-08-17 11:53:55', NULL),
(104, 'Crear dos tablas: sw_ambito_escolar y sw_subambito_escolar y los CRUD correspondientes.', 0, '2020-08-22 15:26:45', NULL),
(106, 'PERSONALIZAR LOS COMENTARIOS POR USUARIO (MENSAJES NO LEIDOS Y LEIDOS)', 0, '2020-11-27 21:21:25', NULL),
(125, 'Cambiar la vista de calificaciones parciales para que pueda manejar el Proyecto Integrador como aporte tipo examen de sub periodo.', 1, '2024-02-21 16:18:50', NULL),
(145, 'Cambiar todas las instancias de \"sw_tipo_educacion\" por \"sw_sub_nivel_educacion\" en todos los archivos. Además, añadir el campo nivel_id de tipo INT enlazado con la tabla sw_nivel_educacion.', 0, '2026-03-19 20:46:03', NULL),
(122, 'Cerrar Periodos Lectivos accediendo al último id_periodo_lectivo de acuerdo a la modalidad (No comparando el pe_anio_inicio!)', 0, '2023-09-25 04:02:13', NULL),
(126, 'Cambiar el CRUD de Distributivos para que despliegue los paralelos de acuerdo a los periodos lectivos actuales.', 0, '2024-04-22 13:17:44', NULL),
(127, 'Cambiar el CRUD de Periodos de Evaluación para que asocie el Rango de Calificaciones con la tabla sw_equivalencia_supletorios', 1, '2024-09-15 12:25:44', NULL),
(130, 'Cambiar el reporte de calificaciones del Periodo Lectivo en el subsistema de estudiantes... ahora se trabaja con la tabla sw_periodo_evaluacion_curso', 0, '2024-09-04 19:49:06', NULL),
(129, 'Cambiar los logos del gobierno en todos los reportes en excel', 0, '2024-08-28 13:08:26', NULL),
(131, 'Cambiar el CRUD de Periodos de Evaluación en la actualización para que obtenga los datos de los rangos de calificaciones para rendir el examen supletorio, ahora debe obtenerse de la tabla sw_equivalencia_supletorios', 1, '2024-09-15 14:52:43', NULL),
(146, '¡REVISAR PORQUE NO AUTORIZA LA ACTUALIZACION DEL COMPORTAMIENTO AL AUTORIZAR EL PARCIAL!', 0, '2026-03-30 20:37:07', NULL),
(147, 'REVISAR EL COMPORTAMIENTO EN EL REPORTE DE SUPERIODO EN EL PERFIL DE TUTOR.', 0, '2026-03-30 22:28:18', NULL),
(135, 'Revisar los redondeos en el cuadro anual en el perfil de tutor.', 1, '2026-06-02 17:30:26', NULL),
(136, 'Revisar el reporte de calificaciones en el perfil de docente. Cambiar el reporte para que salga el cuadro del sub periodo lectivo cuando se consulta una evaluación sumativa.', 0, '2025-02-01 20:55:51', NULL),
(137, 'REVISAR TODO EL SUBSISTEMA DEL PERFIL: INSPECCION', 0, '2025-09-14 01:33:00', NULL),
(141, 'Cambiar todos los reportes a phpspreadsheet y a una sola plantilla... ya está el reporte de parciales en el perfil de Tutor.', 0, '2025-12-07 17:20:18', NULL),
(142, 'Cambiar a una sola plantilla el reporte de subperiodos en el perfil de Tutor.', 0, '2025-12-07 17:22:17', NULL),
(143, 'Cambiar la vista para ingresar asistencia por parte del tutor para que pueda justificar la falta (ingresar el motivo de la falta del estudiante).', 1, '2026-02-11 15:48:18', NULL),
(144, 'Revisar la vista de ingreso de calificaciones con el botón \"Guardar\"', 0, '2026-02-15 18:38:28', NULL),
(148, 'REVISAR EL REPORTE DE ASIGNATURAS CUALITATIVAS EN EL PERFIL DOCENTE.', 0, '2026-04-27 14:00:32', NULL),
(149, 'REVISAR EL BOTÓN DE EDICIÓN DE PERIODOS LECTIVOS,', 0, '2026-05-27 11:59:42', NULL),
(151, 'Implementar la función del botón \" + Nuevo menú \"', 0, '2026-06-02 16:08:35', NULL),
(152, 'Implementar el softDeletes para Tareas en la versión con adminlte2.', 0, '2026-06-02 22:43:37', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
