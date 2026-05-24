DELIMITER $$

CREATE PROCEDURE sp_calcular_promedio_anual(
    IN IdPeriodoLectivo INT, 
    IN IdAsignatura INT, 
    IN IdParalelo INT
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_anual FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo
       AND activo = 1;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_anual = (SELECT calcular_promedio_anual(
            				  IdPeriodoLectivo, IdEstudiante,
            				  IdParalelo, IdAsignatura));
                                
        IF (EXISTS (SELECT * FROM sw_promedio_anual
                    WHERE id_paralelo = IdParalelo
                    AND id_asignatura = IdAsignatura 
                    AND id_estudiante = IdEstudiante
                    AND id_periodo_lectivo = IdPeriodoLectivo)) 
                    THEN
        	UPDATE sw_promedios_anualues
            SET promedio_anual = promedio_anual
            WHERE id_paralelo = IdParalelo
            AND id_asignatura = IdAsignatura 
            AND id_estudiante = IdEstudiante
            AND id_periodo_lectivo = IdPeriodoLectivo;
        ELSE
        	INSERT INTO sw_promedio_anual
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
            	id_asignatura = IdAsignatura,
                id_periodo_lectivo = IdPeriodoLectivo,
                promedio_anual = promedio_anual;
        END IF;
    END LOOP Lazo;
END$$