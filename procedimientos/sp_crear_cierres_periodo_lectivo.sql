DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_crear_cierres_periodo_lectivo`$$
CREATE PROCEDURE `sp_crear_cierres_periodo_lectivo` (IN `IdPeriodoLectivo` INT)
BEGIN
  DECLARE paralelos_done INT DEFAULT 0; 
  DECLARE IdParalelo INT;
  DECLARE IdAporteEvaluacion INT;
  DECLARE ApEstado VARCHAR(1);
  DECLARE ApFechaApertura DATE;
  DECLARE ApFechaCierre DATE;

  DECLARE cParalelos CURSOR FOR
  SELECT id_paralelo
    FROM sw_paralelo p, 
          sw_curso c, 
          sw_especialidad e, 
          sw_tipo_educacion t
    WHERE p.id_curso = c.id_curso 
      AND c.id_especialidad = e.id_especialidad 
      AND e.id_tipo_educacion = t.id_tipo_educacion  
      AND t.id_periodo_lectivo = IdPeriodoLectivo;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET paralelos_done = 1;
  OPEN cParalelos;

  outer_loop: LOOP
    
    FETCH FROM cParalelos INTO IdParalelo;

    IF paralelos_done THEN
        CLOSE cParalelos;
        LEAVE outer_loop;
    END IF;

    INNER_BLOCK: BEGIN
      DECLARE aportes_done INT DEFAULT 0;
      DECLARE cAportesEvaluacion CURSOR FOR
      SELECT a.id_aporte_evaluacion
        FROM sw_aporte_evaluacion a,
              sw_periodo_evaluacion p,
              sw_periodo_lectivo pl
        WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion
          AND p.id_periodo_lectivo = pl.id_periodo_lectivo
          AND p.id_periodo_lectivo = IdPeriodoLectivo;

      DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET aportes_done = 1;
      OPEN cAportesEvaluacion;

      inner_loop: LOOP
        FETCH FROM cAportesEvaluacion INTO IdAporteEvaluacion;

		    IF aportes_done THEN
		      CLOSE cAportesEvaluacion;			
		      LEAVE inner_loop;
		    END IF;

		    SET ApFechaApertura = (
		    SELECT ap_fecha_apertura
		      FROM sw_aporte_evaluacion
		     WHERE id_aporte_evaluacion = IdAporteEvaluacion);

		    SET ApFechaCierre = (
		    SELECT ap_fecha_cierre
		      FROM sw_aporte_evaluacion
		     WHERE id_aporte_evaluacion = IdAporteEvaluacion);

		    IF (NOT EXISTS (SELECT * FROM sw_aporte_paralelo_cierre WHERE id_paralelo = IdParalelo AND id_aporte_evaluacion = IdAporteEvaluacion)) THEN
          INSERT INTO sw_aporte_paralelo_cierre(id_aporte_evaluacion, id_paralelo, ap_estado, ap_fecha_apertura, ap_fecha_cierre)
          values(IdAporteEvaluacion, IdParalelo, 'C', ApFechaApertura, ApFechaCierre); 
        END IF;
            
      END LOOP inner_loop;

    END INNER_BLOCK;

  END LOOP outer_loop;

END$$
DELIMITER ;
