
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_exam_category`
--

DROP TABLE IF EXISTS `sw_exam_category`;
CREATE TABLE `sw_exam_category` (
  `id` int(5) NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `exam_time_in_minutes` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_exam_category`
--

INSERT INTO `sw_exam_category` (`id`, `id_usuario`, `category`, `exam_time_in_minutes`) VALUES
(1, 587, 'CULTURA GENERAL', '10'),
(2, 587, 'M.R.U. Y M.R.U.V.', '20'),
(3, 587, 'DIAGNOSTICO FISICA 2DO. BGU', '30'),
(5, 587, 'CUESTIONARIO ACERCA DE HTML', '10');
