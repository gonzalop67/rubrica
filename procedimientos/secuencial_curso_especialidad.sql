DELIMITER $$
CREATE FUNCTION `secuencial_curso_especialidad`(`IdEspecialidad` INT) RETURNS int(11)
    NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(cu_orden)
		  FROM sw_curso
		 WHERE id_especialidad = IdEspecialidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$
DELIMITER ;