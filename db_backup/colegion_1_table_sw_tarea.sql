
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tarea`
--

DROP TABLE IF EXISTS `sw_tarea`;
CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_tarea`
--

INSERT INTO `sw_tarea` (`id`, `tarea`, `hecho`, `fecha`) VALUES
(42, 'Cambiar la interfaz de Especificaciones/Definir Escalas.', 0, '2018-08-11 21:02:38'),
(6, 'Implementar el subsistema de estadísticas para el perfil de autoridad. Idea: 4 de abril de 2018', 0, '2018-04-05 13:31:33'),
(5, 'Revisar el Reporte de Comportamiento de Parciales en el Perfil de Inspector. Inicio 3 de abril de 2018.', 0, '2018-04-10 15:41:27'),
(35, 'REVISAR LA EDICIÓN DEL COMPORTAMIENTO DE PARCIALES EN EL PERFIL DE INSPECTOR.', 0, '2018-06-22 16:39:42'),
(32, 'Implementar la opción de consultar el horario semanal de clases en el perfil de tutor. Idea: 10-jun-2018.', 0, '2018-06-14 00:22:09'),
(34, 'Cambiar el reporte de comportamiento anual de Tutores. Inicio: 19-jun-2018.', 0, '2018-06-19 19:19:26'),
(11, 'Falta codificar el funcionamiento de hacer clic en el casillero de Autorepresentado en Datos del Representante.', 0, '2018-04-05 15:14:09'),
(38, 'REVISAR EL NOMBRE DE php_excel/reporte_quimestral.php para que incluya el nombre del quimestre.', 0, '2018-07-13 12:09:26'),
(23, 'Cambiar los reportes de calificaciones para los estudiantes para que también se vea el número de faltas injustificadas por asignatura.', 0, '2018-04-27 23:35:25'),
(37, 'CAMBIAR EL REPORTE ANUAL DE COMPORTAMIENTO DE INSPECCIÓN, PARA QUE SE VEA EL COMPORTAMIENTO ASIGNADO POR LOS DOCENTES. 3-jul-2018', 0, '2018-07-03 14:28:04'),
(56, 'Cambiar el reporte de quimestre desde la opción de Reportes/Quimestrales en el perfil de Secretaría, para que incluya los estudiantes que no han dado el examen quimestral (S/E).', 0, '2018-10-01 11:41:06'),
(58, 'Corregir el cálculo de días laborados, es decir, pasar como parámetro la fecha seleccionada, y no que calcule hasta la fecha actual.', 0, '2018-10-08 11:41:11'),
(60, 'Completar la codificación de Estadísticas/Aprobados por Paralelo en el perfil Autoridad. Falta implementar el botón \"Generar Informe\".', 0, '2019-03-02 12:09:38'),
(65, 'Cambiar todos los reportes para que se \"desplieguen\" las asignaturas CUALITATIVAS. Ya está PARCIALES en Secretaría.', 0, '2018-11-05 14:03:47'),
(70, 'Implementar los reportes de promedios de parciales por docente y por área en el perfil de autoridad.', 0, '2019-01-17 17:05:04'),
(80, 'Implementar Inscripciones al nuevo año escolar. Fase 1: Cambiar la creación de nuevos periodos lectivos (No tiene que cerrar los supletorios y validar nuevo periodo).', 0, '2019-05-19 22:26:54'),
(78, 'Realizar la documentación del SIAE', 0, '2019-03-02 12:08:38'),
(98, 'Cambiar el Cierre de Períodos para que sea por estudiante (ESTE CAMBIO ES DEMASIADO INTENSO!).', 0, '2020-07-20 21:38:19'),
(82, 'Corregir el reporte de asistencia en el perfil de docente. Eliminar la condición de id_dia_semana de la tabla sw_hora_clase.', 0, '2019-06-04 20:22:16'),
(115, 'UNIFICAR LOS CRUD PARA LAS TABLAS sw_asignatura_curso Y sw_malla_curricular, y modificar todo el código en los cuales se utilicen esas tablas.', 0, '2022-06-26 04:53:36'),
(86, 'Crear una carpeta \"aplicacion\" con las subcarpetas \"controladores\", \"librerias\", \"modelos\" y \"vistas\" para organizar el código.', 0, '2019-08-19 21:44:33'),
(118, 'Construir el menú mediante AJAX, de acuerdo al perfil del usuario.', 0, '2023-08-13 15:45:55'),
(101, 'CREAR UNA PAGINA DE CONFIGURACIÓN INICIAL DEL AÑO LECTIVO.', 0, '2022-06-26 04:47:16'),
(124, 'Cambiar el código de Ver Reporte de Ingreso de Calificaciones Parciales para que determine si es reporte de parcial o de examen de sub periodo.', 0, '2024-01-09 21:02:33'),
(103, 'Comprobar que la fecha para registro de asistencia esté en el rango del año lectivo en curso.', 0, '2020-08-17 11:53:55'),
(104, 'Crear dos tablas: sw_ambito_escolar y sw_subambito_escolar y los CRUD correspondientes.', 0, '2020-08-22 15:26:45'),
(106, 'PERSONALIZAR LOS COMENTARIOS POR USUARIO (MENSAJES NO LEIDOS Y LEIDOS)', 0, '2020-11-27 21:21:25'),
(125, 'Cambiar la vista de calificaciones parciales para que pueda manejar el Proyecto Integrador como aporte tipo examen de sub periodo.', 1, '2024-02-21 16:18:50'),
(121, 'Asociar Horas Dia en la tabla sw_hora_dia cada vez que se crea una nueva hora clase.', 0, '2023-09-24 08:17:50'),
(122, 'Cerrar Periodos Lectivos accediendo al último id_periodo_lectivo de acuerdo a la modalidad (No comparando el pe_anio_inicio!)', 0, '2023-09-25 04:02:13'),
(126, 'Cambiar el CRUD de Distributivos para que despliegue los paralelos de acuerdo a los periodos lectivos actuales.', 0, '2024-04-22 13:17:44'),
(127, 'Cambiar el CRUD de Periodos de Evaluación para que asocie el Rango de Calificaciones con la tabla sw_equivalencia_supletorios', 1, '2024-09-15 12:25:44'),
(130, 'Cambiar el reporte de calificaciones del Periodo Lectivo en el subsistema de estudiantes... ahora se trabaja con la tabla sw_periodo_evaluacion_curso', 0, '2024-09-04 19:49:06'),
(129, 'Cambiar los logos del gobierno en todos los reportes en excel', 0, '2024-08-28 13:08:26'),
(131, 'Cambiar el CRUD de Periodos de Evaluación en la actualización para que obtenga los datos de los rangos de calificaciones para rendir el examen supletorio, ahora debe obtenerse de la tabla sw_equivalencia_supletorios', 1, '2024-09-15 14:52:43'),
(132, 'Cambiar el CRUD de Periodos de Evaluación en la actualización para que actualice los rangos de los supletorios en la tabla sw_equivalencia_supletorios', 0, '2024-09-15 14:53:52'),
(133, 'Revisar el CRUD para definir días de la semana... no ingresa o actualiza el campo ds_ordinal de la tabla sw_dia_semana', 0, '2024-09-23 20:31:21');
