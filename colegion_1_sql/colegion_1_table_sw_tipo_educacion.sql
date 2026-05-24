
DROP TABLE IF EXISTS `sw_tipo_educacion`;
CREATE TABLE `sw_tipo_educacion` (
  `id_tipo_educacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `te_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `te_bachillerato` tinyint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_tipo_educacion` (`id_tipo_educacion`, `id_periodo_lectivo`, `te_nombre`, `te_bachillerato`) VALUES
(1, 1, 'EDUCACION BASICA SUPERIOR', 0),
(2, 1, 'BACHILLERATO TECNICO', 1),
(3, 2, 'EDUCACION GENERAL BASICA', 0),
(4, 2, 'BACHILLERATO TECNICO', 1),
(5, 2, 'BACHILLERATO EN CIENCIAS', 1),
(6, 3, 'EDUCACION GENERAL BASICA', 0),
(7, 3, 'BACHILLERATO TECNICO', 0),
(8, 3, 'BACHILLERATO EN CIENCIAS', 0),
(9, 4, 'EDUCACION GENERAL BASICA SUPERIOR', 0),
(10, 4, 'BACHILLERATO GENERAL UNIFICADO', 0),
(11, 4, 'BACHILLERATO TECNICO', 0),
(12, 4, 'BACHILLERATO VIRTUAL', 0),
(13, 5, 'Educación General Básica Superior', 0),
(14, 5, 'Bachillerato General Unificado', 1),
(15, 5, 'Bachillerato Técnico', 1),
(17, 6, 'Educación General Básica Superior', 0),
(18, 6, 'Bachillerato Técnico', 0),
(19, 6, 'Bachillerato General Unificado', 0);
