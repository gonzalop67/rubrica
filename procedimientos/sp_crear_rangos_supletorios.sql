DELIMITER $$
CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_crear_rangos_supletorios`(IN `IdPeriodoLectivo` INT)
    NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdPeriodoEvaluacion INT;
    DECLARE IdTipoPeriodoEvaluacion INT;
    
    DECLARE cPeriodosEvaluacion CURSOR FOR
    SELECT id_periodo_evaluacion, 
           id_tipo_periodo 
      FROM sw_periodo_evaluacion 
     WHERE id_periodo_lectivo = IdPeriodoLectivo 
       AND id_tipo_periodo > 1
     ORDER BY id_periodo_evaluacion ASC;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cPeriodosEvaluacion;
    
    Lazo: LOOP
    	FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion, IdTipoPeriodoEvaluacion;
        IF done THEN
        	CLOSE cPeriodosEvaluacion;
            LEAVE Lazo;
        END IF;
        
        IF (NOT EXISTS (SELECT * FROM sw_config_rangos_supletorios
                         WHERE id_periodo_lectivo = IdPeriodoLectivo
                           AND id_periodo_evaluacion = IdPeriodoEvaluacion)) 
                    THEN 
		IF IdTipoPeriodoEvaluacion = 2 THEN
        		INSERT INTO sw_config_rangos_supletorios
            	SET id_periodo_lectivo = IdPeriodoLectivo,
            	id_periodo_evaluacion = IdPeriodoEvaluacion, 
                  nota_desde = 5, 
                  nota_hasta = 6.99, 
                  nota_aprobacion = 7;
		END IF;

		IF IdTipoPeriodoEvaluacion = 3 THEN
        		INSERT INTO sw_config_rangos_supletorios
            	SET id_periodo_lectivo = IdPeriodoLectivo,
            	id_periodo_evaluacion = IdPeriodoEvaluacion, 
                  nota_desde = 0.01, 
                  nota_hasta = 4.99, 
                  nota_aprobacion = 7;
		END IF;

        END IF;
    END LOOP Lazo;
END$$
DELIMITER ;