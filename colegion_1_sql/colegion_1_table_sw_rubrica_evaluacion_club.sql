
DROP TABLE IF EXISTS `sw_rubrica_evaluacion_club`;
CREATE TABLE `sw_rubrica_evaluacion_club` (
  `id_rubrica_evaluacion_club` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `rc_nombre` varchar(24) NOT NULL,
  `rc_abreviatura` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
