
DROP TABLE IF EXISTS `sw_perfil`;
CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL,
  `pe_acceso_login` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sw_perfil` (`id_perfil`, `pe_nombre`) VALUES
(1, 'Administrador'),
(2, 'Docente'),
(3, 'Secretaría'),
(5, 'Inspector'),
(6, 'Autoridad'),
(7, 'Tutor'),
(13, 'DECE');
