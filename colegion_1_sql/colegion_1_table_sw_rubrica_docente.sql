
DROP TABLE IF EXISTS `sw_rubrica_docente`;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
