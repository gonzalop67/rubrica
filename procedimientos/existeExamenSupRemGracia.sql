DELIMITER $$
CREATE FUNCTION `existeExamenSupRemGracia`(`IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `IdTipoPeriodo` INT, `IdPeriodoLectivo` INT) RETURNS boolean
    NO SQL
BEGIN

	DECLARE IdRubricaEvaluacion INT;
	
	SET IdRubricaEvaluacion = (
		SELECT id_rubrica_evaluacion 
        FROM sw_rubrica_evaluacion r, 
        sw_aporte_evaluacion a, 
        sw_periodo_evaluacion p 
        WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
        AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
        AND p.id_tipo_periodo = IdTipoPeriodo 
        AND p.id_periodo_lectivo = IdPeriodoLectivo);

	RETURN EXISTS(SELECT re_calificacion 
                    FROM sw_rubrica_estudiante 
                    WHERE id_estudiante = IdEstudiante 
                    AND id_paralelo = IdParalelo 
                    AND id_asignatura = IdAsignatura 
                    AND id_rubrica_personalizada = IdRubricaEvaluacion);

END$$
DELIMITER ;