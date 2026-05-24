DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_eliminar_cierres_paralelo_periodo_lectivo`$$
CREATE PROCEDURE `sp_eliminar_cierres_paralelo_periodo_lectivo` (IN `IdParalelo` INT, IN `IdPeriodoLectivo` INT)
BEGIN
  DECLARE aportes_done INT DEFAULT 0; 
  DECLARE IdAporteEvaluacion INT;

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

  outer_loop: LOOP
    
    FETCH FROM cAportesEvaluacion INTO IdAporteEvaluacion;

    IF aportes_done THEN
        CLOSE cAportesEvaluacion;
        LEAVE outer_loop;
    END IF;

    DELETE FROM sw_aporte_paralelo_cierre
     WHERE id_aporte_evaluacion = IdAporteEvaluacion
       AND id_paralelo = IdParalelo;
        
  END LOOP outer_loop;

END$$
DELIMITER ;
