DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_sub_periodo`(`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;
	DECLARE Calificacion INT;
	DECLARE Suma INT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio INT DEFAULT 0;
	
	DECLARE cAsignaturas CURSOR FOR
	SELECT a.id_asignatura
      FROM sw_asignatura_curso ac, 
           sw_paralelo p, 
           sw_asignatura a 
     WHERE ac.id_curso = p.id_curso 
       AND ac.id_asignatura = a.id_asignatura 
       AND id_paralelo = IdParalelo 
       AND id_tipo_asignatura = 1
     ORDER BY ac_orden;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		
		SET Calificacion = (
		SELECT calcular_comp_asignatura(IdPeriodoEvaluacion, IdEstudiante, IdParalelo, IdAsignatura));
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

    RETURN Promedio;
END$$
DELIMITER ;