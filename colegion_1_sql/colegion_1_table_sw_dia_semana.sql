
DROP TABLE IF EXISTS `sw_dia_semana`;
CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_ordinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_dia_semana` (`id_dia_semana`, `id_periodo_lectivo`, `ds_nombre`, `ds_ordinal`) VALUES
(14, 5, 'LUNES', 1),
(15, 5, 'MARTES', 2),
(16, 5, 'MIERCOLES', 3),
(17, 5, 'JUEVES', 4),
(18, 5, 'VIERNES', 5),
(19, 5, 'SABADO', 6),
(20, 6, 'LUNES', 1),
(21, 6, 'MARTES', 2),
(22, 6, 'MIÉRCOLES', 3),
(23, 6, 'JUEVES', 4),
(24, 6, 'VIERNES', 5);
