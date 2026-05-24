
DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `actualizar_id_curso_malla`$$
$$

DROP PROCEDURE IF EXISTS `actualizar_id_periodo_lectivo_paralelo`$$
$$

DROP PROCEDURE IF EXISTS `actualizar_id_usuario_sw_horario`$$
$$

DROP PROCEDURE IF EXISTS `promocionar_paralelo`$$
$$

DROP PROCEDURE IF EXISTS `sp_actualizar_nro_matricula`$$
$$

DROP PROCEDURE IF EXISTS `sp_actualizar_periodo_lectivo`$$
$$

DROP PROCEDURE IF EXISTS `sp_actualizar_usuario`$$
$$

DROP PROCEDURE IF EXISTS `sp_asociar_cierre_aporte_cursos`$$
$$

DROP PROCEDURE IF EXISTS `sp_calcular_promedio_anual`$$
CREATE PROCEDURE `sp_calcular_promedio_anual` (IN `IdPeriodoLectivo` INT, IN `IdAsignatura` INT, IN `IdParalelo` INT)  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_anual FLOAT;
    DECLARE IdEstudiante INT;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante
      FROM sw_estudiante_periodo_lectivo
     WHERE id_paralelo = IdParalelo
       AND activo = 1;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;
        
        SET promedio_anual = (SELECT calcular_promedio_anual(
            				  IdPeriodoLectivo, IdEstudiante,
            				  IdParalelo, IdAsignatura));
                                
        IF (EXISTS (SELECT * FROM sw_promedio_anual
                    WHERE id_paralelo = IdParalelo
                    AND id_asignatura = IdAsignatura 
                    AND id_estudiante = IdEstudiante
                    AND id_periodo_lectivo = IdPeriodoLectivo)) 
                    THEN
        	UPDATE sw_promedios_anualues
            SET promedio_anual = promedio_anual
            WHERE id_paralelo = IdParalelo
            AND id_asignatura = IdAsignatura 
            AND id_estudiante = IdEstudiante
            AND id_periodo_lectivo = IdPeriodoLectivo;
        ELSE
        	INSERT INTO sw_promedio_anual
            SET id_paralelo = IdParalelo,
            	id_estudiante = IdEstudiante,
            	id_asignatura = IdAsignatura,
                id_periodo_lectivo = IdPeriodoLectivo,
                promedio_anual = promedio_anual;
        END IF;
    END LOOP Lazo;
END$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_anual`$$
$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_aporte`$$
$$

DROP PROCEDURE IF EXISTS `sp_calcular_prom_quimestre`$$
$$

DROP PROCEDURE IF EXISTS `sp_cerrar_periodo_lectivo`$$
$$

DROP PROCEDURE IF EXISTS `sp_crear_cierres_periodo_lectivo`$$
$$

DROP PROCEDURE IF EXISTS `sp_crear_periodos_evaluacion_cursos`$$
CREATE PROCEDURE `sp_crear_periodos_evaluacion_cursos` (IN `IdPeriodoLectivo` INT)  BEGIN
  DECLARE cursos_done INT DEFAULT 0; 
  DECLARE IdCurso INT;
  DECLARE IdPeriodoEvaluacion INT;
  DECLARE Ponderacion FLOAT;

  DECLARE cCursos CURSOR FOR
   SELECT id_curso
     FROM sw_curso c, 
          sw_especialidad e, 
          sw_tipo_educacion t
    WHERE c.id_especialidad = e.id_especialidad 
      AND e.id_tipo_educacion = t.id_tipo_educacion  
      AND t.id_periodo_lectivo = IdPeriodoLectivo;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET cursos_done = 1;
  OPEN cCursos;

  outer_loop: LOOP
    
    FETCH FROM cCursos INTO IdCurso;

    IF cursos_done THEN
        CLOSE cCursos;
        LEAVE outer_loop;
    END IF;

    INNER_BLOCK: BEGIN
      DECLARE periodos_done INT DEFAULT 0;
      DECLARE cPeriodosEvaluacion CURSOR FOR
       SELECT p.id_periodo_evaluacion
         FROM sw_periodo_evaluacion p,
              sw_periodo_lectivo pl
        WHERE p.id_periodo_lectivo = pl.id_periodo_lectivo
          AND p.id_periodo_lectivo = IdPeriodoLectivo;

      DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET periodos_done = 1;
      OPEN cPeriodosEvaluacion;

      inner_loop: LOOP
        FETCH FROM cPeriodosEvaluacion INTO IdPeriodoEvaluacion;

		    IF periodos_done THEN
		      CLOSE cPeriodosEvaluacion;			
		      LEAVE inner_loop;
		    END IF;

		    SET Ponderacion = (
		    SELECT pe_ponderacion
		      FROM sw_periodo_evaluacion
		     WHERE id_periodo_evaluacion = IdPeriodoEvaluacion);

		    IF (NOT EXISTS (SELECT * FROM sw_periodo_evaluacion_curso WHERE id_curso = IdCurso AND id_periodo_evaluacion = IdPeriodoEvaluacion)) THEN
          INSERT INTO sw_periodo_evaluacion_curso(id_periodo_lectivo, id_periodo_evaluacion, id_curso, pe_ponderacion)
          values(IdPeriodoLectivo, IdPeriodoEvaluacion, IdCurso, Ponderacion); 
        END IF;
            
      END LOOP inner_loop;

    END INNER_BLOCK;

  END LOOP outer_loop;

END$$

DROP PROCEDURE IF EXISTS `sp_crear_rangos_supletorios`$$
CREATE PROCEDURE `sp_crear_rangos_supletorios` (IN `IdPeriodoLectivo` INT)  NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdPeriodoEvaluacion INT;
    DECLARE IdTipoPeriodoEvaluacion INT;
    
    DECLARE cPeriodosEvaluacion CURSOR FOR
    SELECT id_periodo_evaluacion, 
           id_tipo_periodo 
      FROM sw_periodo_evaluacion 
     WHERE id_periodo_lectivo = IdPeriodoLectivo 
       AND id_tipo_periodo > 1
     ORDER BY id_periodo_evaluacion ASC;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cPeriodosEvaluacion;
    
    Lazo: LOOP
    	FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion, IdTipoPeriodoEvaluacion;
        IF done THEN
        	CLOSE cPeriodosEvaluacion;
            LEAVE Lazo;
        END IF;
        
        IF (NOT EXISTS (SELECT * FROM sw_config_rangos_supletorios
                         WHERE id_periodo_lectivo = IdPeriodoLectivo
                           AND id_periodo_evaluacion = IdPeriodoEvaluacion)) 
                    THEN 
		IF IdTipoPeriodoEvaluacion = 2 THEN
        		INSERT INTO sw_config_rangos_supletorios
            	SET id_periodo_lectivo = IdPeriodoLectivo,
            	id_periodo_evaluacion = IdPeriodoEvaluacion, 
                  nota_desde = 5, 
                  nota_hasta = 6.99, 
                  nota_aprobacion = 7;
		END IF;

		IF IdTipoPeriodoEvaluacion = 3 THEN
        		INSERT INTO sw_config_rangos_supletorios
            	SET id_periodo_lectivo = IdPeriodoLectivo,
            	id_periodo_evaluacion = IdPeriodoEvaluacion, 
                  nota_desde = 0.01, 
                  nota_hasta = 4.99, 
                  nota_aprobacion = 7;
		END IF;

        END IF;
    END LOOP Lazo;
END$$

DROP PROCEDURE IF EXISTS `sp_insertar_institucion`$$
$$

DROP PROCEDURE IF EXISTS `sp_insertar_periodo_lectivo`$$
$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `aprueba_todas_asignaturas`$$
CREATE FUNCTION `aprueba_todas_asignaturas` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(1) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; 	
  DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

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
		SET promedio = (SELECT calcular_promedio_periodo_lectivo(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

DROP FUNCTION IF EXISTS `aprueba_todos_remediales`$$
$$

DROP FUNCTION IF EXISTS `aprueba_todos_supletorios`$$
$$

DROP FUNCTION IF EXISTS `calcular_comp_anual`$$
$$

DROP FUNCTION IF EXISTS `calcular_comp_asignatura`$$
CREATE FUNCTION `calcular_comp_asignatura` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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
       AND id_tipo_aporte = 1;

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
$$

DROP FUNCTION IF EXISTS `calcular_comp_insp_quimestre`$$
$$

DROP FUNCTION IF EXISTS `calcular_comp_sub_periodo`$$
CREATE FUNCTION `calcular_comp_sub_periodo` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;
	DECLARE Calificacion INT;
	DECLARE Suma INT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio INT DEFAULT 0;
	
	DECLARE cAsignaturas CURSOR FOR
	SELECT a.id_asignatura
      FROM sw_asignatura_curso ac, 
           sw_paralelo p, 
           sw_asignatura a 
     WHERE ac.id_curso = p.id_curso 
       AND ac.id_asignatura = a.id_asignatura 
       AND id_paralelo = IdParalelo 
       AND id_tipo_asignatura = 1
     ORDER BY ac_orden;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAsignaturas;

	Lazo: LOOP
		FETCH cAsignaturas INTO IdAsignatura;
		IF done THEN
			CLOSE cAsignaturas;
			LEAVE Lazo;
		END IF;
		
		SET Calificacion = (
		SELECT calcular_comp_asignatura(IdPeriodoEvaluacion, IdEstudiante, IdParalelo, IdAsignatura));
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo;

	SELECT Suma / Contador INTO Promedio;

    RETURN Promedio;
END$$

DROP FUNCTION IF EXISTS `calcular_comp_tutor`$$
$$

DROP FUNCTION IF EXISTS `calcular_examen_supletorio`$$
CREATE FUNCTION `calcular_examen_supletorio` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS FLOAT NO SQL
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

DROP FUNCTION IF EXISTS `calcular_promedio_anual`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_anual_proyectos`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_aporte`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_aporte_club`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_final`$$
CREATE FUNCTION `calcular_promedio_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0;
    DECLARE examen_supletorio FLOAT DEFAULT 0;
    DECLARE nota_aprobacion FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;
	DECLARE examen_de_gracia FLOAT DEFAULT 0;
	DECLARE anio_inicial INT DEFAULT 0;

    SET nota_aprobacion = (SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = IdPeriodoLectivo);

	SET anio_inicial = (SELECT pe_anio_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = IdPeriodoLectivo);

	IF anio_inicial < 2023 THEN
		SET promedio_final = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));

		IF promedio_final >= 5 AND promedio_final < 7 THEN 		
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio >= nota_aprobacion THEN
				SET promedio_final = nota_aprobacion;
			ELSE
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,	IdParalelo,IdAsignatura,3));
				IF examen_remedial >= nota_aprobacion THEN
					SET promedio_final = nota_aprobacion;
				ELSE
					SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
					IF examen_de_gracia >= nota_aprobacion THEN
						SET promedio_final = nota_aprobacion;
					END IF;
				END IF;
			END IF;
		ELSE 
			IF promedio_final > 0 AND promedio_final < 5 THEN
				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial >= nota_aprobacion THEN
					SET promedio_final = nota_aprobacion;
				ELSE
					SET examen_de_gracia = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,4));
					IF examen_de_gracia >= nota_aprobacion THEN
						SET promedio_final = nota_aprobacion;
					END IF;
				END IF;
			END IF;
		END IF;
	ELSE
		SET promedio_final = (SELECT calcular_promedio_periodo_lectivo(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));

		IF promedio_final > 4 AND promedio_final < 7 THEN 
			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));

			IF examen_supletorio >= nota_aprobacion THEN
				SET promedio_final = (SELECT nota_final FROM sw_escala_supletorios WHERE examen_supletorio >= nota_minima AND examen_supletorio <= nota_maxima);
			END IF;
		END IF;
	END IF;
	
	RETURN promedio_final;

END$$

DROP FUNCTION IF EXISTS `calcular_promedio_general`$$
$$
CREATE FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

DROP FUNCTION IF EXISTS `calcular_promedio_periodo_lectivo`$$
CREATE FUNCTION `calcular_promedio_periodo_lectivo` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_sub_periodo FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
    DECLARE PonderacionPeriodo FLOAT;
	DECLARE suma_ponderados FLOAT DEFAULT 0;
	
	DECLARE cPeriodosEvaluacion CURSOR FOR
	SELECT pc.id_periodo_evaluacion,
		   pc.pe_ponderacion 
	  FROM sw_periodo_evaluacion pe, 
		   sw_periodo_evaluacion_curso pc 
	 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
	   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
	   AND pc.id_curso = (SELECT id_curso FROM sw_paralelo WHERE id_paralelo = IdParalelo) 
	   AND pc.id_periodo_lectivo = IdPeriodoLectivo
	   AND id_tipo_periodo IN (1, 7, 8);

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodosEvaluacion;

	Lazo: LOOP
		FETCH cPeriodosEvaluacion INTO IdPeriodoEvaluacion, PonderacionPeriodo;
		IF done THEN
			CLOSE cPeriodosEvaluacion;
			LEAVE Lazo;
		END IF;
		SET promedio_sub_periodo = (SELECT calcular_promedio_sub_periodo(IdPeriodoEvaluacion,IdEstudiante,IdParalelo,IdAsignatura));
		SET suma_ponderados = suma_ponderados + promedio_sub_periodo * PonderacionPeriodo;
	END LOOP Lazo;

	RETURN suma_ponderados;
END$$

DROP FUNCTION IF EXISTS `calcular_promedio_quimestre`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_quimestre_club`$$
$$

DROP FUNCTION IF EXISTS `calcular_promedio_sub_periodo`$$
CREATE FUNCTION `calcular_promedio_sub_periodo` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdAporteEvaluacion INT;
    DECLARE PonderacionAporte FLOAT;
    DECLARE promedio_aporte FLOAT DEFAULT 0;
    DECLARE suma_ponderados_aporte FLOAT DEFAULT 0;
    
        DECLARE cAportesEvaluacion CURSOR FOR
    	SELECT id_aporte_evaluacion, 
               ap_ponderacion 
          FROM sw_aporte_evaluacion
         WHERE id_periodo_evaluacion = IdPeriodoEvaluacion;
         
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cAportesEvaluacion;
    
    Lazo: LOOP
    	FETCH cAportesEvaluacion INTO IdAporteEvaluacion, PonderacionAporte;

        IF done THEN
			CLOSE cAportesEvaluacion;
			LEAVE Lazo;
		END IF;
        
        SELECT calcular_promedio_aporte (IdAporteEvaluacion, IdEstudiante, IdParalelo, IdAsignatura) INTO promedio_aporte;
        
        SET suma_ponderados_aporte = suma_ponderados_aporte + promedio_aporte * PonderacionAporte;
    END LOOP Lazo;
    
    RETURN suma_ponderados_aporte;
    
END$$

DROP FUNCTION IF EXISTS `calcular_prom_anual_estudiante`$$
$$

DROP FUNCTION IF EXISTS `calcular_prom_aporte_estudiante`$$
$$

DROP FUNCTION IF EXISTS `calcular_prom_final_cualitativa`$$
$$

DROP FUNCTION IF EXISTS `calcular_prom_quim_cualitativa`$$
$$

DROP FUNCTION IF EXISTS `calcular_prom_quim_estudiante`$$
$$

DROP FUNCTION IF EXISTS `calc_max_nro_matricula`$$
$$

DROP FUNCTION IF EXISTS `contar_remediales`$$
$$

DROP FUNCTION IF EXISTS `contar_remediales_no_aprobados`$$
$$

DROP FUNCTION IF EXISTS `contar_supletorios`$$
$$

DROP FUNCTION IF EXISTS `determinar_asignatura_de_gracia`$$
$$

DROP FUNCTION IF EXISTS `es_promocionado`$$
$$

DROP FUNCTION IF EXISTS `existeExamenSupRemGracia`$$
CREATE FUNCTION `existeExamenSupRemGracia` (`IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `IdTipoPeriodo` INT, `IdPeriodoLectivo` INT) RETURNS TINYINT(1) NO SQL
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

DROP FUNCTION IF EXISTS `obtenerExamenSupRemGracia`$$
CREATE FUNCTION `obtenerExamenSupRemGracia` (`IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `IdTipoPeriodo` INT, `IdPeriodoLectivo` INT) RETURNS TINYINT(1) NO SQL
BEGIN

	DECLARE IdRubricaEvaluacion INT;
    DECLARE Calificacion FLOAT;
	
	SET IdRubricaEvaluacion = (
		SELECT id_rubrica_evaluacion 
        FROM sw_rubrica_evaluacion r, 
        sw_aporte_evaluacion a, 
        sw_periodo_evaluacion p 
        WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
        AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
        AND p.id_tipo_periodo = IdTipoPeriodo 
        AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET Calificacion = (SELECT re_calificacion 
                    FROM sw_rubrica_estudiante 
                    WHERE id_estudiante = IdEstudiante 
                    AND id_paralelo = IdParalelo 
                    AND id_asignatura = IdAsignatura 
                    AND id_rubrica_personalizada = IdRubricaEvaluacion);

    SET Calificacion = IFNULL(Calificacion, 0);

    RETURN Calificacion;

END$$

DROP FUNCTION IF EXISTS `obtenerFechaCierreSupRemGracia`$$
CREATE FUNCTION `obtenerFechaCierreSupRemGracia` (`IdParalelo` INT, `IdTipoPeriodo` INT, `IdPeriodoLectivo` INT) RETURNS DATE NO SQL
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

DROP FUNCTION IF EXISTS `secuencial_curso_asignatura`$$
$$

DROP FUNCTION IF EXISTS `secuencial_curso_especialidad`$$
$$

DROP FUNCTION IF EXISTS `secuencial_hora_clase_dia_semana`$$
$$

DROP FUNCTION IF EXISTS `secuencial_menu_nivel_perfil_padre`$$
$$

DROP FUNCTION IF EXISTS `secuencial_modalidad`$$
CREATE FUNCTION `secuencial_modalidad` () RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(mo_orden)
		  FROM sw_modalidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

DROP FUNCTION IF EXISTS `secuencial_paralelo_periodo_lectivo`$$
$$

DROP FUNCTION IF EXISTS `secuencial_tipo_educacion`$$
$$

DELIMITER ;
