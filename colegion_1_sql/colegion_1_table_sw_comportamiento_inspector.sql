DROP TABLE IF EXISTS `sw_comportamiento_inspector`;
CREATE TABLE `sw_comportamiento_inspector` (
  `id_comportamiento_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL,
  `nro_faltas` int(11) NOT NULL,
  `justificadas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

