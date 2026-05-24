DELIMITER $$
CREATE FUNCTION `calcular_promedio_final`(`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS float
    NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0;
    DECLARE examen_supletorio FLOAT DEFAULT 0;
    DECLARE nota_aprobacion FLOAT DEFAULT 0;
	DECLARE anio_inicial INT DEFAULT 0;

    SET nota_aprobacion = (SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = IdPeriodoLectivo);

	SET anio_inicial = (SELECT pe_anio_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = IdPeriodoLectivo);

	SET promedio_final = (SELECT calcular_promedio_periodo_lectivo(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));

    IF promedio_final > 4 AND promedio_final < 7 THEN 
        SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));

        IF examen_supletorio >= nota_aprobacion THEN
            SET promedio_final = (SELECT nota_final FROM sw_escala_supletorios WHERE examen_supletorio >= nota_minima AND examen_supletorio <= nota_maxima);
        END IF;
    END IF;
	
	RETURN promedio_final;

END$$
DELIMITER ;