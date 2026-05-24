DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_crear_periodos_evaluacion_cursos`$$
CREATE PROCEDURE `sp_crear_periodos_evaluacion_cursos` (IN `IdPeriodoLectivo` INT)
BEGIN
  DECLARE cursos_done INT DEFAULT 0; 
  DECLARE IdCurso INT;
  DECLARE IdPeriodoEvaluacion INT;
  DECLARE Ponderacion FLOAT;

  DECLARE cCursos CURSOR FOR
   SELECT id_curso
     FROM sw_curso c, 
          sw_especialidad e, 
          sw_tipo_educacion t
    WHERE c.id_especialidad = e.id_especialidad 
      AND e.id_tipo_educacion = t.id_tipo_educacion  
      AND t.id_periodo_lectivo = IdPeriodoLectivo;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET cursos_done = 1;
  OPEN cCursos;

  outer_loop: LOOP
    
    FETCH FROM cCursos INTO IdCurso;

    IF cursos_done THEN
        CLOSE cCursos;
        LEAVE outer_loop;
    END IF;

    INNER_BLOCK: BEGIN
      DECLARE periodos_done INT DEFAULT 0;
      DECLARE cPeriodosEvaluacion CURSOR FOR
       SELECT p.id_periodo_evaluacion
         FROM sw_periodo_evaluacion p,
              sw_periodo_lectivo pl
        WHERE p.id_periodo_lectivo = pl.id_periodo_lectivo
          AND p.id_periodo_lectivo = IdPeriodoLectivo;

      DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET periodos_done = 1;
      OPEN cPeriodosEvaluacion;

      inner_loop: LOOP
        FETCH FROM cPeriodosEvaluacion INTO IdPeriodoEvaluacion;

		    IF periodos_done THEN
		      CLOSE cPeriodosEvaluacion;			
		      LEAVE inner_loop;
		    END IF;

		    SET Ponderacion = (
		    SELECT pe_ponderacion
		      FROM sw_periodo_evaluacion
		     WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);

		    IF (NOT EXISTS (SELECT * FROM sw_periodo_evaluacion_curso WHERE id_curso = IdCurso AND id_periodo_evaluacion = IdPeriodoEvaluacion)) THEN
          INSERT INTO sw_periodo_evaluacion_curso(id_periodo_lectivo, id_periodo_evaluacion, id_curso, pe_ponderacion)
          values(IdPeriodoLectivo, IdPeriodoEvaluacion, IdCurso, Ponderacion); 
        END IF;
            
      END LOOP inner_loop;

    END INNER_BLOCK;

  END LOOP outer_loop;

END$$
DELIMITER ;
