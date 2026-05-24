-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2024 a las 22:17:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `us_titulo` varchar(8) NOT NULL,
  `us_titulo_descripcion` varchar(96) NOT NULL,
  `us_apellidos` varchar(32) NOT NULL,
  `us_nombres` varchar(32) NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) NOT NULL,
  `us_login` varchar(24) NOT NULL,
  `us_password` varchar(64) NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sw_usuario`
--

INSERT INTO `sw_usuario` (`id_usuario`, `us_titulo`, `us_titulo_descripcion`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_foto`, `us_genero`, `us_activo`) VALUES
(1, 'Ing.', 'Ingeniero en Sistemas', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'Administrador', 'eJCYkBmXtXug', '855136358.jpg', 'M', 1),
(2, 'Tlgo.', 'Tecnólogo en Informática', 'Cabascango Herrera', 'Milton Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'miltonc', 'cqnNxmWctiw=', '97245135.jpg', 'M', 1),
(3, 'Ing.', 'Ingeniero en Sistemas', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'gonzalop', 'eJCYkDmXtXugXg==', '990894859.png', 'M', 1),
(4, 'Ing.', 'Ingeniero Comercial', 'Benavides Ortiz', 'German Gustavo', 'Ing. German Benavides', 'Benavides Ortiz German Gustavo', 'germanb', 'eKXcyjXL4yy0R/aZ', '183631556.jpg', 'M', 1),
(5, 'Lic.', 'Licenciado en ciencias de la educación', 'Calero Navarrete', 'Elmo Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'elmoc', 'WqzDyDrK9X/3Rw==', '1332781677.jpg', 'M', 1),
(6, 'Lic.', 'Licenciado en ciencias de la educación', 'Cedeño Zambrano', 'Edith Monserrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Edith Monserrate', 'edithc', 'Uq/A1DHX83/wEPCA', '1892493961.jpg', 'F', 0),
(7, 'Dr.', 'Doctor en Jurisprudencia y Abogado de los Tribunales de la Republica', 'Enríquez Martínez', 'Carlos Alberto', 'Dr. Carlos Enríquez', 'Enríquez Martínez Carlos Alberto', 'CARLOSE', 'fKHcyzvW5Ca0TQ==', '1302037093.png', 'M', 0),
(8, 'Dr.', 'Doctor en Ciencias de la Educación', 'Guamán Calderón', 'Luis Alfredo', 'Dr. Luis Guamán', 'Guamán Calderón Luis Alfredo', 'luisg', 'WS7RD/xvT24=', '183951824.png', 'M', 0),
(9, 'Lic.', 'Licenciado en ciencias de la educación', 'Mejía Segarra', 'Rómulo Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'romulom', 'ba/DwmWXsCrF', '43729064.jpg', 'M', 1),
(10, 'Lic.', 'Licenciado en ciencias de la educación', 'Montenegro Yépez', 'Jaime Efrén', 'Lic. Jaime Montenegro', 'Montenegro Yépez Jaime Efrén', 'efrenm', 'Wi3RBPMzGzMKrQo=', '2117906686.jpg', 'M', 0),
(11, 'MSc.', '', 'Proaño Estrella', 'Wilson Eduardo', 'MSc. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'wilsonp', 'eqTbxibB7m62RfSCCw==', '1926558213.jpg', 'M', 1),
(12, 'MSc.', '', 'Rosero Medina', 'Roberto Hernán', 'MSc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'robertos', 'ba/MwibR7m22RfSCctA=', '1180374851.jpg', 'M', 1),
(13, 'Lic.', '', 'Salazar Ordoñez', 'Carmen Alicia', 'Lic. Carmen Salazar', 'Salazar Ordoñez Carmen Alicia', 'alicias', 'fq3MxiY=', '1071121085.jpg', 'F', 1),
(14, 'Lic.', 'Licenciado en ciencias de la educación', 'Salgado Araujo', 'María del Rosario', 'Lic. María Salgado', 'Salgado Araujo María del Rosario', 'rosarios', 'TaHIxjHJ4C+3', '698332508.png', 'F', 0),
(15, 'MSc.', 'Master Universitario en Educación Bilingüe', 'Sanmartín Vásquez', 'Sandra Verónica', 'MSc. Sandra Sanmartín', 'Sanmartín Vásquez Sandra Verónica', 'veronicas', 'aaXcyDrM4n/3R/aBEKk+', '1912073487.jpg', 'F', 1),
(16, 'Lic.', '', 'Trujillo Realpe', 'William Oswaldo', 'Lic. William Trujillo', 'Trujillo Realpe William Oswaldo', 'williamt', 'KvKW8D3J7Wc=', '576934223.jpg', 'M', 1),
(17, 'Lic.', '', 'Zambrano Cedeño', 'Walter Adbón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'walterz', 'UqHWziDKsy62RQ==', '205265583.jpg', 'M', 1),
(22, 'Msc.', 'Técnico Pedagogo en Ciencias de la Educación', 'Peña Almache', 'Jaime Enrique', 'Msc. Jaime Peña', 'Peña Almache Jaime Enrique', 'jaimep', 'VSrKDPhxGTEJz3Pf', '1667431530.png', 'M', 0),
(26, 'Lic', 'Licenciada en Turismo Histórico Cultural', 'Jumbo Cumbicos', 'Diana Patricia', 'Lic Diana Jumbo', 'Jumbo Cumbicos Diana Patricia', 'dianaj', 'e6nPyTXvsy62RuyY', '1119124061.png', 'F', 1),
(31, 'Lic.', 'Licenciado en ciencias de la educación', 'Quijia Pilapaña', 'Jenny Mariela', 'Lic. Jenny Quijia', 'Quijia Pilapaña Jenny Mariela', 'jennyq', 'daXAyS3Usy62RpWZ', '1484784418.jpg', 'F', 1),
(33, 'Mr.', 'Secretaría de la Institución', 'Salamanca', 'Secretaria', 'Mr. Secretaria Salamanca', 'Salamanca Secretaria', 'secretaria', 'bKHCxjnE733lJvSDE84+Ag==', '1928343663.png', 'M', 1),
(34, 'MSc.', 'Licenciada en ciencias de la educación mención Ciencias sociales', 'Chicaiza Andachi', 'Irene Tatiana', 'MSc. Irene Chicaiza', 'Chicaiza Andachi Irene Tatiana', 'irenech', 'drLLyTHG6Sy0R/LgCw==', '1766936795.jpeg', 'F', 1),
(35, 'MSc.', 'Licenciada en Ciencias de Educación mención Lengua y Literatura', 'Lalama Pilla', 'Isabel Cristina', 'MSc. Isabel Lalama', 'Lalama Pilla Isabel Cristina', 'isabell', 'drPPxTHJ7Sy0R/LgCw==', '126627451.png', 'F', 1),
(36, 'Mr.', 'NN1 Ciencias Sociales', 'NN1', 'Sociales', 'Mr. Sociales NN1', 'NN1 Sociales', 'nn1sociales', 'ca6f1DvG6H/oELU=', '1284685113.png', 'M', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
