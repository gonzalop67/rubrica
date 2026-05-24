
DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_actualizar_periodo_lectivo`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_actualizar_periodo_lectivo` (IN `IdPeriodoLectivo` INT, IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` INT)  NO SQL
UPDATE sw_periodo_lectivo
   SET pe_anio_inicio = AnioInicial,
       pe_anio_fin = AnioFinal,
       pe_fecha_inicio = FechaInicial
 WHERE id_periodo_lectivo = IdPeriodoLectivo$$

DROP PROCEDURE IF EXISTS `sp_actualizar_usuario`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_actualizar_usuario` (IN `IdUsuario` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsNombreCompleto` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64), IN `UsActivo` INT)  NO SQL
BEGIN
	UPDATE sw_usuario SET
	us_titulo = UsTitulo,
	us_apellidos = UsApellidos,
	us_nombres = UsNombres,
	us_fullname = UsNombreCompleto,
	us_login = UsLogin,
	us_password = UsPassword,
    us_activo = UsActivo
	WHERE id_usuario = IdUsuario;
END$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_anual`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_calcular_prom_anual` (IN `IdPeriodoLectivo` INT, IN `IdParalelo` INT)  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_anual FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_anual = (SELECT calcular_promedio_general(
        						IdPeriodoLectivo, 
            					IdEstudiante, 
            					IdParalelo));
                                
        IF (EXISTS (SELECT * FROM sw_estudiante_prom_anual
                    WHERE id_paralelo = IdParalelo
                    AND id_estudiante = IdEstudiante
                    AND id_periodo_lectivo = IdPeriodoLectivo)) 
                    THEN
        	UPDATE sw_estudiante_prom_anual
            SET ea_promedio = promedio_anual
            WHERE id_paralelo = IdParalelo
            AND id_estudiante = IdEstudiante
            AND id_periodo_lectivo = IdPeriodoLectivo;
        ELSE
        	INSERT INTO sw_estudiante_prom_anual
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
                id_periodo_lectivo = IdPeriodoLectivo,
                ea_promedio = promedio_anual;
        END IF;
    END LOOP Lazo;
END$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_aporte`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_calcular_prom_aporte` (IN `IdAporteEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_aporte FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_aporte = (SELECT calcular_prom_aporte_estudiante(
        						IdAporteEvaluacion, 
            					IdParalelo, 
            					IdEstudiante));
                                
        IF (EXISTS (SELECT * FROM sw_estudiante_promedio_parcial
                    WHERE id_paralelo = IdParalelo
                    AND id_estudiante = IdEstudiante
                    AND id_aporte_evaluacion = IdAporteEvaluacion)) 
                    THEN
        	UPDATE sw_estudiante_promedio_parcial
            SET ep_promedio = promedio_aporte
            WHERE id_paralelo = IdParalelo
            AND id_estudiante = IdEstudiante
            AND id_aporte_evaluacion = IdAporteEvaluacion;
        ELSE
        	INSERT INTO sw_estudiante_promedio_parcial
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
                id_aporte_evaluacion = IdAporteEvaluacion,
                ep_promedio = promedio_aporte;
        END IF;
    END LOOP Lazo;
END$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_quimestre`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_calcular_prom_quimestre` (IN `IdPeriodoEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_quimestre = (SELECT calcular_prom_quim_estudiante(
        						IdPeriodoEvaluacion, 
            					IdParalelo, 
            					IdEstudiante));
                                
        IF (EXISTS (SELECT * FROM sw_estudiante_prom_quimestral
                    WHERE id_paralelo = IdParalelo
                    AND id_estudiante = IdEstudiante
                    AND id_periodo_evaluacion = IdPeriodoEvaluacion)) 
                    THEN
        	UPDATE sw_estudiante_prom_quimestral
            SET eq_promedio = promedio_quimestre
            WHERE id_paralelo = IdParalelo
            AND id_estudiante = IdEstudiante
            AND id_periodo_evaluacion = IdPeriodoEvaluacion;
        ELSE
        	INSERT INTO sw_estudiante_prom_quimestral
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
                id_periodo_evaluacion = IdPeriodoEvaluacion,
                eq_promedio = promedio_quimestre;
        END IF;
    END LOOP Lazo;
END$$

DROP PROCEDURE IF EXISTS `sp_insertar_institucion`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_institucion` (IN `In_nombre` VARCHAR(64), IN `In_direccion` VARCHAR(45), IN `In_telefono1` VARCHAR(12), IN `In_nom_rector` VARCHAR(45), IN `In_nom_secretario` VARCHAR(45))  NO SQL
BEGIN
	IF (EXISTS (SELECT * FROM sw_institucion)) THEN
		UPDATE sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	ELSE
		INSERT INTO sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario;
	END IF;
END$$

DROP PROCEDURE IF EXISTS `sp_insertar_periodo_lectivo`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_periodo_lectivo` (IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` DATE)  NO SQL
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
		   AND pl.pe_anio_inicio = AnioInicial - 1
		   AND a.ap_tipo < 4;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	-- Primero debo verificar si hay un periodo lectivo anterior
	
	SET @IdPeriodoLectivoAnterior = (SELECT id_periodo_lectivo
                                      FROM sw_periodo_lectivo
                                     WHERE pe_anio_inicio = AnioInicial - 1);

	-- SELECT @IdPeriodoLectivoAnterior;

	IF @IdPeriodoLectivoAnterior IS NOT NULL THEN
		-- Actualizo el estado del periodo lectivo anterior
		UPDATE sw_periodo_lectivo
		   SET pe_estado = 'T'
		 WHERE id_periodo_lectivo = @IdPeriodoLectivoAnterior;

		-- Aqui actualizo a 'C' todos los periodos de evaluacion
		-- menos el examen de gracia utilizando un cursor

		OPEN cAportesEvaluacion;

		REPEAT
			FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
			UPDATE sw_aporte_curso_cierre
			   SET ap_estado = 'C'
			 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
		UNTIL done END REPEAT;

		CLOSE cAportesEvaluacion;
	
	END IF;

	-- Finalmente inserto el nuevo periodo lectivo
	INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado, pe_fecha_inicio)
	VALUES (AnioInicial, AnioFinal, 'A', FechaInicial);

END$$

DROP PROCEDURE IF EXISTS `sp_insertar_usuario`$$
CREATE DEFINER=`colegion`@`localhost` PROCEDURE `sp_insertar_usuario` (IN `IdPeriodoLectivo` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsFullname` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64))  NO SQL
BEGIN
	DECLARE max_id INT;
	INSERT INTO sw_usuario (
		id_periodo_lectivo,
		id_perfil,
		us_titulo,
		us_apellidos,
		us_nombres,
		us_fullname,
		us_login,
		us_password,
        us_activo
	) VALUES (
		IdPeriodoLectivo,
		IdPerfil,
		UsTitulo,
		UsApellidos,
		UsNombres,
		UsFullname,
		UsLogin,
		UsPassword,
        1
	);
    SET max_id = (SELECT MAX(id_usuario) FROM sw_usuario);
    INSERT INTO sw_usuario_perfil SET id_usuario = max_id, id_perfil = IdPerfil;
END$$

DROP FUNCTION IF EXISTS `aprueba_todas_asignaturas`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todas_asignaturas` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(1) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

DROP FUNCTION IF EXISTS `aprueba_todos_remediales`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todos_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

DROP FUNCTION IF EXISTS `aprueba_todos_supletorios`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `aprueba_todos_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				SET done = 1;
				SET aprueba = FALSE;
			END IF;
		ELSE IF promedio < 5 THEN -- tiene que rendir el examen remedial
				SET done = 1;
				SET aprueba = FALSE;
			 END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

DROP FUNCTION IF EXISTS `calcular_comp_anual`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT id_periodo_evaluacion
	  FROM sw_periodo_evaluacion
	 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_comp_asignatura(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

	RETURN Promedio;
END$$

DROP FUNCTION IF EXISTS `calcular_comp_asignatura`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_asignatura` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	
	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND ap_tipo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Calificacion = (
		SELECT co_calificacion
		  FROM sw_calificacion_comportamiento
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

DROP FUNCTION IF EXISTS `calcular_comp_final`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_anual FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
    DECLARE IdAsignatura INT;
	
	DECLARE cAsignaturas CURSOR FOR
	SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND p.id_paralelo = IdParalelo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio_anual = (SELECT calcular_comp_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_anual;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

	RETURN Promedio;
END$$

DROP FUNCTION IF EXISTS `calcular_comp_insp_quimestre`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_comp_insp_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;     
	DECLARE promedio_aporte FLOAT;
	DECLARE IdAporteEvaluacion INT;
	DECLARE Calificacion FLOAT;
	DECLARE Cualitativa VARCHAR(3);
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;

	DECLARE cAportesEvaluacion CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE id_periodo_evaluacion = IdPeriodoEvaluacion
       AND ap_tipo = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportesEvaluacion;

	Lazo1: LOOP
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo1;
		END IF;
		
		SET Cualitativa = (
		SELECT co_calificacion
		  FROM sw_comportamiento_inspector
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Cualitativa = IFNULL(Cualitativa, 'S/N');

		SET Calificacion = (
		SELECT ec_correlativa
		  FROM sw_escala_comportamiento
		 WHERE ec_equivalencia = Cualitativa);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

DROP FUNCTION IF EXISTS `calcular_examen_supletorio`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_examen_supletorio` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS FLOAT NO SQL
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
									AND p.pe_principal = PePrincipal AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
							   FROM sw_rubrica_estudiante 
							  WHERE id_estudiante = IdEstudiante 
								AND id_paralelo = IdParalelo 
								AND id_asignatura = IdAsignatura 
								AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_anual`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; -- variable de salida de la funcion
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	
	-- Aqui calculo el promedio anual utilizando un cursor
	DECLARE cPeriodosEvaluacion CURSOR FOR
		SELECT id_periodo_evaluacion
		  FROM sw_periodo_evaluacion 
		 WHERE id_periodo_lectivo = IdPeriodoLectivo
		   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma/Contador INTO promedio_anual;

	RETURN promedio_anual;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_anual_proyectos`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_anual_proyectos` (`IdPeriodoLectivo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; 	
	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE IdClub INT;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT id_periodo_evaluacion
	  FROM sw_periodo_evaluacion 
	 WHERE id_periodo_lectivo = IdPeriodoLectivo
	   AND pe_principal = 1;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	-- Obtener el id_club correspondiente
	SET IdClub = (SELECT id_club FROM sw_estudiante_club
                   WHERE id_estudiante = IdEstudiante
                     AND id_periodo_lectivo = IdPeriodoLectivo);

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_quimestre = (SELECT calcular_promedio_quimestre_club(
									IdPeriodoEvaluacion,IdEstudiante,IdClub));
		SET Suma = Suma + promedio_quimestre;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO promedio_anual;

	RETURN promedio_anual;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_aporte`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_aporte` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT; 	DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	DECLARE cRubricasEvaluacion CURSOR FOR
	SELECT id_rubrica_evaluacion
	  FROM sw_rubrica_evaluacion r,
           sw_asignatura a
	 WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
       AND a.id_asignatura = IdAsignatura
       AND id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		SET ReCalificacion = (
		SELECT re_calificacion
		  FROM sw_rubrica_estudiante
		 WHERE id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_asignatura = IdAsignatura
		   AND id_rubrica_personalizada = IdRubricaEvaluacion);
           
        SET ReCalificacion = IFNULL(ReCalificacion, 0);

		SET Suma = Suma + ReCalificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_aporte_club`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_aporte_club` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT READS SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_aporte FLOAT;
    DECLARE IdRubricaEvaluacion INT;
	DECLARE ReCalificacion FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;

	DECLARE cRubricasEvaluacion CURSOR FOR
	SELECT id_rubrica_evaluacion
	  FROM sw_rubrica_evaluacion
	 WHERE id_aporte_evaluacion = IdAporteEvaluacion;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cRubricasEvaluacion;

	Lazo1: LOOP
		FETCH cRubricasEvaluacion INTO IdRubricaEvaluacion;
		IF done THEN
			CLOSE cRubricasEvaluacion;
			LEAVE Lazo1;
		END IF;

		SET ReCalificacion = (
		SELECT rc_calificacion
		  FROM sw_rubrica_club
		 WHERE id_estudiante = IdEstudiante
		   AND id_club = IdClub
		   AND id_rubrica_evaluacion = IdRubricaEvaluacion);
           
        SET ReCalificacion = IFNULL(ReCalificacion, 0);

		SET Suma = Suma + ReCalificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SELECT Suma / Contador INTO promedio_aporte;
	
	RETURN promedio_aporte;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_final`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0; 	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;
	DECLARE examen_de_gracia FLOAT DEFAULT 0;

	SET promedio_final = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
	IF promedio_final >= 5 AND promedio_final < 7 THEN 		
		SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
		IF examen_supletorio >= 7 THEN
			SET promedio_final = 7;
		ELSE
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	ELSE 
		IF promedio_final > 0 AND promedio_final < 5 THEN
			SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
			IF examen_remedial >= 7 THEN
				SET promedio_final = 7;
			ELSE
				SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
				IF examen_de_gracia >= 7 THEN
					SET promedio_final = 7;
				END IF;
			END IF;
		END IF;
	END IF;

	RETURN promedio_final;

END$$

DROP FUNCTION IF EXISTS `calcular_promedio_general`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; -- variable de salida de la funcion
	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET suma = suma + (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_quimestre`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT; -- variable de salida de la funcion
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Total_Aportes INT DEFAULT 0;
    DECLARE Examen FLOAT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;
    
    -- Declaracion del cursor que se va a utilizar
    DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    SET Total_Aportes = (SELECT COUNT(*) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);
    
    OPEN cAportesEvaluacion;
    
    REPEAT
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
        
        SELECT calcular_promedio_aporte (IdAporteEvaluacion, IdEstudiante, IdParalelo, IdAsignatura) INTO promedio_aporte;
        
        SET Contador = Contador + 1;
        
        IF Contador <= Total_Aportes - 1 THEN
        	SET Suma = Suma + promedio_aporte;
        ELSE
        	SET Examen = promedio_aporte;
        END IF;
    UNTIL done END REPEAT;
    
    CLOSE cAportesEvaluacion;
    
    SET Promedio = Suma / (Total_Aportes - 1);
    
    SELECT 0.8 * Promedio + 0.2 * Examen INTO promedio_quimestre;
    
    RETURN promedio_quimestre;
    
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_quimestre_club`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_promedio_quimestre_club` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;
	DECLARE promedio_aporte FLOAT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE Total_Aportes INT DEFAULT 0;
    DECLARE Examen FLOAT DEFAULT 0;
    DECLARE Promedio FLOAT DEFAULT 0;
    
        DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    SET Total_Aportes = (SELECT COUNT(*) FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);
    
    OPEN cAportesEvaluacion;
    
    REPEAT
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
        
        SELECT calcular_promedio_aporte_club (IdAporteEvaluacion, IdEstudiante, IdClub) INTO promedio_aporte;
        
        SET Contador = Contador + 1;
        
        IF Contador <= Total_Aportes - 1 THEN
        	SET Suma = Suma + promedio_aporte;
        ELSE
        	SET Examen = promedio_aporte;
        END IF;
    UNTIL done END REPEAT;
    
    CLOSE cAportesEvaluacion;
    
    SET Promedio = Suma / (Total_Aportes - 1);
    
    SELECT 0.8 * Promedio + 0.2 * Examen INTO promedio_quimestre;
    
    RETURN promedio_quimestre;
    
END$$

DROP FUNCTION IF EXISTS `calcular_prom_anual_estudiante`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_prom_anual_estudiante` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_anual FLOAT;     
    DECLARE promedio_quimestre FLOAT;
    DECLARE IdAsignatura INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    
    DECLARE cAsignaturas CURSOR FOR
    SELECT id_asignatura
      FROM sw_asignatura_curso ac,
           sw_paralelo p
     WHERE p.id_curso = ac.id_curso
       AND id_paralelo = IdParalelo;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAsignaturas;
    
    Lazo: LOOP
    	FETCH cAsignaturas INTO IdAsignatura;
        IF done THEN
        	CLOSE cAsignaturas;
            LEAVE Lazo;
        END IF;
        
        SET promedio_anual = (SELECT calcular_promedio_anual(
            						IdPeriodoLectivo, IdEstudiante,
            						IdParalelo, IdAsignatura));
        SET Suma = Suma + promedio_anual;
        SET Contador = Contador + 1;
    END LOOP Lazo;
    
    SELECT Suma / Contador INTO promedio_anual;
    
    RETURN promedio_anual;
    
END$$

DROP FUNCTION IF EXISTS `calcular_prom_aporte_estudiante`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_prom_aporte_estudiante` (`IdAporteEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_aporte FLOAT;
    DECLARE IdAsignatura INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE promedio_asignatura FLOAT;
    
    DECLARE cAsignaturas CURSOR FOR
    SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND a.id_tipo_asignatura = 1
           AND p.id_paralelo = IdParalelo;
       
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAsignaturas;
    
    Lazo: LOOP
    	FETCH cAsignaturas INTO IdAsignatura;
        IF done THEN
        	CLOSE cAsignaturas;
            LEAVE Lazo;
        END IF;
        
        SET promedio_asignatura = (SELECT calcular_promedio_aporte(
            						IdAporteEvaluacion, IdEstudiante,
            						IdParalelo, IdAsignatura));
        SET Suma = Suma + promedio_asignatura;
        SET Contador = Contador + 1;
    END LOOP Lazo;
    
    SELECT Suma / Contador INTO promedio_aporte;
    
    RETURN promedio_aporte;
END$$

DROP FUNCTION IF EXISTS `calcular_prom_quim_estudiante`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calcular_prom_quim_estudiante` (`IdPeriodoEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;
    DECLARE IdAsignatura INT;
    DECLARE Suma FLOAT DEFAULT 0;
    DECLARE Contador INT DEFAULT 0;
    DECLARE promedio_asignatura FLOAT;
    
    DECLARE cAsignaturas CURSOR FOR
    SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND a.id_tipo_asignatura = 1
           AND p.id_paralelo = IdParalelo;
       
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAsignaturas;
    
    Lazo: LOOP
    	FETCH cAsignaturas INTO IdAsignatura;
        IF done THEN
        	CLOSE cAsignaturas;
            LEAVE Lazo;
        END IF;
        
        SET promedio_asignatura = (SELECT calcular_promedio_quimestre(
            						IdPeriodoEvaluacion, IdEstudiante,
            						IdParalelo, IdAsignatura));
        SET Suma = Suma + promedio_asignatura;
        SET Contador = Contador + 1;
    END LOOP Lazo;
    
    SELECT Suma / Contador INTO promedio_quimestre;
    
    RETURN promedio_quimestre;
END$$

DROP FUNCTION IF EXISTS `calc_max_nro_matricula`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `calc_max_nro_matricula` () RETURNS VARCHAR(4) CHARSET ascii NO SQL
BEGIN
	DECLARE max_nro_matricula VARCHAR(4);

	SET max_nro_matricula = (SELECT LPAD(MAX(es_nro_matricula)+1,4,'0') FROM sw_estudiante);
	RETURN IFNULL(max_nro_matricula,'0001');

END$$

DROP FUNCTION IF EXISTS `contar_remediales`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE supletorio FLOAT DEFAULT 0;

	DECLARE cAsignaturas CURSOR FOR
	SELECT id_asignatura 
	FROM sw_paralelo_asignatura 
	WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));

		IF (promedio > 0 AND promedio < 5) OR (promedio >= 5 AND promedio < 7 AND supletorio < 7) THEN 			
			SET contador = contador + 1;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

DROP FUNCTION IF EXISTS `contar_remediales_no_aprobados`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_remediales_no_aprobados` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN -- tiene que rendir el examen supletorio
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				-- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

DROP FUNCTION IF EXISTS `contar_supletorios`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `contar_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

	DECLARE cAsignaturas CURSOR FOR
	SELECT id_asignatura 
	FROM sw_paralelo_asignatura 
	WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio > 5 AND promedio < 7 THEN 			
			SET contador = contador + 1;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

DROP FUNCTION IF EXISTS `determinar_asignatura_de_gracia`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `determinar_asignatura_de_gracia` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE vid_asignatura INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE contador INT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
	DECLARE cAsignaturas CURSOR FOR
		SELECT id_asignatura 
		  FROM sw_paralelo_asignatura 
		 WHERE id_paralelo = IdParalelo;
	
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	SET contador = (SELECT contar_remediales_no_aprobados(IdPeriodoLectivo,IdEstudiante,IdParalelo));

	IF contador = 1 THEN

		OPEN cAsignaturas;

		Lazo: LOOP
			FETCH cAsignaturas INTO IdAsignatura;
			IF done THEN
				CLOSE cAsignaturas;
				LEAVE Lazo;
			END IF;
			SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
			IF promedio > 5 AND promedio < 7 THEN -- tiene que rendir el examen supletorio
				SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
				IF examen_supletorio < 7 THEN
					-- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			ELSE 
				IF promedio > 0 AND promedio < 5 THEN -- tiene que rendir el examen remedial
					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			END IF;
		END LOOP Lazo;

	END IF;

	RETURN vid_asignatura;

END$$

DROP FUNCTION IF EXISTS `es_promocionado`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `es_promocionado` (`IdEstudiante` INT, `IdPeriodoLectivo` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	-- DECLARE IdParalelo INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	DECLARE cAsignaturas CURSOR FOR
	 SELECT id_asignatura
	   FROM sw_paralelo_asignatura
	  WHERE id_paralelo = IdParalelo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		SET promedio = (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

DROP FUNCTION IF EXISTS `secuencial_curso_asignatura`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_curso_asignatura` (`IdCurso` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(ac_orden)
		  FROM sw_asignatura_curso
		 WHERE id_curso = IdCurso);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

DROP FUNCTION IF EXISTS `secuencial_curso_especialidad`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_curso_especialidad` (`IdEspecialidad` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(cu_orden)
		  FROM sw_curso
		 WHERE id_especialidad = IdEspecialidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

DROP FUNCTION IF EXISTS `secuencial_hora_clase_dia_semana`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_hora_clase_dia_semana` (`IdDiaSemana` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(hc_ordinal)
		  FROM sw_hora_clase
		 WHERE id_dia_semana = IdDiaSemana);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

DROP FUNCTION IF EXISTS `secuencial_menu_nivel_perfil_padre`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_menu_nivel_perfil_padre` (`Nivel` INT, `IdPerfil` INT, `Padre` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(mnu_orden)
		  FROM sw_menu
		 WHERE id_perfil = IdPerfil
           AND mnu_nivel = Nivel
           AND mnu_padre = Padre);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

DROP FUNCTION IF EXISTS `secuencial_paralelo_periodo_lectivo`$$
CREATE DEFINER=`colegion`@`localhost` FUNCTION `secuencial_paralelo_periodo_lectivo` (`IdPeriodoLectivo` INT) RETURNS INT(11) NO SQL
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
