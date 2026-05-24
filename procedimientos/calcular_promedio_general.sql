DELIMITER $$
CREATE FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; 	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
	DECLARE IdAsignatura INT;

		DECLARE cAsignaturas CURSOR FOR
		SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND a.id_tipo_asignatura = 1
           AND p.id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET suma = suma + (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END$$
DELIMITER ;