DELIMITER $$
CREATE FUNCTION `calcular_examen_supletorio`(`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS float
    NO SQL
BEGIN
	DECLARE IdRubricaEvaluacion INT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0; -- variable de salida de la funcion

	-- Aqui obtengo el valor del examen supletorio, si existe
	SET IdRubricaEvaluacion = (SELECT id_rubrica_evaluacion 
								   FROM sw_rubrica_evaluacion r, 
									    sw_aporte_evaluacion a, 
										sw_periodo_evaluacion p 
								  WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
									AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
									AND p.id_tipo_periodo = PePrincipal AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
							   FROM sw_rubrica_estudiante 
							  WHERE id_estudiante = IdEstudiante 
								AND id_paralelo = IdParalelo 
								AND id_asignatura = IdAsignatura 
								AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END$$
DELIMITER ;