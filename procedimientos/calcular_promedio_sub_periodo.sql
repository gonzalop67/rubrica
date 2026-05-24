-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------

DELIMITER $$

CREATE FUNCTION `calcular_promedio_sub_periodo`(
`IdPeriodoEvaluacion` INT, 
`IdEstudiante` INT, 
`IdParalelo` INT, 
`IdAsignatura` INT) 
RETURNS float
    
NO SQL

BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdAporteEvaluacion INT;
    DECLARE PonderacionAporte FLOAT;
    DECLARE promedio_aporte FLOAT DEFAULT 0;
    DECLARE suma_ponderados_aporte FLOAT DEFAULT 0;
    
        DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion, 
               ap_ponderacion 
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAportesEvaluacion;
    
    Lazo: LOOP
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion, PonderacionAporte;

        IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo;
		END IF;
        
        SELECT calcular_promedio_aporte (IdAporteEvaluacion, IdEstudiante, IdParalelo, IdAsignatura) INTO promedio_aporte;
        
        SET suma_ponderados_aporte = suma_ponderados_aporte + promedio_aporte * PonderacionAporte;
    END LOOP Lazo;
    
    RETURN suma_ponderados_aporte;
    
END$$

DELIMITER ;