-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 29-04-2020 a las 19:29:06
-- Versión del servidor: 10.1.44-MariaDB-cll-lve
-- Versión de PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--

--
-- Volcado de datos para la tabla `sw_usuario`
--

INSERT INTO `sw_usuario` (`id_usuario`, `id_periodo_lectivo`, `id_perfil`, `us_titulo`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_foto`, `us_genero`, `us_activo`) VALUES
(1, 1, 1, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'ADMINISTRADOR', 'AlhnlffT1WlPf0wsWGnJBTGDhEmD4vG+UwQrCxhpy9k=', '1240361037.jpg', 'M', 1),
(2, 1, 2, 'Lic.', 'Salazar Ordóñez', 'Alicia', 'Lic. Alicia Salazar', 'Salazar Ordóñez Alicia', 'ALICIAS', 'tXHrcb5DZrDXITM7MhUV/vAdfMphUky1NizH6NQ0UxI=', '1929154774.png', 'F', 1),
(3, 1, 2, 'Dr.', 'Enríquez Martínez', 'Carlos Alberto', 'Dr. Carlos Enriquez', 'Enríquez Martínez Carlos Alberto', 'CARLOSE', 'o6nJPLDoyAFl3rcOgixM86hdjvFi8lnRElIpFhHybbo=', 'teacher-male-avatar.png', 'M', 1),
(6, 1, 2, 'Tlgo.', 'Cabascango Herrera', 'Milton Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'MILTONC', 'NC737WPy9brG5VhJAWGV8ni8BQmN9ZaXArMhaO6PzCg=', 'teacher-male-avatar.png', 'M', 1),
(7, 1, 2, 'Tlgo.', 'Peñafiel López', 'Diego Fernando', 'Tlgo. Diego Peñafiel', 'Peñafiel López Diego Fernando', 'diegop', 'rzK/hJmkqY/E1cYVR4dMWA7vwzoqFnTB4ocd7m3M43w=', 'teacher-male-avatar.png', 'M', 0),
(10, 1, 2, 'Lic.', 'Proaño Estrella', 'Wilson Eduardo', 'Lic. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'WILSONP', 'k7gbrllf4+BcF/KgVsFqXUObms7D+yi5AXppsGOUjkY=', 'teacher-male-avatar.png', 'M', 1),
(13, 1, 2, 'Mg.', 'Barragán García', 'Mirian', 'Mg. Mirian Barragán', 'Barragán García Mirian', 'MIRIANB', 'WCeaiy+vLMtk2ypwd88JT/2P8X5x9LShyO/NkxZdojk=', 'teacher-female-avatar.png', 'F', 1),
(15, 1, 2, 'Dr.', 'Guamán Calderón', 'Luis Alfredo', 'Dr. Luis Guamán', 'Guamán Calderón Luis Alfredo', 'LUISG', 'om3pB3fARsW4qgBtBPtMsPoOq+yWaHkN9NUnAjny8as=', 'teacher-male-avatar.png', 'M', 1),
(19, 1, 2, 'Lic.', 'Cedeño Zambrano', 'Edith Monserrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Edith Monserrate', 'EDITHC', 'raZnoxm59AC9JDgyUnQB4lbV3EcB6rsw4UGdK94aNf8=', '330284167.png', 'F', 1),
(20, 1, 2, 'Lic.', 'Montenegro Yépez', 'Jaime Efrén', 'Lic. Efrén Montenegro', 'Montenegro Yépez Jaime Efrén', 'EFRENM', 's0coLjndUJwv74KY/SjyHOXfROS+bmCVQxxOjEDphdg=', 'teacher-male-avatar.png', 'M', 1),
(21, 1, 2, 'Lic.', 'Mejía Segarra', 'Rómulo Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'ROMULOM', 'PUHZvdtioTYGpCxlS5pKWd60deFLDQzsSF1iTmcYzjs=', 'teacher-male-avatar.png', 'M', 1),
(24, 1, 2, 'Lic.', 'Noguera Moscoso', 'Patricia Gimena', 'Lic. Patricia Noguera', 'Noguera Moscoso Patricia Gimena', 'PATRICIAN', 'cmV+kp2KOU+kE1TOhixpWfhvr65M21Z42RSnFf2aVis=', 'teacher-female-avatar.png', 'F', 1),
(26, 1, 2, 'Lic.', 'Salgado Araujo', 'Rosario', 'Lic. Rosario Salgado', 'Salgado Araujo Rosario', 'ROSARIOS', '55uupFaT01KlgWCY3k37zlKDfYLxLaWpo3PVTZf/eHA=', '1447951704.png', 'F', 1),
(36, 1, 5, 'Msc.', 'Caraguay Prócel', 'Carlos Hugo', 'Msc. Carlos Caraguay', 'Caraguay Prócel Carlos Hugo', 'CARLOSC', '6yIN+YXhLMSox6287qd60tfS8KoK+vcAKLSrM5fkjBo=', 'teacher-male-avatar.png', 'M', 1),
(39, 1, 6, 'Msc.', 'Cuyo Maigua', 'Edison Wilfrido', '', 'Cuyo Maigua Edison Wilfrido', 'edisonc', 'X+Ve3aHrlWQVWMUAAKNo6utBd+BspS3v7wgCxzR2NMs=', 'teacher-male-avatar.png', 'M', 0),
(40, 1, 2, 'Lic.', 'Quevedo Barrezueta', 'Alfonso Miguel', 'Lic. Alfonso Quevedo', 'Quevedo Barrezueta Alfonso Miguel', 'ALFONSOQ', 'F060XhDe5/eXdj6LfZoIgBRI4x08P1BCXboLEEMLs3Q=', 'teacher-male-avatar.png', 'M', 1),
(42, 2, 2, 'Msc.', 'Pilataxi Zhinín', 'Ana Lucía', '', 'Pilataxi Zhinín Ana Lucía', 'ANAP', '0QzfhwZlWyCrHtnT3Ib2s+0xjN7HTnnAPgAR9y4YoWY=', 'teacher-female-avatar.png', 'M', 0),
(43, 2, 2, 'Ing.', 'Erazo López', 'Inés Alexandra', '', 'Erazo López Inés Alexandra', 'inese', 'U8F36hkf8hxj6j+BhsHaX4Bcyjt3uLniCvJ5+jF+4GM=', 'teacher-female-avatar.png', 'F', 0),
(68, 3, 2, 'Lic.', 'Calero Navarrete', 'Elmo Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'elmoc', '9iBymsu2VElv3Rd/75kp6gWd9DbFcjJrA5/ndJZz3K0=', 'teacher-male-avatar.png', 'M', 1),
(71, 4, 2, 'Msc.', 'Rosero Medina', 'Roberto Hernán', 'Msc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'robertos', 'rJwKFX2KdiQARwZV+4Hz35oNBKWih2sTxaNHp/PsnAw=', 'teacher-male-avatar.png', 'M', 1),
(73, 4, 2, 'Lic.', 'Zambrano Cedeño', 'Walter Adbón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'walterz', '7xRJgxU8McPSaBH//hd0FV407XDN/20rM69I7K04Tjw=', 'teacher-male-avatar.png', 'M', 1),
(75, 4, 2, 'Lic.', 'Guerrero Onofre', 'Edwin Marcelo', '', 'Guerrero Onofre Edwin Marcelo', 'edwing', '2a5WIlC/Wdb6JUdR5flmOoWLraRG+c9IE/TGCfM4e1g=', 'teacher-male-avatar.png', 'M', 0),
(586, 5, 13, 'Ps.', 'Suasnavas', 'Iván', 'Ps. Iván Suasnavas', 'Suasnavas Iván', 'ivans', 'XFreCV02ziVdHCNukb466AF1WlDjbot134ECgZFA4TY=', '1978304942.png', 'M', 0),
(587, 5, 2, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'gonzalop', 'AlhnlffT1WlPf0wsWGnJBTGDhEmD4vG+UwQrCxhpy9k=', '1772508498.jpg', 'M', 1),
(593, 5, 3, 'Mr.', 'Salamanca', 'Secretaria', '', 'Salamanca Secretaria', 'secretaria', 'L+P4DJ0ChxvjQzBm1whpIdv9AMWCXhIEmGvcPd3DjGQ=', '1386176766.png', 'M', 1),
(594, 6, 2, 'Dr.', 'Castillo Cabay', 'Ramiro Vicente', 'Dr. Ramiro Castillo', 'Castillo Cabay Ramiro Vicente', 'ramiroc', 'Lzb1k+ACVQo8PMEeTJyxZa1jD6T7vL4PHCFcaIgwAPI=', 'teacher-male-avatar.png', 'M', 1),
(595, 7, 7, 'Lic.', 'Morales', 'Carlota', 'Lic. Carlota Morales', 'Morales Carlota', 'carlotam', '/GmvhmkM6glW23gEofW/Ne7860sO4XcV8FjRj1lQPws=', '135780439.png', 'M', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
