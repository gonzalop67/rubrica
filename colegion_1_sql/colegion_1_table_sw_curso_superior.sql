
DROP TABLE IF EXISTS `sw_curso_superior`;
CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_curso_superior` (`id_curso_superior`, `id_periodo_lectivo`, `cs_nombre`) VALUES
(2, 2, 'NOVENO AÑO DE EDUCACION GENERAL BASICA'),
(3, 2, 'DECIMO AÑO DE EDUCACION GENERAL BASICA'),
(4, 2, 'PRIMER AÑO DE BACHILLERATO'),
(5, 2, 'SEGUNDO AÑO DE BACHILLERATO'),
(6, 2, 'TERCER AÑO DE BACHILLERATO');
