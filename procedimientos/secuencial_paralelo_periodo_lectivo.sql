DELIMITER $$
CREATE FUNCTION `secuencial_paralelo_periodo_lectivo`(`IdPeriodoLectivo` INT) RETURNS int(11)
    NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(pa_orden) AS secuencial 
          FROM sw_periodo_lectivo pe, 
               sw_paralelo p, 
               sw_curso c, 
               sw_especialidad e,
               sw_tipo_educacion te
         WHERE pe.id_periodo_lectivo = te.id_periodo_lectivo
           AND te.id_tipo_educacion = e.id_tipo_educacion
           AND e.id_especialidad = c.id_especialidad 
           AND c.id_curso = p.id_curso
           AND pe.id_periodo_lectivo = IdPeriodoLectivo);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$
DELIMITER ;