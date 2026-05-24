DELIMITER $$
DROP PROCEDURE IF EXISTS `promocionar_paralelo`$$
CREATE PROCEDURE `promocionar_paralelo` (IN `IdParaleloActual` INT,IN `IdParaleloAnterior` INT, IN `IdPeriodoLectivo` INT, IN `IdPeriodoLectivoAnterior` INT) NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdEstudiante INT;
    DECLARE NroMatricula INT;
        
    -- Cursor con los id_estudiante del paralelo anterior
    DECLARE cEstudiantes CURSOR FOR
    	SELECT id_estudiante
          FROM sw_estudiante_periodo_lectivo
         WHERE id_paralelo = IdParaleloAnterior;
        
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante; 
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        IF (SELECT es_promocionado(IdEstudiante, IdPeriodoLectivoAnterior, IdParaleloAnterior)) THEN
        	IF (NOT EXISTS(SELECT * FROM sw_estudiante_periodo_lectivo
                           WHERE id_estudiante = IdEstudiante
                           AND id_periodo_lectivo = IdPeriodoLectivo
                           AND id_paralelo = IdParaleloActual))
            THEN 
            	SET NroMatricula = (SELECT MAX(nro_matricula) + 1
                                    FROM sw_estudiante_periodo_lectivo
                                    WHERE id_periodo_lectivo = IdPeriodoLectivo);
                SET NroMatricula = IFNULL(NroMatricula, 1);
                
                INSERT INTO sw_estudiante_periodo_lectivo
                SET id_estudiante = IdEstudiante,
                id_periodo_lectivo = IdPeriodoLectivo,
                id_paralelo = IdParaleloActual,
                es_estado = 'A',
                es_retirado = 'N',
                nro_matricula = NroMatricula;
            END IF;
        END IF;
        
        UPDATE sw_promocion_automatica
        SET pro_estado = 1
        WHERE id_paralelo_actual = IdParaleloActual
        AND id_paralelo_anterior = IdParaleloAnterior;
    END LOOP Lazo;
END$$

DELIMITER ;
