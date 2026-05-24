
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

DROP TABLE IF EXISTS `sw_usuario`;
CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `us_titulo` varchar(8) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_titulo_descripcion` varchar(96) NOT NULL,
  `us_apellidos` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_clave` varchar(64) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_activo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sw_usuario`
--

INSERT INTO `sw_usuario` (`id_usuario`, `id_periodo_lectivo`, `id_perfil`, `us_titulo`, `us_titulo_descripcion`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_clave`, `us_foto`, `us_genero`, `us_activo`) VALUES
(1, 1, 1, 'Ing.', '', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'ADMINISTRADOR', 'lORKI+S+CvCxKmbnrJ6/kWyseAi0ndmP53in2UFhveI=', 'eJCYkBmXtXug', '1036192776.jpg', 'M', 1),
(2, 1, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Salazar Ordoñez', 'Alicia', 'Lic. Alicia Salazar', 'Salazar Ordoñez Alicia', 'ALICIAS', 'tXHrcb5DZrDXITM7MhUV/vAdfMphUky1NizH6NQ0UxI=', '', '1262889970.jpg', 'F', 1),
(3, 1, 2, 'Dr.', 'Doctor en Jurisprudencia y Abogado de los Tribunales de la Republica', 'Enríquez Martínez', 'Carlos Alberto', 'Dr. Carlos Enríquez', 'Enríquez Martínez Carlos Alberto', 'CARLOSE', 'o6nJPLDoyAFl3rcOgixM86hdjvFi8lnRElIpFhHybbo=', '', '784811803.png', 'M', 0),
(6, 1, 2, 'Tlgo.', 'Tecnólogo en Informática', 'Cabascango Herrera', 'Milton Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'MILTONC', 'NC737WPy9brG5VhJAWGV8ni8BQmN9ZaXArMhaO6PzCg=', 'cqnNxmWctiw=', '2068678906.jpg', 'M', 1),
(7, 1, 2, 'Tlgo.', '', 'Peñafiel López', 'Diego Fernando', 'Tlgo. Diego Peñafiel', 'Peñafiel López Diego Fernando', 'diegop', 'rzK/hJmkqY/E1cYVR4dMWA7vwzoqFnTB4ocd7m3M43w=', '', '1401425242.png', 'M', 0),
(10, 1, 2, 'MSc.', 'Master en Enseñanza de la Matemática', 'Proaño Estrella', 'Wilson Eduardo', 'MSc. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'wilsonp', '0TD+BHw/Ad8PkuQz6LTv4YP6ysPeyriYPXfJBovsWJE=', '', '126706935.jpg', 'M', 1),
(13, 1, 2, 'Mgtr.', '', 'Barragán García', 'Mirian', 'Mgtr. Mirian Barragán', 'Barragán García Mirian', 'MIRIANB', 'WCeaiy+vLMtk2ypwd88JT/2P8X5x9LShyO/NkxZdojk=', '', '1471992448.png', 'F', 0),
(15, 1, 2, 'Dr.', '', 'Guamán Calderón', 'Luis Alfredo', 'Dr. Luis Guamán', 'Guamán Calderón Luis Alfredo', 'LUISG', 'om3pB3fARsW4qgBtBPtMsPoOq+yWaHkN9NUnAjny8as=', '', 'teacher-male-avatar.png', 'M', 0),
(19, 1, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Cedeño Zambrano', 'Edith Monserrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Edith Monserrate', 'EDITHC', '7Mae+vBBtf0P8SdmXlgPMUckSkIPRYs9fpoGlQcobEU=', '', '201373202.jpg', 'F', 0),
(20, 1, 2, 'Lic.', '', 'Montenegro Yépez', 'Jaime Efrén', 'Lic. Jaime Montenegro', 'Montenegro Yépez Jaime Efrén', 'EFRENM', 's0coLjndUJwv74KY/SjyHOXfROS+bmCVQxxOjEDphdg=', '', '532475779.jpg', 'M', 0),
(21, 1, 2, 'Lic.', '', 'Mejía Segarra', 'Rómulo Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'ROMULOM', 'Hmk04IDLd1R2x58cPOyDLd2tV/tPZ1vVChG5uddKoLE=', '', '802050108.jpg', 'M', 1),
(24, 1, 2, 'Lic.', '', 'Noguera Moscoso', 'Patricia Gimena', 'Lic. Patricia Noguera', 'Noguera Moscoso Patricia Gimena', 'PATRICIAN', 'cmV+kp2KOU+kE1TOhixpWfhvr65M21Z42RSnFf2aVis=', '', '1651096835.png', 'F', 0),
(26, 1, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Salgado Araujo', 'Rosario', 'Lic. Rosario Salgado', 'Salgado Araujo Rosario', 'ROSARIOS', 'sy7Jq5fLYlXpPXUisap75TELqkeonfv8GqPEuWe58Tg=', '', '290760434.png', 'F', 0),
(36, 1, 5, 'Msc.', '', 'Caraguay Prócel', 'Carlos Hugo', 'Msc. Carlos Caraguay', 'Caraguay Prócel Carlos Hugo', 'CARLOSC', '6yIN+YXhLMSox6287qd60tfS8KoK+vcAKLSrM5fkjBo=', '', 'teacher-male-avatar.png', 'M', 0),
(39, 1, 6, 'Msc.', '', 'Cuyo Maigua', 'Edison Wilfrido', 'Msc. Edison Cuyo', 'Cuyo Maigua Edison Wilfrido', 'edisonc', 'X+Ve3aHrlWQVWMUAAKNo6utBd+BspS3v7wgCxzR2NMs=', '', 'teacher-male-avatar.png', 'M', 0),
(40, 1, 2, 'Lic.', '', 'Quevedo Barrezueta', 'Alfonso Miguel', 'Lic. Alfonso Quevedo', 'Quevedo Barrezueta Alfonso Miguel', 'ALFONSOQ', 'F060XhDe5/eXdj6LfZoIgBRI4x08P1BCXboLEEMLs3Q=', '', '262013955.jpg', 'M', 0),
(42, 2, 2, 'Msc.', '', 'Pilataxi Zhinín', 'Ana Lucía', '', 'Pilataxi Zhinín Ana Lucía', 'ANAP', '0QzfhwZlWyCrHtnT3Ib2s+0xjN7HTnnAPgAR9y4YoWY=', '', 'teacher-female-avatar.png', 'M', 0),
(43, 2, 2, 'Ing.', '', 'Erazo López', 'Inés Alexandra', 'Ing. Inés Erazo', 'Erazo López Inés Alexandra', 'inese', 'U8F36hkf8hxj6j+BhsHaX4Bcyjt3uLniCvJ5+jF+4GM=', '', '702054326.png', 'F', 0),
(68, 3, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Calero Navarrete', 'Elmo Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'elmoc', 'Ysu68Yz2JbHPZORL24TDdBq+blCi5fx3a73RG8ljwOU=', '', '2031728500.jpg', 'M', 1),
(71, 4, 2, 'MSc.', 'Magister en Docencia Universitaria y Administración Educativa', 'Rosero Medina', 'Roberto Hernán', 'MSc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'robertos', 'rL2o4KP8gn7OXvVZoKsZpvqa3vWHfxdjuxQDfTmj0gE=', '', '111935608.jpg', 'M', 1),
(73, 4, 2, 'Lic.', 'Diploma Superior en Ciencias de la Educación', 'Zambrano Cedeño', 'Walter Adbón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'walterz', 'HOF3XmX2ucfPgpGSe56heySaYMcp9W1TX9H8ZyQtgRI=', '', '1171235139.jpg', 'M', 1),
(75, 4, 2, 'Lic.', '', 'Guerrero Onofre', 'Edwin Marcelo', 'Lic. Edwin Guerrero', 'Guerrero Onofre Edwin Marcelo', 'edwing', '2a5WIlC/Wdb6JUdR5flmOoWLraRG+c9IE/TGCfM4e1g=', '', 'teacher-male-avatar.png', 'M', 0),
(587, 5, 2, 'Ing.', 'Ingeniero en Sistemas', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'gonzalop', 'NX0EaaXZJfz96b3/VeAiUaXTi9N37FBlbTG23p5PK+c=', 'eJCYkDmXtXugXg==', '1998782566.png', 'M', 1),
(593, 5, 3, 'Mr.', 'Mr. Mrs. Ms. Secretaría', 'Salamanca', 'Secretaria', 'Secretaría de la institución', 'Salamanca Secretaria', 'secretaria', 'YY9YKab+4qEfHXiegNjvO9YoX+JAh4rhQnyg+C/5EyA=', '', '1386176766.png', 'M', 1),
(594, 6, 2, 'Dr.', '', 'Castillo Cabay', 'Ramiro Vicente', 'Dr. Ramiro Castillo', 'Castillo Cabay Ramiro Vicente', 'ramiroc', 'Lzb1k+ACVQo8PMEeTJyxZa1jD6T7vL4PHCFcaIgwAPI=', '', 'teacher-male-avatar.png', 'M', 0),
(597, 8, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Trujillo Realpe', 'William Oswaldo', 'Lic. William Trujillo', 'Trujillo Realpe William Oswaldo', 'williamt', 'ztr63mZmFhI96Qj5MMmZJmNdufVKoXm27k9MiQh+ikI=', '', '938188062.jpg', 'M', 1),
(598, 8, 2, 'Ing.', 'Master Universitario de II Nivel en Alta Dirección', 'Benavides Ortiz', 'German Gustavo', 'Ing. German Benavides', 'Benavides Ortiz German Gustavo', 'germanb', 'rrNSLtcU2xZd2fmcqe+fp7AIzcpJRFzn0lNRKmC/Wsw=', '', '318123795.jpg', 'M', 1),
(599, 8, 2, 'MSc.', 'Master Universitario en Educación Bilingüe', 'Sanmartín Vásquez', 'Sandra Verónica', 'MSc. Sandra Sanmartín', 'Sanmartín Vásquez Sandra Verónica', 'veronicas', 'JNYAsqLad7SJWf2ZS/MkQ3Fo8ieLdkfxLDaCBPEXOx8=', '', '1733328581.jpg', 'F', 1),
(600, 9, 13, 'MSc.', '', 'Peña Almache', 'Jaime Enrique', 'MSc. Jaime Peña', 'Peña Almache Jaime Enrique', 'jaimep', 'F8OWy6UYC2ajuNLRf43ym7htNx3cwhqXq67RjXlFGoI=', '', '173951668.png', 'M', 0),
(601, 16, 2, 'Lic.', 'Licenciado en ciencias de la educación', 'Quijia Pilapaña', 'Jenny Mariela', 'Lic. Jenny Quijia', 'Quijia Pilapaña Jenny Mariela', 'jennyq', 'dxNjmzkJ5n1O2Wd0YTt4ooEtQinE0/BiWXlZwS6aNSM=', '', '2105622176.jpg', 'F', 1),
(604, 0, 0, 'Lic.', 'Licenciada en Turismo Histórico Cultural', 'Jumbo Cumbicos', 'Diana Patricia ', 'Lic. Diana Jumbo', 'Jumbo Cumbicos Diana Patricia ', 'dianaj', 'acfssFqW2WmytcTZWKTEB+tqsR4aLZK5MXpioilpBlM=', '', '23810478.png', 'F', 1),
(607, 0, 0, 'MSc.', 'Licenciada en ciencias de la educación mención Ciencias sociales', 'Chicaiza Andachi', 'Irene Tatiana', 'MSc. Irene Chicaiza', 'Chicaiza Andachi Irene Tatiana', 'irenech', 'vWs6l9eTyJoj6k4FpNj3RK6jFqVy4sCkGK2W3puEpsI=', '', '1464018303.jpeg', 'F', 1),
(608, 0, 0, 'MSc.', 'Licenciada en Ciencias de Educación mención Lengua y Literatura', 'Lalama Pilla', 'Isabel Cristina', 'MSc. Isabel Lalama', 'Lalama Pilla Isabel Cristina', 'isabell', 'z7BQjIYWyftSYfhqnVig86znV8hN34FtgS6y/7/5GrE=', '', '913638378.png', 'F', 1),
(609, 0, 0, 'Lic.', 'LICENCIADO EN DOCENCIA EDUCATIVA MENCIÓN CIENCIAS SOCIALES', 'Usiña Andrade', 'Lenin Sebastián', 'Lic. Lenin Usiña', 'Usiña Andrade Lenin Sebastián', 'leninu', 'tiVaOTSXDeGvsdjfs9wpQsCmyWcoFjl6hLYNqEWfUfg=', '', '1019195317.jpeg', 'M', 1);
