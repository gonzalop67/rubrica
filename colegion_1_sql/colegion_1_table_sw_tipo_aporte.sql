
DROP TABLE IF EXISTS `sw_tipo_aporte`;
CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sw_tipo_aporte` (`id_tipo_aporte`, `ta_descripcion`) VALUES
(1, 'PARCIAL'),
(2, 'EXAMEN QUIMESTRAL'),
(3, 'SUPLETORIO');
