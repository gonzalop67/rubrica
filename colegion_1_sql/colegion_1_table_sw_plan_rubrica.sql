
DROP TABLE IF EXISTS `sw_plan_rubrica`;
CREATE TABLE `sw_plan_rubrica` (
  `id_plan_rubrica` int(11) NOT NULL,
  `pr_tema` varchar(50) NOT NULL,
  `pr_descripcion` varchar(250) NOT NULL,
  `pr_fecha_elab` datetime NOT NULL,
  `pr_fecha_eval` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
