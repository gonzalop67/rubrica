DELIMITER $$
CREATE PROCEDURE `sp_cerrar_periodo_lectivo`(IN `IdPeriodoLectivo` INT)
    NO SQL
BEGIN

	DECLARE done INT DEFAULT 0;
	DECLARE IdAporteEvaluacion INT;

	DECLARE cAportesEvaluacion CURSOR FOR 
		SELECT a.id_aporte_evaluacion
		  FROM sw_aporte_evaluacion a,
			   sw_periodo_evaluacion p,
			   sw_periodo_lectivo pl
		 WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion
		   AND p.id_periodo_lectivo = pl.id_periodo_lectivo
		   AND p.id_periodo_lectivo = IdPeriodoLectivo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

		UPDATE sw_periodo_lectivo
	       SET pe_estado = 'T',
               id_periodo_estado = 2
	     WHERE id_periodo_lectivo = IdPeriodoLectivo;

	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_paralelo_cierre
		   SET ap_estado = 'C'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;
	
END$$
DELIMITER ;