
DROP TABLE IF EXISTS `sw_modalidad`;
CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `mo_nombre` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_modalidad` (`id_modalidad`, `mo_nombre`) VALUES
(1, 'SEMIPRESENCIAL'),
(2, 'BACHILLERATO VIRTUAL');
