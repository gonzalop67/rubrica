
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_insumo`
--

DROP TABLE IF EXISTS `sw_insumo`;
CREATE TABLE `sw_insumo` (
  `id_insumo` int(11) NOT NULL,
  `in_nombre` varchar(50) NOT NULL,
  `in_descripcion` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_insumo`
--

INSERT INTO `sw_insumo` (`id_insumo`, `in_nombre`, `in_descripcion`) VALUES
(1, 'Tarea', 'Nota asignada a la evaluación de los contenidos y cumplimiento de las tareas que\r\ndeben realizarse en casa. Pueden ser: lecturas, investigaciones pequeñas, recopilación de datos,\r\nobservaciones, traer materiales específicos, entre otras muchas.'),
(2, 'Actividad individual en clase', 'El conjunto de acciones que buscan el aprendizaje realizadas\r\nen la clase.'),
(3, 'Actividad grupal en clase', 'El conjunto de acciones que buscan el aprendizaje realizadas en\r\nclase de forma grupal, buscando instalar el trabajo cooperativo y liderazgo.'),
(4, 'Lección', 'Nota asignada a desempeños intermedios, sean pruebas o trabajos escritos o\r\npresentaciones orales y avances de proyectos.');
