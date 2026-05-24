DELIMITER $$
CREATE FUNCTION `calcular_promedio_periodo_lectivo`(`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS float
    NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_sub_periodo FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
    DECLARE PonderacionPeriodo FLOAT;
	DECLARE suma_ponderados FLOAT DEFAULT 0;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT pc.id_periodo_evaluacion,
		   pc.pe_ponderacion 
	  FROM sw_periodo_evaluacion pe, 
		   sw_periodo_evaluacion_curso pc 
	 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
	   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
	   AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = IdParalelo) 
	   AND pe.id_periodo_lectivo = IdPeriodoLectivo
	   AND id_tipo_periodo IN (1, 7, 8);

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion, PonderacionPeriodo;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_sub_periodo = (SELECT calcular_promedio_sub_periodo(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET suma_ponderados = suma_ponderados + promedio_sub_periodo * PonderacionPeriodo;
	END LOOP Lazo;

	RETURN suma_ponderados;
END$$
DELIMITER ;