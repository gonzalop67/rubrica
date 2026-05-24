-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-10-2024 a las 13:48:30
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
-- Estructura de tabla para la tabla `sw_menu`
--

CREATE TABLE `sw_menu` (
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `mnu_texto` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_enlace` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_link` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mnu_nivel` int(11) NOT NULL,
  `mnu_orden` int(11) NOT NULL,
  `mnu_padre` int(11) NOT NULL,
  `mnu_publicado` int(11) NOT NULL,
  `mnu_icono` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sw_menu`
--

INSERT INTO `sw_menu` (`id_menu`, `id_perfil`, `mnu_texto`, `mnu_enlace`, `mnu_link`, `mnu_nivel`, `mnu_orden`, `mnu_padre`, `mnu_publicado`, `mnu_icono`) VALUES
(2, 1, 'Menus', 'menus/index.php', 'menus', 2, 6, 146, 1, 'fa fa-circle-o'),
(12, 1, 'Perfiles', 'perfiles/view_index.php', 'perfiles', 2, 5, 146, 1, 'fa fa-circle-o'),
(13, 2, 'Calificaciones', '#', '', 1, 11, 0, 1, ''),
(18, 1, 'Usuarios', 'usuario/index.php', 'usuarios', 2, 7, 146, 1, 'fa fa-circle-o'),
(21, 3, 'Matriculación', '#', '#', 1, 22, 0, 1, 'fa fa-share-alt'),
(26, 5, 'Comportamiento', '#', '', 1, 19, 0, 1, ''),
(31, 6, 'Definiciones', '#', '', 1, 6, 0, 1, ''),
(32, 3, 'Libretación', '#', '', 1, 23, 0, 1, ''),
(37, 3, 'Reporte', '#', '', 1, 24, 0, 1, ''),
(38, 2, 'Reportes', '#', '', 1, 14, 0, 1, ''),
(39, 3, 'A Excel', '#', '', 1, 25, 0, 1, ''),
(40, 3, 'Promoción', 'promocion/index.php', 'promociones', 1, 26, 0, 1, ''),
(43, 1, 'Procesos', '#', '#', 1, 5, 0, 1, 'fa fa-lock'),
(45, 2, 'Informes', '#', '', 1, 13, 0, 1, ''),
(48, 2, 'Listas', '#', '', 1, 15, 0, 1, ''),
(52, 7, 'Reportes', '#', '', 1, 30, 0, 1, ''),
(58, 7, 'Comportamiento', '#', '', 1, 27, 0, 1, ''),
(67, 2, 'Supletorios', 'calificaciones/supletorios2.php', 'calificaciones/supletorios', 2, 3, 13, 1, ''),
(68, 2, 'Remediales', 'calificaciones/remediales.php', '', 2, 4, 13, 1, ''),
(69, 2, 'De Gracia', 'calificaciones/de_gracia.php', '', 2, 5, 13, 1, ''),
(71, 2, 'Parciales', 'calificaciones/informe_parciales2.php', '', 2, 1, 45, 1, ''),
(73, 2, 'Anuales', 'calificaciones/informe_anual.php', '', 2, 2, 45, 1, ''),
(74, 2, 'Quimestrales', 'reportes/por_periodo.php', 'reporte_docente_quimestral', 2, 1, 38, 1, ''),
(75, 2, 'Anuales', 'calificaciones/reporte_anual.php', '', 2, 2, 38, 1, ''),
(76, 2, 'Por Parcial', 'listas_estudiantes/por_parcial.php', '', 2, 1, 48, 1, ''),
(77, 2, 'Por Quimestre', 'listas_estudiantes/por_quimestre.php', '', 2, 2, 48, 1, ''),
(78, 5, 'Quimestral', 'inspeccion/comportamiento.php', '', 2, 2, 26, 1, ''),
(79, 5, 'Anual', 'inspeccion/comportamiento_anual.php', '', 2, 3, 26, 1, ''),
(97, 3, 'Validar calificaciones', 'calificaciones/validar_calificaciones.php', '', 2, 1, 32, 1, ''),
(98, 3, 'Libretación', 'reportes/libretacion.php', '', 2, 2, 32, 1, ''),
(99, 3, 'Por Asignatura', 'calificaciones/por_asignaturas.php', '', 2, 2, 37, 1, ''),
(100, 3, 'Parciales', 'calificaciones/reporte_parciales.php', '', 2, 3, 37, 1, ''),
(101, 3, 'Quimestral', 'calificaciones/procesar_promedios.php', '', 2, 4, 37, 1, ''),
(102, 3, 'Anual', 'calificaciones/promedios_anuales.php', '', 2, 5, 37, 1, ''),
(103, 3, 'De Supletorios', 'calificaciones/reporte_supletorios.php', '', 2, 6, 37, 1, ''),
(104, 3, 'De Remediales', 'calificaciones/reporte_remediales.php', '', 2, 7, 37, 1, ''),
(105, 3, 'De Exámenes de Gracia', 'calificaciones/reporte_de_gracia.php', '', 2, 8, 37, 1, ''),
(107, 3, 'Quimestrales', 'php_excel/quimestrales.php', '', 2, 2, 39, 1, ''),
(108, 3, 'Anuales', 'php_excel/anuales.php', '', 2, 3, 39, 1, ''),
(109, 3, 'Cuadro Final', 'php_excel/cuadro_final.php', '', 2, 4, 39, 1, ''),
(110, 7, 'Parciales', 'tutores/parciales2.php', '', 2, 1, 52, 1, ''),
(111, 7, 'Quimestrales', 'tutores/quimestrales2.php', '', 2, 2, 52, 1, ''),
(112, 7, 'Anuales', 'tutores/anuales2.php', '', 2, 3, 52, 1, ''),
(113, 7, 'Supletorios', 'tutores/supletorios.php', '', 2, 4, 52, 1, ''),
(114, 7, 'Remediales', 'tutores/remediales.php', '', 2, 5, 52, 1, ''),
(116, 7, 'De Parciales', 'tutores/comp_parciales2.php', '', 2, 1, 58, 1, ''),
(117, 7, 'De Quimestrales', 'tutores/comportamiento2.php', '', 2, 2, 58, 1, ''),
(119, 2, 'Proyectos', 'listas_estudiantes/proyectos.php', '', 2, 3, 48, 1, ''),
(120, 1, 'Cerrar Periodos', 'aportes_evaluacion/cerrar_periodos2.php', 'cierre_periodos', 2, 1, 43, 1, 'fa fa-circle-o'),
(123, 3, 'Paralelos', 'matriculacion/index2.php', 'matriculacion', 2, 1, 21, 1, 'fa fa-university'),
(127, 6, 'Horarios', '#', '', 1, 7, 0, 1, ''),
(128, 6, 'Definir Días de la Semana', 'dias_semana/index.php', 'dias_semana', 2, 1, 127, 1, ''),
(129, 6, 'Definir Horas Clase', 'hora_clase/index.php', 'horas_clase', 2, 2, 127, 1, ''),
(130, 6, 'Definir Horario Semanal', 'horarios/view_horario_semanal.php', 'horarios', 2, 4, 127, 1, ''),
(132, 6, 'Reportes', '#', '', 1, 8, 0, 1, ''),
(134, 6, 'Quimestral', 'calificaciones/reporte_quimestral_autoridad.php', '', 2, 2, 132, 1, ''),
(135, 6, 'Anual', 'calificaciones/promedios_anuales_autoridad.php', '', 2, 3, 132, 1, ''),
(138, 2, 'Asistencia', '#', '', 1, 12, 0, 1, ''),
(140, 3, 'Cuadro Remediales', 'php_excel/cuadro_remediales.php', '', 2, 5, 39, 1, ''),
(141, 3, 'Cuadro De Gracia', 'php_excel/cuadro_de_gracia.php', '', 2, 6, 39, 1, ''),
(143, 7, 'De Gracia', 'tutores/de_gracia.php', '', 2, 6, 52, 1, ''),
(144, 5, 'Parciales', 'inspeccion/comportamiento_parciales.php', '', 2, 1, 26, 1, ''),
(146, 1, 'Administración', '#', '#', 1, 1, 0, 1, 'fa fa-gear'),
(159, 1, 'Definiciones', '#', '#', 1, 2, 0, 1, 'fa fa-cogs'),
(160, 1, 'Niveles de Educación', 'tipo_educacion/index2.php', 'tipos_educacion', 2, 2, 159, 1, 'fa fa-university'),
(161, 1, 'Especialidades', 'especialidades/index2.php', 'especialidades', 2, 3, 159, 1, 'fa fa-university'),
(162, 1, 'Cursos', 'cursos/index2.php', 'cursos', 2, 4, 159, 1, 'fa fa-university'),
(163, 1, 'Paralelos', 'paralelos/index2.php', 'paralelos', 2, 5, 159, 1, 'fa fa-university'),
(164, 1, 'Areas', 'areas/index2.php', 'areas', 2, 6, 159, 1, 'fa fa-university'),
(165, 1, 'Asignaturas', 'asignaturas/index2.php', 'asignaturas', 2, 7, 159, 1, 'fa fa-university'),
(166, 1, 'Asociar', '#', '#', 1, 4, 0, 1, 'fa fa-share-alt'),
(167, 1, 'Asignaturas Cursos', 'asignaturas_cursos/index2.php', 'asignaturas_cursos', 2, 1, 166, 1, 'fa fa-circle-o'),
(171, 1, 'Paralelos Tutores', 'paralelos_tutores/index2.php', 'paralelos_tutores', 2, 3, 166, 1, 'fa fa-circle-o'),
(176, 1, 'Por Hacer', 'por_hacer/index2.php', 'tareas', 2, 1, 146, 1, 'fa fa-list-alt'),
(179, 13, 'Parciales', 'calificaciones/reporte_parciales.php', 'parciales', 2, 1, 189, 1, ''),
(180, 13, 'Quimestrales', 'calificaciones/procesar_promedios.php', 'quimestrales', 2, 2, 189, 1, ''),
(181, 13, 'Anuales', 'calificaciones/promedios_anuales.php', 'anuales', 2, 3, 189, 1, ''),
(189, 13, 'Reportes', '#', '', 1, 1, 0, 1, ''),
(195, 6, 'Definir Inasistencias', 'inasistencias/index.php', '', 2, 5, 127, 1, ''),
(197, 2, 'Leccionario', 'horarios/view_asistencia.php', '', 2, 1, 138, 1, ''),
(198, 2, 'Ver Horario', 'horarios/ver_horario_docente.php', '', 2, 2, 138, 1, ''),
(200, 1, 'Períodos Lectivos', 'periodos_lectivos/index2.php', 'periodos_lectivos', 2, 4, 146, 1, ''),
(201, 6, 'Malla Curricular', 'malla_curricular/index.php', 'mallas_curriculares', 2, 1, 31, 1, ''),
(202, 6, 'Distributivo', 'distributivo/index.php', 'distributivos', 2, 2, 31, 1, ''),
(203, 6, 'Asociar Dia-Hora', 'asociar_dia_hora/index.php', 'horas_dia', 2, 3, 127, 1, ''),
(204, 5, 'Horarios', '#', '', 1, 20, 0, 1, ''),
(205, 5, 'Leccionario', 'horarios/leccionario.php', '', 2, 1, 204, 1, ''),
(206, 6, 'Estadísticas', '#', '', 1, 10, 0, 1, ''),
(207, 6, 'Aprobados por Paralelo', 'estadisticas/aprobados_paralelo.php', 'estadisticas/aprobados_paralelo', 2, 1, 206, 1, ''),
(208, 3, 'Padrón Electoral', 'padron_electoral/index.php', '', 2, 1, 39, 1, ''),
(210, 7, 'Consultas', '#', '', 1, 29, 0, 1, ''),
(211, 7, 'Lista de Docentes', 'tutores/lista_docentes.php', '', 2, 1, 210, 1, ''),
(212, 7, 'Horario de Clases', 'tutores/horario_clases.php', '', 2, 2, 210, 1, ''),
(213, 6, 'Consultas', '#', '', 1, 9, 0, 1, ''),
(215, 6, 'Horarios de clase', 'autoridad/horario_clases.php', 'horarios_clases', 2, 2, 213, 1, ''),
(216, 5, 'Docentes', 'inspeccion/horario_docentes.php', '', 2, 2, 204, 1, ''),
(217, 5, 'Definiciones', '#', '', 1, 17, 0, 1, ''),
(218, 5, 'Valor del mes', 'valor_del_mes/index.php', '', 2, 1, 217, 1, ''),
(219, 5, 'Consultas', '#', '', 1, 21, 0, 1, ''),
(220, 5, 'Lista de docentes', 'autoridad/lista_docentes.php', '', 2, 1, 219, 1, ''),
(221, 5, 'Horarios de clase', 'autoridad/horario_clases.php', '', 2, 2, 219, 1, ''),
(222, 5, 'Feriados', 'feriados/index.php', '', 2, 2, 217, 1, ''),
(225, 5, 'Asistencia', '#', '', 1, 18, 0, 1, ''),
(228, 7, 'Asistencia', '#', '', 1, 28, 0, 1, ''),
(229, 7, 'Justificar Faltas', 'tutores/view_justificar.php', '', 2, 1, 228, 1, ''),
(230, 3, 'Nómina de Matriculados', 'php_excel/nomina_matriculados.php', 'reporte_nomina_matriculados', 2, 1, 37, 1, ''),
(231, 1, 'Institución', 'institucion/index.php', 'instituciones', 2, 2, 146, 1, ''),
(232, 1, 'Modalidades', 'modalidades/index.php', 'modalidades', 2, 3, 146, 1, ''),
(233, 1, 'Curso Superior', 'asociar_curso_superior/index.php', 'cursos_superiores', 2, 2, 166, 1, ''),
(235, 1, 'Educación Inicial', 'educacion_inicial/index.php', '', 2, 1, 159, 0, ''),
(236, 2, 'Tareas', 'tareas/index.php', '', 2, 1, 13, 0, ''),
(239, 2, 'Parciales', 'calificaciones/index.php', 'calificaciones/parciales', 2, 2, 13, 1, ''),
(240, 1, 'Paralelos Inspectores', 'paralelos_inspectores/index2.php', 'paralelos_inspectores', 2, 4, 166, 1, ''),
(241, 1, 'Especificaciones', '#', '#', 1, 3, 0, 1, ''),
(242, 1, 'Períodos de Evaluación', 'periodos_evaluacion/index2.php', 'periodos_evaluacion', 2, 1, 241, 1, ''),
(243, 1, 'Aportes de Evaluación', 'aportes_evaluacion/index2.php', 'aportes_evaluacion', 2, 2, 241, 1, ''),
(244, 1, 'Insumos de Evaluación', 'rubricas_evaluacion/index2.php', 'insumos_evaluacion', 2, 3, 241, 1, ''),
(245, 1, 'Escalas de Calificaciones', 'escalas/index2.php', 'escalas_calificaciones', 2, 4, 241, 1, ''),
(246, 6, 'Parciales', 'calificaciones/reporte_parciales_autoridad.php', '', 2, 1, 132, 1, ''),
(247, 6, 'Lista de docentes', 'autoridad/lista_docentes.php', 'lista_docentes', 2, 1, 213, 1, ''),
(250, 1, 'Promoción Paralelos', 'promocion_paralelos/index.php', '', 2, 5, 166, 1, ''),
(251, 1, 'Promoción Automática', 'promocion_automatica/index.php', '', 2, 2, 43, 1, ''),
(252, 2, 'Asistencia', 'reportes/asistencia_docente.php', '', 2, 3, 38, 1, ''),
(257, 3, 'Exportar', 'matriculacion/exportar.php', 'exportar', 2, 2, 21, 1, ''),
(258, 3, 'Importar', 'matriculacion/importar.php', 'importar', 2, 3, 21, 1, ''),
(259, 2, 'Cuestionarios', '#', 'cuestionarios', 1, 16, 0, 1, ''),
(260, 2, 'Categorías', 'categorias/index.php', 'categorias', 2, 1, 259, 1, ''),
(261, 2, 'Preguntas', 'preguntas/index.php', 'preguntas', 2, 2, 259, 1, ''),
(262, 2, 'Vista Preliminar', 'cuestionarios/preliminar.php', 'cuestionarios/preliminar', 2, 3, 259, 1, ''),
(263, 2, 'Asociar Paralelo', 'cuestionarios/asociar_paralelo.php', 'cuestionarios/asociar_paralelo', 2, 4, 259, 1, ''),
(269, 6, 'Rendimiento Académico', '', 'estadisticas/rendimiento_academico', 2, 2, 206, 1, ''),
(271, 1, 'Periodos Eval Cursos', 'periodos_evaluacion_cursos/index.php', '', 2, 6, 166, 1, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
