
DROP TABLE IF EXISTS `sw_periodo_lectivo`;

CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_estado` int(11) NOT NULL,
  `id_modalidad` int(11) NOT NULL,
  `pe_anio_inicio` int(5) NOT NULL,
  `pe_anio_fin` int(5) NOT NULL,
  `pe_estado` char(1), 
  `pe_fecha_inicio` date NOT NULL,
  `pe_fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

