-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 07-07-2026 a las 21:17:18
-- Versión del servidor: 10.11.15-MariaDB-cll-lve
-- Versión de PHP: 8.4.15

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

--
-- Volcado de datos para la tabla `sw_persona`
--

INSERT INTO `sw_persona` (`id_persona`, `dni`, `titulo`, `descripcion_titulo`, `primer_apellido`, `segundo_apellido`, `primer_nombre`, `segundo_nombre`, `nombre_corto`, `nombre_completo`, `genero`, `estado`) VALUES
(1, '1709290207', 'Ing.', 'Ingeniero en Sistemas Informáticos y de Computación', 'Peñaherrera', 'Escobar', 'Gonzalo', 'Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'Masculino', 'activo'),
(2, '1707414122', 'Lic.', 'Licenciado en ciencias de la educación', 'Salazar', 'Ordoñez', 'Carmen', 'Alicia', 'Lic. Alicia Salazar', 'Salazar Ordoñez Carmen Alicia', 'Femenino', 'activo'),
(3, '1002003075', 'Tlgo.', 'Tecnólogo en Informática', 'Cabascango', 'Herrera', 'Milton', 'Fabián', 'Tlgo. Milton Cabascango', 'Cabascango Herrera Milton Fabián', 'Masculino', 'activo'),
(4, '0501844583', 'MSc.', 'Master en Enseñanza de la Matemática', 'Proaño', 'Estrella', 'Wilson', 'Eduardo', 'MSc. Wilson Proaño', 'Proaño Estrella Wilson Eduardo', 'Masculino', 'activo'),
(5, '1707248181', 'Lic.', 'Licenciado en Ciencias de la Educación', 'Mejía', 'Segarra', 'Rómulo', 'Oswaldo', 'Lic. Rómulo Mejía', 'Mejía Segarra Rómulo Oswaldo', 'Masculino', 'activo'),
(6, '', 'Lic.', 'Licenciado en ciencias de la educación', 'Calero', 'Navarrete', 'Elmo', 'Eduardo', 'Lic. Elmo Calero', 'Calero Navarrete Elmo Eduardo', 'Masculino', 'activo'),
(7, '', 'MSc.', 'Magister en Docencia Universitaria y Administración Educativa', 'Rosero', 'Medina', 'Roberto', 'Hernán', 'MSc. Roberto Rosero', 'Rosero Medina Roberto Hernán', 'Masculino', 'activo'),
(8, '', 'Lic.', 'Diploma Superior en Ciencias de la Educación', 'Zambrano', 'Cedeño', 'Walter', 'Abdón', 'Lic. Walter Zambrano', 'Zambrano Cedeño Walter Adbón', 'Masculino', 'activo'),
(9, '', 'Ing.', 'Master Universitario de II Nivel en Alta Dirección', 'Benavides', 'Ortiz', 'German', 'Gustavo', 'Ing. German Benavides', 'Benavides Ortiz German Gustavo', 'Masculino', 'activo'),
(10, '1715238851', 'Lic.', 'Licenciado en ciencias de la educación', 'Quijia', 'Pilapaña', 'Jenny', 'Mariela', 'Lic. Jenny Quijia', 'Quijia Pilapaña Jenny Mariela', 'Femenino', 'activo'),
(11, '1717982175', 'Lic.', 'Licenciada en Turismo Histórico Cultural', 'Jumbo', 'Cumbicos', 'Diana', 'Patricia', 'Lic. Diana Jumbo', 'Jumbo Cumbicos Diana Patricia', 'Femenino', 'activo'),
(12, '1718471806', 'MSc.', 'Licenciada en ciencias de la educación mención Ciencias sociales', 'Chicaiza', 'Andachi', 'Irene', 'Tatiana', 'MSc. Irene Chicaiza', 'Chicaiza Andachi Irene Tatiana', 'Femenino', 'activo'),
(13, '1802919231', 'MSc.', 'Licenciada en Ciencias de Educación mención Lengua y Literatura', 'Lalama', 'Pilla', 'Isabel', 'Cristina', 'MSc. Isabel Lalama', 'Lalama Pilla Isabel Cristina', 'Femenino', 'activo'),
(14, '1715119341', 'Lic.', 'LICENCIADO EN DOCENCIA EDUCATIVA MENCIÓN CIENCIAS SOCIALES', 'Usiña', 'Andrade', 'Lenin', 'Sebastián', 'Lic. Sebastián Usiña', 'Usiña Andrade Lenin Sebastián', 'Masculino', 'activo'),
(15, '', 'Lic.', 'LICENCIADO EN CIENCIAS DE LA EDUCACION', 'Hernández', 'Salazar', 'Mayra', 'Araceli', ' Lic. Mayra Hernández', 'Hernández Salazar Mayra Araceli', 'Femenino', 'activo'),
(16, '1713625752', 'Lic.', 'Licenciada en Ciencias de la Educación', 'Heredia', 'Villamarín', 'Sonia', 'Patricia', 'Lic. Sonia Heredia', 'Heredia Villamarín Sonia Patricia', 'Femenino', 'activo'),
(17, '', 'Dr.', 'Doctor en Jurisprudencia y Abogado de los Tribunales de la Republica', 'Enríquez', 'Martínez', 'Carlos', 'Alberto', 'Dr. Carlos Enríquez', 'Enríquez Martínez Carlos Alberto', 'Masculino', 'inactivo'),
(18, '', 'Tlgo.', 'Tecnólogo en Informática', 'Peñafiel', 'López', 'Diego', 'Fernando', 'Tlgo. Diego Peñafiel', 'Peñafiel López Diego Fernando', 'Masculino', 'inactivo'),
(19, '', 'Mgtr.', 'Magister en Ciencias de la Educación', 'Barragán', 'García', 'Ana', 'Mirian', 'Mgtr. Mirian García', 'Barragán García Ana Mirian', 'Femenino', 'inactivo'),
(20, '', 'Dr.', '', 'Guamán', 'Calderón', 'Luis', 'Alfredo', 'Dr. Luis Guamán', 'Guamán Calderón Luis Alfredo', 'Masculino', 'inactivo'),
(21, '', 'Lic.', 'Licenciado en ciencias de la educación', 'Cedeño', 'Zambrano', 'Edith', 'Monserrate', 'Lic. Edith Cedeño', 'Cedeño Zambrano Edith Monserrate', 'Femenino', 'inactivo'),
(22, '', 'Lic.', 'Licenciado en Ciencias de la Educación, Mención Matemáticas.', 'Montenegro', 'Yépez', 'Jaime', 'Efrén', 'Lic. Efrén Montenegro', 'Montenegro Yépez Jaime Efrén', 'Masculino', 'inactivo'),
(23, '', 'Lic.', 'Licenciado en Ciencias de la Educación', 'Noguera', 'Moscoso', 'Patricia', 'Gimena', 'Lic. Rómulo Mejía', 'Noguera Moscoso Patricia Gimena', 'Femenino', 'inactivo'),
(24, '', 'Lic.', 'Licenciado en ciencias de la educación', 'Salgado', 'Araujo', 'María', 'Del Rosario', 'Lic. Rosario Salgado', 'Salgado Araujo María Del Rosario', 'Femenino', 'inactivo'),
(25, '', 'MSc.', 'Magister en Ciencias de la Educación Mención Contabilidad', 'Caraguay', 'Prócel', 'Carlos', 'Hugo', 'MSc. Carlos Caraguay', 'Caraguay Prócel Carlos Hugo', 'Masculino', 'inactivo'),
(26, '', 'Msc.', '', 'Cuyo', 'Maigua', 'Edison', 'Wilfrido', 'Msc. Edison Cuyo', 'Cuyo Maigua Edison Wilfrido', 'Masculino', 'inactivo'),
(27, '', 'Lic.', '', 'Quevedo', 'Barrezueta', 'Alfonso', 'Miguel', 'Lic. Alfonso Quevedo', 'Quevedo Barrezueta Alfonso Miguel', 'Masculino', 'inactivo'),
(28, '', 'Msc.', '', 'Pilataxi', 'Zhinín', 'Ana', 'Lucía', '', 'Pilataxi Zhinín Ana Lucía', 'Masculino', 'inactivo'),
(29, '', 'Ing.', '', 'Erazo', 'López', 'Inés', 'Alexandra', 'Ing. Inés Erazo', 'Erazo López Inés Alexandra', 'Femenino', 'inactivo'),
(30, '', 'Lic.', '', 'Guerrero', 'Onofre', 'Edwin', 'Marcelo', 'Lic. Edwin Guerrero', 'Guerrero Onofre Edwin Marcelo', 'Masculino', 'inactivo'),
(31, '', 'Mr.', 'Mr. Mrs. Ms. Secretaría', 'Salamanca', '', 'Secretaria', '', 'Secretaría de la institución', 'Salamanca Secretaria', 'Masculino', 'activo'),
(32, '', 'Dr.', 'DOCTOR EN CIENCIAS DE LA EDUCACION ESPECIALIZACION ADMINISTRACION EDUCATIVA', 'Castillo', 'Cabay', 'Ramiro', 'Vicente', 'Dr. Ramiro Castillo', 'Castillo Cabay Ramiro Vicente', 'Masculino', 'inactivo'),
(33, '', 'Lic.', 'Licenciado en ciencias de la educación', 'Trujillo', 'Realpe', 'William', 'Oswaldo', 'Lic. William Trujillo', 'Trujillo Realpe William Oswaldo', 'Masculino', 'inactivo'),
(34, '', 'MSc.', 'Master Universitario en Educación Bilingüe', 'Sanmartín', 'Vásquez', 'Sandra', 'Verónica', 'MSc. Sandra Sanmartín', 'Sanmartín Vásquez Sandra Verónica', 'Femenino', 'inactivo');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
