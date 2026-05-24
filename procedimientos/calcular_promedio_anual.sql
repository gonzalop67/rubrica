DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_anual`(`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS float
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; 	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	
		DECLARE cPeriodosEvaluacion CURSOR FOR
		SELECT id_periodo_evaluacion
		  FROM sw_periodo_evaluacion 
		 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND id_tipo_periodo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma/Contador INTO promedio_anual;

	RETURN promedio_anual;
END$$
DELIMITER ;