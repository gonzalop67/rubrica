DELIMITER $$
CREATE FUNCTION `calcular_comp_tutor` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	
	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND ap_tipo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Calificacion = (
		SELECT ec_correlativa
		  FROM sw_comportamiento_tutor ct,
               sw_escala_comportamiento ec
		 WHERE ec.id_escala_comportamiento = ct.id_escala_comportamiento 
           AND id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

DELIMITER ;