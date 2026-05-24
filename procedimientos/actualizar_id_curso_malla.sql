DELIMITER $$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `actualizar_id_curso_malla`() 
BEGIN
	DECLARE done INT DEFAULT 0;
    DECLARE IdCurso INT;
    DECLARE IdMalla INT;

	DECLARE cMallaCurricular CURSOR FOR
	SELECT id_malla_curricular
	  FROM sw_malla_curricular;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cMallaCurricular;

	Lazo1: LOOP
		FETCH cMallaCurricular INTO IdMalla;
		IF done THEN
			CLOSE cMallaCurricular;
			LEAVE Lazo1;
		END IF;

		SET IdCurso = (
		SELECT c.id_curso
		  FROM sw_curso c,
               sw_paralelo p,
               sw_malla_curricular m
		 WHERE c.id_curso = p.id_curso
           AND p.id_paralelo = m.id_paralelo
		   AND id_malla_curricular = IdMalla);
           
        UPDATE sw_malla_curricular
           SET id_curso = IdCurso
         WHERE id_malla_curricular = IdMalla;
	END LOOP Lazo1;

END$$
DELIMITER ;