
DROP TABLE IF EXISTS `sw_institucion`;
CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(12) NOT NULL,
  `in_nom_rector` varchar(45) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_nom_rector`, `in_nom_vicerrector`, `in_nom_secretario`) VALUES
(1, 'UNIDAD EDUCATIVA PCEI FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256-104', 'DR. RAMIRO CASTILLO', 'Lic. Rómulo Mejía', 'Msc. CARLOS CARAGUAY');
