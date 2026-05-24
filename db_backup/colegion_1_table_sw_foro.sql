
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_foro`
--

DROP TABLE IF EXISTS `sw_foro`;
CREATE TABLE `sw_foro` (
  `id_foro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fo_titulo` varchar(50) NOT NULL,
  `fo_descripcion` varchar(250) NOT NULL,
  `fo_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_foro`
--

INSERT INTO `sw_foro` (`id_foro`, `id_usuario`, `fo_titulo`, `fo_descripcion`, `fo_fecha`) VALUES
(1, 1, 'Preguntas Técnicas', 'Preguntas acerca del uso de los foros.', '2015-02-11 02:23:40');
