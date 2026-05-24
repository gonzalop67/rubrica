
DROP TABLE IF EXISTS `sw_periodo_estado`;
CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) NOT NULL,
  `pe_descripcion` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_periodo_estado` (`id_periodo_estado`, `pe_descripcion`) VALUES
(1, 'ACTUAL'),
(2, 'TERMINADO');
