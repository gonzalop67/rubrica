
DROP TABLE IF EXISTS `sw_club`;
CREATE TABLE `sw_club` (
  `id_club` int(11) NOT NULL,
  `cl_nombre` varchar(32) NOT NULL,
  `cl_abreviatura` varchar(6) NOT NULL,
  `cl_carga_horaria` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_club` (`id_club`, `cl_nombre`, `cl_abreviatura`, `cl_carga_horaria`) VALUES
(1, 'CIENCIAS/ECOLOGIA', 'CLCIE.', 0),
(2, 'DISEÑO Y CINE', 'CINE.', 0),
(3, 'TECNOLOGIA', 'CLTEC.', 0),
(4, 'MATEMATICA', 'CLMAT.', 0),
(5, 'DEPORTES', 'CLDEP.', 0);
