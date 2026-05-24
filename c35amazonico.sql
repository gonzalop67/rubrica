-- phpMyAdmin SQL Dump
-- version 4.6.6deb4+deb9u2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 28-09-2021 a las 09:49:19
-- Versión del servidor: 10.1.48-MariaDB-0+deb9u2
-- Versión de PHP: 7.0.33-0+deb9u11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `c35amazonico`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `actualizar_id_curso_malla` ()  NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_actualizar_nro_matricula` (IN `IdPeriodoLectivo` INT)  NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_actualizar_periodo_lectivo` (IN `IdPeriodoLectivo` INT, IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` DATE, IN `FechaFin` DATE, IN `IdModalidad` INT)  NO SQL
UPDATE sw_periodo_lectivo
   SET pe_anio_inicio = AnioInicial,
       pe_anio_fin = AnioFinal,
       pe_fecha_inicio = FechaInicial,
       pe_fecha_fin = FechaFin,
       id_modalidad = IdModalidad
 WHERE id_periodo_lectivo = IdPeriodoLectivo$$

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_actualizar_usuario` (IN `IdUsuario` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsNombreCompleto` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64), IN `UsActivo` INT, IN `UsGenero` VARCHAR(1), IN `UsFoto` VARCHAR(64))  NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_asociar_cierre_aporte_cursos` (IN `IdPeriodoLectivo` INT, IN `IdAporteEvaluacion` INT, IN `FechaApertura` DATE, IN `FechaCierre` DATE)  NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_calcular_prom_anual` (IN `IdPeriodoLectivo` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_calcular_prom_aporte` (IN `IdAporteEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_calcular_prom_quimestre` (IN `IdPeriodoEvaluacion` INT, IN `IdParalelo` INT)  BEGIN
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_cerrar_periodo_lectivo` (IN `IdPeriodoLectivo` INT)  NO SQL
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

		UPDATE sw_periodo_lectivo
	   SET pe_estado = 'T',
           id_periodo_estado = 2
	 WHERE id_periodo_lectivo = IdPeriodoLectivo;

	
	OPEN cAportesEvaluacion;

	REPEAT
		FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
		UPDATE sw_aporte_curso_cierre
		   SET ap_estado = 'C'
		 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
	UNTIL done END REPEAT;

	CLOSE cAportesEvaluacion;
	
END$$

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_crear_cierres_periodo_lectivo` (IN `IdPeriodoLectivo` INT)  BEGIN
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_insertar_institucion` (IN `In_nombre` VARCHAR(64), IN `In_direccion` VARCHAR(45), IN `In_telefono1` VARCHAR(64), IN `In_nom_rector` VARCHAR(45), IN `In_nom_secretario` VARCHAR(45), IN `In_copiar_y_pegar` INT)  NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_insertar_periodo_lectivo` (IN `AnioInicial` INT, IN `AnioFinal` INT, IN `FechaInicial` DATE, IN `FechaFin` DATE, IN `IdModalidad` INT)  NO SQL
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

		
	SET @IdPeriodoLectivoAnterior = (SELECT id_periodo_lectivo
                                      FROM sw_periodo_lectivo
                                     WHERE pe_anio_inicio = AnioInicial - 1);

	
	IF @IdPeriodoLectivoAnterior IS NOT NULL THEN
				UPDATE sw_periodo_lectivo
		   SET pe_estado = 'T',
               id_periodo_estado = 2
		 WHERE id_periodo_lectivo = @IdPeriodoLectivoAnterior;

				
		OPEN cAportesEvaluacion;

		REPEAT
			FETCH cAportesEvaluacion INTO IdAporteEvaluacion;
			UPDATE sw_aporte_curso_cierre
			   SET ap_estado = 'C'
			 WHERE id_aporte_evaluacion = IdAporteEvaluacion;
		UNTIL done END REPEAT;

		CLOSE cAportesEvaluacion;
	
	END IF;

		INSERT INTO sw_periodo_lectivo (pe_anio_inicio, pe_anio_fin, pe_estado, pe_fecha_inicio, pe_fecha_fin, id_modalidad, id_periodo_estado, id_institucion)
	VALUES (AnioInicial, AnioFinal, 'A', FechaInicial, FechaFin, IdModalidad, 1, 1);

END$$

CREATE DEFINER=`c35amazonico`@`localhost` PROCEDURE `sp_insertar_usuario` (IN `IdPeriodoLectivo` INT, IN `IdPerfil` INT, IN `UsTitulo` VARCHAR(5), IN `UsApellidos` VARCHAR(32), IN `UsNombres` VARCHAR(32), IN `UsShortname` VARCHAR(64), IN `UsFullname` VARCHAR(64), IN `UsLogin` VARCHAR(24), IN `UsPassword` VARCHAR(64), IN `UsGenero` VARCHAR(1), IN `UsFoto` INT(64))  NO SQL
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
CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `aprueba_todas_asignaturas` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(1) NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `aprueba_todos_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; 	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN 			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
								SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN 				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET done = 1;
					SET aprueba = FALSE;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;

END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `aprueba_todos_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE aprueba BOOL DEFAULT TRUE; 	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;

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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN 			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
				SET done = 1;
				SET aprueba = FALSE;
			END IF;
		ELSE IF promedio < 5 THEN 				SET done = 1;
				SET aprueba = FALSE;
			 END IF;
		END IF;
	END LOOP Lazo;

	RETURN aprueba;
	
END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_comp_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_comp_asignatura` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_comp_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_comp_insp_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_comp_tutor` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_examen_supletorio` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT, `PePrincipal` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE IdRubricaEvaluacion INT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0; 
		SET IdRubricaEvaluacion = (SELECT id_rubrica_evaluacion 
				     FROM sw_rubrica_evaluacion r, 
					  sw_aporte_evaluacion a, 
					  sw_periodo_evaluacion p 
				    WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
				      AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
				      AND p.pe_principal = PePrincipal 
				      AND p.id_periodo_lectivo = IdPeriodoLectivo);

	SET examen_supletorio = (SELECT re_calificacion
				   FROM sw_rubrica_estudiante 
				  WHERE id_estudiante = IdEstudiante 
				    AND id_paralelo = IdParalelo 
				    AND id_asignatura = IdAsignatura 
				    AND id_rubrica_personalizada = IdRubricaEvaluacion);
	
	RETURN IFNULL(examen_supletorio, 0);
END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_anual` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_anual FLOAT; 	DECLARE promedio_quimestre FLOAT;
	DECLARE IdPeriodoEvaluacion INT;
	DECLARE Suma FLOAT DEFAULT 0;
	DECLARE Contador INT DEFAULT 0;
	
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_anual_proyectos` (`IdPeriodoLectivo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_aporte` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_aporte_club` (`IdAporteEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT READS SQL DATA
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_final` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE FUNCTION `calcular_promedio_general` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE promedio_general float DEFAULT 0; 	DECLARE suma FLOAT DEFAULT 0;
	DECLARE contador INT DEFAULT 0;
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
		SET suma = suma + (SELECT calcular_promedio_final(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura));
		SET contador = contador + 1;
	END LOOP Lazo;

	SET promedio_general = suma / contador;

	RETURN promedio_general;
END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_quimestre` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE promedio_quimestre FLOAT;     DECLARE promedio_aporte FLOAT;
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_promedio_quimestre_club` (`IdPeriodoEvaluacion` INT, `IdEstudiante` INT, `IdClub` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_prom_anual_estudiante` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_prom_aporte_estudiante` (`IdAporteEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_prom_final_cualitativa` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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
	 WHERE pe_principal = 1
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_prom_quim_cualitativa` (`IdPeriodoEval` INT, `IdEstudiante` INT, `IdParalelo` INT, `IdAsignatura` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calcular_prom_quim_estudiante` (`IdPeriodoEvaluacion` INT, `IdParalelo` INT, `IdEstudiante` INT) RETURNS FLOAT NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `calc_max_nro_matricula` () RETURNS VARCHAR(4) CHARSET ascii NO SQL
BEGIN
	DECLARE max_nro_matricula VARCHAR(4);

	SET max_nro_matricula = (SELECT LPAD(MAX(es_nro_matricula)+1,4,'0') FROM sw_estudiante);
	RETURN IFNULL(max_nro_matricula,'0001');

END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `contar_remediales` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `contar_remediales_no_aprobados` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE contador INT DEFAULT 0; 	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

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
		IF (promedio >= 5 AND promedio < 7) AND (7 - promedio > 0.01) THEN 			SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
			IF examen_supletorio < 7 THEN
								SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		ELSE 
			IF promedio > 0 AND promedio < 5 THEN 				SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
				IF examen_remedial < 7 THEN
					SET contador = contador + 1;
				END IF;
			END IF;
		END IF;
	END LOOP Lazo;

	RETURN contador;

END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `contar_supletorios` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `determinar_asignatura_de_gracia` (`IdPeriodoLectivo` INT, `IdEstudiante` INT, `IdParalelo` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE IdAsignatura INT;
	DECLARE vid_asignatura INT DEFAULT 0; 	DECLARE contador INT DEFAULT 0;
	DECLARE done INT DEFAULT 0;
	DECLARE promedio FLOAT DEFAULT 0;
	DECLARE examen_supletorio FLOAT DEFAULT 0;
	DECLARE examen_remedial FLOAT DEFAULT 0;

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
			IF promedio > 5 AND promedio < 7 THEN 				SET examen_supletorio = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,2));
				IF examen_supletorio < 7 THEN
										SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
					IF examen_remedial < 7 THEN
						SET vid_asignatura = IdAsignatura;
                        SET done = 1;
					END IF;
				END IF;
			ELSE 
				IF promedio > 0 AND promedio < 5 THEN 					SET examen_remedial = (SELECT calcular_examen_supletorio(IdPeriodoLectivo,IdEstudiante,IdParalelo,IdAsignatura,3));
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `es_promocionado` (`IdEstudiante` INT, `IdPeriodoLectivo` INT, `IdParalelo` INT) RETURNS TINYINT(4) NO SQL
BEGIN
	DECLARE aprueba BOOL DEFAULT TRUE; 		DECLARE promedio FLOAT DEFAULT 0;
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_curso_asignatura` (`IdCurso` INT) RETURNS INT(11) NO SQL
BEGIN
	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(ac_orden)
		  FROM sw_asignatura_curso
		 WHERE id_curso = IdCurso);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;
END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_curso_especialidad` (`IdEspecialidad` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(cu_orden)
		  FROM sw_curso
		 WHERE id_especialidad = IdEspecialidad);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_hora_clase_dia_semana` (`IdDiaSemana` INT) RETURNS INT(11) NO SQL
BEGIN

	DECLARE Secuencial INT;
	
	SET Secuencial = (
		SELECT MAX(hc_ordinal)
		  FROM sw_hora_clase
		 WHERE id_dia_semana = IdDiaSemana);
           
    SET Secuencial = IFNULL(Secuencial, 0);

	RETURN Secuencial + 1;

END$$

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_menu_nivel_perfil_padre` (`Nivel` INT, `IdPerfil` INT, `Padre` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_paralelo_periodo_lectivo` (`IdPeriodoLectivo` INT) RETURNS INT(11) NO SQL
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

CREATE DEFINER=`c35amazonico`@`localhost` FUNCTION `secuencial_tipo_educacion` (`IdPeriodoLectivo` INT) RETURNS INT(11) NO SQL
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
  `ap_estado` varchar(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_aporte_evaluacion`
--

CREATE TABLE `sw_aporte_evaluacion` (
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_tipo_aporte` int(11) NOT NULL,
  `ap_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ap_shortname` varchar(45) CHARACTER SET latin1 NOT NULL,
  `ap_abreviatura` varchar(8) CHARACTER SET latin1 NOT NULL,
  `ap_tipo` tinyint(4) NOT NULL,
  `ap_estado` varchar(1) CHARACTER SET latin1 NOT NULL,
  `ap_fecha_apertura` date NOT NULL,
  `ap_fecha_cierre` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_aporte_evaluacion`
--

INSERT INTO `sw_aporte_evaluacion` (`id_aporte_evaluacion`, `id_periodo_evaluacion`, `id_tipo_aporte`, `ap_nombre`, `ap_shortname`, `ap_abreviatura`, `ap_tipo`, `ap_estado`, `ap_fecha_apertura`, `ap_fecha_cierre`) VALUES
(1, 1, 1, 'PRIMER PARCIAL', '', 'P1Q1', 1, '', '0000-00-00', '0000-00-00'),
(2, 1, 1, 'SEGUNDO PARCIAL', '', 'P2Q1', 1, '', '0000-00-00', '0000-00-00'),
(3, 1, 2, 'EXAMEN QUIMESTRAL', '', 'EX.Q1', 2, '', '0000-00-00', '0000-00-00'),
(4, 2, 1, 'PRIMER PARCIAL', '', 'P1Q2', 1, '', '0000-00-00', '0000-00-00'),
(5, 2, 1, 'SEGUNDO PARCIAL', '', 'P2Q2', 1, '', '0000-00-00', '0000-00-00'),
(6, 2, 2, 'EXAMEN QUIMESTRAL', '', 'EX.Q2', 2, '', '0000-00-00', '0000-00-00'),
(7, 3, 3, 'SUPLETORIO', '', 'SUP.', 3, '', '0000-00-00', '0000-00-00'),
(8, 4, 3, 'REMEDIAL', '', 'REM.', 3, '', '0000-00-00', '0000-00-00'),
(9, 5, 3, 'EXAMEN DE GRACIA', '', 'GRA.', 3, '', '0000-00-00', '0000-00-00');

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
  `ap_estado` varchar(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_aporte_paralelo_cierre`
--

INSERT INTO `sw_aporte_paralelo_cierre` (`id_aporte_paralelo_cierre`, `id_aporte_evaluacion`, `id_paralelo`, `ap_fecha_apertura`, `ap_fecha_cierre`, `ap_estado`) VALUES
(1, 1, 1, '0000-00-00', '0000-00-00', 'C'),
(2, 1, 3, '0000-00-00', '0000-00-00', 'C'),
(3, 1, 4, '0000-00-00', '0000-00-00', 'C'),
(4, 1, 5, '0000-00-00', '0000-00-00', 'C'),
(5, 1, 6, '0000-00-00', '0000-00-00', 'C'),
(6, 1, 13, '0000-00-00', '0000-00-00', 'C'),
(7, 1, 14, '0000-00-00', '0000-00-00', 'C'),
(8, 1, 15, '0000-00-00', '0000-00-00', 'C'),
(9, 1, 7, '0000-00-00', '0000-00-00', 'C'),
(10, 1, 8, '0000-00-00', '0000-00-00', 'C'),
(11, 1, 9, '0000-00-00', '0000-00-00', 'C'),
(12, 1, 10, '0000-00-00', '0000-00-00', 'C'),
(13, 1, 11, '0000-00-00', '0000-00-00', 'C'),
(14, 1, 12, '0000-00-00', '0000-00-00', 'C'),
(15, 1, 16, '0000-00-00', '0000-00-00', 'C'),
(16, 1, 17, '0000-00-00', '0000-00-00', 'C'),
(17, 1, 18, '0000-00-00', '0000-00-00', 'C'),
(18, 1, 19, '0000-00-00', '0000-00-00', 'C'),
(19, 1, 20, '0000-00-00', '0000-00-00', 'C'),
(20, 1, 21, '0000-00-00', '0000-00-00', 'C'),
(21, 1, 22, '0000-00-00', '0000-00-00', 'C'),
(22, 1, 23, '0000-00-00', '0000-00-00', 'C'),
(23, 1, 24, '0000-00-00', '0000-00-00', 'C'),
(24, 1, 25, '0000-00-00', '0000-00-00', 'C'),
(25, 1, 26, '2021-09-27', '2021-10-15', 'A'),
(26, 1, 27, '2021-09-27', '2021-10-15', 'A'),
(27, 1, 37, '0000-00-00', '0000-00-00', 'C'),
(28, 1, 38, '0000-00-00', '0000-00-00', 'C'),
(29, 1, 39, '0000-00-00', '0000-00-00', 'C'),
(30, 1, 40, '0000-00-00', '0000-00-00', 'C'),
(31, 1, 41, '0000-00-00', '0000-00-00', 'C'),
(32, 1, 42, '0000-00-00', '0000-00-00', 'C'),
(33, 1, 43, '0000-00-00', '0000-00-00', 'C'),
(34, 1, 28, '0000-00-00', '0000-00-00', 'C'),
(35, 1, 29, '0000-00-00', '0000-00-00', 'C'),
(36, 1, 30, '0000-00-00', '0000-00-00', 'C'),
(37, 1, 31, '0000-00-00', '0000-00-00', 'C'),
(38, 1, 32, '0000-00-00', '0000-00-00', 'C'),
(39, 1, 33, '0000-00-00', '0000-00-00', 'C'),
(40, 1, 34, '0000-00-00', '0000-00-00', 'C'),
(41, 1, 35, '0000-00-00', '0000-00-00', 'C'),
(42, 1, 36, '0000-00-00', '0000-00-00', 'C'),
(43, 1, 53, '0000-00-00', '0000-00-00', 'C'),
(44, 1, 54, '0000-00-00', '0000-00-00', 'C'),
(45, 1, 55, '0000-00-00', '0000-00-00', 'C'),
(46, 1, 44, '0000-00-00', '0000-00-00', 'C'),
(47, 1, 45, '0000-00-00', '0000-00-00', 'C'),
(48, 1, 46, '0000-00-00', '0000-00-00', 'C'),
(49, 1, 47, '0000-00-00', '0000-00-00', 'C'),
(50, 1, 48, '0000-00-00', '0000-00-00', 'C'),
(51, 1, 49, '0000-00-00', '0000-00-00', 'C'),
(52, 1, 50, '0000-00-00', '0000-00-00', 'C'),
(53, 1, 51, '0000-00-00', '0000-00-00', 'C'),
(54, 1, 52, '0000-00-00', '0000-00-00', 'C'),
(55, 1, 56, '0000-00-00', '0000-00-00', 'C'),
(56, 1, 57, '0000-00-00', '0000-00-00', 'C'),
(57, 1, 58, '0000-00-00', '0000-00-00', 'C'),
(62, 1, 63, '2021-08-02', '2021-08-20', 'A'),
(64, 2, 1, '0000-00-00', '0000-00-00', 'C'),
(65, 2, 3, '0000-00-00', '0000-00-00', 'C'),
(66, 2, 4, '0000-00-00', '0000-00-00', 'C'),
(67, 2, 5, '0000-00-00', '0000-00-00', 'C'),
(68, 2, 6, '0000-00-00', '0000-00-00', 'C'),
(69, 2, 13, '0000-00-00', '0000-00-00', 'C'),
(70, 2, 14, '0000-00-00', '0000-00-00', 'C'),
(71, 2, 15, '0000-00-00', '0000-00-00', 'C'),
(72, 2, 7, '0000-00-00', '0000-00-00', 'C'),
(73, 2, 8, '0000-00-00', '0000-00-00', 'C'),
(74, 2, 9, '0000-00-00', '0000-00-00', 'C'),
(75, 2, 10, '0000-00-00', '0000-00-00', 'C'),
(76, 2, 11, '0000-00-00', '0000-00-00', 'C'),
(77, 2, 12, '0000-00-00', '0000-00-00', 'C'),
(78, 2, 16, '0000-00-00', '0000-00-00', 'C'),
(79, 2, 17, '0000-00-00', '0000-00-00', 'C'),
(80, 2, 18, '0000-00-00', '0000-00-00', 'C'),
(81, 2, 19, '0000-00-00', '0000-00-00', 'C'),
(82, 2, 20, '0000-00-00', '0000-00-00', 'C'),
(83, 2, 21, '0000-00-00', '0000-00-00', 'C'),
(84, 2, 22, '0000-00-00', '0000-00-00', 'C'),
(85, 2, 23, '0000-00-00', '0000-00-00', 'C'),
(86, 2, 24, '0000-00-00', '0000-00-00', 'C'),
(87, 2, 25, '0000-00-00', '0000-00-00', 'C'),
(88, 2, 26, '2021-09-27', '2021-10-15', 'A'),
(89, 2, 27, '2021-09-27', '2021-10-15', 'A'),
(90, 2, 37, '0000-00-00', '0000-00-00', 'C'),
(91, 2, 38, '0000-00-00', '0000-00-00', 'C'),
(92, 2, 39, '0000-00-00', '0000-00-00', 'C'),
(93, 2, 40, '0000-00-00', '0000-00-00', 'C'),
(94, 2, 41, '0000-00-00', '0000-00-00', 'C'),
(95, 2, 42, '0000-00-00', '0000-00-00', 'C'),
(96, 2, 43, '0000-00-00', '0000-00-00', 'C'),
(97, 2, 28, '0000-00-00', '0000-00-00', 'C'),
(98, 2, 29, '0000-00-00', '0000-00-00', 'C'),
(99, 2, 30, '0000-00-00', '0000-00-00', 'C'),
(100, 2, 31, '0000-00-00', '0000-00-00', 'C'),
(101, 2, 32, '0000-00-00', '0000-00-00', 'C'),
(102, 2, 33, '0000-00-00', '0000-00-00', 'C'),
(103, 2, 34, '0000-00-00', '0000-00-00', 'C'),
(104, 2, 35, '0000-00-00', '0000-00-00', 'C'),
(105, 2, 36, '0000-00-00', '0000-00-00', 'C'),
(106, 2, 53, '0000-00-00', '0000-00-00', 'C'),
(107, 2, 54, '0000-00-00', '0000-00-00', 'C'),
(108, 2, 55, '0000-00-00', '0000-00-00', 'C'),
(109, 2, 44, '0000-00-00', '0000-00-00', 'C'),
(110, 2, 45, '0000-00-00', '0000-00-00', 'C'),
(111, 2, 46, '0000-00-00', '0000-00-00', 'C'),
(112, 2, 47, '0000-00-00', '0000-00-00', 'C'),
(113, 2, 48, '0000-00-00', '0000-00-00', 'C'),
(114, 2, 49, '0000-00-00', '0000-00-00', 'C'),
(115, 2, 50, '0000-00-00', '0000-00-00', 'C'),
(116, 2, 51, '0000-00-00', '0000-00-00', 'C'),
(117, 2, 52, '0000-00-00', '0000-00-00', 'C'),
(118, 2, 56, '0000-00-00', '0000-00-00', 'C'),
(119, 2, 57, '0000-00-00', '0000-00-00', 'C'),
(120, 2, 58, '0000-00-00', '0000-00-00', 'C'),
(125, 2, 63, '0000-00-00', '0000-00-00', 'C'),
(127, 3, 1, '0000-00-00', '0000-00-00', 'C'),
(128, 3, 3, '0000-00-00', '0000-00-00', 'C'),
(129, 3, 4, '0000-00-00', '0000-00-00', 'C'),
(130, 3, 5, '0000-00-00', '0000-00-00', 'C'),
(131, 3, 6, '0000-00-00', '0000-00-00', 'C'),
(132, 3, 13, '0000-00-00', '0000-00-00', 'C'),
(133, 3, 14, '0000-00-00', '0000-00-00', 'C'),
(134, 3, 15, '0000-00-00', '0000-00-00', 'C'),
(135, 3, 7, '0000-00-00', '0000-00-00', 'C'),
(136, 3, 8, '0000-00-00', '0000-00-00', 'C'),
(137, 3, 9, '0000-00-00', '0000-00-00', 'C'),
(138, 3, 10, '0000-00-00', '0000-00-00', 'C'),
(139, 3, 11, '0000-00-00', '0000-00-00', 'C'),
(140, 3, 12, '0000-00-00', '0000-00-00', 'C'),
(141, 3, 16, '0000-00-00', '0000-00-00', 'C'),
(142, 3, 17, '0000-00-00', '0000-00-00', 'C'),
(143, 3, 18, '0000-00-00', '0000-00-00', 'C'),
(144, 3, 19, '0000-00-00', '0000-00-00', 'C'),
(145, 3, 20, '0000-00-00', '0000-00-00', 'C'),
(146, 3, 21, '0000-00-00', '0000-00-00', 'C'),
(147, 3, 22, '0000-00-00', '0000-00-00', 'C'),
(148, 3, 23, '0000-00-00', '0000-00-00', 'C'),
(149, 3, 24, '0000-00-00', '0000-00-00', 'C'),
(150, 3, 25, '0000-00-00', '0000-00-00', 'C'),
(151, 3, 26, '2021-09-27', '2021-10-15', 'A'),
(152, 3, 27, '2021-09-27', '2021-10-15', 'A'),
(153, 3, 37, '0000-00-00', '0000-00-00', 'C'),
(154, 3, 38, '0000-00-00', '0000-00-00', 'C'),
(155, 3, 39, '0000-00-00', '0000-00-00', 'C'),
(156, 3, 40, '0000-00-00', '0000-00-00', 'C'),
(157, 3, 41, '0000-00-00', '0000-00-00', 'C'),
(158, 3, 42, '0000-00-00', '0000-00-00', 'C'),
(159, 3, 43, '0000-00-00', '0000-00-00', 'C'),
(160, 3, 28, '0000-00-00', '0000-00-00', 'C'),
(161, 3, 29, '0000-00-00', '0000-00-00', 'C'),
(162, 3, 30, '0000-00-00', '0000-00-00', 'C'),
(163, 3, 31, '0000-00-00', '0000-00-00', 'C'),
(164, 3, 32, '0000-00-00', '0000-00-00', 'C'),
(165, 3, 33, '0000-00-00', '0000-00-00', 'C'),
(166, 3, 34, '0000-00-00', '0000-00-00', 'C'),
(167, 3, 35, '0000-00-00', '0000-00-00', 'C'),
(168, 3, 36, '0000-00-00', '0000-00-00', 'C'),
(169, 3, 53, '0000-00-00', '0000-00-00', 'C'),
(170, 3, 54, '0000-00-00', '0000-00-00', 'C'),
(171, 3, 55, '0000-00-00', '0000-00-00', 'C'),
(172, 3, 44, '0000-00-00', '0000-00-00', 'C'),
(173, 3, 45, '0000-00-00', '0000-00-00', 'C'),
(174, 3, 46, '0000-00-00', '0000-00-00', 'C'),
(175, 3, 47, '0000-00-00', '0000-00-00', 'C'),
(176, 3, 48, '0000-00-00', '0000-00-00', 'C'),
(177, 3, 49, '0000-00-00', '0000-00-00', 'C'),
(178, 3, 50, '0000-00-00', '0000-00-00', 'C'),
(179, 3, 51, '0000-00-00', '0000-00-00', 'C'),
(180, 3, 52, '0000-00-00', '0000-00-00', 'C'),
(181, 3, 56, '0000-00-00', '0000-00-00', 'C'),
(182, 3, 57, '0000-00-00', '0000-00-00', 'C'),
(183, 3, 58, '0000-00-00', '0000-00-00', 'C'),
(188, 3, 63, '0000-00-00', '0000-00-00', 'C'),
(190, 4, 1, '0000-00-00', '0000-00-00', 'C'),
(191, 4, 3, '0000-00-00', '0000-00-00', 'C'),
(192, 4, 4, '0000-00-00', '0000-00-00', 'C'),
(193, 4, 5, '0000-00-00', '0000-00-00', 'C'),
(194, 4, 6, '0000-00-00', '0000-00-00', 'C'),
(195, 4, 13, '0000-00-00', '0000-00-00', 'C'),
(196, 4, 14, '0000-00-00', '0000-00-00', 'C'),
(197, 4, 15, '0000-00-00', '0000-00-00', 'C'),
(198, 4, 7, '0000-00-00', '0000-00-00', 'C'),
(199, 4, 8, '0000-00-00', '0000-00-00', 'C'),
(200, 4, 9, '0000-00-00', '0000-00-00', 'C'),
(201, 4, 10, '2021-09-27', '2021-10-15', 'A'),
(202, 4, 11, '0000-00-00', '0000-00-00', 'C'),
(203, 4, 12, '0000-00-00', '0000-00-00', 'C'),
(204, 4, 16, '0000-00-00', '0000-00-00', 'C'),
(205, 4, 17, '0000-00-00', '0000-00-00', 'C'),
(206, 4, 18, '0000-00-00', '0000-00-00', 'C'),
(207, 4, 19, '0000-00-00', '0000-00-00', 'C'),
(208, 4, 20, '0000-00-00', '0000-00-00', 'C'),
(209, 4, 21, '0000-00-00', '0000-00-00', 'C'),
(210, 4, 22, '0000-00-00', '0000-00-00', 'C'),
(211, 4, 23, '0000-00-00', '0000-00-00', 'C'),
(212, 4, 24, '0000-00-00', '0000-00-00', 'C'),
(213, 4, 25, '0000-00-00', '0000-00-00', 'C'),
(214, 4, 26, '0000-00-00', '0000-00-00', 'C'),
(215, 4, 27, '0000-00-00', '0000-00-00', 'C'),
(216, 4, 37, '0000-00-00', '0000-00-00', 'C'),
(217, 4, 38, '0000-00-00', '0000-00-00', 'C'),
(218, 4, 39, '0000-00-00', '0000-00-00', 'C'),
(219, 4, 40, '0000-00-00', '0000-00-00', 'C'),
(220, 4, 41, '0000-00-00', '0000-00-00', 'C'),
(221, 4, 42, '0000-00-00', '0000-00-00', 'C'),
(222, 4, 43, '0000-00-00', '0000-00-00', 'C'),
(223, 4, 28, '0000-00-00', '0000-00-00', 'C'),
(224, 4, 29, '0000-00-00', '0000-00-00', 'C'),
(225, 4, 30, '0000-00-00', '0000-00-00', 'C'),
(226, 4, 31, '0000-00-00', '0000-00-00', 'C'),
(227, 4, 32, '0000-00-00', '0000-00-00', 'C'),
(228, 4, 33, '0000-00-00', '0000-00-00', 'C'),
(229, 4, 34, '0000-00-00', '0000-00-00', 'C'),
(230, 4, 35, '0000-00-00', '0000-00-00', 'C'),
(231, 4, 36, '0000-00-00', '0000-00-00', 'C'),
(232, 4, 53, '0000-00-00', '0000-00-00', 'C'),
(233, 4, 54, '0000-00-00', '0000-00-00', 'C'),
(234, 4, 55, '0000-00-00', '0000-00-00', 'C'),
(235, 4, 44, '0000-00-00', '0000-00-00', 'C'),
(236, 4, 45, '0000-00-00', '0000-00-00', 'C'),
(237, 4, 46, '0000-00-00', '0000-00-00', 'C'),
(238, 4, 47, '0000-00-00', '0000-00-00', 'C'),
(239, 4, 48, '0000-00-00', '0000-00-00', 'C'),
(240, 4, 49, '0000-00-00', '0000-00-00', 'C'),
(241, 4, 50, '0000-00-00', '0000-00-00', 'C'),
(242, 4, 51, '0000-00-00', '0000-00-00', 'C'),
(243, 4, 52, '0000-00-00', '0000-00-00', 'C'),
(244, 4, 56, '0000-00-00', '0000-00-00', 'C'),
(245, 4, 57, '0000-00-00', '0000-00-00', 'C'),
(246, 4, 58, '0000-00-00', '0000-00-00', 'C'),
(251, 4, 63, '0000-00-00', '0000-00-00', 'C'),
(253, 5, 1, '0000-00-00', '0000-00-00', 'C'),
(254, 5, 3, '0000-00-00', '0000-00-00', 'C'),
(255, 5, 4, '0000-00-00', '0000-00-00', 'C'),
(256, 5, 5, '0000-00-00', '0000-00-00', 'C'),
(257, 5, 6, '0000-00-00', '0000-00-00', 'C'),
(258, 5, 13, '0000-00-00', '0000-00-00', 'C'),
(259, 5, 14, '0000-00-00', '0000-00-00', 'C'),
(260, 5, 15, '0000-00-00', '0000-00-00', 'C'),
(261, 5, 7, '0000-00-00', '0000-00-00', 'C'),
(262, 5, 8, '0000-00-00', '0000-00-00', 'C'),
(263, 5, 9, '0000-00-00', '0000-00-00', 'C'),
(264, 5, 10, '2021-09-27', '2021-10-15', 'A'),
(265, 5, 11, '0000-00-00', '0000-00-00', 'C'),
(266, 5, 12, '0000-00-00', '0000-00-00', 'C'),
(267, 5, 16, '0000-00-00', '0000-00-00', 'C'),
(268, 5, 17, '0000-00-00', '0000-00-00', 'C'),
(269, 5, 18, '0000-00-00', '0000-00-00', 'C'),
(270, 5, 19, '0000-00-00', '0000-00-00', 'C'),
(271, 5, 20, '0000-00-00', '0000-00-00', 'C'),
(272, 5, 21, '0000-00-00', '0000-00-00', 'C'),
(273, 5, 22, '0000-00-00', '0000-00-00', 'C'),
(274, 5, 23, '0000-00-00', '0000-00-00', 'C'),
(275, 5, 24, '0000-00-00', '0000-00-00', 'C'),
(276, 5, 25, '0000-00-00', '0000-00-00', 'C'),
(277, 5, 26, '0000-00-00', '0000-00-00', 'C'),
(278, 5, 27, '0000-00-00', '0000-00-00', 'C'),
(279, 5, 37, '0000-00-00', '0000-00-00', 'C'),
(280, 5, 38, '0000-00-00', '0000-00-00', 'C'),
(281, 5, 39, '0000-00-00', '0000-00-00', 'C'),
(282, 5, 40, '0000-00-00', '0000-00-00', 'C'),
(283, 5, 41, '0000-00-00', '0000-00-00', 'C'),
(284, 5, 42, '0000-00-00', '0000-00-00', 'C'),
(285, 5, 43, '0000-00-00', '0000-00-00', 'C'),
(286, 5, 28, '0000-00-00', '0000-00-00', 'C'),
(287, 5, 29, '0000-00-00', '0000-00-00', 'C'),
(288, 5, 30, '0000-00-00', '0000-00-00', 'C'),
(289, 5, 31, '0000-00-00', '0000-00-00', 'C'),
(290, 5, 32, '0000-00-00', '0000-00-00', 'C'),
(291, 5, 33, '0000-00-00', '0000-00-00', 'C'),
(292, 5, 34, '0000-00-00', '0000-00-00', 'C'),
(293, 5, 35, '0000-00-00', '0000-00-00', 'C'),
(294, 5, 36, '0000-00-00', '0000-00-00', 'C'),
(295, 5, 53, '0000-00-00', '0000-00-00', 'C'),
(296, 5, 54, '0000-00-00', '0000-00-00', 'C'),
(297, 5, 55, '0000-00-00', '0000-00-00', 'C'),
(298, 5, 44, '0000-00-00', '0000-00-00', 'C'),
(299, 5, 45, '0000-00-00', '0000-00-00', 'C'),
(300, 5, 46, '0000-00-00', '0000-00-00', 'C'),
(301, 5, 47, '0000-00-00', '0000-00-00', 'C'),
(302, 5, 48, '0000-00-00', '0000-00-00', 'C'),
(303, 5, 49, '0000-00-00', '0000-00-00', 'C'),
(304, 5, 50, '0000-00-00', '0000-00-00', 'C'),
(305, 5, 51, '0000-00-00', '0000-00-00', 'C'),
(306, 5, 52, '0000-00-00', '0000-00-00', 'C'),
(307, 5, 56, '0000-00-00', '0000-00-00', 'C'),
(308, 5, 57, '0000-00-00', '0000-00-00', 'C'),
(309, 5, 58, '0000-00-00', '0000-00-00', 'C'),
(314, 5, 63, '0000-00-00', '0000-00-00', 'C'),
(316, 6, 1, '0000-00-00', '0000-00-00', 'C'),
(317, 6, 3, '0000-00-00', '0000-00-00', 'C'),
(318, 6, 4, '0000-00-00', '0000-00-00', 'C'),
(319, 6, 5, '0000-00-00', '0000-00-00', 'C'),
(320, 6, 6, '0000-00-00', '0000-00-00', 'C'),
(321, 6, 13, '0000-00-00', '0000-00-00', 'C'),
(322, 6, 14, '0000-00-00', '0000-00-00', 'C'),
(323, 6, 15, '0000-00-00', '0000-00-00', 'C'),
(324, 6, 7, '0000-00-00', '0000-00-00', 'C'),
(325, 6, 8, '0000-00-00', '0000-00-00', 'C'),
(326, 6, 9, '0000-00-00', '0000-00-00', 'C'),
(327, 6, 10, '2021-09-27', '2021-10-15', 'A'),
(328, 6, 11, '0000-00-00', '0000-00-00', 'C'),
(329, 6, 12, '0000-00-00', '0000-00-00', 'C'),
(330, 6, 16, '0000-00-00', '0000-00-00', 'C'),
(331, 6, 17, '0000-00-00', '0000-00-00', 'C'),
(332, 6, 18, '0000-00-00', '0000-00-00', 'C'),
(333, 6, 19, '0000-00-00', '0000-00-00', 'C'),
(334, 6, 20, '0000-00-00', '0000-00-00', 'C'),
(335, 6, 21, '0000-00-00', '0000-00-00', 'C'),
(336, 6, 22, '0000-00-00', '0000-00-00', 'C'),
(337, 6, 23, '0000-00-00', '0000-00-00', 'C'),
(338, 6, 24, '0000-00-00', '0000-00-00', 'C'),
(339, 6, 25, '0000-00-00', '0000-00-00', 'C'),
(340, 6, 26, '0000-00-00', '0000-00-00', 'C'),
(341, 6, 27, '0000-00-00', '0000-00-00', 'C'),
(342, 6, 37, '0000-00-00', '0000-00-00', 'C'),
(343, 6, 38, '0000-00-00', '0000-00-00', 'C'),
(344, 6, 39, '0000-00-00', '0000-00-00', 'C'),
(345, 6, 40, '0000-00-00', '0000-00-00', 'C'),
(346, 6, 41, '0000-00-00', '0000-00-00', 'C'),
(347, 6, 42, '0000-00-00', '0000-00-00', 'C'),
(348, 6, 43, '0000-00-00', '0000-00-00', 'C'),
(349, 6, 28, '0000-00-00', '0000-00-00', 'C'),
(350, 6, 29, '0000-00-00', '0000-00-00', 'C'),
(351, 6, 30, '0000-00-00', '0000-00-00', 'C'),
(352, 6, 31, '0000-00-00', '0000-00-00', 'C'),
(353, 6, 32, '0000-00-00', '0000-00-00', 'C'),
(354, 6, 33, '0000-00-00', '0000-00-00', 'C'),
(355, 6, 34, '0000-00-00', '0000-00-00', 'C'),
(356, 6, 35, '0000-00-00', '0000-00-00', 'C'),
(357, 6, 36, '0000-00-00', '0000-00-00', 'C'),
(358, 6, 53, '0000-00-00', '0000-00-00', 'C'),
(359, 6, 54, '0000-00-00', '0000-00-00', 'C'),
(360, 6, 55, '0000-00-00', '0000-00-00', 'C'),
(361, 6, 44, '0000-00-00', '0000-00-00', 'C'),
(362, 6, 45, '0000-00-00', '0000-00-00', 'C'),
(363, 6, 46, '0000-00-00', '0000-00-00', 'C'),
(364, 6, 47, '0000-00-00', '0000-00-00', 'C'),
(365, 6, 48, '0000-00-00', '0000-00-00', 'C'),
(366, 6, 49, '0000-00-00', '0000-00-00', 'C'),
(367, 6, 50, '0000-00-00', '0000-00-00', 'C'),
(368, 6, 51, '0000-00-00', '0000-00-00', 'C'),
(369, 6, 52, '0000-00-00', '0000-00-00', 'C'),
(370, 6, 56, '0000-00-00', '0000-00-00', 'C'),
(371, 6, 57, '0000-00-00', '0000-00-00', 'C'),
(372, 6, 58, '0000-00-00', '0000-00-00', 'C'),
(377, 6, 63, '0000-00-00', '0000-00-00', 'C'),
(379, 7, 1, '0000-00-00', '0000-00-00', 'C'),
(380, 7, 3, '0000-00-00', '0000-00-00', 'C'),
(381, 7, 4, '0000-00-00', '0000-00-00', 'C'),
(382, 7, 5, '0000-00-00', '0000-00-00', 'C'),
(383, 7, 6, '0000-00-00', '0000-00-00', 'C'),
(384, 7, 13, '0000-00-00', '0000-00-00', 'C'),
(385, 7, 14, '0000-00-00', '0000-00-00', 'C'),
(386, 7, 15, '0000-00-00', '0000-00-00', 'C'),
(387, 7, 7, '0000-00-00', '0000-00-00', 'C'),
(388, 7, 8, '0000-00-00', '0000-00-00', 'C'),
(389, 7, 9, '0000-00-00', '0000-00-00', 'C'),
(390, 7, 10, '0000-00-00', '0000-00-00', 'C'),
(391, 7, 11, '0000-00-00', '0000-00-00', 'C'),
(392, 7, 12, '0000-00-00', '0000-00-00', 'C'),
(393, 7, 16, '0000-00-00', '0000-00-00', 'C'),
(394, 7, 17, '0000-00-00', '0000-00-00', 'C'),
(395, 7, 18, '0000-00-00', '0000-00-00', 'C'),
(396, 7, 19, '0000-00-00', '0000-00-00', 'C'),
(397, 7, 20, '0000-00-00', '0000-00-00', 'C'),
(398, 7, 21, '0000-00-00', '0000-00-00', 'C'),
(399, 7, 22, '0000-00-00', '0000-00-00', 'C'),
(400, 7, 23, '0000-00-00', '0000-00-00', 'C'),
(401, 7, 24, '0000-00-00', '0000-00-00', 'C'),
(402, 7, 25, '0000-00-00', '0000-00-00', 'C'),
(403, 7, 26, '0000-00-00', '0000-00-00', 'C'),
(404, 7, 27, '0000-00-00', '0000-00-00', 'C'),
(405, 7, 37, '0000-00-00', '0000-00-00', 'C'),
(406, 7, 38, '0000-00-00', '0000-00-00', 'C'),
(407, 7, 39, '0000-00-00', '0000-00-00', 'C'),
(408, 7, 40, '0000-00-00', '0000-00-00', 'C'),
(409, 7, 41, '0000-00-00', '0000-00-00', 'C'),
(410, 7, 42, '0000-00-00', '0000-00-00', 'C'),
(411, 7, 43, '0000-00-00', '0000-00-00', 'C'),
(412, 7, 28, '0000-00-00', '0000-00-00', 'C'),
(413, 7, 29, '0000-00-00', '0000-00-00', 'C'),
(414, 7, 30, '0000-00-00', '0000-00-00', 'C'),
(415, 7, 31, '0000-00-00', '0000-00-00', 'C'),
(416, 7, 32, '0000-00-00', '0000-00-00', 'C'),
(417, 7, 33, '0000-00-00', '0000-00-00', 'C'),
(418, 7, 34, '0000-00-00', '0000-00-00', 'C'),
(419, 7, 35, '0000-00-00', '0000-00-00', 'C'),
(420, 7, 36, '0000-00-00', '0000-00-00', 'C'),
(421, 7, 53, '0000-00-00', '0000-00-00', 'C'),
(422, 7, 54, '0000-00-00', '0000-00-00', 'C'),
(423, 7, 55, '0000-00-00', '0000-00-00', 'C'),
(424, 7, 44, '0000-00-00', '0000-00-00', 'C'),
(425, 7, 45, '0000-00-00', '0000-00-00', 'C'),
(426, 7, 46, '0000-00-00', '0000-00-00', 'C'),
(427, 7, 47, '0000-00-00', '0000-00-00', 'C'),
(428, 7, 48, '0000-00-00', '0000-00-00', 'C'),
(429, 7, 49, '0000-00-00', '0000-00-00', 'C'),
(430, 7, 50, '0000-00-00', '0000-00-00', 'C'),
(431, 7, 51, '0000-00-00', '0000-00-00', 'C'),
(432, 7, 52, '0000-00-00', '0000-00-00', 'C'),
(433, 7, 56, '0000-00-00', '0000-00-00', 'C'),
(434, 7, 57, '0000-00-00', '0000-00-00', 'C'),
(435, 7, 58, '0000-00-00', '0000-00-00', 'C'),
(440, 7, 63, '0000-00-00', '0000-00-00', 'C'),
(442, 8, 1, '0000-00-00', '0000-00-00', 'C'),
(443, 8, 3, '0000-00-00', '0000-00-00', 'C'),
(444, 8, 4, '0000-00-00', '0000-00-00', 'C'),
(445, 8, 5, '0000-00-00', '0000-00-00', 'C'),
(446, 8, 6, '0000-00-00', '0000-00-00', 'C'),
(447, 8, 13, '0000-00-00', '0000-00-00', 'C'),
(448, 8, 14, '0000-00-00', '0000-00-00', 'C'),
(449, 8, 15, '0000-00-00', '0000-00-00', 'C'),
(450, 8, 7, '0000-00-00', '0000-00-00', 'C'),
(451, 8, 8, '0000-00-00', '0000-00-00', 'C'),
(452, 8, 9, '0000-00-00', '0000-00-00', 'C'),
(453, 8, 10, '0000-00-00', '0000-00-00', 'C'),
(454, 8, 11, '0000-00-00', '0000-00-00', 'C'),
(455, 8, 12, '0000-00-00', '0000-00-00', 'C'),
(456, 8, 16, '0000-00-00', '0000-00-00', 'C'),
(457, 8, 17, '0000-00-00', '0000-00-00', 'C'),
(458, 8, 18, '0000-00-00', '0000-00-00', 'C'),
(459, 8, 19, '0000-00-00', '0000-00-00', 'C'),
(460, 8, 20, '0000-00-00', '0000-00-00', 'C'),
(461, 8, 21, '0000-00-00', '0000-00-00', 'C'),
(462, 8, 22, '0000-00-00', '0000-00-00', 'C'),
(463, 8, 23, '0000-00-00', '0000-00-00', 'C'),
(464, 8, 24, '0000-00-00', '0000-00-00', 'C'),
(465, 8, 25, '0000-00-00', '0000-00-00', 'C'),
(466, 8, 26, '0000-00-00', '0000-00-00', 'C'),
(467, 8, 27, '0000-00-00', '0000-00-00', 'C'),
(468, 8, 37, '0000-00-00', '0000-00-00', 'C'),
(469, 8, 38, '0000-00-00', '0000-00-00', 'C'),
(470, 8, 39, '0000-00-00', '0000-00-00', 'C'),
(471, 8, 40, '0000-00-00', '0000-00-00', 'C'),
(472, 8, 41, '0000-00-00', '0000-00-00', 'C'),
(473, 8, 42, '0000-00-00', '0000-00-00', 'C'),
(474, 8, 43, '0000-00-00', '0000-00-00', 'C'),
(475, 8, 28, '0000-00-00', '0000-00-00', 'C'),
(476, 8, 29, '0000-00-00', '0000-00-00', 'C'),
(477, 8, 30, '0000-00-00', '0000-00-00', 'C'),
(478, 8, 31, '0000-00-00', '0000-00-00', 'C'),
(479, 8, 32, '0000-00-00', '0000-00-00', 'C'),
(480, 8, 33, '0000-00-00', '0000-00-00', 'C'),
(481, 8, 34, '0000-00-00', '0000-00-00', 'C'),
(482, 8, 35, '0000-00-00', '0000-00-00', 'C'),
(483, 8, 36, '0000-00-00', '0000-00-00', 'C'),
(484, 8, 53, '0000-00-00', '0000-00-00', 'C'),
(485, 8, 54, '0000-00-00', '0000-00-00', 'C'),
(486, 8, 55, '0000-00-00', '0000-00-00', 'C'),
(487, 8, 44, '0000-00-00', '0000-00-00', 'C'),
(488, 8, 45, '0000-00-00', '0000-00-00', 'C'),
(489, 8, 46, '0000-00-00', '0000-00-00', 'C'),
(490, 8, 47, '0000-00-00', '0000-00-00', 'C'),
(491, 8, 48, '0000-00-00', '0000-00-00', 'C'),
(492, 8, 49, '0000-00-00', '0000-00-00', 'C'),
(493, 8, 50, '0000-00-00', '0000-00-00', 'C'),
(494, 8, 51, '0000-00-00', '0000-00-00', 'C'),
(495, 8, 52, '0000-00-00', '0000-00-00', 'C'),
(496, 8, 56, '0000-00-00', '0000-00-00', 'C'),
(497, 8, 57, '0000-00-00', '0000-00-00', 'C'),
(498, 8, 58, '0000-00-00', '0000-00-00', 'C'),
(503, 8, 63, '0000-00-00', '0000-00-00', 'C'),
(505, 9, 1, '0000-00-00', '0000-00-00', 'C'),
(506, 9, 3, '0000-00-00', '0000-00-00', 'C'),
(507, 9, 4, '0000-00-00', '0000-00-00', 'C'),
(508, 9, 5, '0000-00-00', '0000-00-00', 'C'),
(509, 9, 6, '0000-00-00', '0000-00-00', 'C'),
(510, 9, 13, '0000-00-00', '0000-00-00', 'C'),
(511, 9, 14, '0000-00-00', '0000-00-00', 'C'),
(512, 9, 15, '0000-00-00', '0000-00-00', 'C'),
(513, 9, 7, '0000-00-00', '0000-00-00', 'C'),
(514, 9, 8, '0000-00-00', '0000-00-00', 'C'),
(515, 9, 9, '0000-00-00', '0000-00-00', 'C'),
(516, 9, 10, '0000-00-00', '0000-00-00', 'C'),
(517, 9, 11, '0000-00-00', '0000-00-00', 'C'),
(518, 9, 12, '0000-00-00', '0000-00-00', 'C'),
(519, 9, 16, '0000-00-00', '0000-00-00', 'C'),
(520, 9, 17, '0000-00-00', '0000-00-00', 'C'),
(521, 9, 18, '0000-00-00', '0000-00-00', 'C'),
(522, 9, 19, '0000-00-00', '0000-00-00', 'C'),
(523, 9, 20, '0000-00-00', '0000-00-00', 'C'),
(524, 9, 21, '0000-00-00', '0000-00-00', 'C'),
(525, 9, 22, '0000-00-00', '0000-00-00', 'C'),
(526, 9, 23, '0000-00-00', '0000-00-00', 'C'),
(527, 9, 24, '0000-00-00', '0000-00-00', 'C'),
(528, 9, 25, '0000-00-00', '0000-00-00', 'C'),
(529, 9, 26, '0000-00-00', '0000-00-00', 'C'),
(530, 9, 27, '0000-00-00', '0000-00-00', 'C'),
(531, 9, 37, '0000-00-00', '0000-00-00', 'C'),
(532, 9, 38, '0000-00-00', '0000-00-00', 'C'),
(533, 9, 39, '0000-00-00', '0000-00-00', 'C'),
(534, 9, 40, '0000-00-00', '0000-00-00', 'C'),
(535, 9, 41, '0000-00-00', '0000-00-00', 'C'),
(536, 9, 42, '0000-00-00', '0000-00-00', 'C'),
(537, 9, 43, '0000-00-00', '0000-00-00', 'C'),
(538, 9, 28, '0000-00-00', '0000-00-00', 'C'),
(539, 9, 29, '0000-00-00', '0000-00-00', 'C'),
(540, 9, 30, '0000-00-00', '0000-00-00', 'C'),
(541, 9, 31, '0000-00-00', '0000-00-00', 'C'),
(542, 9, 32, '0000-00-00', '0000-00-00', 'C'),
(543, 9, 33, '0000-00-00', '0000-00-00', 'C'),
(544, 9, 34, '0000-00-00', '0000-00-00', 'C'),
(545, 9, 35, '0000-00-00', '0000-00-00', 'C'),
(546, 9, 36, '0000-00-00', '0000-00-00', 'C'),
(547, 9, 53, '0000-00-00', '0000-00-00', 'C'),
(548, 9, 54, '0000-00-00', '0000-00-00', 'C'),
(549, 9, 55, '0000-00-00', '0000-00-00', 'C'),
(550, 9, 44, '0000-00-00', '0000-00-00', 'C'),
(551, 9, 45, '0000-00-00', '0000-00-00', 'C'),
(552, 9, 46, '0000-00-00', '0000-00-00', 'C'),
(553, 9, 47, '0000-00-00', '0000-00-00', 'C'),
(554, 9, 48, '0000-00-00', '0000-00-00', 'C'),
(555, 9, 49, '0000-00-00', '0000-00-00', 'C'),
(556, 9, 50, '0000-00-00', '0000-00-00', 'C'),
(557, 9, 51, '0000-00-00', '0000-00-00', 'C'),
(558, 9, 52, '0000-00-00', '0000-00-00', 'C'),
(559, 9, 56, '0000-00-00', '0000-00-00', 'C'),
(560, 9, 57, '0000-00-00', '0000-00-00', 'C'),
(561, 9, 58, '0000-00-00', '0000-00-00', 'C'),
(566, 9, 63, '0000-00-00', '0000-00-00', 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_area`
--

CREATE TABLE `sw_area` (
  `id_area` int(11) NOT NULL,
  `ar_nombre` varchar(45) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_area`
--

INSERT INTO `sw_area` (`id_area`, `ar_nombre`) VALUES
(1, 'LENGUA Y LITERATURA'),
(2, 'MATEMATICA'),
(3, 'CIENCIAS SOCIALES'),
(4, 'CIENCIAS NATURALES'),
(5, 'EDUCACION CULTURAL Y ARTISTICA'),
(6, 'EDUCACION FISICA'),
(7, 'LENGUA EXTRANJERA'),
(8, 'PROYECTOS ESCOLARES'),
(9, 'MODULO INTERDISCIPLINAR'),
(10, 'HORAS ADICIONALES A DISCRECION'),
(11, 'ASIGNATURAS OPTATIVAS'),
(12, 'ELECTROMECANICA AUTOMOTRIZ'),
(13, 'ELECTRONICA DE CONSUMO'),
(14, 'INDUSTRIA DE LA CONFECCION'),
(15, 'INSTALACIONES, EQUIPOS Y MAQUINAS ELECTRICAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asignatura`
--

CREATE TABLE `sw_asignatura` (
  `id_asignatura` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL,
  `as_nombre` varchar(120) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `as_abreviatura` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `as_shortname` varchar(45) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `as_curricular` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_asignatura`
--

INSERT INTO `sw_asignatura` (`id_asignatura`, `id_area`, `id_tipo_asignatura`, `as_nombre`, `as_abreviatura`, `as_shortname`, `as_curricular`) VALUES
(1, 11, 1, 'BIOLOGIA SUPERIOR', 'BIO.SUP.', '', 1),
(2, 11, 1, 'QUIMICA SUPERIOR', 'QUIM.SUP.', '', 1),
(3, 11, 1, 'FISICA SUPERIOR', 'FIS.SUP.', '', 1),
(4, 11, 1, 'INVESTIGACION DE CIENCIAS Y TECNOLOGIA', 'INV.CIEN.', '', 1),
(5, 4, 1, 'CIENCIAS NATURALES', 'CC.NN.', '', 1),
(6, 4, 1, 'FISICA', 'FIS.', '', 1),
(7, 4, 1, 'QUIMICA', 'QUIM.', '', 1),
(8, 4, 1, 'BIOLOGIA', 'BIO.', '', 1),
(9, 3, 1, 'ESTUDIOS SOCIALES', 'EE.SS.', '', 1),
(10, 3, 1, 'HISTORIA', 'HIS.', '', 1),
(11, 3, 1, 'EDUCACION PARA LA CIUDADANIA', 'ED.CIUD.', '', 1),
(12, 3, 1, 'FILOSOFIA', 'FILO.', '', 1),
(13, 5, 1, 'EDUCACION CULTURAL Y ARTISTICA', 'ECA', '', 1),
(14, 6, 1, 'EDUCACION FISICA', 'EE.FF.', '', 1),
(15, 12, 1, 'MOTORES DE COMBUSTION EXTERNA', 'MCE', '', 1),
(16, 12, 1, 'TREN DE RODAJE', 'T.R.', '', 1),
(17, 12, 1, 'SISTEMAS ELECTRICOS Y ELECTRONICOS', 'SEE', '', 1),
(18, 12, 1, 'SISTEMAS DE SEGURIDAD Y CONFORTABILIDAD', 'SSYC', '', 1),
(19, 12, 1, 'METALMECANICA APLICADA EN EL MANTENIMIENTO DE VEHICULOS AUTOMOTORES', 'MAMVA', '', 1),
(20, 12, 1, 'ELECTROTECNIA Y ELECTRONICA APLICADAS EN EL MANTENIMIENTO DE VEHICULOS  AUTOMOTORES', 'EYEAMVA', '', 1),
(21, 12, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(22, 12, 2, 'FORMACION EN CENTROS DE TRABAJO', 'FCT', '', 1),
(23, 13, 1, 'EQUIPOS Y SISTEMAS ELECTRONICOS DE AUDIO Y VIDEO', 'EYESE', '', 1),
(24, 13, 1, 'EQUIPOS Y SISTEMAS MICROINFORMATICOS', 'EYSM', '', 1),
(25, 13, 1, 'EQUIPOS Y SISTEMAS MICROPROCESADOS', 'EYESMICRO', '', 1),
(26, 13, 1, 'EQUIPOS Y SISTEMAS DE TELEFONIA', 'EYSTEL', '', 1),
(27, 13, 1, 'ELECTRONICA GENERAL', 'ELEGEN', '', 1),
(28, 13, 1, 'ELECTRONICA DIGITAL', 'ELEDIG', '', 1),
(29, 13, 1, 'INSTALACIONES ELECTRICAS BASICAS', 'INSTELEBAS', '', 1),
(30, 13, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(31, 13, 2, 'FORMACION EN CENTROS DE TRABAJO', 'FCT', '', 1),
(32, 10, 1, 'ANATOMIA', 'ANAT.', '', 1),
(33, 10, 1, 'PSICOLOGIA', 'PSICO.', '', 1),
(34, 14, 1, 'PROCESOS, TECNICAS E IINDUSTRIALIZACION DE PATRONES DE PRENDAS Y COMPLEMENTOS DEL VESTIR', 'PTECEIND', '', 1),
(35, 14, 1, 'TECNICAS DE CORTE DE TEJIDOS Y PIELES', 'TECCORTE', '', 1),
(36, 14, 1, 'TECNICAS DE ENSAMBLAJE', 'TECENSAM', '', 1),
(37, 14, 1, 'ACABADOS DE CONFECCION', 'ACABCONFEC', '', 1),
(38, 14, 1, 'PRODUCTOS Y PROCESOS DE CONFECCION', 'PRODCONFEC', '', 1),
(39, 14, 1, 'MATERIALES TEXTILES, PIEL Y CUERO', 'MATTEXTPYC', '', 1),
(40, 14, 1, 'DIBUJO TECNICO APLICADO', 'DIB.TEC.', '', 1),
(41, 14, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(42, 14, 2, 'FORMACION EN CENTROS DE TRABAJO', 'FCT', '', 1),
(43, 15, 1, 'INSTALACION DE SERVICIOS ESPECIALES EN EDIFICIOS', 'INSTSERVESP', '', 1),
(44, 15, 1, 'INSTALACIONES AUTOMATIZADAS ELECTRICAS', 'INSTAUTELEC', '', 1),
(45, 15, 1, 'INSTALACIONES DE ENLACE Y CENTROS DE TRANSFORMACION', 'INSTENLACT', '', 1),
(46, 15, 1, 'MANTENIMIENTO DE MAQUINAS ELECTRICAS', 'MANTMAQELEC', '', 1),
(47, 15, 1, 'ELECTROTECNIA', 'ELECTROTEC', '', 1),
(48, 15, 1, 'INSTALACIONES ELECTRICAS DE INTERIOR', 'INSTELECINTER', '', 1),
(49, 15, 1, 'AUTOMATISMOS Y TABLEROS ELECTRICOS', 'AUTTABLELEC', '', 1),
(50, 15, 1, 'FORMACION Y ORIENTACION LABORAL', 'FOL', '', 1),
(51, 15, 2, 'FORMACION EN CENTROS DE TRABAJO', 'FCT', '', 1),
(52, 7, 1, 'INGLES', 'INGLES', '', 1),
(53, 1, 1, 'LENGUA Y LITERATURA', 'LYL', '', 1),
(54, 2, 1, 'MATEMATICA', 'MATE', '', 1),
(55, 9, 1, 'EMPRENDIMIENTO Y GESTION', 'EYG', '', 1),
(56, 8, 2, 'PROYECTOS ESCOLARES', 'PR.ES.', '', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_asignatura_curso`
--

INSERT INTO `sw_asignatura_curso` (`id_asignatura_curso`, `id_curso`, `id_asignatura`, `id_periodo_lectivo`, `ac_orden`, `ac_num_horas`) VALUES
(1, 4, 53, 1, 1, 0),
(2, 4, 54, 1, 2, 0),
(3, 4, 9, 1, 3, 0),
(4, 4, 5, 1, 4, 0),
(5, 4, 13, 1, 5, 0),
(6, 4, 14, 1, 6, 0),
(7, 4, 52, 1, 7, 0),
(8, 4, 56, 1, 8, 0),
(9, 5, 53, 1, 1, 0),
(10, 5, 54, 1, 2, 0),
(11, 5, 9, 1, 3, 0),
(12, 5, 5, 1, 4, 0),
(13, 5, 13, 1, 5, 0),
(14, 5, 14, 1, 6, 0),
(15, 5, 52, 1, 7, 0),
(16, 5, 56, 1, 8, 0),
(17, 6, 53, 1, 1, 0),
(18, 6, 54, 1, 2, 0),
(19, 6, 9, 1, 3, 0),
(20, 6, 5, 1, 4, 0),
(21, 6, 13, 1, 5, 0),
(22, 6, 14, 1, 6, 0),
(23, 6, 52, 1, 7, 0),
(24, 6, 56, 1, 8, 0),
(25, 7, 53, 1, 1, 0),
(26, 7, 54, 1, 2, 0),
(27, 7, 9, 1, 3, 0),
(28, 7, 5, 1, 4, 0),
(29, 7, 13, 1, 5, 0),
(30, 7, 14, 1, 6, 0),
(31, 7, 52, 1, 7, 0),
(32, 7, 56, 1, 8, 0),
(33, 8, 53, 1, 1, 0),
(34, 8, 54, 1, 2, 0),
(35, 8, 9, 1, 3, 0),
(36, 8, 5, 1, 4, 0),
(37, 8, 13, 1, 5, 0),
(38, 8, 14, 1, 6, 0),
(39, 8, 52, 1, 7, 0),
(40, 8, 56, 1, 8, 0),
(41, 9, 53, 1, 1, 0),
(42, 9, 54, 1, 2, 0),
(43, 9, 9, 1, 3, 0),
(44, 9, 5, 1, 4, 0),
(45, 9, 13, 1, 5, 0),
(46, 9, 14, 1, 6, 0),
(47, 9, 52, 1, 7, 0),
(48, 9, 56, 1, 8, 0),
(49, 10, 53, 1, 1, 0),
(50, 10, 54, 1, 2, 0),
(51, 10, 9, 1, 3, 0),
(52, 10, 5, 1, 4, 0),
(53, 10, 13, 1, 5, 0),
(54, 10, 14, 1, 6, 0),
(55, 10, 52, 1, 7, 0),
(56, 10, 56, 1, 8, 0),
(57, 11, 53, 1, 1, 0),
(58, 11, 54, 1, 2, 0),
(59, 11, 9, 1, 3, 0),
(60, 11, 5, 1, 4, 0),
(61, 11, 13, 1, 5, 0),
(62, 11, 14, 1, 6, 0),
(63, 11, 52, 1, 7, 0),
(64, 11, 56, 1, 8, 0),
(65, 12, 53, 1, 1, 0),
(66, 12, 54, 1, 2, 0),
(67, 12, 9, 1, 3, 0),
(68, 12, 5, 1, 4, 0),
(69, 12, 13, 1, 5, 0),
(70, 12, 14, 1, 6, 0),
(71, 12, 52, 1, 7, 0),
(72, 12, 56, 1, 8, 0),
(73, 13, 16, 1, 13, 0),
(74, 13, 19, 1, 14, 0),
(75, 13, 21, 1, 15, 0),
(76, 13, 54, 1, 1, 0),
(77, 13, 6, 1, 2, 0),
(78, 13, 7, 1, 3, 0),
(79, 13, 8, 1, 4, 0),
(80, 13, 10, 1, 5, 0),
(81, 13, 11, 1, 6, 0),
(82, 13, 12, 1, 7, 0),
(83, 13, 53, 1, 8, 0),
(84, 13, 52, 1, 9, 0),
(85, 13, 13, 1, 10, 0),
(86, 13, 14, 1, 11, 0),
(87, 13, 55, 1, 12, 0),
(88, 14, 54, 1, 1, 0),
(89, 14, 6, 1, 2, 0),
(90, 14, 7, 1, 3, 0),
(91, 14, 8, 1, 4, 0),
(92, 14, 10, 1, 5, 0),
(93, 14, 11, 1, 6, 0),
(94, 14, 12, 1, 7, 0),
(95, 14, 53, 1, 8, 0),
(96, 14, 52, 1, 9, 0),
(97, 14, 13, 1, 10, 0),
(98, 14, 14, 1, 11, 0),
(99, 14, 55, 1, 12, 0),
(100, 14, 16, 1, 13, 0),
(101, 14, 17, 1, 14, 0),
(102, 14, 20, 1, 15, 0),
(103, 15, 54, 1, 1, 0),
(104, 15, 6, 1, 2, 0),
(105, 15, 7, 1, 3, 0),
(106, 15, 8, 1, 4, 0),
(107, 15, 10, 1, 5, 0),
(108, 15, 53, 1, 6, 0),
(109, 15, 52, 1, 7, 0),
(110, 15, 13, 1, 8, 0),
(111, 15, 14, 1, 9, 0),
(112, 15, 55, 1, 10, 0),
(113, 15, 15, 1, 11, 0),
(114, 15, 17, 1, 12, 0),
(115, 15, 18, 1, 13, 0),
(116, 15, 21, 1, 14, 0),
(117, 15, 22, 1, 15, 0),
(118, 16, 54, 1, 1, 0),
(119, 16, 6, 1, 2, 0),
(120, 16, 7, 1, 3, 0),
(121, 16, 8, 1, 4, 0),
(122, 16, 10, 1, 5, 0),
(123, 16, 11, 1, 6, 0),
(124, 16, 12, 1, 7, 0),
(125, 16, 53, 1, 8, 0),
(126, 16, 52, 1, 9, 0),
(127, 16, 13, 1, 10, 0),
(128, 16, 14, 1, 11, 0),
(129, 16, 55, 1, 12, 0),
(130, 16, 27, 1, 13, 0),
(131, 16, 28, 1, 14, 0),
(132, 16, 29, 1, 15, 0),
(133, 16, 30, 1, 16, 0),
(134, 17, 54, 1, 1, 0),
(135, 17, 6, 1, 2, 0),
(136, 17, 7, 1, 3, 0),
(137, 17, 8, 1, 4, 0),
(138, 17, 10, 1, 5, 0),
(139, 17, 11, 1, 6, 0),
(140, 17, 12, 1, 7, 0),
(141, 17, 53, 1, 8, 0),
(142, 17, 52, 1, 9, 0),
(143, 17, 13, 1, 10, 0),
(144, 17, 14, 1, 11, 0),
(145, 17, 55, 1, 12, 0),
(146, 17, 23, 1, 13, 0),
(147, 17, 24, 1, 14, 0),
(148, 17, 25, 1, 15, 0),
(149, 17, 28, 1, 16, 0),
(150, 18, 54, 1, 1, 0),
(151, 18, 6, 1, 2, 0),
(152, 18, 7, 1, 3, 0),
(153, 18, 8, 1, 4, 0),
(154, 18, 10, 1, 5, 0),
(155, 18, 53, 1, 6, 0),
(156, 18, 52, 1, 7, 0),
(157, 18, 13, 1, 8, 0),
(158, 18, 14, 1, 9, 0),
(159, 18, 55, 1, 10, 0),
(160, 18, 23, 1, 11, 0),
(161, 18, 24, 1, 12, 0),
(162, 18, 25, 1, 13, 0),
(163, 18, 26, 1, 14, 0),
(164, 18, 30, 1, 15, 0),
(165, 18, 31, 1, 16, 0),
(166, 19, 54, 1, 1, 0),
(167, 19, 6, 1, 2, 0),
(168, 19, 7, 1, 3, 0),
(169, 19, 8, 1, 4, 0),
(170, 19, 10, 1, 5, 0),
(171, 19, 11, 1, 6, 0),
(172, 19, 12, 1, 7, 0),
(173, 19, 53, 1, 8, 0),
(174, 19, 52, 1, 9, 0),
(175, 19, 13, 1, 10, 0),
(176, 19, 14, 1, 11, 0),
(177, 19, 55, 1, 12, 0),
(178, 19, 34, 1, 13, 0),
(179, 19, 36, 1, 14, 0),
(180, 19, 39, 1, 15, 0),
(181, 19, 40, 1, 16, 0),
(182, 20, 54, 1, 1, 0),
(183, 20, 6, 1, 2, 0),
(184, 20, 7, 1, 3, 0),
(185, 20, 8, 1, 4, 0),
(186, 20, 10, 1, 5, 0),
(187, 20, 11, 1, 6, 0),
(188, 20, 12, 1, 7, 0),
(189, 20, 52, 1, 9, 0),
(190, 20, 13, 1, 10, 0),
(191, 20, 14, 1, 11, 0),
(192, 20, 55, 1, 12, 0),
(193, 20, 34, 1, 13, 0),
(194, 20, 35, 1, 14, 0),
(195, 20, 36, 1, 15, 0),
(196, 20, 40, 1, 16, 0),
(197, 20, 53, 1, 8, 0),
(198, 21, 54, 1, 1, 0),
(199, 21, 6, 1, 2, 0),
(200, 21, 7, 1, 3, 0),
(201, 21, 8, 1, 4, 0),
(202, 21, 10, 1, 5, 0),
(203, 21, 53, 1, 6, 0),
(204, 21, 52, 1, 7, 0),
(205, 21, 13, 1, 8, 0),
(206, 21, 14, 1, 9, 0),
(207, 21, 55, 1, 10, 0),
(208, 21, 34, 1, 11, 0),
(209, 21, 36, 1, 12, 0),
(210, 21, 37, 1, 13, 0),
(211, 21, 38, 1, 14, 0),
(212, 21, 40, 1, 15, 0),
(213, 21, 41, 1, 16, 0),
(214, 21, 42, 1, 17, 0),
(215, 22, 54, 1, 1, 0),
(216, 22, 6, 1, 2, 0),
(217, 22, 7, 1, 3, 0),
(218, 22, 8, 1, 4, 0),
(219, 22, 10, 1, 5, 0),
(220, 22, 11, 1, 6, 0),
(221, 22, 12, 1, 7, 0),
(222, 22, 53, 1, 8, 0),
(223, 22, 52, 1, 9, 0),
(224, 22, 13, 1, 10, 0),
(225, 22, 14, 1, 11, 0),
(226, 22, 55, 1, 12, 0),
(227, 22, 47, 1, 13, 0),
(228, 22, 48, 1, 14, 0),
(229, 22, 50, 1, 15, 0),
(230, 23, 54, 1, 1, 0),
(231, 23, 6, 1, 2, 0),
(232, 23, 7, 1, 3, 0),
(233, 23, 8, 1, 4, 0),
(234, 23, 10, 1, 5, 0),
(235, 23, 11, 1, 6, 0),
(236, 23, 12, 1, 7, 0),
(237, 23, 53, 1, 8, 0),
(238, 23, 52, 1, 9, 0),
(239, 23, 13, 1, 10, 0),
(240, 23, 14, 1, 11, 0),
(241, 23, 55, 1, 12, 0),
(242, 23, 47, 1, 13, 0),
(243, 23, 48, 1, 14, 0),
(244, 23, 49, 1, 15, 0),
(245, 23, 50, 1, 16, 0),
(246, 24, 54, 1, 1, 0),
(247, 24, 6, 1, 2, 0),
(248, 24, 7, 1, 3, 0),
(249, 24, 8, 1, 4, 0),
(250, 24, 10, 1, 5, 0),
(251, 24, 53, 1, 6, 0),
(252, 24, 52, 1, 7, 0),
(253, 24, 13, 1, 8, 0),
(254, 24, 14, 1, 9, 0),
(255, 24, 55, 1, 10, 0),
(256, 24, 43, 1, 11, 0),
(257, 24, 44, 1, 12, 0),
(258, 24, 45, 1, 13, 0),
(259, 24, 47, 1, 15, 0),
(260, 24, 51, 1, 16, 0),
(261, 24, 46, 1, 14, 0),
(262, 1, 54, 1, 1, 0),
(263, 1, 6, 1, 2, 0),
(264, 1, 7, 1, 3, 0),
(265, 1, 8, 1, 4, 0),
(266, 1, 10, 1, 5, 0),
(267, 1, 11, 1, 6, 0),
(268, 1, 12, 1, 7, 0),
(269, 1, 53, 1, 8, 0),
(270, 1, 52, 1, 9, 0),
(271, 1, 13, 1, 10, 0),
(272, 1, 14, 1, 11, 0),
(273, 1, 55, 1, 12, 0),
(274, 1, 32, 1, 13, 0),
(275, 1, 33, 1, 14, 0),
(276, 2, 54, 1, 1, 0),
(277, 2, 6, 1, 2, 0),
(278, 2, 7, 1, 3, 0),
(279, 2, 8, 1, 4, 0),
(280, 2, 10, 1, 5, 0),
(281, 2, 11, 1, 6, 0),
(282, 2, 12, 1, 7, 0),
(283, 2, 13, 1, 10, 0),
(284, 2, 14, 1, 11, 0),
(285, 2, 53, 1, 8, 0),
(286, 2, 52, 1, 9, 0),
(287, 2, 55, 1, 12, 0),
(288, 2, 32, 1, 13, 0),
(289, 2, 33, 1, 14, 0),
(290, 3, 54, 1, 1, 0),
(291, 3, 6, 1, 2, 0),
(292, 3, 7, 1, 3, 0),
(293, 3, 8, 1, 4, 0),
(294, 3, 10, 1, 5, 0),
(295, 3, 53, 1, 6, 0),
(296, 3, 52, 1, 7, 0),
(297, 3, 13, 1, 8, 0),
(298, 3, 14, 1, 9, 0),
(299, 3, 55, 1, 10, 0),
(300, 3, 32, 1, 11, 0),
(301, 3, 33, 1, 12, 0),
(302, 3, 1, 1, 13, 0),
(303, 3, 2, 1, 14, 0),
(304, 3, 3, 1, 15, 0),
(305, 3, 4, 1, 16, 0),
(306, 27, 53, 1, 1, 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `at_justificacion` varchar(256) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_asociar_curso_superior`
--

CREATE TABLE `sw_asociar_curso_superior` (
  `id_asociar_curso_superior` int(11) NOT NULL,
  `id_curso_inferior` int(11) DEFAULT NULL,
  `id_curso_superior` int(11) DEFAULT NULL,
  `id_periodo_lectivo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_asociar_curso_superior`
--

INSERT INTO `sw_asociar_curso_superior` (`id_asociar_curso_superior`, `id_curso_inferior`, `id_curso_superior`, `id_periodo_lectivo`) VALUES
(1, 4, 5, 1);

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
  `co_cualitativa` varchar(3) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comentario`
--

CREATE TABLE `sw_comentario` (
  `id_comentario` int(11) NOT NULL,
  `id_comentario_padre` int(11) NOT NULL,
  `co_id_usuario` int(11) NOT NULL,
  `co_tipo` tinyint(4) NOT NULL,
  `co_perfil` varchar(16) CHARACTER SET latin1 NOT NULL,
  `co_nombre` varchar(64) CHARACTER SET latin1 NOT NULL,
  `co_texto` varchar(250) CHARACTER SET latin1 NOT NULL,
  `co_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_comentario`
--

INSERT INTO `sw_comentario` (`id_comentario`, `id_comentario_padre`, `co_id_usuario`, `co_tipo`, `co_perfil`, `co_nombre`, `co_texto`, `co_fecha`) VALUES
(2, 0, 44, 2, 'Docente', 'Lic. Rosa Herrera', 'SALUDOS MI ESTIMADO', '2021-08-06 15:19:46'),
(3, 0, 42, 2, 'Docente', 'Prof. Narcisa Holguín', 'SALUDOS YA ESTAN INGRESADOS LOS ESTUDIANTES DE 3 A MECANICA AUTOMOTRIZ ', '2021-08-10 20:06:40'),
(4, 3, 86, 2, 'Administrador', 'Ing. Administrador Daule', 'LISTO MISS. FELICITACIONES :D', '2021-08-10 21:45:53'),
(5, 0, 2, 2, 'Docente', 'Lic. Eladio Varas', 'Buen dìa he ingresado los datos de 21 estudiantes. Solo falta de un estudiante que es el jòven proaño velasque del cuàl no tengo datos.....', '2021-08-12 15:31:00'),
(6, 0, 18, 2, 'Docente', 'Lic. Vilma Peñafiel', 'bUENAS TARDES HE INGRESADO LOS DATOS DE 19 ESTUDIANTES DE 25 QUE TENGO A MI CARGO FALTANTES 6 ESTUDIANTES DE LOS CUALES HE LLAMADO PERO NO CONTESTAN LAS LLAMADAS HE DEJADO MENSAJES OJALA RESPONDAN PARA PODER COMPLETAR LA INFOMACION. ', '2021-08-19 19:56:35'),
(7, 0, 18, 2, 'Docente', 'Lic. Vilma Peñafiel', 'SALUDOS DEL ESTUDIANTES LEON CHAVEZ LUIS CARLOS NO HAY NINGUNA INFORMACIÓN NO TENGO CONTACTO PARA PODER LLAMAR POR FAVOR AYUDA PARA PODER LOCALIZARLO NECESITO SU CONTACTO GRACIAS.', '2021-08-19 19:59:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_comportamiento`
--

CREATE TABLE `sw_comportamiento` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_evaluacion` int(11) NOT NULL,
  `id_indice_evaluacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `co_calificacion` varchar(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `co_calificacion` varchar(1) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso`
--

CREATE TABLE `sw_curso` (
  `id_curso` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `cu_nombre` varchar(128) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `cu_shortname` varchar(45) CHARACTER SET latin1 NOT NULL,
  `cu_orden` int(11) NOT NULL,
  `id_curso_superior` int(11) NOT NULL,
  `bol_proyectos` tinyint(1) NOT NULL,
  `cu_abreviatura` varchar(5) CHARACTER SET latin1 NOT NULL,
  `quien_inserta_comp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_curso`
--

INSERT INTO `sw_curso` (`id_curso`, `id_especialidad`, `cu_nombre`, `cu_shortname`, `cu_orden`, `id_curso_superior`, `bol_proyectos`, `cu_abreviatura`, `quien_inserta_comp`) VALUES
(1, 8, 'PRIMER CURSO', 'PRIMERO CIENCIAS', 1, 0, 0, '1ero.', 1),
(2, 8, 'SEGUNDO CURSO', 'SEGUNDO CIENCIAS', 2, 0, 0, '2do.', 1),
(3, 8, 'TERCER CURSO', 'TERCERO CIENCIAS', 3, 0, 0, '3ro.', 1),
(4, 1, 'SEGUNDO', 'SEGUNDO', 1, 0, 0, '2do.', 1),
(5, 1, 'TERCERO', 'TERCERO', 2, 0, 0, '3ro.', 1),
(6, 1, 'CUARTO', 'CUARTO', 3, 0, 0, '4to.', 1),
(7, 2, 'QUINTO', 'QUINTO', 1, 0, 0, '5to.', 1),
(8, 2, 'SEXTO', 'SEXTO', 2, 0, 0, '6to.', 1),
(9, 2, 'SEPTIMO', 'SEPTIMO', 3, 0, 0, '7mo.', 1),
(10, 3, 'OCTAVO', 'OCTAVO', 1, 0, 0, '8vo.', 1),
(11, 3, 'NOVENO', 'NOVENO', 2, 0, 0, '9no.', 1),
(12, 3, 'DECIMO', 'DECIMO', 3, 0, 0, '10mo.', 1),
(13, 4, 'PRIMER CURSO', 'PRIMERO ELECTROMECÁNICA', 1, 0, 0, '1ro.', 1),
(14, 4, 'SEGUNDO CURSO', 'SEGUNDO ELECTROMECÁNICA', 2, 0, 0, '2do.', 1),
(15, 4, 'TERCER CURSO', 'TERCERO ELECTROMECÁNICA', 3, 0, 0, '3ro.', 1),
(16, 5, 'PRIMER CURSO', 'PRIMERO ELECTRÓNICA', 1, 0, 0, '1ro.', 1),
(17, 5, 'SEGUNDO CURSO', 'SEGUNDO ELECTRÓNICA', 2, 0, 0, '2do.', 1),
(18, 5, 'TERCER CURSO', 'TERCERO ELECTRÓNICA', 3, 0, 0, '3ro.', 1),
(19, 6, 'PRIMER CURSO', 'PRIMERO CONFECCIÓN', 1, 0, 0, '1ro.', 1),
(20, 6, 'SEGUNDO CURSO', 'SEGUNDO CONFECCIÓN', 2, 0, 0, '2do.', 1),
(21, 6, 'TERCER CURSO', 'TERCERO CONFECCIÓN', 3, 0, 0, '3ro.', 1),
(22, 7, 'PRIMER CURSO', 'PRIMERO CONFECCIÓN', 1, 0, 0, '1ro.', 1),
(23, 7, 'SEGUNDO CURSO', 'SEGUNDO CONFECCIÓN', 2, 0, 0, '2do.', 1),
(24, 7, 'TERCER CURSO', 'TERCERO CONFECCIÓN', 3, 0, 0, '3ro.', 1),
(27, 10, 'PRIMERO', 'PRIMERO', 1, 0, 0, '1ro', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_curso_superior`
--

CREATE TABLE `sw_curso_superior` (
  `id_curso_superior` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `cs_nombre` varchar(64) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_dia_semana`
--

CREATE TABLE `sw_dia_semana` (
  `id_dia_semana` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ds_nombre` varchar(10) CHARACTER SET latin1 NOT NULL,
  `ds_ordinal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_dia_semana`
--

INSERT INTO `sw_dia_semana` (`id_dia_semana`, `id_periodo_lectivo`, `ds_nombre`, `ds_ordinal`) VALUES
(1, 1, 'Lunes', 1),
(2, 1, 'Martes', 2),
(3, 1, 'Miércoles', 3),
(4, 1, 'Jueves', 4),
(5, 1, 'Viernes', 5);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_distributivo`
--

INSERT INTO `sw_distributivo` (`id_distributivo`, `id_periodo_lectivo`, `id_malla_curricular`, `id_paralelo`, `id_asignatura`, `id_usuario`) VALUES
(2, 1, 4, 58, 6, 74),
(3, 1, 5, 58, 3, 74),
(4, 1, 6, 58, 54, 74),
(5, 1, 7, 55, 6, 74),
(6, 1, 11, 54, 54, 74),
(7, 1, 8, 55, 54, 74),
(8, 1, 9, 52, 6, 74),
(10, 1, 10, 52, 54, 74),
(11, 1, 12, 51, 54, 74),
(12, 1, 13, 25, 53, 10),
(13, 1, 14, 29, 12, 10),
(14, 1, 53, 54, 6, 93),
(15, 1, 54, 51, 6, 93),
(18, 1, 57, 32, 53, 22),
(19, 1, 58, 34, 53, 22),
(20, 1, 59, 31, 12, 22),
(23, 1, 62, 20, 52, 89),
(24, 1, 63, 23, 52, 89),
(28, 1, 64, 56, 12, 72),
(29, 1, 65, 53, 12, 72),
(30, 1, 66, 50, 12, 72),
(31, 1, 67, 44, 12, 72),
(32, 1, 67, 45, 12, 72),
(33, 1, 68, 57, 12, 72),
(34, 1, 69, 54, 12, 72),
(35, 1, 70, 51, 12, 72),
(36, 1, 71, 46, 12, 72),
(37, 1, 71, 47, 12, 72),
(38, 1, 72, 58, 4, 72),
(39, 1, 73, 56, 33, 72),
(40, 1, 74, 57, 33, 72),
(41, 1, 75, 58, 33, 72),
(42, 1, 76, 49, 41, 73),
(43, 1, 80, 49, 34, 73),
(44, 1, 77, 45, 39, 73),
(45, 1, 78, 45, 34, 73),
(46, 1, 82, 45, 36, 73),
(47, 1, 79, 47, 34, 73),
(48, 1, 81, 47, 35, 73),
(49, 1, 83, 47, 36, 73),
(52, 1, 86, 57, 52, 71),
(53, 1, 88, 54, 52, 71),
(54, 1, 89, 51, 52, 71),
(57, 1, 90, 46, 52, 71),
(58, 1, 90, 47, 52, 71),
(59, 1, 87, 58, 52, 71),
(60, 1, 91, 48, 52, 71),
(61, 1, 92, 56, 14, 68),
(62, 1, 96, 57, 14, 68),
(63, 1, 93, 53, 14, 68),
(64, 1, 94, 50, 14, 68),
(65, 1, 98, 51, 14, 68),
(66, 1, 95, 44, 14, 68),
(67, 1, 95, 45, 14, 68),
(68, 1, 97, 54, 14, 68),
(69, 1, 99, 46, 14, 68),
(70, 1, 99, 47, 14, 68),
(72, 1, 100, 28, 11, 31),
(73, 1, 100, 29, 11, 31),
(74, 1, 100, 30, 11, 31),
(75, 1, 101, 31, 11, 31),
(76, 1, 101, 32, 11, 31),
(77, 1, 101, 33, 11, 31),
(81, 1, 57, 31, 53, 22),
(82, 1, 57, 33, 53, 22),
(83, 1, 58, 35, 53, 22),
(84, 1, 58, 36, 53, 22),
(85, 1, 43, 3, 53, 62),
(86, 1, 44, 3, 54, 62),
(87, 1, 46, 3, 5, 62),
(88, 1, 45, 3, 9, 62),
(89, 1, 47, 3, 13, 62),
(90, 1, 48, 3, 14, 62),
(91, 1, 49, 3, 52, 62),
(92, 1, 50, 3, 56, 62),
(93, 1, 43, 4, 53, 64),
(94, 1, 44, 4, 54, 64),
(95, 1, 46, 4, 5, 64),
(96, 1, 45, 4, 9, 64),
(97, 1, 47, 4, 13, 64),
(98, 1, 48, 4, 14, 64),
(99, 1, 49, 4, 52, 64),
(100, 1, 50, 4, 56, 64),
(101, 1, 109, 5, 53, 15),
(102, 1, 110, 5, 54, 15),
(103, 1, 111, 5, 9, 15),
(104, 1, 112, 5, 5, 15),
(105, 1, 113, 5, 13, 15),
(106, 1, 114, 5, 14, 15),
(107, 1, 115, 5, 52, 15),
(108, 1, 116, 5, 56, 15),
(109, 1, 109, 6, 53, 49),
(110, 1, 110, 6, 54, 49),
(111, 1, 111, 6, 9, 49),
(112, 1, 112, 6, 5, 49),
(113, 1, 113, 6, 13, 49),
(114, 1, 114, 6, 14, 49),
(115, 1, 115, 6, 52, 49),
(116, 1, 116, 6, 56, 49),
(117, 1, 27, 8, 53, 30),
(118, 1, 28, 8, 54, 30),
(119, 1, 29, 8, 9, 30),
(120, 1, 30, 8, 5, 30),
(121, 1, 31, 8, 13, 30),
(122, 1, 32, 8, 14, 30),
(123, 1, 33, 8, 52, 30),
(124, 1, 34, 8, 56, 30),
(125, 1, 15, 10, 53, 75),
(126, 1, 16, 10, 54, 75),
(127, 1, 17, 10, 9, 75),
(128, 1, 18, 10, 5, 75),
(129, 1, 19, 10, 13, 75),
(130, 1, 20, 10, 14, 75),
(131, 1, 117, 10, 52, 75),
(132, 1, 118, 10, 56, 75),
(140, 1, 1, 13, 53, 92),
(141, 1, 3, 13, 54, 92),
(142, 1, 103, 13, 9, 92),
(143, 1, 104, 13, 5, 92),
(144, 1, 105, 13, 13, 92),
(145, 1, 106, 13, 14, 92),
(146, 1, 107, 13, 52, 92),
(147, 1, 108, 13, 56, 92),
(148, 1, 3, 1, 54, 52),
(149, 1, 103, 1, 9, 52),
(150, 1, 104, 1, 5, 52),
(151, 1, 105, 1, 13, 52),
(152, 1, 106, 1, 14, 52),
(153, 1, 107, 1, 52, 52),
(154, 1, 108, 1, 56, 52),
(155, 1, 15, 9, 53, 57),
(156, 1, 16, 9, 54, 57),
(157, 1, 17, 9, 9, 57),
(158, 1, 18, 9, 5, 57),
(159, 1, 19, 9, 13, 57),
(160, 1, 20, 9, 14, 57),
(161, 1, 117, 9, 52, 57),
(162, 1, 118, 9, 56, 57),
(163, 1, 15, 17, 53, 45),
(164, 1, 16, 17, 54, 45),
(165, 1, 17, 17, 9, 45),
(166, 1, 18, 17, 5, 45),
(167, 1, 19, 17, 13, 45),
(168, 1, 20, 17, 14, 45),
(169, 1, 117, 17, 52, 45),
(170, 1, 118, 17, 56, 45),
(171, 1, 35, 18, 53, 19),
(172, 1, 36, 18, 54, 19),
(173, 1, 37, 18, 9, 19),
(174, 1, 38, 18, 5, 19),
(175, 1, 39, 18, 13, 19),
(176, 1, 40, 18, 14, 19),
(177, 1, 41, 18, 52, 19),
(178, 1, 42, 18, 56, 19),
(179, 1, 27, 7, 53, 39),
(180, 1, 28, 7, 54, 39),
(181, 1, 29, 7, 9, 39),
(182, 1, 30, 7, 5, 39),
(183, 1, 31, 7, 13, 39),
(184, 1, 32, 7, 14, 39),
(185, 1, 33, 7, 52, 39),
(186, 1, 34, 7, 56, 39),
(187, 1, 109, 15, 53, 84),
(188, 1, 110, 15, 54, 84),
(189, 1, 111, 15, 9, 84),
(190, 1, 112, 15, 5, 84),
(191, 1, 113, 15, 13, 84),
(192, 1, 114, 15, 14, 84),
(193, 1, 115, 15, 52, 84),
(194, 1, 116, 15, 56, 84),
(195, 1, 27, 16, 53, 94),
(196, 1, 28, 16, 54, 94),
(197, 1, 29, 16, 9, 94),
(198, 1, 30, 16, 5, 94),
(199, 1, 31, 16, 13, 94),
(200, 1, 32, 16, 14, 94),
(201, 1, 33, 16, 52, 94),
(202, 1, 34, 16, 56, 94),
(204, 1, 35, 12, 53, 25),
(205, 1, 36, 12, 54, 25),
(206, 1, 37, 12, 9, 25),
(207, 1, 38, 12, 5, 25),
(208, 1, 39, 12, 13, 25),
(209, 1, 40, 12, 14, 25),
(210, 1, 42, 12, 56, 25),
(211, 1, 35, 11, 53, 32),
(212, 1, 36, 11, 54, 32),
(213, 1, 37, 11, 9, 32),
(214, 1, 38, 11, 5, 32),
(215, 1, 39, 11, 13, 32),
(216, 1, 40, 11, 14, 32),
(217, 1, 42, 11, 56, 32),
(218, 1, 43, 14, 53, 37),
(219, 1, 44, 14, 54, 37),
(220, 1, 45, 14, 9, 37),
(221, 1, 46, 14, 5, 37),
(222, 1, 47, 14, 13, 37),
(223, 1, 48, 14, 14, 37),
(224, 1, 49, 14, 52, 37),
(225, 1, 50, 14, 56, 37),
(226, 1, 35, 19, 53, 91),
(227, 1, 36, 19, 54, 91),
(228, 1, 37, 19, 9, 91),
(229, 1, 38, 19, 5, 91),
(230, 1, 39, 19, 13, 91),
(231, 1, 40, 19, 14, 91),
(232, 1, 41, 19, 52, 91),
(233, 1, 42, 19, 56, 91),
(234, 1, 14, 28, 12, 22),
(235, 1, 59, 32, 12, 22),
(236, 1, 59, 33, 12, 22),
(237, 1, 142, 28, 7, 42),
(238, 1, 142, 29, 7, 42),
(239, 1, 142, 30, 7, 42),
(240, 1, 155, 32, 7, 42),
(241, 1, 155, 31, 7, 42),
(242, 1, 155, 33, 7, 42),
(243, 1, 167, 34, 7, 42),
(244, 1, 167, 35, 7, 42),
(245, 1, 167, 36, 7, 42),
(247, 1, 156, 31, 8, 42),
(248, 1, 156, 32, 8, 42),
(249, 1, 156, 33, 8, 42),
(250, 1, 168, 34, 8, 42),
(251, 1, 169, 34, 10, 31),
(252, 1, 169, 35, 10, 31),
(253, 1, 169, 36, 10, 31),
(254, 1, 141, 29, 6, 66),
(255, 1, 141, 30, 6, 66),
(256, 1, 154, 32, 6, 66),
(257, 1, 154, 33, 6, 66),
(258, 1, 1, 1, 53, 52),
(259, 1, 140, 28, 54, 55),
(260, 1, 140, 29, 54, 55),
(261, 1, 140, 30, 54, 55),
(262, 1, 141, 28, 6, 55),
(263, 1, 146, 30, 52, 35),
(264, 1, 162, 33, 16, 6),
(265, 1, 161, 31, 52, 35),
(266, 1, 164, 31, 20, 6),
(267, 1, 161, 32, 52, 35),
(268, 1, 164, 32, 20, 6),
(269, 1, 150, 30, 16, 6),
(270, 1, 151, 30, 19, 6),
(271, 1, 161, 33, 52, 35),
(272, 1, 176, 34, 21, 6),
(273, 1, 176, 35, 21, 6),
(274, 1, 176, 36, 21, 6),
(275, 1, 152, 29, 21, 6),
(276, 1, 152, 30, 21, 6),
(278, 1, 170, 34, 52, 35),
(279, 1, 170, 35, 52, 35),
(280, 1, 170, 36, 52, 35),
(281, 1, 128, 41, 13, 82),
(282, 1, 163, 31, 17, 36),
(283, 1, 130, 42, 53, 82),
(284, 1, 163, 32, 17, 36),
(285, 1, 130, 43, 53, 82),
(286, 1, 163, 33, 17, 36),
(287, 1, 56, 41, 54, 82),
(288, 1, 120, 37, 54, 82),
(289, 1, 120, 38, 54, 82),
(290, 1, 174, 34, 17, 36),
(291, 1, 174, 35, 17, 36),
(292, 1, 174, 36, 17, 36),
(293, 1, 208, 48, 37, 12),
(294, 1, 188, 44, 40, 12),
(295, 1, 198, 46, 40, 12),
(296, 1, 149, 28, 55, 23),
(297, 1, 76, 48, 41, 12),
(298, 1, 149, 29, 55, 23),
(299, 1, 149, 30, 55, 23),
(300, 1, 160, 31, 55, 23),
(301, 1, 160, 32, 55, 23),
(302, 1, 102, 58, 10, 12),
(303, 1, 160, 33, 55, 23),
(304, 1, 236, 52, 10, 12),
(305, 1, 77, 44, 39, 12),
(306, 1, 148, 28, 14, 21),
(307, 1, 209, 48, 38, 12),
(308, 1, 148, 29, 14, 21),
(309, 1, 148, 30, 14, 21),
(310, 1, 159, 31, 14, 21),
(311, 1, 81, 46, 35, 12),
(312, 1, 125, 37, 56, 12),
(313, 1, 125, 38, 56, 12),
(314, 1, 144, 28, 10, 18),
(315, 1, 84, 39, 56, 12),
(316, 1, 84, 40, 56, 12),
(317, 1, 144, 29, 10, 18),
(318, 1, 144, 30, 10, 18),
(319, 1, 157, 31, 10, 18),
(320, 1, 157, 32, 10, 18),
(321, 1, 157, 33, 10, 18),
(322, 1, 14, 30, 12, 18),
(323, 1, 145, 28, 53, 14),
(324, 1, 145, 29, 53, 14),
(325, 1, 145, 30, 53, 14),
(326, 1, 286, 48, 40, 12),
(327, 1, 159, 32, 14, 2),
(328, 1, 159, 33, 14, 2),
(329, 1, 171, 34, 14, 2),
(330, 1, 171, 35, 14, 2),
(331, 1, 171, 36, 14, 2),
(332, 1, 146, 28, 52, 81),
(333, 1, 146, 29, 52, 81),
(334, 1, 84, 25, 56, 6),
(335, 1, 172, 34, 55, 80),
(336, 1, 172, 35, 55, 80),
(337, 1, 172, 36, 55, 80),
(338, 1, 84, 41, 56, 12),
(339, 1, 168, 36, 8, 80),
(340, 1, 168, 35, 8, 80),
(341, 1, 143, 28, 8, 80),
(342, 1, 143, 29, 8, 80),
(343, 1, 143, 30, 8, 80),
(344, 1, 175, 36, 18, 65),
(345, 1, 162, 31, 16, 65),
(346, 1, 162, 32, 16, 65),
(347, 1, 173, 36, 15, 65),
(348, 1, 152, 28, 21, 33),
(349, 1, 151, 28, 19, 33),
(350, 1, 151, 29, 19, 33),
(351, 1, 150, 29, 16, 33),
(352, 1, 166, 34, 6, 70),
(353, 1, 150, 28, 16, 33),
(354, 1, 166, 35, 6, 70),
(355, 1, 166, 36, 6, 70),
(356, 1, 175, 34, 18, 33),
(357, 1, 175, 35, 18, 33),
(358, 1, 154, 31, 6, 70),
(359, 1, 153, 31, 54, 70),
(360, 1, 153, 32, 54, 70),
(361, 1, 153, 33, 54, 70),
(362, 1, 165, 34, 54, 70),
(363, 1, 165, 35, 54, 70),
(364, 1, 165, 36, 54, 70),
(365, 1, 164, 33, 20, 29),
(366, 1, 135, 42, 52, 5),
(367, 1, 135, 43, 52, 5),
(368, 1, 173, 34, 15, 29),
(369, 1, 63, 39, 52, 5),
(370, 1, 63, 40, 52, 5),
(371, 1, 173, 35, 15, 29),
(372, 1, 63, 41, 52, 5),
(373, 1, 62, 37, 52, 5),
(374, 1, 200, 48, 6, 74),
(375, 1, 200, 49, 6, 74),
(377, 1, 55, 42, 54, 93),
(378, 1, 55, 43, 54, 93),
(380, 1, 62, 21, 52, 89),
(381, 1, 62, 22, 52, 89),
(382, 1, 84, 23, 56, 73),
(383, 1, 84, 24, 56, 73),
(384, 1, 56, 24, 54, 66),
(385, 1, 56, 25, 54, 66),
(386, 1, 56, 39, 54, 93),
(387, 1, 56, 40, 54, 93),
(388, 1, 56, 23, 54, 66),
(389, 1, 190, 46, 6, 59),
(390, 1, 190, 47, 6, 59),
(392, 1, 178, 44, 54, 59),
(393, 1, 178, 45, 54, 59),
(394, 1, 189, 46, 54, 59),
(395, 1, 189, 47, 54, 59),
(398, 1, 289, 57, 54, 59),
(399, 1, 288, 57, 6, 59),
(400, 1, 290, 57, 53, 54),
(401, 1, 227, 51, 53, 54),
(402, 1, 291, 58, 53, 54),
(403, 1, 276, 55, 53, 54),
(404, 1, 237, 52, 53, 54),
(405, 1, 204, 48, 53, 54),
(406, 1, 204, 49, 53, 54),
(407, 1, 195, 46, 53, 54),
(408, 1, 195, 47, 53, 54),
(409, 1, 55, 26, 54, 55),
(410, 1, 55, 27, 54, 55),
(411, 1, 137, 56, 6, 53),
(412, 1, 249, 53, 6, 53),
(413, 1, 212, 50, 6, 53),
(414, 1, 179, 44, 6, 53),
(415, 1, 179, 45, 6, 53),
(416, 1, 136, 56, 54, 53),
(417, 1, 248, 53, 54, 53),
(418, 1, 211, 50, 54, 53),
(419, 1, 121, 20, 9, 50),
(420, 1, 121, 21, 9, 50),
(421, 1, 121, 22, 9, 50),
(422, 1, 126, 23, 9, 50),
(423, 1, 126, 24, 9, 50),
(424, 1, 126, 25, 9, 50),
(429, 1, 13, 39, 53, 47),
(430, 1, 13, 40, 53, 47),
(431, 1, 13, 41, 53, 47),
(432, 1, 119, 37, 53, 47),
(433, 1, 119, 38, 53, 47),
(434, 1, 292, 58, 8, 27),
(435, 1, 293, 58, 1, 27),
(436, 1, 294, 58, 2, 27),
(437, 1, 274, 55, 8, 27),
(438, 1, 273, 55, 7, 27),
(439, 1, 235, 52, 8, 27),
(440, 1, 234, 52, 7, 27),
(441, 1, 202, 48, 8, 27),
(442, 1, 202, 49, 8, 27),
(443, 1, 132, 42, 5, 27),
(444, 1, 201, 48, 7, 27),
(445, 1, 201, 49, 7, 27),
(446, 1, 295, 58, 7, 27),
(447, 1, 228, 51, 13, 41),
(448, 1, 229, 51, 55, 41),
(449, 1, 240, 52, 55, 41),
(450, 1, 197, 46, 55, 41),
(451, 1, 197, 47, 55, 41),
(452, 1, 196, 47, 13, 41),
(453, 1, 187, 44, 55, 41),
(454, 1, 187, 45, 55, 41),
(455, 1, 268, 54, 55, 41),
(456, 1, 279, 55, 55, 41),
(457, 1, 206, 48, 55, 41),
(458, 1, 206, 49, 55, 41),
(459, 1, 297, 57, 55, 41),
(460, 1, 296, 58, 55, 41),
(461, 1, 254, 53, 53, 34),
(462, 1, 217, 50, 53, 34),
(463, 1, 184, 44, 53, 34),
(464, 1, 184, 45, 53, 34),
(465, 1, 266, 54, 53, 34),
(466, 1, 298, 56, 53, 34),
(467, 1, 120, 20, 54, 23),
(468, 1, 120, 21, 54, 23),
(469, 1, 120, 22, 54, 23),
(471, 1, 299, 57, 8, 63),
(472, 1, 263, 54, 8, 63),
(473, 1, 225, 51, 8, 63),
(474, 1, 192, 46, 8, 63),
(475, 1, 192, 47, 8, 63),
(476, 1, 132, 43, 5, 63),
(477, 1, 262, 54, 7, 63),
(478, 1, 224, 51, 7, 63),
(479, 1, 191, 46, 7, 63),
(480, 1, 191, 47, 7, 63),
(481, 1, 300, 57, 7, 63),
(482, 1, 124, 21, 14, 21),
(483, 1, 124, 22, 14, 21),
(484, 1, 129, 23, 14, 21),
(489, 1, 132, 26, 5, 90),
(490, 1, 132, 27, 5, 90),
(492, 1, 131, 26, 9, 18),
(493, 1, 131, 27, 9, 18),
(494, 1, 85, 26, 56, 18),
(495, 1, 130, 26, 53, 14),
(496, 1, 130, 27, 53, 14),
(497, 1, 85, 27, 56, 14),
(498, 1, 119, 20, 53, 9),
(499, 1, 119, 21, 53, 9),
(500, 1, 119, 22, 53, 9),
(501, 1, 13, 23, 53, 9),
(502, 1, 13, 24, 53, 9),
(503, 1, 255, 53, 52, 7),
(504, 1, 218, 50, 52, 7),
(505, 1, 185, 44, 52, 7),
(506, 1, 185, 45, 52, 7),
(507, 1, 238, 52, 52, 7),
(508, 1, 277, 55, 52, 7),
(509, 1, 301, 56, 52, 7),
(510, 1, 129, 24, 14, 2),
(511, 1, 129, 25, 14, 2),
(512, 1, 134, 26, 14, 2),
(513, 1, 134, 27, 14, 2),
(514, 1, 129, 39, 14, 16),
(515, 1, 129, 40, 14, 16),
(516, 1, 129, 41, 14, 16),
(517, 1, 124, 37, 14, 16),
(518, 1, 124, 38, 14, 16),
(519, 1, 138, 56, 8, 8),
(520, 1, 251, 53, 8, 8),
(521, 1, 214, 50, 8, 8),
(522, 1, 181, 44, 8, 8),
(523, 1, 181, 45, 8, 8),
(524, 1, 123, 38, 13, 8),
(526, 1, 250, 53, 7, 8),
(527, 1, 213, 50, 7, 8),
(528, 1, 180, 44, 7, 8),
(529, 1, 180, 45, 7, 8),
(530, 1, 302, 56, 32, 8),
(531, 1, 303, 57, 32, 8),
(532, 1, 304, 58, 32, 8),
(533, 1, 60, 56, 7, 8),
(534, 1, 41, 12, 52, 25),
(535, 1, 41, 11, 52, 32),
(536, 1, 123, 37, 13, 38),
(537, 1, 131, 42, 9, 38),
(538, 1, 131, 43, 9, 38),
(539, 1, 126, 39, 9, 38),
(540, 1, 126, 40, 9, 38),
(541, 1, 126, 41, 9, 38),
(542, 1, 121, 37, 9, 38),
(543, 1, 121, 38, 9, 38),
(544, 1, 63, 24, 52, 81),
(545, 1, 63, 25, 52, 81),
(546, 1, 135, 26, 52, 81),
(547, 1, 135, 27, 52, 81),
(548, 1, 62, 38, 52, 80),
(549, 1, 232, 51, 49, 24),
(550, 1, 233, 51, 50, 24),
(551, 1, 272, 54, 28, 24),
(552, 1, 269, 54, 23, 24),
(553, 1, 259, 53, 28, 24),
(554, 1, 261, 53, 30, 24),
(555, 1, 260, 53, 29, 24),
(556, 1, 223, 50, 50, 24),
(557, 1, 284, 55, 30, 24),
(558, 1, 244, 52, 46, 24),
(559, 1, 85, 42, 56, 24),
(560, 1, 85, 43, 56, 24),
(561, 1, 127, 23, 5, 46),
(562, 1, 127, 24, 5, 46),
(563, 1, 124, 20, 14, 90),
(565, 1, 122, 20, 5, 90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_calificaciones`
--

CREATE TABLE `sw_escala_calificaciones` (
  `id_escala_calificaciones` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `ec_cualitativa` varchar(64) CHARACTER SET latin1 NOT NULL,
  `ec_cuantitativa` varchar(16) CHARACTER SET latin1 NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(2) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_escala_calificaciones`
--

INSERT INTO `sw_escala_calificaciones` (`id_escala_calificaciones`, `id_periodo_lectivo`, `ec_cualitativa`, `ec_cuantitativa`, `ec_nota_minima`, `ec_nota_maxima`, `ec_orden`, `ec_equivalencia`) VALUES
(1, 1, 'Domina los aprendizajes requeridos', '9.00 - 10.00', 9, 10, 1, 'DA'),
(2, 1, 'Alcanza los aprendizajes requeridos', '7.00 - 8.99', 7, 8.99, 2, 'AA'),
(3, 1, 'Está próximo a alcanzar los aprendizajes requeridos', '4.01 - 6.99', 4.01, 6.99, 3, 'EA'),
(4, 1, 'No alcanza los aprendizajes requeridos', '<= 4', 0, 4, 4, 'NA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_comportamiento`
--

CREATE TABLE `sw_escala_comportamiento` (
  `id_escala_comportamiento` int(11) NOT NULL,
  `ec_relacion` varchar(32) CHARACTER SET latin1 NOT NULL,
  `ec_cualitativa` varchar(164) CHARACTER SET latin1 NOT NULL,
  `ec_cuantitativa` varchar(16) CHARACTER SET latin1 NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_equivalencia` varchar(3) CHARACTER SET latin1 NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_escala_proyectos`
--

CREATE TABLE `sw_escala_proyectos` (
  `id_escala_proyectos` int(11) NOT NULL,
  `ec_cualitativa` varchar(256) CHARACTER SET latin1 NOT NULL,
  `ec_cuantitativa` varchar(16) CHARACTER SET latin1 NOT NULL,
  `ec_nota_minima` float NOT NULL,
  `ec_nota_maxima` float NOT NULL,
  `ec_orden` tinyint(4) NOT NULL,
  `ec_equivalencia` varchar(16) CHARACTER SET latin1 NOT NULL,
  `ec_abreviatura` varchar(2) CHARACTER SET latin1 NOT NULL,
  `ec_correlativa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_especialidad`
--

CREATE TABLE `sw_especialidad` (
  `id_especialidad` int(11) NOT NULL,
  `id_tipo_educacion` int(11) NOT NULL,
  `es_nombre` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `es_figura` varchar(64) CHARACTER SET latin1 NOT NULL,
  `es_abreviatura` varchar(15) CHARACTER SET latin1 NOT NULL,
  `es_orden` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_especialidad`
--

INSERT INTO `sw_especialidad` (`id_especialidad`, `id_tipo_educacion`, `es_nombre`, `es_figura`, `es_abreviatura`, `es_orden`) VALUES
(1, 1, 'EGB ELEMENTAL', 'EGB ELEMENTAL', 'EGB. 2-3-4', 0),
(2, 2, 'EGB MEDIA', 'EGB MEDIA', 'EGB. 5-6-7', 0),
(3, 3, 'EGB SUPERIOR', 'EGB SUPERIOR', 'EGB. 8-9-10', 0),
(4, 4, 'ÁREA TÉCNICA INDUSTRIAL', 'ELECTROMECÁNICA AUTOMOTRIZ ', '', 0),
(5, 4, 'ÁREA TÉCNICA INDUSTRIAL', 'ELECTRÓNICA DE CONSUMO  ', '', 0),
(6, 4, 'ÁREA TÉCNICA INDUSTRIAL', 'INDUSTRIA DE LA CONFECCIÓN  (MODISTERIA y SASTRERIA)', '', 0),
(7, 4, 'ÁREA TÉCNICA INDUSTRIAL', 'INSTALACIONES,EQUIPOS Y MÁQUINAS ELÉCTRICAS ', '', 0),
(8, 5, 'BACHILLERATO GENERAL UNIFICADO', 'BACHILLERATO EN CIENCIAS', 'BGU', 0),
(9, 7, 'INICIAL', 'INICIAL', 'IN', 0),
(10, 6, 'EGB PREPARATORIA', 'EGB PREPARATORIA', 'PREPA', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante`
--

CREATE TABLE `sw_estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `es_nro_matricula` int(11) NOT NULL,
  `es_apellidos` varchar(32) CHARACTER SET latin1 NOT NULL,
  `es_nombres` varchar(32) CHARACTER SET latin1 NOT NULL,
  `es_nombre_completo` varchar(64) CHARACTER SET latin1 NOT NULL,
  `es_cedula` varchar(10) CHARACTER SET latin1 NOT NULL,
  `es_genero` varchar(1) CHARACTER SET latin1 NOT NULL,
  `es_email` varchar(64) CHARACTER SET latin1 NOT NULL,
  `es_sector` varchar(36) CHARACTER SET latin1 NOT NULL,
  `es_direccion` varchar(64) CHARACTER SET latin1 NOT NULL,
  `es_telefono` varchar(32) CHARACTER SET latin1 NOT NULL,
  `es_fec_nacim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_estudiante`
--

INSERT INTO `sw_estudiante` (`id_estudiante`, `es_nro_matricula`, `es_apellidos`, `es_nombres`, `es_nombre_completo`, `es_cedula`, `es_genero`, `es_email`, `es_sector`, `es_direccion`, `es_telefono`, `es_fec_nacim`) VALUES
(2, 0, 'ADRIAN BRIONES', 'ISMAEL FERNANDO', 'ADRIAN BRIONES ISMAEL FERNANDO', '0929984953', 'M', '', '', '', '', '0000-00-00'),
(3, 0, 'ACOSTA HIDALGO', 'EMELY ARIANA', 'ACOSTA HIDALGO EMELY ARIANA', '0941801334', 'F', 'Stefannyhidalgop@gmail', 'Daule coop santiago lopez', 'Jose felix Heredia y by Pass MZ 151 SL 6', '0967340292', '2009-05-20'),
(4, 0, 'AMAGUA LOPEZ', 'ISAAC MATEO', 'AMAGUA LOPEZ ISAAC MATEO', '0957380660', 'M', 'Karylex85@hotmail.com', 'Riberaa Opuestas. Frente Iglesia Señ', 'Riberaa Opuestas. Frente Iglesia Señor de los Milagros', '0990452441', '2009-09-21'),
(5, 0, 'AROCA COLOMA', 'ANALIZ ADAMARY', 'AROCA COLOMA ANALIZ ADAMARY', '0956882054', 'F', 'Melissacoloma4@gmail.com', 'Frente a la iglesia asamblea de Dios', 'Frente a la iglesia asamblea de Dios  parque acuático', '0999105834', '2009-04-06'),
(6, 0, 'PEÑAHERRERA  SEGURA', 'ANDERSON JOEL', 'PEÑAHERRERA  SEGURA ANDERSON JOEL', '0942744197', 'M', '', '', 'KM. 42 VIA A DAULE', '0963747286', '2008-12-12'),
(7, 0, 'ADRIAN OLVERA', 'JANDER JOSUE', 'ADRIAN OLVERA JANDER JOSUE', '0957113749', 'M', 'Olverajanderadrian@gmail.com', 'Lomas de sargentillo', 'Las cañas recinto el Mamey', '0961608489', '2001-07-12'),
(8, 0, 'AVILES ORTEGA', 'NAYELI SCARLETH', 'AVILES ORTEGA NAYELI SCARLETH', '0958245854', 'F', 'anaortegahidalgo@gmail.com', 'DAULE', 'AV. SAN FRANCISCO DAULE', '0961652434', '2007-07-23'),
(9, 0, 'ASPIAZU MENDOZA', 'YORIS EMANUEL', 'ASPIAZU MENDOZA YORIS EMANUEL', '0957373764', 'M', '', 'NOBOL', 'NOBOL VIA LOMAS DE SARGENTILLO KM/40.30', '0962797695', '2009-12-26'),
(10, 0, 'ALVEAR CARRANZA', 'JONATHAN ISRAEL', 'ALVEAR CARRANZA JONATHAN ISRAEL', '0955007026', 'M', 'joinathan.nalvear@gmail.com', 'GUAYAQUIL', 'GUAYAQUIL', '0985428442', '2008-09-29'),
(11, 0, 'Alvarado Delgado', 'Martha', 'Alvarado Delgado Martha', '0940453350', 'F', 'alvaradomartha233@gmail.com', 'Santa Lucía', 'DIAGONAL A LA IGLESIA JEHOVA ES MI GUERRERRO', '0978974780', '2005-02-17'),
(12, 0, 'ANGEL RUIZ', 'ARIANA ANALY', 'ANGEL RUIZ ARIANA ANALY', '0900398049', 'F', 'yruizbarzola@gmail.com', 'EL TRIUNFO', 'DOMINGO COMIN Y 2 DE AGOSTO', '0960053302', '2008-06-06'),
(13, 0, 'ALVARADO MORAN', 'SACARLET ANAHY', 'ALVARADO MORAN SACARLET ANAHY', '0942711631', 'F', '', '', 'RECINTO HUANCHINCHAL', '991534711', '2009-08-18'),
(14, 0, 'ALVARADO BORJA', 'BRITHANY DAYANA', 'ALVARADO BORJA BRITHANY DAYANA', '0958236861', 'F', '', 'DAULE', 'PATRIA NUEVA', '', '2005-03-15'),
(15, 0, 'BAQUERIZO AVILES', 'KAREN SUGEY', 'BAQUERIZO AVILES KAREN SUGEY', '0955106257', 'F', 'ruiz19beatriz@gmail.com', 'PADRE JUAN BAUTISTA', 'GORKY MEDRANDA Y GENERAL RUMIÑAGUI', '0961662106', '2003-10-14'),
(16, 0, 'ALVARADO CHAVEZ', 'ASAHEL JEHOSUA', 'ALVARADO CHAVEZ ASAHEL JEHOSUA', '0958710741', 'M', 'asahelalvarado912@gmail.com', 'DAULE', 'CDLA. SAN FRANCISCO. CALLE. LAS AMERICAS Y PROVINCIA DE AZOGUEZ', '0961657586', '2005-11-25'),
(17, 0, 'ALVARADO TOMALA', 'MAYTTE ELIZABETH', 'ALVARADO TOMALA MAYTTE ELIZABETH', '0957836430', 'F', '', '', '', '968996002', '2008-12-08'),
(18, 0, 'BRIONES CORDOVA', 'LEONARDO JEREMIAS', 'BRIONES CORDOVA LEONARDO JEREMIAS', '0957568363', 'M', 'fatimabriones90@hotmail.com', 'DAULE', 'DIONISIO NAVAS Y BOLIVAR Y SUCRE', '0992065902', '2010-08-17'),
(19, 0, 'BARZOLA DIAZ', 'DAYSI GISELL', 'BARZOLA DIAZ DAYSI GISELL', '0941804999', 'F', 'narcisadiaz515@gmail.com', 'RUMIÑAHUI', 'ESEQUIEL MORA Y JOSE TORRES ESPINOZA', '0962119287', '2008-01-30'),
(20, 0, 'ACOSTA POLANCO', 'SAMUEL MAXIMILIANO', 'ACOSTA POLANCO SAMUEL MAXIMILIANO', '0944030618', 'M', 'acostapolanco2007@gmail.com', 'LA FABIOLA- Daule', 'KM 15 VIA GUAYAQUIL SALITRE', '09989576906', '2006-12-12'),
(21, 0, 'BARZOLA PLUAS', 'ANDY JOSUE', 'BARZOLA PLUAS ANDY JOSUE', '0929986156', 'M', '', 'DAULE', 'SAN FRANCISCO', '', '2005-01-31'),
(22, 0, 'ALVAREZ JUPITER', 'CARLOS LUIS', 'ALVAREZ JUPITER CARLOS LUIS', '0959444944', 'M', '', 'DAULE', 'CDLA JUAN BAUTISTA AGUIRRE OSMAEL PEREZ PAZMIÑO Y LEONIDAS LA TO', '0969298923', '2007-11-20'),
(23, 0, 'AGUILERA VILLAMAR', 'LUIS FERNANDO', 'AGUILERA VILLAMAR LUIS FERNANDO', '0955897558', 'M', 'luisvillamar37@gmail.com', 'Guayaquil', 'Puente Lucia Km 26 via a Daule', '0968230244', '2004-03-15'),
(24, 0, 'ALCIVAR CASTRO', 'JOSUE ARCEL', 'ALCIVAR CASTRO JOSUE ARCEL', '0940223886', 'M', 'arcelalcivar6@gmail.com', 'Guayaquil', 'Puente Lucia Eugenio Espejo/Mz12 SL7A', '0992090421', '2004-05-08'),
(25, 0, 'BAJAÑA SANCHEZ', 'SABRINA SCARLETTE', 'BAJAÑA SANCHEZ SABRINA SCARLETTE', '0958706848', 'F', 'isabel-sanchemoreno@hotmail.com', 'EL MATE', 'EL MATE', '0980381868', '2007-02-11'),
(26, 0, 'BONILLA ANZULES', 'WILLIAM MOISES', 'BONILLA ANZULES WILLIAM MOISES', '', 'M', '', '', '', '', '0000-00-00'),
(27, 0, 'LEON ANZULES', 'BRAYAN ANDRES', 'LEON ANZULES BRAYAN ANDRES', '0958549909', 'M', 'leanbran3916497@estudiantes3.edu.ec', 'DAULE', 'CDADELA EL RECUERDO', '0939841283', '2004-05-18'),
(28, 0, 'ALVARADO MORAN', 'ESTRELLA NATIVIDAD', 'ALVARADO MORAN ESTRELLA NATIVIDAD', '0942711656', 'F', 'estrella.alvarado@gmail.com', 'HUANCHICHAL', 'HUANCHICHAL', '0982960137', '2007-12-25'),
(29, 0, 'BRIONES ANCHUNDIA', 'RICARDO ISRAEL', 'BRIONES ANCHUNDIA RICARDO ISRAEL', '0942304049', 'M', '', 'DAULE', 'MARIANITA 4 CERCA DE BAR DE LOS CAICEDO', '0985890549', '2004-03-14'),
(30, 0, 'LOY MERA', 'JOFFRE ALEXANDER', 'LOY MERA JOFFRE ALEXANDER', '0952785160', 'M', 'bscjoffrealex@gmail.com', 'COOPERATIVA NUEVA VIICTORIA', 'KM 26 VIA A DAULE', '0997951612', '2008-07-14'),
(31, 0, 'CASTR INTRIAGO', 'ANGIE MAIR', 'CASTR INTRIAGO ANGIE MAIR', '1314682772', 'F', '', 'DAULE', 'FRENTE AL COLEGIO RIBERAS DEL DAULE', '0996404839', '2005-03-01'),
(32, 0, 'RUIZ MORAN', 'GABRIELA JAZMIN', 'RUIZ MORAN GABRIELA JAZMIN', '092987097', 'F', 'kari123@gmail.com', 'LOS DAULIS', 'DOMINGO COMIN', '0985428442', '2005-11-20'),
(33, 0, 'CASTRO VILLEGAS', 'LISSETH YALITH', 'CASTRO VILLEGAS LISSETH YALITH', '0929931681', 'F', '', 'SANTA LUCIA', 'FATIMA', '', '2004-08-24'),
(34, 0, 'MOROCHO ESPINOZA', 'SHIRLEY DANIELA', 'MOROCHO ESPINOZA SHIRLEY DANIELA', '0941803371', 'F', 'tatiespinoza1984@gmail.com', 'BELEN', '26 DE NOVIEMBRE', '0959568323', '2007-03-10'),
(35, 0, 'San Lucas Melani', 'Agila Cristina', 'San Lucas Melani Agila Cristina', '0956972129', 'F', '', '', 'Cdla. El Triunfo calle Magro y Homero Espinoza', '0979728529', '2010-02-14'),
(36, 0, 'CHAVEZ CAICEDO', 'KRISTEL NAOMI', 'CHAVEZ CAICEDO KRISTEL NAOMI', '0956804058', 'F', '', 'DAULE', 'FRANCISCO VALVERDE Y ANTONIO HUAYAMABE', '', '2004-06-08'),
(37, 0, 'MAGALLANES TOMALA', 'HERLEN JOSUE', 'MAGALLANES TOMALA HERLEN JOSUE', '0943056275', 'M', 'herlen.magallanes@gmail.com', 'LAUREL', 'LAS PIÑAS DE ARRIBA', '0968055642', '2008-10-24'),
(38, 0, 'BRIONES QUINTO', 'KARLA NICOLLE', 'BRIONES QUINTO KARLA NICOLLE', '0942014184', 'F', 'lucrediamante@hotmail.com', 'SAN GABRIEL', 'SAN GABRIEL CERCA DEL PUENTE SAN GABRIEL', '0968626522', '2007-12-15'),
(39, 0, 'MAGALLANES JURADO', 'ANAIS DAYANARA', 'MAGALLANES JURADO ANAIS DAYANARA', '0943029025', 'F', 'anais.magallanes@gmail.com', 'LAUREL', 'RECINTO LAS PIÑAS DE ARRIBA', '0998246625', '2009-08-14'),
(40, 0, 'CHOEZ MAGALLANES', 'EVELYN NARCISA', 'CHOEZ MAGALLANES EVELYN NARCISA', '0943135749', 'F', '', 'LOMAS DE SARGENTILLO', 'LOMAS CALLE B Y VIRGEN DEL MONSERRATE MZ 17 SL 7', '', '2004-07-13'),
(41, 0, 'BANCHON GOMEZ', 'JANDRY JESUS', 'BANCHON GOMEZ JANDRY JESUS', '0955889654', 'M', 'jandrybanchon@gmail.com', 'RECINTO CHONANA VIEJA', 'SANTA LUCIA', '0960053302', '2008-12-24'),
(42, 0, 'Nicolas Obregón', 'Alvarado Flavio', 'Nicolas Obregón Alvarado Flavio', '0958094120', 'M', '', 'Recinto San Gabriel por el Rio', 'Desvio de las Cañas Magro', '0989810284', '2010-03-29'),
(43, 0, 'PLUAS SANTANA', 'JUAN SEBASTIAN', 'PLUAS SANTANA JUAN SEBASTIAN', '0957532096', 'M', 'juansantanam78@gmail.com', 'SINDICATO DE CHOFERES DAULE', 'MALECON E ISIDRO AYORA', '0939100154', '2008-01-22'),
(44, 0, 'FALCONES VILLAMAR', 'ERICK ANEL', 'FALCONES VILLAMAR ERICK ANEL', '0957130768', 'M', '', 'DAULE', 'TEOFILO CAICEDO QUITO', '0985941209', '2005-05-05'),
(45, 0, 'TOMALA RUIZ', 'MILDRED DAMARIS', 'TOMALA RUIZ MILDRED DAMARIS', '0940224322', 'F', 'tomalaruizdamaris@gmail.com', 'MULTICENTRO', 'GALO PLAZA LAZO', '0961570848', '2008-07-24'),
(46, 0, 'CALLE BRAVO', 'DEIVIT ALBERTO', 'CALLE BRAVO DEIVIT ALBERTO', '1250011978', 'M', 'gloriapetrabravoronquillo@gmail.com', 'EL MATE', 'EL MATE AL LADO IGLESIA FILADELFIA', '0981019062', '2006-04-29'),
(47, 0, 'HOLGUIN TUTIVEN', 'THAINA JAMILETH', 'HOLGUIN TUTIVEN THAINA JAMILETH', '0941805616', 'F', 'jamilethholguin86@gmail.com', 'ASSAD BUCARAM', 'CALLEJON 14 Y JOSE NAVAS', '0990620130', '2007-05-27'),
(48, 0, 'FERRUZOLA TORRES', 'VALERIA ADRIANA', 'FERRUZOLA TORRES VALERIA ADRIANA', '0940754377', 'F', '', 'DAULE', 'RECINTO FLOR DE MARÍA', '', '2005-05-07'),
(49, 0, 'HARO CHAVEZ', 'HEIDY PATRICIA', 'HARO CHAVEZ HEIDY PATRICIA', '0943311746', 'F', '', 'DAULE', 'PEDRO CHAMBERS Y OTTO CARBO', '', '2005-04-08'),
(50, 0, 'MORALES UBILLA', 'JACKSON JOEL', 'MORALES UBILLA JACKSON JOEL', '0957587470', 'M', 'jacksonmolrales@gmail.com', 'DAULE', 'DAULE', '0996678589', '2008-12-27'),
(51, 0, 'BOADA SANTILLAN', 'WILLIAM', 'BOADA SANTILLAN WILLIAM', '0955549159', 'M', 'lilasantillan54@gmail.com', 'DAULE', 'SAN FRANCISCO', '091924282', '2008-05-31'),
(52, 0, 'HIDALGO ROMERO', 'EMELY LUCIA', 'HIDALGO ROMERO EMELY LUCIA', '0941852766', 'F', '', 'DAULE', 'BOQUERON', '', '2006-01-11'),
(53, 0, 'CAMBA MACIAS', 'JORDAN VLADIMIR', 'CAMBA MACIAS JORDAN VLADIMIR', '0958068850', 'M', 'cambamaciasjordanvladimir@gmail.com', 'DAULE', 'EL RECUERDO, VICTOR MANUEL RENDON CERCA DE ECUADOR AMAZONICO', '0991467424', '2007-07-04'),
(54, 0, 'LEON CHAVEZ', 'MARIA CLARA', 'LEON CHAVEZ MARIA CLARA', '0958243107', 'F', '', 'DAULE', 'JOSE GOMEZ', '', '2002-08-16'),
(55, 0, 'LEON CHAVEZ', 'MIRIAN ROCIO', 'LEON CHAVEZ MIRIAN ROCIO', '0958242901', 'F', '', 'DAULE', 'JOSE GOMEZ', '', '2004-04-14'),
(56, 0, 'LEON RUIZ', 'SANDY JUDELLY', 'LEON RUIZ SANDY JUDELLY', '0958631111', 'F', '', 'DAULE', 'PEDRO PANTALEON LEONEL TORRES', '', '2004-01-24'),
(57, 0, 'MERCHAN RAVELO', 'CLARIS MADELINE', 'MERCHAN RAVELO CLARIS MADELINE', '0941747842', 'F', 'eliza87rav@hotmail.com', 'MARIANITA 5 CAYETANO', 'PICHINCHA Y CORONEL', '0978787280', '2008-10-24'),
(58, 0, 'LUNA RODRIGUEZ', 'ELVIS ISAIAS', 'LUNA RODRIGUEZ ELVIS ISAIAS', '0929185833', 'M', '', 'DAULE', 'DAULE', '', '2004-05-30'),
(59, 0, 'MEDINA SESME', 'JORGE LEONEL', 'MEDINA SESME JORGE LEONEL', '0940756075', 'M', '', 'DAULE', 'SANTA ROSA', '', '2005-06-08'),
(60, 0, 'MENDEZ DUQUE', 'RUTH MARIA', 'MENDEZ DUQUE RUTH MARIA', '0943032581', 'F', '', 'DAULE', 'BRISAS DEL DAULE', '', '2005-09-08'),
(61, 0, 'MINDIOLAZA DUMES', 'ANDERSON ANDRES', 'MINDIOLAZA DUMES ANDERSON ANDRES', '0942169434', 'M', '', 'DAULE', 'LAUREL', '', '2004-11-12'),
(62, 0, 'MAZAMBA ROMERO', 'JONATHAN ALEXANDER', 'MAZAMBA ROMERO JONATHAN ALEXANDER', '0956190946', 'M', 'imazamba89@gmail.com', 'MAGRO', 'ATRAS DE LA ESCUELA ISMAEL PEREZ CASTRO', '0986356869', '2007-11-04'),
(63, 0, 'DUQUE LEON', 'EDU STALIN', 'DUQUE LEON EDU STALIN', '0941800270', 'M', 'eduduque@gmail.com', 'PENINSULA DE ANIMAS', 'VIA SANTA LUCIA', '0969278008', '2007-12-20'),
(64, 0, 'MOREIRA CALDERON', 'KEVIN ANDRES', 'MOREIRA CALDERON KEVIN ANDRES', '1317754990', 'M', 'moreirakevin557@gmail.com', 'BANIFE', 'PEDRO IASAIAS PRIMERA ETAPA', '0939588619', '2009-03-12'),
(65, 0, 'MORALES UBILLA', 'GRISMANEL JAMILETH', 'MORALES UBILLA GRISMANEL JAMILETH', '0957587652', 'F', '', 'DAULE', 'RIBERAS OPUESTAS', '', '2005-03-25'),
(66, 0, 'NAVARRETE ROMERO', 'ANGEL RICARDO', 'NAVARRETE ROMERO ANGEL RICARDO', '0941800690', 'M', '', 'DAULE', 'AV.SAN FRANCISCO', '', '2006-01-12'),
(67, 0, 'PANCHANA MERA', 'AURORA ROMINA', 'PANCHANA MERA AURORA ROMINA', '0942562356', 'F', '', 'DAULE', 'CALLE QUITO', '', '2003-05-28'),
(68, 0, 'SALAS LEON', 'MEIBI DEYANEIDA', 'SALAS LEON MEIBI DEYANEIDA', '0957136336', 'F', '', 'DAULE', 'BOLIVAR ENTRE CORDEROS', '', '2005-03-04'),
(69, 0, 'SOLIS GAVILANEZ', 'LUGGI XAVIER', 'SOLIS GAVILANEZ LUGGI XAVIER', '0958593238', 'M', '', 'DAULE', 'OLMEDO ALMEIDA Y ABDON CALDRON', '', '2005-01-03'),
(70, 0, 'TUTIVEN FUENTES', 'THAIZ MIRELY', 'TUTIVEN FUENTES THAIZ MIRELY', '0941802514', 'F', '', 'DAULE', 'BOLIVAR Y PIEDRAHITA', '', '2006-08-02'),
(71, 0, 'AVILEZ PIGUAVE', 'JOSE MOISES', 'AVILEZ PIGUAVE JOSE MOISES', '0955685441', 'M', '', 'DAULE', 'COOP. NARCISA DE JESUS', '0983714980', '2005-02-01'),
(72, 0, 'Ruíz Pihuave', 'Génesis yrsley', 'Ruíz Pihuave Génesis yrsley', '0956376289', 'F', 'genesisypi2006@gmail.com', 'Daule', 'Av. San Francisco y República del Ecuador', '0960908018', '2006-01-02'),
(73, 0, 'ALVARADO RIVAS', 'JOSE ANDRES', 'ALVARADO RIVAS JOSE ANDRES', '0941803629', 'M', '', 'CANTON DAULE', 'CIUDADELA EL RECUERDO', '', '2021-08-05'),
(74, 0, 'CABRERA ANA', 'ELVIS ALEXIS', 'CABRERA ANA ELVIS ALEXIS', '941745457', 'M', 'mariuxgaracellyanajimenez@gmail.com', 'DAULE', 'Vía cascol de bahona', '991836807', '2009-10-25'),
(75, 0, 'AVELLAN AROCA', 'ALANIS ISRIEL', 'AVELLAN AROCA ALANIS ISRIEL', '0958164600', 'F', '', 'DAULE', 'RECUERDO', '', '2002-11-14'),
(76, 0, 'CANDELARIO LOZANO', 'LISBETH ESTEFANIA', 'CANDELARIO LOZANO LISBETH ESTEFANIA', '958592727', 'F', '', 'DAULE', 'EL RECUERDO', '967920648', '2009-07-07'),
(77, 0, 'CARDENAS MORAN', 'KERLY MARIA', 'CARDENAS MORAN KERLY MARIA', '0943509976', 'F', '', 'EL TAPE', 'PARROQUIA LOS LOJAS', '963380092', '2006-05-16'),
(78, 0, 'CHIRIGUAYA ALVEAR', 'ANDY DANIEL', 'CHIRIGUAYA ALVEAR ANDY DANIEL', '0929984920', 'M', '', 'ARENAL', 'KM 42 VIA A DAULE', '0991130886', '2007-08-08'),
(79, 0, 'COLOMA MAGALLANES', 'MATIAS ISAIAS', 'COLOMA MAGALLANES MATIAS ISAIAS', '0956810576', 'M', '', 'LA RINCONADA', 'RCTO LA RINCONADA VIA A DAULE POR LA TOMA DE AGUA', '0991957438', '2006-08-11'),
(80, 0, 'JAIME MENDEZ', 'NAOMI NICOLE', 'JAIME MENDEZ NAOMI NICOLE', '0957614589', 'F', 'naominicolejaimemendez@gmail.com', 'DAULE', 'Av. San Francisco e Isidro faJardo', '0939120893', '2007-07-31'),
(81, 0, 'CASTRO RIVAS', 'ZAYLA NARCISA', 'CASTRO RIVAS ZAYLA NARCISA', '0941612830', 'F', 'zaylacr2007@gmail.com', 'Frente a la lavadora expres', 'DAULE', '0994352332', '2007-09-13'),
(82, 0, 'ALVEAR MORAN', 'FABRICIO JAIR', 'ALVEAR MORAN FABRICIO JAIR', '927972026', 'M', 'alvearf850@gmail.com', 'daule ---yolita', 'JUAN MIGUEL TRIVIÑO Y JUSTO TORRES.', '904922481', '2005-05-20'),
(83, 0, 'MORAN AZPIAZU', 'JORGE JAFET', 'MORAN AZPIAZU JORGE JAFET', '0958098907', 'M', 'dianaaspiazu3@golmail.com', 'DAULE', 'Leopoldo benite  y leónidas García', '0993669500', '2007-10-12'),
(84, 0, 'CEVALLOS CUSME', 'CRISTHOPER ERNESTO', 'CEVALLOS CUSME CRISTHOPER ERNESTO', '0951143189', 'M', 'cusmeedita84@gmail.com', 'NOBOL', 'PETRILLO', '0969846123', '2007-08-16'),
(85, 0, 'AMPUERO  CORDOVA', 'JULIO CESAR', 'AMPUERO  CORDOVA JULIO CESAR', '958684748', 'M', 'domecaicepinela@gmail.com', 'yolita', 'calle narcisa de jesus gracia moreno', '985521311', '2004-02-08'),
(86, 0, 'ANCHUNDIA BAQUE', 'JEFFERSON JESUA', 'ANCHUNDIA BAQUE JEFFERSON JESUA', '0944357276', 'M', 'jeffersoanchundi2015@gmail.com', 'daule', 'carlos julio arosemena', '985982739', '2004-05-06'),
(87, 0, 'RUIZ LAVAYEN', 'GLENN PATRICK', 'RUIZ LAVAYEN GLENN PATRICK', '0940223423', 'M', 'ruizlavayeng@gmail.com', 'DAULE', 'Manuel Villavicieno y Pablo Annibal  Velez', '0991982985', '2007-03-10'),
(88, 0, 'COX AREVALO', 'DANNY JAVIER', 'COX AREVALO DANNY JAVIER', '0957594047', 'M', 'danielcoxco90@gmail.com', 'RCTO. LOS KIOSKOS', 'NOBOL', '0968971850', '2006-10-23'),
(89, 0, 'FRANCO SALAVARRIA', 'EDWIN JOEL', 'FRANCO SALAVARRIA EDWIN JOEL', '0959022484', 'M', 'franjoel0506@gmail.com', 'LAUREL RCTO. LOS ANGELES', 'LAUREL- DAULE', '0994044864', '2006-10-05'),
(90, 0, 'MIRANDA ALVAREZ', 'ANDREA SILVANA', 'MIRANDA ALVAREZ ANDREA SILVANA', '0957533235', 'F', 'silvaalvarez2007@Gmail.Com', 'DAULE- JUAN BAUTISTA AGUIRRE', 'José Vélez Callejón Babahoyo', '0959211169', '2007-10-07'),
(91, 0, 'SALAZAR CASTRO', 'ELKIN LEONARDO', 'SALAZAR CASTRO ELKIN LEONARDO', '0957493935', 'M', 'elkinsalazar0722@hotmail.com', 'DAULE', 'AL LADO DEL CEMENTERIO', '0991704033', '2006-10-19'),
(92, 0, 'MORALES ZAMBRANO', 'JOEL ALFREDO', 'MORALES ZAMBRANO JOEL ALFREDO', '0956721914', 'M', 'joelmorales12345677@gmail.com', 'Ciudadela San Josè', 'DAULE', '0969327909', '2004-11-28'),
(93, 0, 'ALVARADO PLUAS', 'JORDY DANILO', 'ALVARADO PLUAS JORDY DANILO', '0956895544', 'M', '', 'Palestina', 'Recinto Coloradal', '0999142108', '2002-12-11'),
(94, 0, 'POTE RAMIREZ', 'DERIAN ALEXANDER', 'POTE RAMIREZ DERIAN ALEXANDER', '0956238018', 'M', 'derianalexanderpoteramirez@gmail.com', '', 'Cooperativa los Àngeles km 26 (la toma)', '0989294980', '2005-01-29'),
(95, 0, 'NAVARRETE MURRIETA', 'WELLINGTON DAVID', 'NAVARRETE MURRIETA WELLINGTON DAVID', '0950701631', 'M', 'wellingtonnavarrete27@gmail.com', 'Marianita 4', 'DAULE', '0960926177', '2005-02-17'),
(96, 0, 'BAJAÑA CORTEZ', 'STEVEN GENARO', 'BAJAÑA CORTEZ STEVEN GENARO', '0942276197', 'M', 'stevenbajana65@gmail.com', 'Guayaquil', 'Las Mercedes Km 24 via Guayaquil - Daule', '0993975010', '2001-05-28'),
(97, 0, 'BARZOLA SILVA', 'EDUARDO ALEJANDRO', 'BARZOLA SILVA EDUARDO ALEJANDRO', '0957035603', 'M', 'alejandro18barzola@gmail.com', 'Nobol', 'Recinto los Canales via a Lomas de Sargentillo', '0998561290', '2003-09-14'),
(98, 0, 'BARZOLA SILVA', 'EDUARDO WLADIMIR', 'BARZOLA SILVA EDUARDO WLADIMIR', '0942627084', 'M', 'wladibarzola19@gmail.com', 'Nobol', 'Recintolos Canales via a Lomas de Sargentillo por la Garza Roja', '0980688624', '2003-09-14'),
(99, 0, 'BURGOS BORBOR', 'BELIN BYRON', 'BURGOS BORBOR BELIN BYRON', '0940452238', 'M', '', 'Lomas de Sargentillo', 'Recintopuerto las cañas', '0994798635', '2003-03-14'),
(100, 0, 'CAICEDO ADRIAN', 'ANNER MOISES', 'CAICEDO ADRIAN ANNER MOISES', '0956992986', 'M', '', 'Daule', '25 De Diciembre e Isidoro Carchi', '0999501948', '2003-01-23'),
(101, 0, 'MARTINEZ VAQUE', 'ELKIN DANIEL', 'MARTINEZ VAQUE ELKIN DANIEL', '0940229743', 'M', '', 'Los pozos', 'DAULE', '0993803260', '2004-04-05'),
(102, 0, 'CARPIO VILLAMAR', 'GILMAR DAMIAN', 'CARPIO VILLAMAR GILMAR DAMIAN', '0929931509', 'M', '', 'Santa Lucia', 'Bermejo de Abajo via al porvenir', '0994820497', '2004-01-06'),
(103, 0, 'MERCHAN VARGAS', 'DANIEL EDUARDO', 'MERCHAN VARGAS DANIEL EDUARDO', '0957114515', 'M', 'Merchand341@gmail.com', 'jose velez y Domingo comin', 'DAULE', '0961437059', '2005-06-12'),
(104, 0, 'MORALES BUENO', 'GILMAR ALEJANDRO', 'MORALES BUENO GILMAR ALEJANDRO', '0943038265', 'M', '', 'ARENAL', 'DAULE', '0997363421', '2005-05-07'),
(105, 0, 'ALVARADO VALVERDE', 'LEONARDO FRANCISCO', 'ALVARADO VALVERDE LEONARDO FRANCISCO', '0942302852', 'M', '', '', 'RECINTO HUANCHINCHAL', '99714684', '2008-10-05'),
(106, 0, 'MOREY VEGA', 'BYRON GREGORIO', 'MOREY VEGA BYRON GREGORIO', '0957963275', 'M', 'andreamorey9516@gmail.com', 'Recinto la Independencia', 'DAULE', '0982073728', '2005-10-04'),
(107, 0, 'ANCHUNDIA CORTEZ', 'MIGUEL ANGEL', 'ANCHUNDIA CORTEZ MIGUEL ANGEL', '0959436734', 'M', '', '', 'RECINTO LOS POZOS', '988062031', '2008-12-14'),
(108, 0, 'AVELINO QUIÑONEZ', 'GENESIS DANIELA', 'AVELINO QUIÑONEZ GENESIS DANIELA', '0959418690', 'F', '', '', 'CDLA. JUAN BAUTISTA AGUIRRE', '967548676', '2009-06-03'),
(109, 0, 'BAJAÑA SANCHEZ', 'ANGELICA VALERIA', 'BAJAÑA SANCHEZ ANGELICA VALERIA', '0957986342', 'F', '', '', '9 DE OCTUBRE Y BY P', '0969208376', '2008-10-23'),
(110, 0, 'BAQUE CABRERA', 'LUIS ALFREDO', 'BAQUE CABRERA LUIS ALFREDO', '0956996102', 'M', '', '', 'JOSE FELIX MOREIRA Y MARIA MAIGON', '969167223', '2008-12-11'),
(111, 0, 'BARZOLA ESQUIVEL', 'SANTIAGO JAYCO', 'BARZOLA ESQUIVEL SANTIAGO JAYCO', '0956805360', 'M', '', '', 'JOSE VELIZ Y COLON', '984713058', '2008-05-08'),
(112, 0, 'BARZOLA MONCADA', 'ALESSANDRO DAVID', 'BARZOLA MONCADA ALESSANDRO DAVID', '0942727009', 'M', '', '', 'GUARUMAL', '994907391', '2009-07-05'),
(113, 0, 'PEÑAFIEL BONILLA', 'ANTHONY DAVID', 'PEÑAFIEL BONILLA ANTHONY DAVID', '0953414034', 'M', 'anthonypenafiel290@gmail.com', 'Mz#1345, Sol#4, cop San Francisco 2 ', 'PASCUALES', '0989429453', '2005-09-02'),
(114, 0, 'PLUAS QUIJIJE', 'MIGUEL ANGEL', 'PLUAS QUIJIJE MIGUEL ANGEL', '0928202290', 'M', '', 'Sector San isidro y eugenio espejo', 'NOBOL', '0969153695', '2005-03-11'),
(115, 0, 'ACOSTA NAVARRETE', 'JOSE MIGUEL', 'ACOSTA NAVARRETE JOSE MIGUEL', '0959018771', 'M', '', 'Calle Domingo Comin y Gonzalo Zambuu', 'Calle Domingo Comin y Gonzalo Zambuunbide	', '0981323036', '2014-04-04'),
(116, 0, 'ARREAGA MORALES', 'JORDAN JOXEL', 'ARREAGA MORALES JORDAN JOXEL', '0928203165', 'M', 'joxelarreaga@gmail.com', 'DAULE', 'CARBO CONCEPCION', '991343230', '2005-10-11'),
(117, 0, 'ALVARADO REYES', 'HECTOR ABRAHAM', 'ALVARADO REYES HECTOR ABRAHAM', '0958806671', 'M', 'alvaradoabraham8006@gmail.com', 'JUAN BAUTISTA AGUIRRE', 'DAULE-OLMEDO Y JOAQUIN GALLEGOS LARA MZ 5', '0981388006', '2005-04-15'),
(118, 0, 'BANGUERA RIVERA', 'TEOFILO ARMANDO', 'BANGUERA RIVERA TEOFILO ARMANDO', '0803683606', 'M', 'teoarmando2021@gmail.com', 'salitre', 'nuevo salitre', '959731465', '2005-06-20'),
(119, 0, 'PLUAS ZAMORA', 'JIMMY ANDRES', 'PLUAS ZAMORA JIMMY ANDRES', '0943808709', 'M', 'zamorangela069@gmail.com', 'PUENTE LUCÌA', 'KM 26 VIA A DAULE COOP.NUEVA VICTORIA', '0969857559 - 0939011143', '2004-07-01'),
(120, 0, 'BAJAÑA SANCHEZ', 'LILIBETH ANDREINA', 'BAJAÑA SANCHEZ LILIBETH ANDREINA', '0958706574', 'F', 'isabel-sanche-moreno@hotmail.com', 'DAULE', '9 DE OCTUBRE ENTRE LA B Y P', '0980381868', '2005-07-19'),
(121, 0, 'ROMERO RAMOS', 'JUAN CARLOS', 'ROMERO RAMOS JUAN CARLOS', '0954919262', 'M', '', 'KM 26 VÌA A DAULE', 'LA TOMA KM 26', '0989672571', '2004-09-24'),
(122, 0, 'BAYONA ORTEGA', 'RONALD ALEXI', 'BAYONA ORTEGA RONALD ALEXI', '0958554206', 'M', 'alexis3d3djeurk7@gmail.com', 'daule', 'riveras opuestas', '991562997', '2005-01-31'),
(123, 0, 'BRAN BAJAÑA', 'BYRON VICENTE', 'BRAN BAJAÑA BYRON VICENTE', '0929938728', 'M', 'branbajaña@gmail.com', 'santa lucia', 'cabuyal', '961769327', '2004-06-16'),
(124, 0, 'ROMERO ROMERO', 'CRISTHIAN ALEXANDER', 'ROMERO ROMERO CRISTHIAN ALEXANDER', '0941461428', 'M', 'ar4008475@gmail.com', 'LOS LOJAS', 'DAULE', '0938655265', '2003-04-19'),
(125, 0, 'BRIONES ALVARADO', 'NERY SAMUEL', 'BRIONES ALVARADO NERY SAMUEL', '0942690371', 'M', 'samuelbrionesalvarado200502@gmail.com', 'DAULE', 'miguel hurtado y cesar bahamonde   patria nueva', '9926262962', '2005-11-02'),
(126, 0, 'RONQUILLO ROMERO', 'FELIPE ISRAEL', 'RONQUILLO ROMERO FELIPE ISRAEL', '0941401309', 'M', 'Quinde538@gmail.com', 'PUENTE LUCÌA', 'Km 27 Via a Daule', '0961371840', '2005-01-17'),
(127, 0, 'CABRERA GARCIA', 'JONATHAN JOSE', 'CABRERA GARCIA JONATHAN JOSE', '941746554', 'M', 'jose2005cgarcia@gmail.com', 'nobol', 'narcisa de jesus', '968906775', '2005-11-19'),
(128, 0, 'CERCADO ALVARADO', 'DARLING OMAR', 'CERCADO ALVARADO DARLING OMAR', '0927678904', 'M', 'darlingomar25@gmail.com', 'DAULE', 'huanchichal', '994270302', '2004-11-25'),
(129, 0, 'RONQUILLO VELASCO', 'RODRIGO JAMIL', 'RONQUILLO VELASCO RODRIGO JAMIL', '0955797543', 'M', 'rodrigoronquillo001@gmail.com', 'DOS BOCAS', 'DAULE - LOS LOJAS', '0990728918', '2005-01-12'),
(130, 0, 'CERVANTES MAYA', 'JENNIFER ARIANA', 'CERVANTES MAYA JENNIFER ARIANA', '0956332415', 'F', 'jenniffercervantes66@gmail.com', 'pascuales', 'ASAAD BUCARAM', '960239405', '2004-08-19'),
(131, 0, 'CASTAÑEDA POZO', 'ALBERTO DAVID', 'CASTAÑEDA POZO ALBERTO DAVID', '0942689928', 'M', 'albertocastanedapozo@gmail.com', 'DAULE', 'PARROQUIA  LIMONAL- RCTO JESUS MARIA', '0985452967', '2004-04-24'),
(132, 0, 'RUIZ CASTRO', 'JUSTIN MAXIMILIANO', 'RUIZ CASTRO JUSTIN MAXIMILIANO', '0927678086', 'M', 'ruizcastrojustin@gmail.com', 'HUANCHICHAL', 'Km 6½ vía Magro -Piñal', '0960959628', '2005-03-08'),
(133, 0, 'CHERRES RENGIFO', 'JUAN DANIEL', 'CHERRES RENGIFO JUAN DANIEL', '956967418', 'M', 'jorgesaulreyesrengifo@gmail.com', 'PALESTINA', 'COLORADAL', '995167218', '2005-08-10'),
(134, 0, 'FRANCO PEÑAFIEL', 'JUAN IGNACIO', 'FRANCO PEÑAFIEL JUAN IGNACIO', '0953019809', 'M', 'juanignaciofrancopenafiel@gmail.com', 'DAULE', 'RCTO CASCOL DE BAHONA', '985659352', '2005-06-24'),
(135, 0, 'GILER CASTRO', 'DANIEL ALBERTO', 'GILER CASTRO DANIEL ALBERTO', '0959339508', 'M', 'danielgcastro@gmail.com', 'LIMONAL', 'AV. MIRAFLORES CALLE PRINCIPAL', '0960134976', '2005-01-04'),
(136, 0, 'SALAS SALAS', 'ERLYN ADRIAN', 'SALAS SALAS ERLYN ADRIAN', '0929982551', 'M', 'adrianjosue132708@gmail.com', 'DAULE', 'domingo comin y baypas', '0986360478', '2006-05-19'),
(137, 0, 'NARANJO TORRES', 'EMELY DENISSE', 'NARANJO TORRES EMELY DENISSE', '0929996957', 'F', 'Emelynaranjo04@outlook.com', 'DAULE-BANIFE', 'MARIETA VEINTIMILLA Y MANUEL DEFAZ', '0981741611', '2004-10-20'),
(138, 0, 'QUINTO TORRES', 'LISSETH ANAIS', 'QUINTO TORRES LISSETH ANAIS', '0941746331', 'F', 'quintoanais390@gmail.com', 'DAULE', 'BENJAMIN CARRIÓN Y HUGO ORTIZ', '0991935979', '2005-07-10'),
(139, 0, 'CHIRIGUAYA DUQUE', 'ELIAS ENRIQUE', 'CHIRIGUAYA DUQUE ELIAS ENRIQUE', '0957595374', 'M', '', '', '', '', '0000-00-00'),
(140, 0, 'DUMES LOPEZ', 'JORGE EDUARDO', 'DUMES LOPEZ JORGE EDUARDO', '0927604231', 'M', '', '', '', '', '0000-00-00'),
(141, 0, 'CABRERA RONQUILLO', 'NOELIA ANALIZ', 'CABRERA RONQUILLO NOELIA ANALIZ', '0941801706', 'F', '', '', 'JOAQUIN GALLEGO LARA Y 9 DE OCTUBRE', '999629439', '2009-05-09'),
(142, 0, 'GARCIA ARREAGA', 'LUIS ANNIBAL', 'GARCIA ARREAGA LUIS ANNIBAL', '0954860664', 'M', 'luisgarciaareaga22@gmail.com', 'Palestina', 'Macul de Palestina', '0997254143', '2003-10-22'),
(143, 0, 'MACIAS BAJAÑA', 'JOEL STEVEN', 'MACIAS BAJAÑA JOEL STEVEN', '0943031054', 'M', 'maciasjoel1713@gmail.com', 'Santa Lucia', 'Bermejo del Frente', '0993652855', '2001-08-15'),
(144, 0, 'MACIAS PEREZ', 'DARWIN ULISES', 'MACIAS PEREZ DARWIN ULISES', '0959418567', 'M', 'maciasulises88@gmail.com', 'Daule', 'Riberas del Daule', '0997542356', '2001-11-10'),
(145, 0, 'MAGALLANES ALVARADO', 'ANDY JORDYN', 'MAGALLANES ALVARADO ANDY JORDYN', '0957425655', 'M', '', '', '', '', '0000-00-00'),
(146, 0, 'MAGALLANES LOPEZ', 'ALEXANDER GEOVANNY', 'MAGALLANES LOPEZ ALEXANDER GEOVANNY', '0957308737', 'M', '', '', '', '', '0000-00-00'),
(147, 0, 'MONTOYA SANTANA', 'MIGUEL ANGEL', 'MONTOYA SANTANA MIGUEL ANGEL', '0943026922', 'M', 'miguelmontoyasantana17@gmail.com', 'Daule', 'Patria Nueva', '0980225191', '2004-07-17'),
(148, 0, 'QUINTO TORRES', 'MAURICIO JAVIER', 'QUINTO TORRES MAURICIO JAVIER', '0942307562', 'M', 'maurcioquintot@gmail.com', 'Nobol', 'Petrillo via o avenida principal centro de la comuna', '0968005870', '2003-11-01'),
(149, 0, 'RAMIREZ PEÑAFIEL', 'CESAR JESUS', 'RAMIREZ PEÑAFIEL CESAR JESUS', '0940417207', 'M', 'cesarjesusramirez@gmail.com', 'Daule', 'Cascol de Bahona', '0990953095', '2003-12-05'),
(150, 0, 'REYES CHAVEZ', 'PEDRO ALFONSO', 'REYES CHAVEZ PEDRO ALFONSO', '0928920495', 'M', '', 'Daule', 'Marianita 3', '0980589166', '2003-11-04'),
(151, 0, 'CAICEDO CHAVEZ', 'ODALIS ANALIA', 'CAICEDO CHAVEZ ODALIS ANALIA', '0957048556', 'F', '', '', 'VISTA FLORIDA', '939162538', '2009-02-28'),
(152, 0, 'CASTRO INTRIAGO', 'GHISLAINE LARISSA', 'CASTRO INTRIAGO GHISLAINE LARISSA', '1316094828', 'F', '', '', 'AV.PIEDRAHITA Y SIXTO RUGEL', '996404839', '2009-01-10'),
(153, 0, 'CERCADO HERRERA', 'STEVEN JASMANI', 'CERCADO HERRERA STEVEN JASMANI', '0957949498', 'M', '', '', 'SAN JOSE', '997766399', '2007-01-06'),
(154, 0, 'CEVALLO CUSME', 'MIGUEL ALEJANDRO', 'CEVALLO CUSME MIGUEL ALEJANDRO', '0951191212', 'M', '', '', 'KM 30 VIA A DAULE', '9941445296', '2008-12-29'),
(155, 0, 'CHIPRE SILVA JANDRY GEAMPIERRE', 'CHIPRE SILVA JANDRY GEAMPIERRE', 'CHIPRE SILVA JANDRY GEAMPIERRE CHIPRE SILVA JANDRY GEAMPIERRE', '0957434103', 'M', '', '', 'HUANCHINCHAL', '939053393', '2009-07-24'),
(156, 0, 'CORDOVA MADRID', 'EMILY SCARLET', 'CORDOVA MADRID EMILY SCARLET', '0951418136', 'F', '', '', 'PEAJE DE CHIVERIA', '981092521', '2009-05-06'),
(157, 0, 'DIAZ SALAS', 'ELKIN PEDRO', 'DIAZ SALAS ELKIN PEDRO', '0941801219', 'M', '', '', 'RENDON  Y HOMERO', '967161181', '2008-05-19'),
(158, 0, 'DUMES ALVARADO', 'JORDY', 'DUMES ALVARADO JORDY', '0943357913', 'M', '', '', '', '', '0000-00-00'),
(159, 0, 'ESPINOZA TORRES', 'LILIANA LILIBETH', 'ESPINOZA TORRES LILIANA LILIBETH', '0942626664', 'F', '', '', 'RCTO LA RINCONADA', '939198037', '2009-07-11'),
(160, 0, 'ESPINOZA TORRES', 'MARIA ALEXANDRA', 'ESPINOZA TORRES MARIA ALEXANDRA', '0958983363', 'F', '', '', 'LA RINCONADA', '939198037', '2008-01-17'),
(161, 0, 'BARZOLA ALVARADO', 'ARTURO FERNANDO', 'BARZOLA ALVARADO ARTURO FERNANDO', '0957013527', 'M', '', 'KM. 29 VIA A DAULE', 'KM. 29 VIA A DAULE', '0939004871', '2004-10-28'),
(162, 0, 'CEDEÑO ALVARADO', 'ISAAC ALEXANDER', 'CEDEÑO ALVARADO ISAAC ALEXANDER', '0957742315', 'M', '', 'LIMONAL', 'LIMONAL RECINTO EL RECREO', '0939632876', '2005-01-18'),
(163, 0, 'AROCA PILLIGUA', 'GENESIS DAMARIS', 'AROCA PILLIGUA GENESIS DAMARIS', '0942801523', 'F', '', 'Entrada Limonal 	', 'Entrada Limonal 	', '099461586', '2013-02-28'),
(164, 0, 'CAICEDO GAVILANES', 'MATHIAS DARIKSON', 'CAICEDO GAVILANES MATHIAS DARIKSON', '0959128703', 'M', '', 'ABDON CALDERON Y VICTOR MANUEL	', 'ABDON CALDERON Y VICTOR MANUEL	', '', '0000-00-00'),
(165, 0, 'COROZO MORALES', 'LUIS SANTIAGO', 'COROZO MORALES LUIS SANTIAGO', '0958713422', 'M', '', 'JOAQUIN GALLEGOS LARA', 'JOAQUIN GALLEGOS LARA', '0978746352', '2004-12-07'),
(166, 0, 'CORNEJO BARZOLA', 'EMELY SOLANGE', 'CORNEJO BARZOLA EMELY SOLANGE', '0960721355', 'F', '', 'RIBERAS OPUESTAS', 'RIBERAS OPUESTAS', '0969258432 0967863121', '2013-01-16'),
(167, 0, 'CUEVA CARLOS', 'FRANCISCO GERARDO', 'CUEVA CARLOS FRANCISCO GERARDO', '0954521928', 'M', '', 'DAULE', 'VERNAZA 501 Y AYACUCHO', '0963749251', '2003-07-26'),
(168, 0, 'DESINTONIO ORTIZ', 'CRISTHIAN JOEL', 'DESINTONIO ORTIZ CRISTHIAN JOEL', '0941803918', 'M', '', 'MAGRO KM 41', 'MAGRO KM 41', '0992191933', '2005-10-11'),
(169, 0, 'GONZALEZ MORAN', 'JACKSON JOSUE', 'GONZALEZ MORAN JACKSON JOSUE', '0941749632', 'M', '', 'LOMAS DE SARGENTILLO', 'LOMAS DE SARGENTILLO', '0959626045', '2005-01-31'),
(170, 0, 'GOYA ROSADO', 'LUIS GABRIEL', 'GOYA ROSADO LUIS GABRIEL', '0953612108', 'M', '', 'KM 26 VIA A DAULE', 'KM26 VIA A DAULE', '0939395195', '2005-06-11'),
(171, 0, 'LEON MOREIRA', 'ELKIN ANTHONY', 'LEON MOREIRA ELKIN ANTHONY', '0942609330', 'M', '', 'DAULE', 'DR. JARAMILLO DETRAS DEL IESS', '0988106186', '2005-09-22'),
(172, 0, 'LOY TORRES', 'LUIS OLMEDO', 'LOY TORRES LUIS OLMEDO', '0941133100', 'M', '', 'LOS LOJAS', 'LOS LOJAS', '0968046272', '2005-01-19'),
(173, 0, 'MACIAS CORTEZ', 'ANTHONY FERNEY', 'MACIAS CORTEZ ANTHONY FERNEY', '0944222272', 'M', '', 'RECINTO YURIMA', 'RECINTO YURIMA', '0979449904', '2004-11-27'),
(174, 0, 'MARTINEZ LOOR', 'SAMUEL ALEJANDRO', 'MARTINEZ LOOR SAMUEL ALEJANDRO', '0958244840', 'M', '', 'DAULE BANIFE', 'DAULE BANIFE', '0980273139', '2005-08-16'),
(175, 0, 'MERCHAN CABRERA', 'JORDY CRISTHOFER', 'MERCHAN CABRERA JORDY CRISTHOFER', '0941133183', 'M', '', 'DAULA ATRAS DEL CEMENTERIO', 'DAULE ATRAS DEL CEMENTERIO', '09141932214', '2005-02-21'),
(176, 0, 'PEÑA RUGEL', 'ALLAN STEVEN', 'PEÑA RUGEL ALLAN STEVEN', '0941801359', 'M', '', 'LAS AMERICAS Y ESTERO NATO', 'LAS AMERICAS Y ESTERO NATO', '0985442308', '2005-01-24'),
(177, 0, 'PINELA CORTEZ', 'JILPSON JAIR', 'PINELA CORTEZ JILPSON JAIR', '0956245765', 'M', '', 'LAUREL', 'LAUREL', '0982634159', '2005-07-08'),
(178, 0, 'RODRIGUEZ MORAN', 'PEDRO JAVIER', 'RODRIGUEZ MORAN PEDRO JAVIER', '0958679540', 'M', '', 'DAULE AV. SAN FRANCISCO', 'DAULE AV. SAN FRANCISCO', '0968454638', '2006-06-08'),
(179, 0, 'ROMERO CAMBA', 'ALAN JOSUE', 'ROMERO CAMBA ALAN JOSUE', '0929985133', 'M', '', 'DAULE', 'JOSE NAVAS Y FRANCISCO VILLACRES', '0982442921', '2006-03-27'),
(180, 0, 'SALAZAR GOMEZ', 'ALEX EDUARDO', 'SALAZAR GOMEZ ALEX EDUARDO', '0957126725', 'M', '', 'DAULE', 'RIBERA OPUESTA', '0981051970', '2005-08-29'),
(181, 0, 'SALAZAR MORENO', 'EDUARDO ALEXANDER', 'SALAZAR MORENO EDUARDO ALEXANDER', '0955925078', 'M', '', 'LAUREL', 'RECINTO LOS ANGELES', '0993297652', '2005-09-30'),
(182, 0, 'SANCHEZ NOBOA', 'LEONARDO RONALDO', 'SANCHEZ NOBOA LEONARDO RONALDO', '0929984631', 'M', '', 'RECINTO NAUPE', 'RECINTO NAUPE', '0939043486', '2005-03-24'),
(183, 0, 'VIZUETA LOPEZ', 'TITO DAVID', 'VIZUETA LOPEZ TITO DAVID', '0929933539', 'M', '', 'SANTA LUCIA', 'SANTA LUCIA', '0959841508', '2006-03-22'),
(184, 0, 'ESPINOZA CAMBA', 'EDISON FERNANDO', 'ESPINOZA CAMBA EDISON FERNANDO', '0941610933', 'M', '', 'PETRILLO', 'PETRILLO', '0978853608', '2005-10-24'),
(185, 0, 'BAQUE CABRERA', 'ANGELA NARCISA', 'BAQUE CABRERA ANGELA NARCISA', '0956995872', 'F', 'Cabrera28vane@gmail.com', 'DAULE', 'CALLE 3 DE NOVIEMBRE Y AMAZONA', '044564165', '2005-08-02'),
(186, 0, 'BAQUE CABRERA', 'MERLYN MARIA', 'BAQUE CABRERA MERLYN MARIA', '0956996045', 'F', 'Cabrera28vane@gmail.com', 'REFERENCIA ATRAS CLINICA VIALICES', 'JOSE VELEZ  HEREDIA Y MARIA MAIGO', '0969167223', '2006-01-15'),
(187, 0, 'LEON CEPADA', 'ROSA PILAR', 'LEON CEPADA ROSA PILAR', '0943180687', 'F', 'dianacepada442@gmil.com', 'DAULE', 'CASCOL DE BAHONA', '0997012550', '2005-10-08'),
(188, 0, 'CHOEZ NAVARRETE', 'ALANIS LLONA', 'CHOEZ NAVARRETE ALANIS LLONA', '0957130404', 'F', 'analischoez52@gmail.com', 'EL TRIUNFO', 'MAGRO Y GENERAL LEONIDAS PLAZAS', '0990405772', '2006-01-09'),
(189, 0, 'BARZOLA HUAYAMAVE', 'NOEMI ELIZA', 'BARZOLA HUAYAMAVE NOEMI ELIZA', '0929983252', 'F', 'elizabarzola07@gmil.com', 'DAULE', 'JOSE VELEZ Y NARCISA DE JESUS', '0994796831', '2006-10-17'),
(190, 0, 'MAYA BENALCAZAR', 'LESLIE FERNANDA', 'MAYA BENALCAZAR LESLIE FERNANDA', '0959428681', 'F', 'ferlelimaya@gmail.com', 'DAULE', 'SANTA LUCIA Y BOYACA', '0996594176', '2006-04-24'),
(191, 0, 'CERCADO VARGAS', 'PRISCILA ELIZABETH', 'CERCADO VARGAS PRISCILA ELIZABETH', '0941134835', 'F', 'priscila23cercado@gmail.com', 'HUANCHICHAL', 'ENTRADA VIA A LAS CAÑAS', '0981733742', '2006-05-23'),
(192, 0, 'MOREIRA CALDERON', 'LEYLA XIOMARA', 'MOREIRA CALDERON LEYLA XIOMARA', '0958506812', 'F', 'leylamoreira95@gmail.com', 'BANIFE', 'PEDRO ISAIAS PRIMERA ETAPA', '0979905098', '2006-07-17'),
(193, 0, 'NIETO BONILLA', 'AMBAR DAYANA', 'NIETO BONILLA AMBAR DAYANA', '0941894628', 'F', 'dayanitabonilla2006@gmail.com', 'DAULE', 'AVENIDA SAN FRANCISCO', '0960294709', '2006-05-19'),
(194, 0, 'VARGARA VILLAMAR', 'JULEYDI ESTEFANIA', 'VARGARA VILLAMAR JULEYDI ESTEFANIA', '0954615126', 'F', 'juleydivergara.968@gmail.com', 'PATRIA NUEVA', 'por la antena de patria nueva', '0985165072', '2006-01-31'),
(195, 0, 'SANTANA RODRIGUEZ', 'GENESIS LILIANA', 'SANTANA RODRIGUEZ GENESIS LILIANA', '0958757395', 'F', 'santarodriguez@gmail.com', 'LA SECA', 'LA SECA', '0968089759', '2006-03-31'),
(196, 0, 'FOLLECO YANEZ', 'KIARA FIORELLA', 'FOLLECO YANEZ KIARA FIORELLA', '0954902805', 'F', 'alvarofolleco55@hotmail.com', 'SANTA CLARA', 'PIEDRAITA Y BOLIVAR', '0989967356', '2006-05-03'),
(197, 0, 'MENDEZ VERA', 'AILIN ARIANA', 'MENDEZ VERA AILIN ARIANA', '0955160932', 'F', 'javier-mendez-19-@hotmail.com', 'MARAVILLA. RECINTO', 'RECINTO SANTA ROSA', '0962729367', '2005-04-11'),
(198, 0, 'MENDEZ CORTEZ', 'LESLY DAYANA', 'MENDEZ CORTEZ LESLY DAYANA', '0941805467', 'F', 'lesliemendez701@gmail.com', 'SAN JOSE. DAULE', 'ESTERO NATO Y AVENIDA DE LAS AMERICA', '0991111543', '2006-11-11'),
(199, 0, 'VILLARREAL PEÑAFIEL', 'LEYDI ALEXANDRA', 'VILLARREAL PEÑAFIEL LEYDI ALEXANDRA', '0943584375', 'F', 'alexandravillareal2005@gmail.com', 'PEDRO ISAIAS. DAULE', 'BARTOLOME VILLAMAR', '0968734103', '2005-10-01'),
(200, 0, 'BARZOLA BONILLA', 'DIANA  LISBHET', 'BARZOLA BONILLA DIANA  LISBHET', '0942169947', 'F', 'lisbhetbarzola@gmail.com', 'PEDRO ISAIAS', 'PEDRO ISAIAS', '0991447163', '2006-07-19'),
(201, 0, 'SARCOS GARCIA', 'DANNA ANDREINA', 'SARCOS GARCIA DANNA ANDREINA', '0952379717', 'F', 'pameharcia1987@gmail.com', 'SAN JACINTO', 'RECINTO SAN JACINTO. MATE', '0981399725', '2005-10-21'),
(202, 0, 'ADRIAN ESPINOZA', 'ANGELA ALEXANDRA', 'ADRIAN ESPINOZA ANGELA ALEXANDRA', '0957765217', 'F', 'angelaadrian.eb57@gmail.com', 'PEDRO ISAIAS 3 ETAPA', 'CALLES. NOVIEMBRE Y MARILIS FUNTE', '0969422975', '2005-08-02'),
(203, 0, 'QUINTO NOBOA', 'KARLA  DANIELA', 'QUINTO NOBOA KARLA  DANIELA', '0941447724', 'F', 'pulguitaquinto@hotmail.com', 'DAULE', 'RECINTO SAN GABRIEL', '0982930883', '2006-03-25'),
(204, 0, 'MORAN VELIZ', 'MAYTE JAMLETH', 'MORAN VELIZ MAYTE JAMLETH', '0929999530', 'F', 'maytejamileth@gmail.com', 'NOBOL', 'RECINTO PAJONAL', '0967820968', '2006-02-08'),
(205, 0, 'DEL ROSARIO AVENTURA', 'NAOMI ABIGAIL', 'DEL ROSARIO AVENTURA NAOMI ABIGAIL', '0955944640', 'F', 'ventualexandra25@gmail.com', 'RECINTO LAS CAÑAS', 'LOMAS DE SRGENTILLO', '0969636165', '2006-07-08'),
(206, 0, 'LOZANO ALMEIDA', 'EMELY ROCIO', 'LOZANO ALMEIDA EMELY ROCIO', '1250897335', 'F', 'emelyalmeida191@gmail.com', 'DAULE', 'PATRIA NUEVA ATRAS DE LA PJ', '0979904160', '2006-07-30'),
(207, 0, 'VILLAMAR CAMPOZANO', 'ALLISON MAYTHE', 'VILLAMAR CAMPOZANO ALLISON MAYTHE', '0942654195', 'F', 'allissonvillamar35@gmail.com', 'DAULE', 'VELEZ Y COMIN DETRAS DEL CEMENTERIO', '0960572667', '2005-08-19'),
(208, 0, 'CARPIO CARPIO', 'ESTEBAN JOSE', 'CARPIO CARPIO ESTEBAN JOSE', '943613943', 'M', '', 'DAULE', 'BOQUERON', '0967895481', '2009-07-19'),
(209, 0, 'CAÑARTE SEGURA', 'ANDERSON STALIN', 'CAÑARTE SEGURA ANDERSON STALIN', '0942743097', 'M', '', 'DAULE', 'PATRIA NUEVA', '0997435338', '2008-10-08'),
(210, 0, 'CEDEÑO TELLO', 'MAYTE VANESSA', 'CEDEÑO TELLO MAYTE VANESSA', '0958166852', 'F', 'cedenokenya5@gmail.com', 'BANIFE', 'PATRIA NUEVA', '0991327825', '2007-09-17'),
(211, 0, 'QUINTO VILLAGOMEZ', 'BRITHANY JORDANA', 'QUINTO VILLAGOMEZ BRITHANY JORDANA', '0942152034', 'F', '', 'Daule', 'Calle principal: Desvio de las cañas', '', '2008-08-16'),
(212, 0, 'VAZQUEZ ALVARADO', 'JORDAN ALEXANDER', 'VAZQUEZ ALVARADO JORDAN ALEXANDER', '0942272683', 'M', '', 'Daule', '', '', '2007-09-01'),
(213, 0, 'CORNEJO BARZOLA', 'ALANNY ANAIS', 'CORNEJO BARZOLA ALANNY ANAIS', '0959432766', 'F', '', 'LA PLAYITA', 'RIVERA OPUESTA', '09969258438', '2008-12-29'),
(214, 0, 'CHAVEZ PLUAS', 'DAIRA LISANDRA', 'CHAVEZ PLUAS DAIRA LISANDRA', '0941805632', 'F', 'dianapluas28@gmail.com', 'DAULE', 'FRANCISCO VILLACRES Y MANUEL VILLAVICENCIO', '0990138745', '2010-02-12'),
(215, 0, 'CRUZ ROMAN', 'JONATHAN RODOLFO', 'CRUZ ROMAN JONATHAN RODOLFO', '0957322423', 'M', 'rodolfo_cruz1982@hotmail.com', 'BANIFE', 'ISIDRO DE VEINZA Y CESAR BAHAMONDE', '0968717888', '2009-07-30'),
(216, 0, 'SUAREZ RODRIGUEZ', 'CARLOS ANTONIO', 'SUAREZ RODRIGUEZ CARLOS ANTONIO', '0930509351', 'M', '', 'Daule', 'Patria Nueva', '', '2008-01-28'),
(217, 0, 'MORAN CEDEÑO', 'ARIEL YAZMANY', 'MORAN CEDEÑO ARIEL YAZMANY', '0957672199', 'M', '', 'JUAN.B.AGUIRRE', 'RIO PULA Y LAS AMERICAS', '0968852263', '2009-05-11'),
(218, 0, 'PLUAS MORAN', 'JANETH ANTONELLA', 'PLUAS MORAN JANETH ANTONELLA', '0954955175', 'F', '', 'Nobol', '', '', '2008-07-23'),
(219, 0, 'MORAN LUCIO', 'JOSUE MAYKENER', 'MORAN LUCIO JOSUE MAYKENER', '0941853194', 'M', 'kari_161987@hotmail.com', 'PADRE JUAN B A', 'DOMINGO COMIN Y GONZALO SANDUMBIDE', '0939553503', '2007-02-27'),
(220, 0, 'NUMERABLE HERRERA', 'JANDRY JESÚS', 'NUMERABLE HERRERA JANDRY JESÚS', '0954299923', 'M', '', 'LIMONAL', 'DESVIO DE LIMONAL', '0985865064', '2010-08-31'),
(221, 0, 'PIGUAVE RODRIGUEZ', 'SEBASTIAN SANTIAGO', 'PIGUAVE RODRIGUEZ SEBASTIAN SANTIAGO', '0951353952', 'M', 'rmlucisita.36@gmail.com', 'BANIFE', 'CALLE JUSTO TORRES Y MARTHA BUCARAM', '0999271810', '2010-05-30'),
(222, 0, 'RODRIGUEZ RUIZ', 'RICHARD JESUS', 'RODRIGUEZ RUIZ RICHARD JESUS', '0942015587', 'M', '', 'Daule', 'Av. San francisco, calle 26de noviembre.', '0979254932', '2008-06-22'),
(223, 0, 'PITA MEJIA', 'LITZY MAYERLY', 'PITA MEJIA LITZY MAYERLY', '957160021', 'F', '', 'DAULE', '', '0991441163', '0000-00-00'),
(224, 0, 'PLUAS RUIZ', 'JESUS RONALDO', 'PLUAS RUIZ JESUS RONALDO', '0956237333', 'M', '', 'YOLITA', '10 DE FEBRERO Y JOAQUIN GALLEGOS LARA', '0967070964', '2010-06-25'),
(225, 0, 'RAMIREZ SELLAN', 'ANTONIO JAVIER', 'RAMIREZ SELLAN ANTONIO JAVIER', '0941805772', 'M', '', 'DAULE', 'SAN FRANCISCO', '0993016440', '0000-00-00'),
(226, 0, 'COLOMA CANDELARIO', 'JOSTYN JOEL', 'COLOMA CANDELARIO JOSTYN JOEL', '0957628498', 'M', '', 'DAULE', 'DAULE AVENIDA LOS DAULIS Y VICENTE PINO MORAN', '0939021796', '2010-05-02'),
(227, 0, 'RODRIGUEZ ARROBA', 'EMELY JASLADY', 'RODRIGUEZ ARROBA EMELY JASLADY', '0941801870', 'F', 'ortizortizjeomayra@gmail.com', 'SANTA CLARA', 'ANTONIO PARRA (PERIMETRAL)', '0980839804', '2009-08-03'),
(228, 0, 'RODRIGUEZ LOZANO', 'YAJAIRA ADAMARIS', 'RODRIGUEZ LOZANO YAJAIRA ADAMARIS', '0957734601', 'F', '', 'SAN FRANCISCO', '26 DE NOVIEMBRE Y OTTO CARBO', '0939569761', '2010-01-11'),
(229, 0, 'FERNANDEZ BRIONES', 'PIERINA NATASHA', 'FERNANDEZ BRIONES PIERINA NATASHA', '0957568413', 'F', 'fatimabriones90@hotmail.com', 'DAULE', 'DIONISIO NAVAS Y BOLIVAR Y SUCRE', '0992065902', '2009-08-27'),
(230, 0, 'RUIZ BARZOLA', 'KETZIA NAHIARA', 'RUIZ BARZOLA KETZIA NAHIARA', '0958204919', 'F', 'karenbarzola33@gmail.com', 'SANTA CLARA', 'ISMAEL PEREZ PAZMIÑO Y FELICISISIMO ROJAS', '0990207028', '2010-05-07'),
(231, 0, 'FLORES RODRIGUEZ', 'EMILY MICHELLE', 'FLORES RODRIGUEZ EMILY MICHELLE', '0957617533', 'F', '', 'JUAN BAUTISTA AGUIRRE', 'JOSE PERALTA Y BYPAS', '0927322933', '2010-08-04'),
(232, 0, 'RUIZ PILALO', 'HILLARY JULIETH', 'RUIZ PILALO HILLARY JULIETH', '0950995142', 'F', '', 'DAULE', 'AVENIDA GRAN COLOMBIA Y 10 DE FEBRERO', '0988627329', '2010-03-04'),
(233, 0, 'SANCHEZ MINA', 'DENISSE DAYANA', 'SANCHEZ MINA DENISSE DAYANA', '1350513279', 'F', '', 'DAULE', '', '0995395462', '0000-00-00'),
(234, 0, 'SANCHEZ NAVARRETE', 'YORYENI ZAMARA', 'SANCHEZ NAVARRETE YORYENI ZAMARA', '98994410', 'F', '', 'DAULE', 'CAMILO PONCE Y CARLOS CEVALLOS', '0983634782', '2010-04-02'),
(235, 0, 'GOMEZ BAJAÑA', 'WENDY ANABEL', 'GOMEZ BAJAÑA WENDY ANABEL', '0941857922', 'F', '', 'DAULE', '2 DE AGOSTO Y DOMINGO COMIN ENTRE BOLIVAR', '0996079108', '2010-07-15'),
(236, 0, 'HERRERA HUAYAMAVE', 'ARIADNA EDITH', 'HERRERA HUAYAMAVE ARIADNA EDITH', '0942581208', 'F', 'michellycordova25@gmail.com', 'DAULE', '\"AV. SAN FRANCISCO Y ABDON CALDERON GARAY Y COA\"', '0968833306', '2009-01-30'),
(237, 0, 'SESME CERCADO', 'CARLOS ALEXANDER', 'SESME CERCADO CARLOS ALEXANDER', '0957226285', 'M', '', 'SANTA CLARA', 'Cdla. Assad Bucaram', '0967687950', '2009-09-22'),
(238, 0, 'MONTALVAN MACIAS', 'LISBETH STEFANY', 'MONTALVAN MACIAS LISBETH STEFANY', '0942832288', 'F', 'lisbeth_montalvan@hotmail.com', 'DAULE', 'BOLIVAR Y GENERAL VERNAZA', '0939603252', '2009-11-14'),
(239, 0, 'SILVA JAEN', 'JEAN CARLOS', 'SILVA JAEN JEAN CARLOS', '0957369507', 'M', 'jaenguagualeonor@gmail.com', 'José Vélez y Quito', 'Parte posterior del mercado', '0961059965', '2009-11-16'),
(240, 0, 'SILVA ORTEGA', 'AYLIN NEBRASKA', 'SILVA ORTEGA AYLIN NEBRASKA', '0958581837', 'F', 'sthefaniasilva_18@hotmail.com', 'RIBERA OPUESTA', 'FRENTE A LA IGLESIA SR DE LOS MILAGROS', '991562997', '2010-08-06'),
(241, 0, 'ORDOÑEZ CARDOZO', 'JOSWELL ELYSANDER', 'ORDOÑEZ CARDOZO JOSWELL ELYSANDER', '0917296607', 'M', 'cardozojoselyn65@gmail.com', 'DAULE', 'JUAN BAUTISTA AGUIRRE Y LEONIDAS LA TORRE Y PEDRO PANTALEON', '0994037962', '2009-10-19'),
(242, 0, 'RODRIGUEZ MORAN', 'LISSETTE JASBLEIDY', 'RODRIGUEZ MORAN LISSETTE JASBLEIDY', '0941801813', 'F', 'sinelandia88@gmail.com', 'DAULE', 'JOSE CARBO Y DUCHICE', '0926167388', '2009-11-23'),
(243, 0, 'RONQUILLO BAJAÑA', 'GLADYS MILAGROS', 'RONQUILLO BAJAÑA GLADYS MILAGROS', '0957364458', 'F', 'magnoronquillo@hotmail.com', 'DAULE', 'VICENTE PIEDRAHITA Y ROCAFUERTE', '0967111471', '2008-09-14'),
(244, 0, 'SALAS ADRIAN', 'CRISTOPHER JESUS', 'SALAS ADRIAN CRISTOPHER JESUS', '0957864226', 'M', 'Jamilethadriamora@gmail.com', 'DAULE', 'VÍCTOR MANUEL RENDON Y HOMERO ESPINOZA', '0980880949', '2009-12-28'),
(245, 0, 'SALAZAR ZUÑIGA', 'JESUS EMANUEL', 'SALAZAR ZUÑIGA JESUS EMANUEL', '0956937254', 'M', '', 'DAULE', 'PROV DE AZOGUEZ Y LAS AMERICAS Y RIO AMAZONAS', '0985700920', '2009-12-15'),
(246, 0, 'SAN LUCAS AVILES', 'DIANA YAMILET', 'SAN LUCAS AVILES DIANA YAMILET', '0942497090', 'F', '', 'DAULE', 'VIA DAULE / STA LUCIA  Y FRANCISCO DE PAULE Y SANTANDER', '0982693526', '2009-11-24'),
(247, 0, 'VALLEJO PEÑAFIEL', 'JOSE LUIS', 'VALLEJO PEÑAFIEL JOSE LUIS', '0943091694', 'M', '', 'DAULE', 'DOMINGO COMIN Y MANUEL GONZALEZ', '0967414023', '2010-10-13'),
(248, 0, 'VELASCO ALVARADO', 'JERAMY JOSUE', 'VELASCO ALVARADO JERAMY JOSUE', '0942304361', 'M', '', 'MAGRO', 'CALLE BOLIVAR MORAN', '0969216285', '2009-10-29'),
(249, 0, 'VERA BRIONES', 'SCARLET LISBETH', 'VERA BRIONES SCARLET LISBETH', '0956901979', 'F', 'brionesgordillo@hotmail.com', 'DAULE', 'HOMERO ESPINOZA Y 10 DE FEBRERO', '0994336433', '2009-07-02'),
(250, 0, 'VILLACRES RUIZ', 'JUSTIN ALEJANDRO', 'VILLACRES RUIZ JUSTIN ALEJANDRO', '1850874957', 'M', 'ruizruiz.marthamaria278@gmail.com', 'DAULE', 'BOLIVAR Y ENRIQUE ROMERO', '0987048254', '2010-10-20'),
(251, 0, 'VINCES SALDARRIAGA', 'KATHERINE ARIANA', 'VINCES SALDARRIAGA KATHERINE ARIANA', '0941801920', 'F', '', 'DAULE', 'ANTONIO PARRA Y JOSE GOMEZ IZQUIERDO', '0985451399', '2009-12-28'),
(252, 0, 'YANEZ SALAVARRIA', 'ALAN PAUL', 'YANEZ SALAVARRIA ALAN PAUL', '0941805715', 'M', 'cecicecibelsalavarria@gmail.com', 'DAULE', 'PABLO HANNIBAL VELA Y JOSE MARIA EGAS', '0990530007', '2010-03-13'),
(253, 0, 'ZURITA RUIZ', 'EMERSON GABRIEL', 'ZURITA RUIZ EMERSON GABRIEL', '956958177', 'M', 'doriszurita1977@gmail.com', 'DAULE', 'JOSE FElIX HEREDIA  Y LUIS MARIA MAIGON', '0986767712', '2010-08-18'),
(254, 0, 'PAREDES VERA', 'PAREDES VERA', 'PAREDES VERA PAREDES VERA', '0941801797', 'M', '', 'DAULE', 'CALLE BOLÍVAR AVENIDA QUINTO Y MANUEL GONZÁLEZ', '0993769921', '2009-11-19'),
(255, 0, 'RONQUILLO ALVARADO', 'CARMEN ROSA', 'RONQUILLO ALVARADO CARMEN ROSA', '0942300500', 'F', 'carmen ronquillo265@gmail.com', 'MARIANITA 3', 'RIO DAULE Y EDUARDO FLORES', '0991391710', '2006-09-11'),
(256, 0, 'ALVARADO MEDINA', 'FREDDY ENRIQUE', 'ALVARADO MEDINA FREDDY ENRIQUE', '0929980654', 'M', 'fredddyalvarado70@gmail.com', '', 'Calle Gregorio Conforme', '0985503581', '2004-11-02'),
(257, 0, 'RUIZ CAPUZANO', 'NAYELI ISABEL', 'RUIZ CAPUZANO NAYELI ISABEL', '0957575921', 'F', 'isabeelruizcampuzano76@gmail.com', 'SIXTO RUGEL', 'SIXTO RUGEL', '0939100590', '2004-09-21'),
(258, 0, 'CRUZ ROMAN', 'RAFAEL LEONEL', 'CRUZ ROMAN RAFAEL LEONEL', '0958856494', 'M', '', 'Patria Nueva	', 'Patria Nueva	', '0968717888', '2014-04-26'),
(259, 0, 'DUEÑAS TROYA', 'JOAN JAVIER', 'DUEÑAS TROYA JOAN JAVIER', '0959191073', 'M', '', 'Alamos', 'Alamos', '0939370482', '2014-02-17'),
(260, 0, 'FAJARDO PITA', 'DERECK DANIEL', 'FAJARDO PITA DERECK DANIEL', '0957094741', 'M', '', '09  de Octubre y Piedrahita  por can', '09  de Octubre y Piedrahita  por canal 9 	', '0990523837', '2013-10-18'),
(261, 0, 'FLORES RODRIGUEZ', 'ADRIANA DAMARIS', 'FLORES RODRIGUEZ ADRIANA DAMARIS', '0960175818', 'F', '', 'Jose Peralta y Bypa frente a las des', 'Jose Peralta y Bypa frente a las desmontaciones descanso frente', '0994746035', '2014-05-19'),
(262, 0, 'FRANCO CARDENAS', 'NEHEMIAS KLEYTON', 'FRANCO CARDENAS NEHEMIAS KLEYTON', '0956596183', 'F', '', 'AVENIDA SAN FRANCISCO CDLA EL RECUER', 'AVENIDA SAN FRANCISCO CDLA EL RECUERDO', '0968841505', '2013-08-23'),
(263, 0, 'GALLEGOS CHAPA', 'TAYLOR AGUSTIN', 'GALLEGOS CHAPA TAYLOR AGUSTIN', '0957124795', 'M', '', 'San Martín y Bolivar	', 'San Martín y Bolivar	', '091175489 0939086836', '2013-10-03'),
(264, 0, 'GARCIA HERRERA', 'VALESKA JULIETH', 'GARCIA HERRERA VALESKA JULIETH', '0958150799', 'F', '', '', '', '0989699396', '0000-00-00'),
(265, 0, 'HUAYAMAVE MARQUEZ', 'CRISTHIAN ALEJANDRO', 'HUAYAMAVE MARQUEZ CRISTHIAN ALEJANDRO', '0958567513', 'F', '', 'Ciudadela El Recuerdo	', 'Ciudadela El Recuerdo	', '0999270059   0991109095', '2014-02-10'),
(266, 0, 'JAMA PIGUAVE', 'ISNAY JHOANIE', 'JAMA PIGUAVE ISNAY JHOANIE', '0957930472', 'F', '', 'Ayacucho y Piedrahita', 'Ayacucho y Piedrahita', '0967376438', '2013-12-18'),
(267, 0, 'LEON LINO', 'CARLOS LUIS', 'LEON LINO CARLOS LUIS', '0958534158', 'M', '', 'Assab Bucaram Call Jose Maria Egas a', 'Assab Bucaram Call Jose Maria Egas al otro del carretero preinci', '0960959537   0980957907', '2014-03-24'),
(268, 0, 'LEY VENEGAS', 'BRUNO', 'LEY VENEGAS BRUNO', '0956268494', 'M', '', '', '', '', '0000-00-00'),
(269, 0, 'LOPEZ CARPIO', 'ALEXANDER SEBASTIAN', 'LOPEZ CARPIO ALEXANDER SEBASTIAN', '0957796345', 'M', '', 'Recinto Boqueron del Redondel pasa l', 'Recinto Boqueron del Redondel pasa la casa de los policIAS EN LA', '0969613887', '2013-12-20'),
(270, 0, 'MALDONADO SALDARRIAGA', 'BOLIVAR GREGORIO', 'MALDONADO SALDARRIAGA BOLIVAR GREGORIO', '0959130162', 'M', '', 'Calle Antonio Parra', 'Calle Antonio Parra', '0985451399', '2014-07-03'),
(271, 0, 'MERCHAN FAJARDO', 'ALLAN FRANCISCO', 'MERCHAN FAJARDO ALLAN FRANCISCO', '0958494601', 'M', '', 'Marianita 1 	', 'Marianita 1 	', '0999184152', '2014-02-23'),
(272, 0, 'MORAN ALVARADO', 'JENIFER GUADALUPE', 'MORAN ALVARADO JENIFER GUADALUPE', '0942706003', 'F', '', 'Gral. Leonidad Plaza y Batallo Daule', 'Gral. Leonidad Plaza y Batallo Daule Mz 	', '0991850708', '2012-09-25'),
(273, 0, 'MORAN CEDEÑO', 'GRACIELA NAILEA', 'MORAN CEDEÑO GRACIELA NAILEA', '095817567', 'F', '', 'Rio Pula y las Americas calle Princi', 'Rio Pula y las Americas calle Principal diagonal  al descanso de', '0968852263', '2013-10-14'),
(274, 0, 'NAVARRETE RIVERA', 'RAFAELA FRANSHESKA', 'NAVARRETE RIVERA RAFAELA FRANSHESKA', '0956725295', 'F', '', 'Coperativa Assad Bucaram 	', 'Coperativa Assad Bucaram 	', '0958889409', '2013-09-07'),
(275, 0, 'NIVELA CAMBA', 'JOSE ANDRES', 'NIVELA CAMBA JOSE ANDRES', '0960935344', 'F', '', '', '', '0968284604', '2013-12-21'),
(276, 0, 'RONQUILLO ALVARADO', 'YANDRI JOSUE', 'RONQUILLO ALVARADO YANDRI JOSUE', '0957446438', 'M', 'malvaradomoran@gmail', 'Nobol', 'Lotizacion bella flor', '0989554591', '2003-06-23'),
(277, 0, 'RUGEL SALAS', 'JOSUE EDUARDO', 'RUGEL SALAS JOSUE EDUARDO', '0958817603', 'M', '', 'Daule', 'Los Alamos por la perimetral', '0980215556', '2004-01-13'),
(278, 0, 'TOMALA VAZQUEZ', 'MAURCIO JUSTINOS', 'TOMALA VAZQUEZ MAURCIO JUSTINOS', '0941134751', 'M', '', 'Daule', 'Recinto el Naule', '0969166302', '2004-06-01');
INSERT INTO `sw_estudiante` (`id_estudiante`, `es_nro_matricula`, `es_apellidos`, `es_nombres`, `es_nombre_completo`, `es_cedula`, `es_genero`, `es_email`, `es_sector`, `es_direccion`, `es_telefono`, `es_fec_nacim`) VALUES
(279, 0, 'VARGAS FRANCO', 'DEIVY JAVIER', 'VARGAS FRANCO DEIVY JAVIER', '0929913069', 'M', '', 'Lomas de Sargentillo', 'Recintolas Cañas', '0989128690', '2005-02-10'),
(280, 0, 'VASQUEZ CASTRO', 'VASQUEZ CASTRO JOSTYN ALEXANDER', 'VASQUEZ CASTRO VASQUEZ CASTRO JOSTYN ALEXANDER', '0929172781', 'M', 'vjostyn533@gmail.com', 'Palestina', 'Recinto Coloradal', '0995910731', '2004-02-04'),
(281, 0, 'VELIZ CARRIEL', 'MIGUEL ANGEL', 'VELIZ CARRIEL MIGUEL ANGEL', '0953007697', 'M', '', 'Nobol', 'Juan Alvarez y Tomas Martinez', '0969393974', '2002-08-14'),
(282, 0, 'VELIZ GAMBOA', 'JHON WILSON', 'VELIZ GAMBOA JHON WILSON', '094420193', 'M', 'jhonveliz@gmal.com', 'Guayaquil', 'Km 20 via Daule Guayaquil', '0986722199', '2003-12-20'),
(283, 0, 'ALVARADO LARA', 'DIEGO ANTINIO', 'ALVARADO LARA DIEGO ANTINIO', '0958436362', 'M', 'da2427869@gmail.com', 'DAULE', 'BAY PASS PERIMETRAL Y BATALLON MZ155SL 1A', '0990426335', '2005-02-18'),
(284, 0, 'ROSADO VERA', 'ERWIN SIMON', 'ROSADO VERA ERWIN SIMON', '0958415606', 'M', 'rosadoverarositasilvana@gmail.com', 'DAULE', 'RESINTO RIO PERDIDO', '0960972923', '2003-07-24'),
(285, 0, 'MORA GONZALEZ', 'ROLANDO EMILIANO', 'MORA GONZALEZ ROLANDO EMILIANO', '0927673525', 'M', 'tanitap.93mg@hotmail.com', 'DAULE', 'DESVIO LAS CAÑAS', '0999619402', '2004-08-30'),
(286, 0, 'CHOEZ NAVARRETE', 'REYNALDO PAUL', 'CHOEZ NAVARRETE REYNALDO PAUL', '095130800', 'M', 'reynaldonavarrete@gmail.com', 'DAULE', 'AV. SAN FRANCISCO Y HOMERO ESPINOZA', '969414790', '2005-09-05'),
(287, 0, 'SALAZAR CASTRO', 'JESUS MANUEL', 'SALAZAR CASTRO JESUS MANUEL', '0958203887', 'M', '', 'Boca de las piñas', 'DAULE - Recinto los jasmin', '0996037212', '2005-09-01'),
(288, 0, 'SANCHEZ SANCHEZ', 'EFREN JESUS', 'SANCHEZ SANCHEZ EFREN JESUS', '0957469380', 'M', 'rociosanchez@gmail.com', 'SANTA LUCÌA -  LA LORENA', 'km57via daule santa lucia', '0939894313', '2005-04-03'),
(289, 0, 'SESME RONQUILLO', 'DAVIS ARIEL', 'SESME RONQUILLO DAVIS ARIEL', '0958704736', 'M', '', 'JUAN BAUTISTA AGUIRRE', 'DAULE - PEDRO CHAMBERS Y OTTO CARBO', '0985558699', '2004-09-05'),
(290, 0, 'ESPINOZA RODRIGUEZ', 'ANTHONY', 'ESPINOZA RODRIGUEZ ANTHONY', '941136723', 'M', 'anthonyespinoza26072004@gmail.com', 'LOMAS', 'RECT LAS CAÑAS', '981185504', '2004-07-26'),
(291, 0, 'TUTIVEN PIZA', 'EDISON JOSEHP', 'TUTIVEN PIZA EDISON JOSEHP', '0928202639', 'M', 'edisontutipiza@gmail.com', 'CIUDADELA LA PROVIDENCIA', 'NOBOL', '0968428292', '2005-05-18'),
(292, 0, 'VILLAMARIN BORJA', 'STEVEN ALEXANDER', 'VILLAMARIN BORJA STEVEN ALEXANDER', '0957933559', 'M', 'stivenalexandervillamarin@gmail.com', 'Daule', 'Patria Nueva Hermogenes Rivas y Cesar Bahamonde', '0986722199', '2004-07-18'),
(293, 0, 'FAJARDO BALDIRES', 'GEORGY SALOMON', 'FAJARDO BALDIRES GEORGY SALOMON', '0928298255', 'M', 'salomonbaldires@gmail.com', 'NOBOL', 'lotizacion san ramon', '979655032', '2004-02-28'),
(294, 0, 'ZAMBRANO RUIZ', 'PATRICIO JAVIER', 'ZAMBRANO RUIZ PATRICIO JAVIER', '0942677782', 'M', '', 'Daule', 'Parroquia Limonal Recinto Loma del Papayo', '0982932083', '2004-11-16'),
(295, 0, 'ALVARADO VERA', 'JEFFERSON AUGUSTO', 'ALVARADO VERA JEFFERSON AUGUSTO', '0942564188', 'M', 'alvejeau3170411@estudiantes3.edu.ec', 'Daule', 'Daule', '0994425298', '2002-11-20'),
(296, 0, 'ALVAREZ SALAZAR', 'RICARDO JOEL', 'ALVAREZ SALAZAR RICARDO JOEL', '0927673459', 'M', 'alsarijo4177768@estudiantes3.edu.ec', 'Daule', 'Daule', '0968835311', '2002-08-12'),
(297, 0, 'AVILES ORTEGA', 'FERNANDO EZEQUIEL', 'AVILES ORTEGA FERNANDO EZEQUIEL', '0929052942', 'M', 'avorfeez3712538@estudiantes3.edu.ec', 'AV.SAN FRANCISCO, JOSE DOMINGO ELIZA', 'Daule', '0960521985', '2003-11-10'),
(298, 0, 'BRIONES MORAN', 'MOISES ROBERTO', 'BRIONES MORAN MOISES ROBERTO', '0941025843', 'M', 'brmomoro4081365@estudiantes3.edu.ec', 'Daule', 'Daule', '0982520649', '2004-06-06'),
(299, 0, 'CHOEZ INTRIAGO', 'JEAN PIERRE', 'CHOEZ INTRIAGO JEAN PIERRE', '0941632077', 'M', 'chinjepi8424742@estudiantes3.edu.ec', 'Daule', 'Daule', '0996951611', '2005-03-18'),
(300, 0, 'BRIONES SANCHEZ', 'JEAMPOL', 'BRIONES SANCHEZ JEAMPOL', '0927672089', 'M', 'ms7822963@gmail.com', 'AV.SAN FRANCISCO, J.J LEON MERA', 'Daule', '0939009814', '2002-05-01'),
(301, 0, 'GOMEZ MEJIA', 'FRANCISCO JOEL', 'GOMEZ MEJIA FRANCISCO JOEL', '0956916472', 'M', 'gomefrjo3335230@estudiantes3.edu.ec', 'PEDRO ISAIAS 1ERA ETAPA', 'Daule', '0993375853', '2004-11-25'),
(302, 0, 'JIMENEZ VILLAMAR', 'STEVEN FERNANDO', 'JIMENEZ VILLAMAR STEVEN FERNANDO', '0956825012', 'M', 'jivistfe4191868@estudiantes3.edu.ec', 'RCTO. LA LEGUA', 'SANTA LUCIA', '0967012670', '2004-06-24'),
(303, 0, 'JURADO PARRAGA', 'KEVIN ANIBAL', 'JURADO PARRAGA KEVIN ANIBAL', '0950625004', 'M', 'jupakean3151279@estudiantes3.edu.ec', 'SANTA LUCIA', 'SANTA LUCIA', '0986373681', '2004-10-15'),
(304, 0, 'LIBERIO GONZALEZ', 'DIEGO DAVID', 'LIBERIO GONZALEZ DIEGO DAVID', '0950592139', 'M', 'ligodida3951203@estudiantes3.edu.ec', 'RCTO. LAS CAÑAS', 'LOMAS DE SARGENTILLO', '0964159234', '0000-00-00'),
(305, 0, 'MAGALLANES DUMES', 'JOSHTIN JOSE', 'MAGALLANES DUMES JOSHTIN JOSE', '0956095913', 'M', 'madujojo4237110@estudiantes3.edu.ec', 'RCTO. RIO PERDIDO', 'NOBOL', '0993192697', '2004-06-21'),
(306, 0, 'MINDIOLAZA RONQUILLO', 'JANDRY JOSE', 'MINDIOLAZA RONQUILLO JANDRY JOSE', '0942666520', 'M', 'mirojajo2366418@estudiantes3.edu.ec', 'RCTO.LA VUELTA', 'Daule', '0963460522', '2004-05-03'),
(307, 0, 'PAMBI CADENA', 'ABEL ELIASIB', 'PAMBI CADENA ABEL ELIASIB', '0953657798', 'M', 'pacaabel10924223@estudiantes3.edu.ec', 'Daule', 'Daule', '0985437906', '2004-09-25'),
(308, 0, 'PAREDES VERA', 'WILLIAM SAMUEL', 'PAREDES VERA WILLIAM SAMUEL', '0940223738', 'M', 'pavewisa4076944@estudiantes3.edu.ec', 'AV. QUITO DOMINGO  COMIN', 'Daule', '0990451978', '2005-05-18'),
(309, 0, 'PELAEZ DUMES', 'ANTHONY JOSTIN', 'PELAEZ DUMES ANTHONY JOSTIN', '0929976751', 'M', 'peduanjo3272494@estudiantes3.edu.ec', 'RCTO.PAJONAL', 'NOBOL', '0992464819', '2004-06-26'),
(310, 0, 'RONQUILLO MORAN', 'ERICK ALEJANDRO', 'RONQUILLO MORAN ERICK ALEJANDRO', '0941745994', 'M', 'romoeral2100045@estudiantes3.edu.ec', 'RCTO. LA GABARRA', 'Daule', '0980835679', '2003-01-07'),
(311, 0, 'RUGEL BONILLA', 'JOSE LUIS', 'RUGEL BONILLA JOSE LUIS', '0942166703', 'M', 'rubojolu3524276@estudiantes3.edu.ec', 'KM27 RCTO MATE', 'DAULE', '0994390711', '2004-02-01'),
(312, 0, 'SANCHEZ NOBOA', 'PATRICIO ANDRES', 'SANCHEZ NOBOA PATRICIO ANDRES', '0941025975', 'M', 'sanopaan4061870@estudiantes3.edu.ec', 'RCTO. NAUPE', 'DAULE', '0939043486', '2004-01-12'),
(313, 0, 'TROYA LEON', 'CARLOS STEVEN', 'TROYA LEON CARLOS STEVEN', '0958732133', 'M', 'trlecast3787052@estudiantes3.edu.ec', 'CALLE OLMEDO', 'DAULE', '0921949202', '2004-08-15'),
(314, 0, 'TUTIVEN DELGADO', 'DAVID RENE', 'TUTIVEN DELGADO DAVID RENE', '0941852147', 'M', 'tudedare1712907@estudiantes3.edu.ec', 'RCTO.GUARUMAL', 'DAULE', '0967010685', '2003-12-26'),
(315, 0, 'VILLAFUERTE FAJARDO', 'ANDER JAHER', 'VILLAFUERTE FAJARDO ANDER JAHER', '0941133530', 'M', 'vifaanja3502765@estudiantes3.edu.ec', 'RCTO.CAÑAS', 'DAULE', '0939409631', '2004-10-16'),
(316, 0, 'VILLAMAR GONZALEZ', 'DAYANARA SCARLET', 'VILLAMAR GONZALEZ DAYANARA SCARLET', '0927604702', 'F', 'vigodasc3985676@estudiantes3.edu.ec', '10 DE AGOSTO Y FRANCISCO GONZALEZ', 'DAULE', '0992400331', '2004-09-14'),
(317, 0, 'ZAMBRANO CANDELARIO', 'JEFFRY RONALDO', 'ZAMBRANO CANDELARIO JEFFRY RONALDO', '0956319792', 'M', 'zacajero10851280@estudiantes3.edu.ec', 'RCTO PAJONAL', 'Daule', '0960093146', '2002-08-07'),
(318, 0, 'ACOSTA MORA', 'DARLIN JOEL', 'ACOSTA MORA DARLIN JOEL', '0957845795', 'M', 'marlon16joel@gmail.com', 'Daule', 'PATRIA NUEVA', '093967615861', '2002-05-16'),
(319, 0, 'PORTILLA TARIRA', 'JENNIFER VANESSA', 'PORTILLA TARIRA JENNIFER VANESSA', '0941744070', 'F', 'tarira0208@gmail.com', 'Nobol', 'km 30 Via a Daule-Petrillo', '0978748049', '2008-02-03'),
(320, 0, 'ALMEIDA MORA', 'JEAN CARLOS', 'ALMEIDA MORA JEAN CARLOS', '0953551074', 'M', 'almojeca2456789@estudiantes3.edu.ec', 'SANTA LUCIA', 'RECINTO EL MANGLE', '0981258656', '2004-02-02'),
(321, 0, 'RIVAS RATTIA', 'CESAR ANDRES', 'RIVAS RATTIA CESAR ANDRES', '33214073', 'M', 'pulidoana277@gmail.com', 'DAULE', 'JUAN BAUTISTA AGUIRRE', '0989310394', '2007-08-21'),
(322, 0, 'RAMOS CEDEÑO', 'FLAVIO GABRIEL', 'RAMOS CEDEÑO FLAVIO GABRIEL', '0929086197', 'M', 'flaviogabrielramos2006@gmail.com', 'NOBOL', 'PETRILLO KM 30 VIA A DAULE', '0994811590', '2006-10-26'),
(323, 0, 'PILLASAGUA CALERO', 'CHELSEA PAULETTE', 'PILLASAGUA CALERO CHELSEA PAULETTE', '0958273583', 'F', 'pauletpillasagua2008@gmail.com', 'DAULE', 'CDLA. ASSAD BUCARAM', '0993372984', '2008-03-12'),
(324, 0, 'SALAZAR GOMEZ', 'AGUSTIN FRANCISCO', 'SALAZAR GOMEZ AGUSTIN FRANCISCO', '0957126964', 'M', 'salazargomezagustin@gmail.com', 'DAULE', 'RIVERAS OPUESTAS', '098 418 7318', '2008-10-17'),
(325, 0, 'SOLORZANO MENDEZ', 'LESLY KATIRIA', 'SOLORZANO MENDEZ LESLY KATIRIA', '0940105588', 'F', 'barbysolmen@hotmail.com', 'DAULE', 'Narcisa de Jesús y Joaquín Gallegos Mz. 93. Junto Col. JLTamayo', '0967308678', '2008-06-06'),
(326, 0, 'TUTIVEN FUENTES', 'HEIDY OMAIRA', 'TUTIVEN FUENTES HEIDY OMAIRA', '0941802506', 'F', 'mirnafuentes1976@hotmail.com', 'DAULE', 'SIMON BOLIVAR Y PIEDRAHITA FRENTE AL MUNICIPIO DE DAULE', '0968430060', '2008-10-14'),
(327, 0, 'MEDINA LOZANO', 'JEAMPOL MARTIN', 'MEDINA LOZANO JEAMPOL MARTIN', '0942274408', 'M', 'maryurybasilia@gmail.com', 'DAULE', 'RCTO. EL PIGIO LA SECA A LA ORILLA DEL RIO - LOS LOJAS', '0986904690', '2008-08-06'),
(328, 0, 'MEDINA LOZANO', 'JEAMPIERRE MARTIN', 'MEDINA LOZANO JEAMPIERRE MARTIN', '0942274184', 'M', 'maryurybasilia@gmail.com', 'DAULE', 'RCTO. EL PIGIO LA SECA A LA ORILLA DEL RIO - LOS LOJAS', '0986904690', '2008-08-06'),
(329, 0, 'ROMERO CASTILLO', 'SHIRLEY ALEJANDRA', 'ROMERO CASTILLO SHIRLEY ALEJANDRA', '0943031518', 'F', '', 'DAULE', 'ASSAD BUCARAM', '0993696482', '2008-10-24'),
(330, 0, 'POZO MORA', 'JHONNY JECSUA', 'POZO MORA JHONNY JECSUA', '0954496436', 'M', 'da2751864@gmail.com', 'NOBOL', 'PETRILLO PRIMAVERA 2', '0958792677', '2006-07-05'),
(331, 0, 'BALLES ORTEGA', 'DIEGO ALEXANDER', 'BALLES ORTEGA DIEGO ALEXANDER', '0957244023', 'M', 'erikaballes@hotmail.es', 'SANTA LUCIA', 'VIA CABUYAL RCTO. CANDELA', '0987759234', '2008-05-12'),
(332, 0, 'MENDEZ BRIONES', 'SEBASTIAN DERLIS', 'MENDEZ BRIONES SEBASTIAN DERLIS', '0941856239', 'M', '', 'DAULE', 'CDLA. BRISAS DEL DAULE VIA NAUPE', '0939159123', '2008-03-17'),
(333, 0, 'ANCHUNDIA BAQUE', 'KEYLA CHIQUINKIRA', 'ANCHUNDIA BAQUE KEYLA CHIQUINKIRA', '0959285339', 'F', 'anchundiakeyla099@gmail.com', 'BANIFE', 'BANIFE', '0939146950', '2005-06-16'),
(334, 0, 'FRANCO MINDIOLAZA', 'OSCAR JOPSEL', 'FRANCO MINDIOLAZA OSCAR JOPSEL', '0953996584', 'M', '', '', 'PATRIA NUEVA', '997430853', '2009-07-15'),
(335, 0, 'HERNANDEZ VILLARREAL', 'JUNIOR ALEJANDRO', 'HERNANDEZ VILLARREAL JUNIOR ALEJANDRO', '149498564', 'M', '', 'SECTOR LAS CUCHARAS, CALLE JUVENAL J', 'PEDRO CARBO', '992100575', '2007-11-29'),
(336, 0, 'VERA PARRALES', 'JASMERI LISBETH', 'VERA PARRALES JASMERI LISBETH', '0942979170', 'F', 'jasmeni@gmail.com', 'RECINTO LOS QUEMADOS', 'LA T VIA LAUREL', '0967755345', '2005-06-13'),
(337, 0, 'JIMENEZ LAMILLA', 'TANYA NOELY', 'JIMENEZ LAMILLA TANYA NOELY', '0942756123', 'F', '', 'SANTA LUCIA', 'BARBASCO CENTRAL', '993569349', '2009-02-03'),
(338, 0, 'RONQUILLO HUAYAMAVE', 'JAIRO FRANCISCO', 'RONQUILLO HUAYAMAVE JAIRO FRANCISCO', '0942713801', 'M', 'jairo.ronquillo@gmail.com', 'DAULE', 'RECINTO NAUPE', '0951757013', '2007-12-02'),
(339, 0, 'MAGALLANES PEÑAFIEL', 'JUAN DANIEL', 'MAGALLANES PEÑAFIEL JUAN DANIEL', '0957527799', 'M', '', '', 'PEDRO ISAIAS 2DA ETAPA', '3939384954', '2009-10-06'),
(340, 0, 'MOSQUERA LENIS', 'ARIANA ABIGAIL', 'MOSQUERA LENIS ARIANA ABIGAIL', '0957114887', 'F', 'mosqueralenisa983@gmail.com', 'MARIANITA # 5', 'LEOPOLDO BENITEZ Y JOAQUIN ORRALIA', '0980305988', '2004-07-05'),
(341, 0, 'AVILEZ ROMAN', 'MAYERLI NINOSKA', 'AVILEZ ROMAN MAYERLI NINOSKA', '0955187863', 'F', 'mayerli.avilez@gmail.com', 'EL RECUERDO', 'VICTOR RENDON Y ALFREDO BAQUERIZO MORENO', '0959602212', '0000-00-00'),
(342, 0, 'BARZOLA MEDINA', 'BRITHANY NICOLE', 'BARZOLA MEDINA BRITHANY NICOLE', '0942456039', 'F', 'britanynicolebarzolamedina@gmail.com', 'NOBOL', 'MANABI BELLA FLOR', '0983846144', '2004-01-05'),
(343, 0, 'ROMERO DUMES', 'VICTOR JARETH', 'ROMERO DUMES VICTOR JARETH', '0941807654', 'M', 'dumesr452@gmail.com', 'JUDIPA', 'RECINTO JUDIPA', '09833369130', '2008-02-27'),
(344, 0, 'MORA ORTEGA', 'YULEISI MILENA', 'MORA ORTEGA YULEISI MILENA', '0957082332', 'F', 'karla.moraortega2002@gmail.com', 'Bahona nuevo by pass Nobol', 'DAULE', '0959652257', '2006-10-14'),
(345, 0, 'AROCA PILLIGUA', 'ANNA EDUARDA', 'AROCA PILLIGUA ANNA EDUARDA', '0958038895', 'F', '', 'LIMONAL', 'DAULE', '0979541119', '2007-05-01'),
(346, 0, 'FRANCO CANTOS', 'MAYERLIN JOHANNA', 'FRANCO CANTOS MAYERLIN JOHANNA', '094302804', 'F', 'francomayerlin78@gmail.com', 'RCTO CHIGUIJO', 'PARROQUIA LAUREL', '0969763789', '2003-12-25'),
(347, 0, 'CASTAÑO SALTOS', 'RUBI MILENA', 'CASTAÑO SALTOS RUBI MILENA', '0929204410', 'F', 'chiintabebe30@gmail.com', 'AYACUCHO Y VELIZ', 'AYACUCHO Y VELIZ', '0963199186', '2008-04-12'),
(348, 0, 'LAMILLA PERALTA', 'XIMENA MARIELIS', 'LAMILLA PERALTA XIMENA MARIELIS', '0942270554', 'F', 'marielisp@gmail.com', 'RCTO BRABASCO', 'SANTA LUCIA RECINTO BARBASCO', '0985712651', '2007-09-13'),
(349, 0, 'MANTUANO GOMEZ', 'AARON YERALD', 'MANTUANO GOMEZ AARON YERALD', '0958146771', 'M', '', 'RCTO. PAJONAL - SAGRADO CORAZÓN DE J', 'DAULE- ENTRADA POR MAGRO', '0959012782', '2006-03-20'),
(350, 0, 'ROMERO ROSADO', 'JORGE EZEQUIEL', 'ROMERO ROSADO JORGE EZEQUIEL', '0942727165', 'M', 'juliaelenarosado@gmail.com', 'LA TOMA', 'KM 26 VIA A DAULE', '0939800496', '2008-02-06'),
(351, 0, 'VELOZ JIMENEZ', 'MABEL ELENA', 'VELOZ JIMENEZ MABEL ELENA', '0941465965', 'F', 'Jimyjavier531@gmail.com', 'SANTA CLARA- Fco Orellana  y  Pedro ', 'DAULE', '0990297807', '2007-11-08'),
(352, 0, 'MONTALVAN MACIAS', 'KEVIN GABRIEL', 'MONTALVAN MACIAS KEVIN GABRIEL', '0957361447', 'M', '', '', 'BOLIVAR Y VERNAZA', '967709102', '2007-09-01'),
(353, 0, 'GUERRERO CAICEDO', 'JOSE DANIEL', 'GUERRERO CAICEDO JOSE DANIEL', '0944364637', 'M', 'nathaly-caicedo1989@gmaiul.com', 'EL TRIIUNFO', 'DAULE, CDLA EL TRIUNFO', '0960989809', '2008-03-13'),
(354, 0, 'MENDEZ SARCO', 'GEOVANNA MAYTE', 'MENDEZ SARCO GEOVANNA MAYTE', '0942272188', 'F', 'edisonmendez1982@gmail.com', 'RECINTO TIINTAL DE ANIMAS', 'ANIMAS TINTAL', '0989851057', '2008-12-03'),
(355, 0, 'DELGADO MORALES', 'ELAINE ELIZABETH', 'DELGADO MORALES ELAINE ELIZABETH', '0942017583', 'F', 'moraleselaine104@gmail.com', 'VICENTE PIEDRAHITA', 'DAULE', '0959725597', '2002-09-20'),
(356, 0, 'MORAN HUAYAMAVE', 'EMILIO JAHIR', 'MORAN HUAYAMAVE EMILIO JAHIR', '0957390750', 'M', '', '', 'ENTRADA AL PEDREGAL', '0969771161', '2009-10-17'),
(357, 0, 'VELASCO BUENDIA', 'MARIA ESTHER', 'VELASCO BUENDIA MARIA ESTHER', '0957767163', 'F', 'buendiamary.77@gmail.com', 'Frente a la píldora “Gloria Matilde”', 'DAULE', '0967687195', '2007-06-07'),
(358, 0, 'NAVARRETE ALVARADO', 'MARIA JOSE', 'NAVARRETE ALVARADO MARIA JOSE', '0941899692', 'F', '', '', 'CDLA. SIXTO RUGEL Y CALLEJON S/N', '994987194', '2009-03-04'),
(359, 0, 'MORAN ASPIAZU', 'ISABEL CARMEN', 'MORAN ASPIAZU ISABEL CARMEN', '0940225527', 'F', 'moranisabel769@gmail.com', 'CDLA BELEN', 'GARCIA MORENOOTTO AVELLAN', '0999394268', '2004-01-30'),
(360, 0, 'MACIAS RONQUILLO', 'JOSUE MIGUEL', 'MACIAS RONQUILLO JOSUE MIGUEL', '0942270083', 'M', 'glendiuchis@hotmail.com', 'OLMEDO ALMEIDA Y  LUIS URDANETA', 'DAULE', '0993260365', '2007-11-05'),
(361, 0, 'ORTEGA ALDAZ', 'MEYLI PATRICIA', 'ORTEGA ALDAZ MEYLI PATRICIA', '0942160847', 'F', '', '', 'CALLE QUITO Y JOSE VELEZ', '961871120', '2009-10-03'),
(362, 0, 'AVILES LOPEZ', 'VERONICA ANGELA', 'AVILES LOPEZ VERONICA ANGELA', '0958970006', 'F', 'vero0424aviles@gmail.com', 'Marianita #5-Paseo shopping Daule di', 'DAULE', '0939917026', '2004-10-24'),
(363, 0, 'PINELA TOMALA', 'JESUS GABRIEL', 'PINELA TOMALA JESUS GABRIEL', '0942160151', 'M', '', '', 'CIUDADELA LOS ALAMOS', '0981051437', '2008-10-25'),
(364, 0, 'RIVAS RONQUILLO', 'LADY ADRIANA', 'RIVAS RONQUILLO LADY ADRIANA', '0941139180', 'F', 'rivaslady115@gmail.com', 'RCTO JIGUAL', 'RCTO JIGUAL', '0989699173', '2001-05-01'),
(365, 0, 'MOTA BAJAÑA', 'JORGE ADRIAN', 'MOTA BAJAÑA JORGE ADRIAN', '0942647686', 'M', 'jorgito.mota09@gmail.com', 'RCTO PAIPAYALES', 'RCTO PAIPAYALES', '0982729584', '2004-02-09'),
(366, 0, 'PROAÑO AYALA', 'JEAN CARLOS', 'PROAÑO AYALA JEAN CARLOS', '0929996783', 'M', 'JEANCARLOSPROAÑOAYALA@Gmail.com10 de feb', 'RUMIÑAHUI', '10 DE FEBRERO Y LUIS URDANETA', '0979980464', '2004-02-21'),
(367, 0, 'HERRERA FAJARDO', 'LITZY NORELIA', 'HERRERA FAJARDO LITZY NORELIA', '094427020', 'F', 'herreralitzy114@gmail.com', 'YOLITA', 'NARCISA DE JESUS Y DOMINGO ELIZALDE', '0997537129', '2004-04-12'),
(368, 0, 'LIBERIO BAJAÑA', 'SCARLET FRANCISCA', 'LIBERIO BAJAÑA SCARLET FRANCISCA', '0927678532', 'F', 'scarletfrancisca04@gmail.com', 'EL TRIUNFO 3RA ETAPA', 'BATALLON DAULE Y GENERAL LEONIDAS PLAZA', '0988366946', '2004-10-04'),
(369, 0, 'ALMEIDA MORENO', 'OMAR XAVIER', 'ALMEIDA MORENO OMAR XAVIER', '0942769076', 'M', 'almoomxa2867342@estudiantes3.edu.ec', 'DAULE', 'RECINTO COLORADO', '0979815742', '2003-11-24'),
(370, 0, 'RONQUILLO BAJAÑA', 'ALEXANDER JESUS', 'RONQUILLO BAJAÑA ALEXANDER JESUS', '0957364698', 'M', '', '', 'VICENTE PIEDRAHITA Y ROCAFUERTE', '967111471', '2007-06-08'),
(371, 0, 'ALVARADO BAJAÑA', 'SERGIO JAREN', 'ALVARADO BAJAÑA SERGIO JAREN', '0943466672', 'M', 'albaseja10682604@estudiantes3.edu.ec', 'DAULE', 'ENTRADA AL VUELTA', '0978783138', '2004-11-17'),
(372, 0, 'SANCHEZ ORTEGA', 'BRIYHANY GYSLAINE', 'SANCHEZ ORTEGA BRIYHANY GYSLAINE', '0931222160', 'F', '', '', 'ANTONIO PARRA', '980257155', '2009-05-13'),
(373, 0, 'ALVAREZ MARTINEZ', 'ARIEL ORESTE', 'ALVAREZ MARTINEZ ARIEL ORESTE', '0954012027', 'M', 'almaaror9052463@estudiantes3.edu.ec', 'NOBOL', 'CALLA PRINCIPAL RIO AMAZONA', '0967295517', '2004-07-23'),
(374, 0, 'SUAREZ RODRIGUEZ', 'KATHERINE DOMENICA', 'SUAREZ RODRIGUEZ KATHERINE DOMENICA', '0950636019', 'F', '', '', 'PATRIA NUEVA', '979864567', '2009-06-10'),
(375, 0, 'TOMALA LOPEZ', 'CHRISTOPHER LIZANDER', 'TOMALA LOPEZ CHRISTOPHER LIZANDER', '0956821904', 'M', '', '', '9 DE OCTUBRE Y PIEDRAHITA', '939875307', '2009-05-07'),
(376, 0, 'BRIONES YUPA', 'OSCAR EDUARDO', 'BRIONES YUPA OSCAR EDUARDO', '0928846187', 'M', 'bryuosed11643230@estudiantes3.edu.ec', 'NARAJAL', 'VIA GRAN DEL MAR', '0995143443', '2003-05-28'),
(377, 0, 'TORRES PLUAS', 'DAMARIS LADY KATHERINE', 'TORRES PLUAS DAMARIS LADY KATHERINE', '1729903391', 'F', '', '', 'MAGRO', '0991535012', '2006-10-03'),
(378, 0, 'UBILLA MARTINEZ', 'LADY KATHERINE', 'UBILLA MARTINEZ LADY KATHERINE', '0943468868', 'F', '', '', 'PATRIA NUEVA', '990474382', '2009-07-18'),
(379, 0, 'ZAMBRANO JAUREGUI', 'DANNYSHA JHEYDI', 'ZAMBRANO JAUREGUI DANNYSHA JHEYDI', '0957712193', 'M', '', '', '', '', '0000-00-00'),
(380, 0, 'FAJARDO CASTAÑEDA', 'GUIDOS TEINER', 'FAJARDO CASTAÑEDA GUIDOS TEINER', '0943035030', 'M', 'facagust3978595@estudiantes3.edu.ec', 'DAULE', 'KM 54 DAULE VIA SANTA LUCIA', '0939282711', '2004-08-28'),
(381, 0, 'FUENTES PEÑAFIEL', 'PEDRO JORDY', 'FUENTES PEÑAFIEL PEDRO JORDY', '0956103493', 'M', 'pepejo3760444@estudiantes3.edu.ec', 'DAULE', 'RECINTO VALDIVIA', '0969867307', '2004-04-17'),
(382, 0, 'GAMBOA GAVILANEZ', 'ALFREDO SEBASTIAN', 'GAMBOA GAVILANEZ ALFREDO SEBASTIAN', '0955915814', 'M', 'gagaalse4168321@estudiantes3.edu.ec', 'DAULE', 'SEÑOR DE LOS MILAGROS Y GALLARDO', '0993915520', '2003-09-26'),
(383, 0, 'JIMENEZ TORRES', 'ANTHONY ARMANDO', 'JIMENEZ TORRES ANTHONY ARMANDO', '0956481030', 'M', 'jitoanar3782790@estudiantes3.edu.ec', 'DAULE', 'CDAL. JUAN BAUTISTA AGUIRRES', '0985561982', '2004-05-22'),
(384, 0, 'TRIANA SUAREZ', 'SERGIO ALEXANDER', 'TRIANA SUAREZ SERGIO ALEXANDER', '0951769652', 'M', 'sergio05triana@gmail.com', 'SAN GABRIEL', 'NOBOL', '0991901207', '2004-07-05'),
(385, 0, 'LAMILLA ORTIZ', 'STEVEN', 'LAMILLA ORTIZ STEVEN', '0957164429', 'M', '', 'SANTA LUCIA', 'BERMEJO  DE ABAJO', '0991441195', '2002-02-18'),
(386, 0, 'LAVALLEN VERA', 'JOSEPH DENNYS', 'LAVALLEN VERA JOSEPH DENNYS', '0942452772', 'M', 'lavejode3875812@estudiantes3.edu.ec', 'DAULE', 'MARIANITA # 5', '0961887252', '2003-05-30'),
(387, 0, 'PAREDES QUINTO', 'KRISTHEL ANAIS', 'PAREDES QUINTO KRISTHEL ANAIS', '0941853509', 'F', 'krisparedes11@gmail.com', 'RCTO BOQUERON', 'VIAS LAS MARAVILLAS', '0997633988', '2004-09-09'),
(388, 0, 'LOPEZ SUAREZ', 'KEVIN', 'LOPEZ SUAREZ KEVIN', '0958728487', 'M', 'losukele3004710@estudiantes3.edu.ec', 'GUAYAQUIL', 'KM 26 VIA A DAULE', '0999634631', '2003-06-23'),
(389, 0, 'MARTINEZ HIDALGO', 'JORDY JAVIER', 'MARTINEZ HIDALGO JORDY JAVIER', '0958886764', 'M', 'mahijoja2602379@estudiantes3.edu.ec', 'DAULE', 'RECINTO SAN GABRIEL DERSVIO LAS CAÑAS', '0939920667', '2005-11-02'),
(390, 0, 'MONTALVAN MACIAS', 'LUIS MIGUEL', 'MONTALVAN MACIAS LUIS MIGUEL', '0957475718', 'M', 'momalumi11054409@estudiantes3.edu.ec', 'DAULE', 'PLAZA BOLIVAR', '0993655307', '2003-11-05'),
(391, 0, 'MURILLO HERRERA', 'JOSTIN ISRAEL', 'MURILLO HERRERA JOSTIN ISRAEL', '0943432690', 'M', 'muhejois7569175@estudiantes3.edu.ec', 'DAULE', '10 DE AGOSTO Y VERNAZA', '0963445039', '2004-05-14'),
(392, 0, 'MURILLO LEON', 'DAVE SAMUEL', 'MURILLO LEON DAVE SAMUEL', '0954759882', 'M', 'muledasa10016242@estudiantes3.edu.ec', 'DAULE', 'VIA A SALITRE KM 21', '0993013488', '2004-10-01'),
(393, 0, 'PEÑAFIEL PACHECO', 'NICASIO GENARO', 'PEÑAFIEL PACHECO NICASIO GENARO', '0929977353', 'M', '', 'DAULE', 'DEVIO LAS CAÑA O SAN ANDRES', '0991677716', '2002-06-19'),
(394, 0, 'QUINTO GOMEZ', 'ANTHONY ALEJANDRO', 'QUINTO GOMEZ ANTHONY ALEJANDRO', '0959429184', 'M', 'qugoanal2866230@estudiantes3.edu.ec', 'DAULE', 'CDLA. PATRIA NUEVA', '0967245862', '2004-08-09'),
(395, 0, 'QUINTO SEGURA', 'PEDRO FERNANDO', 'QUINTO SEGURA PEDRO FERNANDO', '0955900741', 'M', 'qusepefe7409958@estudiantes3.edu.ec', 'LOMA DE SARGENTILLO', 'RECINTO EL PRINCIPE', '0969462761', '2003-10-09'),
(396, 0, 'AGUILERA BANCHÓN', 'GENESIS ELIZABETH', 'AGUILERA BANCHÓN GENESIS ELIZABETH', '0957813884', 'F', 'Madonnacapri8@gmail.com', 'DAULE', 'CALLE 9 DE OCTUBRE Y BAY PASS', '0967623418', '2003-05-29'),
(397, 0, 'ROMERO MONCAYO', 'JORDY ALEJANDRO', 'ROMERO MONCAYO JORDY ALEJANDRO', '0929209732', 'M', '', 'DAULE', 'VIAS LAS MARAVILLA', '0991209710', '2003-09-26'),
(398, 0, 'SALAS BARZOLA', 'CARLOS ANDRES', 'SALAS BARZOLA CARLOS ANDRES', '0941691297', 'M', 'sabacaan2323473@estudiantes3.edu.ec', 'DAULE', 'RECINTO NARANJO', '0979493124', '2004-02-07'),
(399, 0, 'SEGURA OLVERA', 'ANDERSON EFRAIN', 'SEGURA OLVERA ANDERSON EFRAIN', '0958011405', 'M', 'seolanef1860123@estudiantes3.edu.ec', 'LOMA DE SARGENTILLO', 'RECINTO EL PRINCIPE', '0960189036', '2003-07-22'),
(400, 0, 'SEME RUIZ', 'CARLOS JOEL', 'SEME RUIZ CARLOS JOEL', '0929911683', 'M', 'serucajo1886442@estudiantes3.edu.ec', 'LOMAS DE SARGENTILLO', 'BARRIO SAN VICENTE', '0961241100', '2003-05-26'),
(401, 0, 'SOLIS TORRES', 'VICTOR DARLIN', 'SOLIS TORRES VICTOR DARLIN', '0956915607', 'M', 'sotovida2410913@estudiantes3.edu.ec', 'DAULE', 'FLOR DE MARIA', '0968961066', '2004-09-08'),
(402, 0, 'VILLAFUERTE FAJARDO', 'NISTER NATHAEL', 'VILLAFUERTE FAJARDO NISTER NATHAEL', '0941133407', 'M', 'vifanina3947411@estudiantes3.edu.ec', 'LOMA DE SARGENTILLO', 'LOMA DE SARGENTILLO CALLE PRINCIPAL', '0990262213', '2004-01-27'),
(403, 0, 'PACHECO ALVARADO', 'LARITZA JISLEINE', 'PACHECO ALVARADO LARITZA JISLEINE', '0962357497', 'F', '', 'Francisco de Marco y 9 de octubre 	', 'Francisco de Marco y 9 de octubre 	', '0979583805', '2013-10-31'),
(404, 0, 'PAREJA ROMERO', 'LESTER DANIEL', 'PAREJA ROMERO LESTER DANIEL', '0958960445', 'F', '', 'Multicentro Manzana 12 solar 2 x el', 'Multicentro Manzana 12 solar 2 x el upc de la yolita	', '0960538684', '2014-05-19'),
(405, 0, 'PEÑAFIEL SANCHEZ', 'MIKAEL ADRIEL', 'PEÑAFIEL SANCHEZ MIKAEL ADRIEL', '0958912842', 'M', '', 'AV. San francisco.	', 'AV. San francisco.	', '0979501378', '2013-11-02'),
(406, 0, 'PILLASAGUA VILLAMAR', 'ELKYN ANDRES', 'PILLASAGUA VILLAMAR ELKYN ANDRES', '0957946544', 'M', '', 'Assab Bucaram 	', 'Assab Bucaram 	', '0993619054', '2014-01-20'),
(407, 0, 'RAVELO LEON', 'DARLING KRISTIN', 'RAVELO LEON DARLING KRISTIN', '0959301383', 'F', '', 'Olmedo y Francisco de Marco.	', 'Olmedo y Francisco de Marco.	', '0969747554', '2014-03-16'),
(408, 0, 'REYES CHIRIGUAYA', 'DAYERLIN DARIANA', 'REYES CHIRIGUAYA DAYERLIN DARIANA', '0958848400', 'F', '', 'Av Señor de los milagros y gran Colo', 'Av Señor de los milagros y gran Colombia 	', '0993455815', '2014-04-17'),
(409, 0, 'RODRIGUEZ MORAN', 'LADY AITANA', 'RODRIGUEZ MORAN LADY AITANA', '0958673295', 'F', '', 'Av San Francisco CDLA SAN JOSEQQQQ	', 'Av San Francisco CDLA SAN JOSEQQQQ	', '09896221122', '2014-01-30'),
(410, 0, 'ROMERO RIVAS', 'EMANUEL MATIAS', 'ROMERO RIVAS EMANUEL MATIAS', '0959034869', 'F', '', 'Banife calles Balzar y Santa Lucia 	', 'Banife calles Balzar y Santa Lucia 	', '0997179818', '2014-06-07'),
(411, 0, 'RONDAN RONQUILLO', 'LORELEY MADELEY', 'RONDAN RONQUILLO LORELEY MADELEY', '0961404431', 'F', '', 'Belén	', 'Belén	', '0998252164', '2014-07-11'),
(412, 0, 'SESME MORA', 'KEVIN EMANUEL', 'SESME MORA KEVIN EMANUEL', '0958946675', 'M', '', '', '', '', '2014-05-09'),
(413, 0, 'SOLORZANO ROMERO', 'LENIN JESUS', 'SOLORZANO ROMERO LENIN JESUS', '0957620057', 'M', '', 'DOMINGO COMÍN 	', 'DOMINGO COMÍN 	', '', '2013-12-21'),
(414, 0, 'TORO GARCIA', 'ANUSHKA KATRINA', 'TORO GARCIA ANUSHKA KATRINA', '0932450505', 'F', '', 'PEDRO MENEDEZ 	', 'PEDRO MENEDEZ 	', '0939512524', '2011-11-20'),
(415, 0, 'TORRES PLUAS', 'BRENDA ROMINA', 'TORRES PLUAS BRENDA ROMINA', '0959382763', 'F', '', 'Magro atrás de la Escuela Ismael Per', 'Magro atrás de la Escuela Ismael Perez Pazmiño calle 28 de marzo', '0991535012', '2014-06-28'),
(416, 0, 'YANEZ SALAVARRIA', 'JHON ANDERSON', 'YANEZ SALAVARRIA JHON ANDERSON', '0958348591', 'M', '', 'Ciudadela Assad Bucaram 	', 'Ciudadela Assad Bucaram 	', '0990530007', '2014-03-04'),
(417, 0, 'Bajaña Sánchez', 'Ruth Lisbeth', 'Bajaña Sánchez Ruth Lisbeth', '0942773854', 'F', 'isabel-sanche-moreno@hotmail com', 'Daule', '9 DE OCTUBRE Y BY PASS', '0980381868', '2005-02-17'),
(418, 0, 'BRIONES SANCHEZ', 'ALANIS NOHELY', 'BRIONES SANCHEZ ALANIS NOHELY', '0955889108', 'F', 'alanisbriones662@gmail.com', 'BERMEJO', 'SANTA LUCÌA. BERMEJO', '0986628340', '2007-06-12'),
(419, 0, 'FERNANDEZ ALVARADO', 'MILENA JULIRTH', 'FERNANDEZ ALVARADO MILENA JULIRTH', '0951286541', 'F', '', 'PEAJE CHIVERIA', 'BARRIO SAN GUILLERMO, PASANDO PEAJE CHIVERIA', '0979754936', '2006-05-22'),
(420, 0, 'FERRUZOLA SAMANIEGO', 'DAMARIS NICOLLE', 'FERRUZOLA SAMANIEGO DAMARIS NICOLLE', '0940686686', 'F', 'nikolferruzola10@gmail.com', 'PUENTE LUCÍA', 'LOS PINOS PUENTE LUCIA', '0997503632', '2007-07-07'),
(421, 0, 'HUAYAMAVE PRADO', 'MELANY JULETTE', 'HUAYAMAVE PRADO MELANY JULETTE', '0929862647', 'F', 'mayiprado2087@gmail.com', 'DAULE', 'CIELO DE JERUSALEN 2', '0901687322', '2006-05-31'),
(422, 0, 'BARBA NARANJO', 'MARK  ANTHONY', 'BARBA NARANJO MARK  ANTHONY', '0944213131', 'M', 'marktra26@gmail.com', '', 'Av. Las mercedes km24 via Daule', '0992546090', '2004-02-18'),
(423, 0, 'LOZANO DUQUE', 'JOSUE JEFFERSON', 'LOZANO DUQUE JOSUE JEFFERSON', '0958883365', 'M', 'jjferson1025@gmail.com', 'DAULE', 'CDLA. BUCARAM CERCA DE LA CASA DE LA SELECCIÓN', '0962770821', '2008-10-10'),
(424, 0, 'BAJAÑA RODRIGUEZ', 'MIRELLY MITZIBELL', 'BAJAÑA RODRIGUEZ MIRELLY MITZIBELL', '0942760273', 'F', '', 'DAULE', 'AV. SAN FRANCISCO CDLA,. SAN JOSE CERCA DEPOSITO DE CERVEZA', '0961937351', '2007-12-16'),
(425, 0, 'NOBOA LOPEZ', 'ANDERSON LEONEL', 'NOBOA LOPEZ ANDERSON LEONEL', '0957772759', 'M', '', 'DAULE', 'DESVIO DE LAS CAÑAS KM 34 CERCA DE LA PILADORA', '0990274310', '2008-04-02'),
(426, 0, 'LEON LOPEZ', 'MAYTHE ELISABETH', 'LEON LOPEZ MAYTHE ELISABETH', '0959041575', 'F', 'maytheleon2007@gmail.com', 'DAULE', 'MARIANITA 5 CORONEL CAYETANO Y CARLOS LA TORRE DIAGONAL UE DAULE', '0997722859', '2007-10-27'),
(427, 0, 'RODRIGUEZ PEREDO', 'TATIANA ELIZABETH', 'RODRIGUEZ PEREDO TATIANA ELIZABETH', '0950658625', 'F', '', 'GUAYAQUIL', 'KM 26 VIA DAULE COOP LOS ANGELES ENTRANDO POR NUEVA VICTORIA', '0959057178', '2007-10-16'),
(428, 0, 'BUSTAMANTE CASTRO', 'ANDY BERNARDO', 'BUSTAMANTE CASTRO ANDY BERNARDO', '1306962894', 'M', 'castroarteagamariadaniela131@gmail.com', 'KM 24 VIA DAULE', 'RCTO LAS MERCEDES', '0988638762', '2002-07-19'),
(429, 0, 'BONILLA RONQUILLO', 'JORDY GABRIEL', 'BONILLA RONQUILLO JORDY GABRIEL', '0940418981', 'M', 'jordybonilla371@gmail.com', 'EL TRIUNFO', 'DOMINGO COMIN Y MANUEL GONZALEZ.', '0962569866', '2005-05-05'),
(430, 0, 'BUSTAMANTE CASTRO', 'MARELIN KARELIS', 'BUSTAMANTE CASTRO MARELIN KARELIS', '1208277226', 'F', 'karelisbustamante30@gmail.com', 'LAS MERCEDES', 'KM 24 VIA A DAULE', '0983303609', '2004-06-30'),
(431, 0, 'CARRILLO CHAVEZ', 'LUISA MARIA', 'CARRILLO CHAVEZ LUISA MARIA', '0941720534', 'F', '', 'ASSAB BUCARAM', 'BY PASS Y MANUEL VILLAVICENCIO', '0990648605', '2003-09-18'),
(432, 0, 'MORAN RODRIGUEZ', 'ASHLEY DAYANA', 'MORAN RODRIGUEZ ASHLEY DAYANA', '0941801367', 'F', '', 'DAULE', 'CALLE 9 DE OCTUBRE LEONIDAS PLAZA A 2 CUADRAS CLINICA DIALISIS', '0994521959', '2007-11-15'),
(433, 0, 'CASTRO ARTEAGA', 'MARIA DANIELA', 'CASTRO ARTEAGA MARIA DANIELA', '0943171421', 'F', 'castroarteagamariadaniela13@gmail.com', 'KM 24 VIA DAULE', 'KM 24 VIA DAULE', '0985480177', '2003-02-11'),
(434, 0, 'FRANCO JURADO', 'ELSON AUGUSTO', 'FRANCO JURADO ELSON AUGUSTO', '0927672774', 'M', 'francojuradoelsonaugusto@gmail.com', 'RCTO PIÑAL DE ABAJO', 'RCTO PIÑAL DE ABAJO', '0992478392', '2004-02-12'),
(435, 0, 'JIMENEZ GARCIA', 'CAROLINA ALEXANDRA', 'JIMENEZ GARCIA CAROLINA ALEXANDRA', '0944188465', 'F', 'carolaj0905@gmail.com', 'LA YOLITA', 'LA  YOLITA', '0960978034', '2003-04-05'),
(436, 0, 'LEMA CORTEZ', 'XIOMARA NICOLE', 'LEMA CORTEZ XIOMARA NICOLE', '0953757176', 'F', 'Xiomaranicollena18@gmail.com', 'PATRIA NUEVA', 'RODRIGO ROLDAN', '0988543084', '2003-08-22'),
(437, 0, 'MACIAS RONQUILLO', 'JESSEL ANABEL', 'MACIAS RONQUILLO JESSEL ANABEL', '0942279233', 'F', 'Rendenlirda133@gmail.com', 'LA YOLITA', 'OLMEDO ALMEIDA Y JOSE TORRES', '0993260365', '2003-12-10'),
(438, 0, 'MOLINA BARZOLA', 'PEDRO DAVID', 'MOLINA BARZOLA PEDRO DAVID', '0928928340', 'M', 'barzoladavid627@gmail.com', 'CIUDADELA LOS DAULIS', 'DOMINGO COMIN Y LA LIBERTAD', '0968426503', '2003-11-23'),
(439, 0, 'ORTEGA ARTEAGA', 'LUIS ENRRIQUE', 'ORTEGA ARTEAGA LUIS ENRRIQUE', '0942799081', 'M', 'luke29099@outlook.es', 'SANTA LUCIA', 'STA LUCIA', '042709729', '2005-03-29'),
(440, 0, 'LOPEZ MORENO', 'GABRIEL VALENTIN', 'LOPEZ MORENO GABRIEL VALENTIN', '0958697708', 'M', 'morenoortegajosedaniel@gmaIl.com', 'DAULE, CLINICA DE DIALISIS', 'JOSE FELIX HEREDIA Y GNRAL LEONIDAS PLAZA EL TRIUNFO', '0997085312', '2006-11-15'),
(441, 0, 'CAMBA CEDEÑO', 'LIZ MAITHE', 'CAMBA CEDEÑO LIZ MAITHE', '0958091571', 'F', '', 'DAULE', 'PATRIA NUEVA HERMOGENES RIVAS Y JOSE DE LA CUADRA A 2 CUADRAS PJ', '0991327825', '2005-11-01'),
(442, 0, 'SOLORZANO ROMERO', 'MILAGROS LISBETH', 'SOLORZANO ROMERO MILAGROS LISBETH', '0942711250', 'F', '', 'DAULE', 'DOMINGO COMIN ATRAS DEL CEMENTERIO', '0961383748', '2008-09-14'),
(443, 0, 'CORDERO SALAS', 'ADONIS FERNANDO', 'CORDERO SALAS ADONIS FERNANDO', '0942309881', 'M', '', 'DAULE', 'ENTRE ANTONIO H.E. HIPOLITO CAMBA', '967366336', '2005-01-20'),
(444, 0, 'MENDEZ SARCO', 'JANDRY EDISON', 'MENDEZ SARCO JANDRY EDISON', '0942272196', 'M', '', 'TINTAL DE ANIMAS', 'TINTAL DE ANIMAS', '0967815162', '2007-08-10'),
(445, 0, 'CORREA CASTRO', 'ALEXANDER JACINTO', 'CORREA CASTRO ALEXANDER JACINTO', '0955003942', 'M', '', 'ESTACADA', 'RECINTO LA ESTACADA DAULE', '93959887682', '2005-08-14'),
(446, 0, 'ROMAN BAJAÑA', 'ALEXANDRA NARCISA', 'ROMAN BAJAÑA ALEXANDRA NARCISA', '0957585995', 'F', 'alejandranarcisaromanbajaña@gmail.com', 'DAULE', 'MARIANITA 1', '0969850109', '2007-12-11'),
(447, 0, 'CORTEZ PACHECO', 'CARLOS ARIEL', 'CORTEZ PACHECO CARLOS ARIEL', '0942154766', 'M', '', 'ANIMAS CHIGUIJO', 'LAUREL', '961611820', '2006-01-01'),
(448, 0, 'ZAMBRANO NAVARRETE', 'GUADALUPE NAOMI', 'ZAMBRANO NAVARRETE GUADALUPE NAOMI', '0952712255', 'F', '', 'DAULE', 'BOLIVAR Y 2 DE AGOSTO, UNA CUADRA ANTES DEL IESS', '0982373528', '2007-01-24'),
(449, 0, 'CRUZ CHAVEZ', 'IGNACIO ISRAEL', 'CRUZ CHAVEZ IGNACIO ISRAEL', '0941136434', 'M', '', 'NAUPE', 'VIA A LAS CAÑAS ENTRANDO POR LA GABARRA', '0968921778', '2007-10-23'),
(450, 0, 'SANCHEZ CARLOS', 'DANIEL AGUSTIN', 'SANCHEZ CARLOS DANIEL AGUSTIN', '0929984870', 'M', '', 'NAUPE', 'NAUPE ENTRANDO POR LAS CAÑAS', '0979668099', '2007-03-18'),
(451, 0, 'MORAN LUQUE', 'AXEL DIEGO', 'MORAN LUQUE AXEL DIEGO', '0943307538', 'M', 'axelmoran000@gmail.com', 'DAULE', 'Luis Lara Rocafuerte - salida a la perimetral', '0981541669', '2005-07-01'),
(452, 0, 'ORTEGA MERCHAN', 'ANNIE LORELAY', 'ORTEGA MERCHAN ANNIE LORELAY', '0942561671', 'F', 'annykrys1julio@gamail.com', 'RECINTO HUANCHICHAL', 'Huanchichal', '0982950534', '2007-08-21'),
(453, 0, 'Veliz Guerrero', 'Sergio Omar', 'Veliz Guerrero Sergio Omar', '0957321052', 'M', '', 'Daule', '9 de agosto', '0982949972', '2009-02-09'),
(454, 0, 'MURILLO TORRES', 'RUTH NOEMI', 'MURILLO TORRES RUTH NOEMI', '0959384033', 'F', 'proanomurilloalexandrascarlett@gmail.com', 'VALDIVIA', 'RECINTO VALDIVIA', '09968937452', '2007-01-19'),
(455, 0, 'MURILLO TORRES', 'RUTH DOMENICA', 'MURILLO TORRES RUTH DOMENICA', '0956384108', 'F', 'proanomurilloalexandrascarlett@gmail.com', 'VALDIVIA', 'VALDIVIA', '0968937452', '2007-01-19'),
(456, 0, 'MAYA ALVARADO', 'SCARLETH MILAGROS', 'MAYA ALVARADO SCARLETH MILAGROS', '0942807553', 'F', 'milagrosmaya1994@gmail.com', 'DAULE', 'OLMEDO Y GENERAL RUMIÑAHUI', '0981887388', '2007-10-10'),
(457, 0, 'MENDEZ VERA', 'NATASHA IRAIDA', 'MENDEZ VERA NATASHA IRAIDA', '0942734344', 'F', 'natashajimanezvera@gmail.com', 'MARAVILLAS', 'VIA SALITRE. SANTA ROSA', '0962729367', '2006-07-19'),
(458, 0, 'PEÑA TORRES', 'MAYLIN MADELYNE', 'PEÑA TORRES MAYLIN MADELYNE', '0941726465', 'F', 'maylinpetorres@gmail.com', 'RCTO SAN JOSE', 'ESTERO NATO Y PROV.DE AZOGUEZ', '0980191057', '2004-03-24'),
(459, 0, 'TORRES PINCAY', 'ALEXI DANIEL', 'TORRES PINCAY ALEXI DANIEL', '0956276729', 'M', 'pincayalexi79@gmail.com', 'EL RECUERDO', 'VICTOR MANUEL RENDON Y JOAQUIN GALLEGOS LARA', '0968754030', '2007-06-23'),
(460, 0, 'RONQUILLO JARAMILLO', 'BERTHA ELIZABETH', 'RONQUILLO JARAMILLO BERTHA ELIZABETH', '0929984797', 'F', 'elizabethronquillo21@gmail.com', 'RCTO NAUPE', 'RCTO NAUPE', '0991781790', '2005-01-21'),
(461, 0, 'MORENO CHILAN', 'ANDY RICARDO', 'MORENO CHILAN ANDY RICARDO', '0943127290', 'M', 'andymor3421y@gmail.com', 'PUENTE LUCIA', 'KM 27 VIA A DAULE', '0996590355', '2006-10-09'),
(462, 0, 'RONQUILLO TOMALA', 'ANDREINA SHIRLEY', 'RONQUILLO TOMALA ANDREINA SHIRLEY', '0929999902', 'F', 'ronquilloandreina5@gmail.com', 'RCTO NAUPE', 'RCTO NAUPE', '0988068156', '2004-03-02'),
(463, 0, 'RUIZ CASTRO', 'ZULEYKA ANGELINA', 'RUIZ CASTRO ZULEYKA ANGELINA', '0944200708', 'F', 'zr1235060@gmail.com', 'PIÑAL', 'KM 6 Y MEDIO VÌA MAGRO PIÑAL', '099157343', '2007-04-07'),
(464, 0, 'SALAS BARZOLA', 'ANA ROCÍO', 'SALAS BARZOLA ANA ROCÍO', '0929982601', 'F', 'salasanita181@gmail.com', 'Daule', 'CALLE DOMINGO COMIN Y JOSÉ MARIA CAMBA', '0999410417', '2007-01-19'),
(465, 0, 'SANCHEZ PINELA', 'JOHNNY SMITH', 'SANCHEZ PINELA JOHNNY SMITH', '0952444065', 'M', 'js9379248@gmail.com', 'PARROQUIA LAUREL', 'PARROQUIA LAUREL', '0939361063', '2004-09-17'),
(466, 0, 'MORA NAVARRETE', 'JACOB ISMAEL', 'MORA NAVARRETE JACOB ISMAEL', '0930382379', 'M', 'raquel.navarrete1974@gmail.com', 'SANTA LUCIA', 'KM 54 VIA A   SANTA LUCIA', '0980974240', '2007-10-13'),
(467, 0, 'CHOEZ LIBERIO', 'JEREMY JACINTO', 'CHOEZ LIBERIO JEREMY JACINTO', '0939806020', 'M', 'jemychz@hotmail.com', 'SAN JOSE', 'BAY PASS ENTRE BATALLON DAULE', '', '2004-05-06'),
(468, 0, 'JIMENEZ RAVELO', 'NAYELI DAYANNA', 'JIMENEZ RAVELO NAYELI DAYANNA', '0929992758', 'F', 'nayeli21jimenez@gmail.com', 'DAULE', 'GENERAL VERNAZA Y PEDRO CARBO', '0979943855', '2001-09-18'),
(469, 0, 'UBILLA MARTINEZ', 'NAYELI ANDREINA', 'UBILLA MARTINEZ NAYELI ANDREINA', '0943388355', 'F', 'Nayeliubilla64@gmail.com', 'PATRIA NUEVA', 'LOS POZO-LOMA PELADA', '0967322360', '2004-01-13'),
(470, 0, 'VARGAS LEON', 'PIERINA NOHELY', 'VARGAS LEON PIERINA NOHELY', '092767433', 'F', 'Pierinavargas2004@gmail.com', 'DAULE', 'RODRIGO CHAVEZ Y DOMINGO ELIZALDE', '0986163599', '2004-09-26'),
(471, 0, 'BAZURTO AVILES', 'JOSELYNE PRISCILA', 'BAZURTO AVILES JOSELYNE PRISCILA', '0956087985', 'F', 'bazurtojoselyne@gmail.com', 'PASCUALES', 'COOP.LOS ANGELES KM 26 VIA DAULE', '0963655497', '2004-07-19'),
(472, 0, 'Vera Parrales', 'Jahaira Alexandra', 'Vera Parrales Jahaira Alexandra', '0957302078', 'F', 'jahairaveraparrales@gmail.com', 'Daule', '11 de octubre y Juan León Mera MZ 28 SL 2', '0992269249', '2009-02-04'),
(473, 0, 'Ortega Merchán', 'Héctor Josué', 'Ortega Merchán Héctor Josué', '0942561663', 'M', 'krys_contb@hotmail.com', 'Recinto Huachichal', 'Recinto Huachichal', '0968995396', '2009-06-11'),
(474, 0, 'López zurita', 'Darwin snaydher', 'López zurita Darwin snaydher', '0958118192', 'M', 'Darwinlopez010zurita@gmail.com', 'Rcto. La Fabiola', 'Km16 vía salitre', '0939174764', '2009-01-10'),
(475, 0, 'Lamilla Ruíz', 'Erick Javier', 'Lamilla Ruíz Erick Javier', '0957667785', 'M', '', 'Daule', 'Daule', '0985894183', '2009-08-03'),
(476, 0, 'Abad Salas', 'Roberto Carlos', 'Abad Salas Roberto Carlos', '0941800781', 'M', 'abadjenny457@gmail.com', 'La Yolita', 'Homero Espinoza y  Galo Plaza Lasso', '0969122551', '2005-10-04'),
(477, 0, 'Ramos Cedeño', 'Juliana Joely', 'Ramos Cedeño Juliana Joely', '0931442081', 'F', 'Julianitaramos289@gmail.com', 'Petrillo', 'Petrillo Km 30 vía daule', '0968100173', '2009-05-01'),
(478, 0, 'Coloma Ronquillo', 'Narcisa Jamel', 'Coloma Ronquillo Narcisa Jamel', '0931318232', 'F', 'virgikatherine@hotmail.com', 'Daule', 'AV. Domingo Comin Manuel Gonzales', '0994720835', '2009-09-10'),
(479, 0, 'Piguave Vargas', 'Luis Enrique', 'Piguave Vargas Luis Enrique', '0953326014', 'M', 'maria.vargas.selda@hotmail.com', 'Daule', 'Domingo Can.Real. MZ.159.', '0999405065', '2009-03-02'),
(480, 0, 'García Herrera', 'Sergi Daniel', 'García Herrera Sergi Daniel', '0942793837', 'M', 'herreraevelin081@gmail.com', 'Patria Nueva', 'Antes de llegar a la PJ mano izquierda', '0989699396', '2009-02-25'),
(481, 0, 'Chiriguaya Chiriguaya', 'Jandry Manuel', 'Chiriguaya Chiriguaya Jandry Manuel', '0942728775', 'M', 'Jandrychiriguaya77@gmail.com', 'Daule', 'KM 16 via Guayaquil salitre', '0968170781', '2009-06-15'),
(482, 0, 'Sanchez Realpe', 'Raul Maximiliano', 'Sanchez Realpe Raul Maximiliano', '0941617847', 'M', 'maxrealpe246@gmail.com', 'Cdla. El Triunfo', 'Rocafuerte y Luis Lara Lara', '0978918497', '2008-01-29'),
(483, 0, 'FRANCO SALAVARRIA', 'NEY MATHIAS', 'FRANCO SALAVARRIA NEY MATHIAS', '0959022203', 'M', 'neyfranco2611s@gmail.com', 'Recinto los Ángeles', 'Recinto los Ángeles', '0980457167', '2008-11-26'),
(484, 0, 'NARANJO ALEJANDRO', 'MIGUEL ANTHONY', 'NARANJO ALEJANDRO MIGUEL ANTHONY', 'O942782921', 'M', '', 'Jose veliz y 9 de octubre', 'Daule', '099200400101', '2008-08-22'),
(485, 0, 'ARCENTALES ESPINOZA', 'LUIS MARIO EFREN', 'ARCENTALES ESPINOZA LUIS MARIO EFREN', '0957572449', 'M', 'leonardoam_1975@hotmail.com', 'Daule', 'CALLES MALECON Y QUITO', '0991594224', '2008-12-12'),
(486, 0, 'Nieto Caicedo', 'Jimena Gabriela', 'Nieto Caicedo Jimena Gabriela', '0953130796', 'F', 'Neynieto2021@gmail.com', 'Daule', 'Olmedo y abdon calderon', '0969134103', '2008-12-29'),
(487, 0, 'Moran Gomez', 'Analia Elizabeth', 'Moran Gomez Analia Elizabeth', '0941856379', 'F', 'Diana Elizabeth gomez choez223@gmail.com', 'Recinto rinconada daule', 'Recinto rinconada daule', '0939849089', '2009-05-01'),
(488, 0, 'Sanchez Caleño', 'Ariel Oswaldo', 'Sanchez Caleño Ariel Oswaldo', '0959423047', 'M', 'Almeidaomar725@gmail.com', 'Recinto colorado', 'Recinto colorado', '0968489299', '2009-06-27'),
(489, 0, 'Sanchez Navarrete', 'Yorgelis Yeinimar', 'Sanchez Navarrete Yorgelis Yeinimar', '096090958', 'F', 'Yeninavarreter@gmail.com', 'Banife', 'Camilo ponce y carlos cevallos', '0983634782', '2009-02-19'),
(490, 0, 'Saldaña Barzola', 'Scarlet Mayte', 'Saldaña Barzola Scarlet Mayte', '0955850870', 'F', '', 'Banife', 'Justo torres y marta bucaram', '0980059977', '2009-11-10'),
(491, 0, 'CARABAJO GOMEZ', 'JHON HENRY', 'CARABAJO GOMEZ JHON HENRY', '0958066631', 'M', 'jhonjhenrycarabajogomez@gmail.com', 'PUENTE LUCIA', 'COOP. LOS PINOS KM 26 VIA A DAULE', '0982273273', '2007-01-19'),
(492, 0, 'CARABAJO GOMEZ', 'DILADY MICHELLE', 'CARABAJO GOMEZ DILADY MICHELLE', '0958066581', 'F', 'diladymichelle2007@gmail.com', 'GUAYAQUIL', 'COLINAS DE LA ALBORADA', '0959520906', '2007-01-19'),
(493, 0, 'CHIRIGUAYA SILVA', 'KEILLY YAMILETH', 'CHIRIGUAYA SILVA KEILLY YAMILETH', '0956218036', 'F', '2019julyamparo@gmail.com', 'DAULE', '9 DE OCTUBRE Y JUAN LEON MERA', '0989094381', '2007-01-17'),
(494, 0, 'ESPINOZA TORRES', 'VALERIA DAMARIS', 'ESPINOZA TORRES VALERIA DAMARIS', '0956432769', 'F', '', 'DAULE, EL RECUERDO', 'MISAEL ACOSTA Y 9 DE OCTUBRE', '0985996484', '2006-06-02'),
(495, 0, 'ZAMBRANO RUIZ', 'MADELINE NAOMY', 'ZAMBRANO RUIZ MADELINE NAOMY', '0958789216', 'F', 'madelinezambrano881@gmail.com', 'NAUPE', 'MAS ADELANTE DE PIÑAL', '0989855283', '2007-05-11'),
(496, 0, 'CLAVIJO PINELA', 'LUIS FERNANDO', 'CLAVIJO PINELA LUIS FERNANDO', '0930048204', 'M', 'luis_cp2008@hotmail.com', 'LOMAS DE SARGENTILLO', 'DAULE Y ELOY ALFARO SECTOR 2 DE MAYO', '0961977681', '2007-12-30'),
(497, 0, 'NUMERABLE BUENO', 'NAYELY SOFIA', 'NUMERABLE BUENO NAYELY SOFIA', '', 'F', 'Antonelita1996cc@gmail.com', 'EL MANGLE. SANTA LUCIA', 'KM 55 VIA DAULE SANTA LUCÍA', '0939787233', '2005-12-03'),
(498, 0, 'CANDELARIO MEDINA', 'JEREMY DE JESUS', 'CANDELARIO MEDINA JEREMY DE JESUS', '0956593107', 'M', 'jeremycandelario74@gmail.com', 'DAULE', 'PEDRO ISAIAS 1ERA ETAPA', '0981374999', '2006-05-21'),
(499, 0, 'Acosta Navarrete', 'Milton omar', 'Acosta Navarrete Milton omar', '0957663263', 'M', '', 'Daule', 'Domingo comin', '0981323036', '2007-12-04'),
(500, 0, 'CORNEJO BARZOLA', 'NARCISA FERNANDA', 'CORNEJO BARZOLA NARCISA FERNANDA', '0943043604', 'F', '', 'DAULE', 'RIBERAS OPUESTAS: SECTOR LAS PLAYITAS', '0969258438', '2005-04-21'),
(501, 0, 'CORNEJO BARZOLA', 'DOMENICA VICTORIA', 'CORNEJO BARZOLA DOMENICA VICTORIA', '0959432014', 'F', '', 'DAULE', 'RIBERAS OPUESTAS: SECTOR LAS PLAYITAS', '0969258438', '2007-02-01'),
(502, 0, 'PEÑA RUGEL', 'ANGEL GABRIEL', 'PEÑA RUGEL ANGEL GABRIEL', '0941801201', 'M', '', 'DAULE', 'ATRAS DE LA IGLESIA SAN FRANCISCO', '0969786588', '2008-04-07'),
(503, 0, 'FLORES RODRIGUEZ', 'HILLARY TATIANA', 'FLORES RODRIGUEZ HILLARY TATIANA', '0941801318', 'F', 'hillaryfr@gmail.com', 'DAULE', 'ASSAD BUCARAM', '0982828962', '2008-12-09'),
(504, 0, 'Alvarado Quinto', 'Cristhian Kevin', 'Alvarado Quinto Cristhian Kevin', '0957129547', 'M', 'cristhianalvarado457@gmail.com', 'El Recuerdo', 'Olmedo y  General  Rumiñahui', '0991758979', '2005-10-01'),
(505, 0, 'SANCHEZ CALEÑO', 'MILEY PAMELA', 'SANCHEZ CALEÑO MILEY PAMELA', '0943041145', 'F', '', 'Daule', 'Recinto colorado', '0968489299', '2008-06-24'),
(506, 0, 'ORTIZ RENDON', 'JEAN PABLO', 'ORTIZ RENDON JEAN PABLO', '1208778827', 'M', 'eljeyckell29@gmail.com', 'Daule', 'Recinto Flor de Maria', '0986151788', '2004-06-29'),
(507, 0, 'ASCANIO DIAZ', 'JHOAN ALEJANDRO', 'ASCANIO DIAZ JHOAN ALEJANDRO', '32536847', 'M', 'jhoanascanio58@gmail.com', 'Daule', 'Señor de los Milagros y Gallegos LA', '0969239885', '2007-01-08'),
(508, 0, 'Alvarado Sanchez', 'Cristina Elizabeth', 'Alvarado Sanchez Cristina Elizabeth', '0953482783', 'F', 'criceli25alvarado@hotmail.com', 'Daule', 'Cdla. Yolita Leonidas Proaño entre Gabriel Garcia. M. y Luis U.', '2- 798708', '2008-04-25'),
(509, 0, 'ROMAN GOMEZ', 'SERGIO ISMAEL', 'ROMAN GOMEZ SERGIO ISMAEL', '0929999589', 'M', 'Ismaelroman615@gmail.com', 'Daule', 'Riberas Opuestas', '0967043043', '2008-07-01'),
(510, 0, 'TORRES LOOR', 'SCARLETH ELIZABETH', 'TORRES LOOR SCARLETH ELIZABETH', '0942153271', 'F', '', 'Daule', 'Rct: San Gabriel', '0959682427', '2008-09-26'),
(511, 0, 'PAREDES BUENO', 'SEGUNDO ISIDRO', 'PAREDES BUENO SEGUNDO ISIDRO', '0956310155', 'M', '', 'Daule', 'Sector Los Alamos', '0939065664', '2005-09-14'),
(512, 0, 'HERRERA MOREIRA', 'JORGE ROLANDO', 'HERRERA MOREIRA JORGE ROLANDO', '0943053793', 'M', '', 'Daule', 'Av. Piedrahita Mz. 486', '', '2007-11-28'),
(513, 0, 'RIVAS RUIZ', 'ALEX EDUARDO', 'RIVAS RUIZ ALEX EDUARDO', '0958674558', 'M', '', 'Daule', 'Victor Manuel Rendón', '', '2007-04-13'),
(514, 0, 'SUAREZ CHIRIGUAYA', 'JOSTIN JOSUE', 'SUAREZ CHIRIGUAYA JOSTIN JOSUE', '0958660425', 'M', '', 'Daule', 'Justo Torres y Assad Bucaram', '', '2008-03-04'),
(515, 0, 'RODRIGUEZ CASTRO', 'JESUS CRISTOBAL', 'RODRIGUEZ CASTRO JESUS CRISTOBAL', '0942015561', 'M', '', 'Daule', 'Via Daule- Nobol (Magro)', '', '2008-10-20'),
(516, 0, 'SILVA VILLAMAR', 'NORELYS CHARLOT', 'SILVA VILLAMAR NORELYS CHARLOT', '0943582692', 'F', '', 'Daule', 'La Clemencia', '', '2009-09-02'),
(517, 0, 'VARGAS LEON', 'VALESKA VALENTINA', 'VARGAS LEON VALESKA VALENTINA', '0941804932', 'F', '', 'Daule', '', '', '2007-12-19'),
(518, 0, 'VILLARREAL PEÑAFIEL', 'JULISSA LISBETH', 'VILLARREAL PEÑAFIEL JULISSA LISBETH', '0943573642', 'F', '', 'Daule', '', '', '0000-00-00'),
(519, 0, 'VEINTIMILLA MUÑIZ', 'ALEX SANTIAGO', 'VEINTIMILLA MUÑIZ ALEX SANTIAGO', '0953219235', 'M', '', 'Daule', '', '', '0000-00-00'),
(520, 0, 'ASPIAZU MENDOZA', 'NARCISA ARYSLEY', 'ASPIAZU MENDOZA NARCISA ARYSLEY', '0932454184', 'F', '', '', '', '', '0000-00-00'),
(521, 0, 'BAJAÑASANCHEZ', 'GABRIELA GUADALUPE', 'BAJAÑASANCHEZ GABRIELA GUADALUPE', '0941851818', 'F', '', '', '', '', '0000-00-00'),
(522, 0, 'CARABAJO GOMEZ', 'MIGUEL ANGEL', 'CARABAJO GOMEZ MIGUEL ANGEL', '0953273166', 'M', '', '', '', '', '0000-00-00'),
(523, 0, 'RUIZ RUIZ', 'ANTHONY JOSUE', 'RUIZ RUIZ ANTHONY JOSUE', '0958437949', 'M', '', '', '', '', '0000-00-00'),
(524, 0, 'SANCHEZ GARCIA', 'PIERINA BETZAIDA', 'SANCHEZ GARCIA PIERINA BETZAIDA', '0942564139', 'F', '', 'Daule', '', '', '0000-00-00'),
(525, 0, 'SANCHEZ SAN LUCAS', 'NICOLE ANAHIS', 'SANCHEZ SAN LUCAS NICOLE ANAHIS', '0943029751', 'F', '', 'Daule', '', '', '0000-00-00'),
(526, 0, 'SEMINARIO VILLAMAR', 'JONAYKER JAHEL', 'SEMINARIO VILLAMAR JONAYKER JAHEL', '1727201814', 'M', '', '', '', '', '0000-00-00'),
(527, 0, 'SESME ALVARADO', 'ALBERTO ALEXANDER', 'SESME ALVARADO ALBERTO ALEXANDER', '0959407412', 'M', '', '', '', '', '0000-00-00'),
(528, 0, 'UBILLA VELASQUEZ', 'GABRIEL ANTONIO', 'UBILLA VELASQUEZ GABRIEL ANTONIO', '0957827728', 'M', '', '', '', '', '0000-00-00'),
(529, 0, 'VILLAMAR BOHORQUEZ', 'MICHAEL ADRIAN', 'VILLAMAR BOHORQUEZ MICHAEL ADRIAN', '0959142126', 'M', '', 'Daule', '', '', '0000-00-00'),
(530, 0, 'ZAMBRANO BERMUDEZ', 'IBSON MOISES', 'ZAMBRANO BERMUDEZ IBSON MOISES', '1729369833', 'M', '', '', '', '', '0000-00-00'),
(531, 0, 'Cortez Cacao', 'Bismark Jostin', 'Cortez Cacao Bismark Jostin', '0942303017', 'M', 'maivelincortez@hotmail.com', 'Daule', 'Francisco huerta rendon y vicente pino moran', '0939341998', '2008-07-30'),
(532, 0, 'Gaspar Perez', 'Mirela Anais', 'Gaspar Perez Mirela Anais', '0941800989', 'F', 'pamela.gaspar1911@gmail.com', 'Daule', 'Km. 41 via Daule frente a vulcanizadora Bemba', '0994493573', '2008-09-17'),
(533, 0, 'Herrera Murillo', 'Dafne Valeska', 'Herrera Murillo Dafne Valeska', '0940228059', 'F', 'josty_xavier2011@hotmail.com', 'Daule', 'Calle piedrahita MZ25 SL23 D diagonal al Cementerio general', '2- 119005', '2008-05-31'),
(534, 0, 'García Saldarriaga', 'Jostin José', 'García Saldarriaga Jostin José', '0942779067', 'M', 'jostingarciasaldarriaga10@hotmail.com', 'Belén', 'Av. San  Francisco y  García Moreno', '0963221114', '2002-01-17'),
(535, 0, 'Mora Merelo', 'Diego José', 'Mora Merelo Diego José', '0957500911', 'M', 'diegomerelo29@outlook.com', 'El Mate.Sta Lucia', 'Via Santa Clara Barrio Paraiso', '0982902683', '2004-10-24'),
(536, 0, 'Pozo Veloz', 'Abel Iván', 'Pozo Veloz Abel Iván', '0941747198', 'M', '', 'Cocal', 'La T vía Salitre', '0989641964', '2003-02-11'),
(537, 0, 'Ruiz Bajaña', 'Julio Efraín', 'Ruiz Bajaña Julio Efraín', '0957130446', 'M', 'juniorruba19@gmail.com', 'San José', 'Homero Espinoza Rendón Y Miguel Letamendi', '0999550337', '2005-05-19'),
(538, 0, 'Ruiz Guaranda', 'Lilibeth Dayana', 'Ruiz Guaranda Lilibeth Dayana', '0942600842', 'F', 'lilibethr792@gmail.com', 'Brisas del Daule', 'Rcto.Brisas Del Daule Via A Naupe', '0959803477', '2005-04-02'),
(539, 0, 'Zambrano Reliche', 'José Luis', 'Zambrano Reliche José Luis', '0941899874', 'M', 'jose.cnt007@gmail.com', 'El Laurel', 'Sector 2. Arcadia Espinoza', '0969625894', '2005-07-07'),
(540, 0, 'Barzola Mora', 'José Francisco', 'Barzola Mora José Francisco', '0958348997', 'M', 'josebarzola197@gmail.com', 'San Francisco', 'José Domingo Elizalde', '0939175147', '2004-06-10'),
(541, 0, 'León Salazar', 'Matheu Aimar', 'León Salazar Matheu Aimar', '0958816548', 'M', 'mathiuleonsalazar@gmail.com', 'Sta. Clara .Independencia', 'Piedrahita y Pedro Carbo M4  SL 24', '0987453070', '2005-07-10'),
(542, 0, 'BONILLA TOMALA', 'NATHALY CECIBEL', 'BONILLA TOMALA NATHALY CECIBEL', '0941134769', 'F', 'nathalybonilla01@hotmail.com', 'DAULE', 'RECINTO NAUPE. VIA A CLARISA ENTRADA DESVIO A LAS CAÑAS', '0991226130', '2004-05-04'),
(543, 0, 'CAMPIUZANO CERVANTES', 'STEFANIA MARLENE', 'CAMPIUZANO CERVANTES STEFANIA MARLENE', '1729868453', 'F', '', 'DAULE', '10 DE AGOSTO', '', '2005-04-13'),
(544, 0, 'Mantuano Gomez', 'Melany Milagros', 'Mantuano Gomez Melany Milagros', '0929984284', 'F', '', 'Daule', 'Recinto Pajonal por la capilla Sagrado corazon de Jesus', '0939688284', '2008-01-28'),
(545, 0, 'Parra Chuqui', 'Marc Anthony', 'Parra Chuqui Marc Anthony', '1250838529', 'M', '', 'Daule', 'Km 27 via Daule sector puente lucia Coop. Los pinos', '0967855075', '2005-05-24'),
(546, 0, 'Espinoza Mota', 'Ricardo Samuel', 'Espinoza Mota Ricardo Samuel', '0958195521', 'M', 'eudosiamotasalvarria@hmail.com', 'Daule', 'Leonidas proaño y homero espinoza MZ88 SL18', '0986386830', '2008-06-12');
INSERT INTO `sw_estudiante` (`id_estudiante`, `es_nro_matricula`, `es_apellidos`, `es_nombres`, `es_nombre_completo`, `es_cedula`, `es_genero`, `es_email`, `es_sector`, `es_direccion`, `es_telefono`, `es_fec_nacim`) VALUES
(547, 0, 'Bajaña Suarez', 'Alex Adrian', 'Bajaña Suarez Alex Adrian', '0943949727', 'M', 'alex-andreina@hotmail.com', 'Daule', 'Calle Santa Lucia y Justo Torres', '0960270460', '2008-07-10'),
(548, 0, 'Castro Banchon', 'Ginnette Eileen', 'Castro Banchon Ginnette Eileen', '0958677718', 'F', '', 'Daule', 'Cdla. Enrique Gil Gilbert calle Bolivar y Gnral. Crespin cerezo', '0978921707', '2008-04-25'),
(549, 0, 'Chele Chica', 'Daniela Jungsuh', 'Chele Chica Daniela Jungsuh', '095778959', 'F', 'wchicasoto@gmail.com', 'Daule', 'Km 27 via Daule puente lucia Coop. Los pinos', '0985867317', '2007-11-22'),
(550, 0, 'Pluas Leon', 'Ariel Guillermo', 'Pluas Leon Ariel Guillermo', '0958655813', 'F', '', 'Daule', 'Ernesto castro y Santa lucia', '0989449531', '2008-06-30'),
(551, 0, 'Navas Leon', 'Andy Axel', 'Navas Leon Andy Axel', '0959224460', 'M', 'leonfer1983@hotmail.com', 'Daule', 'Km. 47 Via daule frente a la escuela Veintiocho de Julio', '096376612', '2005-11-09'),
(552, 0, 'Vargas Sánchez', 'Elkin Agustin', 'Vargas Sánchez Elkin Agustin', '1315163483', 'M', 'elkin.vargas@ueamazonico.edu.ec', 'Nobol', 'Nobol', '0989269126', '2005-08-28'),
(553, 0, 'CRUZ MARTILLO', 'DERLYS ADEMIR', 'CRUZ MARTILLO DERLYS ADEMIR', '0929994259', 'M', '', '', 'LOTIZACION LONARZAN', '990783017', '2006-03-04'),
(554, 0, 'ELOY GARCIA', 'ANDY JEAMPOOL', 'ELOY GARCIA ANDY JEAMPOOL', '0944208016', 'M', '', '', 'GALO PLAZA Y 26 DE NOVIEMBRE', '098 155 5015', '2005-10-10'),
(555, 0, 'ELOY GARCIA', 'JOSTIN STEVEN', 'ELOY GARCIA JOSTIN STEVEN', '0943690529', 'M', '', 'DAULE', 'GALO PLAZA Y 26 DE NOVIEMBRE', '0 98 155 5015', '2006-10-15'),
(556, 0, 'FRANCO CANTOS', 'BYRON ALEXI', 'FRANCO CANTOS BYRON ALEXI', '0959379934', 'M', '', '', '', '', '0000-00-00'),
(557, 0, 'FRANCO LINO', 'DUSTIN GILMAR', 'FRANCO LINO DUSTIN GILMAR', '0929984664', 'M', '', 'DAULE', '', '0969660170', '0000-00-00'),
(558, 0, 'FRANCO RUIZ', 'ELKIN ANDRES', 'FRANCO RUIZ ELKIN ANDRES', '0958410847', 'M', '', 'CDLA. SAN FRANCISCO HOMERO ESPINOZA', 'CDLA. SAN FRANCISCO HOMERO ESPINOZA', '969293911', '2006-09-16'),
(559, 0, 'GUILLEN QUIROZ', 'ADRIAN JEAMPIERRE', 'GUILLEN QUIROZ ADRIAN JEAMPIERRE', '0927674966', 'M', '', 'DAULE', 'DAULE', '0985695944', '0000-00-00'),
(560, 0, 'GURUMENDI QUIJIJE', 'CRISTHIAN JOEL', 'GURUMENDI QUIJIJE CRISTHIAN JOEL', '0944192490', 'M', 'stefaniaquije88@gmail.com', 'AV.JOAQUIN GALLEGOS LARA', 'AV.JOAQUIN GALLEGOS LARA', '0984742045', '2005-08-17'),
(561, 0, 'HERRERA SALAZAR', 'JORGE LEONARDO', 'HERRERA SALAZAR JORGE LEONARDO', '0929922748', 'M', '', 'SABANILLA  PEDRO CARBO', '10 DE AGOSTO Y CALLEJON SIN NOMBRE', '0991600251', '2005-05-12'),
(562, 0, 'HOLGUIN CHILA', 'JOSTIN GABRIEL', 'HOLGUIN CHILA JOSTIN GABRIEL', '0942015355', 'M', 'yostinchila90@gmail.com', 'GUAYAS TARQUI', 'KM23 VIA DAULE LAGO DE CAPEIRA', '0990716256', '2006-01-02'),
(563, 0, 'HUACON RUIZ', 'HERNAN JOSUE', 'HUACON RUIZ HERNAN JOSUE', '0957392210', 'M', '', 'LOS LOJA LA ESTACADA', 'KM23 VIA DAULE', '0980846439', '2005-08-18'),
(564, 0, 'INTRIAGO MOREIRA', 'CRISTHIAN WILLIANS', 'INTRIAGO MOREIRA CRISTHIAN WILLIANS', '1350583660', 'M', '', '', '', '', '0000-00-00'),
(565, 0, 'JIMENEZ GOMEZ CUELLO', 'EDIE SANTIAGO', 'JIMENEZ GOMEZ CUELLO EDIE SANTIAGO', '0943418160', 'M', '', 'LOMAS DE SARGENTILLO', 'CIUDADELA 1RO DE MAYO', '0994948492', '2006-12-12'),
(566, 0, 'LEON CHAVEZ', 'LUIS CARLOS', 'LEON CHAVEZ LUIS CARLOS', '0943575936', 'M', '', '', '', '', '0000-00-00'),
(567, 0, 'LEON FREIRE', 'CRISTA SHARIK', 'LEON FREIRE CRISTA SHARIK', '0959448317', 'F', '', 'GUAYAS DAULE', 'CDLA. BELEN   AVN  SAN FRANCISCO 26 DE NOVIEMBRE', '959751685', '2006-12-04'),
(568, 0, 'LOY ROMERO', 'HAMILTON STAYNER', 'LOY ROMERO HAMILTON STAYNER', '0956724793', 'M', '', 'PUENTE LUCIA', 'KM 27 VIA DAULE', '969990236', '2006-03-16'),
(569, 0, 'MACIAS MONTENEGRO', 'JOEL ALEXANDER', 'MACIAS MONTENEGRO JOEL ALEXANDER', '969990236', 'M', 'joelalexandermaciasmontenegro@gmail.com', 'LOS RIOS- VINCES', 'PALESTINA VIA SAN  JACINTO', '967546443', '2006-08-04'),
(570, 0, 'MAGALLANES VERA', 'HECTOR GERARDO', 'MAGALLANES VERA HECTOR GERARDO', '0929977262', 'M', 'hestormag_1975@hotmail.com', 'DAULE', 'RIO PERDIDO', '961070334', '2006-06-28'),
(571, 0, 'MEGIA ZAMBRANO', 'JESUS AGUSTIN', 'MEGIA ZAMBRANO JESUS AGUSTIN', '0959386608', 'M', '', 'LA ESQUINA DEL SABOR', 'AV. SAN FRANCISCO', '939338540', '2006-04-26'),
(572, 0, 'MONTOYA SANTANA', 'JOSE ANGEL', 'MONTOYA SANTANA JOSE ANGEL', '0959376260', 'M', '', 'PATRIA NUEVA', 'PATRIA NUEVA, JOSE DE LA CUADRA', '0968900561', '2007-03-16'),
(573, 0, 'MORAN MAGALLANES', 'KEVIN DARIO', 'MORAN MAGALLANES KEVIN DARIO', '0942306713', 'M', '', '', '', '0968907415', '0000-00-00'),
(574, 0, 'PENAHERRERA SEGURA', 'NIXON JOSUE', 'PENAHERRERA SEGURA NIXON JOSUE', '0942744202', 'M', '', 'RECINTO SAN GABRIEL', 'KM 42 VIA A DAULE RCTO SAN GABRIEL', '0963747286', '2006-08-03'),
(575, 0, 'Salas Bajaña', 'David Daniel', 'Salas Bajaña David Daniel', '0954932901', 'M', 'Fabiolabb1887@gmail.com', 'Coop Nueva victoria', 'Km26via daule', '0954932901', '2006-02-28'),
(576, 0, 'Veintimila Muñiz', 'Javier Alejandro', 'Veintimila Muñiz Javier Alejandro', '0953219359', 'M', 'jennymargarita23@gmail.com', 'San Francisco', 'Km 16 Vía Daule Y Av Francisco Jiménez Cano', '0980103092', '2006-09-16'),
(577, 0, 'Almeida vera', 'Andy Ernesto', 'Almeida vera Andy Ernesto', '0950762989', 'M', 'andyalmeida570@gmail.com', 'Sector#6', 'Laurel sector 6', '0987436331', '2006-07-01'),
(578, 0, 'Piguave alvarado', 'Elkin patricio', 'Piguave alvarado Elkin patricio', '0941805863', 'M', 'Elkinpiguave458@gmail.com', 'Ciudadela assad bucaram', 'Daule', '0980234106', '2006-08-26'),
(579, 0, 'Tomala López', 'Elkin Javier', 'Tomala López Elkin Javier', '0958760548', 'M', '', 'San Pablo', '9 de octubre y piedrahita', '0939875307', '2206-10-11'),
(580, 0, 'Molina Basurto', 'Cristhoper Josue', 'Molina Basurto Cristhoper Josue', '0953544400', 'M', 'Cristophermolina32@gmail.com', 'La vuelta', 'Recinto  la  vuelta', '0986591884', '2006-06-03'),
(581, 0, 'Quintero Quinto', 'Bianca Naomi', 'Quintero Quinto Bianca Naomi', '0942506882', 'M', 'Linabquinto13@gmail.com', 'Yolita', 'Linabquinto13@gmail.com', '0961348118', '2006-06-14'),
(582, 0, 'Ruiz bonilla', 'Ariel Alexander', 'Ruiz bonilla Ariel Alexander', '0958750333', 'M', 'ruizbonilla05ariel@gmail.com', 'Recinto brisas del daule', 'Recinto brisas del daule', '0997009768', '2004-10-05'),
(583, 0, 'Villamar Méndez', 'Jean Carlos', 'Villamar Méndez Jean Carlos', '0958507550', 'M', 'mendezsandy281@gmail.com', 'Recinto tintal de aminas', 'Recinto tintal', '0936970107', '2006-03-04'),
(584, 0, 'Ronquillo Gavilanes', 'Jonathan Omar', 'Ronquillo Gavilanes Jonathan Omar', '0929984573', 'M', 'geomayragavilanes27@gmail.com', 'Recinto Peninsula de Animas', 'Vía daule Santa Lucia', '0997729568', '2006-09-09'),
(585, 0, 'Navas Magallanez', 'José luis', 'Navas Magallanez José luis', '0942715921', 'M', 'luisnavasmagallanes@gmail.com', 'Cerca del comercial Steveen', 'Rocafuerte y la tercera tribu chonana', '0993057465', '2006-02-01'),
(586, 0, 'Villafuerte Briones', 'Cristoffer Ronny', 'Villafuerte Briones Cristoffer Ronny', '0955544093', 'M', 'cristofferronnyvillafuertebrio@gmail.com', 'Calle 12 de mayo/ frente a la unidad', 'Las cañas', '0993958129', '2006-09-01'),
(587, 0, 'Peñafiel Estrella', 'Elkin Josue', 'Peñafiel Estrella Elkin Josue', '0942271925', 'M', 'Josuepenafiel20@gmail.com', 'Sector San Lorenzo', 'Lomas de Sargentillo', '0988310114', '2006-10-20'),
(588, 0, 'Salavarria Cortez', 'Christopher Emanuel', 'Salavarria Cortez Christopher Emanuel', '0942012683', 'M', 'marcelasalavarria1701@gmail.com', 'Entrada por desvío a laurel frente a', 'Recinto los Ángeles', '0999129903', '2004-12-02'),
(589, 0, 'Torres moncayo', 'Alexis Jacob', 'Torres moncayo Alexis Jacob', '0954982229', 'M', 'alexistorres@213gmail.com', 'patria nueva', 'Hipólito camba y Manuel de fas', '0986662328', '2006-09-18'),
(590, 0, 'Moran Holguín', 'KENY DANIEL', 'Moran Holguín KENY DANIEL', '0958722076', 'M', 'morankenny106@gmail.com', 'La victoria', 'Recinto la victoria', '0988945808', '2005-11-27'),
(591, 0, 'Villamar benalcazar', 'Enrique ricardo', 'Villamar benalcazar Enrique ricardo', '958659872', 'M', 'Rickardito2006@gmail.com', 'Por la maternidad', 'Por la maternidad', '0961103053', '2006-11-15'),
(592, 0, 'Paredes lamilla', 'Iker alejandro', 'Paredes lamilla Iker alejandro', '0929999498', 'M', 'Iker05paredes@gmail.com', 'Banife', 'Martha bucaram y pedro menendez', '0963914676', '2006-07-05'),
(593, 0, 'Morante Briones', 'Braulio Rafael', 'Morante Briones Braulio Rafael', '1250612601', 'M', 'morantebraulio427@gmail.com', 'Via salitre', 'Las maravillas', '0990851338', '2004-11-28'),
(594, 0, 'Plúas Quijije', 'Marvin Francisco', 'Plúas Quijije Marvin Francisco', '0941896714', 'M', 'marvinpluas750@gmail.com', 'La Estancia', 'Sector Plan América', '0993128027', '2006-07-05'),
(595, 0, 'Cercado Alvarado', 'Steven Danilo', 'Cercado Alvarado Steven Danilo', '0927971051', 'M', 'cealstda2114268@ueamazonico.edu.ec', '', 'Huanchichal', '.0985781764', '2006-05-19'),
(596, 0, 'Carranza Pino', 'Dennis Alberto', 'Carranza Pino Dennis Alberto', '0959855693', 'M', 'capideal5298308@ueamazonico.edu.ec', 'GUYAS DAULE', '.. Cdla.Asad Buscarán..calle Jose Maria egas', '0990090437', '2004-05-04'),
(597, 0, 'Castro Alban', 'Linsen Josué', 'Castro Alban Linsen Josué', '0931635494', 'M', 'caallijo12430802@ueamazonico.edu.ec', '', 'Bolívar y la segunda', '0984879139', '2006-01-05'),
(598, 0, 'Alvarado Herrera', 'Bryan Paúl', 'Alvarado Herrera Bryan Paúl', '0929978246', 'M', '', '', 'Bolivar y la segunda', '0961338479', '2004-10-06'),
(599, 0, 'Alvear Mejía', 'Willy Joel', 'Alvear Mejía Willy Joel', '0942704305', 'M', 'almewijo3970644@ueamazonico.edu.ec', '', 'Alamos', '0960907819', '2006-07-30'),
(600, 0, 'Chávez Jiménez', 'Erwin Gabriel', 'Chávez Jiménez Erwin Gabriel', '0959429374', 'M', 'chjierga11642228@ueamazonico.edu.ec', 'Daule', 'Perimetral y pedro pantaleon', '0989570349', '2006-06-05'),
(601, 0, 'velasco López', 'hillary', 'velasco López hillary', '0942303181', 'F', 'h_michellevl@live.com', '', 'daule', ' 593968989870', '2006-06-24'),
(602, 0, 'Arévalo Mora', 'Hendrick Leodán', 'Arévalo Mora Hendrick Leodán', '0954022125', 'M', '', 'Guayas', 'Km 26 via a Daule', '0969853409', '2007-01-25'),
(603, 0, 'CANDELARIO ESPINOZA', 'JENNIFER STEFANIA', 'CANDELARIO ESPINOZA JENNIFER STEFANIA', '0941801342', 'F', '', 'Daule', 'Ciudadela \"Assad BUCARAN\" (Daule)', '0992258546', '2007-09-25'),
(604, 0, 'Ronquillo Argudo', 'Isaac Santiago', 'Ronquillo Argudo Isaac Santiago', '0956956239', 'M', 'isaacdaniel1710@hotmail.com', 'Juan Leon Mera y 9 de Octubre', 'Juan Leon Mera y 9 de Octubre', '0992021310', '2006-10-17'),
(605, 0, 'Piguave Zambrano', 'Michael Antonio', 'Piguave Zambrano Michael Antonio', '0926672881', 'M', 'piguavemichael76@gmail.com', 'Pila de abajo alado de la escuela Is', 'Pila de abajo alado de la escuela Ismael Pérez pasmiño por las', '0990799363', '2004-06-02'),
(606, 0, 'campaña bejarano', 'javier ezequiel', 'campaña bejarano javier ezequiel', '0931404610', 'M', '', 'banife', 'daule', '0982221435', '1992-10-11'),
(607, 0, 'Veliz Córdova', 'Juan Francisco', 'Veliz Córdova Juan Francisco', '0942713454', 'M', 'mecnaranjo15@gmail.com', 'Daule', 'Alfredo Baquerizo Moreno', '0992816200', '0000-00-00'),
(608, 0, 'Barzola Martinez', 'Danna Alejandra', 'Barzola Martinez Danna Alejandra', '0959432063', 'F', '', 'Daule', 'Daule', '0990122629', '2009-08-29'),
(609, 0, 'García Ruiz', 'Ney Anthony', 'García Ruiz Ney Anthony', '0942303686', 'M', 'yanina.ruiz84@gmail.com', 'Rcto.La Bahona', 'Rcto.La Bahona', '0988487883', '2008-12-06'),
(610, 0, 'Tutiven Salas', 'Joice Maria', 'Tutiven Salas Joice Maria', '0941801516', 'F', 'electramariasalasronquillo@gmail.com', 'Daule', 'Juan Bautista Aguirre', '0959710971', '2009-09-12'),
(611, 0, 'Vasquez  Brines', 'Nathaly Anahy', 'Vasquez  Brines Nathaly Anahy', '0957885981', 'F', 'brionesgordillo@hotmail.com', 'Daule', 'Rodrigo Chavez Rumiñahui', '0980441491', '2009-06-29'),
(612, 0, 'Castro Martinez', 'Maykel Jesus', 'Castro Martinez Maykel Jesus', '0931330419', 'M', '06clatitamartinez21@gmail.com', 'Daule', 'Rcto. Pajonal', '0988772953', '2009-09-21'),
(613, 0, 'Rugel Rosado', 'Luis Andrés', 'Rugel Rosado Luis Andrés', '0959276437', 'M', 'jackelinrosado727@gmail.com', 'Daule', 'Rcto.Los Kiosko', '0993482388', '2008-09-10'),
(614, 0, 'Mantuano Magallanes', 'Nayeli Fiorella', 'Mantuano Magallanes Nayeli Fiorella', '0957038862', 'F', '', 'Daule', 'Rcto. Rio perdido', '0953031024', '2009-02-28'),
(615, 0, 'Jurado Moran', 'Jeremy Bautista', 'Jurado Moran Jeremy Bautista', '0941801474', 'M', '', 'Daule', 'Rcto.San Gabriel', '0939747706', '2009-07-22'),
(616, 0, 'Caicedo Holguin', 'Maria Cecilia', 'Caicedo Holguin Maria Cecilia', '0929997906', 'F', '', '', 'ANA PAREDES Y PABLO HANNIBAL VELA MZ', '0989604177', '2007-06-17'),
(617, 0, 'Alvarado Navarrete', 'Javier Andres', 'Alvarado Navarrete Javier Andres', '0959447954', 'M', '', '', 'Kilómetro 26 vía salitre-aurora', '0959875916', '2005-09-21'),
(618, 0, 'Alvarado Castañeda', 'Edison Javier', 'Alvarado Castañeda Edison Javier', '0959403486', 'M', '', '', 'Km 54 via Daule - Santa lucia', '0990639940', '2006-02-13'),
(619, 0, 'Adrian Diaz', 'Karen Yaritza', 'Adrian Diaz Karen Yaritza', '0941805897', 'F', '', '', 'Víctor Manuel rendon y Homero Espinoza ciudadela del recuerdo', '0985896815', '2005-10-14'),
(620, 0, 'Caicedo Chavez', 'Edison Eduardo', 'Caicedo Chavez Edison Eduardo', '0942800525', 'M', '', 'Florida', 'frente a la planta de agua potable', '0981786840', '2006-02-07'),
(621, 0, 'Aroca Torres', 'Jostin Emanuel', 'Aroca Torres Jostin Emanuel', '0959397811', 'M', '', '', 'Abdon Calderon y Victor Rendon', '0990942666', '2005-04-17'),
(622, 0, 'Barzola Bonilla', 'Cesar Javier', 'Barzola Bonilla Cesar Javier', '0942169954', 'M', '', '', ': Pedro  Isaías', ':  0982841661', '2005-02-28'),
(623, 0, 'Arteaga Gavilanes', 'Jennifer Dayana', 'Arteaga Gavilanes Jennifer Dayana', '0956188403', 'F', '', '', 'Recinto Huanchichal', '0939997094', '2005-07-20'),
(624, 0, 'Chiriguaya Reliche', 'Jandry Iyec', 'Chiriguaya Reliche Jandry Iyec', '0957055569', 'M', '', 'Parr.\"laurel\" rct\" san vicente\"', '', '0981199329', '2005-02-25'),
(625, 0, 'Bustos Segura', 'Fiorella Dayana', 'Bustos Segura Fiorella Dayana', '0958690547', 'F', '', 'frente al IESS', 'ciudadela Rosa mira calle Bolivar y 14 de septiembre', '0961785636', '2006-05-02'),
(626, 0, 'Bonilla Ronquillo', 'Jinsop Ezequiel', 'Bonilla Ronquillo Jinsop Ezequiel', '0940418973', 'M', '', 'Daule', ':calles Domingo Comin y Manuel González', '0962569866', '2003-12-18'),
(627, 0, 'Chica Zambrano', 'Said', 'Chica Zambrano Said', '0929209260', 'M', '', 'petrillo', 'KM30 vía Daule', '0995823501', '2006-08-29'),
(628, 0, 'Carchichabla Salazar', 'Luis Enrique', 'Carchichabla Salazar Luis Enrique', '0927971986', 'M', '', '', 'Ezequiel mora y domingo elizalde MZ 49', '0962119982', '2006-07-04'),
(629, 0, 'Alvarado Vera', 'Leonardo Leonel', 'Alvarado Vera Leonardo Leonel', '0942564196', 'M', '', '', '', '593 99 442 529', '2005-12-14'),
(630, 0, 'Acosta Bajaña', 'Junior Jose', 'Acosta Bajaña Junior Jose', '0944207950', 'M', '', '', 'MZ 16 O????? Y A?????? B???????? M?????', '0968933133', '0000-00-00'),
(631, 0, 'Aroca Torres', 'Jair Jostin', 'Aroca Torres Jair Jostin', '0959397878', 'M', '', '', 'Abdon Calderon y Victor Rendon', '0979726749', '2005-04-17'),
(632, 0, 'ALVARADO RUIZ', 'MILEIDY ANNABELLE', 'ALVARADO RUIZ MILEIDY ANNABELLE', '0958623530', 'M', 'miladyalvarado36@gmail.com', 'DAULE', 'ANA PAREDES-PABLO HUERTA', '0968407124', '2003-04-10'),
(633, 0, 'MENDOZA ROMERO', 'BRYAN  WASHINGTON', 'MENDOZA ROMERO BRYAN  WASHINGTON', '0957213192', 'M', '', 'DAULE', 'ISIDRO VEIINZA Y VELAZCO IBARRA', '0968429828', '2003-05-25'),
(634, 0, 'MEJIA SALAZAR', 'JORGE JOEL', 'MEJIA SALAZAR JORGE JOEL', '0941026403', 'M', 'jm7069689@gmail.com', 'DAULE', 'MARTHA BUCARAN Y PEDRO ORTIZ', '0991121340', '2004-05-07'),
(635, 0, 'VERA DIAZ', 'BRAIDIN SIMON', 'VERA DIAZ BRAIDIN SIMON', '0967709390', 'M', 'veradiazbraidinsimon@gmail.com', 'SERGIO TORAL', '', '0988885068', '2003-05-17'),
(636, 0, 'YEPEZ JUANAZO', 'JOHN ISAIAS', 'YEPEZ JUANAZO JOHN ISAIAS', '0940688807', 'M', 'jhonyepez@gmail.com', 'RCTO SAN PABLO  SANTA LUCIA', '', '0987763676', '2004-07-24'),
(637, 0, 'ESQUIVEL MORAN', 'TIANA DALESKA', 'ESQUIVEL MORAN TIANA DALESKA', '958944159', 'F', '', 'HOMERO ESPINOZA Y 9 DE OCTUBRE CENTR', 'HOMERO ESPINOZA Y 9 DE OCTUBRE CENTRO DE DIALISIS', '993721457', '2014-05-14'),
(638, 0, 'MINA ESTUPIÑAN', 'RUTH MARINA', 'MINA ESTUPIÑAN RUTH MARINA', '000000000', 'F', '', '', '', '', '0000-00-00'),
(639, 0, 'LEON RONQUILLO', 'SABRINA ANGELICA', 'LEON RONQUILLO SABRINA ANGELICA', '000111111', 'F', '', '', '', '', '0000-00-00'),
(640, 0, 'Cavero Castro', 'Denilson Daniel', 'Cavero Castro Denilson Daniel', '1250973037', 'M', '', 'Las Lojas', 'recinto dos reversa', '0999172326', '2005-06-27'),
(641, 0, 'OLVERA CAMPOVERDE', 'DYLAN STALIN', 'OLVERA CAMPOVERDE DYLAN STALIN', '0957209364', 'M', 'danielacampoverdereino@hotmail.com', 'Via nobol antes de Petrillo', 'Km 32.5 via Daule', '0998404861', '2009-05-01'),
(642, 0, 'León Cepeda', 'Sixto Raúl', 'León Cepeda Sixto Raúl', '0957798838', 'M', '', 'Cascol de Bahona', 'Cascol de Bahona', '0997012550', '2007-08-24'),
(643, 0, 'Huacon Briones', 'Marlyn Valentina', 'Huacon Briones Marlyn Valentina', '0941805483', 'F', 'huaconvalentina@gmail.com', 'San Francisco', 'Calle Miguel Letamendy y Bay Pass', '0958823613', '2009-05-02'),
(644, 0, 'Jiménez Gomezcuello', 'Edwin Joel', 'Jiménez Gomezcuello Edwin Joel', '0958222614', 'M', 'ejimenezgomezcuello@gmail.com', 'Lomas de sargentillo', 'Callejon sn y calle 1ro de mayo', '0939618504', '2009-06-22'),
(645, 0, 'Nieto Bonilla', 'Emilio Eduardo', 'Nieto Bonilla Emilio Eduardo', '0957881568', 'M', 'nataly.bonilla1987@gmail.com', 'AV.San Francisco   Y Homero Espinosa', 'AV.San Francisco por la entrada de la iglesia san francisco', '0996890356', '2008-10-28'),
(646, 0, 'HERRERA Pillasagua', 'Aylin Alanis', 'HERRERA Pillasagua Aylin Alanis', '0956987325', 'F', 'Sugeypillasagua11@gmail.com', 'Prov.de Azoguez  y callejo', 'Prov.de Azoguez y callejo Mz145 Sl 9', '0993663193', '2008-11-29'),
(647, 0, 'Peñafiel Bajaña', 'Jesús Alberto', 'Peñafiel Bajaña Jesús Alberto', '0941801649', 'M', 'mary_angel20@hotmail.com', '', 'PABLO HANNIBAL VELA Y JOSE MARIA EGAS', '0968893818', '2009-10-15'),
(648, 0, 'JIMENEZ  AVILÉS', 'NAYELY  SUSANA', 'JIMENEZ  AVILÉS NAYELY  SUSANA', '0958678583', 'F', 'taniaavilesquinto@gmail.com', '', 'LEONIDAS PROAÑO Y HOMERO ESPINOZA', '0959018569', '2008-08-06'),
(649, 0, 'Garcia Diaz', 'Juan Jonathan', 'Garcia Diaz Juan Jonathan', '0942300369', 'M', 'andrediaz920@gmail.com', 'Magro Homero espinoza', 'Entrado por las Clinica las Dialisis', '0986250564', '2009-06-12'),
(650, 0, 'Moncayo Reyes', 'Anthony Jeampierre', 'Moncayo Reyes Anthony Jeampierre', '0957787237', 'M', 'ereyescoloma@gmail.com', 'Santa Clara', 'Bolivar y Crispin cerezo', '0959195497', '2009-04-17'),
(651, 0, 'PEÑA BRIONES', 'ELKIN STALiN', 'PEÑA BRIONES ELKIN STALiN', '0957602592', 'M', '', 'TALLER DE TORNO GUAYMABE', 'Zona rural Daule vía Daule -santalucia', '0968088220', '2009-03-27'),
(652, 0, 'Vargas Coloma', 'Irvin Gregorio', 'Vargas Coloma Irvin Gregorio', '0956043905', 'M', 'ncoloma2020@gmail.com', 'Atrás del Estadio los Daulis', 'Camilo de Estruje y Antonio Huayamabe Mz 2', '0967269153', '2009-03-02'),
(653, 0, 'Cortez Amagua', 'Andy Miguel', 'Cortez Amagua Andy Miguel', '1752118172', 'M', 'Karylex85@hotmai.com', '', 'Riberas Opuestas. Frente Iglesia Señor de los Milagros', '0980634020', '2009-04-02'),
(654, 0, 'Noboa vera', 'Adriana Narcisa', 'Noboa vera Adriana Narcisa', '0944233436', 'F', '', 'La Aurora', 'Piedrahita y José Olmedo', '0967732954', '2008-03-07'),
(655, 0, 'Quinto Rivas', 'Lisley Carolina', 'Quinto Rivas Lisley Carolina', '0941802100', 'F', 'quintolisley@gmail.com', 'Colegio Juan Bautista Aguirre', 'Cdla. Juan Bautista Aguirre', '0988272993', '2009-04-30'),
(656, 0, 'Medina Yanon', 'Daniella barbarita', 'Medina Yanon Daniella barbarita', '0959404013', 'F', '', '', 'Calle justo torres y Proaños', '0988680540', '2008-12-12'),
(657, 0, 'Cobeña González', 'Kenet Alexander', 'Cobeña González Kenet Alexander', '0957388390', 'M', 'erikagonzalez23@hotmail.es', 'A una cuadra de la escuela Hugo Serr', 'García Moreno y Leónidas proaño', '0959219537', '2009-04-29'),
(658, 0, 'Romero Camba', 'Keyra Veronica', 'Romero Camba Keyra Veronica', '0943607895', 'F', '', '', 'Ciudadela Assad Bucaram Daule', '0960180623', '2009-05-03'),
(659, 0, 'DANTE YANDEL', 'CANTOS GOMEZ', 'DANTE YANDEL CANTOS GOMEZ', '0943671180', 'M', 'Carmen.gomez58@yahoo.com', 'Patria Nueva', 'Jose Maria Velasco Ibarra y Camilo de Estruje', '0985796144', '2009-10-15'),
(660, 0, 'Suarez San Lucas', 'Nathalia Nicole', 'Suarez San Lucas Nathalia Nicole', '0941139024', 'F', 'jonathansuarez1999@gmail.com', '', 'Patria Nueva Coop los pozos Daule', '0968249663', '2005-07-24'),
(661, 0, 'León Vera', 'Victoria Norellys', 'León Vera Victoria Norellys', '0956846190', 'F', 'meryvera561@gmail.com', 'Provincia del Guayas cantón Nobol', 'Zona Nobol vía Nobol-Petrillo Lotizacion', '0993128640', '2009-06-11'),
(662, 0, 'Palma Pote', 'David Alex', 'Palma Pote David Alex', '0952565802', 'M', 'palmapotedavid@gmail.com', 'km 26 via Daule Coop Los Angeles 1', 'km 26 via Daule', '0963366705', '2009-01-24'),
(663, 0, 'Salas Marquéz', 'Kristel Ariana', 'Salas Marquéz Kristel Ariana', '0942817255', 'F', '', 'Ciudadela Assad Bucaram', 'Pablo Anivel Vela - José Peralta (Esquina )', '0989828021', '2009-09-19'),
(664, 0, 'Hernandez Espinoza', 'Stalin Emidio', 'Hernandez Espinoza Stalin Emidio', '0953887825', 'M', 'stalinhernandez15@gmail.com', 'Radio cideral', 'Jose Domingo Elizalde/Olmedo Almeida /narcisa jesus', '0939172420', '2009-03-02'),
(665, 0, 'España veloz', 'Braulio Alejandro', 'España veloz Braulio Alejandro', '0958351305', 'M', '', '', 'Leónidas plaza', '0992282224', '2006-04-21'),
(666, 0, 'Campaña Bejarano', 'Ezequiel Javier', 'Campaña Bejarano Ezequiel Javier', '0931404611', 'M', 'Sur@GMAIL.COM', 'sur', 'sur', '0982221435', '1992-10-11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_estudiante_club`
--

CREATE TABLE `sw_estudiante_club` (
  `id_estudiante_club` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `es_retirado` varchar(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `es_retirado` varchar(1) CHARACTER SET latin1 NOT NULL DEFAULT 'N',
  `nro_matricula` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_estudiante_periodo_lectivo`
--

INSERT INTO `sw_estudiante_periodo_lectivo` (`id_estudiante_periodo_lectivo`, `id_estudiante`, `id_periodo_lectivo`, `id_paralelo`, `es_estado`, `es_retirado`, `nro_matricula`) VALUES
(2, 2, 1, 26, 'N', 'N', 1),
(3, 3, 1, 20, 'N', 'N', 2),
(4, 4, 1, 20, 'N', 'N', 3),
(5, 5, 1, 20, 'N', 'N', 4),
(6, 6, 1, 37, 'N', 'N', 5),
(7, 7, 1, 35, 'N', 'N', 6),
(8, 8, 1, 42, 'N', 'N', 7),
(9, 9, 1, 19, 'N', 'S', 8),
(10, 10, 1, 25, 'N', 'N', 9),
(11, 11, 1, 48, 'N', 'N', 10),
(12, 12, 1, 25, 'N', 'N', 11),
(13, 13, 1, 37, 'N', 'N', 12),
(14, 14, 1, 57, 'N', 'N', 13),
(15, 15, 1, 57, 'N', 'N', 14),
(16, 16, 1, 49, 'N', 'N', 15),
(17, 17, 1, 37, 'N', 'N', 16),
(18, 18, 1, 19, 'N', 'N', 17),
(19, 19, 1, 25, 'N', 'N', 18),
(20, 20, 1, 27, 'N', 'N', 19),
(21, 21, 1, 57, 'N', 'N', 20),
(22, 22, 1, 27, 'N', 'N', 21),
(23, 23, 1, 35, 'N', 'N', 22),
(24, 24, 1, 35, 'N', 'N', 23),
(25, 25, 1, 27, 'N', 'N', 24),
(26, 26, 1, 27, 'N', 'N', 25),
(27, 27, 1, 34, 'N', 'N', 26),
(28, 28, 1, 25, 'N', 'N', 27),
(29, 29, 1, 27, 'N', 'N', 28),
(30, 30, 1, 25, 'N', 'N', 29),
(31, 31, 1, 57, 'N', 'N', 30),
(32, 32, 1, 25, 'N', 'N', 31),
(33, 33, 1, 57, 'N', 'N', 32),
(34, 34, 1, 25, 'N', 'N', 33),
(35, 35, 1, 12, 'N', 'N', 34),
(36, 36, 1, 57, 'N', 'N', 35),
(37, 37, 1, 25, 'N', 'N', 36),
(38, 38, 1, 27, 'N', 'N', 37),
(39, 39, 1, 25, 'N', 'N', 38),
(40, 40, 1, 57, 'N', 'N', 39),
(41, 41, 1, 25, 'N', 'N', 40),
(42, 42, 1, 12, 'N', 'N', 41),
(43, 43, 1, 25, 'N', 'N', 42),
(44, 44, 1, 57, 'N', 'N', 43),
(45, 45, 1, 25, 'N', 'N', 44),
(46, 46, 1, 27, 'N', 'N', 45),
(47, 47, 1, 25, 'N', 'N', 46),
(48, 48, 1, 57, 'N', 'N', 47),
(49, 49, 1, 57, 'N', 'N', 48),
(50, 50, 1, 25, 'N', 'N', 49),
(51, 51, 1, 25, 'N', 'N', 50),
(52, 52, 1, 57, 'N', 'N', 51),
(53, 53, 1, 27, 'N', 'N', 52),
(54, 54, 1, 57, 'N', 'N', 53),
(55, 55, 1, 57, 'N', 'N', 54),
(56, 56, 1, 57, 'N', 'N', 55),
(57, 57, 1, 25, 'N', 'N', 56),
(58, 58, 1, 57, 'N', 'N', 57),
(59, 59, 1, 57, 'N', 'N', 58),
(60, 60, 1, 57, 'N', 'N', 59),
(61, 61, 1, 57, 'N', 'N', 60),
(62, 62, 1, 25, 'N', 'N', 61),
(63, 63, 1, 25, 'N', 'N', 62),
(64, 64, 1, 25, 'N', 'N', 63),
(65, 65, 1, 57, 'N', 'N', 64),
(66, 66, 1, 57, 'N', 'N', 65),
(67, 67, 1, 57, 'N', 'N', 66),
(68, 68, 1, 57, 'N', 'N', 67),
(69, 69, 1, 57, 'N', 'N', 68),
(70, 70, 1, 57, 'N', 'N', 69),
(71, 71, 1, 32, 'N', 'N', 70),
(72, 72, 1, 56, 'N', 'N', 71),
(73, 73, 1, 51, 'N', 'N', 72),
(74, 74, 1, 18, 'N', 'N', 73),
(75, 75, 1, 57, 'N', 'N', 74),
(76, 76, 1, 18, 'N', 'N', 75),
(77, 77, 1, 27, 'N', 'N', 76),
(78, 78, 1, 27, 'N', 'N', 77),
(79, 79, 1, 27, 'N', 'N', 78),
(80, 80, 1, 42, 'N', 'N', 79),
(81, 81, 1, 42, 'N', 'N', 80),
(82, 82, 1, 31, 'N', 'N', 81),
(83, 83, 1, 42, 'N', 'N', 82),
(84, 84, 1, 42, 'N', 'N', 83),
(85, 85, 1, 31, 'N', 'N', 84),
(86, 86, 1, 31, 'N', 'N', 85),
(87, 87, 1, 42, 'N', 'N', 86),
(88, 88, 1, 42, 'N', 'N', 87),
(89, 89, 1, 42, 'N', 'N', 88),
(90, 90, 1, 42, 'N', 'N', 89),
(91, 91, 1, 42, 'N', 'N', 90),
(92, 92, 1, 33, 'N', 'N', 91),
(93, 93, 1, 35, 'N', 'N', 92),
(94, 94, 1, 33, 'N', 'N', 93),
(95, 95, 1, 33, 'N', 'N', 94),
(96, 96, 1, 35, 'N', 'N', 95),
(97, 97, 1, 35, 'N', 'N', 96),
(98, 98, 1, 35, 'N', 'N', 97),
(99, 99, 1, 35, 'N', 'N', 98),
(100, 100, 1, 35, 'N', 'N', 99),
(101, 101, 1, 33, 'N', 'N', 100),
(102, 102, 1, 35, 'N', 'N', 101),
(103, 103, 1, 33, 'N', 'N', 102),
(104, 104, 1, 33, 'N', 'N', 103),
(105, 105, 1, 37, 'N', 'N', 104),
(106, 106, 1, 33, 'N', 'N', 105),
(107, 107, 1, 37, 'N', 'N', 106),
(108, 108, 1, 37, 'N', 'N', 107),
(109, 109, 1, 37, 'N', 'N', 108),
(110, 110, 1, 37, 'N', 'N', 109),
(111, 111, 1, 37, 'N', 'N', 110),
(112, 112, 1, 37, 'N', 'N', 111),
(113, 113, 1, 33, 'N', 'N', 112),
(114, 114, 1, 33, 'N', 'N', 113),
(115, 115, 1, 14, 'N', 'N', 114),
(116, 116, 1, 31, 'N', 'N', 115),
(117, 117, 1, 47, 'N', 'N', 116),
(118, 118, 1, 31, 'N', 'N', 117),
(119, 119, 1, 33, 'N', 'N', 118),
(120, 120, 1, 47, 'N', 'N', 119),
(121, 121, 1, 33, 'N', 'N', 120),
(122, 122, 1, 31, 'N', 'N', 121),
(123, 123, 1, 31, 'N', 'N', 122),
(124, 124, 1, 33, 'N', 'N', 123),
(125, 125, 1, 31, 'N', 'N', 124),
(126, 126, 1, 33, 'N', 'N', 125),
(127, 127, 1, 31, 'N', 'N', 126),
(128, 128, 1, 31, 'N', 'N', 127),
(129, 129, 1, 33, 'N', 'N', 128),
(130, 130, 1, 31, 'N', 'N', 129),
(131, 131, 1, 47, 'N', 'N', 130),
(132, 132, 1, 33, 'N', 'N', 131),
(133, 133, 1, 31, 'N', 'N', 132),
(134, 134, 1, 47, 'N', 'N', 133),
(135, 135, 1, 47, 'N', 'N', 134),
(136, 136, 1, 33, 'N', 'N', 135),
(137, 137, 1, 47, 'N', 'N', 136),
(138, 138, 1, 47, 'N', 'N', 137),
(139, 139, 1, 35, 'N', 'S', 138),
(140, 140, 1, 35, 'N', 'S', 139),
(141, 141, 1, 37, 'N', 'N', 140),
(142, 142, 1, 35, 'N', 'N', 141),
(143, 143, 1, 35, 'N', 'N', 142),
(144, 144, 1, 35, 'N', 'N', 143),
(145, 145, 1, 35, 'N', 'S', 144),
(146, 146, 1, 35, 'N', 'S', 145),
(147, 147, 1, 35, 'N', 'N', 146),
(148, 148, 1, 35, 'N', 'N', 147),
(149, 149, 1, 35, 'N', 'N', 148),
(150, 150, 1, 35, 'N', 'N', 149),
(151, 151, 1, 37, 'N', 'N', 150),
(152, 152, 1, 37, 'N', 'N', 151),
(153, 153, 1, 37, 'N', 'N', 152),
(154, 154, 1, 37, 'N', 'N', 153),
(155, 155, 1, 37, 'N', 'N', 154),
(156, 156, 1, 37, 'N', 'N', 155),
(157, 157, 1, 37, 'N', 'N', 156),
(158, 158, 1, 37, 'N', 'N', 157),
(159, 159, 1, 37, 'N', 'N', 158),
(160, 160, 1, 37, 'N', 'N', 159),
(161, 161, 1, 32, 'N', 'N', 160),
(162, 162, 1, 32, 'N', 'N', 161),
(163, 163, 1, 14, 'N', 'N', 162),
(164, 164, 1, 14, 'N', 'N', 163),
(165, 165, 1, 32, 'N', 'N', 164),
(166, 166, 1, 14, 'N', 'N', 165),
(167, 167, 1, 32, 'N', 'N', 166),
(168, 168, 1, 32, 'N', 'N', 167),
(169, 169, 1, 32, 'N', 'N', 168),
(170, 170, 1, 32, 'N', 'N', 169),
(171, 171, 1, 32, 'N', 'N', 170),
(172, 172, 1, 32, 'N', 'N', 171),
(173, 173, 1, 32, 'N', 'N', 172),
(174, 174, 1, 32, 'N', 'N', 173),
(175, 175, 1, 32, 'N', 'N', 174),
(176, 176, 1, 32, 'N', 'N', 175),
(177, 177, 1, 32, 'N', 'N', 176),
(178, 178, 1, 32, 'N', 'N', 177),
(179, 179, 1, 32, 'N', 'N', 178),
(180, 180, 1, 32, 'N', 'N', 179),
(181, 181, 1, 32, 'N', 'N', 180),
(182, 182, 1, 32, 'N', 'N', 181),
(183, 183, 1, 32, 'N', 'N', 182),
(184, 184, 1, 32, 'N', 'N', 183),
(185, 185, 1, 44, 'N', 'N', 184),
(186, 186, 1, 44, 'N', 'N', 185),
(187, 187, 1, 44, 'N', 'N', 186),
(188, 188, 1, 44, 'N', 'N', 187),
(189, 189, 1, 44, 'N', 'N', 188),
(190, 190, 1, 44, 'N', 'N', 189),
(191, 191, 1, 44, 'N', 'N', 190),
(192, 192, 1, 44, 'N', 'N', 191),
(193, 193, 1, 44, 'N', 'N', 192),
(194, 194, 1, 44, 'N', 'N', 193),
(195, 195, 1, 44, 'N', 'N', 194),
(196, 196, 1, 44, 'N', 'N', 195),
(197, 197, 1, 44, 'N', 'N', 196),
(198, 198, 1, 44, 'N', 'N', 197),
(199, 199, 1, 44, 'N', 'N', 198),
(200, 200, 1, 44, 'N', 'N', 199),
(201, 201, 1, 44, 'N', 'N', 200),
(202, 202, 1, 44, 'N', 'N', 201),
(203, 203, 1, 44, 'N', 'N', 202),
(204, 204, 1, 44, 'N', 'N', 203),
(205, 205, 1, 44, 'N', 'N', 204),
(206, 206, 1, 44, 'N', 'N', 205),
(207, 207, 1, 44, 'N', 'N', 206),
(208, 208, 1, 18, 'N', 'N', 207),
(209, 209, 1, 18, 'N', 'N', 208),
(210, 210, 1, 18, 'N', 'N', 209),
(211, 211, 1, 41, 'N', 'N', 210),
(212, 212, 1, 41, 'N', 'N', 211),
(213, 213, 1, 18, 'N', 'N', 212),
(214, 214, 1, 18, 'N', 'N', 213),
(215, 215, 1, 18, 'N', 'N', 214),
(216, 216, 1, 41, 'N', 'N', 215),
(217, 217, 1, 18, 'N', 'N', 216),
(218, 218, 1, 41, 'N', 'N', 217),
(219, 219, 1, 18, 'N', 'N', 218),
(220, 220, 1, 18, 'N', 'N', 219),
(221, 221, 1, 18, 'N', 'N', 220),
(222, 222, 1, 41, 'N', 'N', 221),
(223, 223, 1, 18, 'N', 'N', 222),
(224, 224, 1, 18, 'N', 'N', 223),
(225, 225, 1, 18, 'N', 'N', 224),
(226, 226, 1, 19, 'N', 'N', 225),
(227, 227, 1, 18, 'N', 'N', 226),
(228, 228, 1, 18, 'N', 'N', 227),
(229, 229, 1, 19, 'N', 'N', 228),
(230, 230, 1, 18, 'N', 'N', 229),
(231, 231, 1, 19, 'N', 'N', 230),
(232, 232, 1, 18, 'N', 'N', 231),
(233, 233, 1, 18, 'N', 'N', 232),
(234, 234, 1, 18, 'N', 'N', 233),
(235, 235, 1, 19, 'N', 'N', 234),
(236, 236, 1, 19, 'N', 'N', 235),
(237, 237, 1, 18, 'N', 'N', 236),
(238, 238, 1, 19, 'N', 'N', 237),
(239, 239, 1, 18, 'N', 'N', 238),
(240, 240, 1, 18, 'N', 'N', 239),
(241, 241, 1, 19, 'N', 'N', 240),
(242, 242, 1, 19, 'N', 'N', 241),
(243, 243, 1, 19, 'N', 'N', 242),
(244, 244, 1, 19, 'N', 'N', 243),
(245, 245, 1, 19, 'N', 'N', 244),
(246, 246, 1, 19, 'N', 'N', 245),
(247, 247, 1, 19, 'N', 'N', 246),
(248, 248, 1, 19, 'N', 'N', 247),
(249, 249, 1, 19, 'N', 'N', 248),
(250, 250, 1, 19, 'N', 'N', 249),
(251, 251, 1, 19, 'N', 'N', 250),
(252, 252, 1, 19, 'N', 'N', 251),
(253, 253, 1, 19, 'N', 'N', 252),
(254, 254, 1, 19, 'N', 'N', 253),
(255, 255, 1, 44, 'N', 'N', 254),
(256, 256, 1, 55, 'N', 'N', 255),
(257, 257, 1, 44, 'N', 'N', 256),
(258, 258, 1, 14, 'N', 'N', 257),
(259, 259, 1, 14, 'N', 'N', 258),
(260, 260, 1, 14, 'N', 'N', 259),
(261, 261, 1, 14, 'N', 'N', 260),
(262, 262, 1, 14, 'N', 'N', 261),
(263, 263, 1, 14, 'N', 'N', 262),
(264, 264, 1, 14, 'N', 'N', 263),
(265, 265, 1, 14, 'N', 'N', 264),
(266, 266, 1, 14, 'N', 'N', 265),
(267, 267, 1, 14, 'N', 'N', 266),
(268, 268, 1, 14, 'N', 'N', 267),
(269, 269, 1, 14, 'N', 'N', 268),
(270, 270, 1, 14, 'N', 'N', 269),
(271, 271, 1, 14, 'N', 'N', 270),
(272, 272, 1, 14, 'N', 'N', 271),
(273, 273, 1, 14, 'N', 'N', 272),
(274, 274, 1, 14, 'N', 'N', 273),
(275, 275, 1, 14, 'N', 'N', 274),
(276, 276, 1, 35, 'N', 'N', 275),
(277, 277, 1, 35, 'N', 'N', 276),
(278, 278, 1, 35, 'N', 'N', 277),
(279, 279, 1, 35, 'N', 'N', 278),
(280, 280, 1, 35, 'N', 'N', 279),
(281, 281, 1, 35, 'N', 'N', 280),
(282, 282, 1, 35, 'N', 'N', 281),
(283, 283, 1, 52, 'N', 'N', 282),
(284, 284, 1, 52, 'N', 'N', 283),
(285, 285, 1, 52, 'N', 'N', 284),
(286, 286, 1, 31, 'N', 'N', 285),
(287, 287, 1, 33, 'N', 'N', 286),
(288, 288, 1, 33, 'N', 'N', 287),
(289, 289, 1, 33, 'N', 'N', 288),
(290, 290, 1, 31, 'N', 'N', 289),
(291, 291, 1, 33, 'N', 'N', 290),
(292, 292, 1, 35, 'N', 'N', 291),
(293, 293, 1, 31, 'N', 'N', 292),
(294, 294, 1, 35, 'N', 'N', 293),
(295, 295, 1, 34, 'N', 'N', 294),
(296, 296, 1, 34, 'N', 'N', 295),
(297, 297, 1, 34, 'N', 'N', 296),
(298, 298, 1, 34, 'N', 'N', 297),
(299, 299, 1, 34, 'N', 'N', 298),
(300, 300, 1, 34, 'N', 'N', 299),
(301, 301, 1, 34, 'N', 'N', 300),
(302, 302, 1, 34, 'N', 'N', 301),
(303, 303, 1, 34, 'N', 'N', 302),
(304, 304, 1, 34, 'N', 'N', 303),
(305, 305, 1, 34, 'N', 'N', 304),
(306, 306, 1, 34, 'N', 'N', 305),
(307, 307, 1, 34, 'N', 'N', 306),
(308, 308, 1, 34, 'N', 'N', 307),
(309, 309, 1, 34, 'N', 'N', 308),
(310, 310, 1, 34, 'N', 'N', 309),
(311, 311, 1, 34, 'N', 'N', 310),
(312, 312, 1, 34, 'N', 'N', 311),
(313, 313, 1, 34, 'N', 'N', 312),
(314, 314, 1, 34, 'N', 'N', 313),
(315, 315, 1, 34, 'N', 'N', 314),
(316, 316, 1, 34, 'N', 'N', 315),
(317, 317, 1, 34, 'N', 'N', 316),
(318, 318, 1, 34, 'N', 'N', 317),
(319, 319, 1, 40, 'N', 'N', 318),
(320, 320, 1, 36, 'N', 'N', 319),
(321, 321, 1, 40, 'N', 'N', 320),
(322, 322, 1, 40, 'N', 'N', 321),
(323, 323, 1, 40, 'N', 'N', 322),
(324, 324, 1, 40, 'N', 'N', 323),
(325, 325, 1, 40, 'N', 'N', 324),
(326, 326, 1, 40, 'N', 'N', 325),
(327, 327, 1, 40, 'N', 'N', 326),
(328, 328, 1, 40, 'N', 'N', 327),
(329, 329, 1, 40, 'N', 'N', 328),
(330, 330, 1, 40, 'N', 'N', 329),
(331, 331, 1, 40, 'N', 'N', 330),
(332, 332, 1, 40, 'N', 'N', 331),
(333, 333, 1, 58, 'N', 'N', 332),
(334, 334, 1, 37, 'N', 'N', 333),
(335, 335, 1, 37, 'N', 'N', 334),
(336, 336, 1, 58, 'N', 'N', 335),
(337, 337, 1, 37, 'N', 'N', 336),
(338, 338, 1, 25, 'N', 'N', 337),
(339, 339, 1, 37, 'N', 'N', 338),
(340, 340, 1, 58, 'N', 'N', 339),
(341, 341, 1, 25, 'N', 'N', 340),
(342, 342, 1, 58, 'N', 'N', 341),
(343, 343, 1, 25, 'N', 'N', 342),
(344, 344, 1, 42, 'N', 'N', 343),
(345, 345, 1, 42, 'N', 'N', 344),
(346, 346, 1, 58, 'N', 'N', 345),
(347, 347, 1, 25, 'N', 'N', 346),
(348, 348, 1, 25, 'N', 'N', 347),
(349, 349, 1, 42, 'N', 'N', 348),
(350, 350, 1, 25, 'N', 'N', 349),
(351, 351, 1, 42, 'N', 'N', 350),
(352, 352, 1, 37, 'N', 'N', 351),
(353, 353, 1, 25, 'N', 'N', 352),
(354, 354, 1, 25, 'N', 'N', 353),
(355, 355, 1, 42, 'N', 'N', 354),
(356, 356, 1, 37, 'N', 'N', 355),
(357, 357, 1, 42, 'N', 'N', 356),
(358, 358, 1, 37, 'N', 'N', 357),
(359, 359, 1, 58, 'N', 'N', 358),
(360, 360, 1, 42, 'N', 'N', 359),
(361, 361, 1, 37, 'N', 'N', 360),
(362, 362, 1, 42, 'N', 'N', 361),
(363, 363, 1, 37, 'N', 'N', 362),
(364, 364, 1, 58, 'N', 'N', 363),
(365, 365, 1, 58, 'N', 'N', 364),
(366, 366, 1, 58, 'N', 'N', 365),
(367, 367, 1, 58, 'N', 'N', 366),
(368, 368, 1, 58, 'N', 'N', 367),
(369, 369, 1, 36, 'N', 'N', 368),
(370, 370, 1, 37, 'N', 'N', 369),
(371, 371, 1, 36, 'N', 'N', 370),
(372, 372, 1, 37, 'N', 'N', 371),
(373, 373, 1, 36, 'N', 'N', 372),
(374, 374, 1, 37, 'N', 'N', 373),
(375, 375, 1, 37, 'N', 'N', 374),
(376, 376, 1, 36, 'N', 'N', 375),
(377, 377, 1, 37, 'N', 'N', 376),
(378, 378, 1, 37, 'N', 'N', 377),
(379, 379, 1, 37, 'N', 'N', 378),
(380, 380, 1, 36, 'N', 'N', 379),
(381, 381, 1, 36, 'N', 'N', 380),
(382, 382, 1, 36, 'N', 'N', 381),
(383, 383, 1, 36, 'N', 'N', 382),
(384, 384, 1, 58, 'N', 'N', 383),
(385, 385, 1, 36, 'N', 'N', 384),
(386, 386, 1, 36, 'N', 'N', 385),
(387, 387, 1, 58, 'N', 'N', 386),
(388, 388, 1, 36, 'N', 'N', 387),
(389, 389, 1, 36, 'N', 'N', 388),
(390, 390, 1, 36, 'N', 'N', 389),
(391, 391, 1, 36, 'N', 'N', 390),
(392, 392, 1, 36, 'N', 'N', 391),
(393, 393, 1, 36, 'N', 'N', 392),
(394, 394, 1, 36, 'N', 'N', 393),
(395, 395, 1, 36, 'N', 'N', 394),
(396, 396, 1, 47, 'N', 'N', 395),
(397, 397, 1, 36, 'N', 'N', 396),
(398, 398, 1, 36, 'N', 'N', 397),
(399, 399, 1, 36, 'N', 'N', 398),
(400, 400, 1, 36, 'N', 'N', 399),
(401, 401, 1, 36, 'N', 'N', 400),
(402, 402, 1, 36, 'N', 'N', 401),
(403, 403, 1, 14, 'N', 'N', 402),
(404, 404, 1, 14, 'N', 'N', 403),
(405, 405, 1, 14, 'N', 'N', 404),
(406, 406, 1, 14, 'N', 'N', 405),
(407, 407, 1, 14, 'N', 'N', 406),
(408, 408, 1, 14, 'N', 'N', 407),
(409, 409, 1, 14, 'N', 'N', 408),
(410, 410, 1, 14, 'N', 'N', 409),
(411, 411, 1, 14, 'N', 'N', 410),
(412, 412, 1, 14, 'N', 'N', 411),
(413, 413, 1, 14, 'N', 'N', 412),
(414, 414, 1, 14, 'N', 'N', 413),
(415, 415, 1, 14, 'N', 'N', 414),
(416, 416, 1, 14, 'N', 'N', 415),
(417, 417, 1, 48, 'N', 'N', 416),
(418, 418, 1, 27, 'N', 'N', 417),
(419, 419, 1, 27, 'N', 'N', 418),
(420, 420, 1, 27, 'N', 'N', 419),
(421, 421, 1, 27, 'N', 'N', 420),
(422, 422, 1, 55, 'N', 'N', 421),
(423, 423, 1, 40, 'N', 'N', 422),
(424, 424, 1, 40, 'N', 'N', 423),
(425, 425, 1, 40, 'N', 'N', 424),
(426, 426, 1, 40, 'N', 'N', 425),
(427, 427, 1, 40, 'N', 'N', 426),
(428, 428, 1, 58, 'N', 'N', 427),
(429, 429, 1, 58, 'N', 'N', 428),
(430, 430, 1, 58, 'N', 'N', 429),
(431, 431, 1, 58, 'N', 'N', 430),
(432, 432, 1, 40, 'N', 'N', 431),
(433, 433, 1, 58, 'N', 'N', 432),
(434, 434, 1, 58, 'N', 'N', 433),
(435, 435, 1, 58, 'N', 'N', 434),
(436, 436, 1, 58, 'N', 'N', 435),
(437, 437, 1, 58, 'N', 'N', 436),
(438, 438, 1, 58, 'N', 'N', 437),
(439, 439, 1, 58, 'N', 'N', 438),
(440, 440, 1, 27, 'N', 'N', 439),
(441, 441, 1, 40, 'N', 'N', 440),
(442, 442, 1, 40, 'N', 'N', 441),
(443, 443, 1, 29, 'N', 'N', 442),
(444, 444, 1, 27, 'N', 'N', 443),
(445, 445, 1, 29, 'N', 'N', 444),
(446, 446, 1, 27, 'N', 'N', 445),
(447, 447, 1, 29, 'N', 'N', 446),
(448, 448, 1, 27, 'N', 'N', 447),
(449, 449, 1, 27, 'N', 'N', 448),
(450, 450, 1, 27, 'N', 'N', 449),
(451, 451, 1, 33, 'N', 'N', 450),
(452, 452, 1, 27, 'N', 'N', 451),
(453, 453, 1, 38, 'N', 'N', 452),
(454, 454, 1, 27, 'N', 'N', 453),
(455, 455, 1, 27, 'N', 'N', 454),
(456, 456, 1, 27, 'N', 'N', 455),
(457, 457, 1, 27, 'N', 'N', 456),
(458, 458, 1, 58, 'N', 'N', 457),
(459, 459, 1, 27, 'N', 'N', 458),
(460, 460, 1, 58, 'N', 'N', 459),
(461, 461, 1, 27, 'N', 'N', 460),
(462, 462, 1, 58, 'N', 'N', 461),
(463, 463, 1, 27, 'N', 'N', 462),
(464, 464, 1, 27, 'N', 'N', 463),
(465, 465, 1, 58, 'N', 'N', 464),
(466, 466, 1, 25, 'N', 'N', 465),
(467, 467, 1, 58, 'N', 'N', 466),
(468, 468, 1, 58, 'N', 'N', 467),
(469, 469, 1, 58, 'N', 'N', 468),
(470, 470, 1, 58, 'N', 'N', 469),
(471, 471, 1, 58, 'N', 'N', 470),
(472, 472, 1, 38, 'N', 'N', 471),
(473, 473, 1, 38, 'N', 'N', 472),
(474, 474, 1, 38, 'N', 'N', 473),
(475, 475, 1, 38, 'N', 'N', 474),
(476, 476, 1, 54, 'N', 'N', 475),
(477, 477, 1, 38, 'N', 'N', 476),
(478, 478, 1, 38, 'N', 'N', 477),
(479, 479, 1, 38, 'N', 'N', 478),
(480, 480, 1, 38, 'N', 'N', 479),
(481, 481, 1, 38, 'N', 'N', 480),
(482, 482, 1, 38, 'N', 'N', 481),
(483, 483, 1, 38, 'N', 'N', 482),
(484, 484, 1, 38, 'N', 'N', 483),
(485, 485, 1, 38, 'N', 'N', 484),
(486, 486, 1, 38, 'N', 'N', 485),
(487, 487, 1, 38, 'N', 'N', 486),
(488, 488, 1, 38, 'N', 'N', 487),
(489, 489, 1, 38, 'N', 'N', 488),
(490, 490, 1, 38, 'N', 'N', 489),
(491, 491, 1, 27, 'N', 'N', 490),
(492, 492, 1, 27, 'N', 'N', 491),
(493, 493, 1, 27, 'N', 'N', 492),
(494, 494, 1, 27, 'N', 'N', 493),
(495, 495, 1, 27, 'N', 'N', 494),
(496, 496, 1, 27, 'N', 'N', 495),
(497, 497, 1, 27, 'N', 'N', 496),
(498, 498, 1, 27, 'N', 'N', 497),
(499, 499, 1, 38, 'N', 'N', 498),
(500, 500, 1, 40, 'N', 'N', 499),
(501, 501, 1, 40, 'N', 'N', 500),
(502, 502, 1, 40, 'N', 'N', 501),
(503, 503, 1, 40, 'N', 'N', 502),
(504, 504, 1, 54, 'N', 'N', 503),
(505, 505, 1, 41, 'N', 'N', 504),
(506, 506, 1, 41, 'N', 'N', 505),
(507, 507, 1, 41, 'N', 'N', 506),
(508, 508, 1, 39, 'N', 'N', 507),
(509, 509, 1, 41, 'N', 'N', 508),
(510, 510, 1, 41, 'N', 'N', 509),
(511, 511, 1, 41, 'N', 'N', 510),
(512, 512, 1, 41, 'N', 'N', 511),
(513, 513, 1, 41, 'N', 'N', 512),
(514, 514, 1, 41, 'N', 'N', 513),
(515, 515, 1, 41, 'N', 'N', 514),
(516, 516, 1, 41, 'N', 'N', 515),
(517, 517, 1, 41, 'N', 'N', 516),
(518, 518, 1, 41, 'N', 'N', 517),
(519, 519, 1, 41, 'N', 'N', 518),
(520, 520, 1, 41, 'N', 'N', 519),
(521, 521, 1, 41, 'N', 'N', 520),
(522, 522, 1, 41, 'N', 'N', 521),
(523, 523, 1, 41, 'N', 'N', 522),
(524, 524, 1, 41, 'N', 'N', 523),
(525, 525, 1, 41, 'N', 'N', 524),
(526, 526, 1, 41, 'N', 'N', 525),
(527, 527, 1, 41, 'N', 'N', 526),
(528, 528, 1, 41, 'N', 'N', 527),
(529, 529, 1, 41, 'N', 'N', 528),
(530, 530, 1, 41, 'N', 'N', 529),
(531, 531, 1, 39, 'N', 'N', 530),
(532, 532, 1, 39, 'N', 'N', 531),
(533, 533, 1, 39, 'N', 'N', 532),
(534, 534, 1, 54, 'N', 'N', 533),
(535, 535, 1, 54, 'N', 'N', 534),
(536, 536, 1, 54, 'N', 'N', 535),
(537, 537, 1, 54, 'N', 'N', 536),
(538, 538, 1, 54, 'N', 'N', 537),
(539, 539, 1, 54, 'N', 'N', 538),
(540, 540, 1, 54, 'N', 'N', 539),
(541, 541, 1, 54, 'N', 'N', 540),
(542, 542, 1, 49, 'N', 'N', 541),
(543, 543, 1, 57, 'N', 'N', 542),
(544, 544, 1, 39, 'N', 'N', 543),
(545, 545, 1, 39, 'N', 'N', 544),
(546, 546, 1, 39, 'N', 'N', 545),
(547, 547, 1, 39, 'N', 'N', 546),
(548, 548, 1, 39, 'N', 'N', 547),
(549, 549, 1, 39, 'N', 'N', 548),
(550, 550, 1, 39, 'N', 'N', 549),
(551, 551, 1, 39, 'N', 'N', 550),
(552, 552, 1, 30, 'N', 'N', 551),
(553, 553, 1, 29, 'N', 'N', 552),
(554, 554, 1, 29, 'N', 'N', 553),
(555, 555, 1, 29, 'N', 'N', 554),
(556, 556, 1, 29, 'N', 'N', 555),
(557, 557, 1, 29, 'N', 'N', 556),
(558, 558, 1, 29, 'N', 'N', 557),
(559, 559, 1, 29, 'N', 'N', 558),
(560, 560, 1, 29, 'N', 'N', 559),
(561, 561, 1, 29, 'N', 'N', 560),
(562, 562, 1, 29, 'N', 'N', 561),
(563, 563, 1, 29, 'N', 'N', 562),
(564, 564, 1, 29, 'N', 'N', 563),
(565, 565, 1, 29, 'N', 'N', 564),
(566, 566, 1, 29, 'N', 'N', 565),
(567, 567, 1, 29, 'N', 'N', 566),
(568, 568, 1, 29, 'N', 'N', 567),
(569, 569, 1, 29, 'N', 'N', 568),
(570, 570, 1, 29, 'N', 'N', 569),
(571, 571, 1, 29, 'N', 'N', 570),
(572, 572, 1, 29, 'N', 'N', 571),
(573, 573, 1, 29, 'N', 'N', 572),
(574, 574, 1, 29, 'N', 'N', 573),
(575, 575, 1, 30, 'N', 'N', 574),
(576, 576, 1, 30, 'N', 'N', 575),
(577, 577, 1, 30, 'N', 'N', 576),
(578, 578, 1, 30, 'N', 'N', 577),
(579, 579, 1, 30, 'N', 'N', 578),
(580, 580, 1, 30, 'N', 'N', 579),
(581, 581, 1, 30, 'N', 'N', 580),
(582, 582, 1, 30, 'N', 'N', 581),
(583, 583, 1, 30, 'N', 'N', 582),
(584, 584, 1, 30, 'N', 'N', 583),
(585, 585, 1, 30, 'N', 'N', 584),
(586, 586, 1, 30, 'N', 'N', 585),
(587, 587, 1, 30, 'N', 'N', 586),
(588, 588, 1, 30, 'N', 'N', 587),
(589, 589, 1, 30, 'N', 'N', 588),
(590, 590, 1, 30, 'N', 'N', 589),
(591, 591, 1, 30, 'N', 'N', 590),
(592, 592, 1, 30, 'N', 'N', 591),
(593, 593, 1, 30, 'N', 'N', 592),
(594, 594, 1, 30, 'N', 'N', 593),
(595, 595, 1, 28, 'N', 'N', 594),
(596, 596, 1, 28, 'N', 'N', 595),
(597, 597, 1, 28, 'N', 'N', 596),
(598, 598, 1, 28, 'N', 'N', 597),
(599, 599, 1, 28, 'N', 'N', 598),
(600, 600, 1, 28, 'N', 'N', 599),
(601, 601, 1, 30, 'N', 'S', 600),
(602, 602, 1, 26, 'N', 'N', 601),
(603, 603, 1, 26, 'N', 'N', 602),
(604, 604, 1, 30, 'N', 'N', 603),
(605, 605, 1, 30, 'N', 'N', 604),
(606, 606, 1, 22, 'N', 'N', 605),
(607, 607, 1, 21, 'N', 'N', 606),
(608, 608, 1, 21, 'N', 'N', 607),
(609, 609, 1, 21, 'N', 'N', 608),
(610, 610, 1, 21, 'N', 'N', 609),
(611, 611, 1, 21, 'N', 'N', 610),
(612, 612, 1, 21, 'N', 'N', 611),
(613, 613, 1, 21, 'N', 'N', 612),
(614, 614, 1, 21, 'N', 'N', 613),
(615, 615, 1, 21, 'N', 'N', 614),
(616, 616, 1, 28, 'N', 'N', 615),
(617, 617, 1, 28, 'N', 'N', 616),
(618, 618, 1, 28, 'N', 'N', 617),
(619, 619, 1, 28, 'N', 'N', 618),
(620, 620, 1, 28, 'N', 'N', 619),
(621, 621, 1, 28, 'N', 'N', 620),
(622, 622, 1, 28, 'N', 'N', 621),
(623, 623, 1, 28, 'N', 'N', 622),
(624, 624, 1, 28, 'N', 'N', 623),
(625, 625, 1, 28, 'N', 'N', 624),
(626, 626, 1, 28, 'N', 'N', 625),
(627, 627, 1, 28, 'N', 'N', 626),
(628, 628, 1, 28, 'N', 'N', 627),
(629, 629, 1, 28, 'N', 'N', 628),
(630, 630, 1, 28, 'N', 'N', 629),
(631, 631, 1, 28, 'N', 'N', 630),
(632, 632, 1, 52, 'N', 'N', 631),
(633, 633, 1, 52, 'N', 'N', 632),
(634, 634, 1, 52, 'N', 'N', 633),
(635, 635, 1, 52, 'N', 'N', 634),
(636, 636, 1, 52, 'N', 'N', 635),
(637, 637, 1, 14, 'N', 'N', 636),
(638, 638, 1, 27, 'N', 'N', 637),
(639, 639, 1, 27, 'N', 'N', 638),
(640, 640, 1, 28, 'N', 'N', 639),
(641, 641, 1, 20, 'N', 'N', 640),
(642, 642, 1, 20, 'N', 'N', 641),
(643, 643, 1, 20, 'N', 'N', 642),
(644, 644, 1, 20, 'N', 'N', 643),
(645, 645, 1, 20, 'N', 'N', 644),
(646, 646, 1, 20, 'N', 'N', 645),
(647, 647, 1, 20, 'N', 'N', 646),
(648, 648, 1, 20, 'N', 'N', 647),
(649, 649, 1, 20, 'N', 'N', 648),
(650, 650, 1, 20, 'N', 'N', 649),
(651, 651, 1, 20, 'N', 'N', 650),
(652, 652, 1, 20, 'N', 'N', 651),
(653, 653, 1, 20, 'N', 'N', 652),
(654, 654, 1, 20, 'N', 'N', 653),
(655, 655, 1, 20, 'N', 'N', 654),
(656, 656, 1, 20, 'N', 'N', 655),
(657, 657, 1, 20, 'N', 'N', 656),
(658, 658, 1, 20, 'N', 'N', 657),
(659, 659, 1, 20, 'N', 'N', 658),
(660, 660, 1, 20, 'N', 'N', 659),
(661, 661, 1, 20, 'N', 'N', 660),
(662, 662, 1, 20, 'N', 'N', 661),
(663, 663, 1, 20, 'N', 'N', 662),
(664, 664, 1, 20, 'N', 'N', 663),
(665, 665, 1, 20, 'N', 'N', 664),
(666, 666, 1, 20, 'N', 'N', 665);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_estudiante_prom_anual`
--

INSERT INTO `sw_estudiante_prom_anual` (`id`, `id_paralelo`, `id_estudiante`, `id_periodo_lectivo`, `ea_promedio`) VALUES
(1, 20, 3, 1, 0),
(2, 20, 4, 1, 0),
(3, 20, 5, 1, 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_feriado`
--

CREATE TABLE `sw_feriado` (
  `id_feriado` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `fe_fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_horario`
--

INSERT INTO `sw_horario` (`id_horario`, `id_asignatura`, `id_paralelo`, `id_dia_semana`, `id_hora_clase`) VALUES
(2, 53, 63, 1, 1),
(3, 52, 20, 1, 2),
(4, 9, 20, 1, 3),
(5, 5, 20, 1, 4),
(6, 14, 20, 2, 1),
(7, 53, 20, 2, 2),
(8, 54, 20, 2, 3);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_clase`
--

CREATE TABLE `sw_hora_clase` (
  `id_hora_clase` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `hc_nombre` varchar(12) CHARACTER SET latin1 NOT NULL,
  `hc_hora_inicio` time NOT NULL,
  `hc_hora_fin` time NOT NULL,
  `hc_ordinal` int(11) NOT NULL,
  `hc_tipo` char(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_hora_clase`
--

INSERT INTO `sw_hora_clase` (`id_hora_clase`, `id_periodo_lectivo`, `hc_nombre`, `hc_hora_inicio`, `hc_hora_fin`, `hc_ordinal`, `hc_tipo`) VALUES
(1, 1, 'Mat. 1ra', '07:00:00', '08:00:00', 1, ''),
(2, 1, 'Mat. 2da', '08:00:00', '09:00:00', 2, ''),
(3, 1, 'Mat. 3ra', '09:00:00', '10:00:00', 3, ''),
(4, 1, 'Mat. 4ta', '10:00:00', '11:00:00', 4, ''),
(5, 1, 'Vesp. 1ra', '13:00:00', '14:00:00', 6, ''),
(6, 1, 'Vesp. 2da', '14:00:00', '15:00:00', 7, ''),
(7, 1, 'Vesp. 3ra', '15:00:00', '16:00:00', 8, ''),
(8, 1, 'Vesp. 4ta', '16:00:00', '17:00:00', 9, ''),
(9, 1, 'Vesp. 5ta', '17:00:00', '18:00:00', 10, ''),
(10, 1, 'Mat. 5ta', '11:00:00', '12:00:00', 5, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_hora_dia`
--

CREATE TABLE `sw_hora_dia` (
  `id_hora_dia` int(11) NOT NULL,
  `id_dia_semana` int(11) NOT NULL,
  `id_hora_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_hora_dia`
--

INSERT INTO `sw_hora_dia` (`id_hora_dia`, `id_dia_semana`, `id_hora_clase`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 10),
(6, 1, 5),
(7, 1, 6),
(8, 1, 7),
(9, 1, 8),
(10, 1, 9),
(11, 2, 1),
(12, 2, 2),
(13, 2, 3),
(14, 2, 4),
(15, 2, 10),
(16, 2, 5),
(17, 2, 6),
(18, 2, 7),
(19, 2, 8),
(20, 2, 9),
(21, 3, 1),
(22, 3, 2),
(23, 3, 3),
(24, 3, 4),
(25, 3, 10),
(26, 3, 5),
(27, 3, 6),
(28, 3, 7),
(29, 3, 8),
(30, 3, 9),
(31, 4, 1),
(32, 4, 2),
(33, 4, 3),
(34, 4, 4),
(35, 4, 10),
(36, 4, 5),
(37, 4, 6),
(38, 4, 7),
(39, 4, 8),
(40, 4, 9),
(41, 5, 1),
(42, 5, 2),
(43, 5, 4),
(44, 5, 3),
(45, 5, 10),
(46, 5, 5),
(47, 5, 6),
(48, 5, 7),
(49, 5, 8),
(50, 5, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_inasistencia`
--

CREATE TABLE `sw_inasistencia` (
  `id_inasistencia` int(11) NOT NULL,
  `in_nombre` varchar(32) CHARACTER SET latin1 NOT NULL,
  `in_abreviatura` varchar(1) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_institucion`
--

CREATE TABLE `sw_institucion` (
  `id_institucion` int(11) NOT NULL,
  `in_nombre` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_direccion` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_telefono1` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_nom_rector` varchar(45) CHARACTER SET utf8 NOT NULL,
  `in_nom_vicerrector` varchar(45) CHARACTER SET latin1 NOT NULL,
  `in_nom_secretario` varchar(45) CHARACTER SET latin1 NOT NULL,
  `in_url` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_logo` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_amie` varchar(16) CHARACTER SET latin1 NOT NULL,
  `in_ciudad` varchar(64) CHARACTER SET latin1 NOT NULL,
  `in_copiar_y_pegar` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_institucion`
--

INSERT INTO `sw_institucion` (`id_institucion`, `in_nombre`, `in_direccion`, `in_telefono1`, `in_nom_rector`, `in_nom_vicerrector`, `in_nom_secretario`, `in_url`, `in_logo`, `in_amie`, `in_ciudad`, `in_copiar_y_pegar`) VALUES
(1, 'Unidad Educativa \"Ecuador Amazónica\"', '9 de Octubre, entre General Rumiñahui y Abdón', '0988885898', 'Msc. Bélgica Pita Velasco', '', '', 'https://ueamazonico.edu.ec/', '', '09H03230', 'DAULE', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_jornada`
--

CREATE TABLE `sw_jornada` (
  `id_jornada` int(11) NOT NULL,
  `jo_nombre` varchar(16) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_jornada`
--

INSERT INTO `sw_jornada` (`id_jornada`, `jo_nombre`) VALUES
(1, 'MATUTINA'),
(2, 'VESPERTINA');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_malla_curricular`
--

INSERT INTO `sw_malla_curricular` (`id_malla_curricular`, `id_periodo_lectivo`, `id_curso`, `id_paralelo`, `id_asignatura`, `ma_horas_presenciales`, `ma_horas_autonomas`, `ma_horas_tutorias`, `ma_subtotal`) VALUES
(1, 1, 4, 0, 53, 10, 0, 0, 10),
(3, 1, 4, 0, 54, 8, 0, 0, 8),
(4, 1, 3, 0, 6, 2, 1, 1, 3),
(5, 1, 3, 0, 3, 4, 2, 2, 6),
(6, 1, 3, 0, 54, 3, 1, 2, 5),
(7, 1, 18, 0, 6, 2, 0, 0, 2),
(8, 1, 18, 0, 54, 3, 0, 0, 3),
(9, 1, 24, 0, 6, 2, 0, 0, 2),
(10, 1, 24, 0, 54, 3, 0, 0, 3),
(11, 1, 17, 0, 54, 4, 0, 0, 4),
(12, 1, 23, 0, 54, 4, 2, 2, 6),
(13, 1, 11, 0, 53, 6, 6, 0, 6),
(14, 1, 13, 0, 12, 2, 2, 0, 2),
(15, 1, 8, 0, 53, 8, 0, 0, 8),
(16, 1, 8, 0, 54, 7, 0, 0, 7),
(17, 1, 8, 0, 9, 3, 0, 0, 3),
(18, 1, 8, 0, 5, 5, 0, 0, 5),
(19, 1, 8, 0, 13, 2, 0, 0, 2),
(20, 1, 8, 0, 14, 5, 0, 0, 5),
(27, 1, 7, 0, 53, 8, 0, 0, 8),
(28, 1, 7, 0, 54, 7, 0, 0, 7),
(29, 1, 7, 0, 9, 3, 0, 0, 3),
(30, 1, 7, 0, 5, 5, 0, 0, 5),
(31, 1, 7, 0, 13, 2, 0, 0, 2),
(32, 1, 7, 0, 14, 5, 0, 0, 5),
(33, 1, 7, 0, 52, 3, 0, 0, 3),
(34, 1, 7, 0, 56, 1, 0, 0, 1),
(35, 1, 9, 0, 53, 8, 0, 0, 8),
(36, 1, 9, 0, 54, 7, 0, 0, 7),
(37, 1, 9, 0, 9, 3, 0, 0, 3),
(38, 1, 9, 0, 5, 5, 0, 0, 5),
(39, 1, 9, 0, 13, 2, 0, 0, 2),
(40, 1, 9, 0, 14, 5, 0, 0, 5),
(41, 1, 9, 0, 52, 3, 0, 0, 3),
(42, 1, 9, 0, 56, 1, 0, 0, 1),
(43, 1, 5, 0, 53, 10, 0, 0, 10),
(44, 1, 5, 0, 54, 8, 0, 0, 8),
(45, 1, 5, 0, 9, 2, 0, 0, 2),
(46, 1, 5, 0, 5, 3, 0, 0, 3),
(47, 1, 5, 0, 13, 2, 0, 0, 2),
(48, 1, 5, 0, 14, 5, 0, 0, 5),
(49, 1, 5, 0, 52, 3, 0, 0, 3),
(50, 1, 5, 0, 56, 1, 0, 0, 1),
(53, 1, 17, 0, 6, 3, 0, 0, 3),
(54, 1, 23, 0, 6, 3, 3, 0, 3),
(55, 1, 12, 0, 54, 6, 0, 0, 6),
(56, 1, 11, 0, 54, 6, 0, 0, 6),
(57, 1, 14, 0, 53, 5, 0, 0, 5),
(58, 1, 15, 0, 53, 2, 2, 0, 2),
(59, 1, 14, 0, 12, 2, 0, 0, 2),
(60, 1, 1, 0, 7, 2, 0, 0, 2),
(62, 1, 10, 0, 52, 5, 0, 0, 5),
(63, 1, 11, 0, 52, 5, 5, 0, 5),
(64, 1, 1, 0, 12, 2, 2, 0, 2),
(65, 1, 16, 0, 12, 2, 2, 0, 2),
(66, 1, 22, 0, 12, 2, 2, 0, 2),
(67, 1, 19, 0, 12, 2, 2, 0, 2),
(68, 1, 2, 0, 12, 2, 2, 0, 2),
(69, 1, 17, 0, 12, 2, 0, 0, 2),
(70, 1, 23, 0, 12, 2, 0, 0, 2),
(71, 1, 20, 0, 12, 2, 0, 0, 2),
(72, 1, 3, 0, 4, 2, 2, 0, 2),
(73, 1, 1, 0, 33, 2, 2, 0, 2),
(74, 1, 2, 0, 33, 3, 3, 0, 3),
(75, 1, 3, 0, 33, 2, 2, 0, 2),
(76, 1, 21, 0, 41, 3, 0, 0, 3),
(77, 1, 19, 0, 39, 3, 0, 0, 3),
(78, 1, 19, 0, 34, 2, 0, 0, 2),
(79, 1, 20, 0, 34, 2, 0, 0, 2),
(80, 1, 21, 0, 34, 7, 0, 0, 7),
(81, 1, 20, 0, 35, 3, 0, 0, 3),
(82, 1, 19, 0, 36, 3, 0, 0, 3),
(83, 1, 20, 0, 36, 3, 0, 0, 3),
(84, 1, 11, 0, 56, 2, 0, 0, 2),
(85, 1, 12, 0, 56, 2, 2, 0, 2),
(86, 1, 2, 0, 52, 5, 5, 0, 5),
(87, 1, 3, 0, 52, 3, 3, 0, 3),
(88, 1, 17, 0, 52, 5, 0, 0, 5),
(89, 1, 23, 0, 52, 5, 0, 0, 5),
(90, 1, 20, 0, 52, 5, 0, 0, 5),
(91, 1, 21, 0, 52, 3, 0, 0, 3),
(92, 1, 1, 0, 14, 2, 2, 0, 2),
(93, 1, 16, 0, 14, 2, 2, 0, 2),
(94, 1, 22, 0, 14, 2, 2, 0, 2),
(95, 1, 19, 0, 14, 2, 0, 0, 2),
(96, 1, 2, 0, 14, 2, 2, 0, 2),
(97, 1, 17, 0, 14, 2, 2, 0, 2),
(98, 1, 23, 0, 14, 2, 0, 0, 2),
(99, 1, 20, 0, 14, 2, 0, 0, 2),
(100, 1, 13, 0, 11, 2, 2, 0, 2),
(101, 1, 14, 0, 11, 2, 0, 0, 2),
(102, 1, 3, 0, 10, 6, 6, 0, 6),
(103, 1, 4, 0, 9, 2, 0, 0, 2),
(104, 1, 4, 0, 5, 3, 0, 0, 3),
(105, 1, 4, 0, 13, 2, 0, 0, 2),
(106, 1, 4, 0, 14, 5, 0, 0, 5),
(107, 1, 4, 0, 52, 3, 0, 0, 3),
(108, 1, 4, 0, 56, 1, 0, 0, 1),
(109, 1, 6, 0, 53, 10, 0, 0, 10),
(110, 1, 6, 0, 54, 8, 0, 0, 8),
(111, 1, 6, 0, 9, 2, 0, 0, 2),
(112, 1, 6, 0, 5, 3, 0, 0, 3),
(113, 1, 6, 0, 13, 2, 0, 0, 2),
(114, 1, 6, 0, 14, 5, 0, 0, 5),
(115, 1, 6, 0, 52, 3, 0, 0, 3),
(116, 1, 6, 0, 56, 1, 0, 0, 1),
(117, 1, 8, 0, 52, 3, 0, 0, 3),
(118, 1, 8, 0, 56, 1, 0, 0, 1),
(119, 1, 10, 0, 53, 6, 0, 0, 6),
(120, 1, 10, 0, 54, 6, 0, 0, 6),
(121, 1, 10, 0, 9, 4, 0, 0, 4),
(122, 1, 10, 0, 5, 4, 0, 0, 4),
(123, 1, 10, 0, 13, 2, 0, 0, 2),
(124, 1, 10, 0, 14, 5, 0, 0, 5),
(125, 1, 10, 0, 56, 3, 0, 0, 3),
(126, 1, 11, 0, 9, 4, 0, 0, 4),
(127, 1, 11, 0, 5, 4, 0, 0, 4),
(128, 1, 11, 0, 13, 2, 0, 0, 2),
(129, 1, 11, 0, 14, 5, 0, 0, 5),
(130, 1, 12, 0, 53, 6, 0, 0, 6),
(131, 1, 12, 0, 9, 4, 0, 0, 4),
(132, 1, 12, 0, 5, 4, 0, 0, 4),
(133, 1, 12, 0, 13, 2, 0, 0, 2),
(134, 1, 12, 0, 14, 5, 0, 0, 5),
(135, 1, 12, 0, 52, 5, 0, 0, 5),
(136, 1, 1, 0, 54, 5, 0, 0, 5),
(137, 1, 1, 0, 6, 3, 0, 0, 3),
(138, 1, 1, 0, 8, 2, 0, 0, 2),
(139, 1, 1, 0, 10, 3, 0, 0, 3),
(140, 1, 13, 0, 54, 5, 0, 0, 5),
(141, 1, 13, 0, 6, 3, 0, 0, 3),
(142, 1, 13, 0, 7, 2, 0, 0, 2),
(143, 1, 13, 0, 8, 2, 0, 0, 2),
(144, 1, 13, 0, 10, 3, 0, 0, 3),
(145, 1, 13, 0, 53, 5, 0, 0, 5),
(146, 1, 13, 0, 52, 5, 0, 0, 5),
(147, 1, 13, 0, 13, 2, 0, 0, 2),
(148, 1, 13, 0, 14, 2, 0, 0, 2),
(149, 1, 13, 0, 55, 2, 0, 0, 2),
(150, 1, 13, 0, 16, 4, 0, 0, 4),
(151, 1, 13, 0, 19, 4, 0, 0, 4),
(152, 1, 13, 0, 21, 2, 0, 0, 2),
(153, 1, 14, 0, 54, 4, 0, 0, 4),
(154, 1, 14, 0, 6, 3, 0, 0, 3),
(155, 1, 14, 0, 7, 3, 0, 0, 3),
(156, 1, 14, 0, 8, 2, 0, 0, 2),
(157, 1, 14, 0, 10, 3, 0, 0, 3),
(158, 1, 14, 0, 13, 2, 0, 0, 2),
(159, 1, 14, 0, 14, 2, 0, 0, 2),
(160, 1, 14, 0, 55, 2, 0, 0, 2),
(161, 1, 14, 0, 52, 5, 0, 0, 5),
(162, 1, 14, 0, 16, 4, 0, 0, 4),
(163, 1, 14, 0, 17, 2, 0, 0, 2),
(164, 1, 14, 0, 20, 4, 0, 0, 4),
(165, 1, 15, 0, 54, 3, 0, 0, 3),
(166, 1, 15, 0, 6, 2, 0, 0, 2),
(167, 1, 15, 0, 7, 2, 0, 0, 2),
(168, 1, 15, 0, 8, 2, 0, 0, 2),
(169, 1, 15, 0, 10, 2, 0, 0, 2),
(170, 1, 15, 0, 52, 3, 0, 0, 3),
(171, 1, 15, 0, 14, 2, 0, 0, 2),
(172, 1, 15, 0, 55, 2, 0, 0, 2),
(173, 1, 15, 0, 15, 13, 0, 0, 13),
(174, 1, 15, 0, 17, 8, 0, 0, 8),
(175, 1, 15, 0, 18, 3, 0, 0, 3),
(176, 1, 15, 0, 21, 1, 0, 0, 1),
(177, 1, 15, 0, 22, 160, 0, 0, 160),
(178, 1, 19, 0, 54, 5, 0, 0, 5),
(179, 1, 19, 0, 6, 3, 0, 0, 3),
(180, 1, 19, 0, 7, 2, 0, 0, 2),
(181, 1, 19, 0, 8, 2, 0, 0, 2),
(182, 1, 19, 0, 10, 3, 0, 0, 3),
(183, 1, 19, 0, 11, 2, 0, 0, 2),
(184, 1, 19, 0, 53, 5, 0, 0, 5),
(185, 1, 19, 0, 52, 5, 0, 0, 5),
(186, 1, 19, 0, 13, 2, 0, 0, 2),
(187, 1, 19, 0, 55, 2, 0, 0, 2),
(188, 1, 19, 0, 40, 2, 0, 0, 2),
(189, 1, 20, 0, 54, 4, 0, 0, 4),
(190, 1, 20, 0, 6, 3, 0, 0, 3),
(191, 1, 20, 0, 7, 3, 0, 0, 3),
(192, 1, 20, 0, 8, 2, 0, 0, 2),
(193, 1, 20, 0, 10, 3, 0, 0, 3),
(194, 1, 20, 0, 11, 2, 0, 0, 2),
(195, 1, 20, 0, 53, 5, 0, 0, 5),
(196, 1, 20, 0, 13, 2, 0, 0, 2),
(197, 1, 20, 0, 55, 2, 0, 0, 2),
(198, 1, 20, 0, 40, 2, 0, 0, 2),
(199, 1, 21, 0, 54, 3, 0, 0, 3),
(200, 1, 21, 0, 6, 2, 0, 0, 2),
(201, 1, 21, 0, 7, 2, 0, 0, 2),
(202, 1, 21, 0, 8, 2, 0, 0, 2),
(203, 1, 21, 0, 10, 2, 0, 0, 2),
(204, 1, 21, 0, 53, 2, 0, 0, 2),
(205, 1, 21, 0, 14, 2, 0, 0, 2),
(206, 1, 21, 0, 55, 2, 0, 0, 2),
(207, 1, 21, 0, 36, 7, 0, 0, 7),
(208, 1, 21, 0, 37, 4, 0, 0, 4),
(209, 1, 21, 0, 38, 2, 0, 0, 2),
(211, 1, 22, 0, 54, 5, 0, 0, 5),
(212, 1, 22, 0, 6, 3, 0, 0, 3),
(213, 1, 22, 0, 7, 2, 0, 0, 2),
(214, 1, 22, 0, 8, 2, 0, 0, 2),
(215, 1, 22, 0, 10, 3, 0, 0, 3),
(216, 1, 22, 0, 11, 2, 0, 0, 2),
(217, 1, 22, 0, 53, 5, 0, 0, 5),
(218, 1, 22, 0, 52, 5, 0, 0, 5),
(219, 1, 22, 0, 13, 2, 0, 0, 2),
(220, 1, 22, 0, 55, 2, 0, 0, 2),
(221, 1, 22, 0, 47, 4, 0, 0, 4),
(222, 1, 22, 0, 48, 4, 0, 0, 4),
(223, 1, 22, 0, 50, 2, 0, 0, 2),
(224, 1, 23, 0, 7, 3, 0, 0, 3),
(225, 1, 23, 0, 8, 2, 0, 0, 2),
(226, 1, 23, 0, 10, 3, 0, 0, 3),
(227, 1, 23, 0, 53, 5, 0, 0, 5),
(228, 1, 23, 0, 13, 2, 0, 0, 2),
(229, 1, 23, 0, 55, 2, 0, 0, 2),
(230, 1, 23, 0, 47, 2, 0, 0, 2),
(231, 1, 23, 0, 48, 2, 0, 0, 2),
(232, 1, 23, 0, 49, 4, 0, 0, 4),
(233, 1, 23, 0, 50, 2, 0, 0, 2),
(234, 1, 24, 0, 7, 2, 0, 0, 2),
(235, 1, 24, 0, 8, 2, 0, 0, 2),
(236, 1, 24, 0, 10, 2, 0, 0, 2),
(237, 1, 24, 0, 53, 2, 0, 0, 2),
(238, 1, 24, 0, 52, 3, 0, 0, 3),
(239, 1, 24, 0, 14, 2, 0, 0, 2),
(240, 1, 24, 0, 55, 2, 0, 0, 2),
(241, 1, 24, 0, 43, 6, 0, 0, 6),
(242, 1, 24, 0, 44, 6, 0, 0, 6),
(243, 1, 24, 0, 45, 5, 0, 0, 5),
(244, 1, 24, 0, 46, 6, 0, 0, 6),
(245, 1, 24, 0, 47, 2, 0, 0, 2),
(247, 1, 24, 0, 51, 160, 0, 0, 160),
(248, 1, 16, 0, 54, 5, 0, 0, 5),
(249, 1, 16, 0, 6, 3, 0, 0, 3),
(250, 1, 16, 0, 7, 2, 0, 0, 2),
(251, 1, 16, 0, 8, 2, 0, 0, 2),
(252, 1, 16, 0, 10, 3, 0, 0, 3),
(253, 1, 16, 0, 11, 2, 0, 0, 2),
(254, 1, 16, 0, 53, 5, 0, 0, 5),
(255, 1, 16, 0, 52, 5, 0, 0, 5),
(256, 1, 16, 0, 13, 2, 0, 0, 2),
(257, 1, 16, 0, 55, 2, 0, 0, 2),
(258, 1, 16, 0, 27, 3, 0, 0, 3),
(259, 1, 16, 0, 28, 3, 0, 0, 3),
(260, 1, 16, 0, 29, 2, 0, 0, 2),
(261, 1, 16, 0, 30, 2, 0, 0, 2),
(262, 1, 17, 0, 7, 3, 0, 0, 3),
(263, 1, 17, 0, 8, 2, 0, 0, 2),
(264, 1, 17, 0, 10, 3, 0, 0, 3),
(265, 1, 17, 0, 11, 2, 0, 0, 2),
(266, 1, 17, 0, 53, 5, 0, 0, 5),
(267, 1, 17, 0, 13, 2, 0, 0, 2),
(268, 1, 17, 0, 55, 2, 0, 0, 2),
(269, 1, 17, 0, 23, 4, 0, 0, 4),
(270, 1, 17, 0, 24, 2, 0, 0, 2),
(271, 1, 17, 0, 25, 2, 0, 0, 2),
(272, 1, 17, 0, 28, 2, 0, 0, 2),
(273, 1, 18, 0, 7, 2, 0, 0, 2),
(274, 1, 18, 0, 8, 2, 0, 0, 2),
(275, 1, 18, 0, 10, 2, 0, 0, 2),
(276, 1, 18, 0, 53, 2, 0, 0, 2),
(277, 1, 18, 0, 52, 3, 0, 0, 3),
(278, 1, 18, 0, 14, 2, 0, 0, 2),
(279, 1, 18, 0, 55, 2, 0, 0, 2),
(280, 1, 18, 0, 23, 5, 0, 0, 5),
(281, 1, 18, 0, 24, 6, 0, 0, 6),
(282, 1, 18, 0, 25, 8, 0, 0, 8),
(283, 1, 18, 0, 26, 5, 0, 0, 5),
(284, 1, 18, 0, 30, 1, 0, 0, 1),
(285, 1, 18, 0, 31, 160, 0, 0, 160),
(286, 1, 21, 0, 40, 2, 0, 0, 2),
(287, 1, 21, 0, 42, 160, 0, 0, 160),
(288, 1, 2, 0, 6, 3, 0, 0, 3),
(289, 1, 2, 0, 54, 4, 0, 0, 4),
(290, 1, 2, 0, 53, 5, 0, 0, 5),
(291, 1, 3, 0, 53, 2, 0, 0, 2),
(292, 1, 3, 0, 8, 2, 0, 0, 2),
(293, 1, 3, 0, 1, 4, 0, 0, 4),
(294, 1, 3, 0, 2, 5, 0, 0, 5),
(295, 1, 3, 0, 7, 2, 0, 0, 2),
(296, 1, 3, 0, 55, 2, 0, 0, 2),
(297, 1, 2, 0, 55, 2, 0, 0, 2),
(298, 1, 1, 0, 53, 5, 0, 0, 5),
(299, 1, 2, 0, 8, 2, 0, 0, 2),
(300, 1, 2, 0, 7, 3, 0, 0, 3),
(301, 1, 1, 0, 52, 5, 0, 0, 5),
(302, 1, 1, 0, 32, 3, 0, 0, 3),
(303, 1, 2, 0, 32, 2, 0, 0, 2),
(304, 1, 3, 0, 32, 3, 0, 0, 3);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_menu`
--

INSERT INTO `sw_menu` (`id_menu`, `id_perfil`, `mnu_texto`, `mnu_enlace`, `mnu_link`, `mnu_nivel`, `mnu_orden`, `mnu_padre`, `mnu_publicado`, `mnu_icono`) VALUES
(2, 1, 'Menus', 'menu/index2.php', 'administracion/menus', 2, 5, 146, 1, 'fa fa-circle-o'),
(12, 1, 'Perfiles', 'perfil/view_index.php', 'administracion/perfiles', 2, 4, 146, 1, 'fa fa-circle-o'),
(13, 2, 'Calificaciones', '#', '', 1, 2, 0, 1, ''),
(18, 1, 'Usuarios', 'usuario/view_index.php', 'administracion/usuarios', 2, 7, 146, 1, 'fa fa-circle-o'),
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
(116, 7, 'Parciales', 'tutores/comp_parciales2.php', '', 2, 1, 58, 1, ''),
(117, 7, 'Quimestrales', 'tutores/comportamiento2.php', '', 2, 2, 58, 1, ''),
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
(176, 1, 'Por Hacer', 'por_hacer/index.php', '#', 2, 1, 146, 0, 'fa fa-list-alt'),
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
(231, 1, 'Institución', 'institucion/index.php', '', 2, 6, 146, 1, ''),
(232, 1, 'Modalidades', 'modalidades/view_index.php', '', 2, 2, 146, 1, ''),
(233, 1, 'Curso Superior', 'asociar_curso_superior/index.php', '', 2, 4, 166, 1, ''),
(235, 1, 'Educación Inicial', 'educacion_inicial/index.php', '', 2, 1, 159, 1, ''),
(236, 2, 'Tareas', 'tareas/index.php', '', 2, 1, 13, 1, ''),
(239, 2, 'Parciales', 'calificaciones/index.php', '', 2, 2, 13, 1, ''),
(240, 1, 'Especificaciones', '#', '', 1, 3, 0, 1, ''),
(241, 1, 'Períodos de Evaluación', 'periodos_evaluacion/index2.php', '', 2, 1, 240, 1, ''),
(242, 1, 'Aportes de Evaluación', 'aportes_evaluacion/index2.php', '', 2, 2, 240, 1, ''),
(243, 1, 'Rúbricas de Evaluación', 'rubricas_evaluacion/index2.php', '', 2, 3, 240, 1, ''),
(244, 1, 'Escalas de Calificaciones', 'escalas/index2.php', '', 2, 4, 240, 1, ''),
(245, 7, 'Enlaces', '#', '', 1, 5, 0, 1, ''),
(246, 7, 'Asistencia', 'http://asistencia.ueamazonico.edu.ec/', '', 2, 1, 245, 1, ''),
(247, 7, 'Registro de teletrabajo', 'http://ueamazonico.edu.ec/teletrabajo', '', 2, 2, 245, 1, ''),
(248, 7, 'Recursos Docentes', 'https://drive.google.com/drive/folders/1oOtAmN_EFd3dXAOFVEHa_UFk', '', 2, 3, 245, 1, ''),
(249, 2, 'Enlaces', '#', '', 1, 9, 0, 1, ''),
(250, 2, 'Asistencia', 'https://asistencia.ueamazonico.edu.ec', '', 2, 1, 249, 1, ''),
(251, 2, 'Registro de Teletrabajo', 'https://ueamazonico.edu.ec/teletrabajo', '', 2, 2, 249, 1, ''),
(252, 2, 'Recursos', 'https://drive.google.com/drive/folders/1oOtAmN_EFd3dXAOFVEHa_UFk', '', 2, 3, 249, 1, ''),
(253, 7, 'Matriculación', '#', '', 1, 6, 0, 1, ''),
(254, 7, 'Matricular a Estud.', 'matriculacion/index.php', '', 2, 1, 253, 1, ''),
(255, 7, 'Matriculación a Proyectos', 'matriculacion/clubes.php', '', 2, 2, 253, 0, ''),
(256, 1, 'Plan Anual', 'plan_anual/index.php', '', 2, 8, 159, 1, ''),
(257, 2, 'Planificaciones', 'planificaciones/index.php', '', 2, 4, 45, 1, ''),
(258, 1, 'cuestionarios', 'cuestionarios/index.php', '', 2, 9, 159, 1, ''),
(259, 1, 'inasistencia', 'inasistencia/index.php', '', 2, 10, 159, 1, ''),
(260, 1, 'foros', 'foros/index.php', '', 2, 11, 159, 1, ''),
(261, 6, 'Lista de Docentes', 'autoridad/lista_docentes.php', '', 2, 3, 213, 1, ''),
(262, 7, 'Anual', 'tutores/comp_anual.php', '', 2, 3, 58, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_menu_perfil`
--

CREATE TABLE `sw_menu_perfil` (
  `id_perfil` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `mo_nombre` varchar(32) CHARACTER SET latin1 NOT NULL,
  `mo_activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_modalidad`
--

INSERT INTO `sw_modalidad` (`id_modalidad`, `mo_nombre`, `mo_activo`) VALUES
(1, 'ORDINARIA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo`
--

CREATE TABLE `sw_paralelo` (
  `id_paralelo` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_jornada` int(11) NOT NULL,
  `pa_nombre` varchar(16) CHARACTER SET latin1 NOT NULL,
  `pa_orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_paralelo`
--

INSERT INTO `sw_paralelo` (`id_paralelo`, `id_periodo_lectivo`, `id_curso`, `id_jornada`, `pa_nombre`, `pa_orden`) VALUES
(1, 1, 4, 1, 'A', 5),
(3, 1, 5, 1, 'A', 6),
(4, 1, 5, 1, 'B', 7),
(5, 1, 6, 1, 'A', 8),
(6, 1, 6, 1, 'B', 9),
(7, 1, 7, 1, 'A', 10),
(8, 1, 7, 1, 'B', 11),
(9, 1, 8, 1, 'A', 12),
(10, 1, 8, 1, 'B', 13),
(11, 1, 9, 1, 'A', 14),
(12, 1, 9, 1, 'B', 15),
(13, 1, 4, 2, 'A', 36),
(14, 1, 5, 2, 'A', 37),
(15, 1, 6, 2, 'A', 38),
(16, 1, 7, 2, 'A', 39),
(17, 1, 8, 2, 'A', 40),
(18, 1, 9, 2, 'A', 41),
(19, 1, 9, 2, 'B', 42),
(20, 1, 10, 1, 'A', 16),
(21, 1, 10, 1, 'B', 17),
(22, 1, 10, 1, 'C', 18),
(23, 1, 11, 1, 'A', 19),
(24, 1, 11, 1, 'B', 20),
(25, 1, 11, 1, 'C', 21),
(26, 1, 12, 1, 'A', 22),
(27, 1, 12, 1, 'B', 23),
(28, 1, 13, 1, 'A', 24),
(29, 1, 13, 1, 'B', 25),
(30, 1, 13, 1, 'C', 26),
(31, 1, 14, 1, 'A', 28),
(32, 1, 14, 1, 'B', 27),
(33, 1, 14, 1, 'C', 29),
(34, 1, 15, 1, 'A', 30),
(35, 1, 15, 1, 'B', 31),
(36, 1, 15, 1, 'C', 32),
(37, 1, 10, 2, 'A', 43),
(38, 1, 10, 2, 'B', 44),
(39, 1, 11, 2, 'A', 45),
(40, 1, 11, 2, 'B', 46),
(41, 1, 11, 2, 'C', 47),
(42, 1, 12, 2, 'A', 48),
(43, 1, 12, 2, 'B', 49),
(44, 1, 19, 2, 'A MODISTERIA', 50),
(45, 1, 19, 2, 'B SASTRERIA', 51),
(46, 1, 20, 2, 'A MODISTERIA', 52),
(47, 1, 20, 2, 'B SASTRERIA', 53),
(48, 1, 21, 2, 'A MODISTERIA', 54),
(49, 1, 21, 2, 'B SASTRERIA', 55),
(50, 1, 22, 2, 'A', 56),
(51, 1, 23, 2, 'A', 57),
(52, 1, 24, 2, 'A', 58),
(53, 1, 16, 2, 'A', 59),
(54, 1, 17, 2, 'A', 60),
(55, 1, 18, 2, 'A', 61),
(56, 1, 1, 2, 'A', 62),
(57, 1, 2, 2, 'A', 63),
(58, 1, 3, 2, 'A', 64),
(63, 1, 27, 1, 'A', 4);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_inspector`
--

CREATE TABLE `sw_paralelo_inspector` (
  `id_paralelo_inspector` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_paralelo_tutor`
--

CREATE TABLE `sw_paralelo_tutor` (
  `id_paralelo_tutor` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_paralelo_tutor`
--

INSERT INTO `sw_paralelo_tutor` (`id_paralelo_tutor`, `id_paralelo`, `id_usuario`, `id_periodo_lectivo`) VALUES
(60, 1, 52, 1),
(61, 3, 62, 1),
(62, 4, 64, 1),
(63, 5, 15, 1),
(64, 6, 49, 1),
(65, 7, 39, 1),
(66, 8, 30, 1),
(67, 9, 57, 1),
(68, 10, 75, 1),
(69, 11, 32, 1),
(70, 12, 25, 1),
(72, 21, 9, 1),
(73, 22, 21, 1),
(74, 23, 66, 1),
(75, 24, 46, 1),
(76, 25, 81, 1),
(77, 26, 55, 1),
(78, 27, 14, 1),
(79, 28, 23, 1),
(80, 29, 18, 1),
(81, 30, 35, 1),
(83, 32, 22, 1),
(84, 31, 70, 1),
(85, 33, 2, 1),
(86, 34, 42, 1),
(87, 35, 36, 1),
(88, 36, 65, 1),
(89, 20, 90, 1),
(90, 13, 92, 1),
(91, 14, 37, 1),
(92, 15, 84, 1),
(93, 16, 94, 1),
(94, 17, 45, 1),
(95, 18, 19, 1),
(96, 19, 91, 1),
(97, 37, 38, 1),
(98, 38, 82, 1),
(99, 39, 48, 1),
(100, 40, 5, 1),
(101, 41, 47, 1),
(102, 42, 93, 1),
(103, 43, 63, 1),
(104, 44, 53, 1),
(105, 45, 28, 1),
(106, 46, 26, 1),
(107, 47, 44, 1),
(108, 48, 12, 1),
(109, 49, 73, 1),
(110, 50, 8, 1),
(111, 51, 54, 1),
(112, 52, 11, 1),
(113, 53, 24, 1),
(114, 54, 71, 1),
(115, 55, 77, 1),
(116, 56, 72, 1),
(117, 57, 4, 1),
(118, 58, 27, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_perfil`
--

CREATE TABLE `sw_perfil` (
  `id_perfil` int(11) NOT NULL,
  `pe_nombre` varchar(16) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `pe_nivel_acceso` int(11) NOT NULL DEFAULT '2',
  `pe_acceso_login` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
(13, 'Dece', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_periodo_estado`
--

CREATE TABLE `sw_periodo_estado` (
  `id_periodo_estado` int(11) NOT NULL,
  `pe_descripcion` varchar(15) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `pe_abreviatura` varchar(6) CHARACTER SET latin1 NOT NULL,
  `pe_shortname` varchar(15) CHARACTER SET latin1 NOT NULL,
  `pe_principal` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_periodo_evaluacion`
--

INSERT INTO `sw_periodo_evaluacion` (`id_periodo_evaluacion`, `id_periodo_lectivo`, `id_tipo_periodo`, `pe_nombre`, `pe_abreviatura`, `pe_shortname`, `pe_principal`) VALUES
(1, 1, 0, 'PRIMER QUIMESTRE', '1ER.Q.', '', 1),
(2, 1, 0, 'SEGUNDO QUIMESTRE', '2DO.Q.', '', 1),
(3, 1, 0, 'SUPLETORIO', 'SUP.', '', 2),
(4, 1, 0, 'REMEDIAL', 'REM.', '', 3),
(5, 1, 0, 'GRACIA', 'GRA.', '', 4);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_periodo_lectivo`
--

INSERT INTO `sw_periodo_lectivo` (`id_periodo_lectivo`, `id_periodo_estado`, `id_modalidad`, `id_institucion`, `pe_anio_inicio`, `pe_anio_fin`, `pe_estado`, `pe_fecha_inicio`, `pe_fecha_fin`) VALUES
(1, 1, 1, 1, 2021, 2022, 'A', '2021-04-18', '2022-02-25');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_plan_rubrica`
--

CREATE TABLE `sw_plan_rubrica` (
  `id_plan_rubrica` int(11) NOT NULL,
  `pr_tema` varchar(50) CHARACTER SET latin1 NOT NULL,
  `pr_descripcion` varchar(250) CHARACTER SET latin1 NOT NULL,
  `pr_fecha_elab` datetime NOT NULL,
  `pr_fecha_eval` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_representante`
--

CREATE TABLE `sw_representante` (
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `re_apellidos` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `re_nombres` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `re_nombre_completo` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `re_cedula` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `re_genero` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `re_email` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `re_sector` varchar(36) CHARACTER SET utf8 DEFAULT NULL,
  `re_direccion` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `re_telefono` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `re_observacion` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `re_parentesco` varchar(45) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci COMMENT='					';

--
-- Volcado de datos para la tabla `sw_representante`
--

INSERT INTO `sw_representante` (`id_representante`, `id_estudiante`, `re_apellidos`, `re_nombres`, `re_nombre_completo`, `re_cedula`, `re_genero`, `re_email`, `re_sector`, `re_direccion`, `re_telefono`, `re_observacion`, `re_parentesco`) VALUES
(1, 6, 'SEGURA MORAN', 'ZOILA CAROLINA', 'SEGURA MORAN ZOILA CAROLINA', '0919944892', NULL, '', '', 'KM.42 VIA A DAULE', '0963747286', NULL, 'MAMA'),
(2, 7, 'Adrian Olvera', 'David Celestino', 'Adrian Olvera David Celestino', '0916400336', NULL, '', 'Lomas de Sargentllo', 'Las Cañas recinto el Mamey', '0988575189', NULL, 'Padre'),
(3, 9, 'MENDOZA ROMERO', 'EDY MARCELA', 'MENDOZA ROMERO EDY MARCELA', '1308860699', NULL, '', 'NOBOL', 'NOBOL VIA LOMAS DE SARGENTILLO KM/40.30', '0962797695', NULL, 'MAMA'),
(4, 13, 'MORAN NIEVES', 'NANCY MARIELA', 'MORAN NIEVES NANCY MARIELA', '', NULL, '', '', '', '991534711', NULL, 'MAMA'),
(5, 11, 'Alvarado Jiménez', 'Julio', 'Alvarado Jiménez Julio', '0910196443', NULL, '', 'Santa Lucía', 'Rcto. La Lorena', '0978974780', NULL, 'Padre'),
(6, 17, 'ALVARADO DUQUE', 'JIMMY HILARIO', 'ALVARADO DUQUE JIMMY HILARIO', '', NULL, '', '', '', '968996002', NULL, 'abuelo'),
(7, 15, 'AVILES RUIZ', 'MARGARITA BEATRIZ', 'AVILES RUIZ MARGARITA BEATRIZ', '0920086717', NULL, '', 'GORKY MEDRANDA Y GENERAL RUMIÑAG', 'PADRE JUAN BAUTISTA', '0961662106', NULL, 'MAMA'),
(8, 18, 'BRIONES CÓRDOVA', 'FÁTIMA DEL CARMEN', 'BRIONES CÓRDOVA FÁTIMA DEL CARMEN', '2400106023', NULL, 'fatimabrio', 'DAULE', 'DIONISIO NAVAS Y BOLIVAR Y SUCRE', '0992065902', NULL, 'MAMA'),
(9, 21, 'PLUAS RUIZ', 'JOVA ELIZABETH', 'PLUAS RUIZ JOVA ELIZABETH', '0912359437', NULL, '', 'SAN FRANCISCO', 'DAULE', '0967070964', NULL, 'MAMA'),
(10, 20, 'RUIZ RUIZ', 'ESTHER MARÌA', 'RUIZ RUIZ ESTHER MARÌA', '0922275821', NULL, '', 'LA FABIOLA', 'KM 15 VIA GUAYAQUIL SALITRE', '0959280058', NULL, 'madrasta'),
(11, 22, 'JUPITER ANZOATEGUI', 'ANA DEL ROCIO', 'JUPITER ANZOATEGUI ANA DEL ROCIO', '0919274597', NULL, '', 'DAULE', 'CDLA JUAN BAUTISTA AGUIRRE OSMAEL PEREZ PAZMIÑO Y LEONIDAS LA TO', '990591332', NULL, 'MADRE'),
(12, 23, 'Villamar Salazar', 'Diana Edilma', 'Villamar Salazar Diana Edilma', '0921800645', NULL, '', 'Guayaquil', 'PuenteLucia Km 26 via a Daule', '0997087652', NULL, 'Madre'),
(13, 24, 'Castro Campuzano', 'Luisana Elizabeth', 'Castro Campuzano Luisana Elizabeth', '0920940640', NULL, '', 'Guayaquil', 'Puente Lucia Eugenio Espejo/Mz12 SL7A', '0991496680', NULL, 'Madre'),
(14, 25, 'MORENO CORTEZ', 'FRANCISCA ISABEL', 'MORENO CORTEZ FRANCISCA ISABEL', '0908776396', NULL, '', 'EL MATE', 'EL MATE', '0980381868', NULL, 'TIA'),
(15, 29, 'ANCHUNDIA GOMEZ', 'HERLINDA ANABELL', 'ANCHUNDIA GOMEZ HERLINDA ANABELL', '0922545892', NULL, '', 'DAULE', 'MARIANITA 4', '0981180878', NULL, 'MADRE'),
(16, 31, 'INTRIAGO VERA', 'JENNIFER ELIZABETH', 'INTRIAGO VERA JENNIFER ELIZABETH', '1310714108', NULL, '', 'FRENTE AL COLEGIO RIBERAS DEL DA', 'DULE', '0996404839', NULL, 'MAMA'),
(17, 33, 'CASTRO SEGURA', 'LUIS ALBERTO', 'CASTRO SEGURA LUIS ALBERTO', '926834052', NULL, '', 'FATIMA', 'SANTA LUCIA', '0939503115', NULL, 'PAPA'),
(18, 36, 'CAICEDO PIGUAVE', 'MARTHA KARINA', 'CAICEDO PIGUAVE MARTHA KARINA', '0920841533', NULL, '', 'FRANCISCO VALVERDE Y ANTONIO HUA', 'DAULE', '0969224798', NULL, 'MAMA'),
(19, 38, 'QUINTO GUILLEN', 'LUCRECIA ISABEL', 'QUINTO GUILLEN LUCRECIA ISABEL', '0914188461', NULL, 'lucrediama', 'DESVIO DE LAS CAÑAS', 'RECINTO SAN GABRIEL', '0990786780', NULL, 'MADRE'),
(20, 40, 'MAGALLANES MENDOZA', 'MARIUXI YADIRA', 'MAGALLANES MENDOZA MARIUXI YADIRA', '0925536880', NULL, '', 'LOMAS CALLE B Y VIRGEN DEL MONSE', 'LOMAS DE SARGENTILLO', '0961346158', NULL, 'MAMA'),
(21, 44, 'VILLAMAR GAVILANEZ', 'VERÓNICA DEL ROCÍO', 'VILLAMAR GAVILANEZ VERÓNICA DEL ROCÍO', '0801107418', NULL, '', 'TEOFILIO CAICEDO QUITO', 'DAULE', '0985941209', NULL, 'MAMA'),
(22, 46, 'CALLE RUIZ', 'CRISTHIAN ALBERTO', 'CALLE RUIZ CRISTHIAN ALBERTO', '919468280', NULL, '', 'EL MATE', 'EL MATE', '0994942035', '', 'PADRE'),
(23, 48, 'TORRES LEÓN', 'HAIDEE CLARA', 'TORRES LEÓN HAIDEE CLARA', '0922133597', NULL, '', 'FLOR DE MARÍA', 'DAULE', '0980972292', NULL, 'MAMA'),
(24, 49, 'HARO RAMIREZ', 'JAVIER ARNULFO', 'HARO RAMIREZ JAVIER ARNULFO', '0916816572', NULL, '', 'CIUDADELA BELEN', 'DAULE', '0989674763', NULL, 'PAPA'),
(25, 52, 'ROMERO GUERRERO', 'CAROLINA MARGOTH', 'ROMERO GUERRERO CAROLINA MARGOTH', '0920844461', NULL, '', 'BOQUERON', 'DAULE', '0967849907', NULL, 'MAMA'),
(26, 53, 'MACIAS VILLEGAS', 'CECILIA AMARILIS', 'MACIAS VILLEGAS CECILIA AMARILIS', '0920346780', NULL, '', 'DAULE', 'EL RECUERDO', '0991794253', '', 'MADRE'),
(27, 54, 'CHAVEZ TORRES', 'JACINTA GELACIA', 'CHAVEZ TORRES JACINTA GELACIA', '0916172547', NULL, '', 'JOSE GOMEZ', 'DAULE', '0959017329', NULL, 'MAMA'),
(28, 55, 'CHAVEZ TORRES', 'JACINTA GELACIA', 'CHAVEZ TORRES JACINTA GELACIA', '0916172547', NULL, '', 'JOSE GOMEZ', 'DAULE', '0959017329', NULL, 'MAMA'),
(29, 56, 'RUÍZ RUÍZ', 'FRESIA TEODORA', 'RUÍZ RUÍZ FRESIA TEODORA', '0915026702', NULL, '', 'PEDRO PANTALEON LEONEL TORRES', 'DAULE', '0980831739', NULL, 'MAMA'),
(30, 58, 'LUNA RODRIGUEZ', 'ELENA MARISOL', 'LUNA RODRIGUEZ ELENA MARISOL', '0924430499', NULL, '', 'KM 27 VIA DAULE', 'DAULE', '0993754139', NULL, 'MAMA'),
(31, 59, 'MEDINA RUGEL', 'ROLANDO ISIDRO', 'MEDINA RUGEL ROLANDO ISIDRO', '0919462440', NULL, '', 'RECINTO SANTA ROSA', 'DAULE', '0979474320', NULL, 'PAPA'),
(32, 60, 'DUQUE GONZALEZ', 'CINTHIA FRANCIA', 'DUQUE GONZALEZ CINTHIA FRANCIA', '0926707415', NULL, '', 'BRISAS DE DAULE', 'DAULE', '0992288919', NULL, 'MAMA'),
(33, 61, 'MINDIOLAZA TORRES', 'ANDRES FILIBERTO', 'MINDIOLAZA TORRES ANDRES FILIBERTO', '0908831464', NULL, '', 'RECINTO BOCAS DE LAS PIÑAS', 'DAULE', '0999588715', NULL, 'PAPA'),
(34, 65, 'SAMANIEGO DIAZ', 'VERONICA ALEXANDRA', 'SAMANIEGO DIAZ VERONICA ALEXANDRA', '0928925437', NULL, '', 'RIBERAS OPUESTAS', 'DAULE', '0961094563', NULL, 'MADRASTRA'),
(35, 66, 'ROMERO MERCHAN', 'VIVIANA DE JESÚS', 'ROMERO MERCHAN VIVIANA DE JESÚS', '0925486821', NULL, '', 'AV.SAN FRANCISCO', 'DAULE', '0969584729', NULL, 'MAMA'),
(36, 67, 'ESPINOZA MERA', 'DIANA DEL PILAR', 'ESPINOZA MERA DIANA DEL PILAR', '0915820872', NULL, '', 'CALLE QUITO', 'DAULE', '0990356842', NULL, 'TIA'),
(37, 68, 'LEÓN TORRES', 'LINDA KATTY', 'LEÓN TORRES LINDA KATTY', '0915422943', NULL, '', 'BOLIVAR ENTRE FEBRES CORDERO Y P', 'DAULE', '0982075346', NULL, 'MAMA'),
(38, 69, 'GAVILANES NAVARRETE', 'ANA LUISA', 'GAVILANES NAVARRETE ANA LUISA', '0921968533', NULL, '', 'OLMEDO ALMEIDA Y ABDON CALDERON', 'DAULE', '0990947505', NULL, 'MAMA'),
(39, 70, 'FUENTES QUINTO', 'MIRNA ELIZABETH', 'FUENTES QUINTO MIRNA ELIZABETH', '0916375413', NULL, '', 'BOLIVAR Y PIEDRAHITA', 'DAULE', '0989769557', NULL, 'MAMA'),
(40, 71, 'PIGUAVE TORRES', 'MARICELA ANDREA', 'PIGUAVE TORRES MARICELA ANDREA', '0916176514', NULL, '', 'COOP NARCISA DE JESUS', 'DAULE', '0983714980', NULL, 'MAMA'),
(41, 72, 'Ruíz Peñefiel', 'Eduardo Gregorio', 'Ruíz Peñefiel Eduardo Gregorio', '09', NULL, '', 'Daule', 'Av. San Francisco', '0961712755', NULL, 'Papá'),
(42, 74, 'Ana Jimenez', 'Mariuxi Aracelly', 'Ana Jimenez Mariuxi Aracelly', '0921124053', NULL, '', 'Vía cascol de bahona', 'DAULE', '991836807', NULL, 'MAMÁ'),
(43, 75, 'AROCA LOPEZ', 'BRIGITE LASTENIA', 'AROCA LOPEZ BRIGITE LASTENIA', '0922278940', NULL, '', 'EL RECUERDO', 'DAULE', '0980078676', NULL, 'MAMA'),
(44, 76, 'LOZANO LEON', 'FABIOLA ANNABELL', 'LOZANO LEON FABIOLA ANNABELL', '952189169', NULL, '', 'OLMEDO Y ABDON CALDERON', 'DAULE', '967920648', NULL, 'MAMÁ'),
(45, 77, 'MORAN PEREZ', 'PALMIRA MARIA', 'MORAN PEREZ PALMIRA MARIA', '0917593386', NULL, '', 'EL TAPE', 'PARROQUIA LOS LOJAS', '0963380091', NULL, 'MADRE'),
(46, 78, 'ALVEAR CALERO', 'NATALY LORENA', 'ALVEAR CALERO NATALY LORENA', '921023628', NULL, '', 'ARENAL', 'KM 42 VIA A DAULE', '0987507141', NULL, 'MADRE'),
(47, 79, 'MAGALLANES LOPEZ', 'CLAUDIA FRANCISCA', 'MAGALLANES LOPEZ CLAUDIA FRANCISCA', '0917169017', NULL, '', 'LA RINCONADA', 'LA RINCONADA POR LA TOMA DE AGUA', '0993683164', NULL, 'MADRE'),
(48, 80, 'MENDEZ RIVAS', 'YOLANDA ELIZABETH', 'MENDEZ RIVAS YOLANDA ELIZABETH', '0922494406', NULL, '', 'DAULE', 'Av.San Francisco e Isidro FaJARDO', '0986997096', NULL, 'MAMA'),
(49, 81, 'RIVAS GAVILANEZ', 'MARIA', 'RIVAS GAVILANEZ MARIA', '0913368429', NULL, '', 'DAULE', 'DAULE', '0994352332', NULL, 'MAMA'),
(50, 83, 'AZPIAZU RUIZ', 'DIANA DEL CARMEN', 'AZPIAZU RUIZ DIANA DEL CARMEN', '0924723075', NULL, '', 'Cdl marianita 4', 'Cerca del portal.de los Caicedos  DAULE', '0993669500', NULL, 'MAMA'),
(51, 82, 'MORAN', 'LUCY', 'MORAN LUCY', '916490105', NULL, '', 'yolita', 'por la hugo serrano', '0997319227', NULL, 'mama'),
(52, 84, 'CUSME CEVALLOS', 'ROSA EDITA', 'CUSME CEVALLOS ROSA EDITA', '1312128224', NULL, '', 'PETRILLO', 'N0BOL', '0994145296', NULL, 'MAMA'),
(53, 85, 'CORDOVA', 'LORENA', 'CORDOVA LORENA', '926166083', NULL, '', 'POR LA HUGO SERRANO', 'YOLITA', '985521311', NULL, 'mama'),
(54, 86, 'ANCHUNDIA SANCHEZ', 'JEFFERSON FERNANDO', 'ANCHUNDIA SANCHEZ JEFFERSON FERNANDO', '922545157', NULL, '', 'MARIANITA 1', 'daule', '0985982739', NULL, 'papa'),
(55, 87, 'LAVAYEN MERCADO', 'LUCIA MARIA', 'LAVAYEN MERCADO LUCIA MARIA', '0921526703', NULL, '', 'DAULE', 'Manuel villavicencio y pablo hannibal velez', '099198295', NULL, 'MAMA'),
(56, 90, 'ALVAREZ TOMALA', 'CARMEN CECILIA', 'ALVAREZ TOMALA CARMEN CECILIA', '0910453414', NULL, '', 'JUAN BAUTISTA AGUIRRE DAULE', 'José Vélez Callejón Babahoyo', '0993029338', NULL, 'MAMA'),
(57, 89, 'FRANCO TORRES', 'EDWIN HERNAN', 'FRANCO TORRES EDWIN HERNAN', '1803821089', NULL, '', 'LAUREL', 'RECTO. LOS ANGELES', '0994044864', NULL, 'PAPA'),
(58, 91, 'CASTRO CASTRO', 'MARTHA ANGELICA', 'CASTRO CASTRO MARTHA ANGELICA', '0927559641', NULL, '', 'DAULE', 'Calle domingo comin Y san martin', '0999170403', NULL, 'MAMA'),
(59, 92, 'ZAMBRANO SALAVARRÌA', 'CAROLA JAQUELINE', 'ZAMBRANO SALAVARRÌA CAROLA JAQUELINE', '0917688780', NULL, '', 'DAULE', 'Ciudadela San Josè', '0987241336', NULL, 'MAMÀ'),
(60, 94, 'RAMIREZ SEVILLANO', 'RUTH ALEXANDRA', 'RAMIREZ SEVILLANO RUTH ALEXANDRA', '0924690282', NULL, '', '', 'Cooperativa los àngeles (la toma)', '0997890180', NULL, 'MAMÀ'),
(61, 93, 'Pluas Quijije', 'Tomasa Monserrate', 'Pluas Quijije Tomasa Monserrate', '0918108812', NULL, '', 'Palestina', 'Recinto Coloradal', '0994526711', NULL, 'Madre'),
(62, 95, 'MURRIETA DÀVILA', 'MARÌA MARIUXI', 'MURRIETA DÀVILA MARÌA MARIUXI', '0921249637', NULL, '', '', 'SALITRE', '0985745662', NULL, 'MAMÀ'),
(63, 97, 'Silva Piza', 'Ana Maria', 'Silva Piza Ana Maria', '0917416950', NULL, '', 'Nobol', 'Recinto los Canales Viaa Lomas de Sargentillo', '0989409741', NULL, 'Madre'),
(64, 98, 'Silva Piza', 'Ana Maria', 'Silva Piza Ana Maria', '0917416950', NULL, '', 'Nobol', 'Recinto los Canales via a Lomas de Sargentillo por la Garza Roja', '0989409741', NULL, 'Madre'),
(65, 99, 'Borbor Franco', 'Juana Segunda', 'Borbor Franco Juana Segunda', '0910089986', NULL, '', 'Lomas de Sargentillo', 'Recinto puerto las Cañas', '0990936796', NULL, 'Madre'),
(66, 100, 'Caicedo Adrian', 'Xiomara Jamileth', 'Caicedo Adrian Xiomara Jamileth', '0943513515', NULL, '', 'Daule', 'Maria Caichi y Colon', '0981730176', NULL, 'Hermana'),
(67, 102, 'Carpio Villamar', 'Anderson Elias', 'Carpio Villamar Anderson Elias', '0929512929', NULL, '', 'Santa Lucia', 'Bermejo de Abajo via al Porvenir', '0969403563', NULL, 'Hermano'),
(68, 103, 'MERCHÀN TELLO', 'DANIEL LEONARDO', 'MERCHÀN TELLO DANIEL LEONARDO', '0920792413', NULL, '', 'José Vélez y Domingo comin', 'DAULE', '0969169468', NULL, 'PAPÀ'),
(69, 104, 'BUENO MURILLO', 'RITA ALEXANDRA', 'BUENO MURILLO RITA ALEXANDRA', '0918963091', NULL, '', 'ARENAL', 'DAULE', '0997363421', NULL, 'MAMÀ'),
(70, 105, 'VALVERDE BUENO', 'SARA ISABEL', 'VALVERDE BUENO SARA ISABEL', '', NULL, '', '', 'HUANCHINCHAL', '99714684', NULL, 'MAMA'),
(71, 106, 'MOREY ADRIAN', 'EVELYN ANDREA', 'MOREY ADRIAN EVELYN ANDREA', '0942012378', NULL, '', 'Recinto la independencia', 'DAULE', '0982073728', NULL, 'TIA'),
(72, 107, 'CORTEZ MENDEZ', 'MAYRA ELISA', 'CORTEZ MENDEZ MAYRA ELISA', '', NULL, '', '', 'RECINTO LOS POZOS', '988062031', NULL, 'MAMA'),
(73, 108, 'QUIÑONEZ', 'WENDY MARIUXI', 'QUIÑONEZ WENDY MARIUXI', '', NULL, '', '', 'CDLA. JUAN BAUTISTA AGUIRRE', '967548676', NULL, 'MAMA'),
(74, 109, 'MORENO CORTEZ', 'FRANCISCA ISABEL', 'MORENO CORTEZ FRANCISCA ISABEL', '', NULL, '', '', '9 DE OCTUBRE Y BY P', '0969208375', NULL, 'MAMA'),
(75, 110, 'CABRERA ROSADO', 'GRACIELA VANESSA', 'CABRERA ROSADO GRACIELA VANESSA', '0924563968', NULL, '', 'D', 'JOSE FELIX MOREIRA Y MARIA MAIGON', '0969167223', NULL, 'MAMA'),
(76, 111, 'ESQUIVEL PLUAS', 'SANDRA LAURA', 'ESQUIVEL PLUAS SANDRA LAURA', '0915186373', NULL, '', '', 'JOSE VELIZ Y COLON', '0984713058', NULL, 'MAMA'),
(77, 112, 'GALARZA MOREIRA', 'LUCIA', 'GALARZA MOREIRA LUCIA', '', NULL, '', '', 'GUARUMAL', '986748968', NULL, 'MAMA'),
(78, 113, 'BONILLA BONILLA', 'LOURDES MERCEDES', 'BONILLA BONILLA LOURDES MERCEDES', '0926165853', NULL, '', 'Mz#1345, Sol#4, cop San Francisc', 'PASCUALES', '0981807560', NULL, 'MAMÀ'),
(79, 114, 'QUIJIJE CRUZ', 'GEOCONDA ELIZABETH', 'QUIJIJE CRUZ GEOCONDA ELIZABETH', '', NULL, '', 'Por la antigua fabrica de hielo', 'NOBOL', '0989131238', NULL, 'MAMÀ'),
(80, 116, 'MORALES GUILLEN', 'LUPE CELESTE', 'MORALES GUILLEN LUPE CELESTE', '919671032', NULL, '', 'FABRICA VIEJA DE HIELO', 'nobol', '0991343230', NULL, 'mama'),
(81, 117, 'ALVARADO LINO', 'CANDELARIA DEL ROSARIO', 'ALVARADO LINO CANDELARIA DEL ROSARIO', '0913708574', NULL, '', 'JUAN BAUTISTA AGUIRRE-DAULE', 'OLMEDO Y JOAQUIN GALLEGOS LARA MZ 5', '0993051058', NULL, 'TIA'),
(82, 118, 'rivera gonzalez', 'maela', 'rivera gonzalez maela', '914634662', NULL, '', 'ciberg el eyon esquina', 'salitre', '0959731465', NULL, 'mama'),
(83, 119, 'ZAMORA MORA', 'ANGELA RAQUEL', 'ZAMORA MORA ANGELA RAQUEL', '0917043580', NULL, '', 'PUENTE LUCÌA', 'KM 26 VIA A DAULE', '0939011143', NULL, 'MAMÀ'),
(84, 121, 'RAMOS RONQUILLO', 'FLOR ISABEL', 'RAMOS RONQUILLO FLOR ISABEL', '916810658', NULL, '', 'POR LA PRIMAVERA', 'PETRILLO KM 30', '0939983633', NULL, 'MAMÀ'),
(85, 122, 'ORTEGA CHAGUAY', 'JANETH MERCEDES', 'ORTEGA CHAGUAY JANETH MERCEDES', '925373789', NULL, '', 'RIBERAS DEL DAULE', 'daule', '0991562997', NULL, 'mama'),
(86, 120, 'SANCHEZ ROMERO', 'FRANCISCA ISABEL', 'SANCHEZ ROMERO FRANCISCA ISABEL', '0919464628', NULL, '', 'DAULE', '9 DE OCTUBRE ENTRE LA B Y P', '0980381868', NULL, 'MAMÁ'),
(87, 123, 'Bajaña Piloso', 'Juana de los angeles', 'Bajaña Piloso Juana de los angeles', '916099120', NULL, '', 'entrada por picadura', 'santa lucia-cabuyal', '0981030172', NULL, 'mama'),
(88, 124, 'ROMERO VARGAS', 'MIRIAN PETITA', 'ROMERO VARGAS MIRIAN PETITA', '0920810603', NULL, '', 'RECINTO CAÑA FISTOLE', 'DAULE', '0963865265', NULL, 'MAMÀ'),
(89, 125, 'ALVARADO CHAGUAY', 'KARINA', 'ALVARADO CHAGUAY KARINA', '908538960', NULL, '', 'PATRIA NUEVA', 'daule', '0992626962', NULL, 'mama'),
(90, 126, 'ROMERO ÀVILA', 'SANDRA ELIZABETH', 'ROMERO ÀVILA SANDRA ELIZABETH', '0920979937', NULL, '', 'PUENTE LUCÌA', 'Km 27 Via a Daule', '0961371840', NULL, 'MAMÀ'),
(91, 127, 'garcia ruiz', 'nancy', 'garcia ruiz nancy', '914069877', NULL, '', 'san jose', 'nobol', '965592720', NULL, 'mama'),
(92, 128, 'alvarado burgos', 'sonia maria', 'alvarado burgos sonia maria', '915239529', NULL, '', 'huanchichal via a naupe', 'daule', '0990770223', NULL, 'mama'),
(93, 129, 'RONQUILLO RUIZ', 'JUAN CARLOS', 'RONQUILLO RUIZ JUAN CARLOS', '0920840352', NULL, '', '', 'RECINTO DOS BOCAS', '099728918', NULL, 'PAPÀ'),
(94, 130, 'MAYA', 'JESSICA', 'MAYA JESSICA', '0961029832', NULL, '', 'PASCUALES', 'ASSAD BUCARAM', '0961029832', NULL, 'mama'),
(95, 131, 'SILVA MORA', 'SELIA LUCÍA', 'SILVA MORA SELIA LUCÍA', '0916113921', NULL, '', 'DAULE', 'PARROQUIA LIMONAL-RCTO JESUS MARUIA', '0985452967', NULL, 'MAMÁ'),
(96, 132, 'RUIZ BRIONES', 'EULOGIO', 'RUIZ BRIONES EULOGIO', '0903053908', NULL, '', 'Rct. Huanchichal', '', '0991448777', NULL, 'ABUELO'),
(97, 133, 'RENGIFO ROMERO', 'SUSAN LIZ', 'RENGIFO ROMERO SUSAN LIZ', '', NULL, '', 'palestina', 'rcte coloradal km 71', '0969198888', NULL, 'mama'),
(98, 134, 'PEÑAFIEL MOLINA', 'MARIANA DE JESÚS', 'PEÑAFIEL MOLINA MARIANA DE JESÚS', '920660743', NULL, '', 'DAULE', 'RCTO. CASCOL DE BAHONA', '0985659352', NULL, 'MAMÁ'),
(99, 136, 'SALAS BARZOLA', 'INES ISABEL', 'SALAS BARZOLA INES ISABEL', '0919073858', NULL, '', 'DAULE', '', '0986624198', NULL, 'MAMÀ'),
(100, 135, 'CASTRO ALVARADO', 'VANESSA LORENA', 'CASTRO ALVARADO VANESSA LORENA', '0919671842', NULL, '', 'LIMONAL', 'AV. MIRAFLORES CALLE PRINCIPAL', '0968160758', NULL, 'MAMÁ'),
(101, 137, 'TORRES ROMERO', 'MARJORIE DEL CARMEN', 'TORRES ROMERO MARJORIE DEL CARMEN', '0916174618', NULL, '', 'DAULE-BANIFE', 'MARIETA VEINTIMILLA Y MANUEL DEFAZ', '0980834708', NULL, 'MAMÁ'),
(102, 138, 'TORRES LAVAYEN', 'VIKY YESENIA', 'TORRES LAVAYEN VIKY YESENIA', '0917682536', NULL, '', 'DAULE-CENTRO MATERNO', 'BENJAMIN CARRIÓN Y HUGO ORTIZ', '0991935979', NULL, 'MAMÁ'),
(103, 139, 'No existe', 'No existe', 'No existe No existe', 'No existe', NULL, '', 'No existe', 'No existe', 'No existe', NULL, 'No existe'),
(104, 140, 'No existe', 'No existe', 'No existe No existe', 'No existe', NULL, '', 'No existe', 'No existe', 'No existe', NULL, 'No existe'),
(105, 142, 'Garcia Arreaga', 'Luis Antonio', 'Garcia Arreaga Luis Antonio', '0921020285', NULL, '', 'Macul de Palestina', 'Palestina', '0993815546', '', 'Padre'),
(106, 143, 'Macias Ramos', 'Freddy Manuel', 'Macias Ramos Freddy Manuel', '0915053888', NULL, '', 'Santa Lucia', 'Bermejo del Frente', '0997448730', '', 'Padre'),
(107, 144, 'Rivas Azpiazu', 'Jenny Maria', 'Rivas Azpiazu Jenny Maria', '0907008551', NULL, '', 'Daule', 'Riberas del Daule', '0958506268', NULL, 'Abuela'),
(108, 145, 'No existe', 'No existe', 'No existe No existe', 'No existe', NULL, '', 'No existe', 'No existe', 'No existe', NULL, 'No existe'),
(109, 146, 'No existe', 'No existe', 'No existe No existe', 'No existe', NULL, '', 'No existe', 'No existe', 'No existe', NULL, 'No existe'),
(110, 147, 'Montoya Garcia', 'Mariano de Jesus', 'Montoya Garcia Mariano de Jesus', '0922546676', NULL, '', 'Daule', 'Patria Nueva', '0980225191', NULL, 'Padre'),
(111, 148, 'Torres Arellano', 'Geoconda Karina', 'Torres Arellano Geoconda Karina', '0916055205', NULL, '', 'Nobol', 'Petrillo', '0980704967', NULL, 'Madre'),
(112, 149, 'Peñafiel Gonzalez', 'Mariuxi Elizabeth', 'Peñafiel Gonzalez Mariuxi Elizabeth', '0915555643', NULL, '', 'Daule', 'Cascol de Bahona', '0980674301', NULL, 'Madre'),
(113, 150, 'Chavez Ruiz', 'Karina Yessenia', 'Chavez Ruiz Karina Yessenia', '', NULL, '', 'Daule', 'Marianita 3', '0922548540', NULL, 'Madre'),
(114, 141, 'RONQUILLO RIVAS', 'PAOLA NATHALY', 'RONQUILLO RIVAS PAOLA NATHALY', '0926165085', NULL, '', '', '9 DE OCTUBRE', '0969333840', NULL, 'MAMA'),
(115, 151, 'CHAVEZ CEDEÑO', 'ANA MARISELA', 'CHAVEZ CEDEÑO ANA MARISELA', '', NULL, '', '', 'VISTA FLORIDA', '939162538', NULL, 'MAMA'),
(116, 152, 'INTRIGO VERA', 'JENNIFER ELIZABETH', 'INTRIGO VERA JENNIFER ELIZABETH', '1310714108', NULL, '', '', 'AV.PIEDRAHITA Y SIXTO RUGEL', '0996404839', NULL, 'MAMA'),
(117, 153, 'HERRERA TROYA', 'NARCISA KATHERINE', 'HERRERA TROYA NARCISA KATHERINE', '', NULL, '', '', 'SAN JOSE', '997766399', NULL, 'MAMA'),
(118, 154, 'CUSME CEDEÑO', 'ROSA EDITA', 'CUSME CEDEÑO ROSA EDITA', '1312128224', NULL, '', '', 'KM 30 VIA A DAULE', '9941445296', NULL, 'MAMA'),
(119, 155, 'CHIPRE PIGUABE', 'ROBERTO PAUL', 'CHIPRE PIGUABE ROBERTO PAUL', '0925894420', NULL, '', '', 'HUANCHINCHAL', '0939053393', NULL, 'PAPA'),
(120, 159, 'TORRES TOMALA', 'MARIA TERESA', 'TORRES TOMALA MARIA TERESA', '0929200376', NULL, '', '', 'LA RINCONADA', '0963434332', NULL, 'MAMA'),
(121, 160, 'TORRES TOMALA', 'MARIA TERESA', 'TORRES TOMALA MARIA TERESA', '0929200376', NULL, '', '', '', '939198037', NULL, 'MAMA'),
(122, 162, 'ALVARADO GOYA', 'NILDA LOURDES', 'ALVARADO GOYA NILDA LOURDES', '', NULL, '', 'LIMONAL RECINTO EL RECREO', 'LIMONAL', '0991818645', NULL, 'MADRE'),
(123, 165, 'MORALES PILALO', 'JESSENIA MILAGRO', 'MORALES PILALO JESSENIA MILAGRO', '0918627027', NULL, '', 'JOAQUIN GALLEGOS LARA', 'DAULE', '0987907023', NULL, 'MEDRE'),
(124, 167, 'CARLOS TUTIVEN', 'JANETH MAYRA', 'CARLOS TUTIVEN JANETH MAYRA', '0919460980', NULL, '', 'VERNAZA 501 Y AYACUCHO', 'DAULE', '963749251', NULL, 'MADRE'),
(125, 168, 'ORTIZ SUAREZ', 'GOYA GREGORIA', 'ORTIZ SUAREZ GOYA GREGORIA', '0917596959', NULL, '', 'MAGRO KM41', 'MAGRO KM41', '0968501126', NULL, 'MADRE'),
(126, 169, 'MORAN ORTEGANO', 'DEISY CATHERINE', 'MORAN ORTEGANO DEISY CATHERINE', '', NULL, '', 'LOMAS DE SARGENTILLO', 'LOMAS DE SARGENTILLO', '0959691746', NULL, 'MADRE'),
(127, 170, 'ROSADO MONTIEL', 'MAYIX CAROLINA', 'ROSADO MONTIEL MAYIX CAROLINA', '0926499567', NULL, '', 'KM 26 VIA A DAULE', 'KM 26 VIA A DAULE', '0961915974', NULL, 'MADRE'),
(128, 171, 'LEON MOREIRA', 'MIRIAM ILIANA', 'LEON MOREIRA MIRIAM ILIANA', '0925481996', NULL, '', 'DR. JARAMILLO DETRAS DEL IESS', 'DAULE', '0993895270', NULL, 'MADRE'),
(129, 172, 'TORRES ROMERO', 'ANA ANGELICA', 'TORRES ROMERO ANA ANGELICA', '0923545206', NULL, '', 'LOS LOJAS', 'LOS LOJAS', '0989758396', NULL, 'MADRE'),
(130, 173, 'MACIAS CORTEZ', 'MELISSA TATIANA', 'MACIAS CORTEZ MELISSA TATIANA', '1206509133', NULL, '', 'RECINTO YURIMA', 'RECINTO YURIMA', '0979449904', NULL, 'MADRE'),
(131, 174, 'LOOR CAREÑO', 'NORMA NOEMI', 'LOOR CAREÑO NORMA NOEMI', '0926706870', NULL, '', 'DAULE BANIFE', 'DAULE BANIFE', '0980273139', NULL, 'MADRE'),
(132, 175, 'CABRERA VILLAFUERTE', 'GREY CANDELARIA', 'CABRERA VILLAFUERTE GREY CANDELARIA', '0915506646', NULL, '', 'DAULE ATRAS DEL CEMENTERIO', 'DAULE ATRAS DEL CEMENTERIO', '0986599707', NULL, 'MADRE'),
(133, 176, 'RUGEL DELGADO', 'ARIANA JESUS', 'RUGEL DELGADO ARIANA JESUS', '', NULL, '', 'LAS AMERICA Y ESTERO NATO', 'LAS AMERICA Y ESTERO NATO', '0969786588', NULL, 'MADRE'),
(134, 177, 'CORTEZ CANTOS', 'MELVA LOURDES', 'CORTEZ CANTOS MELVA LOURDES', '0923758114', NULL, '', 'LAUREL', 'LAUREL', '0982634159', NULL, 'MADRE'),
(135, 178, 'MORAN RONQUILLO', 'ESTEFANIA', 'MORAN RONQUILLO ESTEFANIA', '', NULL, '', 'AV. SAN FRANCISCO', 'DAULE', '0968454638', NULL, 'MADRE'),
(136, 179, 'CAMBA CHOEZ', 'VERONICA EMPERATRIZ', 'CAMBA CHOEZ VERONICA EMPERATRIZ', '', NULL, '', 'JOSE NAVAS Y FRANCISCO VILLACRES', 'JOSE NAVAS Y FRANCISCO VILLACRES', '0982442921', NULL, 'MADRE'),
(137, 180, 'GOMEZ ALCIVAR', 'HERLINDA MARGARITA', 'GOMEZ ALCIVAR HERLINDA MARGARITA', '0917995920', NULL, '', 'FRENTE A LA IGLESIA SEÑOR DE L M', 'RIBERA OPUESTA', '0994514015', NULL, 'MADRE'),
(138, 181, 'MORENO MOTA', 'JENNY ALEXANDRA', 'MORENO MOTA JENNY ALEXANDRA', '', NULL, '', 'RECINTO LOS ANGELES', 'LAUREL', '0993297652', NULL, 'MADRE'),
(139, 182, 'NOBOA BRIONES', 'ANDREINA LEONOR', 'NOBOA BRIONES ANDREINA LEONOR', '0925139131', NULL, '', 'RECINTO NAUPE', 'RECINTO NAUPE', '0939043486', NULL, 'MADRE'),
(140, 183, 'LOPEZ VIZUETA', 'MAYRA', 'LOPEZ VIZUETA MAYRA', '', NULL, '', 'RECINTO BARRANQUILLA', 'SANTA LUCIA', '0959841508', NULL, 'MADRE'),
(141, 184, 'CAMBA ARREAGA', 'ANDREA FRANCISCA', 'CAMBA ARREAGA ANDREA FRANCISCA', '0921567285', NULL, '', 'PETRILLO', 'PETRILLO', '0978853608', NULL, 'MADRE'),
(142, 185, 'CABRERA ROSADO', 'GRACIELA VANESSA', 'CABRERA ROSADO GRACIELA VANESSA', '0924563958', NULL, 'cabrera.28', 'DAULE', '3 DE NOVIEMBRE Y AMAZONA', '0969167223', NULL, 'MAMA'),
(143, 186, 'CABRERA ROSADO', 'GRACIELA VANESSA', 'CABRERA ROSADO GRACIELA VANESSA', '0924563968', NULL, 'cabrera.28', 'DAULE', '3 DE NOVIEMBRE Y AMAZONA', '0969167223', NULL, 'MAMA'),
(144, 187, 'CEPADA NUÑEZ', 'DIANA PATRICIA', 'CEPADA NUÑEZ DIANA PATRICIA', '0922390240', NULL, 'dianaceped', 'CASCOL DE BAHONA', 'DAULE', '0997012550', NULL, 'MAMA'),
(145, 188, 'NAVARRETE GOMEZ', 'ROANA VANESSA', 'NAVARRETE GOMEZ ROANA VANESSA', '0923454225', NULL, 'navarreter', 'patria nueva', 'DAULE', '0992482414', NULL, 'MAMA'),
(146, 189, 'HUAYAMAVE BARZOLA', 'RUTH MAGDALENA', 'HUAYAMAVE BARZOLA RUTH MAGDALENA', '0915793103', NULL, 'magdalenah', 'JOSE VELEZ Y NARCISA DE JESUS', 'DAULE', '0994796831', NULL, 'MAMA'),
(147, 190, 'BENALCAZAR MARTILLO', 'WUENDY LEONELA', 'BENALCAZAR MARTILLO WUENDY LEONELA', '0923753560', NULL, 'wbenalcaza', 'BANIFE', 'SANTA LUCIA Y BOYACA', '0960413647', NULL, 'MAMA'),
(148, 191, 'VARGAS HOLGUIN', 'MARIA MARIBEL', 'VARGAS HOLGUIN MARIA MARIBEL', '0916814858', NULL, 'priscila23', 'HUANCHICHAL', 'ENTRADA POR DESVIO LAS CAÑAS', '0981733742', NULL, 'MAMA'),
(149, 192, 'CALDERON BRAVO', 'AGUSTINA MONSERRAT', 'CALDERON BRAVO AGUSTINA MONSERRAT', '0928376532', NULL, 'gatitacald', 'BANIFE', 'PEDRO ISAIAS PRIMERA ETAPA', '0939588619', NULL, 'MAMA'),
(150, 193, 'BONILLA TOMALA', 'MARCIA NATALY', 'BONILLA TOMALA MARCIA NATALY', '0924477193', NULL, 'bonilla198', 'DAULE', 'AVENIDA SAN FRANCISCO', '0996890356', NULL, 'MAMA'),
(151, 194, 'VILLAMAR TORRES', 'KATIUSCA ESTEFANIA', 'VILLAMAR TORRES KATIUSCA ESTEFANIA', '0927322156', NULL, 'juleydiver', 'PATRIA NUEVA', 'PATRIA NUEVA POR LAS ANTENAS', '0985165072', NULL, 'MAMA'),
(152, 195, 'RODRIGUEZ GOMEZ', 'CARMEN SORAIDA', 'RODRIGUEZ GOMEZ CARMEN SORAIDA', '0926832163', NULL, 'santanarod', 'LA SECA', 'LA SECA', '0980833530', NULL, 'MAMA'),
(153, 196, 'FOLLECO BAZOLA', 'ALVARO IVAN', 'FOLLECO BAZOLA ALVARO IVAN', '0905849600', NULL, 'alvarofoll', 'SANTA CLARA', 'PIEDRAITA Y BOLIVAR', '0980678540', NULL, 'PAPA'),
(154, 197, 'VERA PONCE', 'GUISSELLA JESSENIA', 'VERA PONCE GUISSELLA JESSENIA', '0923753883', NULL, 'nastahajim', 'VIA LAS MARAVILLAS', 'RECINTO SANTA ROSA', '0962729367', NULL, 'MAMA'),
(155, 198, 'CORTEZ MENDEZ', 'CASILDA MARCIA', 'CORTEZ MENDEZ CASILDA MARCIA', '0919049551', NULL, 'casildacor', 'SAN JOSE. DAULE', 'ESTERO NATO Y AVENIDA DE LAS AMERICA', '0982626781', NULL, 'MAMA'),
(156, 199, 'PEÑAFIEL BONILLA', 'LAYDI JULIANA', 'PEÑAFIEL BONILLA LAYDI JULIANA', '0926834219', NULL, 'ladyjulian', 'PEDROISAIAS', 'BARTOLOMEVILLAMAR', '0968025460', NULL, 'MAMA'),
(157, 200, 'BONILLA YAGUAL', 'LADY DIANA', 'BONILLA YAGUAL LADY DIANA', '0926835802', NULL, 'ladydiana', 'PEDRO ISAIAS', 'PEDRO ISAIAS', '0991447163', NULL, 'MAMA'),
(158, 201, 'GARCIA DECIMAVILLA', 'PAMELA KATIUSCA', 'GARCIA DECIMAVILLA PAMELA KATIUSCA', '0926702697', NULL, 'pameharcia', 'SAN JACINTO', 'RECINTO SAN JACINTO. MATE', '0991965854', NULL, 'MAMA'),
(159, 202, 'ESPINOZA BRIONES', 'MARIA LUISA', 'ESPINOZA BRIONES MARIA LUISA', '1206387704', NULL, 'marialuisa', 'PEDRO ISAIAS 3 ETAPA', 'CALLE. 3 NOVIEMBRE Y AMARILIS FUENTE', '0960525562', NULL, 'MAMA'),
(160, 203, 'QUINTO', 'SONIA', 'QUINTO SONIA', '0941447690', NULL, 'pulguitaqu', 'DAULE', 'SAN GABRIEL. DAULE', '0968985834', NULL, 'MAMA'),
(161, 204, 'VELIZ MARTINEZ', 'BLANCA CHARITO', 'VELIZ MARTINEZ BLANCA CHARITO', '0915251193', NULL, 'blancaveli', 'NOBOL', 'RECINTO PAJONAL', '0961224130', NULL, 'MAMA'),
(162, 205, 'VENTURA QUIMIS', 'ALEXANDRA ISABEL', 'VENTURA QUIMIS ALEXANDRA ISABEL', '0926832189', NULL, 'ventualexa', 'RECINTO LAS CAÑAS', 'LOMAS DE SARGENTILLO', '0997711054', NULL, 'MAMA'),
(163, 206, 'ALMEIDA LINO', 'JAKUELINE MARGARITA', 'ALMEIDA LINO JAKUELINE MARGARITA', '1206134056', NULL, 'emelyalmei', 'DAULE', 'PATRIA NUEVA. REFERENCIA POR LA PJ', '0997990416', NULL, 'MAMA'),
(164, 207, 'VILLAMAR CAMPOZANO', 'VIVIANA ESTEFANIA', 'VILLAMAR CAMPOZANO VIVIANA ESTEFANIA', '0926378290', NULL, 'loveestefi', 'DAULE', 'VELIZ Y DOMINGO COMIN', '0968005839', NULL, 'MAMA'),
(165, 209, 'SEGURA RIVERA', 'DIAGA VERONICA', 'SEGURA RIVERA DIAGA VERONICA', '0929862852', NULL, '', 'PATRIA NUEVA', 'DAULE', '0997435338', NULL, 'MAMÁ'),
(166, 210, 'CEDEÑO', 'VANESSA KENYA', 'CEDEÑO VANESSA KENYA', '0928157700', NULL, '', 'PATRIA NUEVA', 'DAULE', '0991327825', NULL, 'MAMÁ'),
(167, 211, 'VILLAGOMEZ AREVALOS', 'LINDA ROSALIA', 'VILLAGOMEZ AREVALOS LINDA ROSALIA', '0923723456', NULL, '', 'Daule', 'Calle Principal: Desvio las cañas', '0968619598', '', 'Madre'),
(168, 213, 'BARZOLA PLUAS', 'CECILIA MARTHA', 'BARZOLA PLUAS CECILIA MARTHA', '0926980185', NULL, '', 'RIVERA OPUESTA', 'LA PLAYITA', '0996925843', NULL, 'MAMÁ'),
(169, 212, 'ALVARADO AMADOR', 'ERIKA SORAYA', 'ALVARADO AMADOR ERIKA SORAYA', '0919462309', NULL, '', 'Daule', '', '0990753231', NULL, 'Madre'),
(170, 214, 'PLUAS SESME', 'DIANA MERCEDES', 'PLUAS SESME DIANA MERCEDES', '0921312351', NULL, 'dianapluas', 'FRANCISCO VILLACRES Y MANUEL VIL', 'DAULE', '0990138745', NULL, 'MAMÁ'),
(171, 215, 'ROMAN TORRES', 'EDILMA BEATRIZ', 'ROMAN TORRES EDILMA BEATRIZ', '0929707495', NULL, 'rodolfo_cr', 'ISIDRO DE VEINZA Y CESAR BAHAMON', 'BANIFE', '0968717888', NULL, 'MAMÁ'),
(172, 216, 'RODRIGUEZ MONTENEGRO', 'JANETTE ALEXANDRA', 'RODRIGUEZ MONTENEGRO JANETTE ALEXANDRA', '0930171855', NULL, '', 'Daule', 'Patria Nueva', '0979364567', NULL, 'Madre'),
(173, 217, 'CEDEÑO TELLO', 'SANDY', 'CEDEÑO TELLO SANDY', '0928339761', NULL, '', 'RIO PULA Y LAS AMERICAS', 'JUAN.B.AGUIRRE', '0968852263', NULL, 'MAMÁ'),
(174, 219, 'NAVARRETE HUACON', 'KARINA VICTORIA', 'NAVARRETE HUACON KARINA VICTORIA', '0926708579', NULL, 'kari_16198', 'DOMINGO COMIN Y GONZALO SANDUMBI', 'PADRE JUAN B A', '0939553503', NULL, 'MADRASTRA'),
(175, 220, 'HERRERA CAMBA', 'BELLA NARCISA', 'HERRERA CAMBA BELLA NARCISA', '0922499116', NULL, '', 'DESVIO DE LIMONAL', 'LIMONAL', '0985865064', NULL, 'MAMÁ'),
(176, 218, 'VELIZ MARTINEZ', 'BLANCA', 'VELIZ MARTINEZ BLANCA', '', NULL, '', 'Nobol', '', '0961224130', NULL, 'Abuela'),
(177, 221, 'Rodríguez Montenegro', 'Lucy Dominga', 'Rodríguez Montenegro Lucy Dominga', '0940612377', NULL, '', 'CALLE JUSTO TORRES Y MARTHA BUCA', 'BANIFE', '0999271810', NULL, 'MAMÁ'),
(178, 222, 'RUIZ PEÑAFIEL', 'MONICA EDITH', 'RUIZ PEÑAFIEL MONICA EDITH', '0968636609', NULL, '', 'Daule', 'Av. San francisco, calle 26 de noviembre.', '0968636609', '', 'Madre'),
(179, 223, 'MEJIA', 'ISAMAR', 'MEJIA ISAMAR', '', NULL, '', '', 'DAULE', '0991441163', NULL, 'MAMÁ'),
(180, 224, 'PLUAS RUIZ', 'JOVA ELIZABETH', 'PLUAS RUIZ JOVA ELIZABETH', '0912359437', NULL, '', '10 DE FEBRERO Y JOAQUIN GA', 'YOLITA', '0967070964', NULL, 'MAMÁ'),
(181, 226, 'CANDELARIO GARCIA', 'ESTHER', 'CANDELARIO GARCIA ESTHER', '0918682196', NULL, '', 'DAULE', 'DAULE AVENIDA LOS DAULIS Y VICENTE PINO MORAN', '0939021796', NULL, 'MAMA'),
(182, 227, 'ARROBA ORTIZ', 'JEOMAIRA ROSSEMARY', 'ARROBA ORTIZ JEOMAIRA ROSSEMARY', '0940557408', NULL, 'ortizortiz', 'ANTONIO PARRA (PERIMETRAL)', 'SANTA CLARA', '0980839804', NULL, 'MAMÁ'),
(183, 228, 'RODRIGUEZ ZAMBRANO', 'JAYRO RODOLFO', 'RODRIGUEZ ZAMBRANO JAYRO RODOLFO', '0920346160', NULL, '', '26 DE NOVIEMBRE Y OTTO CARBO', 'SAN FRANCISCO', '0939569761', NULL, 'PAPA'),
(184, 230, 'BARZOLA LUNA', 'KAREN JULHIANA', 'BARZOLA LUNA KAREN JULHIANA', '0922279104', NULL, '', 'ISMAEL PEREZ PAZMIÑO Y FELICISIS', 'SANTA CLARA', '0990207028', NULL, 'MAMÁ'),
(185, 229, 'BRIONES CÓRDOVA', 'FÁTIMA DEL CARMEN', 'BRIONES CÓRDOVA FÁTIMA DEL CARMEN', '2400106023', NULL, 'fatimabrio', 'SANTA CLARA', 'DIONISIO NAVAS Y BOLIVAR Y SUCRE', '0927322933', NULL, 'MAMA'),
(186, 231, 'RODRIGUEZ BANCHON', 'YURI GEOMAYRA', 'RODRIGUEZ BANCHON YURI GEOMAYRA', '0927322933', NULL, '', 'JUAN BAUTISTA AGUIRRE', 'JOSE PERALTA Y BYPAS', '0927322933', NULL, 'MAMA'),
(187, 232, 'RUIZ PILALO', 'ADELAIDA MARICELA', 'RUIZ PILALO ADELAIDA MARICELA', '0940765456', NULL, '', 'AVENIDA GRAN COLOMBIA Y 10 DE FE', 'DAULE', '0988627329', NULL, 'MAMÁ'),
(188, 234, 'NAVARRETE RAVELO', 'YENIFER MARIA', 'NAVARRETE RAVELO YENIFER MARIA', '9640420023', NULL, '', 'CAMILO PONCE Y CARLOS CEVALLOS', 'DAULE', '0983634782', NULL, 'MAMÁ'),
(189, 235, 'BAJAÑA QUINTO', 'MARIA ELOISA', 'BAJAÑA QUINTO MARIA ELOISA', '0917077919', NULL, '', 'DAULE', '2 DE AGOSTO Y DOMINGO COMIN ENTRE BOLIVAR', '0996079108', NULL, 'MAMA'),
(190, 236, 'Huayamabe Cordova', 'Josselyn', 'Huayamabe Cordova Josselyn', '0929488625', NULL, 'michellyco', 'DAULE', '\"AV. SAN FRANCISCO Y ABDON CALDERON GARAY Y COA\"', '0968833306', NULL, 'MAMA'),
(191, 237, 'CERCADO RUIZ', 'PETRA DEL CARMEN', 'CERCADO RUIZ PETRA DEL CARMEN', '0915704266', NULL, '', 'Cdla. Assad Bucaram', 'SANTA CLARA', '0967687950', NULL, 'MAMÁ'),
(192, 239, 'JAEN GUAGUA', 'LEONOR ALIDA', 'JAEN GUAGUA LEONOR ALIDA', '0801315250', NULL, 'jaenguagua', 'Parte posterior del mercado', 'José Vélez y Quito', '0961059965', NULL, 'MAMÁ'),
(193, 238, 'MACIAS MESTANZA', 'FLOR MARIA', 'MACIAS MESTANZA FLOR MARIA', '0917316820', NULL, 'lisbeth_mo', 'DAULE', 'BOLIVAR Y GENERAL VERNAZA', '0939603252', NULL, 'MAMA'),
(194, 240, 'ORTEGA CHAGUAY', 'JANETH MERCEDES', 'ORTEGA CHAGUAY JANETH MERCEDES', '0925373789', NULL, '', 'FRENTE A LA IGLESIA SR DE LOS MI', 'RIBERA OPUESTA', '0991562997', NULL, 'MAMÁ'),
(195, 241, 'CARDOZO AYALA', 'JOSELYN ALEJANDRA', 'CARDOZO AYALA JOSELYN ALEJANDRA', '24.795.506', NULL, 'cardozojo', 'DAULE', 'JUAN BAUTISTA AGUIRRE Y LEONIDAS LA TORRE Y PEDRO PANTALEON', '994037962', NULL, 'MAMA'),
(196, 242, 'MORAN RONQUILLO', 'LISSETTE ESTEFANIA', 'MORAN RONQUILLO LISSETTE ESTEFANIA', '0926167388', NULL, 'sinelandia', 'DAULE', 'JOSE CARBO Y DUCHICE', '0926167388', NULL, 'MAMA'),
(197, 243, 'RONQUILLO CASTRO', 'MAGNO WILFRIDO', 'RONQUILLO CASTRO MAGNO WILFRIDO', '0920844529', NULL, 'magnoronqu', 'DAULE', 'VICENTE PIEDRAHITA Y ROCAFUERTE', '0967111471', NULL, 'PAPA'),
(198, 244, 'ADRIAN MORA', 'JAMILETH ANAIS', 'ADRIAN MORA JAMILETH ANAIS', '0925485963', NULL, 'Jamilethad', 'DAULE', 'VÍCTOR MANUEL RENDON Y HOMERO ESPINOZA', '0980880949', NULL, 'MAMA'),
(199, 245, 'ZUÑIGA LEON', 'M ARIA ISABEL', 'ZUÑIGA LEON M ARIA ISABEL', '0921248480', NULL, '', 'DAULE', 'PROV DE AZOGUEZ Y LAS AMERICAS Y RIO AMAZONAS', '0985700920', NULL, 'MAMA'),
(200, 246, 'AVILES BARZOLA', 'OLGA LETICIA', 'AVILES BARZOLA OLGA LETICIA', '0982693526', NULL, '', 'DAULE', 'VIA DAULE / STA LUCIA  Y FRANCISCO DE PAULE Y SANTANDER', '0982693526', NULL, 'MAMA'),
(201, 247, 'PEÑAFIEL RAMIREZ', 'DIANA ALEXANDRA', 'PEÑAFIEL RAMIREZ DIANA ALEXANDRA', '0922940812', NULL, '', 'DAULE', 'DOMINGO COMIN Y MANUEL GONZALEZ', '0967414023', NULL, 'MAMA'),
(202, 248, 'ALVARADO AVILES', 'KETTY MARIA', 'ALVARADO AVILES KETTY MARIA', '0929481877', NULL, '', 'MAGRO', 'CALLE BOLIVAR MORAN', '0969216285', NULL, 'MAMA'),
(203, 249, 'BRIONES GORDILLO', 'ESTHER ROSARIO', 'BRIONES GORDILLO ESTHER ROSARIO', '0920942182', NULL, 'brionesgor', 'DAULE', 'HOMERO ESPINOZA Y 10 DE FEBRERO', '0994336433', NULL, 'MAMA'),
(204, 250, 'RUIZ RUIZ', 'MARTHA MARIA', 'RUIZ RUIZ MARTHA MARIA', '0918626649', NULL, 'ruizruiz.m', 'DAULE', 'BOLIVAR Y ENRIQUE ROMERO', '0987048254', NULL, 'MAMA'),
(205, 251, 'SALDARRIAGA ECHEVERRIA', 'ISAMAR STEFANIA', 'SALDARRIAGA ECHEVERRIA ISAMAR STEFANIA', '0941643025', NULL, '', 'DAULE', 'ANTONIO PARRA Y JOSE GOMEZ IZQUIERDO', '0985451399', NULL, 'MAMA'),
(206, 252, 'SALAVARRIA SANCHEZ', 'CECILIA CECIBEL', 'SALAVARRIA SANCHEZ CECILIA CECIBEL', '0929404168', NULL, 'cecicecibe', 'DAULE', 'PABLO HANNIBAL VELA Y JOSE MARIA EGAS', '990530007', NULL, 'MAMA'),
(207, 253, 'ZURITA RUIZ', 'DORIS MARIA', 'ZURITA RUIZ DORIS MARIA', '0916499726', NULL, 'doriszurit', 'DAULE', 'JOSE FElIX HEREDIA  Y LUIS MARIA MAIGON', '0986767712', NULL, 'MAMA'),
(208, 254, 'VERA QUINTO', 'JENNY JESSENIA', 'VERA QUINTO JENNY JESSENIA', '0926708728', NULL, '', 'DAULE', 'CALLE BOLÍVAR AVENIDA QUINTO Y MANUEL GONZÁLEZ', '0993769921', NULL, 'MAMA'),
(209, 255, 'ALVARADO LAVAYEN', 'RINA DORIS', 'ALVARADO LAVAYEN RINA DORIS', '0923114797', NULL, 'rinaalvara', 'MARIANITA 3', 'RIO DAULE Y EDUARDO FLORES', '0991391710', NULL, 'MAMA'),
(210, 257, 'RUIZ RUGEL', 'JOSE LUCIANO', 'RUIZ RUGEL JOSE LUCIANO', '0915037477', NULL, 'isabelruiz', 'SIXTO RUGEL', 'SIXTO RUGEL', '0939100590', NULL, 'PAPA'),
(211, 260, 'PITA BONILLA', 'JULIAA ANDREINA', 'PITA BONILLA JULIAA ANDREINA', '927976167', NULL, '', '09  de Octubre y Piedrahita  por', '09  de Octubre y Piedrahita  por canal 9', '0990523837', NULL, 'Mamà'),
(212, 261, 'RODRIGUEZ BANCHON', 'YURI GEOMAYRA', 'RODRIGUEZ BANCHON YURI GEOMAYRA', '0927322933', NULL, '', 'Jose Peralta y Bypa frente a las', 'Jose Peralta y Bypa frente a las desmontaciones descanso frente', '0994746035', NULL, 'Mamà'),
(213, 262, 'CARDENAS AGUILERA', 'PAOLA CECIBEL', 'CARDENAS AGUILERA PAOLA CECIBEL', '0920479383', NULL, '', 'AVENIDA SAN FRANCISCO CDLA EL RE', 'AVENIDA SAN FRANCISCO CDLA EL RECUERDO', '0968841505', '', 'Mamà'),
(214, 263, 'CHAPA MORA', 'GLADYS BELEN', 'CHAPA MORA GLADYS BELEN', '0921332243', NULL, '', 'San Martín y Bolivar', 'San Martín y Bolivar', '0939086836', NULL, 'Mamà'),
(215, 264, 'HERRERA MARQUEZ', 'EVELING MYLING', 'HERRERA MARQUEZ EVELING MYLING', '0926371477', NULL, '', '', '', '0989699396', NULL, 'Mamà'),
(216, 265, 'MARQUEZ MORALES', 'MARIELA CECILIA', 'MARQUEZ MORALES MARIELA CECILIA', '0928374867', NULL, '', 'Ciudadela El Recuerdo', 'Ciudadela El Recuerdo', '999270059', NULL, 'Mamà'),
(217, 266, 'PIGUAVE HERRERA', 'DAYANA MAILY', 'PIGUAVE HERRERA DAYANA MAILY', '0932169709', NULL, '', 'Ayacucho y Piedrahita', 'Ayacucho y Piedrahita', '967376438', '', 'Mamà'),
(218, 267, 'TOMALÁ BAJAÑA', 'ZOLANDA MARICELA', 'TOMALÁ BAJAÑA ZOLANDA MARICELA', '917465346', NULL, '', 'Assab Bucaram Call Jose Maria Eg', 'Assab Bucaram Call Jose Maria Egas al otro del carretero preinci', '0980957907', NULL, 'Abuela'),
(219, 269, 'CARPIO SAN LUCAS', 'ROXANA ISABEL', 'CARPIO SAN LUCAS ROXANA ISABEL', '0923205298', NULL, '', 'Recinto Boqueron del Redondel pa', 'Recinto Boqueron del Redondel pasa la casa de los policIAS EN LA', '0969613887', NULL, 'Mamà'),
(220, 270, 'STEFANIA SALDARRIAGA', 'ISAMAR', 'STEFANIA SALDARRIAGA ISAMAR', '', NULL, '', 'Calle Antonio Parra', 'Calle Antonio Parra', '941643025', '', 'Mamà'),
(221, 271, 'FAJARDO MANTUANO', 'JOHANA KATHERINE', 'FAJARDO MANTUANO JOHANA KATHERINE', '925211807', NULL, '', 'Marianita 1', 'Marianita 1', '0999184152', NULL, 'Mamà'),
(222, 115, 'NAVARRETE HUSCON', 'KARINA VICTORIA', 'NAVARRETE HUSCON KARINA VICTORIA', '', NULL, '', 'Calle Domingo Comin y Gonzalo Za', 'Calle Domingo Comin y Gonzalo Zambuunbide', '981323036', NULL, 'Mamà'),
(223, 163, 'PILLIGUA LIBERIO', 'ANDY GRACE', 'PILLIGUA LIBERIO ANDY GRACE', '924640006', NULL, '', 'Entrada Limonal', 'Entrada Limonal', '981323036', NULL, 'Mamà'),
(224, 166, 'BARZOLA', 'CECILIA', 'BARZOLA CECILIA', '', NULL, '', 'RIBERAS OPUESTAS', 'RIBERAS OPUESTAS', '0969925843', NULL, 'Mamà'),
(225, 258, 'ROMAN TORRES', 'EDILMA BEATRIZ', 'ROMAN TORRES EDILMA BEATRIZ', '929707495', NULL, '', 'Patria Nueva', 'Patria Nueva', '0968717888', NULL, 'Mamà'),
(226, 272, 'ALVARDO NIETO', 'KETY DEL ROCÍO', 'ALVARDO NIETO KETY DEL ROCÍO', '922272190', NULL, '', 'Gral. Leonidad Plaza y Batallo D', 'Gral. Leonidad Plaza y Batallo Daule Mz', '991850708', NULL, 'Mamà'),
(227, 273, 'CEDEÑO TELLO', 'SANDY FRANCISCA', 'CEDEÑO TELLO SANDY FRANCISCA', '0928339761', NULL, '', 'Rio Pula y las Americas calle Pr', 'Rio Pula y las Americas calle Principal diagonal  al descanso de', '0968852263', NULL, 'Mamà'),
(228, 274, 'RIVERA CEDEÑO', 'DENISSE LILIANA', 'RIVERA CEDEÑO DENISSE LILIANA', '0928884287', NULL, '', 'Coperativa Assad Bucaram', 'Coperativa Assad Bucaram', '0958889409', NULL, 'Mamà'),
(229, 275, 'CAMBA ROMAN', 'JENNIFER INES', 'CAMBA ROMAN JENNIFER INES', '923209746', NULL, '', '', '', '968284604', NULL, 'Mamà'),
(230, 276, 'ALVARADO MORAN', 'MARIA MONSERRATE', 'ALVARADO MORAN MARIA MONSERRATE', '0918070848', NULL, '', 'Nobol', 'Lotizacion bella flor', '0999412221', NULL, 'Madre'),
(231, 277, 'RUGEL SALAS', 'DOLORES DOMITILA', 'RUGEL SALAS DOLORES DOMITILA', '0920883717', NULL, '', 'Daule', 'Los Alamos por la perimetral', '0983631470', NULL, 'Madre'),
(232, 278, 'Vasquez Tomala', 'Ketty Germania', 'Vasquez Tomala Ketty Germania', '0922093836', NULL, '', 'Daule', 'Recinto el Naupe', '0969166302', NULL, 'Madre'),
(233, 279, 'Franco Rodriguez', 'Patricia Mayra', 'Franco Rodriguez Patricia Mayra', '0921247995', NULL, '', 'Recinto las Cañas', 'Lomas de Sargentillo', '0989128690', NULL, 'Madre'),
(234, 280, 'Castro Pantaleon', 'Jasmin Maricela', 'Castro Pantaleon Jasmin Maricela', '0915584189', NULL, '', 'Palestina', 'Recinto Coloradal', '0986504547', NULL, 'Madre'),
(235, 281, 'Veliz Perez', 'Robert Napoleon', 'Veliz Perez Robert Napoleon', '0910083419', NULL, '', 'Nobol', 'Juan Alvarez y Tomas Martinez', '0993463701', NULL, 'Padre'),
(236, 282, 'Gamboa Contreras', 'Jenniffer Aracelly', 'Gamboa Contreras Jenniffer Aracelly', '0925341430', NULL, '', 'Guayaquil', 'Km 20 via Daule Guayaquil', '0939367215', NULL, 'Madre'),
(237, 287, 'SALAZAR PLÙAS', 'CESAR MANUEL', 'SALAZAR PLÙAS CESAR MANUEL', '980736685', NULL, '', 'RECINTO LOS JASMINES', 'DAULE', '0996037212', NULL, 'PAPÀ'),
(238, 288, 'SÀNCHEZ SÀNCHEZ', 'EFRÈN JESÙS', 'SÀNCHEZ SÀNCHEZ EFRÈN JESÙS', '0915343305', NULL, '', 'SANTA LUCÌA', 'km 57 daule sta lucia', '0939894313', NULL, 'PAPÀ'),
(239, 289, 'SESME CALERO', 'ALBERTO ARMANDO', 'SESME CALERO ALBERTO ARMANDO', '091661500', NULL, '', 'ASSAD BUCARÀM', 'DAULE', '0990761574', NULL, 'PAPÀ'),
(240, 291, 'PIZA ALVARADO', 'VERÒNICA VIVIANA', 'PIZA ALVARADO VERÒNICA VIVIANA', '0921022190', NULL, '', 'CDLA. LA PROVIDENCIA', 'NOBOL', '0988302678', NULL, 'MAMÀ'),
(241, 290, 'Rodriguez barzola', 'maria eugenia', 'Rodriguez barzola maria eugenia', '924902133', NULL, '', 'barrio 12 de mayo', 'lomas de sargentillo', '0981185504', NULL, 'mama'),
(242, 292, 'Borja Merchan', 'Johana Elizabeth', 'Borja Merchan Johana Elizabeth', '1720392644', NULL, '', 'Daule', 'Patria Nueva Hermogenes Rivas y Cesar Bahamonde', '0961520732', NULL, 'Madre'),
(243, 294, 'Zambrano Mosquera', 'Marco Javier', 'Zambrano Mosquera Marco Javier', '0915701932', NULL, '', 'Daule', 'Parroquia elLimonal Recinto Loma del Papayo', '0968783609', NULL, 'Padre'),
(244, 293, 'BALDIRES HAS', 'ESTHER GRISELDA', 'BALDIRES HAS ESTHER GRISELDA', '921941738', NULL, '', 'LOTIZACION SAN RAMON', 'nobol', '0993202997', NULL, 'mama'),
(245, 319, 'TARIRA RAMOS', 'TANIA JAZMIN', 'TARIRA RAMOS TANIA JAZMIN', '1205918285', NULL, '', 'Nobol', 'Km 30 vía Daule-Petrillo', '0978832733', NULL, 'mamá'),
(246, 320, 'MORA REYES', 'LEYDY LETTY', 'MORA REYES LEYDY LETTY', '0917590598', NULL, '', 'SANTA LUCIA', 'RECINTO EL MANGLE', '0981258656', NULL, 'MADRE'),
(247, 322, 'CEDEÑO VASQUEZ', 'NORMA MARIA', 'CEDEÑO VASQUEZ NORMA MARIA', '0918824434', NULL, 'normycde@h', 'NOBOL', 'PETRILLO KM 30 VÍA A DAULE', '0994811590', NULL, 'MAMÁ'),
(248, 321, 'RATTIA SALAZAR', 'MARIA ANGELICA', 'RATTIA SALAZAR MARIA ANGELICA', '16976779', NULL, '', 'DAULE', 'FRENTE A LA CLINICA DE DIALISIS', '0989310394', NULL, 'MAMÁ'),
(249, 323, 'CALERO SELLAN', 'LILIANA FRANCISCA', 'CALERO SELLAN LILIANA FRANCISCA', '0920691094', NULL, '', 'DAULE', 'CDLA. ASSAD BUCARAM', '0993372984', NULL, 'MAMÁ'),
(250, 324, 'GOMEZ ALCIVAR', 'HERLINDA MARGARITA', 'GOMEZ ALCIVAR HERLINDA MARGARITA', '0917995920', NULL, '', 'DAULE', 'RIBERAS OPUESTAS', '0994514015', NULL, 'MAMÁ'),
(251, 325, 'MENDEZ ARREAGA', 'BARBARA FELICITA', 'MENDEZ ARREAGA BARBARA FELICITA', '0917171886', NULL, '', 'DAULE', 'NARCISA DE JESUS Y JOAQUIN GALLEGOS', '0967308678', NULL, 'MAMÁ'),
(252, 326, 'TUTIVEN CORTEZ', 'BYRON OMAR', 'TUTIVEN CORTEZ BYRON OMAR', '0916375413', NULL, '', 'DAULE', 'SIMON BOLIVAR Y PIEDRAHITA', '0989769557', NULL, 'PAPÁ'),
(253, 327, 'LOZANO BARZOLA', 'MARYURY BASILIA', 'LOZANO BARZOLA MARYURY BASILIA', '0920986239', NULL, '', 'DAULE', 'RCTO EL PIGIO-LA SECA- LOS LOJAS', '0986904690', NULL, 'MAMÁ'),
(254, 328, 'LOZANO BARZOLA', 'MARYURY BASILIA', 'LOZANO BARZOLA MARYURY BASILIA', '0920986239', NULL, '', 'DAULE', 'RCTO EL PIGIO-LA SECA- LOS LOJAS', '0986904690', NULL, 'MAMÁ'),
(255, 329, 'CASTILLO MUÑOZ', 'JUANA FAUSTINA', 'CASTILLO MUÑOZ JUANA FAUSTINA', '0918193848', NULL, '', 'DAULE', 'ASSAD BUCARAM', '0993696482', NULL, 'MAMÁ'),
(256, 330, 'MORA PLUAS', 'MARIA BELEN', 'MORA PLUAS MARIA BELEN', '0929657761', NULL, '', 'NOBOL', 'PETRILLO PRIMAVERA 2', '0983559354', NULL, 'MAMÁ'),
(257, 331, 'BALLES GUEVARA', 'FELIX WALTER', 'BALLES GUEVARA FELIX WALTER', '0989023382', NULL, '', 'SANTA LUCIA', 'VIA CABUYAL RCTO. CANDELA', '0989023382', '', 'PAPÁ'),
(258, 332, 'BRIONES LEON', 'CARMEN CAROLINA', 'BRIONES LEON CARMEN CAROLINA', '0921502541', NULL, '', 'DAULE', 'BRISAS DE DAULE', '0959235963', NULL, 'MAMÁ'),
(259, 333, 'ANCHUNDIA SANCHEZ', 'JEFFERSON FERNANDO', 'ANCHUNDIA SANCHEZ JEFFERSON FERNANDO', '0922545157', NULL, 'jeffersona', 'MARIANITA 1', 'CALLLEJON DE LOMAS DE SARGENTILLO', '098598273', NULL, 'PAPA'),
(260, 334, 'MINDIOLAZA BAJAÑA', 'CINTHYA ISAMAR', 'MINDIOLAZA BAJAÑA CINTHYA ISAMAR', '0921246492', NULL, '', '', 'PATRIA NUEVA', '997430853', NULL, 'MAMA'),
(261, 335, 'VILLARREAL TORO', 'ELVIA ROSA', 'VILLARREAL TORO ELVIA ROSA', '138489568', NULL, '', '', 'PEDRO CARBO', '992100575', NULL, 'MAMA'),
(262, 336, 'PARRALES LEON', 'NARCISA ALEXANDRA', 'PARRALES LEON NARCISA ALEXANDRA', '0916602865', NULL, '', 'LOS QUEMADOS', 'LA T VIA LAUREL', '0967755345', NULL, 'MAMA'),
(263, 337, 'LAMILLA ORTEGA', 'SONIA', 'LAMILLA ORTEGA SONIA', '0915523393', NULL, '', 'BARBASCO CENTRAL', 'SANTA LUCIA', '0993569349', NULL, 'MAMA'),
(264, 339, 'PEÑAFIEL BONILLA', 'ERIKA  STEFANIA', 'PEÑAFIEL BONILLA ERIKA  STEFANIA', '0929989697', NULL, '', '', 'PEDRO ISAIAS 2DA ETAPA', '0939384954', NULL, 'MAMA'),
(265, 340, 'LENIS FAJARDO', 'MONICA LOURDES', 'LENIS FAJARDO MONICA LOURDES', '0915485353', NULL, '', 'MARIANITA # 5', 'LEOPOLDO BENITEZY JOAQUIN ORRANTIA', '0994526173', NULL, 'MAM,A'),
(266, 342, 'MEDINA BARZOLA', 'FELIPA', 'MEDINA BARZOLA FELIPA', '0920941697', NULL, '', 'NOBOL', 'BELLA FLOR', '0985888394', NULL, 'MAMA'),
(267, 344, 'ORTEGA NIETO', 'AMARILIS YOCONDA', 'ORTEGA NIETO AMARILIS YOCONDA', '0915808497', NULL, '', 'DAULE', 'Bahona nuevo by pass Nobol -FRENTE A GARZA ROJA', '0915808497', NULL, 'MAMA'),
(268, 345, 'PILLIGUA LIBERIO', 'ANDY GRACE', 'PILLIGUA LIBERIO ANDY GRACE', '0924640006', NULL, '', 'DAULE', 'ENTRADA A LIMONAL', '0961063363', NULL, 'MAMA'),
(269, 346, 'CANTOS SANCHEZ', 'JOHANNA EPIFANIA', 'CANTOS SANCHEZ JOHANNA EPIFANIA', '0921361283', NULL, '', 'LAUREL', 'RCTO CHIGUIJO', '0969763789', NULL, 'MAMA'),
(270, 349, 'GOMEZ MURILLO', 'MARIA ISABEL', 'GOMEZ MURILLO MARIA ISABEL', '0921249165', NULL, '', 'DAULE- ENTRADA POR MAGRO', 'RCTO. PAJONAL', '0959012782', NULL, 'MAMA'),
(271, 351, 'JIMENEZ NUMERABLE', 'ROSA ELENA', 'JIMENEZ NUMERABLE ROSA ELENA', '0928845676', NULL, '', 'DAULE', 'colegio Juan Bautista', '0990297807', NULL, 'MAMA'),
(272, 352, 'MACIAS MESTANZA', 'FLOR MARIA', 'MACIAS MESTANZA FLOR MARIA', '0917316820', NULL, '', '', 'BOLIVAR Y VERNAZA', '0980807595', NULL, 'MAMA'),
(273, 355, 'DELGADO DELGADO', 'SEGUNDO AUDON', 'DELGADO DELGADO SEGUNDO AUDON', '0912163094', NULL, '', 'DAULE', 'FRENTE AL COLEGIO LOS DAULIS', '0985463838', NULL, 'PAPA'),
(274, 356, 'HUAYAMAVE H', 'JAZMIN', 'HUAYAMAVE H JAZMIN', '0940765142', NULL, '', '', 'ENTRADA AL PEDREGAL', '0969771161', NULL, 'MAMA'),
(275, 357, 'BUENDIA RODRIGUEZ', 'SILVIA PATRICIA', 'BUENDIA RODRIGUEZ SILVIA PATRICIA', '0927134130', NULL, '', 'DAULE', 'Al frente de la píldora“Gloria Matilde”- CIELO DE BELEN', '096768795', NULL, 'MAMA'),
(276, 358, 'ALVARADO CAMPOVERDE', 'PAOLA LORENA', 'ALVARADO CAMPOVERDE PAOLA LORENA', '0922549225', NULL, '', '', 'CDLA. SIXTO RUGEL Y CALLEJON S/N', '0985642650', NULL, 'MAMA'),
(277, 360, 'RUIZ ARREAGA', 'GLENDA PATRICIA', 'RUIZ ARREAGA GLENDA PATRICIA', '0916171853', NULL, '', 'DAULE', 'CDLA YOLITA', '0959684680', NULL, 'TIA'),
(278, 361, 'ALDAZ QUINTO', 'PATRICIA YANETH', 'ALDAZ QUINTO PATRICIA YANETH', '0917314528', NULL, '', '', 'CALLE QUITO Y JOSE VELEZ', '0961871120', NULL, 'MAMA'),
(279, 359, 'ASPIAZU RUIZ', 'DIANA DEL CARMEN', 'ASPIAZU RUIZ DIANA DEL CARMEN', '0924723075', NULL, '', 'BELEN', 'GARCIA MORENO Y OTTO AVELLAN', '0993669500', NULL, 'MAMA'),
(280, 362, 'LOPEZ LEON', 'ELIANA ELOISA', 'LOPEZ LEON ELIANA ELOISA', '0922274840', NULL, '', 'DAULE', 'MARIANITA 5- Paseo shopping Daule diagonal Unidad Educativa Daul', '0985682222', NULL, 'MAMA'),
(281, 363, 'TOMALA', 'DELIA', 'TOMALA DELIA', '', NULL, '', '', 'CIUDADELA LOS ALAMOS', '0981051437', NULL, 'MAMA'),
(282, 364, 'RONQUILLO CANTOS', 'ELSA ARGENTINA', 'RONQUILLO CANTOS ELSA ARGENTINA', '0915518419', NULL, '', 'CORRENTOSO', 'RCTO LAS MARAVILLAS', '0994121838', NULL, 'MAMA'),
(283, 365, 'BAJAÑA CHIRIGUAYA', 'BERTHA ARACELY', 'BAJAÑA CHIRIGUAYA BERTHA ARACELY', '0920979754', NULL, '', 'SANTA LUCIA', 'RCTO PAIPAYALES', '0994750149', NULL, 'MAMA'),
(284, 366, 'AYALA GARCIA', 'SANDRA ELIZABETH', 'AYALA GARCIA SANDRA ELIZABETH', '0918606906', NULL, '', 'RUMIÑAHUI', '10 DE AGOSTO Y LUIS URDANETA', '0939641578', NULL, 'MAMA'),
(285, 367, 'FAJARDO CANDELARIO', 'EVELYN ALICIA', 'FAJARDO CANDELARIO EVELYN ALICIA', '0922399795', NULL, '', 'YOLITA', 'NARCISA DE JESUS Y DOMINGO ELIZALDECandelario', '0997537129', NULL, 'MAMA'),
(286, 368, 'LIBERIO BAJAÑA', 'YANINA YARITZA', 'LIBERIO BAJAÑA YANINA YARITZA', '', NULL, '', 'EL TRIUNFO 3ERA ETAPA', 'BATALLON DAULE Y GENERAL LEONIDAS PLAZA', '0989515367', NULL, 'HERMANA'),
(287, 369, 'MORENO CORTEZ', 'FATIMA', 'MORENO CORTEZ FATIMA', '0942517250', NULL, '', 'DAULE', 'RECINTO COLORADO', '0959710275', NULL, 'MAMA'),
(288, 370, 'RONQUILLO CASTRO', 'MAGNO WILFRIDO', 'RONQUILLO CASTRO MAGNO WILFRIDO', '0920844529', NULL, '', '', 'VICENTE PIEDRAHITA Y ROCAFUERTE', '939035989', NULL, 'PAPA'),
(289, 371, 'BAJAÑA BEDOR', 'ROSA', 'BAJAÑA BEDOR ROSA', '0919194233', NULL, '', 'DAULE', 'ENTRADA AL VUELTA', '0991804617', NULL, 'MAMA'),
(290, 372, 'ORTEGA SAENZ', 'ERIKA', 'ORTEGA SAENZ ERIKA', '0924240989', NULL, '', 'santa clara', 'ANTONIO PARRA', '0980257155', NULL, 'MAMA'),
(291, 374, 'RODRIGUEZ MONTENEGRO', 'JANETH ALEXANDRA', 'RODRIGUEZ MONTENEGRO JANETH ALEXANDRA', '0930171855', NULL, '', '', 'PATRIA NUEVA', '0979864567', NULL, 'MAMA'),
(292, 373, 'MARTINEZ ROMERO', 'ROSA', 'MARTINEZ ROMERO ROSA', '0918103532', NULL, '', 'NOBOL', 'CALLA PRINCIPAL RIO AMAZONA', '0968352567', NULL, 'MAMA'),
(293, 375, 'LOPEZ. DE HIDALGO', 'ROSA DEL CONSUELO', 'LOPEZ. DE HIDALGO ROSA DEL CONSUELO', '', NULL, '', '', '9DE OCTUBRE Y PIEDRAHITA', '0999735870', NULL, 'TIA'),
(294, 376, 'YUPA ASOGUE', 'ANA CRISTHINA', 'YUPA ASOGUE ANA CRISTHINA', '0994331866', NULL, '', 'NARANJAL', 'COOPERTIVA 6 DE JULIO', '0994331866', NULL, 'MAMA'),
(295, 377, 'PLUAS TOMALA', 'BERNARDA DEL LOURDES', 'PLUAS TOMALA BERNARDA DEL LOURDES', '', NULL, '', '', 'MAGRO', '0991535012', NULL, 'MAMA'),
(296, 378, 'MARTINEZ BARZOLA', 'MONICA MARIA', 'MARTINEZ BARZOLA MONICA MARIA', '', NULL, '', '', 'PATRIA NUEVA', '981518043', NULL, 'MAMA');
INSERT INTO `sw_representante` (`id_representante`, `id_estudiante`, `re_apellidos`, `re_nombres`, `re_nombre_completo`, `re_cedula`, `re_genero`, `re_email`, `re_sector`, `re_direccion`, `re_telefono`, `re_observacion`, `re_parentesco`) VALUES
(297, 380, 'CASTAÑEDA TORRES', 'MONICA JACKELINE', 'CASTAÑEDA TORRES MONICA JACKELINE', '0913660296', NULL, '', 'DAULE', 'KM 54 DAULE VIA SANTA LUCIA', '0999333509', NULL, 'MAMA'),
(298, 381, 'FUENTES ROMAN', 'PEDRO ISAIAS', 'FUENTES ROMAN PEDRO ISAIAS', '0909401101', NULL, '', 'DAULE', 'RECINTO VALDIVIA', '0969867307', NULL, 'PAPA'),
(299, 382, 'GAVILANEZ LOOR', 'MARIA ALEXANDRA', 'GAVILANEZ LOOR MARIA ALEXANDRA', '0917590051', NULL, '', 'DAULE', 'SEÑOR DE LOS MILAGROS Y GALLARDO', '0981596728', NULL, 'MAMA'),
(300, 383, 'TORRES RUGEL', 'PATRICIA', 'TORRES RUGEL PATRICIA', '0925897316', NULL, '', 'DAULE', 'CDAL. JUAN BAUTISTA AGUIRRES', '0992147167', NULL, 'MAMA'),
(301, 385, 'ORTIZ AYALA', 'ESPERANZA', 'ORTIZ AYALA ESPERANZA', '0957164429', NULL, '', 'SANTA LUCIA', 'BERMEO DE ABAJO', '0997931030', NULL, 'MAMA'),
(302, 386, 'VERA QUINTANA', 'PATRICIA', 'VERA QUINTANA PATRICIA', '0912826575', NULL, '', 'DAULE', 'MARIANITA # 5', '0989386363', NULL, 'MAMA'),
(303, 387, 'QUINTO BAJAÑA', 'MARITZA KARINA', 'QUINTO BAJAÑA MARITZA KARINA', '0920085446', NULL, '', 'BOQUERON VIA LAS MARAVILLAS', 'RCTO BOQUERON', '0991029492', NULL, 'MAMA'),
(304, 388, 'SUAREZ CERCADO', 'CECILIA MAGDALENA', 'SUAREZ CERCADO CECILIA MAGDALENA', '0922788823', NULL, '', 'GUAYAQUIL', 'KM 26 VIA A DAULE', '0963292616', NULL, 'MAMA'),
(305, 389, 'HIDALGO ESPINOZA', 'MABEL ELIZABETH', 'HIDALGO ESPINOZA MABEL ELIZABETH', '0923877260', NULL, '', 'DAULE', 'RECINTO SAN GABRIEL DERSVIO LAS CAÑAS', '0988202898', NULL, 'MAMA'),
(306, 390, 'MONTALVAN BRIONES', 'JAVIER', 'MONTALVAN BRIONES JAVIER', '0919270850', NULL, '', 'DAULE', 'PLAZA BOLIVAR', '0986942505', NULL, 'PAPA'),
(307, 391, 'HERRERA ALVAREZ', 'STEFANIE', 'HERRERA ALVAREZ STEFANIE', '0922135751', NULL, '', 'DAULE', '10 DE AGOSTO Y VERNAZA', '0981795458', NULL, 'MAMA'),
(308, 392, 'LEON HERRERA', 'VERONICA SUSANA', 'LEON HERRERA VERONICA SUSANA', '0923602503', NULL, '', 'DAULE', 'VIA A SALITRE KM 21', '0994671437', NULL, 'MAMA'),
(309, 393, 'PACHECO ALVARADO', 'AMPARO DE JESUS', 'PACHECO ALVARADO AMPARO DE JESUS', '0915596381', NULL, '', 'DAULE', 'DEVIO LAS CAÑA O SAN ANDRES', '0979712119', NULL, 'MAMA'),
(310, 394, 'GOMEZ QUIJIJE', 'QUETY ALEXANDRA', 'GOMEZ QUIJIJE QUETY ALEXANDRA', '0916493554', NULL, '', 'DAULE', 'CDLA. PATRIA NUEVA', '0979422743', NULL, 'MAMA'),
(311, 395, 'SEGURA ANDRIAN', 'HERVAS NATIVIDAD', 'SEGURA ANDRIAN HERVAS NATIVIDAD', '0914025598', NULL, '', 'LOMA DE SARGENTILLO', 'RECINTO EL PRINCIPE', '0982849523', NULL, 'MAMA'),
(312, 396, 'BANCHÓN GUERRERO', 'ANA MARÍA', 'BANCHÓN GUERRERO ANA MARÍA', '919464149', NULL, '', 'DAULE', 'CALLE 9 DE OCTUBRE Y BAY PASS', '0969873313', NULL, 'MAMÁ'),
(313, 397, 'MONCAYO CRUZ', 'FLOR MARIA', 'MONCAYO CRUZ FLOR MARIA', '0925211633', NULL, '', 'VIAS LAS MARAVILLA', 'VIAS LAS MARAVILLA', '0979436151', NULL, 'MAMA'),
(314, 398, 'BARZOLA SALAS', 'DELEY ISABEL', 'BARZOLA SALAS DELEY ISABEL', '0909946170', NULL, '', 'DAULE', 'RECINTO NARANJO', '0994706624', NULL, 'MAMA'),
(315, 399, 'OLVERA SEGURA', 'DIANA IVONNE', 'OLVERA SEGURA DIANA IVONNE', '0924477342', NULL, '', 'LOMA DE SARGENTILLO', 'RECINTO EL PRINCIPE', '0960189036', NULL, 'MAMA'),
(316, 400, 'SEME RUIZ', 'ESTOICA', 'SEME RUIZ ESTOICA', '0912951522', NULL, '', 'LOMA DE SARGENTILLO', 'BARRIO SAN VICENTE', '0988388907', NULL, 'TIA'),
(317, 401, 'SOLIS MINDIOLA', 'VICTOR HUGO', 'SOLIS MINDIOLA VICTOR HUGO', '1200078457', NULL, '', 'DAULE', 'FLOR DE MARIA', '0987357947', NULL, 'PAPA'),
(318, 402, 'FAJARDO HOLGUIN', 'AMARILI EUGENIA', 'FAJARDO HOLGUIN AMARILI EUGENIA', '0915635171', NULL, '', 'LOMA DE SARGENTILLO', 'LOMA DE SARGENTILLO CALLE PRINCIPAL', '0978771770', NULL, 'MAMA'),
(319, 259, 'Troya De Mera', 'Diana', 'Troya De Mera Diana', '', NULL, '', 'Alamos', 'Alamos', '0939370482', NULL, 'Mamà'),
(320, 403, 'PACHECO ALVARADO', 'JENNIFER DOLORES', 'PACHECO ALVARADO JENNIFER DOLORES', '929409381', NULL, '', 'Francisco de Marco y 9 de octubr', 'Francisco de Marco y 9 de octubre', '0979583805', NULL, 'Mamà'),
(321, 404, 'ROMERO MARTINEZ', 'GENESIS ESTHER', 'ROMERO MARTINEZ GENESIS ESTHER', '0953020625', NULL, '', 'Multicentro Manzana 12 solar 2 x', 'Multicentro Manzana 12 solar 2 x el upc de la yolita', '0960538684', NULL, 'Mamà'),
(322, 405, 'SANCHEZ ORTEGA', 'JENNIFER PAOLA', 'SANCHEZ ORTEGA JENNIFER PAOLA', '0941077745', NULL, '', 'AV. San francisco.', 'AV. San francisco.', '0979501378', NULL, 'Mamà'),
(323, 406, 'PILLASAGUA', 'ERIKA LEONELA', 'PILLASAGUA ERIKA LEONELA', '0920980091', NULL, '', 'Assab Bucaram', 'Assab Bucaram', '0993619054', NULL, 'Mamà'),
(324, 408, 'CHIRIGUAYA', 'DENNISE KATIUSKA', 'CHIRIGUAYA DENNISE KATIUSKA', '911652790', NULL, '', 'Av Señor de los milagros y gran', 'Av Señor de los milagros y gran Colombia', '0993455815', NULL, 'Mamà'),
(325, 409, 'RONQUILLO RUIZ', 'JACKELINE SAMANTA', 'RONQUILLO RUIZ JACKELINE SAMANTA', '0940490527', NULL, '', 'Av San Francisco CDLA SAN JOSEQQ', 'Av San Francisco CDLA SAN JOSEQQQQ', '098622112', NULL, 'Mamà'),
(326, 410, 'RIVAS RUIZ', 'EVELING EMILIA', 'RIVAS RUIZ EVELING EMILIA', '941130205', NULL, '', 'Banife calles Balzar y Santa Luc', 'Banife calles Balzar y Santa Lucia', '0997179818', NULL, 'Mamà'),
(327, 411, 'RONQUILLO RUIZ', 'JACKELINE SAMANTA', 'RONQUILLO RUIZ JACKELINE SAMANTA', '940490527', NULL, '', 'Belén', 'Belén', '0998252164', NULL, 'Mamà'),
(328, 412, 'MORA CORTEZ', 'AMÉRICA MARICELA', 'MORA CORTEZ AMÉRICA MARICELA', '', NULL, '', '', '', '0998252164', NULL, 'Mamà'),
(329, 413, 'ROMERO CASTRO', 'RAQUEL JESUS', 'ROMERO CASTRO RAQUEL JESUS', '929201945', NULL, '', 'DOMINGO COMÍN', 'DOMINGO COMÍN', '0998252164', NULL, 'Mamà'),
(330, 414, 'GARCÍA BEDOYA', 'GUADALUPE', 'GARCÍA BEDOYA GUADALUPE', '928462340', NULL, '', 'PEDRO MENEDEZ', 'PEDRO MENEDEZ', '0939512524', NULL, 'Mamà'),
(331, 415, 'PLUAS TOMALAS', 'BERNARDA DE LOURDES', 'PLUAS TOMALAS BERNARDA DE LOURDES', '0929404168', NULL, '', 'Magro atrás de la Escuela Ismael', 'Magro atrás de la Escuela Ismael Perez Pazmiño calle 28 de marzo', '0991535012', NULL, 'Mamà'),
(332, 416, 'SALAVARRIA SANCHEZ', 'CECILIA CECIBEL', 'SALAVARRIA SANCHEZ CECILIA CECIBEL', '929404168', NULL, '', 'Ciudadela Assad Bucaram', 'Ciudadela Assad Bucaram', '0990530007', NULL, 'Mamà'),
(333, 418, 'Briones Herrera', 'Carlos Javier', 'Briones Herrera Carlos Javier', '0916493935', NULL, '', 'BERMEJO', 'BERMEJO SANTA LUCIA', '0986628340', NULL, 'padre'),
(334, 419, 'ALVARADO RUIZ', 'FLOR MARIBEL', 'ALVARADO RUIZ FLOR MARIBEL', '', NULL, '', 'PEAJE CHIVERIA', 'BARRIO SAN GUILLERMO, ENTRADA HIDALGO HIDALGO', '0979754936', NULL, 'MADRE'),
(335, 420, 'CASTAÑEDA SANCHEZ', 'LADY ESTEFANÌA', 'CASTAÑEDA SANCHEZ LADY ESTEFANÌA', '0927244459', NULL, '', 'LOS MANGOS', 'PUENTE LUCÌA', '0960465652', NULL, 'MADRASTA'),
(336, 421, 'PRADO JIMENEZ', 'MAYI IVETTE', 'PRADO JIMENEZ MAYI IVETTE', '0922243019', NULL, '', 'DAULE PILADORA EL EDEN', 'LOS ALAMOS 2', '0960599245', NULL, 'MADRE'),
(337, 423, 'DUQUE LIBERIO', 'CARMEN ALEXANDRA', 'DUQUE LIBERIO CARMEN ALEXANDRA', '0919680066', NULL, '', 'DAULE', 'CDLA. BUCARAM', '0994591139', NULL, 'MAMÁ'),
(338, 424, 'RODRIGUEZ CASTRO', 'AURA MITZIBELL', 'RODRIGUEZ CASTRO AURA MITZIBELL', '0920764677', NULL, '', 'DAULE', 'AV. SAN FRANCISCO CDLA. SAN JOSE', '0960909140', NULL, 'MAMÁ'),
(339, 425, 'LOPEZ COX', 'NELLY MARGARITA', 'LOPEZ COX NELLY MARGARITA', '', NULL, '', 'DAULE', 'RCTO BRISAS CERCA DE LA PILADORA VIRGEN', '0990274310', NULL, 'MAMÁ'),
(340, 426, 'LOPEZ LEON', 'ELIANA ELOISA', 'LOPEZ LEON ELIANA ELOISA', '0922274840', NULL, '', 'DAULE', 'MARIANITA 5 DIAGONAL UE DAULE', '0985682222', NULL, 'MAMÁ'),
(341, 427, 'PEREDO TUAREZ', 'MARIUXI', 'PEREDO TUAREZ MARIUXI', '0925128837', NULL, '', 'GUAYAQUIL', 'KM 26 VIA DAULE COOP LOS ANGELES', '0959057178', NULL, 'MAMÁ'),
(342, 428, 'CASTRO ROSADO', 'MANUELA MARILIN', 'CASTRO ROSADO MANUELA MARILIN', '1204337057', NULL, '', 'RCTO LAS MERCEDES', 'KM 24 VIA A DAULE', '0985230360', NULL, 'MAMA'),
(343, 429, 'RUIZ ARREAGA', 'GLENDA PATRICIA', 'RUIZ ARREAGA GLENDA PATRICIA', '0916171853', NULL, '', 'LA YOLITA', 'OLMEDO ALMEIDA Y LUIS URDANETA', '0999326036', NULL, 'TIA'),
(344, 430, 'CASTRO ROSADO', 'MANUELA MARILIN', 'CASTRO ROSADO MANUELA MARILIN', '1204337057', NULL, '', 'LAS MERCEDES', 'KM 24 VIA DAULE', '0985303609', NULL, 'MAMA'),
(345, 431, 'CHAVEZ GONZALEZ', 'MARIA FERNANDA', 'CHAVEZ GONZALEZ MARIA FERNANDA', '0922495692', NULL, '', 'ASSAC BUCARAM', 'BY PASS Y MANUEL VILLAVICENCIO', '0982968295', NULL, 'MAMA'),
(346, 432, 'RODRIGUEZ BANCHON', 'SILVIA EUGENIA', 'RODRIGUEZ BANCHON SILVIA EUGENIA', '0922279757', NULL, '', 'DAULE', 'CALLE 9 DE OCTUBRE Y LEONIDAS PLAZA', '0984889245', NULL, 'MAMÁ'),
(347, 433, 'CASTRO ROSADO', 'MANUELA MARILIN', 'CASTRO ROSADO MANUELA MARILIN', '1204337057', NULL, 'castromari', 'LAS MERCEDES', 'KM 24 VIA DAULE', '0985303609', NULL, 'MAMA'),
(348, 434, 'JURADO FRANCO', 'KATIUSKA ADELINA', 'JURADO FRANCO KATIUSKA ADELINA', '0918109729', NULL, '', 'RCTO PIÑAL DE ABAJO', 'RCTO PIÑAL DE ABAJO', '0967997715', NULL, 'MAMA'),
(349, 435, 'GARCIA JIMENEZ', 'JANNY ALEXANDRA', 'GARCIA JIMENEZ JANNY ALEXANDRA', '0919049254', NULL, '', 'RUMIÑAHUI', 'RUMIÑAHUI', '0984918433', NULL, 'MAMA'),
(350, 436, 'LEMA RUIZ', 'JESUS VINICIO', 'LEMA RUIZ JESUS VINICIO', '2190318621', NULL, '', 'DURAN EL RECREO COOP 28 DE AGOST', 'DURAN', '0991726750', NULL, 'PAPA'),
(351, 437, 'RUIZ RODRIGUEZ', 'GLENDA PATRICIA', 'RUIZ RODRIGUEZ GLENDA PATRICIA', '0916171853', NULL, 'glendiuchi', 'LA YOLITA', 'OLMEDO ALMEIDA Y LUIS URDANETA', '0993260365', NULL, 'TIA'),
(352, 438, 'BARZOLA BARZOLA', 'ROSA ELVIRA', 'BARZOLA BARZOLA ROSA ELVIRA', '0911504629', NULL, '', 'CIUDADELA LOS DAULIS', 'DOMINGO COMIN Y LA LIBERTAD', '0968426503', NULL, 'MAMA'),
(353, 439, 'JURADO ALVARADO', 'MERCEDES NARCISA', 'JURADO ALVARADO MERCEDES NARCISA', '0926379033', NULL, '', 'STA LUCIA', 'STA LUCIA VIA BALZAR', '0988272519', NULL, 'PRIMA'),
(354, 440, 'MORENO ORTEGA', 'JOSÉ DANIEL', 'MORENO ORTEGA JOSÉ DANIEL', '0931511232', NULL, '', 'ANIMAS', 'ANIMAS, ATRAS DE LA IGLESIA DE ANIMAS', '0967639502', NULL, 'HERMANO'),
(355, 441, 'CEDEÑO TELLO', 'KENYA VANESSA', 'CEDEÑO TELLO KENYA VANESSA', '0928157700', NULL, '', 'DAULE', 'PATRIA NUEVA HERMOGENES RIVAS', '0991327822', NULL, 'MAMÁ'),
(356, 442, 'ROMERO CASTRO', 'RAQUEL JESUS', 'ROMERO CASTRO RAQUEL JESUS', '0929201945', NULL, '', 'DAULE', 'DOMINGO COMIN ATRAS DEL CEMENTERIO', '0968365710', NULL, 'MAMÁ'),
(357, 444, 'MENDEZ CORTEZ', 'PORFIRIA', 'MENDEZ CORTEZ PORFIRIA', '0912920584', NULL, '', 'PASANDO EL MURO DE ANIMAS', 'TONTAL DE ANIMAS', '0968494285', NULL, 'TIA'),
(358, 446, 'BAJAÑA VELIZ', 'HILDA NARCISA', 'BAJAÑA VELIZ HILDA NARCISA', '0923249775', NULL, '', 'DAULE MARIANITA 1', 'MARIANITA 1 POR EL PARQUE', '0967324407', NULL, 'MADRE'),
(359, 445, 'CHOEZ PEÑAHERRERA', 'MARIA VIRGINIA', 'CHOEZ PEÑAHERRERA MARIA VIRGINIA', '919534438', NULL, '', 'ESTACADA', 'DAULE', '0959438852', NULL, 'TIA'),
(360, 447, 'PACHECO SAN LUCAS', 'CARLOTA ELADIA', 'PACHECO SAN LUCAS CARLOTA ELADIA', '0918533167', NULL, '', 'CHIGUIJO', 'LAS ANIMAS', '961611820', NULL, 'MAMA'),
(361, 448, 'NAVARRETE BARZOLA', 'GLADYS GUADALUPE', 'NAVARRETE BARZOLA GLADYS GUADALUPE', '0916492283', NULL, '', 'DAULE', 'BOLIVAR Y SAMBORODON CERCA DEL IESS', '0993216668', NULL, 'MADRE'),
(362, 449, 'CRUZ MURILLO', 'VANESSA ALEXANDRA', 'CRUZ MURILLO VANESSA ALEXANDRA', '0920844776', NULL, '', 'VIA LAS CAÑAS NAUPE POR MAGRO', '200 MTR DE PILADORA NACOL', '0993477763', NULL, 'MADRE'),
(363, 451, 'LUQUE HUAYAMABE', 'MERLY KATHERINE', 'LUQUE HUAYAMABE MERLY KATHERINE', '0919464495', NULL, '', 'DAULE', 'Luis Lara y Rocafuerte - Salida a la perimetral', '0991770533', NULL, 'MAMÀ'),
(364, 450, 'CARLOS ORTEGA', 'MAYRA ELIZABETH', 'CARLOS ORTEGA MAYRA ELIZABETH', '0922094263', NULL, '', 'NAUPE', 'NAUPE ENTRADA A LAS CAÑAS', '0979668099', NULL, 'MADRE'),
(365, 452, 'ORTEGA MERCHAN', 'AMMY KRISTELL', 'ORTEGA MERCHAN AMMY KRISTELL', '0923111728', NULL, '', 'HUANCHICHAL', 'HUANCHICHAL', '098295534', NULL, 'HERMANA'),
(366, 453, 'Guerrero Candelario', 'Angelica', 'Guerrero Candelario Angelica', '0920533502', NULL, '', 'Daule', '9 de agosto', '0982949972', NULL, 'Mamá'),
(367, 454, 'TORRES TORRES', 'EDILMA ALEXANDRA', 'TORRES TORRES EDILMA ALEXANDRA', '0916743503', NULL, '', 'VALDIVIA', 'VALDIVIA', '0996893745', NULL, 'MADRE'),
(368, 455, 'TORRES TORRES', 'EDILMA ALEXANDRA', 'TORRES TORRES EDILMA ALEXANDRA', '0916743503', NULL, '', 'VALDIVIA', 'VALDIVIA', '0968937452', NULL, 'MADRE'),
(369, 456, 'ALVARADO NIETO', 'JULIA JAZMIN', 'ALVARADO NIETO JULIA JAZMIN', '0915321491', NULL, '', 'EL RECUERDO', 'OLMEDO  GENERAL RUMIÑAHUI', '0981887388', NULL, 'MADRE'),
(370, 457, 'VERA PONCE', 'GUISELLA YESENIA', 'VERA PONCE GUISELLA YESENIA', '0923753883', NULL, '', 'LAS MARAVILLAS', 'MARAVILLAS SANTA ROSA', '0962729367', NULL, 'MADRE'),
(371, 458, 'TORRES MARTILLO', 'MONICA DEL ROCIO', 'TORRES MARTILLO MONICA DEL ROCIO', '0917176653', NULL, '', 'RCTO SAN JOSE', 'RCTO SAN JOSE', '0980191057', NULL, 'MAMA'),
(372, 459, 'MORAN', 'AMADA', 'MORAN AMADA', '0905621553', NULL, '', '', '', '0961977681', '', 'MADRE'),
(373, 460, 'JARAMILLO TOMALA', 'GLADYS GARDENIA', 'JARAMILLO TOMALA GLADYS GARDENIA', '0920032018', NULL, '', 'RCTO NAUPE', 'RCTO NAUPE', '0980745112', NULL, 'MAMA'),
(374, 461, 'CHILAN MERCHAN', 'MIRIAN JANETTE', 'CHILAN MERCHAN MIRIAN JANETTE', '1717140394', NULL, '', 'PUENTE LUCIA', 'KM27 VIA DAULE', '0982572922', NULL, 'MADRE'),
(375, 463, 'RUIZ BRIONES', 'EULOGIO', 'RUIZ BRIONES EULOGIO', '0903053908', NULL, '', 'PIÑAL', 'PIÑAL KM 6 Y MEDIO MAGRO', '0991157343', NULL, 'ABUELO'),
(376, 462, 'TOMALA TOMALA', 'VICENTA ELIZABETH', 'TOMALA TOMALA VICENTA ELIZABETH', '0922717251', NULL, '', 'RCTO NAUPE', 'RCTO NAUPE', '0985661788', NULL, 'MAMA'),
(377, 464, 'BARZOLA MARTILLO', 'ROSA JANETTE', 'BARZOLA MARTILLO ROSA JANETTE', '0916747470', NULL, '', 'PARQUE ACUATICO', 'DOMINGO COMIN Y JOSE MARIA', '0999410417', NULL, 'MADRE'),
(378, 465, 'PINELA CANTOS', 'JUANA LUCIA', 'PINELA CANTOS JUANA LUCIA', '0922624168', NULL, '', 'PARROQUIA LAUREL', 'PARROQUIA LAUREL', '0989018410', NULL, 'MADRE'),
(379, 467, 'LIBERIO MEJIA', 'LUZ MARIA', 'LIBERIO MEJIA LUZ MARIA', '0913660205', NULL, 'Marialiber', 'BAY PASS- SECTOR SAN JOSE', 'BAY PASS ENTRE BATALLON DAULE', '0997450723', NULL, 'MAMA'),
(380, 468, 'RAVELO BANCHON', 'AURA REBECA', 'RAVELO BANCHON AURA REBECA', '0921022661', NULL, 'auraravelo', 'DAULE', 'GENERAL VERNAZA Y PEDRO CARBO', '0939668010', NULL, 'MAMA'),
(381, 469, 'MARTINEZ BARZOLA', 'MONICA MARIA', 'MARTINEZ BARZOLA MONICA MARIA', '0916175987', NULL, '', 'LOS POZOS - LOMA PELADA', 'LOS POZO LOMA PELADA', '0981518043', NULL, 'MAMA'),
(382, 470, 'LEON DELGADO', 'PATRICIA PAULINA', 'LEON DELGADO PATRICIA PAULINA', '0921248357', NULL, '', 'RUMIÑAHUI', 'RODRIGO CHAVEZY DOMINGO ELIZALDE', '0981382029', NULL, 'MAMA'),
(383, 471, 'AVILES RUIZ', 'LUISA MARIA', 'AVILES RUIZ LUISA MARIA', '0996602884', NULL, '', 'PASCUALES', 'COOP. LOS ANGELES KM 26 VIA DAULE', '0996602884', NULL, 'MAMA'),
(384, 472, 'Parrales León', 'Narcisa Alexandra', 'Parrales León Narcisa Alexandra', '0916602865', NULL, '', 'Daule', '11 de octubre y Juan León Mera MZ 28 SL 2', '0967755345', NULL, 'Mamá'),
(385, 473, 'ORTEGA MERCHAN', 'AMMY KRYSTELL', 'ORTEGA MERCHAN AMMY KRYSTELL', '0923111728', NULL, '', 'Rcto. Huanchichal', 'Rcto. Huanchichal', '0981351317', NULL, 'Hermana'),
(386, 474, 'Zurita Ruiz', 'Alexandra del Rocío', 'Zurita Ruiz Alexandra del Rocío', '0917119109', NULL, '', 'Rcto. La Fabiola', 'Km 16 vía salitre', '0939174764', NULL, 'Mamá'),
(387, 475, 'Ruíz Mosquera', 'María Fernanda', 'Ruíz Mosquera María Fernanda', '0941029878', NULL, '', 'Daule', 'Detras del Estadio Daule', '0985894183', NULL, 'Mamá'),
(388, 477, 'Cedeño Vásquez', 'Norma María', 'Cedeño Vásquez Norma María', '0918824434', NULL, '', 'Petrillo', 'Petrillo Km 30 vía daule', '0994811590', NULL, 'Mamá'),
(389, 478, 'COLOMA LOPEZ', 'MICHAEL FABIAN', 'COLOMA LOPEZ MICHAEL FABIAN', '0921537551', NULL, '', 'Daule', 'AV.DOMINGO COMIN MANUEL GONZALES', '0994720835', NULL, 'Papá'),
(390, 479, 'Vargas Selda', 'Maria Fernanda', 'Vargas Selda Maria Fernanda', '0921836953', NULL, '', 'Daule', 'Domingo Can.Real.MZ.159', '0992320472', NULL, 'Mamá'),
(391, 480, 'Herrera Márquez', 'Eveling Mayling', 'Herrera Márquez Eveling Mayling', '0926371477', NULL, '', 'Patria Nueva', 'Patria Nueva', '0989699396', NULL, 'Mamá'),
(392, 481, 'Chiriguaya Fariño', 'Ana del Pilar', 'Chiriguaya Fariño Ana del Pilar', '0918960592', NULL, '', 'Daule', 'KM 16 Via Guayaquil', '0968170781', NULL, 'Mamá'),
(393, 482, 'Realpe Andrade', 'Nuvia Germania', 'Realpe Andrade Nuvia Germania', '0801312166', NULL, '', 'Cdla. El Triunfo', 'Rocafuerte y Luis Lara Lara', '0982872862', NULL, 'Mamá'),
(394, 483, 'FRANCO TORRES', 'EDWIN HERNÁN', 'FRANCO TORRES EDWIN HERNÁN', '1803821089', NULL, '', 'Recinto los Ángeles', 'Recinto los Ángeles', '0994044864', NULL, 'Papá'),
(395, 484, 'Bonilla', 'Alejandro', 'Bonilla Alejandro', '0928841766', NULL, '', 'Santa Lucia', 'Santa Lucia', '0959132779', NULL, 'Hermana'),
(396, 485, 'ARCENTALES MERO', 'LEONARDO EFREN', 'ARCENTALES MERO LEONARDO EFREN', '0918310608', NULL, '', 'Daule', 'CALLES MALECON Y QUITO', '0980885042', NULL, 'Papá'),
(397, 486, 'Nieto Hidalgo', 'Byron wilfrido', 'Nieto Hidalgo Byron wilfrido', '0911343572', NULL, '', 'Daule', 'Olmedo y albdon Calderón', '0939961047', NULL, 'Papá'),
(398, 487, 'Gomez choez', 'Diana Elizabeth', 'Gomez choez Diana Elizabeth', '0940684673', NULL, '', 'Recinto rinconada', 'Recinto rinconada', '0939849089', NULL, 'Mamá'),
(399, 488, 'Caleño Almeida', 'Julia Ana', 'Caleño Almeida Julia Ana', '0925944894', NULL, '', 'Recinto Colorado', 'Recinto Colorado', '0925944894', NULL, 'Mamá'),
(400, 489, 'Navarrete ravelo', 'Yenifer maria', 'Navarrete ravelo Yenifer maria', '0964042063', NULL, '', 'Banife', 'Camilo ponce y carlos cevallos', '0983634782', NULL, 'Mamá'),
(401, 490, 'Saldaña Hidalgo', 'Eddy Jarol', 'Saldaña Hidalgo Eddy Jarol', '0915069819', NULL, '', 'Banife', 'Calle justo torres y marta bucaram', '0980059977', NULL, 'Papá'),
(402, 491, 'CARABAJO PERALTA', 'MANUEL HENRIQUE', 'CARABAJO PERALTA MANUEL HENRIQUE', '0919187906', NULL, '', 'PUENTE LUCIA', 'COOP. LOS PINOS KM26 VIA A DAULE', '0990934439', NULL, 'PADRE'),
(403, 492, 'CARABAJO PERALTA', 'MANUEL ENRIQUE', 'CARABAJO PERALTA MANUEL ENRIQUE', '0919187906', NULL, '', 'PUENTE LUCIA', 'COOP. LOS PINOS KM 26 VIA A DAULE', '0990934439', NULL, 'PADRE'),
(404, 493, 'SILVA BAJAÑA', 'JULY AMPARO', 'SILVA BAJAÑA JULY AMPARO', '0916600737', NULL, '', 'DAULE', '9 DE OCTUBRE Y JUAN LEON MERA', '0989094381', NULL, 'MADRE'),
(405, 494, 'TORRES TORRES', 'MARICELA LILIAN', 'TORRES TORRES MARICELA LILIAN', '0916271901', NULL, '', 'DAULE, EL RECUERDO', 'MISAEL ACOSTA Y 9 DE OCTUBRE', '0993165307', '', 'MADRE'),
(406, 495, 'ZAMBRANO MOSQUERA', 'MARCO JAVIER', 'ZAMBRANO MOSQUERA MARCO JAVIER', '0915701932', NULL, '', 'NAUPE', 'MAS ADELANTE DE PIÑAL', '0968783609', '', 'PADRE'),
(407, 496, 'MORAN VOLLAFUERTE', 'AMADA', 'MORAN VOLLAFUERTE AMADA', '0905621553', NULL, '', 'LOMAS DE SARGENTILLO', 'SECTO 2 DE MAYO', '0961977681', '', 'ABUELA'),
(408, 498, 'RONERO RAMOS', 'FATIMA MONICA', 'RONERO RAMOS FATIMA MONICA', '0908510316', NULL, '', 'DAULE', 'PEDRO ISAIAS', '0981374999', NULL, 'ABUELA'),
(409, 499, 'Navarrete', 'Karina', 'Navarrete Karina', '0926708579', NULL, '', 'Daule', 'Domingo Can.Real.MZ.159', '0926708579', NULL, 'Mamá'),
(410, 500, 'BARZOLA PLUAS', 'CECILIA MARTHA', 'BARZOLA PLUAS CECILIA MARTHA', '0926980186', NULL, '', 'DAULE', 'RIBERAS OPUESTAS: LAS PLAYITAS', '0969258438', NULL, 'MAMÁ'),
(411, 501, 'BARZOLA PLUAS', 'CECILIA MARTHA', 'BARZOLA PLUAS CECILIA MARTHA', '0926980186', NULL, '', 'DAULE', 'RIBERAS OPUESTAS: LAS PLAYITAS', '0969258438', NULL, 'MAMÁ'),
(412, 502, 'RUGEL DELGADO', 'ARIANA JESUS', 'RUGEL DELGADO ARIANA JESUS', '0923249527', NULL, '', 'DAULE', 'ATRAS DE LA IGLESIA SAN FRANCISCO', '0969786588', NULL, 'MAMÁ'),
(413, 503, 'RODRIGUEZ BANCHON', 'YURI GEOMAIRA', 'RODRIGUEZ BANCHON YURI GEOMAIRA', '0927322933', NULL, '', 'DAULE', 'ASSAD BUCARAM', '0994746035', '', 'MAMÁ'),
(414, 504, 'Quinto Jiménez', 'Angela Hayde', 'Quinto Jiménez Angela Hayde', '091810533-', NULL, '', 'El Recuerdo', 'Olmedo y  General  Rumiñahui', '0985987323', NULL, 'mamá'),
(415, 505, 'CALEÑO ALMEIDA', 'JULIA ANA', 'CALEÑO ALMEIDA JULIA ANA', '0925944894', NULL, '', 'Daule', 'Recinto colorado', '0959127504', NULL, 'Madre'),
(416, 506, 'ORTIZ VECILLA', 'DAYSE JACQUELINE', 'ORTIZ VECILLA DAYSE JACQUELINE', '1206532309', NULL, 'eljeyckell', 'Daule', 'Recinto Flor de Maria', '0969492197', NULL, 'Tia'),
(417, 507, 'DIAZ URBINA', 'JENNY DEL VALLE', 'DIAZ URBINA JENNY DEL VALLE', '11836390', NULL, 'jennydiaz', 'Daule', 'Señor de los Milagros y Gallegos LA', '0985424593', NULL, 'Madre'),
(418, 509, 'GOMEZ ALCIVAR', 'CECILIA MARICELA', 'GOMEZ ALCIVAR CECILIA MARICELA', '0917173403', NULL, 'ceciliaalc', 'Daule', 'Riberas Opuestas', '0980956160', NULL, 'Madre'),
(419, 510, 'TORRES QUINTO', 'MARITZA MILAGROS', 'TORRES QUINTO MARITZA MILAGROS', '0941460255', NULL, '', 'Daule', 'Rct: San Gabriel', '0959682427', NULL, 'Tia'),
(420, 511, 'BUENO ALMEA', 'GEORGINA GRICELDA', 'BUENO ALMEA GEORGINA GRICELDA', '0923871404', NULL, '', 'Daule', 'Sector Los Alamos', '0990276239', NULL, 'Madre'),
(421, 508, 'Sanchez Navarrete', 'Jessica Elizabeth', 'Sanchez Navarrete Jessica Elizabeth', '0919113845', NULL, '', 'Daule', 'Cdla. Yolita Leonidad proaño entre Gabriel Garcia M. y Luis U.', '0988363273', NULL, 'Mama'),
(422, 512, 'MOREIRA ECHEVERRIA', 'JOAN STEFANIA', 'MOREIRA ECHEVERRIA JOAN STEFANIA', '0994338537', NULL, 'moreiraste', 'Daule', 'Av. Piedrahita Mz. 486', '0994338537', NULL, 'Madre'),
(423, 513, 'RUIZ PAREDES', 'GISSELA ESTHER', 'RUIZ PAREDES GISSELA ESTHER', '0926709338', NULL, '', 'Daule', 'Victor Manuel Rendón', '0959282377', NULL, 'Madre'),
(424, 514, 'CHIRIGUAYA MOLINA', 'NANCY DEL ROCIO', 'CHIRIGUAYA MOLINA NANCY DEL ROCIO', '0920979697', NULL, 'chiriguaya', 'Daule', 'Justo Torres y Assad Bucaram', '0985960761', NULL, 'Madre'),
(425, 515, 'CASTRO CALENDARIO', 'MARIA CECILIA', 'CASTRO CALENDARIO MARIA CECILIA', '0916497357', NULL, '', 'Daule', 'Via Daule- Nobol( Magro)', '0959926110', NULL, 'Madre'),
(426, 516, 'VILLAMAR TORRES', 'JUANA GUSTINA', 'VILLAMAR TORRES JUANA GUSTINA', '0927973420', NULL, '', 'Daule', 'La Clemencia', '0988357736', NULL, 'Madre'),
(427, 517, 'LEON DELGADO', 'PATRICIA PAULINA', 'LEON DELGADO PATRICIA PAULINA', '0921248357', NULL, '', 'Daule', '', '-', NULL, 'Madre'),
(428, 518, 'PEÑAFIEL BONILLA', 'LADY', 'PEÑAFIEL BONILLA LADY', '0926834219', NULL, '', 'Daule', '', '0939384954', '', 'Madre'),
(429, 519, 'VEINTIMILLA BAJAÑA', 'LEYTON JAVIER', 'VEINTIMILLA BAJAÑA LEYTON JAVIER', '0921944021', NULL, '', 'Daule', '', '-', NULL, 'Padre'),
(430, 531, 'Cacao Cercado', 'Jacinta Fabiola', 'Cacao Cercado Jacinta Fabiola', '0918316506', NULL, '', 'Daule', 'Francisco huerta rendon y Vicente pino moran', '0991283065', NULL, 'Mama'),
(431, 532, 'Perez Galarza', 'Marian de las Mercedes', 'Perez Galarza Marian de las Mercedes', '0920031564', NULL, '', 'Daule', 'KM. 14 Via Daule frente a vulcanizadora Bemba', '0994493573', NULL, 'Mama'),
(432, 533, 'Murillo Cedeño', 'Andrea Valeria', 'Murillo Cedeño Andrea Valeria', '0920752292', NULL, '', 'Daule', 'Calle piedrahita MZ25 SL23 D diagonal al cementerio general', '0988805555', NULL, 'Mama'),
(433, 534, 'Saldarriaga Ponce', 'Maria Florentina', 'Saldarriaga Ponce Maria Florentina', '0917187973', NULL, '', 'Belèn', 'Av. San  Francisco y  García Moreno', '0981346692', NULL, 'mamá'),
(434, 535, 'Merelo Holguín', 'Carmen Marlene', 'Merelo Holguín Carmen Marlene', '0921312815', NULL, '', 'El Mate. Sta. Lucia', 'Vía Sta. Clara  Barrio Paraíso', '0968535105', NULL, 'mamá'),
(435, 536, 'Veloz Camejo', 'Consuelo Isabel', 'Veloz Camejo Consuelo Isabel', '0921201224', NULL, '', 'Cocal', 'La T vía Salitre', '0980333208', NULL, 'mamá'),
(436, 537, 'Ruiz Pincay', 'Julio Efraín', 'Ruiz Pincay Julio Efraín', '0909530396', NULL, '', 'San José', 'Homero Espinoza Rendón  y  Miguel Letamendi', '0986087938', NULL, 'papá'),
(437, 538, 'Guaranda Méndez', 'Virginia Pilar', 'Guaranda Méndez Virginia Pilar', '0921696795', NULL, '', 'Brisas del Daule', 'Rcto.Brisas Del Daule Via A Naupe', '0993874301', NULL, 'mamá'),
(438, 539, 'Reliche Reyes', 'Mariela Katherine', 'Reliche Reyes Mariela Katherine', '0920620069', NULL, '', 'El Laurel', 'Sector 2. Arcadia Espinoza', '0982846979', NULL, 'mamá'),
(439, 540, 'Mora Quinto', 'Vicenta Glenda', 'Mora Quinto Vicenta Glenda', '0916742893', NULL, '', 'San Francisco', 'José Domingo Elizalde', '0939175147', NULL, 'mamá'),
(440, 541, 'Salazar León', 'Alba Lorena', 'Salazar León Alba Lorena', '0919460311', NULL, '', 'Sta. Clara .Independecia', 'Piedrahita y Pedro Carbo Mz 4 SL 24', '0960247115', NULL, 'mamá'),
(441, 543, 'CAMPUZANO YAGUAL', 'DAVID BERNARDO', 'CAMPUZANO YAGUAL DAVID BERNARDO', '0916104797', NULL, '', '10 DE AGOSTO', 'DAULE', '0967728336', NULL, 'PAPA'),
(442, 544, 'Gomez Murillo', 'Maria Isabel', 'Gomez Murillo Maria Isabel', '0921249165', NULL, '', 'Daule', 'Recinto Pajonal por la capilla Sagrado corazon de Jesus y Maria', '0939688284', NULL, 'Mama'),
(443, 545, 'Bravo Chuqui', 'Andrea Elizabeth', 'Bravo Chuqui Andrea Elizabeth', '1250102637', NULL, '', 'Daule', 'Km 27 via Daule sector puente lucia Coop. Los pinos', '0967855075', NULL, 'Hermana'),
(444, 546, 'Mota Salavarria', 'Eudosia de los Santos', 'Mota Salavarria Eudosia de los Santos', '0912955499', NULL, '', 'Daule', 'Leonidas proaños y homero espinoza MZ88 SL18', '0986386830', '', 'Mama'),
(445, 547, 'Bajaña Piguave', 'Kleber Andres', 'Bajaña Piguave Kleber Andres', '0921334074', NULL, '', 'Daule', 'Calle santa lucia y justo torres', '0994027163', NULL, 'Padre'),
(446, 548, 'Banchon Espinoza', 'Lissette Nataly', 'Banchon Espinoza Lissette Nataly', '0925342503', NULL, '', 'Daule', 'Cdla. Enrique Gil Gilbert calle bolivar y Gnral. Crispin cerezo', '0997891707', NULL, 'Mama'),
(447, 549, 'Chica Soto', 'Wendy Carolina', 'Chica Soto Wendy Carolina', '0916243348', NULL, '', 'Daule', 'Km 27 via Daule puente lucia Coop. Los pinos', '0985867317', NULL, 'Mama'),
(448, 550, 'Leon Brionez', 'Rosa Elvira', 'Leon Brionez Rosa Elvira', '0920842184', NULL, '', 'Daule', 'Ernesto Castro y Santa lucia atras del gran Aki', '0989448531', NULL, 'Mama'),
(449, 553, 'MARTILLO', 'MARIANA', 'MARTILLO MARIANA', '0922788674', NULL, '', '', '', '099783017', NULL, 'MAMA'),
(450, 554, 'GARCIA SANCHEZ', 'GLORIA MARIA', 'GARCIA SANCHEZ GLORIA MARIA', '0924816051', NULL, '', 'GALO PLAZA Y 26 DE NOVIEMBRE', 'GALO PLAZA Y 26 DE NOVIEMBRE', '0981555015', NULL, 'MAMA'),
(451, 555, 'GARCIA SANCHEZ', 'GLORIA MARIA', 'GARCIA SANCHEZ GLORIA MARIA', '0924816051', NULL, '', 'DAULE', 'GALO PLAZA Y 26 DE NOVIEMBRE', '0981555015', NULL, 'MAMA'),
(452, 557, 'LINO DE FRANCO', 'LORENA', 'LINO DE FRANCO LORENA', '0922392824', NULL, '', 'DAULE', 'DAULE', '0968881837', NULL, 'MAMA'),
(453, 558, 'RUIZ FRANCO', 'YESSICA ALEXANDRA', 'RUIZ FRANCO YESSICA ALEXANDRA', '928532399', NULL, 'jessiruizf', 'DAULE', 'AV. SAN FRANCISCO CALLE HOMERO ESPINOZA', '969293911', NULL, 'MAMA'),
(454, 559, 'QUIROZ', 'WENDY', 'QUIROZ WENDY', '0929995314', NULL, '', 'DAULE', 'DAULE', '099025887', NULL, 'MAMA'),
(455, 560, 'QUIJIJE MORAN', 'GUISELLA STEFANIA', 'QUIJIJE MORAN GUISELLA STEFANIA', '0923202758', NULL, 'stefaniaqu', 'DAULE', 'AV.JOAQUIN GALLEGOS LARA', '0984742095', NULL, 'MAMA'),
(456, 561, 'SALAZAR BRIONES', 'CINDY TATIANA', 'SALAZAR BRIONES CINDY TATIANA', '', NULL, '', 'SABANILLA PEDRO CARBO', '10 DE AGOSTO Y CALLEJON SIN NOMBRE', '0991600251', NULL, 'MAMA'),
(457, 562, 'CHILA BORJA', 'MARIA ISABEL', 'CHILA BORJA MARIA ISABEL', '1204122996', NULL, 'yostinchil', 'GUAYAQUIL TARQUI', 'KM23 VIA DAULE LAGO DE CAPEIRA', '0990716256', NULL, 'MAMA'),
(458, 563, 'RUIZ HERRERA', 'BRENDA HIRALDA', 'RUIZ HERRERA BRENDA HIRALDA', '9238771138', NULL, 'Josué.huac', 'LOS LOJAS LA ESTACADA', 'KM23 VIA DAULE', '9855606384', NULL, 'MAMA'),
(459, 564, 'MOREIRA', 'SANTA', 'MOREIRA SANTA', '1309966818', NULL, '', '', '', '0982643547', NULL, 'MAMA'),
(460, 565, 'JIMENEZ LOPEZ', 'VICTOR', 'JIMENEZ LOPEZ VICTOR', '0920628039', NULL, '', 'LOMAS DE SARGENTILLO', 'CIUDADELA 1RO DE MAYO', '0993354014', NULL, 'PAPA'),
(461, 566, 'LEON CHAVES', 'N.N', 'LEON CHAVES N.N', '', NULL, '', '', '', '00', NULL, 'MAMA'),
(462, 567, 'FREIRE BAJAÑA', 'DIANA KATHERINE', 'FREIRE BAJAÑA DIANA KATHERINE', '', NULL, 'freirebaja', 'DAULE', 'CDLA. BELEN   AVN  SAN FRANCISCO 26 DE NOVIEMBRE', '959751685', NULL, 'MAMA'),
(463, 568, 'ROMERO CEDILLO', 'MARCELINA', 'ROMERO CEDILLO MARCELINA', '', NULL, '', 'PUENTE LUCIA', 'KM 27 VIA DAULE', '969990236', NULL, 'MAMA'),
(464, 569, 'CORREA HOLGUIN', 'ERIKA GABRIELA', 'CORREA HOLGUIN ERIKA GABRIELA', '942221698', NULL, 'joelalexan', 'LOS RIOS-VINCES', 'PALESTINA VIA SAN  JACINTO', '967546443', NULL, 'MAMA'),
(465, 570, 'VERA ROSADO', 'ANA ROCIO', 'VERA ROSADO ANA ROCIO', '917178386', NULL, '', 'RIO PERDIDO', 'DAULE', '961070334', NULL, 'MAMA'),
(466, 571, 'ZAMBRANO SUAREZ', 'MARIA TERESA', 'ZAMBRANO SUAREZ MARIA TERESA', '925171464', NULL, '', 'LA ESQUINA DEL SABOR DAULE', 'AV. SAN FRANCISCO', '997181236', NULL, 'MAMA'),
(467, 572, 'MONTOYA', 'MARIANO', 'MONTOYA MARIANO', '', NULL, '', 'DAULE', '', '0968900561', NULL, 'PAPA'),
(468, 573, 'MAGALLANES', 'MARISOL', 'MAGALLANES MARISOL', '', NULL, '', '', '', '0968907415', NULL, 'MAMA'),
(469, 574, 'SEGURA MORAN', 'ZOILA', 'SEGURA MORAN ZOILA', '0919944892', NULL, '', '', '', '0963747286', NULL, 'MAMA'),
(470, 590, 'Moran Veliz', 'Johnny Dionicio', 'Moran Veliz Johnny Dionicio', '0920035011', NULL, '', 'Recinto la victoria', 'Recinto la victoria', '0960119208', NULL, 'padre'),
(471, 580, 'Basurto figueroa', 'Aidee Maria', 'Basurto figueroa Aidee Maria', '0917908287', NULL, '', 'La vuelta', 'La vuelta', '0997896011', NULL, 'MAMA'),
(472, 595, 'Alvarado Burgos', 'Sonia María', 'Alvarado Burgos Sonia María', '', NULL, '', '', 'Huanchichal', '0990770223', NULL, 'madre'),
(473, 596, 'Pino Zambrano', 'Maria Eugenia', 'Pino Zambrano Maria Eugenia', '0924730120', NULL, '', 'DAULE', 'Cdla.Asad Buscarán..calle Jose Maria egas', '0990090437', NULL, 'MADRE'),
(474, 597, 'Herrera Morales', 'Diana Nathali', 'Herrera Morales Diana Nathali', '', NULL, '', '', 'Bolívar y la segunda', '0961157026', NULL, 'MADRE'),
(475, 598, 'Herrera Morales', ': Diana Nathali', 'Herrera Morales : Diana Nathali', '', NULL, '', '', 'Bolivar y la segunda', '0961157026', NULL, 'MADRE'),
(476, 599, 'Mejia Chiriguaya', 'Cinthia Nataly', 'Mejia Chiriguaya Cinthia Nataly', '', NULL, '', 'l', 'los  alamos', '0982554233', '', 'madre'),
(477, 600, 'Chávez Castro', 'Erwin Rodolfo', 'Chávez Castro Erwin Rodolfo', '', NULL, '', 'Daule', 'Perimetral y pedro pantaleon', '0980119484', NULL, 'padre'),
(478, 602, 'Mora Mindiolaza', 'Maribel del Rocio', 'Mora Mindiolaza Maribel del Rocio', '0921695524', NULL, '', 'Km 26 via a Daule', '', '0959967997', NULL, 'Madre'),
(479, 603, 'Espinoza Bajaña', 'Genessi Francisca', 'Espinoza Bajaña Genessi Francisca', '0928675412', NULL, '', 'Daule', 'Ciudadela \"Assad BUCARAN\" (Daule)', '0992258546', NULL, 'Madre'),
(480, 601, 'kdsnmflKnsdLKnfSKNdfnksnkv,v', 'Maria Jose', 'kdsnmflKnsdLKnfSKNdfnksnkv,v Maria Jose', '0992309238', NULL, 'elipita@pr', 'dsfasdffsagdfg', '.sadn.fnsdf.knsd.fkn,.dknbs,.', '654923105', NULL, 'mama'),
(481, 585, 'Magallanez Avilés', 'Elizabeth del Rocío', 'Magallanez Avilés Elizabeth del Rocío', '0913780201', NULL, '', 'Rocafuerte y la tercera tribu ch', 'Rocafuerte y la tercera tribu chonana', '0993057465', '', 'Madre'),
(482, 592, 'lamilla medina', 'Mayra juliana', 'lamilla medina Mayra juliana', '0921526539', NULL, '', 'Banife', 'Martha bucaram', '0963914676', NULL, 'Madre'),
(483, 587, 'Estrella Mantuano', 'Martha Domitila', 'Estrella Mantuano Martha Domitila', '0926985300', NULL, '', 'Lomas de Sargentillo', 'Sector San Lorenzo', '0998326398', NULL, 'Madre'),
(484, 578, 'Alvarado cantos', 'Graciela maria', 'Alvarado cantos Graciela maria', '0920495231', NULL, '', 'Ciudadela assad bucaram', 'Manuel villavicencio y callejon mz 143 sl 7', '0986888116', NULL, 'MADRE'),
(485, 594, 'Quijije Chipre', 'Raquel Yaneth', 'Quijije Chipre Raquel Yaneth', '0917134579', NULL, '', 'Sector Plan América', 'Al frente de la Hacienda San Isidro', '0993128027', NULL, 'Madre'),
(486, 581, 'Quinto Franco', 'Lucia Eliza', 'Quinto Franco Lucia Eliza', '0920886033', NULL, '', 'Yolita', 'Narcisa de  jesus y Gracia Moreno', '096134118', NULL, 'Madre'),
(487, 584, 'Gavilánes Tomala', 'Lissette Geomayra', 'Gavilánes Tomala Lissette Geomayra', '0926700758', NULL, '', 'Recinto Peninsula de Animas', 'Vía Daule Santa Lucia', '0997720568', NULL, 'Mamá'),
(488, 582, 'Bonilla bonilla', 'Maria Elvira', 'Bonilla bonilla Maria Elvira', '0920651056', NULL, '', 'Recinto brisas del daule', 'Recinto brisas del daule', '0939352124', NULL, 'Madre'),
(489, 588, 'Salavarria Cortez', 'Ligia Marcela', 'Salavarria Cortez Ligia Marcela', '0922275318', NULL, '', 'Recinto los Ángeles', 'Recinto los Ángeles', '0999129903', '', 'Mamá'),
(490, 579, 'López Cereso', 'Gina patricia', 'López Cereso Gina patricia', '0918199563', NULL, '', '9 de octubre y piedrahita', '9 de octubre y piedrahita', '0939875307', NULL, 'MAMAM'),
(491, 589, 'Moncayo chiriguaya', 'Johanna Elizabeth', 'Moncayo chiriguaya Johanna Elizabeth', '0921966214', NULL, '', 'Hipólito camba y manuel de faz', 'Hipólito camba y manuel de faz', '0986662328', NULL, 'Madre'),
(492, 575, 'Bajaña Bajaña', 'Fabiola erica', 'Bajaña Bajaña Fabiola erica', '0925945636', NULL, '', 'Coop nueva victoria', 'Km26 vía daule', '0939432223', NULL, 'Madre'),
(493, 552, 'Sánchez Quinde', 'Sandy Belén', 'Sánchez Quinde Sandy Belén', '0926833120', NULL, '', 'Nobol', 'Gregorio conforme y los tamarindos', '0926833120', NULL, 'Madre'),
(494, 576, 'Muñiz Sanchez', 'Jenny Margarita', 'Muñiz Sanchez Jenny Margarita', '926562430', NULL, '', 'NORTE', 'Km 16 via a Daule, coop San Francisco Mz 756, SL 4.', '985817077', NULL, 'Madre'),
(495, 586, 'BENALCAZAR MARTILLO', 'JESSICA PAOLA', 'BENALCAZAR MARTILLO JESSICA PAOLA', '0920946043', NULL, '', 'CIUDADELA MARIANITA 1', 'BARRIO BANIFE', '0991748725', NULL, 'Madre'),
(496, 591, 'Méndez Naranjo', 'Sandy del Rocío', 'Méndez Naranjo Sandy del Rocío', '0927670505', NULL, '', 'Daule', 'Recinto tintal de animas', '0963970107', NULL, 'Madre'),
(497, 593, 'Morante Briones', 'Santa Stefania', 'Morante Briones Santa Stefania', '1207730092', NULL, '', 'Las maravillas', 'Las maravillas', '0986728753', NULL, 'HERMANA'),
(498, 604, 'Ronquillo Quinto', 'Mario Frankiln', 'Ronquillo Quinto Mario Frankiln', '0913233227', NULL, '', 'Juan leon Mera y 9 de Octubre', 'Juan leon Mera y 9 de Octubre', '0984304903', NULL, 'padre'),
(499, 616, 'Salazar Canales', 'Teodora Maria', 'Salazar Canales Teodora Maria', '', NULL, '', 'ANA PAREDES Y PABLO HANNIBAL VEL', '', '0989604177', NULL, 'abuela'),
(500, 617, 'Alvarado Navarrete', 'Alba Soraya', 'Alvarado Navarrete Alba Soraya', '', NULL, '', '', 'Kilómetro 26 vía salitre-aurora', '0988832721', NULL, 'hermana'),
(501, 618, 'Castañeda Torres', 'Mirian', 'Castañeda Torres Mirian', '', NULL, '', '', 'Km 54 via Daule - Santa lucia', '0992387157', NULL, 'madre'),
(502, 619, 'Díaz salas', 'Maritza Isabel', 'Díaz salas Maritza Isabel', '', NULL, '', '', '', '0993921306', NULL, 'madre'),
(503, 620, 'Chavez', 'Ana Marisela', 'Chavez Ana Marisela', '', NULL, '', 'frente a la planta de agua potab', 'florida', '0930162538', NULL, 'madre'),
(504, 621, 'Torres Ruiz', 'Mercy', 'Torres Ruiz Mercy', '', NULL, '', '', 'Abdon Calderon y Victror Rendon', '0990442666', NULL, 'madre'),
(505, 622, 'Bonilla Yagual', 'Lady', 'Bonilla Yagual Lady', '', NULL, '', '', 'Pedro Isaias', '0991447163', NULL, 'madre'),
(506, 623, 'Gavilanes Castillo', 'Katiuska', 'Gavilanes Castillo Katiuska', '', NULL, '', '', 'Recinto Huanchichal', '0939997094', NULL, 'madre'),
(507, 624, 'Reliche Contreras', 'Yazmin Rosalba', 'Reliche Contreras Yazmin Rosalba', '0916744063', NULL, '', '', 'Parr.\"laurel\" rct\" san vicente\"', '0959722983', NULL, 'madre'),
(508, 625, 'SEGURA CASTRO', 'SENA ELENA AURORA', 'SEGURA CASTRO SENA ELENA AURORA', '', NULL, '', '', 'ciudadela Rosa mira calle Bolivar y 14 de septiembre', '0986085151', NULL, 'madre'),
(509, 626, 'Ronquillo Viteri', 'Jeniffer Narcisa', 'Ronquillo Viteri Jeniffer Narcisa', '', NULL, '', '', 'calles Domingo Comin y Manuel González', '0962569866', NULL, 'madre'),
(510, 627, 'ZAMBRANO NAVARRETE', 'LORENA BEATRIZ', 'ZAMBRANO NAVARRETE LORENA BEATRIZ', '', NULL, '', 'petrillo', 'KM30 vía Daule', '0995823501', NULL, 'madre'),
(511, 628, 'Carchichabla Vásquez', 'Luis Florencio', 'Carchichabla Vásquez Luis Florencio', '', NULL, '', '', ': Ezequiel mora y domingo elizalde MZ 49 SL 13', '0960269533', NULL, 'padre'),
(512, 629, 'Vera Bajaña', 'JahairaJesenia', 'Vera Bajaña JahairaJesenia', '', NULL, '', '', '', '593 99 442', NULL, 'madre'),
(513, 630, 'Bajaña Martinez', 'Martha Victoria', 'Bajaña Martinez Martha Victoria', '', NULL, '', '', 'MZ 16 Oʟᴍᴇᴅᴏ Y Aʟғʀᴇᴅᴏ Bᴀǫᴜᴇʀɪᴢᴏ Mᴏʀᴇɴᴏ', '0980594664', NULL, 'madre'),
(514, 631, 'Torres Ruiz', 'Mercy Cumanda', 'Torres Ruiz Mercy Cumanda', '', NULL, '', '', 'Abdon Calderon y Victor Rendon', '0990442666', NULL, 'madre'),
(515, 637, 'MORAN RUIZ', 'MARINA ROCÌO', 'MORAN RUIZ MARINA ROCÌO', '', NULL, '', '', '', '993721457', NULL, 'MAMA'),
(516, 640, 'Castro Leon', 'Grecia', 'Castro Leon Grecia', '', NULL, '', 'Las lojas', 'dos reversa', '0916745219', NULL, 'madre'),
(517, 641, 'Campoverde Reino', 'DANIELA', 'Campoverde Reino DANIELA', '0920545605', NULL, 'danielacam', 'Peaje chiveria x comedor Carmita', 'Km 32 via daule', '0998404861', '', 'MADRE'),
(518, 642, 'Cepeda Nuñez', 'Diana Patricia', 'Cepeda Nuñez Diana Patricia', '0997012550', NULL, '', 'Ciudadela Asac Buscaran por el d', 'Cascol de Bahona', '0997012550', NULL, 'MADRE'),
(519, 643, 'Briones Barcia', 'Karen Alexandra', 'Briones Barcia Karen Alexandra', '0922091772', NULL, 'Karepluasf', '', 'Cdla San José', '0958823613', NULL, 'MADRE'),
(520, 644, 'Jiménez', 'Victor', 'Jiménez Victor', '0920628039', NULL, '', 'Lomas de sargentillo', '', '0993354014', NULL, 'Padre'),
(521, 645, 'Bonilla Tomala', 'Marcia Nataly', 'Bonilla Tomala Marcia Nataly', '0924477193', NULL, 'nataly.bon', '', 'Por la entrada del dispensario san francisco hay un camion blanc', '0996890356', NULL, 'MADRE'),
(522, 646, 'Pillasagua Licoa', 'Edith Sugey', 'Pillasagua Licoa Edith Sugey', '0922242920', NULL, '', 'Prov.de Azoguez y callejo', 'Prov.de Azoguez y callejo', '0993663193', NULL, 'MADRE'),
(523, 5, 'coloma alvarado', 'Melissa Maria', 'coloma alvarado Melissa Maria', '0927559227', NULL, 'Melissacol', '', 'Calle Bolivar by paz', '0984419699', NULL, 'MADRE'),
(524, 647, 'Bajaña Quinto', 'Ana Johana', 'Bajaña Quinto Ana Johana', '0923873285', NULL, '', '', 'PABLO HANNIBAL VELA Y JOSE MARIA EGAS', '0968893818', NULL, 'MADRE'),
(525, 648, 'AVILÉS QUINTO', 'TANNIA RAQUEL', 'AVILÉS QUINTO TANNIA RAQUEL', '0917463820', NULL, '', 'A UNA CUADRA DEL COLEGIO JOSÉ LU', 'LEONIDAS PROAÑO Y HOMERO ESPINOZA', '0959018569', NULL, 'MADRE'),
(526, 649, 'Diaz Ruiz', 'Andreina Madelyne', 'Diaz Ruiz Andreina Madelyne', '0929208809', NULL, '', 'A lado hay un taller mecanico', 'Magro Homero Espinoza', '0986250564', NULL, 'MADRE'),
(527, 650, 'Reyes Coloma', 'Elida', 'Reyes Coloma Elida', '0920083375', NULL, '', '', 'Bolivar y crispin cerezo', '0959195497', NULL, 'MADRE'),
(528, 651, 'Briones', 'Jessica', 'Briones Jessica', '0921361507', NULL, '', 'Zona rural Daule vía Daule -sant', 'Zona rural Daule vía Daule -santalucia', '0968088220', NULL, 'MADRE'),
(529, 652, 'Coloma Castro', 'Jacinta Narcisa', 'Coloma Castro Jacinta Narcisa', '0917315631', NULL, '', 'Atrás del Estadio los Daulis', 'Camilo de Estruje y Antonio Huayamabe Mz 2', '0967269153', NULL, 'MADRE'),
(530, 653, 'Lopez Viera', 'Mercedes del Pilar', 'Lopez Viera Mercedes del Pilar', '1704956810', NULL, '', '', 'Riberaa Opuestas. Frente Iglesia Señor de los Milagros', '0994259148', NULL, 'MADRE'),
(531, 654, 'Mestanza Bravo', 'Jefferson Manuel', 'Mestanza Bravo Jefferson Manuel', '0927348631', NULL, '', 'Por el tutin de Eloy Alfaro', 'Por el tutin de Eloy Alfaro', '0927348631', NULL, 'Hermano'),
(532, 4, 'Amagua Lopez', 'Karina  Alexandra', 'Amagua Lopez Karina  Alexandra', '1719590448', NULL, '', 'Riberaa Opuestas. Frente Iglesia', 'Riberaa Opuestas. Frente Iglesia Señor de los Milagros', '0980634020', NULL, 'Tia'),
(533, 655, 'Rivas Villamar', 'Wendy Carolina', 'Rivas Villamar Wendy Carolina', '0918195769', NULL, '', 'Cdla. Juan Bautista Aguirre', 'Cdla. Juan Bautista Aguirre', '0988272993', NULL, 'MADRE'),
(534, 3, 'Hidalgo Pino', 'Dayan Stefanny', 'Hidalgo Pino Dayan Stefanny', '0924476120', NULL, '', 'Dos cuadras mas adelante del cen', 'Jose felix Heredia y by Pass MZ 151 SL 6', '0967340292', NULL, 'MADRE'),
(535, 656, 'Medina Dumes', 'Francisco Policarpio', 'Medina Dumes Francisco Policarpio', '0943036012', NULL, '', '', 'Calle justo torres y Proaños', '0969751347', NULL, 'Hermano'),
(536, 657, 'González Quinto', 'Erika Claribel', 'González Quinto Erika Claribel', '0927321323', NULL, '', 'A una cuadra de la escuela Hugo', 'García Moreno y Leónidas proaño', '0959219537', NULL, 'MADRE'),
(537, 658, 'Camba Choez', 'Veronica Emperatriz', 'Camba Choez Veronica Emperatriz', '0922546205', NULL, '', 'Ciudadela Assad Bucaram Daule', 'Ciudadela Assad Bucaram Daule', '0982442921', NULL, 'MADRE'),
(538, 659, 'GOMEZ CHACEZ', 'CARMEN DEL ROCIO', 'GOMEZ CHACEZ CARMEN DEL ROCIO', '0925173742', NULL, '', 'Cerca de la IGLESIA SAN JUAN BAU', 'JOSE MARIA VELASCO IBARRA Y CAMILO DE ESTRUJE', '044507634', NULL, 'MADRE'),
(539, 660, 'Suarez San Lucas', 'Jonathan Alfredo', 'Suarez San Lucas Jonathan Alfredo', '0941139016', NULL, '', 'Loma pelada PJ a 3 cuadras', 'Patria nueva Daule', '0939191051', NULL, 'Hermano'),
(540, 661, 'Vera Torres', 'Mery Liliana', 'Vera Torres Mery Liliana', '0920530870', NULL, '', 'Zona Nobol vía Nobol-Petrillo Lo', 'Zona Nobol vía Nobol-Petrillo Lotizacion', '0961097516', NULL, 'MADRE'),
(541, 662, 'Pote Arevalo', 'Margarita Nereyda', 'Pote Arevalo Margarita Nereyda', '0921436309', NULL, 'nereydapot', 'Km 26 via Daule coop Los Angeles', 'Km 26 via Daule coop Los Angeles 1', '0993790311', NULL, 'MADRE'),
(542, 663, 'Marquéz Carrera', 'Lorena Auxiliadora', 'Marquéz Carrera Lorena Auxiliadora', '0922241765', NULL, '', 'Taller Mecánico Automotriz \"Tecn', 'Pablo Anivel Vela - José Peralta (Esquina )', '0979922714', NULL, 'MADRE'),
(543, 664, 'Espinoza Peñafiel', 'Katiusca Johana', 'Espinoza Peñafiel Katiusca Johana', '0921570867', NULL, '', 'Radio cideral', 'Jose Domingo Elizalde/Olmedo Almeida /narcisa jesus', '0939172420', NULL, 'MADRE'),
(544, 665, 'veloz Arriagada', 'Silvia Verónica', 'veloz Arriagada Silvia Verónica', '0917316606', NULL, '', '', 'Leónidas plaza', '0992282224', NULL, 'MADRE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_respuesta`
--

CREATE TABLE `sw_respuesta` (
  `id_respuesta` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `re_texto` text CHARACTER SET latin1 NOT NULL,
  `re_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `re_autor` int(11) NOT NULL,
  `re_perfil` varchar(50) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_cualitativa`
--

CREATE TABLE `sw_rubrica_cualitativa` (
  `id_paralelo` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `rc_calificacion` varchar(3) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_docente`
--

CREATE TABLE `sw_rubrica_docente` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_paralelo` int(11) NOT NULL,
  `rd_nombre` varchar(64) CHARACTER SET latin1 NOT NULL,
  `rd_descripcion` varchar(256) CHARACTER SET latin1 NOT NULL,
  `rd_fecha_envio` date NOT NULL,
  `rd_fecha_revision` date NOT NULL,
  `rd_observacion` varchar(128) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_rubrica_estudiante`
--

INSERT INTO `sw_rubrica_estudiante` (`id_rubrica_estudiante`, `id_estudiante`, `id_paralelo`, `id_asignatura`, `id_rubrica_personalizada`, `re_calificacion`) VALUES
(1, 2, 26, 5, 1, 0),
(2, 602, 26, 5, 1, 0),
(3, 603, 26, 5, 1, 0),
(4, 20, 27, 5, 1, 0),
(5, 20, 27, 5, 10, 0),
(6, 20, 27, 5, 2, 0),
(7, 20, 27, 5, 3, 0),
(8, 22, 27, 5, 3, 0),
(9, 26, 27, 5, 3, 0),
(10, 2, 26, 5, 3, 0),
(11, 602, 26, 5, 3, 0),
(12, 603, 26, 5, 3, 0),
(13, 22, 27, 5, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_rubrica_evaluacion`
--

CREATE TABLE `sw_rubrica_evaluacion` (
  `id_rubrica_evaluacion` int(11) NOT NULL,
  `id_aporte_evaluacion` int(11) NOT NULL,
  `id_tipo_rubrica` int(11) NOT NULL,
  `id_tipo_asignatura` int(11) NOT NULL DEFAULT '1',
  `ru_nombre` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `ru_abreviatura` varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_rubrica_evaluacion`
--

INSERT INTO `sw_rubrica_evaluacion` (`id_rubrica_evaluacion`, `id_aporte_evaluacion`, `id_tipo_rubrica`, `id_tipo_asignatura`, `ru_nombre`, `ru_abreviatura`) VALUES
(1, 1, 1, 1, 'PORTAFOLIO', 'P1Q1'),
(2, 2, 1, 1, 'NOTA', 'P2Q1'),
(3, 3, 1, 1, 'EXAMEN QUIMESTRAL', 'EX.Q1'),
(4, 4, 1, 1, 'NOTA', 'P1Q2'),
(5, 5, 1, 1, 'PORTAFOLIO', 'P2Q2'),
(6, 6, 1, 1, 'EXAMEN QUIMESTRAL', 'EX.Q2'),
(7, 7, 1, 1, 'SUPLETORIO', 'SUP.'),
(8, 8, 1, 1, 'REMEDIAL', 'REM.'),
(9, 9, 1, 1, 'EXAMEN DE GRACIA', 'GRA.'),
(10, 1, 1, 1, 'PROYECTO', 'P1Q1'),
(11, 5, 1, 1, 'PROYECTO CIENTIFICO', 'P2Q2'),
(12, 5, 1, 1, 'PROYECTO HUMANISTICO', 'P2Q2');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tarea`
--

CREATE TABLE `sw_tarea` (
  `id` int(11) NOT NULL,
  `tarea` varchar(255) CHARACTER SET latin1 NOT NULL,
  `hecho` tinyint(1) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tema`
--

CREATE TABLE `sw_tema` (
  `id_tema` int(11) NOT NULL,
  `id_foro` int(11) NOT NULL,
  `te_titulo` varchar(50) CHARACTER SET latin1 NOT NULL,
  `te_descripcion` text CHARACTER SET latin1 NOT NULL,
  `te_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_aporte`
--

CREATE TABLE `sw_tipo_aporte` (
  `id_tipo_aporte` int(11) NOT NULL,
  `ta_descripcion` varchar(45) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_tipo_aporte`
--

INSERT INTO `sw_tipo_aporte` (`id_tipo_aporte`, `ta_descripcion`) VALUES
(1, 'PARCIAL'),
(2, 'EXAMEN QUIMESTRAL'),
(3, 'SUPLETORIO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_asignatura`
--

CREATE TABLE `sw_tipo_asignatura` (
  `id_tipo_asignatura` int(11) NOT NULL,
  `ta_descripcion` varchar(64) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `te_orden` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_tipo_educacion`
--

INSERT INTO `sw_tipo_educacion` (`id_tipo_educacion`, `id_periodo_lectivo`, `te_nombre`, `te_bachillerato`, `te_orden`) VALUES
(1, 1, 'Educación General Básica Elemental', 0, 2),
(2, 1, 'Educación General Básica Media', 0, 3),
(3, 1, 'Educación General Básica Superior', 0, 4),
(4, 1, 'Bachillerato Técnico', 1, 5),
(5, 1, 'Bachillerato General Unificado', 1, 6),
(6, 1, 'Educación General Básica Preparatoria', 1, 7),
(7, 1, 'Educación Inicial', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_tipo_periodo`
--

CREATE TABLE `sw_tipo_periodo` (
  `id_tipo_periodo` int(11) NOT NULL,
  `tp_descripcion` varchar(45) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
-- Estructura de tabla para la tabla `sw_tipo_rubrica`
--

CREATE TABLE `sw_tipo_rubrica` (
  `id_tipo_rubrica` int(11) NOT NULL,
  `tr_descripcion` varchar(16) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_tipo_rubrica`
--

INSERT INTO `sw_tipo_rubrica` (`id_tipo_rubrica`, `tr_descripcion`) VALUES
(1, 'OBLIGATORIO'),
(2, 'OPCIONAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario`
--

CREATE TABLE `sw_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `us_titulo` varchar(5) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_apellidos` varchar(32) COLLATE utf8_spanish2_ci NOT NULL,
  `us_nombres` varchar(32) COLLATE utf8_spanish2_ci NOT NULL,
  `us_shortname` varchar(45) CHARACTER SET latin1 NOT NULL,
  `us_fullname` varchar(64) COLLATE utf8_spanish2_ci NOT NULL,
  `us_login` varchar(24) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_password` varchar(64) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `us_foto` varchar(100) CHARACTER SET latin1 NOT NULL,
  `us_genero` varchar(1) CHARACTER SET latin1 NOT NULL,
  `us_activo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_usuario`
--

INSERT INTO `sw_usuario` (`id_usuario`, `id_periodo_lectivo`, `id_perfil`, `us_titulo`, `us_apellidos`, `us_nombres`, `us_shortname`, `us_fullname`, `us_login`, `us_password`, `us_foto`, `us_genero`, `us_activo`) VALUES
(1, 1, 1, 'Ing.', 'Peñaherrera Escobar', 'Gonzalo Nicolás', 'Ing. Gonzalo Peñaherrera', 'Peñaherrera Escobar Gonzalo Nicolás', 'ADMINISTRADOR', 'WtiObwUU0MFeLD0Z7UyJWCEUQBMoG1iPknS6i3HF6cs=', '1448127258.png', 'M', 1),
(2, 1, 2, 'Lic.', 'Varas Bajaña', 'Eladio Jesús', 'Lic. Eladio Varas', 'Varas Bajaña Eladio Jesús', 'eladiov', 'Nvhkbf22b53QLBj2aW2mxkKiMWSE7H+aRlQvb9YqC8M=', '1680449617.png', 'M', 1),
(3, 1, 2, 'Ing.', 'Tumbaco Ortiz', 'Grace Kelly', 'Ing. Grace Tumbaco', 'Tumbaco Ortiz Grace Kelly', 'gracet', 'tzPTgv7VtxBBzZxo/o3hwjDDDo8Ft1yNYf5xR9GgdjY=', '915738194.png', 'F', 1),
(4, 1, 2, 'Lic.', 'Torres Calderón', 'Katherine Isabel', 'Lic. Katherine Torres', 'Torres Calderón Katherine Isabel', 'katherinet', 'FeY9rpVtMTgHUnJVOrbI6gR4fQQHdaLHyc9trTGqVxY=', '761133941.png', 'F', 1),
(5, 1, 2, 'Lic.', 'Tomalá Alvarado', 'Alisson Daniela', 'Lic. Alisson Tomalá', 'Tomalá Alvarado Alisson Daniela', 'alissont', 'kb1+CSEUy0tImz2OBYlyFMGov584VnVpJJadrDRCPbw=', '648065916.png', 'F', 1),
(6, 1, 2, 'Sr.', 'Sánchez Véliz', 'Ramito Catalino', 'Sr. Ramito Sánchez', 'Sánchez Véliz Ramito Catalino', 'ramitos', 'R3hGbuqhEasXboEpgKaSNzYwaegwy6GG8arBdjSklmg=', '345145106.png', 'M', 1),
(7, 1, 2, 'CPA', 'Sánchez Peña', 'Juan Carlos', 'CPA Juan Sánchez', 'Sánchez Peña Juan Carlos', 'juans', 'xul2KxZIp2VEzMbqH5b0jnk597x8d2a9k0/Bfy7lsIc=', '1508659996.png', 'M', 1),
(8, 1, 2, 'Lic.', 'Sánchez Chávez', 'Yanina Mabel', 'Lic. Yanina Sánchez', 'Sánchez Chávez Yanina Mabel', 'yaninas', 'qmWtLGFs9iyWV3MQBRU2q0VxATO6WAGmHhFder9kVgw=', '118372275.png', 'F', 1),
(9, 1, 2, 'Lic.', 'Rugel Martínez', 'Mayiya Judith ', 'Lic. Mayiya Rugel', 'Rugel Martínez Mayiya Judith ', 'mayiyar', 'Sg+I7uxpWMHz5pvw7YvITEtEZW3Q/gYyuTWwbrjeGXQ=', '373076890.png', 'F', 1),
(10, 1, 2, 'Lic.', 'Ronquillo  Camba', 'Kenia Verónica', 'Lic. Kenia Ronquillo', 'Ronquillo  Camba Kenia Verónica', 'keniar', 'dRjILmKPth7Ao/tnAusFZq9uzAybJFMt3SM6JYBT3js=', '123669596.png', 'F', 1),
(11, 1, 2, 'Sr.', 'Ramos León', 'Galo Enrique', 'Sr. Galo Ramos', 'Ramos León Galo Enrique', 'galor', 'FMIOu/PjfmZKIkiDWI/+x55L0QQdNHUXAkU1wzZQPx0=', '2050311281.png', 'M', 1),
(12, 1, 2, 'Srta.', 'Ramírez Sánchez', 'Esther María', 'Srta. Esther Ramírez', 'Ramírez Sánchez Esther María', 'estherr', 'vhooDgggEhS/mfz6VxCPwtWS5Y1GWqgN2fOkh3+34wg=', '1775742127.png', 'F', 1),
(13, 1, 2, 'Lic.', 'Quinto Villamar', 'Karen Anabel', 'Lic. Karen Quinto', 'Quinto Villamar Karen Anabel', 'karenq', 'V7ga2mH7B5nfqh5hS0PeIqoB0ccfvveUiGM8Bbjptdc=', '1878123615.png', 'F', 1),
(14, 1, 2, 'Lic.', 'Pozo Arreaga', 'Gladys María', 'Lic. Gladys Pozo', 'Pozo Arreaga Gladys María', 'gladysp', 'jjOuCC9OTXQOJJ1Nlei9HNJ5ltrZOlb/v9079wMsMSY=', '1010337073.png', 'F', 1),
(15, 1, 2, 'Lic.', 'Plúas Aanchundia', 'Reyes Elizabeth', 'Lic. Reyes Plúas', 'Plúas Aanchundia Reyes Elizabeth', 'elizabethp', 'G8FLqd4hIRPrw2wEGStoPG4o0UQ14cnKg6VGe60ISfo=', '959135517.png', 'F', 1),
(16, 1, 2, 'Lic.', 'Piza Sandoval ', 'Félix Gilberto ', 'Lic. Félix Piza', 'Piza Sandoval  Félix Gilberto ', 'felixp', 'fJfvgEiJSHJK8TwHkzO/JcqqpwJFx4WkvZsE1y0Oimg=', '1943082093.png', 'M', 1),
(17, 1, 2, 'Lic.', 'Pinela Mestanza', 'Dévora Faviola', 'Lic. Dévora Pinela', 'Pinela Mestanza Dévora Faviola', 'devorap', 'ZPXUGX3RfbCGaa6ILhJgdpRvfCMg96H9x+GuvDLtYrk=', '336221529.png', 'F', 1),
(18, 1, 2, 'Lic.', 'Peñafiel Osorio', 'Vilma Rosario', 'Lic. Vilma Peñafiel', 'Peñafiel Osorio Vilma Rosario', 'vilmap', 'y+EKg0jJ2BadcUkJFVBSPUJ02Pg3pXl1F+s76fPEc/U=', '188556793.png', 'F', 1),
(19, 1, 2, 'Lic.', 'París Moreno Reyes', 'Mery Patricia', 'Lic. Mery París', 'París Moreno Reyes Mery Patricia', 'meryp', '65cL5ssoOllZe+EN9kjvmYP+HS6v4/FwwYZxaiYGsaU=', '1263003954.png', 'F', 1),
(20, 1, 2, 'Lic.', 'Ortega Mejía', 'Mélida Judith', 'Lic. Mélida Ortega', 'Ortega Mejía Mélida Judith', 'melidao', 'idROCL7QLS4ykjgvQ/bjI3hIGRBMEczCy3MASbm4Av8=', '947369378.png', 'F', 1),
(21, 1, 2, 'Lic.', 'Nieto Navarrete', 'José Benedicto', 'Lic. José Nieto', 'Nieto Navarrete José Benedicto', 'josen', '+Xyyr2zIz7stK1v/OCiLcXExe7wlYj3VrbVsmv8/msg=', '847777213.png', 'M', 1),
(22, 1, 2, 'Lcda.', 'Carlosama Ortega ', 'Narcisa De Jesús', 'Lcda. Narcisa Carlosama', 'Carlosama Ortega  Narcisa De Jesús', 'narcisac', 'KcN7KE0mdAN7M3skwFYq0D+rvcKyaH+FjibMBzhQboI=', '2021411820.png', 'F', 1),
(23, 1, 2, 'Prof.', 'Navarrete Buste', 'Johanna Mariela ', 'Prof. Johanna Navarrete', 'Navarrete Buste Johanna Mariela ', 'johannan', 'nZrVrwZ0diDXs6aaKeVyL0jsLu/HYeL9alluLPVEOgM=', '846401652.png', 'F', 1),
(24, 1, 2, 'Lic.', 'Naranjo Mantuano ', 'Renato Evaristo', 'Lic. Renato Naranjo', 'Naranjo Mantuano  Renato Evaristo', 'renaton', 'bWhJUtrPzzN8UCDSRMM3FQP7WbK8M7D2I8gABZYMoa0=', '1242913306.png', 'M', 1),
(25, 1, 2, 'Lic.', 'Morán Ortiz', 'Carlos Eduardo', 'Lic. Carlos Morán', 'Morán Ortiz Carlos Eduardo', 'carlosm', 'aI3mzPYgESxW83TP+g7z5i8DgmhmllihisuhRZIuvnI=', '168991802.png', 'M', 1),
(26, 1, 2, 'Lic.', 'Morán Dumes ', 'Fátima Herminia', 'Lic. Fátima Morán', 'Morán Dumes  Fátima Herminia', 'fatimam', '6qjfWISzehn2zGx7kdSlpFOkHEUj/DhV98uF6xTptRw=', '1155329981.png', 'F', 1),
(27, 1, 2, 'Prof.', 'Morales Rodríguez', 'Diana Selinda', 'Prof. Diana Morales', 'Morales Rodríguez Diana Selinda', 'dianam', 'd52jSogSkTrto0Wbx/VkmiEpXxlXWJqfDAqtNUzTw5A=', '1901459950.png', 'F', 1),
(28, 1, 2, 'Lic.', 'Morales Cruz  ', 'Carlos Stalin', 'Lic. Carlos Morales', 'Morales Cruz   Carlos Stalin', 'carlosmo', 'apzXOTcNdqFRN4WblOhd4fLgDiG+iblUhdbIDYWbO/Q=', '2081290784.png', 'M', 1),
(29, 1, 2, 'Sr.', 'Mejía Suárez', 'Robin Danny ', 'Sr. Robin Mejía', 'Mejía Suárez Robin Danny ', 'robinm', '9c3nA2asOxy8QfA75GMT+NLRHGmI+A4nDgKJ2gBtuoM=', '611106964.png', 'M', 1),
(30, 1, 2, 'Lic.', 'Medina Soledispa', 'Clara Isabel  ', 'Lic. Clara Medina', 'Medina Soledispa Clara Isabel  ', 'claram', 'TwY33TvRF6T9YL0xpdHzMsun3vzx8IYmPu6H72qQ7RY=', '606699092.png', 'F', 1),
(31, 1, 2, 'Tlgo.', 'Martillo', 'Manuel', 'Tlgo. Manuel Martillo', 'Martillo Manuel', 'manuelm', '9OIS10RUn9ND98xJOAg8V1tZeZXOOd2+nZQNJhp0nWo=', '1047302724.png', 'M', 1),
(32, 1, 2, 'Lic.', 'Mantuano Loy ', 'Glenda Guisella', 'Lic. Glenda Mantuano', 'Mantuano Loy  Glenda Guisella', 'glendam', 'lskJqSA9EK1cf8RXIU/ACG4yUsk1F74pbcvY3Ci/Yiw=', '1163825244.png', 'F', 1),
(33, 1, 2, 'Lic.', 'Mantuano Alvarado ', 'Leonardo David', 'Lic. Leonardo Mantuano', 'Mantuano Alvarado  Leonardo David', 'leonardom', 'dqj/pdrGSTXIS1JYrqh4eY4Z3CXNtcXJ+JNXBtlVbG8=', '377430460.png', 'M', 1),
(34, 1, 2, 'Lic.', 'Lozano García', 'Mirna Oralides', 'Lic. Mirna Lozano', 'Lozano García Mirna Oralides', 'mirnal', 'QVafkj58RDUjSUPOchQWXeywdlOWisB68c8/Hw+Pw7E=', '1629556263.png', 'F', 1),
(35, 1, 2, 'Licda', 'López Montoya ', 'Ana Isabel ', 'Licda. Ana López', 'López Montoya  Ana Isabel ', 'analopez', 'K9mhXcDYDYMFH/qWvFHR4T8MmLehihmucQ987baNmtI=', '33837488.png', 'F', 1),
(36, 1, 2, 'Lic.', 'López Briones', 'José Robinson', 'Lic. José López', 'López Briones José Robinson', 'josel', 'kcaZimKu+wfDn3wgreKmtjFDbb5Y2e/lYQlmO1z3obA=', '706105935.png', 'M', 1),
(37, 1, 2, 'Lic.', 'Loor Ronquillo', 'Marcela Karina', 'Lic. Marcela Loor', 'Loor Ronquillo Marcela Karina', 'marcelal', 'T4tGCst5VcN4joGDdt4Hm5uyrpUdd0A4GuY8lGWhd6s=', '1132153880.png', 'F', 1),
(38, 1, 2, 'Tlgo.', 'León García', 'Rosa María', 'Tlgo. Rosa León', 'León García Rosa María', 'rosal', 'dkG8JVLjbWZsvhTcqAg8I8nYsLPUcS76G+uWmcokfY4=', '164696051.png', 'F', 1),
(39, 1, 2, 'Tlga.', 'Lamilla Sánchez', 'Maricela María ', 'Tlga. Maricela Lamilla', 'Lamilla Sánchez Maricela María ', 'maricelal', '6YMB9UWy9qa7HAgu/JxGO/gm7nFY/mY1yMUO/OHS8Iw=', '840257985.png', 'F', 1),
(40, 1, 2, 'Sr.', 'Jurado Cruz ', 'Tomás Euclides', 'Sr. Tomás Jurado', 'Jurado Cruz  Tomás Euclides', 'tomasj', 'qyCDAEYfhnDEzYGXU4EmT9qUbLAw0ne8Y1EwWSEzjPA=', '233972935.png', 'M', 1),
(41, 1, 2, 'Tlga.', 'Jiménez Ruiz ', 'Adela Del Rosario ', 'Tlga. Adela Jiménez', 'Jiménez Ruiz  Adela Del Rosario ', 'adelaj', 'aQZEyEIoI0FLxkUjO7d1jUdectpL2BKz0oPYA3zT7/o=', '1821173914.png', 'F', 1),
(42, 1, 2, 'Prof.', 'Holguín Balladares', 'Narcisa Del Carmen', 'Prof. Narcisa Holguín', 'Holguín Balladares Narcisa Del Carmen', 'narcisah', 'qIPkuPxEiiUIkymklX+GJwgJFM5r/wCBuaUQQGEfyvo=', '280187255.png', 'F', 1),
(43, 1, 2, 'Prof.', 'Hidalgo Chaguay', 'Flavio Alejandro', 'Prof. Flavio Hidalgo', 'Hidalgo Chaguay Flavio Alejandro', 'flavioh', '3k05RpuUFJWEq5chldPRnk4gIgELFKOk7M5jOPddaYM=', '515495791.png', 'M', 1),
(44, 1, 2, 'Lic.', 'Herrera Santana', 'Rosa Felicita', 'Lic. Rosa Herrera', 'Herrera Santana Rosa Felicita', 'rosah', 'CP9beRGbfcmhq/J6w2jslKxZI8i3KMPfpJTBggYPT0w=', '324485779.png', 'F', 1),
(45, 1, 2, 'Prof.', 'Guillén Holguín ', 'Blanca Estela ', 'Prof. Blanca Guillén', 'Guillén Holguín  Blanca Estela ', 'blancag', '2wrz8lhd655ughb2Q5hVtVzw3BQD+7d7ktw7ejYZ8DE=', '887052697.png', 'F', 1),
(46, 1, 2, 'Tlgo.', 'González Malagón', 'Maritza Geraldine', 'Tlgo. Maritza González', 'González Malagón Maritza Geraldine', 'maritzag', 'Anck1847EI4T6VQJTTPuGY3l9VsXTEz6eDJVg8ysavk=', '1369728544.png', 'F', 1),
(47, 1, 2, 'Lic.', 'González  Díaz', 'José Wladimir ', 'Lic. José González', 'González  Díaz José Wladimir ', 'joseg', 'pCtnxBA4AQXA4dpTkNjF8AkkIp6vQOWazwa21/cH/fk=', '240244892.png', 'M', 1),
(48, 1, 2, 'Lic.', 'Gómez Haro', 'Patricia Edith', 'Lic. Patricia Gómez', 'Gómez Haro Patricia Edith', 'patriciag', 'RB9njBBqx1VqOJMN4vO3r+z1tf2/JEQYQ4alHlAdv8o=', '2054308345.png', 'F', 1),
(49, 1, 2, 'Lic.', 'Gómez Chiriguaya', 'Nilda María', 'Lic. Nilda Gómez', 'Gómez Chiriguaya Nilda María', 'nildag', 'Ut3+VUloOMeCDfsTTFGU6s9N0yL15cIeWUtTQHrokkA=', '421497791.png', 'F', 1),
(50, 1, 2, 'Prof.', 'García Morán   ', 'Yadira Elizabeth  ', 'Prof. Yadira García', 'García Morán    Yadira Elizabeth  ', 'yadirag', 'S8HUbUspeD8I0MRo8w5UMXqoghDAMVDLXTKKUygdEgc=', '1714888846.png', 'F', 1),
(51, 1, 2, 'Lic.', 'García Alvarez', 'Javier Antonio', 'Lic. Javier García', 'García Alvarez Javier Antonio', 'javierg', 'ryMJZKNonjqz8Em1DjON+xY2ODt1A6K1Yxq1bXmsUik=', '920060638.png', 'M', 1),
(52, 1, 2, 'Prof.', 'Franco Veloz ', 'Silvia María ', 'Prof. Silvia Franco', 'Franco Veloz  Silvia María ', 'silviaf', 'nwFg26FvZYgkq2WB9XmB04gRLwA6ENTPPYnt5cBqqmY=', '1746720463.png', 'F', 1),
(53, 1, 2, 'Lic.', 'Decimavilla Briones', 'Lucy Edith', 'Lic. Lucy Decimavilla', 'Decimavilla Briones Lucy Edith', 'lucyd', 'YVtA4yJyWk0xc9RKsZFR/JNMLMWpYQW10AeTH9MyOIg=', '920157287.png', 'F', 1),
(54, 1, 2, 'Prof.', 'Contreras González', 'Nancy Pilar', 'Prof. Nancy Contreras', 'Contreras González Nancy Pilar', 'nancyc', 'vSu1cZ0ga0fjlFlYXyLhbt53UxCPvSbF+hZRBGDOIpM=', '1751284313.png', 'F', 1),
(55, 1, 2, 'Prof.', 'Coloma Tutiven ', 'Violeta Alexandra', 'Prof. Violeta Coloma', 'Coloma Tutiven  Violeta Alexandra', 'violetac', 'jy7UMTDk34ZKUbGVAyjLHaHqYFFqqG4sYbhM9PHnqwY=', '1060078934.png', 'F', 1),
(56, 1, 2, 'Srta.', 'Chévez Olaya', 'Wendy Geoconda', 'Srta. Wendy Chévez', 'Chévez Olaya Wendy Geoconda', 'wendych', '7+fqrBJhnB5XnejcAdflLrNqMD9/jFHo3tFO2PbWgDA=', '617293489.png', 'F', 1),
(57, 1, 2, 'Lic.', 'Chaguay Nieto', 'Haydee Maricela ', ' Haydee Chaguay', 'Chaguay Nieto Haydee Maricela ', 'haydeech', 'AlF9pZaVfE39VwG3QrHF8rzIqJiNsWzfd6rcQ0npu+E=', '2124251991.png', 'F', 1),
(58, 1, 2, 'Lic.', 'Cedeño Dueñas', 'José Alfredo', 'Lic. José Cedeño', 'Cedeño Dueñas José Alfredo', 'josec', 's/fae10ADr+omuqM09x/721Bow86Q6ofO0SRcFPtVjY=', '1068929867.png', 'M', 1),
(59, 1, 2, 'Lic.', 'Cazorla Vera', 'Luis Enrique', 'Lic. Luis Cazorla', 'Cazorla Vera Luis Enrique', 'luisc', 'mnsIhbnOvryWFt0JT+YanfK+YjVCGxf3SiLhL428cWQ=', '503328621.png', 'M', 1),
(60, 1, 2, 'Lic.', 'Carpio Rodríguez', 'Marianela Magdalena', 'Lic. Marianela Carpio', 'Carpio Rodríguez Marianela Magdalena', 'marianelac', 'kz4OzW/YU0dYG8VW68tAf0hIfwt54ZlByadmja0pZGo=', '551439921.png', 'F', 1),
(62, 1, 2, 'Tlga.', 'Cantos Duque ', 'Melba Maritza', 'Tlga. Melba Cantos', 'Cantos Duque  Melba Maritza', 'melbac', 'eor0mgOZTo88KFFLb5XjBBomkMBg1HJ0Kz18RbsgXDQ=', '666898153.png', 'F', 1),
(63, 1, 2, 'Prof.', 'Navarrete Pita ', 'Henry Gonzalo ', 'Prof. Henry Navarrete', 'Navarrete Pita  Henry Gonzalo ', 'henryn', 'Ay3LfCYgPdj8igPouu+4c0apXXbQvif5ftbUb+pCdO8=', '948075093.png', 'M', 1),
(64, 1, 2, 'Lic.', 'Campaña Salavarría', 'Mariuxi Elizabeth', 'Lic. Mariuxi Campaña', 'Campaña Salavarría Mariuxi Elizabeth', 'mariuxic', 'E8rs8Ha2hTLESew2HS2bhnn9LCNvJAQx3TppnGgR6sI=', '1949128097.png', 'F', 1),
(65, 1, 2, 'Lic.', 'Caicedo Piguave ', 'César Joffre ', 'Lic. César Caicedo', 'Caicedo Piguave  César Joffre ', 'cesarc', 'U7t63jyW7XYxLnWvQKvAL7/2y4nVUfirWmzS0dtZdpc=', '1450981813.png', 'M', 1),
(66, 1, 2, 'Lic.', 'Barzola  Alvarado', 'León Hermógenes', 'Lic. León Barzola', 'Barzola  Alvarado León Hermógenes', 'leonb', 'xnM+K4Fj+JaVrqAGJXsfT5UkPFj+G1liz5Dxz+CltfE=', '1894265832.png', 'M', 1),
(67, 1, 2, 'Lic.', 'Bajaña León', 'Adelaida Suggey', 'Lic. Adelaida Bajaña', 'Bajaña León Adelaida Suggey', 'adelaidab', 'zKpCkDrPcqv4HwkhYJicRkTvKGtsBWLdUcRyHq3dJAY=', '1910098971.png', 'F', 1),
(68, 1, 2, 'Lic.', 'Avilés Ramírez ', 'Juan Manuel ', 'Lic. Juan Avilés', 'Avilés Ramírez  Juan Manuel ', 'juanma', 'mUY6NdFBX6C4PqdA6xDNzsF3zN9yf/RcdGvReJKf2iY=', '1738049233.png', 'M', 1),
(69, 1, 2, 'Lic.', 'Astudillo Aguilar', 'Rommel Freddy', 'Lic. Rommel Astudillo', 'Astudillo Aguilar Rommel Freddy', 'rommela', '3es7rxieeCgnwGRbI0/+nf9TkQKhffXujOWux28OHsg=', '1385419476.png', 'M', 1),
(70, 1, 2, 'Tlgo.', 'Aroca Torres', 'Francisco Vicente', 'Tlgo. Francisco Aroca', 'Aroca Torres Francisco Vicente', 'franciscoa', 'HcYoRGNR43TwzHRRC1jt76xNQvadyxhMemCysfDV4Gk=', '1754501087.png', 'M', 1),
(71, 1, 2, 'Lic.', 'Aroca Barzola ', 'Jessica Katiuska', 'Lic. Jessica Aroca', 'Aroca Barzola  Jessica Katiuska', 'jessicaa', 'M4ZW5tEWwsyy30eb3F1JEyUv0kbH5r7Q3NlYOyhAhx8=', '2039696912.png', 'F', 1),
(72, 1, 2, 'Lic.', 'Alvarez Fuentes ', 'Richard Vicente', ' Richard Alvarez', 'Alvarez Fuentes  Richard Vicente', 'richarda', 'OP6Sbbsllf6xIywo6Mdefqkrk9HuOo78R84LedcOCMc=', '85639161.png', 'M', 1),
(73, 1, 2, 'Lic.', 'ALVARADO RIVAS ', 'WILMER TEODULFO ', 'Lic. WILMER ALVARADO', 'ALVARADO RIVAS  WILMER TEODULFO ', 'wilmera', 'LqaehV87v+SAZRDDu0fIzoegzeOmhgYKaI/aQarCjAE=', '1676357481.png', 'M', 1),
(74, 1, 2, 'Lic.', 'Alvarado Bonilla ', 'Noemí Narcisa', 'Lic. Noemí Alvarado', 'Alvarado Bonilla  Noemí Narcisa', 'noemia', 'mFjiQGySLQnLeqcPP7M3HjoFESEPkrzj736C/CPaplM=', '1113638517.png', 'F', 1),
(75, 1, 2, 'Lic.', 'Alvarado Bajaña ', 'Clara Aracely', 'Lic. Clara Alvarado', 'Alvarado Bajaña  Clara Aracely', 'claraa', 'CG/MfWM42rWiIBh88T/u/fneVsg+bkKQ32LtV4L5vpA=', '1161163191.png', 'F', 1),
(76, 1, 2, 'Lic.', 'Almeida Tutiven', 'Noralma Elena', 'Lic. Noralma Almeida', 'Almeida Tutiven Noralma Elena', 'noralmaa', '68/cW8bBnJrYynN5Fu6+jNrcBhGtU6ZFX7rcGgIJWew=', '1190873728.png', 'F', 1),
(77, 1, 2, 'Lic.', 'Acosta Muñoz', 'Javier Esteban', 'Lic. Javier Acosta', 'Acosta Muñoz Javier Esteban', 'javiera', 'vdb78d/bpGSeddmn/DC5Ibxka7SZiGLRgFgxFCVtTJg=', '37527181.png', 'M', 1),
(78, 1, 2, 'Lic.', 'Méndez Rivas ', 'Sindy Soraya', 'Lic. Sindy Méndez', 'Méndez Rivas  Sindy Soraya', 'sindym', 'rB0KcI7nJQ8hgFkpPVlkX64DXjRrjMKuqkDy8vK34gs=', '623963626.png', 'F', 1),
(79, 1, 2, 'Lic.', 'Vargas Pérez', 'Clemencia Targelia', 'Lic. Clemencia Vargas', 'Vargas Pérez Clemencia Targelia', 'clemenciav', 'S8Vh0Fup4hzbyw7TuSiVK+v3DcQHxLjRCL0rLxpJD60=', '821247416.png', 'F', 1),
(80, 1, 2, 'Lic.', 'JURADO ZAMBRANO', 'JEIMY MARTIE', 'Lic. JEIMY JURADO', 'JURADO ZAMBRANO JEIMY MARTIE', 'jeimyj', 'X7EX4s1XTs3Qas64F/36/iMbwGj90XH/99lfE0imp64=', '1949160373.png', 'F', 1),
(81, 1, 2, '', 'Duque Torres', 'Gabriela Elizabeth', ' Gabriela Duque', 'Duque Torres Gabriela Elizabeth', 'gabrielad', 'lcksUxUYLEpT//YVPrw9PEOv/I6S+Z73cMkKQ+RsawQ=', '1180533760.png', 'F', 1),
(82, 1, 2, '', 'Chávez Alvarado', 'Génessis Selene', ' Génessis Chávez', 'Chávez Alvarado Génessis Selene', 'genessisch', 'l0BAR4dRAnYhWQX8Cp0pOgcJp3tj4XIzF4jt/O73Pyc=', '2001878610.png', 'F', 1),
(83, 1, 2, 'Tlga.', 'Barahona Ruiz', 'Jennifer Gabriela', 'Tlga. Jennifer Barahona', 'Barahona Ruiz Jennifer Gabriela', 'jenniferb', '5jYVjfE09owGkVIDQETZ56JEye0MXAKG7wlz4+kaXdo=', '1200648509.png', 'F', 1),
(84, 1, 2, 'Lic.', 'Alvarado Bodero', 'Betsy Edith', 'Lic. Betsy Alvarado', 'Alvarado Bodero Betsy Edith', 'betsya', '2hxFREhmGJmHgzrVhTkrBIw0pfvu9A/EPWIWsY9CYOM=', '1156487950.png', 'F', 1),
(85, 1, 6, 'Msc.', 'Pita Velasco', 'Bélgica Elizabeth', 'Msc. Bélgica Pita', 'Pita Velasco Bélgica Elizabeth', 'belgicap', 'd6WWcgDO/j2Z1RZ60vLykuKXdHVGxX+N5+Jrh+NoE7U=', '1363809335.png', 'F', 1),
(86, 1, 1, 'Ing.', 'Daule', 'Administrador', 'Ing. Administrador Daule', 'Daule Administrador', 'amazonico', 'dcUWZTD8ZkGzFC5ClJfZz561MqSVnMezUBDDhOq2ur0=', '476267592.png', 'M', 1),
(88, 1, 3, 'Tnlgo', 'Reyes Pita', 'Angel Jussinmy', 'Tnlgo Angel Reyes', 'Reyes Pita Angel Jussinmy', 'Juchor', 'ZoIKZOroQTAqHa+nNk3m9eTNpbrzJP0QUGTMT/3UYoY=', '833844892.png', 'M', 1),
(89, 1, 2, 'Lcdo', 'Andrade Mendoza', 'Javier Ignacio', 'Lcdo Javier Andrade', 'Andrade Mendoza Javier Ignacio', 'ignacio.andrade', 'ui/UAaaNTMTadj/hjrtkdU8LojlQxr89bJj2QHcCqF4=', '1366730305.png', 'M', 1),
(90, 1, 2, 'Licen', 'Campaña Bejarano', 'Javier Ezequiel', 'Licen Javier Campaña', 'Campaña Bejarano Javier Ezequiel', 'Ezekielkamp', 't2hv7DECFThRB0B24IRvBNeZAJN+iwaTAn6/1nLHkxU=', '530529171.png', 'M', 1),
(91, 1, 2, 'Tnlga', 'RODRIGUEZ ALVARADO', 'JESSICA CLEMENCIA', 'Tnlga JESSICA RODRIGUEZ', 'RODRIGUEZ ALVARADO JESSICA CLEMENCIA', 'jessica.rodriguez', '17kWBGVCONexvFSWIqNGYu3UW6k60lCc/8fX+f6wtpk=', '457883588.png', 'F', 1),
(92, 1, 2, 'Lcda.', ' Cedeño Tomala', 'Estoica Yanela', 'Lcda. Estoica ', ' Cedeño Tomala Estoica Yanela', 'estoicac', 'VD7NCQS6QajaZlnsGbU/cPiqdtXAKe2o98DEyWH14Vk=', '342693991.png', 'F', 1),
(93, 1, 2, 'Lcda.', 'Candelario Duarte', 'Tatiana Vanessa', 'Lcda. Tatiana Candelario', 'Candelario Duarte Tatiana Vanessa', 'tatianav', 'GGYp3lPdxLfLzt9q7nbZNiz7qsVU1DpjP3S6AejLBWE=', '1129678821.png', 'F', 1),
(94, 1, 2, 'Lcda.', 'Villamar Salamea', 'Carol Estefania', 'Lcda. Carol Villamar', 'Villamar Salamea Carol Estefania', 'carolv', 'LYi3YEd1w7WbJTguTdyAaL2JemLeQ9s+9AE+FbZ+WI8=', '1854009525.png', 'F', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_usuario_perfil`
--

CREATE TABLE `sw_usuario_perfil` (
  `id_usuario` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `sw_usuario_perfil`
--

INSERT INTO `sw_usuario_perfil` (`id_usuario`, `id_perfil`) VALUES
(1, 1),
(2, 2),
(2, 7),
(3, 2),
(4, 2),
(4, 7),
(5, 2),
(5, 7),
(6, 2),
(7, 2),
(8, 2),
(8, 7),
(9, 2),
(9, 7),
(10, 2),
(11, 2),
(11, 7),
(12, 2),
(12, 7),
(13, 2),
(14, 2),
(14, 7),
(15, 2),
(15, 7),
(16, 2),
(17, 2),
(18, 2),
(18, 7),
(19, 2),
(19, 7),
(20, 2),
(21, 2),
(21, 7),
(22, 2),
(22, 7),
(23, 2),
(23, 7),
(24, 2),
(24, 7),
(25, 2),
(25, 5),
(25, 7),
(26, 2),
(26, 7),
(27, 2),
(27, 7),
(28, 2),
(28, 7),
(29, 2),
(30, 2),
(30, 7),
(31, 2),
(31, 5),
(32, 2),
(32, 7),
(33, 2),
(34, 2),
(35, 2),
(35, 7),
(36, 2),
(36, 7),
(37, 2),
(37, 7),
(38, 2),
(38, 7),
(39, 2),
(39, 7),
(40, 2),
(41, 1),
(41, 2),
(41, 3),
(41, 6),
(42, 2),
(42, 7),
(43, 2),
(44, 2),
(44, 7),
(45, 2),
(45, 7),
(46, 2),
(46, 3),
(46, 7),
(47, 2),
(47, 7),
(48, 2),
(48, 7),
(49, 2),
(49, 7),
(50, 2),
(51, 2),
(52, 2),
(52, 7),
(53, 2),
(53, 7),
(54, 2),
(54, 7),
(55, 2),
(55, 7),
(56, 2),
(57, 2),
(57, 7),
(58, 2),
(59, 2),
(60, 2),
(62, 2),
(62, 7),
(63, 2),
(63, 7),
(64, 2),
(64, 7),
(65, 2),
(65, 7),
(66, 2),
(66, 7),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(70, 7),
(71, 2),
(71, 7),
(72, 2),
(72, 7),
(73, 2),
(73, 7),
(74, 2),
(75, 2),
(75, 7),
(76, 2),
(77, 2),
(77, 7),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(81, 7),
(82, 2),
(82, 7),
(83, 2),
(84, 2),
(84, 7),
(85, 6),
(86, 1),
(88, 1),
(88, 2),
(88, 3),
(88, 5),
(88, 7),
(89, 2),
(90, 2),
(90, 3),
(90, 6),
(90, 7),
(91, 2),
(91, 7),
(92, 2),
(92, 7),
(93, 2),
(93, 7),
(94, 2),
(94, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sw_valor_mes`
--

CREATE TABLE `sw_valor_mes` (
  `id_valor_mes` int(11) NOT NULL,
  `id_periodo_lectivo` int(11) NOT NULL,
  `vm_mes` int(11) NOT NULL,
  `vm_valor` varchar(15) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

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
  ADD KEY `id_jornada` (`id_jornada`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

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
  ADD KEY `id_institucion` (`id_institucion`),
  ADD KEY `fk_periodo_lectivo_periodo_estado` (`id_periodo_estado`),
  ADD KEY `fk_periodo_lectivo_modalidad` (`id_modalidad`);

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
  ADD KEY `fk_tipo_educacion_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_tipo_rubrica`
--
ALTER TABLE `sw_tipo_rubrica`
  ADD PRIMARY KEY (`id_tipo_rubrica`);

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
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`),
  ADD KEY `fk_usuario_perfil_perfil` (`id_perfil`);

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
  MODIFY `id_aporte_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=567;
--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=307;
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
  MODIFY `id_asociar_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
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
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;
--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=667;
--
-- AUTO_INCREMENT de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  MODIFY `id_estudiante_club` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=667;
--
-- AUTO_INCREMENT de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
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
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  MODIFY `id_horario_examen` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
--
-- AUTO_INCREMENT de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  MODIFY `id_inasistencia` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;
--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;
--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
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
  MODIFY `id_paralelo_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;
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
  MODIFY `id_periodo_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
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
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=545;
--
-- AUTO_INCREMENT de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
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
  MODIFY `id_tipo_aporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `sw_tipo_rubrica`
--
ALTER TABLE `sw_tipo_rubrica`
  MODIFY `id_tipo_rubrica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
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
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_paralelo_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_paralelo_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_ibfk_2` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`);

--
-- Filtros para la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD CONSTRAINT `fk_periodo_lectivo_modalidad` FOREIGN KEY (`id_modalidad`) REFERENCES `sw_modalidad` (`id_modalidad`),
  ADD CONSTRAINT `fk_periodo_lectivo_periodo_estado` FOREIGN KEY (`id_periodo_estado`) REFERENCES `sw_periodo_estado` (`id_periodo_estado`);

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
-- Filtros para la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD CONSTRAINT `fk_usuario_perfil_perfil` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`),
  ADD CONSTRAINT `fk_usuario_perfil_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
