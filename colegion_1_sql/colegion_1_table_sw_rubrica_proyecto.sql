
DROP TABLE IF EXISTS `sw_rubrica_proyecto`;
CREATE TABLE `sw_rubrica_proyecto` (
  `id_rubrica_proyecto` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) DEFAULT NULL,
  `rp_nombre` varchar(36) DEFAULT NULL,
  `rp_abreviatura` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
