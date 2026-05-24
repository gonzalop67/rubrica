DELIMITER //
DROP PROCEDURE IF EXISTS `generar_usuario_estudiante`//
CREATE PROCEDURE generar_usuario_estudiante(IN IdParalelo INT)
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE p_estudiante_id INT;
    DECLARE p_apellidos VARCHAR(255);
    DECLARE p_nombres VARCHAR(255);
    DECLARE p_email VARCHAR(255);
    DECLARE p_cedula VARCHAR(255);
    DECLARE p_genero INT;
    DECLARE p_retirado INT;
    DECLARE new_user_id INT;

    -- Inserta en 'usuarios' usando datos de 'estudiantes'
    DECLARE cEstudiantes CURSOR FOR
	 SELECT e.id_estudiante,
            e.es_apellidos,
            e.es_nombres,
            e.es_email,
            e.es_cedula,
            ep.es_retirado, 
            dg.dg_abreviatura
       FROM sw_estudiante e, 
            sw_estudiante_periodo_lectivo ep,
            sw_def_genero dg
      WHERE e.id_estudiante = ep.id_estudiante
        AND ep.id_paralelo = IdParalelo
        AND e.id_def_genero = dg.id_def_genero;

    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

    OPEN cEstudiantes;

    Lazo: LOOP
        FETCH cEstudiantes INTO p_estudiante_id, p_apellidos, p_nombres, p_email, p_cedula, p_retirado, p_genero;
        IF done THEN
            CLOSE cEstudiantes;
            LEAVE Lazo;
        END IF;

        IF NOT EXISTS (SELECT 1 FROM sw_usuario WHERE us_apellidos = p_apellidos AND us_nombres = p_nombres) THEN
        BEGIN
            INSERT INTO sw_usuario 
            SET us_titulo = 'Sr.',
                us_titulo_descripcion = 'Sr. estudiante',
                us_apellidos = p_apellidos,
                us_nombres = p_nombres,
                us_shortname = CONCAT(LOWER(REPLACE(p_nombres, ' ', '.')), '.', LOWER(REPLACE(p_apellidos, ' ', '.'))), -- Shortname (ej: juan.perez)
                us_fullname = CONCAT(p_apellidos, ' ', p_nombres), -- Fullname 
                us_email = p_email,
                us_login = p_cedula, -- Login (usamos la cédula como login)
                us_password = 'eJCYkBmXtXug', -- Contraseña por defecto (debería ser cambiada por el usuario)
                us_foto = 'No_image_available.svg.png', -- Foto por defecto
                us_genero = p_genero,
                us_activo = 1; -- Activo

            -- Obtener el ID del usuario recién insertado
            
            SET new_user_id = LAST_INSERT_ID();
            -- Insertar en sw_usuario_perfil para asignar el rol de estudiante (asumiendo id_rol = 15 para estudiantes)
            INSERT INTO sw_usuario_perfil (id_usuario, id_perfil) VALUES (new_user_id, 15);

            -- Actualizar el id_usuario en la tabla sw_estudiante
            UPDATE sw_estudiante SET id_usuario = new_user_id WHERE id_estudiante = p_estudiante_id;

            -- Actualizar el campo activo de sw_usuario dependiendo del estado de retiro del estudiante
            IF p_retirado = 1 THEN
                UPDATE sw_usuario SET us_activo = 0 WHERE id_usuario = new_user_id; -- Si el estudiante está retirado, desactivar el usuario
            END IF;
        END;
        END IF;
    END LOOP Lazo;
END //
DELIMITER ;