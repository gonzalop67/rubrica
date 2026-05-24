DELIMITER $$
CREATE FUNCTION `obtenerFechaCierreSupRemGracia`(`IdParalelo` INT, `IdTipoPeriodo` INT, `IdPeriodoLectivo` INT) RETURNS date
    NO SQL
BEGIN

	DECLARE FechaCierre date;
	
	SET FechaCierre = (
		SELECT ac.ap_fecha_cierre 
        FROM sw_periodo_evaluacion p, 
            sw_aporte_evaluacion a, 
            sw_aporte_paralelo_cierre ac,  
            sw_paralelo pa 
        WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
        AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
        AND pa.id_paralelo = ac.id_paralelo 
        AND pa.id_paralelo = IdParalelo 
        AND id_tipo_periodo = IdTipoPeriodo 
        AND p.id_periodo_lectivo = IdPeriodoLectivo);

	RETURN FechaCierre;

END$$
DELIMITER ;