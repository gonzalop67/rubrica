
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tema`
--

DROP TABLE IF EXISTS `sw_tema`;
CREATE TABLE `sw_tema` (
  `id_tema` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `te_titulo` varchar(50) NOT NULL,
  `te_descripcion` text NOT NULL,
  `te_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_tema`
--

INSERT INTO `sw_tema` (`id_tema`, `id_foro`, `te_titulo`, `te_descripcion`, `te_fecha`) VALUES
(1, 1, 'Reglas de uso de los foros', '1. Estos foros son exclusivamente para tratar temas relacionados con las asignaturas del colegio.\n2. Se pide un mínimo de educación en el lenguaje. Todos los mensajes tienen su autor.\n3. Procure escribir claramente sus mensajes para que todo mundo pueda entenderlos.\n4. Solamente los docentes y administrativos podrán crear nuevos foros.\n5. Antes de crear un nuevo foro, por favor revisar si no existe uno parecido creado con anterioridad.', '2015-02-11 21:00:34');
