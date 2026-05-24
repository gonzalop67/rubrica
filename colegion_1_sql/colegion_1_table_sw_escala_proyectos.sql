
DROP TABLE IF EXISTS `sw_escala_proyectos`;
CREATE TABLE `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL,
  `ec_cualitativa` varchar(256) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) NOT NULL,
  `ec_abreviatura` varchar(2) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_escala_proyectos` (`id_escala_proyectos`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`, `ec_abreviatura`, `ec_correlativa`) VALUES
(1, 'Demuestra destacado desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un excelente aporte a su formación integral.', '9.00 - 10.00', 9, 10, 1, 'EXCELENTE', 'EX', 4),
(2, 'Demuestra muy buen desempeño en cada fase del desarrollo del proyecto escolar lo que constituye un aporte a su formación integral.', '7.00 - 8.99', 7, 8.99, 2, 'MUY BUENA', 'MB', 3),
(3, 'Demuestra buen desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye a su formación integral.', '4.01 - 6.99', 4.01, 6.99, 3, 'BUENA', 'B', 2),
(4, 'Demuestra regular desempeño en cada fase del desarrollo del proyecto escolar lo que contribuye escasamente a su formación integral.', '<= 4', 0.01, 4, 4, 'REGULAR', 'R', 1),
(5, 'Sin notas.', '0', 0, 0, 5, 'SIN NOTAS', 'SN', 0);
