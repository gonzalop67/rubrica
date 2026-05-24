DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_insertar_institucion`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_institucion` (IN `In_nombre` VARCHAR(64), IN `In_direccion` VARCHAR(45), IN `In_telefono` VARCHAR(12), IN `In_nom_rector` VARCHAR(45), IN `In_nom_secretario` VARCHAR(45))  NO SQL
BEGIN
	IF (EXISTS (SELECT * FROM sw_institucion)) THEN
		UPDATE sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono = In_telefono,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	ELSE
		INSERT INTO sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono = In_telefono,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	END IF;
END$$