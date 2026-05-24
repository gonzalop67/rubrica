
DROP TABLE IF EXISTS `sw_inasistencia`;
CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) NOT NULL,
  `in_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_inasistencia` (`id_inasistencia`, `in_nombre`, `in_abreviatura`) VALUES
(1, 'Atraso', 'A'),
(2, 'Fuga', 'F'),
(3, 'falta Injustificada', 'I'),
(4, 'falta Justificada', 'J'),
(5, 'Permiso', 'P'),
(6, 'aSiste', 'S');
