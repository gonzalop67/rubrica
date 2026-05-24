
DROP TABLE IF EXISTS `sw_escala_calificaciones`;
CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ec_cualitativa` varchar(64) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_escala_calificaciones` (`id_escala_calificaciones`, `id_periodo_lectivo`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`) VALUES
(1, 1, 'Supera los aprendizajes requeridos.', '10', 10, 10, 1, 'S'),
(2, 1, 'Domina los aprendizajes requeridos.', '9', 9, 9.999, 2, 'D'),
(3, 1, 'Alcanza los aprendizajes requeridos.', '7-8', 7, 8.999, 3, 'A'),
(4, 1, 'Está próximo a alcanzar los aprendizajes requeridos.', '5-6', 4.001, 6.999, 4, 'E'),
(5, 1, 'No alcanza los aprendizajes requeridos.', '<= 4', 0.01, 4, 5, 'N'),
(6, 2, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(7, 2, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(8, 2, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(9, 2, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(10, 3, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(11, 3, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(12, 3, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(13, 3, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(14, 4, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(15, 4, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(16, 4, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(17, 4, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(18, 5, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(19, 5, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(20, 5, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(21, 5, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N'),
(22, 6, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'D'),
(23, 6, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'A'),
(24, 6, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'E'),
(25, 6, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'N');
