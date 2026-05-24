
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_exam_results`
--

DROP TABLE IF EXISTS `sw_exam_results`;
CREATE TABLE `sw_exam_results` (
  `id` int(5) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_type` varchar(100) NOT NULL,
  `total_question` varchar(10) NOT NULL,
  `correct_answer` varchar(10) NOT NULL,
  `wrong_answer` varchar(10) NOT NULL,
  `exam_time` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
