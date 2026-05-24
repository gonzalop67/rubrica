
DROP TABLE IF EXISTS `sw_periodo_evaluacion`;
CREATE TABLE `sw_periodo_evaluacion` (
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `pe_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_abreviatura` varchar(6) NOT NULL,
  `pe_shortname` varchar(15) NOT NULL,
  `pe_principal` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_periodo_evaluacion` (`id_periodo_evaluacion`, `id_periodo_lectivo`, `id_tipo_periodo`, `pe_nombre`, `pe_abreviatura`, `pe_shortname`, `pe_principal`) VALUES
(1, 1, 0, 'PRIMER QUIMESTRE', '1ER.Q.', '', 1),
(2, 1, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', '', 1),
(3, 1, 0, 'SUPLETORIO', 'SUPLE.', '', 2),
(4, 1, 0, 'REMEDIAL', 'REMED.', '', 3),
(5, 1, 0, 'DE GRACIA', 'GRACIA', '', 4),
(6, 2, 0, 'PRIMER QUIMESTRE', '1ER.Q.', '', 1),
(7, 2, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', '', 1),
(8, 2, 0, 'EXAMEN SUPLETORIO', 'SUPLE.', '', 2),
(9, 2, 0, 'EXAMEN REMEDIAL', 'REMED.', '', 3),
(10, 2, 0, 'EXAMEN DE GRACIA', 'GRACIA', '', 4),
(11, 3, 0, 'PRIMER QUIMESTRE', '1ER.Q.', '', 1),
(12, 3, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', '', 1),
(13, 3, 0, 'SUPLETORIO', 'SUP.', '', 2),
(14, 3, 0, 'REMEDIAL', 'REM.', '', 3),
(15, 3, 0, 'DE GRACIA', 'GRA.', '', 4),
(16, 4, 0, 'PRIMER QUIMESTRE', '1ER. Q', '', 1),
(17, 4, 0, 'SEGUNDO QUIMESTRE', '2DO. Q', '', 1),
(18, 4, 0, 'EXAMEN SUPLETORIO', 'SUP', '', 2),
(19, 4, 0, 'EXAMEN REMEDIAL', 'REM', '', 3),
(20, 4, 0, 'EXAMEN DE GRACIA', 'GRA', '', 4),
(21, 5, 0, 'PRIMER QUIMESTRE', '1ER.Q.', 'PRIMERO', 1),
(22, 5, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', 'SEGUNDO', 1),
(23, 5, 0, 'EXAMEN SUPLETORIO', 'SUP.', '', 2),
(24, 5, 0, 'EXAMEN REMEDIAL', 'REM.', '', 3),
(28, 5, 0, 'EXAMEN DE GRACIA', 'GRA.', '', 4),
(29, 6, 0, 'PRIMER QUIMESTRE', '1ER.Q.', 'PRIMERO', 1),
(30, 6, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', 'SEGUNDO', 1),
(31, 6, 0, 'EXAMEN SUPLETORIO', 'SUP.', '', 2),
(32, 6, 0, 'EXAMEN REMEDIAL', 'REM.', '', 3),
(33, 6, 0, 'EXAMEN DE GRACIA', 'GRA.', '', 4);
