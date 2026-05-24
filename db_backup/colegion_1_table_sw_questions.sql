
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_questions`
--

DROP TABLE IF EXISTS `sw_questions`;
CREATE TABLE `sw_questions` (
  `id` int(5) NOT NULL,
  `id_category` int(5) NOT NULL,
  `question_no` varchar(5) NOT NULL,
  `question` varchar(500) NOT NULL,
  `is_correct` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_questions`
--

INSERT INTO `sw_questions` (`id`, `id_category`, `question_no`, `question`, `is_correct`) VALUES
(1, 1, '1', 'El primer sistema de escritura se desarrolló en ....................', 3),
(2, 1, '2', '¿En qué país se originó la epidemia del coronavirus COVID-19?', 6),
(3, 1, '3', 'El país más grande en extensión es', 7),
(4, 1, '4', 'Los derechos del hombre se refieren esencialmente a', 11),
(5, 1, '5', 'La primera guerra mundial comenzó en el año de ....... y terminó en el año de ........', 15),
(6, 1, '6', 'El salario que permite satisfacer las necesidades elementales del trabajador y su familia, se denomina', 16),
(7, 1, '7', 'El tipo de escritura que se empleó en la antigua civilización egipcia se denomina', 20),
(8, 1, '8', 'La Ilíada y la Odisea de Homero son obras literarias de', 22),
(9, 1, '9', '¿Cuál de los siguientes monumentos es característico de la cultura egipcia?', 25),
(10, 1, '10', '¿Cuál de los siguientes animales no es un mamífero?', 30),
(11, 3, '1', 'Estudia la materia, la energía y las formas en que éstas se relacionan', 32),
(12, 3, '2', 'Son las estrategias para construir conocimiento y ciencia', 37),
(13, 3, '3', 'Estudia los fenómenos del mesocosmos', 39),
(14, 3, '4', 'Conjunto de conocimientos ordenados y sistematizados acerca de un aspecto de la realidad.', 43),
(15, 3, '5', 'Propiedades comunes a todos los cuerpos', 50),
(16, 3, '6', 'Estudia los fenómenos del microcosmos', 54),
(17, 3, '7', 'Originalmente era la diezmillonésima parte del cuadrante terrestre', 56),
(18, 3, '8', 'Unidad: kilogramo', 61),
(19, 3, '9', 'Se ocupa de las cualidades del sonido', 64),
(20, 3, '10', 'Es considerado el más grande de los físicos de la antigüedad', 69),
(21, 3, '11', 'Creador de la Teoría Heliocéntrica', 73),
(22, 3, '12', 'Magnitudes que se definen independientemente de otras', 79),
(23, 3, '13', 'Estudia los fenómenos del Macrocosmos', 82),
(24, 3, '14', 'Tiene qué ver con el calor, la temperatura y sus efectos.', 87),
(25, 3, '15', 'Establece las leyes de la caída libre de los cuerpos', 92),
(26, 3, '16', 'Asienta la bases de la Física Cuántica', 96),
(27, 3, '17', 'Realiza la “gran síntesis” de la Física Clásica', 98),
(28, 3, '18', 'Unidad: el metro', 101),
(29, 3, '19', 'Es el creador de la Física Relativista', 109),
(30, 3, '20', 'Características que distinguen cuerpos y sustancias.', 110),
(31, 2, '1', 'En el MRU la magnitud constante es:', 117),
(32, 2, '2', 'El desplazamiento es una cantidad', 121),
(33, 2, '3', 'En el MRU la aceleración es', 122),
(34, 2, '4', 'La cinemática estudia el movimiento de los cuerpos tomando en cuenta  las causas que lo producen', 126),
(35, 2, '5', 'La trayectoria es el punto de referencia del movimiento', 128),
(36, 2, '6', 'La gráfica distancia versus tiempo en el MRUV, resulta:', 133),
(37, 2, '7', 'Un auto parte del reposo con aceleración constante de 2 m/s². Calcula su rapidez final luego de 5 s.', 134),
(38, 2, '8', 'Si un auto inicia su marcha desde el reposo y luego de 12 s su rapidez es igual a 30 m/s. Calcula la distancia que avanzó.', 141),
(39, 2, '9', 'Un móvil inicia su recorrido con una rapidez inicial igual a 3 m/s, acelerando a razón de 5 m/s², se quiere saber cuál es su rapidez luego de 9 segundos.', 143),
(40, 2, '10', 'Un móvil inicia su recorrido con una rapidez de 7 m/s, si luego de 8 s su rapidez fue de 33 m/s. Halla el espacio que recorrió en dicho intervalo de tiempo.', 145),
(41, 5, '1', 'Un documento de hipertexto solo se compone de texto.', 151),
(42, 5, '2', 'Los navegadores se encargan de interpretar el código HTML de los documentos.', 155),
(43, 5, '3', 'El nuevo protocolo encargado de establecer los estándares del HTML es el http.', 158),
(44, 5, '4', 'Los navegadores tienen que ser compatibles con la última versión HTML para poder insertar imágenes.', 161),
(45, 5, '5', 'Si un navegador no reconoce una etiqueta...', 164),
(46, 5, '6', 'Las páginas web escritas en HTML tienen que tener la extensión html cuando se guardan.', 167),
(47, 5, '7', 'El código de las páginas estará comprendido entre las etiquetas <html> y </html>.', 169),
(48, 5, '8', '¿Qué código va entre las etiquetas <head> y </head>?', 174),
(49, 5, '9', 'El título de una página se establece dentro de la cabeza o encabezado.', 175),
(50, 5, '10', 'Las etiquetas <body> y </body> van...', 0),
(51, 5, '11', 'El cuerpo de Html, es lo que va entre <body> y </body>', 182),
(53, 5, '12', '¿Qué significa <html> en la primera línea del documento?', 184);
