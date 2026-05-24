DELIMITER $$
DROP FUNCTION IF EXISTS `calc_prom_subperiodo_cualitativa`$$
CREATE FUNCTION `calc_prom_subperiodo_cualitativa` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Cualitativa VARCHAR(3);
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;

	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND id_tipo_aporte IN (1, 2);

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Cualitativa = (
		SELECT rc_calificacion
		  FROM sw_rubrica_cualitativa
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEvaluacion
		   AND id_asignatura = IdAsignatura);
           
        SET Cualitativa = IFNULL(Cualitativa, 'S/N');

		SET Calificacion = (
		SELECT ec_correlativa
		  FROM sw_escala_comportamiento
		 WHERE ec_equivalencia = Cualitativa);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = ROUND((Suma / Contador) * 2);

	RETURN Promedio;
END$$
DELIMITER ;