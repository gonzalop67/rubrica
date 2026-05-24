DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_eliminar_cierres_aporte_periodo_lectivo`$$
CREATE PROCEDURE `sp_eliminar_cierres_aporte_periodo_lectivo` (IN `IdAporteEvaluacion` INT, IN `IdPeriodoLectivo` INT)
BEGIN
  DECLARE aportes_done INT DEFAULT 0; 
  DECLARE IdParalelo INT;

  DECLARE cParalelos CURSOR FOR
    SELECT id_paralelo
      FROM sw_paralelo 
     WHERE id_periodo_lectivo = IdPeriodoLectivo;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET aportes_done = 1;
  OPEN cParalelos;

  outer_loop: LOOP
    
    FETCH FROM cParalelos INTO IdParalelo;

    IF aportes_done THEN
        CLOSE cParalelos;
        LEAVE outer_loop;
    END IF;

    DELETE FROM sw_aporte_paralelo_cierre
     WHERE id_aporte_evaluacion = IdAporteEvaluacion
       AND id_paralelo = IdParalelo;
        
  END LOOP outer_loop;

END$$
DELIMITER ;
