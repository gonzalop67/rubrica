
DROP TABLE IF EXISTS `sw_indice_evaluacion_def`;
CREATE TABLE `sw_indice_evaluacion_def` (
  `id_indice_evaluacion` int(11) NOT NULL,
  `ie_descripcion` varchar(64) NOT NULL,
  `ie_abreviatura` varchar(8) NOT NULL,
  `ie_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_indice_evaluacion_def` (`id_indice_evaluacion`, `ie_descripcion`, `ie_abreviatura`, `ie_orden`) VALUES
(1, 'CUMPLE CON VALORES', 'VALORES', 1),
(2, 'CUMPLE CON LAS NORMAS INSTITUCIONALES', 'NORMAS', 2),
(3, 'ASISTE PUNTUALMENTE A LA INSTITUCION', 'PUNTUAL', 3),
(4, 'CUMPLE LAS NORMAS DE PRESENTACION', 'PRESENT', 4);
