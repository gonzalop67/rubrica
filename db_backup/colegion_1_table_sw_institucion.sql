
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

DROP TABLE IF EXISTS `sw_institucion`;
CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(64) NOT NULL,
  `in_regimen` varchar(45) NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `in_genero_rector` varchar(1) NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_genero_vicerrector` varchar(1) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_genero_secretario` varchar(1) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_distrito` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_regimen`, `in_nom_rector`, `in_genero_rector`, `in_nom_vicerrector`, `in_genero_vicerrector`, `in_nom_secretario`, `in_genero_secretario`, `in_url`, `in_logo`, `in_amie`, `in_distrito`, `in_ciudad`, `in_copiar_y_pegar`) VALUES
(1, 'UNIDAD EDUCATIVA FISCAL SALAMANCA', 'Calle el Tiempo y Pasaje Mónaco', '2256311/2254818', 'SIERRA', 'MSc. Wilson Proaño', 'M', 'Lic. Rómulo Mejía', 'M', 'MSc. Verónica Sanmartín', 'F', 'http://colegionocturnosalamanca.com', '1762697172.gif', '17H00215', '', 'Quito D.M.', 0);
