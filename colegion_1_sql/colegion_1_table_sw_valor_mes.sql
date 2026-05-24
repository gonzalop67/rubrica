
DROP TABLE IF EXISTS `sw_valor_mes`;
CREATE TABLE `sw_valor_mes` (
  `id_valor_mes` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `vm_mes` int(11) NOT NULL,
  `vm_valor` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_valor_mes` (`id_valor_mes`, `id_periodo_lectivo`, `vm_mes`, `vm_valor`) VALUES
(1, 6, 9, 'JUSTICIA'),
(2, 6, 10, 'LIBERTAD'),
(3, 6, 11, 'RESPETO'),
(4, 6, 12, 'RESPONSABILIDAD'),
(5, 6, 1, 'LEALTAD'),
(6, 6, 2, 'HONESTIDAD'),
(7, 6, 3, 'CONFIANZA'),
(8, 6, 4, 'COMPAÑERISMO'),
(9, 6, 5, 'SOLIDARIDAD'),
(10, 6, 6, 'GRATITUD');
