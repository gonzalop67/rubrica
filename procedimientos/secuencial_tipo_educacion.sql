DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_tipo_educacion`(`IdPeriodoLectivo` INT) RETURNS int(11)
    NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(te_orden)
		  FROM sw_tipo_educacion
		 WHERE id_periodo_lectivo = IdPeriodoLectivo);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$
DELIMITER ;