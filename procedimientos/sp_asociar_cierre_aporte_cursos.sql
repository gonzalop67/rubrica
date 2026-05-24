DELIMITER $$

CREATE PROCEDURE sp_asociar_cierre_aporte_cursos(
    IN IdPeriodoLectivo INT, 
    IN IdAporteEvaluacion INT
)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdCurso INT;
    
    DECLARE cCursos CURSOR FOR
    SELECT id_curso 
      FROM sw_curso c, 
           sw_especialidad e, 
           sw_tipo_educacion t 
     WHERE c.id_especialidad = e.id_especialidad 
       AND e.id_tipo_educacion = t.id_tipo_educacion 
       AND t.id_periodo_lectivo = IdPeriodoLectivo 
     ORDER BY c.id_especialidad, id_curso ASC;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cCursos;
    
    Lazo: LOOP
    	FETCH cCursos INTO IdCurso;
        IF done THEN
        	CLOSE cCursos;
            LEAVE Lazo;
        END IF;
        
        IF (NOT EXISTS (SELECT * FROM sw_aporte_curso_cierre
                         WHERE id_aporte_evaluacion = IdAporteEvaluacion
                           AND id_curso = IdCurso)) 
                    THEN
        	INSERT INTO sw_aporte_curso_cierre
            SET id_aporte_evaluacion = IdAporteEvaluacion,
            	id_curso = IdCurso;
        END IF;
    END LOOP Lazo;
END$$