DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_final`(
    `IdPeriodoLectivo` INT, 
    `IdEstudiante` INT, 
    `IdParalelo` INT) RETURNS float
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_anual FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
    DECLARE IdAsignatura INT;
	
	DECLARE cAsignaturas CURSOR FOR
	SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND p.id_paralelo = IdParalelo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio_anual = (SELECT calcular_comp_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_anual;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

	RETURN Promedio;
END$$
DELIMITER ;