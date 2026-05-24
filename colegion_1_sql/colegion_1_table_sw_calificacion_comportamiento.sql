
DROP TABLE IF EXISTS `sw_calificacion_comportamiento`;
CREATE TABLE `sw_calificacion_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `co_calificacion` int(11) NOT NULL,
  `co_cualitativa` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

