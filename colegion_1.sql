-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 05-04-2021 a las 02:35:50
-- Versión del servidor: 10.3.27-MariaDB-cll-lve
-- Versión de PHP: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegion_1`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `actualizar_id_curso_malla` ()  NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_actualizar_nro_matricula` (IN `IdPeriodoLectivo` INT)  NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdEstudiante INT;
    DECLARE Contador INT DEFAULT 0;
    
    DECLARE cEstudiantes CURSOR FOR
    SELECT id_estudiante 
      FROM sw_estudiante_periodo_lectivo
     WHERE id_periodo_lectivo = IdPeriodoLectivo 
     ORDER BY id_estudiante_periodo_lectivo ASC;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cEstudiantes;
    
    Lazo: LOOP
    	FETCH cEstudiantes INTO IdEstudiante;
        IF done THEN
        	CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;

	SET Contador = Contador + 1;
        
        UPDATE sw_estudiante_periodo_lectivo
	       SET nro_matricula = Contador
	     WHERE id_estudiante = IdEstudiante
	       AND id_periodo_lectivo = IdPeriodoLectivo;
    END LOOP Lazo;
END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_actualizar_periodo_lectivo` (IN `IdPeriodoLectivo` INT, IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` DATE, IN `FechaFin` DATE, IN `IdModalidad` INT)  NO SQL
UPDATE sw_periodo_lectivo
   SET pe_anio_inicio = AnioInicial,
       pe_anio_fin = AnioFinal,
       pe_fecha_inicio = FechaInicial,
       pe_fecha_fin = FechaFin,
       id_modalidad = IdModalidad
 WHERE id_periodo_lectivo = IdPeriodoLectivo$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_actualizar_usuario` (IN `IdUsuario` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsNombreCompleto` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64), IN `UsActivo` INT, IN `UsGenero` VARCHAR(1), IN `UsFoto` VARCHAR(64))  NO SQL
BEGIN
	UPDATE sw_usuario SET
	us_titulo = UsTitulo,
	us_apellidos = UsApellidos,
	us_nombres = UsNombres,
	us_fullname = UsNombreCompleto,
	us_login = UsLogin,
	us_password = UsPassword,
    us_activo = UsActivo,
    us_genero = UsGenero,
    us_foto = UsFoto
	WHERE id_usuario = IdUsuario;
END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_asociar_cierre_aporte_cursos` (IN `IdPeriodoLectivo` INT, IN `IdAporteEvaluacion` INT, IN `FechaApertura` DATE, IN `FechaCierre` DATE)  NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE IdParalelo INT;
    
    DECLARE cParalelos CURSOR FOR
    SELECT id_paralelo 
      FROM sw_paralelo p,
           sw_curso c, 
           sw_especialidad e, 
           sw_tipo_educacion t 
     WHERE p.id_curso = c.id_curso
       AND c.id_especialidad = e.id_especialidad 
       AND e.id_tipo_educacion = t.id_tipo_educacion 
       AND t.id_periodo_lectivo = IdPeriodoLectivo 
     ORDER BY c.id_especialidad, id_paralelo ASC;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    OPEN cParalelos;
    
    Lazo: LOOP
    	FETCH cParalelos INTO IdParalelo;
        IF done THEN
        	CLOSE cParalelos;
            LEAVE Lazo;
        END IF;
        
        IF (NOT EXISTS (SELECT * FROM sw_aporte_paralelo_cierre
                         WHERE id_aporte_evaluacion = IdAporteEvaluacion
                           AND id_paralelo = IdParalelo)) 
                    THEN
        	INSERT INTO sw_aporte_paralelo_cierre
            SET id_aporte_evaluacion = IdAporteEvaluacion,
            	id_paralelo = IdParalelo,
                ap_fecha_apertura = FechaApertura,
                ap_fecha_cierre = FechaCierre,
                ap_estado = 'C';
        END IF;
    END LOOP Lazo;
END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_calcular_prom_anual` (IN `IdPeriodoLectivo` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_calcular_prom_aporte` (IN `IdAporteEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_calcular_prom_quimestre` (IN `IdPeriodoEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_cerrar_periodo_lectivo` (IN `IdPeriodoLectivo` INT)  NO SQL
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
		   AND p.id_periodo_lectivo = IdPeriodoLectivo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	-- Actualizo el estado del periodo lectivo anterior
	UPDATE sw_periodo_lectivo
	   SET pe_estado = 'T',
           id_periodo_estado = 2
	 WHERE id_periodo_lectivo = IdPeriodoLectivo;

	-- Aqui actualizo a 'C' todos los periodos de evaluacion

	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_curso_cierre
		   SET ap_estado = 'C'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;
	
END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_crear_cierres_periodo_lectivo` (IN `IdPeriodoLectivo` INT)  BEGIN
    DECLARE paralelos_done INT DEFAULT 0; 
    DECLARE IdParalelo INT;
    DECLARE IdCurso INT;
    DECLARE IdAporteEvaluacion INT;
    DECLARE ApEstado VARCHAR(1);
    DECLARE ApFechaApertura DATE;
    DECLARE ApFechaCierre DATE;

    DECLARE cParalelos CURSOR FOR
    SELECT id_paralelo
      FROM sw_paralelo p, 
           sw_curso c, 
           sw_especialidad e, 
           sw_tipo_educacion t
     WHERE p.id_curso = c.id_curso 
       AND c.id_especialidad = e.id_especialidad 
       AND e.id_tipo_educacion = t.id_tipo_educacion  
       AND t.id_periodo_lectivo = IdPeriodoLectivo;

    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET paralelos_done = 1;
    OPEN cParalelos;

    outer_loop: LOOP
      
        FETCH FROM cParalelos INTO IdParalelo;

	IF paralelos_done THEN
	    CLOSE cParalelos;
	    LEAVE outer_loop;
	END IF;

        INNER_BLOCK: BEGIN
            DECLARE aportes_done INT DEFAULT 0;
            DECLARE cAportesEvaluacion CURSOR FOR
            SELECT a.id_aporte_evaluacion
              FROM sw_aporte_evaluacion a,
                   sw_periodo_evaluacion p,
                   sw_periodo_lectivo pl
             WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion
               AND p.id_periodo_lectivo = pl.id_periodo_lectivo
               AND p.id_periodo_lectivo = IdPeriodoLectivo;

            DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET aportes_done = 1;
            OPEN cAportesEvaluacion;

            inner_loop: LOOP
                FETCH FROM cAportesEvaluacion INTO IdAporteEvaluacion;

		IF aportes_done THEN
		    CLOSE cAportesEvaluacion;			
		    LEAVE inner_loop;
		END IF;

		SET IdCurso = (
		SELECT id_curso
		  FROM sw_paralelo 
		 WHERE id_paralelo = IdParalelo);

		SET ApEstado = (
		SELECT ap_estado
		  FROM sw_aporte_curso_cierre
		 WHERE id_curso = IdCurso
		   AND id_aporte_evaluacion = IdAporteEvaluacion);

		SET ApFechaApertura = (
		SELECT ap_fecha_apertura
		  FROM sw_aporte_curso_cierre
		 WHERE id_curso = IdCurso
		   AND id_aporte_evaluacion = IdAporteEvaluacion);

		SET ApFechaCierre = (
		SELECT ap_fecha_cierre
		  FROM sw_aporte_curso_cierre
		 WHERE id_curso = IdCurso
		   AND id_aporte_evaluacion = IdAporteEvaluacion);

                INSERT INTO sw_aporte_paralelo_cierre(id_aporte_evaluacion, id_paralelo, ap_estado, ap_fecha_apertura, ap_fecha_cierre)
                values(IdAporteEvaluacion, IdParalelo, ApEstado, ApFechaApertura, ApFechaCierre); 
            
            END LOOP inner_loop;

        END INNER_BLOCK;

    END LOOP outer_loop;

END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_insertar_institucion` (IN `In_nombre` VARCHAR(64), IN `In_direccion` VARCHAR(45), IN `In_telefono1` VARCHAR(64), IN `In_nom_rector` VARCHAR(45), IN `In_nom_secretario` VARCHAR(45), IN `In_copiar_y_pegar` INT)  NO SQL
BEGIN
	IF (EXISTS (SELECT * FROM sw_institucion)) THEN
		UPDATE sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario,
        in_copiar_y_pegar = In_copiar_y_pegar
        WHERE id_institucion = 1;
	ELSE
		INSERT INTO sw_institucion
		SET in_nombre = In_nombre,
		in_direccion = In_direccion,
		in_telefono1 = In_telefono1,
		in_nom_rector = In_nom_rector,
		in_nom_secretario = In_nom_secretario,
        in_copiar_y_pegar = In_copiar_y_pegar;
	END IF;
END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_insertar_periodo_lectivo` (IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` DATE, IN `FechaFin` DATE, IN `IdModalidad` INT)  NO SQL
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
		   AND a.ap_tipo < 3;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	-- Primero debo verificar si hay un periodo lectivo anterior
	
	SET @IdPeriodoLectivoAnterior = (SELECT id_periodo_lectivo
                                      FROM sw_periodo_lectivo
                                     WHERE pe_anio_inicio = AnioInicial - 1);

	-- SELECT @IdPeriodoLectivoAnterior;

	IF @IdPeriodoLectivoAnterior IS NOT NULL THEN
		-- Actualizo el estado del periodo lectivo anterior
		UPDATE sw_periodo_lectivo
		   SET pe_estado = 'T',
               id_periodo_estado = 2
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
	INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado, pe_fecha_inicio, pe_fecha_fin, id_modalidad, id_periodo_estado, id_institucion)
	VALUES (AnioInicial, AnioFinal, 'A', FechaInicial, FechaFin, IdModalidad, 1, 1);

END$$

CREATE DEFINER=`colegion_1`@`localhost` PROCEDURE `sp_insertar_usuario` (IN `IdPeriodoLectivo` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsShortname` VARCHAR(64), IN `UsFullname` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64), IN `UsGenero` VARCHAR(1), IN `UsFoto` INT(64))  NO SQL
BEGIN
	DECLARE max_id INT;
	INSERT INTO sw_usuario (
		id_periodo_lectivo,
		id_perfil,
		us_titulo,
		us_apellidos,
		us_nombres,
        us_shortname,
		us_fullname,
		us_login,
		us_password,
        us_activo,
        us_genero,
        us_foto
	) VALUES (
		IdPeriodoLectivo,
		IdPerfil,
		UsTitulo,
		UsApellidos,
		UsNombres,
        UsShortname,
		UsFullname,
		UsLogin,
		UsPassword,
        1,
        UsGenero,
        UsFoto
	);
    SET max_id = (SELECT MAX(id_usuario) FROM sw_usuario);
    INSERT INTO sw_usuario_perfil SET id_usuario = max_id, id_perfil = IdPerfil;
END$$

--
-- Funciones
--
CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `aprueba_todas_asignaturas` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(1) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
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
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `aprueba_todos_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `aprueba_todos_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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
		   AND id_tipo_periodo = 1;

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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_asignatura` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_insp_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_comp_tutor` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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
		SELECT ec_correlativa
		  FROM sw_comportamiento_tutor ct,
               sw_escala_comportamiento ec
		 WHERE ec.id_escala_comportamiento = ct.id_escala_comportamiento 
           AND id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEvaluacion);
           
        SET Calificacion = IFNULL(Calificacion, 0);

		SET Suma = Suma + Calificacion;
		SET Contador = Contador + 1;
	END LOOP Lazo1;

	SET Promedio = Suma / Contador;

	RETURN Promedio;
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_examen_supletorio` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS FLOAT NO SQL
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
				      AND p.id_tipo_periodo = PePrincipal 
				      AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
				   FROM sw_rubrica_estudiante 
				  WHERE id_estudiante = IdEstudiante 
				    AND id_paralelo = IdParalelo 
				    AND id_asignatura = IdAsignatura 
				    AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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
		   AND id_tipo_periodo = 1;

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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_anual_proyectos` (`IdPeriodoLectivo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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
	   AND id_tipo_periodo = 1;

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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_aporte` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_aporte_club` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT READS SQL DATA
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE promedio_final FLOAT DEFAULT 0; 	
	DECLARE examen_supletorio FLOAT DEFAULT 0;
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; -- variable de salida de la funcion
	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
	DECLARE IdAsignatura INT;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
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
		SET suma = suma + (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_promedio_quimestre_club` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_prom_anual_estudiante` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_prom_aporte_estudiante` (`IdAporteEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_prom_final_cualitativa` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE prom_quim FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	DECLARE IdPeriodoEval INT;

	DECLARE cPeriodos CURSOR FOR
	SELECT id_periodo_evaluacion
	  FROM sw_periodo_evaluacion
	 WHERE id_tipo_periodo = 1
	   AND id_periodo_lectivo = IdPeriodoLectivo;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cPeriodos;

	LAZO: LOOP
		FETCH cPeriodos INTO IdPeriodoEval;
		IF done THEN
			CLOSE cPeriodos;
			LEAVE LAZO;
		END IF;

		SET prom_quim = (
		SELECT calcular_prom_quim_cualitativa(
		  IdPeriodoEval,
		  IdEstudiante,
		  IdParalelo,
		  IdAsignatura));
           
		SET Suma = Suma + prom_quim;
		SET Contador = Contador + 1;
	END LOOP LAZO;

	SELECT Suma / Contador INTO Promedio;
	
	RETURN Promedio;
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_prom_quim_cualitativa` (`IdPeriodoEval` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE Calificacion VARCHAR(2);
	DECLARE Cuantitativa FLOAT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	DECLARE Promedio FLOAT DEFAULT 0;
	DECLARE IdAporteEval INT;

	DECLARE cAportes CURSOR FOR
	SELECT id_aporte_evaluacion
	  FROM sw_aporte_evaluacion
	 WHERE ap_tipo = 1
	   AND id_periodo_evaluacion = IdPeriodoEval;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	OPEN cAportes;

	LAZO: LOOP
		FETCH cAportes INTO IdAporteEval;
		IF done THEN
			CLOSE cAportes;
			LEAVE LAZO;
		END IF;

		SET Calificacion = (
		SELECT rc_calificacion
		  FROM sw_rubrica_cualitativa
		 WHERE id_asignatura = IdAsignatura
		   AND id_estudiante = IdEstudiante
		   AND id_paralelo = IdParalelo
		   AND id_aporte_evaluacion = IdAporteEval);
           
	    SET Calificacion = IFNULL(Calificacion, 'SN');

		SET Cuantitativa = (
		SELECT ec_correlativa
		  FROM sw_escala_proyectos
		 WHERE ec_abreviatura = Calificacion);

		SET Suma = Suma + Cuantitativa;
		SET Contador = Contador + 1;
	END LOOP LAZO;

	SELECT Suma / Contador INTO Promedio;
	
	RETURN Promedio;
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calcular_prom_quim_estudiante` (`IdPeriodoEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `calc_max_nro_matricula` () RETURNS VARCHAR(4) CHARSET ascii NO SQL
BEGIN
	DECLARE max_nro_matricula VARCHAR(4);

	SET max_nro_matricula = (SELECT LPAD(MAX(es_nro_matricula)+1,4,'0') FROM sw_estudiante);
	RETURN IFNULL(max_nro_matricula,'0001');

END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `contar_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE supletorio FLOAT DEFAULT 0;

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
		SET promedio = (SELECT calcular_promedio_anual(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));

		IF (promedio > 0 AND promedio < 5) OR (promedio >= 5 AND promedio < 7 AND supletorio < 7) THEN 			
			SET contador = contador + 1;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `contar_remediales_no_aprobados` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; -- variable de salida de la funcion
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

	-- Aqui determino si el estudiante aprueba en todas las asignaturas
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `contar_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `determinar_asignatura_de_gracia` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
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
		SELECT a.id_asignatura
          FROM sw_asignatura a, 
               sw_asignatura_curso ac, 
               sw_paralelo p 
         WHERE a.id_asignatura = ac.id_asignatura 
           AND p.id_curso = ac.id_curso 
           AND a.id_tipo_asignatura = 1
           AND p.id_paralelo = IdParalelo;
	
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `es_promocionado` (`IdEstudiante` INT, `IdPeriodoLectivo` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; -- variable de salida de la funcion
	-- DECLARE IdParalelo INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE IdAsignatura INT;

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
		SET promedio = (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		IF promedio < 7 THEN
			SET done = 1;
			SET aprueba = FALSE;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_curso_asignatura` (`IdCurso` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(ac_orden)
		  FROM sw_asignatura_curso
		 WHERE id_curso = IdCurso);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_curso_especialidad` (`IdEspecialidad` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(cu_orden)
		  FROM sw_curso
		 WHERE id_especialidad = IdEspecialidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_hora_clase_dia_semana` (`IdDiaSemana` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(hc_ordinal)
		  FROM sw_hora_clase
		 WHERE id_dia_semana = IdDiaSemana);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_menu_nivel_perfil_padre` (`Nivel` INT, `IdPerfil` INT, `Padre` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_paralelo_periodo_lectivo` (`IdPeriodoLectivo` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`colegion_1`@`localhost` FUNCTION `secuencial_tipo_educacion` (`IdPeriodoLectivo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(te_orden)
		  FROM sw_tipo_educacion
		 WHERE id_periodo_lectivo = IdPeriodoLectivo);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_curso_cierre`
--

CREATE TABLE `sw_aporte_curso_cierre` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_evaluacion`
--

CREATE TABLE `sw_aporte_evaluacion` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_tipo_aporte` int(11) NOT NULL,
  `ap_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_shortname` varchar(45) NOT NULL,
  `ap_abreviatura` varchar(8) NOT NULL,
  `ap_tipo` tinyint(4) NOT NULL,
  `ap_estado` varchar(1) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_paralelo_cierre`
--

CREATE TABLE `sw_aporte_paralelo_cierre` (
  `id_aporte_paralelo_cierre` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL,
  `ap_estado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) NOT NULL,
  `ar_nombre` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL,
  `as_nombre` varchar(84) COLLATE latin1_spanish_ci NOT NULL,
  `as_abreviatura` varchar(12) COLLATE latin1_spanish_ci NOT NULL,
  `as_shortname` varchar(45) COLLATE latin1_spanish_ci NOT NULL,
  `as_curricular` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura_curso`
--

CREATE TABLE `sw_asignatura_curso` (
  `id_asignatura_curso` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ac_orden` int(11) NOT NULL,
  `ac_num_horas` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asistencia_estudiante`
--

CREATE TABLE `sw_asistencia_estudiante` (
  `id_asistencia_estudiante` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `ae_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asistencia_tutor`
--

CREATE TABLE `sw_asistencia_tutor` (
  `id_asistencia_tutor` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_inasistencia` int(11) NOT NULL,
  `at_fecha` date NOT NULL,
  `at_justificacion` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) NOT NULL,
  `id_curso_inferior` int(11) DEFAULT NULL,
  `id_curso_superior` int(11) DEFAULT NULL,
  `id_periodo_lectivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_calificacion_comportamiento`
--

CREATE TABLE `sw_calificacion_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `co_calificacion` int(11) NOT NULL,
  `co_cualitativa` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club`
--

CREATE TABLE `sw_club` (
  `id_club` int(11) NOT NULL,
  `cl_nombre` varchar(32) NOT NULL,
  `cl_abreviatura` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_club_docente`
--

CREATE TABLE `sw_club_docente` (
  `id_club_docente` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_comentario_padre` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) NOT NULL,
  `co_nombre` varchar(64) NOT NULL,
  `co_texto` varchar(250) NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento`
--

CREATE TABLE `sw_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_indice_evaluacion` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_inspector`
--

CREATE TABLE `sw_comportamiento_inspector` (
  `id_comportamiento_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento_tutor`
--

CREATE TABLE `sw_comportamiento_tutor` (
  `id_comportamiento_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_escala_comportamiento` int(11) NOT NULL,
  `co_calificacion` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso`
--

CREATE TABLE `sw_curso` (
  `id_curso` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `cu_nombre` varchar(128) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cu_shortname` varchar(45) NOT NULL,
  `cu_orden` int(11) NOT NULL,
  `id_curso_superior` int(11) NOT NULL,
  `bol_proyectos` tinyint(1) NOT NULL,
  `cu_abreviatura` varchar(5) NOT NULL,
  `quien_inserta_comp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `cs_nombre` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ds_nombre` varchar(10) NOT NULL,
  `ds_ordinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_distributivo`
--

CREATE TABLE `sw_distributivo` (
  `id_distributivo` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_malla_curricular` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_calificaciones`
--

CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ec_cualitativa` varchar(64) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) NOT NULL,
  `ec_cualitativa` varchar(164) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_proyectos`
--

CREATE TABLE `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL,
  `ec_cualitativa` varchar(256) NOT NULL,
  `ec_cuantitativa` varchar(16) NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) NOT NULL,
  `ec_abreviatura` varchar(2) NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) NOT NULL,
  `id_tipo_educacion` int(11) NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(50) NOT NULL,
  `es_abreviatura` varchar(15) NOT NULL,
  `es_orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `es_nro_matricula` int(11) NOT NULL,
  `es_apellidos` varchar(32) NOT NULL,
  `es_nombres` varchar(32) NOT NULL,
  `es_nombre_completo` varchar(64) NOT NULL,
  `es_cedula` varchar(10) NOT NULL,
  `es_genero` varchar(1) NOT NULL,
  `es_email` varchar(64) NOT NULL,
  `es_sector` varchar(36) NOT NULL,
  `es_direccion` varchar(64) NOT NULL,
  `es_telefono` varchar(32) NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_club`
--

CREATE TABLE `sw_estudiante_club` (
  `id_estudiante_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `es_retirado` varchar(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_periodo_lectivo`
--

CREATE TABLE `sw_estudiante_periodo_lectivo` (
  `id_estudiante_periodo_lectivo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `es_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_retirado` varchar(1) NOT NULL DEFAULT 'N',
  `nro_matricula` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `sw_estudiante_periodo_lectivo`
--
DELIMITER $$
CREATE TRIGGER `tg_update_estudiante_periodo_lectivo` AFTER UPDATE ON `sw_estudiante_periodo_lectivo` FOR EACH ROW UPDATE sw_rubrica_estudiante 
   SET id_paralelo = new.id_paralelo
 WHERE id_estudiante = new.id_estudiante
   AND id_paralelo = old.id_paralelo
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_promedio_parcial`
--

CREATE TABLE `sw_estudiante_promedio_parcial` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `ep_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_prom_anual`
--

CREATE TABLE `sw_estudiante_prom_anual` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ea_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_prom_quimestral`
--

CREATE TABLE `sw_estudiante_prom_quimestral` (
  `id` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `eq_promedio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_feriado`
--

CREATE TABLE `sw_feriado` (
  `id_feriado` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `fe_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_foro`
--

CREATE TABLE `sw_foro` (
  `id_foro` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fo_titulo` varchar(50) NOT NULL,
  `fo_descripcion` varchar(250) NOT NULL,
  `fo_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario`
--

CREATE TABLE `sw_horario` (
  `id_horario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_horario_examen`
--

CREATE TABLE `sw_horario_examen` (
  `id_horario_examen` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_examen` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `he_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `hc_nombre` varchar(12) NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_ordinal` int(11) NOT NULL,
  `hc_tipo` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_dia`
--

CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_inasistencia`
--

CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) NOT NULL,
  `in_abreviatura` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_indice_evaluacion`
--

CREATE TABLE `sw_indice_evaluacion` (
  `id_indice_evaluacion` int(11) NOT NULL,
  `valores_t` float NOT NULL,
  `cum_norma_t` float NOT NULL,
  `pun_asiste_t` float NOT NULL,
  `presentacion_t` float NOT NULL,
  `valores_i` float NOT NULL,
  `cum_norma_i` float NOT NULL,
  `pun_asiste_i` float NOT NULL,
  `presentacion_i` float NOT NULL,
  `total` float NOT NULL,
  `promedio` float NOT NULL,
  `equivalencia` varchar(1) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_indice_evaluacion_def`
--

CREATE TABLE `sw_indice_evaluacion_def` (
  `id_indice_evaluacion` int(11) NOT NULL,
  `ie_descripcion` varchar(64) NOT NULL,
  `ie_abreviatura` varchar(8) NOT NULL,
  `ie_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) NOT NULL,
  `in_direccion` varchar(45) NOT NULL,
  `in_telefono1` varchar(64) NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8 NOT NULL,
  `in_nom_vicerrector` varchar(45) NOT NULL,
  `in_nom_secretario` varchar(45) NOT NULL,
  `in_url` varchar(64) NOT NULL,
  `in_logo` varchar(64) NOT NULL,
  `in_amie` varchar(16) NOT NULL,
  `in_ciudad` varchar(64) NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_insumo`
--

CREATE TABLE `sw_insumo` (
  `id_insumo` int(11) NOT NULL,
  `in_nombre` varchar(50) NOT NULL,
  `in_descripcion` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_jornada`
--

CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) NOT NULL,
  `jo_nombre` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_malla_curricular`
--

CREATE TABLE `sw_malla_curricular` (
  `id_malla_curricular` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `ma_horas_presenciales` int(11) NOT NULL,
  `ma_horas_autonomas` int(11) NOT NULL,
  `ma_horas_tutorias` int(11) NOT NULL,
  `ma_subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu`
--

CREATE TABLE `sw_menu` (
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `mnu_texto` varchar(32) CHARACTER SET latin1 NOT NULL,
  `mnu_enlace` varchar(64) CHARACTER SET latin1 NOT NULL,
  `mnu_link` varchar(64) CHARACTER SET latin1 NOT NULL,
  `mnu_nivel` int(11) NOT NULL,
  `mnu_orden` int(11) NOT NULL,
  `mnu_padre` int(11) NOT NULL,
  `mnu_publicado` int(11) NOT NULL,
  `mnu_icono` varchar(25) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sw_menu`
--

INSERT INTO `sw_menu` (`id_menu`, `id_perfil`, `mnu_texto`, `mnu_enlace`, `mnu_link`, `mnu_nivel`, `mnu_orden`, `mnu_padre`, `mnu_publicado`, `mnu_icono`) VALUES
(2, 1, 'Menus', 'menu/index2.php', 'administracion/menus', 2, 5, 146, 1, 'fa fa-circle-o'),
(12, 1, 'Perfiles', 'perfil/view_index.php', 'administracion/perfiles', 2, 4, 146, 1, 'fa fa-circle-o'),
(13, 2, 'Calificaciones', '#', '', 1, 2, 0, 1, ''),
(18, 1, 'Usuarios', 'usuario/view_index.php', 'administracion/usuarios', 2, 6, 146, 1, 'fa fa-circle-o'),
(21, 3, 'Matriculación', '#', '#', 1, 4, 0, 1, 'fa fa-share-alt'),
(26, 5, 'Comportamiento', '#', '', 1, 3, 0, 1, ''),
(28, 3, 'Definiciones', '#', '#', 1, 2, 0, 1, 'fa fa-cogs'),
(31, 6, 'Definiciones', '#', '', 1, 2, 0, 1, ''),
(32, 3, 'Libretación', '#', '', 1, 5, 0, 1, ''),
(37, 3, 'Reporte', '#', '', 1, 6, 0, 1, ''),
(38, 2, 'Reportes', '#', '', 1, 6, 0, 1, ''),
(39, 3, 'A Excel', '#', '', 1, 7, 0, 1, ''),
(40, 3, 'Promoción', 'promocion/index.php', '', 1, 8, 0, 1, ''),
(43, 1, 'Cierres', '#', '#', 1, 5, 0, 1, 'fa fa-lock'),
(45, 2, 'Informes', '#', '', 1, 5, 0, 1, ''),
(48, 2, 'Listas', '#', '', 1, 7, 0, 1, ''),
(52, 7, 'Reportes', '#', '', 1, 4, 0, 1, ''),
(56, 2, 'Cuestionarios', 'cuestionarios/index.php', '', 1, 8, 0, 0, ''),
(58, 7, 'Comportamiento', '#', '', 1, 1, 0, 1, ''),
(67, 2, 'Supletorios', 'calificaciones/supletorios.php', '', 2, 3, 13, 1, ''),
(68, 2, 'Remediales', 'calificaciones/remediales.php', '', 2, 4, 13, 1, ''),
(69, 2, 'De Gracia', 'calificaciones/de_gracia.php', '', 2, 5, 13, 1, ''),
(71, 2, 'Parciales', 'calificaciones/informe_parciales2.php', '', 2, 1, 45, 1, ''),
(73, 2, 'Anuales', 'calificaciones/informe_anual.php', '', 2, 3, 45, 1, ''),
(74, 2, 'Quimestrales', 'reportes/por_periodo.php', '', 2, 1, 38, 1, ''),
(75, 2, 'Anuales', 'calificaciones/reporte_anual.php', '', 2, 2, 38, 1, ''),
(76, 2, 'Por Parcial', 'listas_estudiantes/por_parcial.php', '', 2, 1, 48, 1, ''),
(77, 2, 'Por Quimestre', 'listas_estudiantes/por_quimestre.php', '', 2, 2, 48, 1, ''),
(78, 5, 'Quimestral', 'inspeccion/comportamiento.php', '', 2, 2, 26, 1, ''),
(79, 5, 'Anual', 'inspeccion/comportamiento_anual.php', '', 2, 5, 26, 1, ''),
(97, 3, 'Validar calificaciones', 'calificaciones/validar_calificaciones.php', '', 2, 1, 32, 1, ''),
(98, 3, 'Libretación', 'reportes/libretacion.php', '', 2, 2, 32, 1, ''),
(99, 3, 'Por Asignatura', 'calificaciones/por_asignaturas.php', '', 2, 2, 37, 1, ''),
(100, 3, 'Parciales', 'calificaciones/reporte_parciales.php', '', 2, 3, 37, 1, ''),
(101, 3, 'Quimestral', 'calificaciones/procesar_promedios.php', '', 2, 4, 37, 1, ''),
(102, 3, 'Anual', 'calificaciones/promedios_anuales.php', '', 2, 5, 37, 1, ''),
(103, 3, 'De Supletorios', 'calificaciones/reporte_supletorios.php', '', 2, 6, 37, 1, ''),
(104, 3, 'De Remediales', 'calificaciones/reporte_remediales.php', '', 2, 7, 37, 1, ''),
(105, 3, 'De Exámenes de Gracia', 'calificaciones/reporte_de_gracia.php', '', 2, 8, 37, 1, ''),
(107, 3, 'Quimestrales', 'php_excel/quimestrales.php', '', 2, 4, 39, 1, ''),
(108, 3, 'Anuales', 'php_excel/anuales.php', '', 2, 5, 39, 1, ''),
(109, 3, 'Cuadro Final', 'php_excel/cuadro_final.php', '', 2, 6, 39, 1, ''),
(110, 7, 'Parciales', 'tutores/parciales2.php', '', 2, 1, 52, 1, ''),
(111, 7, 'Quimestrales', 'tutores/quimestrales2.php', '', 2, 2, 52, 1, ''),
(112, 7, 'Anuales', 'tutores/anuales2.php', '', 2, 3, 52, 1, ''),
(113, 7, 'Supletorios', 'tutores/supletorios.php', '', 2, 4, 52, 1, ''),
(114, 7, 'Remediales', 'tutores/remediales.php', '', 2, 5, 52, 1, ''),
(116, 7, 'De Parciales', 'tutores/comp_parciales2.php', '', 2, 1, 58, 1, ''),
(117, 7, 'De Quimestrales', 'tutores/comportamiento2.php', '', 2, 2, 58, 1, ''),
(119, 2, 'Proyectos', 'listas_estudiantes/proyectos.php', '', 2, 3, 48, 1, ''),
(120, 1, 'Periodos', 'aportes_evaluacion/cerrar_periodos.php', 'admin/cerrar_periodos', 2, 1, 43, 1, 'fa fa-circle-o'),
(123, 3, 'Paralelos', 'matriculacion/index.php', 'secretaria/matriculacion_paralelos', 2, 2, 21, 1, 'fa fa-university'),
(124, 3, 'Proyectos Escolares', 'matriculacion/clubes.php', 'secretaria/proyectos_escolares', 2, 3, 21, 1, ''),
(127, 6, 'Horarios', '#', '', 1, 4, 0, 1, ''),
(128, 6, 'Definir Días de la Semana', 'dias_semana/index.php', '', 2, 1, 127, 1, ''),
(129, 6, 'Definir Horas Clase', 'hora_clase/index.php', '', 2, 2, 127, 1, ''),
(130, 6, 'Definir Horario Semanal', 'horarios/view_horario_semanal.php', '', 2, 4, 127, 1, ''),
(132, 6, 'Reportes', '#', '', 1, 7, 0, 1, ''),
(134, 6, 'Quimestral', 'calificaciones/reporte_quimestral_autoridad.php', '', 2, 2, 132, 1, ''),
(135, 6, 'Anual', 'calificaciones/promedios_anuales_autoridad.php', '', 2, 3, 132, 1, ''),
(138, 2, 'Asistencia', '#', '', 1, 4, 0, 1, ''),
(140, 3, 'Cuadro Remediales', 'php_excel/cuadro_remediales.php', '', 2, 7, 39, 1, ''),
(141, 3, 'Cuadro De Gracia', 'php_excel/cuadro_de_gracia.php', '', 2, 8, 39, 1, ''),
(143, 7, 'De Gracia', 'tutores/de_gracia.php', '', 2, 7, 52, 1, ''),
(144, 5, 'Parciales', 'inspeccion/comportamiento_parciales.php', '', 2, 1, 26, 1, ''),
(146, 1, 'Administración', '#', '#', 1, 1, 0, 1, 'fa fa-gear'),
(149, 1, 'Configuración', '#', '#', 0, 3, 0, 1, 'fa fa-share-alt'),
(159, 1, 'Definiciones', '#', '#', 0, 2, 0, 1, 'fa fa-cogs'),
(160, 1, 'Niveles de Educación', 'tipo_educacion/index2.php', 'definiciones/tipos_educacion', 2, 2, 159, 1, 'fa fa-university'),
(161, 1, 'Especialidades', 'especialidades/index2.php', 'definiciones/especialidades', 2, 3, 159, 1, 'fa fa-university'),
(162, 1, 'Cursos', 'cursos/index2.php', 'definiciones/cursos', 2, 4, 159, 1, 'fa fa-university'),
(163, 1, 'Paralelos', 'paralelos/index2.php', 'definiciones/paralelos', 2, 5, 159, 1, 'fa fa-university'),
(164, 1, 'Areas', 'areas/index.php', 'definiciones/areas', 2, 6, 159, 1, 'fa fa-university'),
(165, 1, 'Asignaturas', 'asignaturas/index2.php', 'definiciones/asignaturas', 2, 7, 159, 1, 'fa fa-university'),
(166, 1, 'Asociar', '#', '#', 0, 4, 0, 1, 'fa fa-share-alt'),
(167, 1, 'Asignaturas Cursos', 'asignaturas_cursos/index2.php', 'asociaciones/asignaturas_cursos', 2, 1, 166, 1, 'fa fa-circle-o'),
(171, 1, 'Paralelos Tutores', 'paralelos_tutores/index2.php', 'asociaciones/paralelos_tutores', 2, 3, 166, 1, 'fa fa-circle-o'),
(176, 1, 'Por Hacer', 'por_hacer/index.php', '#', 2, 1, 146, 1, 'fa fa-list-alt'),
(179, 13, 'Parciales', 'calificaciones/reporte_parciales.php', 'parciales', 2, 1, 189, 1, ''),
(180, 13, 'Quimestrales', 'calificaciones/procesar_promedios.php', 'quimestrales', 2, 2, 189, 1, ''),
(181, 13, 'Anuales', 'calificaciones/promedios_anuales.php', 'anuales', 2, 3, 189, 1, ''),
(189, 13, 'Reportes', '#', '', 1, 1, 0, 1, ''),
(195, 6, 'Definir Inasistencias', 'inasistencias/index.php', '', 2, 5, 127, 1, ''),
(197, 2, 'Ingresar Asistencia', 'horarios/view_asistencia.php', '', 2, 1, 138, 1, ''),
(198, 2, 'Ver Horario', 'horarios/ver_horario_docente.php', '', 2, 2, 138, 1, ''),
(200, 1, 'Períodos Lectivos', 'periodos_lectivos/index2.php', '', 2, 3, 146, 1, ''),
(201, 6, 'Malla Curricular', 'malla_curricular/index.php', '', 2, 4, 31, 1, ''),
(202, 6, 'Distributivo', 'distributivo/index.php', '', 2, 5, 31, 1, ''),
(203, 6, 'Asociar Dia-Hora', 'asociar_dia_hora/index.php', '', 2, 3, 127, 1, ''),
(204, 5, 'Horarios', '#', '', 1, 4, 0, 1, ''),
(205, 5, 'Leccionario', 'horarios/leccionario.php', '', 2, 1, 204, 1, ''),
(206, 6, 'Estadísticas', '#', '', 1, 9, 0, 1, ''),
(207, 6, 'Aprobados por Paralelo', 'estadisticas/aprobados_paralelo.php', '', 2, 1, 206, 1, ''),
(208, 3, 'Padrón Electoral', 'padron_electoral/index.php', '', 2, 3, 39, 1, ''),
(210, 7, 'Consultas', '#', '', 1, 3, 0, 1, ''),
(211, 7, 'Lista de Docentes', 'tutores/lista_docentes.php', '', 2, 1, 210, 1, ''),
(212, 7, 'Horario de Clases', 'tutores/horario_clases.php', '', 2, 2, 210, 1, ''),
(213, 6, 'Consultas', '#', '', 1, 8, 0, 1, ''),
(215, 6, 'Horarios de clase', 'autoridad/horario_clases.php', '', 2, 2, 213, 1, ''),
(216, 5, 'Docentes', 'inspeccion/horario_docentes.php', '', 2, 2, 204, 1, ''),
(217, 5, 'Definiciones', '#', '', 1, 1, 0, 1, ''),
(218, 5, 'Valor del mes', 'valor_del_mes/index.php', '', 2, 1, 217, 1, ''),
(219, 5, 'Consultas', '#', '', 1, 5, 0, 1, ''),
(220, 5, 'Lista de docentes', 'autoridad/lista_docentes.php', '', 2, 1, 219, 1, ''),
(221, 5, 'Horarios de clase', 'autoridad/horario_clases.php', '', 2, 2, 219, 1, ''),
(222, 5, 'Feriados', 'feriados/index.php', '', 2, 2, 217, 1, ''),
(225, 5, 'Asistencia', '#', '', 1, 2, 0, 1, ''),
(228, 7, 'Asistencia', '#', '', 1, 2, 0, 1, ''),
(229, 7, 'Justificar Faltas', 'tutores/view_justificar.php', '', 2, 1, 228, 1, ''),
(230, 3, 'Nómina de Matriculados', 'php_excel/nomina_matriculados.php', '', 2, 1, 37, 1, ''),
(231, 1, 'Institución', 'institucion/index.php', '', 2, 1, 149, 1, ''),
(232, 1, 'Modalidades', 'modalidades/view_index.php', '', 2, 2, 146, 1, ''),
(233, 1, 'Curso Superior', 'asociar_curso_superior/index.php', '', 2, 4, 166, 1, ''),
(235, 1, 'Educación Inicial', 'educacion_inicial/index.php', '', 2, 1, 159, 1, ''),
(236, 2, 'Tareas', 'tareas/index.php', '', 2, 1, 13, 0, ''),
(239, 2, 'Parciales', 'calificaciones/index.php', '', 2, 2, 13, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu_perfil`
--

CREATE TABLE `sw_menu_perfil` (
  `id_perfil` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_menu_perfil`
--

INSERT INTO `sw_menu_perfil` (`id_perfil`, `id_menu`) VALUES
(1, 2),
(1, 12),
(1, 18),
(1, 43),
(1, 120),
(1, 146),
(1, 149),
(1, 159),
(1, 160),
(1, 161),
(1, 162),
(1, 163),
(1, 164),
(1, 165),
(1, 166),
(1, 167),
(1, 171),
(1, 176),
(1, 200),
(1, 231),
(1, 232),
(1, 233),
(1, 235),
(2, 13),
(2, 38),
(2, 45),
(2, 48),
(2, 56),
(2, 67),
(2, 68),
(2, 69),
(2, 71),
(2, 73),
(2, 74),
(2, 75),
(2, 76),
(2, 77),
(2, 119),
(2, 138),
(2, 197),
(2, 198),
(2, 236),
(3, 21),
(3, 28),
(3, 32),
(3, 37),
(3, 39),
(3, 40),
(3, 97),
(3, 98),
(3, 99),
(3, 100),
(3, 101),
(3, 102),
(3, 103),
(3, 104),
(3, 105),
(3, 107),
(3, 108),
(3, 109),
(3, 123),
(3, 124),
(3, 140),
(3, 141),
(3, 208),
(3, 230),
(5, 26),
(5, 78),
(5, 79),
(5, 144),
(5, 204),
(5, 205),
(5, 216),
(5, 217),
(5, 218),
(5, 219),
(5, 220),
(5, 221),
(5, 222),
(5, 225),
(6, 31),
(6, 127),
(6, 128),
(6, 129),
(6, 130),
(6, 132),
(6, 134),
(6, 135),
(6, 195),
(6, 201),
(6, 202),
(6, 203),
(6, 206),
(6, 207),
(6, 213),
(6, 215),
(7, 52),
(7, 58),
(7, 110),
(7, 111),
(7, 112),
(7, 113),
(7, 114),
(7, 116),
(7, 117),
(7, 143),
(7, 210),
(7, 211),
(7, 212),
(7, 228),
(7, 229),
(13, 179),
(13, 180),
(13, 181),
(13, 189);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_modalidad`
--

CREATE TABLE `sw_modalidad` (
  `id_modalidad` int(11) NOT NULL,
  `mo_nombre` varchar(32) NOT NULL,
  `mo_activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `pa_nombre` varchar(5) NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_asignatura`
--

CREATE TABLE `sw_paralelo_asignatura` (
  `id_paralelo_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL DEFAULT 2,
  `pe_acceso_login` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_perfil`
--

INSERT INTO `sw_perfil` (`id_perfil`, `pe_nombre`, `pe_nivel_acceso`, `pe_acceso_login`) VALUES
(1, 'Administrador', 3, 1),
(2, 'Docente', 2, 1),
(3, 'Secretaría', 1, 1),
(5, 'Inspección', 1, 1),
(6, 'Autoridad', 3, 1),
(7, 'Tutor', 2, 1),
(13, 'DECE', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_estado`
--

CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) NOT NULL,
  `pe_descripcion` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_periodo_estado`
--

INSERT INTO `sw_periodo_estado` (`id_periodo_estado`, `pe_descripcion`) VALUES
(1, 'ACTUAL'),
(2, 'TERMINADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_evaluacion`
--

CREATE TABLE `sw_periodo_evaluacion` (
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_tipo_periodo` int(11) NOT NULL,
  `pe_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_abreviatura` varchar(6) NOT NULL,
  `pe_shortname` varchar(15) NOT NULL,
  `pe_principal` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_lectivo`
--

CREATE TABLE `sw_periodo_lectivo` (
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_periodo_estado` int(11) NOT NULL,
  `id_modalidad` int(11) NOT NULL,
  `id_institucion` int(11) NOT NULL,
  `pe_anio_inicio` int(11) NOT NULL,
  `pe_anio_fin` int(11) NOT NULL,
  `pe_estado` char(1) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_fecha_inicio` date NOT NULL,
  `pe_fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_permiso`
--

CREATE TABLE `sw_permiso` (
  `id_permiso` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `read` int(11) NOT NULL,
  `insert` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `delete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_plan_rubrica`
--

CREATE TABLE `sw_plan_rubrica` (
  `id_plan_rubrica` int(11) NOT NULL,
  `pr_tema` varchar(50) NOT NULL,
  `pr_descripcion` varchar(250) NOT NULL,
  `pr_fecha_elab` datetime NOT NULL,
  `pr_fecha_eval` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_representante`
--

CREATE TABLE `sw_representante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `re_apellidos` varchar(32) DEFAULT NULL,
  `re_nombres` varchar(32) DEFAULT NULL,
  `re_nombre_completo` varchar(64) DEFAULT NULL,
  `re_cedula` varchar(10) DEFAULT NULL,
  `re_genero` varchar(1) DEFAULT NULL,
  `re_email` varchar(64) DEFAULT NULL,
  `re_sector` varchar(36) DEFAULT NULL,
  `re_direccion` varchar(64) DEFAULT NULL,
  `re_telefono` varchar(16) DEFAULT NULL,
  `re_observacion` varchar(256) DEFAULT NULL,
  `re_parentesco` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='					';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_respuesta`
--

CREATE TABLE `sw_respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `re_texto` text NOT NULL,
  `re_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `re_autor` int(11) NOT NULL,
  `re_perfil` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_club`
--

CREATE TABLE `sw_rubrica_club` (
  `id_rubrica_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `rc_calificacion` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_cualitativa`
--

CREATE TABLE `sw_rubrica_cualitativa` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `rc_calificacion` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_docente`
--

CREATE TABLE `sw_rubrica_docente` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rd_nombre` varchar(64) NOT NULL,
  `rd_descripcion` varchar(256) NOT NULL,
  `rd_fecha_envio` date NOT NULL,
  `rd_fecha_revision` date NOT NULL,
  `rd_observacion` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_estudiante`
--

CREATE TABLE `sw_rubrica_estudiante` (
  `id_rubrica_estudiante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_rubrica_personalizada` int(11) NOT NULL,
  `re_calificacion` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion`
--

CREATE TABLE `sw_rubrica_evaluacion` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL DEFAULT 1,
  `ru_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_abreviatura` varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_personalizada`
--

CREATE TABLE `sw_rubrica_personalizada` (
  `id_rubrica_personalizada` int(11) NOT NULL,
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rp_tema` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `rp_fec_envio` date NOT NULL,
  `rp_fec_evaluacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tarea`
--

CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tema`
--

CREATE TABLE `sw_tema` (
  `id_tema` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `te_titulo` varchar(50) NOT NULL,
  `te_descripcion` text NOT NULL,
  `te_fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) NOT NULL,
  `ta_descripcion` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_tipo_asignatura`
--

INSERT INTO `sw_tipo_asignatura` (`id_tipo_asignatura`, `ta_descripcion`) VALUES
(1, 'CUANTITATIVA'),
(2, 'CUALITATIVA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_educacion`
--

CREATE TABLE `sw_tipo_educacion` (
  `id_tipo_educacion` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `te_nombre` varchar(48) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `te_bachillerato` tinyint(11) NOT NULL,
  `te_orden` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_tipo_educacion`
--

INSERT INTO `sw_tipo_educacion` (`id_tipo_educacion`, `id_periodo_lectivo`, `te_nombre`, `te_bachillerato`, `te_orden`) VALUES
(1, 1, 'EDUCACION BASICA SUPERIOR', 0, 1),
(2, 1, 'BACHILLERATO TECNICO', 1, 2),
(3, 2, 'EDUCACION GENERAL BASICA', 0, 1),
(4, 2, 'BACHILLERATO TECNICO', 1, 2),
(5, 2, 'BACHILLERATO EN CIENCIAS', 1, 3),
(6, 3, 'EDUCACION GENERAL BASICA', 0, 1),
(7, 3, 'BACHILLERATO TECNICO', 0, 2),
(8, 3, 'BACHILLERATO EN CIENCIAS', 0, 3),
(9, 4, 'EDUCACION GENERAL BASICA SUPERIOR', 0, 1),
(10, 4, 'BACHILLERATO GENERAL UNIFICADO', 0, 2),
(11, 4, 'BACHILLERATO TECNICO', 0, 3),
(12, 4, 'BACHILLERATO VIRTUAL', 0, 0),
(13, 5, 'Educación General Básica Superior', 0, 1),
(14, 5, 'Bachillerato General Unificado', 1, 2),
(15, 5, 'Bachillerato Técnico', 1, 3),
(17, 6, 'Educación General Básica Superior', 0, 1),
(18, 6, 'Bachillerato Técnico', 1, 3),
(19, 6, 'Bachillerato General Unificado', 1, 2),
(20, 7, 'EDUCACION GENERAL BASICA SUPERIOR', 0, 1),
(21, 7, 'BACHILLERATO GENERAL UNIFICADO', 0, 2),
(22, 7, 'BACHILLERATO TECNICO', 0, 3),
(23, 8, 'EDUCACION GENERAL BASICA SUPERIOR', 0, 1),
(24, 8, 'BACHILLERATO GENERAL UNIFICADO', 0, 2),
(25, 8, 'BACHILLERATO TECNICO', 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) NOT NULL,
  `tp_descripcion` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sw_tipo_periodo`
--

INSERT INTO `sw_tipo_periodo` (`id_tipo_periodo`, `tp_descripcion`) VALUES
(1, 'QUIMESTRE'),
(2, 'SUPLETORIO'),
(3, 'REMEDIAL'),
(4, 'DE GRACIA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `us_titulo` varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_apellidos` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_nombres` varchar(32) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_shortname` varchar(45) NOT NULL,
  `us_fullname` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_foto` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_activo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_valor_mes`
--

CREATE TABLE `sw_valor_mes` (
  `id_valor_mes` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `vm_mes` int(11) NOT NULL,
  `vm_valor` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sw_valor_mes`
--

INSERT INTO `sw_valor_mes` (`id_valor_mes`, `id_periodo_lectivo`, `vm_mes`, `vm_valor`) VALUES
(1, 6, 9, 'JUSTICIA'),
(2, 6, 10, 'LIBERTAD'),
(3, 6, 11, 'RESPETO'),
(4, 6, 12, 'RESPONSABILIDAD'),
(5, 6, 1, 'LEALTAD'),
(6, 6, 2, 'HONESTIDAD'),
(7, 6, 3, 'CONFIANZA'),
(8, 6, 4, 'COMPAÑERISMO'),
(9, 6, 5, 'SOLIDARIDAD'),
(10, 6, 6, 'GRATITUD'),
(11, 6, 7, 'AMISTAD'),
(15, 6, 8, 'PERSEVERANCIA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_curso_cierre`
--
ALTER TABLE `sw_aporte_curso_cierre`
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD PRIMARY KEY (`id_aporte_evaluacion`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD PRIMARY KEY (`id_aporte_paralelo_cierre`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `id_area` (`id_area`);

--
-- Indices de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD PRIMARY KEY (`id_asignatura_curso`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD PRIMARY KEY (`id_asistencia_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_asignatura`,`id_paralelo`,`id_inasistencia`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_inasistencia` (`id_inasistencia`),
  ADD KEY `sw_asistencia_estudiante_ibfk_1` (`id_hora_clase`);

--
-- Indices de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD PRIMARY KEY (`id_asistencia_tutor`),
  ADD KEY `fk_asistencia_tutor_estudiante` (`id_estudiante`),
  ADD KEY `fk_asistencia_tutor_paralelo` (`id_paralelo`),
  ADD KEY `id_asistencia_tutor_inasistencia` (`id_inasistencia`);

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `fk_curso_superior_periodo_lectivo_idx` (`id_periodo_lectivo`),
  ADD KEY `fk_curso_inferior_curso_idx` (`id_curso_inferior`),
  ADD KEY `fk_curso_superior_curso_idx` (`id_curso_superior`);

--
-- Indices de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  ADD KEY `id_paralelo` (`id_paralelo`,`id_estudiante`,`id_aporte_evaluacion`,`id_asignatura`);

--
-- Indices de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  ADD PRIMARY KEY (`id_club_docente`);

--
-- Indices de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Indices de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD PRIMARY KEY (`id_comportamiento_inspector`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_indice_evaluacion` (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  ADD PRIMARY KEY (`id_comportamiento_tutor`);

--
-- Indices de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD PRIMARY KEY (`id_distributivo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_malla_curricular` (`id_malla_curricular`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`);

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  ADD PRIMARY KEY (`id_escala_proyectos`);

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`);

--
-- Indices de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  ADD PRIMARY KEY (`id_estudiante_club`);

--
-- Indices de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD PRIMARY KEY (`id_estudiante_periodo_lectivo`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_periodo_lectivo`,`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD PRIMARY KEY (`id_feriado`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  ADD PRIMARY KEY (`id_foro`);

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_hora_clase` (`id_hora_clase`),
  ADD KEY `sw_horario_ibfk_1` (`id_asignatura`),
  ADD KEY `sw_horario_ibfk_2` (`id_dia_semana`),
  ADD KEY `sw_horario_ibfk_4` (`id_paralelo`);

--
-- Indices de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  ADD PRIMARY KEY (`id_horario_examen`);

--
-- Indices de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD PRIMARY KEY (`id_hora_clase`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD PRIMARY KEY (`id_hora_dia`),
  ADD KEY `sw_hora_dia_ibfk_1` (`id_dia_semana`),
  ADD KEY `sw_hora_dia_ibfk_2` (`id_hora_clase`);

--
-- Indices de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  ADD PRIMARY KEY (`id_inasistencia`);

--
-- Indices de la tabla `sw_indice_evaluacion`
--
ALTER TABLE `sw_indice_evaluacion`
  ADD PRIMARY KEY (`id_indice_evaluacion`);

--
-- Indices de la tabla `sw_indice_evaluacion_def`
--
ALTER TABLE `sw_indice_evaluacion_def`
  ADD PRIMARY KEY (`id_indice_evaluacion`);

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD PRIMARY KEY (`id_malla_curricular`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD KEY `fk_menu_perfil_menu` (`id_menu`),
  ADD KEY `fk_menu_perfil_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_jornada` (`id_jornada`);

--
-- Indices de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  ADD PRIMARY KEY (`id_paralelo_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`);

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`);

--
-- Indices de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  ADD PRIMARY KEY (`id_periodo_estado`);

--
-- Indices de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`),
  ADD UNIQUE KEY `pe_anio_inicio` (`pe_anio_inicio`),
  ADD UNIQUE KEY `pe_anio_fin` (`pe_anio_fin`),
  ADD KEY `id_institucion` (`id_institucion`);

--
-- Indices de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `fk_menus_idx` (`id_menu`),
  ADD KEY `fk_perfiles_idx` (`id_perfil`);

--
-- Indices de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  ADD PRIMARY KEY (`id_plan_rubrica`);

--
-- Indices de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `fk_representante_estudiante_idx` (`id_estudiante`);

--
-- Indices de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  ADD PRIMARY KEY (`id_rubrica_club`);

--
-- Indices de la tabla `sw_rubrica_docente`
--
ALTER TABLE `sw_rubrica_docente`
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_docente` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD PRIMARY KEY (`id_rubrica_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD PRIMARY KEY (`id_rubrica_evaluacion`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  ADD PRIMARY KEY (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  ADD PRIMARY KEY (`id_tema`),
  ADD KEY `id_foro` (`id_foro`);

--
-- Indices de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  ADD PRIMARY KEY (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  ADD PRIMARY KEY (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD PRIMARY KEY (`id_tipo_educacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`);

--
-- Indices de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD PRIMARY KEY (`id_valor_mes`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  MODIFY `id_aporte_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  MODIFY `id_asistencia_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  MODIFY `id_asistencia_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  MODIFY `id_club_docente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  MODIFY `id_comportamiento_inspector` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  MODIFY `id_comportamiento_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  MODIFY `id_escala_proyectos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  MODIFY `id_estudiante_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  MODIFY `id_feriado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  MODIFY `id_foro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  MODIFY `id_horario_examen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  MODIFY `id_inasistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_indice_evaluacion`
--
ALTER TABLE `sw_indice_evaluacion`
  MODIFY `id_indice_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_indice_evaluacion_def`
--
ALTER TABLE `sw_indice_evaluacion_def`
  MODIFY `id_indice_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  MODIFY `id_paralelo_asignatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  MODIFY `id_periodo_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  MODIFY `id_periodo_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  MODIFY `id_plan_rubrica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  MODIFY `id_rubrica_club` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  MODIFY `id_rubrica_personalizada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  MODIFY `id_tipo_aporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  MODIFY `id_valor_mes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD CONSTRAINT `sw_aporte_evaluacion_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Filtros para la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD CONSTRAINT `sw_asignatura_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `sw_area` (`id_area`);

--
-- Filtros para la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_1` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`);

--
-- Filtros para la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD CONSTRAINT `fk_asistencia_tutor_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_asistencia_tutor_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `id_asistencia_tutor_inasistencia` FOREIGN KEY (`id_inasistencia`) REFERENCES `sw_inasistencia` (`id_inasistencia`);

--
-- Filtros para la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD CONSTRAINT `sw_curso_ibfk_1` FOREIGN KEY (`id_especialidad`) REFERENCES `sw_especialidad` (`id_especialidad`);

--
-- Filtros para la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD CONSTRAINT `sw_distributivo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_distributivo_ibfk_2` FOREIGN KEY (`id_malla_curricular`) REFERENCES `sw_malla_curricular` (`id_malla_curricular`),
  ADD CONSTRAINT `sw_distributivo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_distributivo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_distributivo_ibfk_5` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD CONSTRAINT `sw_especialidad_ibfk_1` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_2` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD CONSTRAINT `sw_feriado_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD CONSTRAINT `sw_horario_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_horario_ibfk_2` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_horario_ibfk_3` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_horario_ibfk_4` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD CONSTRAINT `sw_hora_clase_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD CONSTRAINT `sw_hora_dia_ibfk_1` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_2` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD CONSTRAINT `sw_malla_curricular_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_malla_curricular_ibfk_3` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD CONSTRAINT `fk_menu_perfil_menu` FOREIGN KEY (`id_menu`) REFERENCES `sw_menu` (`id_menu`),
  ADD CONSTRAINT `fk_menu_perfil_perfil` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`);

--
-- Filtros para la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD CONSTRAINT `sw_paralelo_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_ibfk_2` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`);

--
-- Filtros para la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD CONSTRAINT `sw_representante_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_2` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Filtros para la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD CONSTRAINT `sw_tipo_educacion_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD CONSTRAINT `sw_valor_mes_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
