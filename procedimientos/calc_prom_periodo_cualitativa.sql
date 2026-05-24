DELIMITER $$
DROP FUNCTION IF EXISTS `calc_prom_periodo_cualitativa`$$
CREATE FUNCTION `calc_prom_periodo_cualitativa` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdPeriodoEvaluacion INT;
    DECLARE Calificacion FLOAT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;

    DECLARE cPeriodosEvaluacion CURSOR FOR
    SELECT id_periodo_evaluacion
      FROM sw_periodo_evaluacion
     WHERE id_periodo_lectivo = IdPeriodoLectivo
       AND id_tipo_periodo = 1;

    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

    OPEN cPeriodosEvaluacion;

    Lazo: LOOP
        FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
        IF done THEN
            CLOSE cPeriodosEvaluacion;
            LEAVE Lazo; -- Fixed label
        END IF;
        
        -- Corrected syntax for assigning variable from SELECT
        SET Calificacion = calc_prom_subperiodo_cualitativa(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura);

        SET Suma = Suma + Calificacion;
        SET Contador = Contador + 1;
    END LOOP Lazo; -- Fixed label

    -- Prevent division by zero
    IF Contador > 0 THEN
        SET Promedio = CEIL(Suma / Contador);
    ELSE
        SET Promedio = 0;
    END IF;

    RETURN Promedio;
END$$
DELIMITER ;
