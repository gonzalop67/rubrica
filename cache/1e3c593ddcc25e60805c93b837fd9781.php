<?php $this->layout = 'layout.app'; ?>

<?php ob_start(); $this->currentSection = 'content'; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card m-t-10">
                    <div class="card-header d-flex justify-content-between">
                        <h5><strong>Editar Usuario: <?php echo htmlspecialchars((string)($usuario['nombre_corto']), ENT_QUOTES, "UTF-8"); ?></strong></h5>
                        <div>
                            <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/usuarios">Volver al Listado de Usuarios</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formulario" action="" enctype="multipart/form-data" method="post">
                            <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo htmlspecialchars((string)($usuario['id_usuario']), ENT_QUOTES, "UTF-8"); ?>">
                            <div class="row mb-2">
                                <label for="tipo_documento" class="col-sm-2 col-form-label">DNI:</label>
                                <div class="col-sm-4">
                                    <select name="tipo_documento" id="tipo_documento" class="form-control">
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <?php foreach($tipos_documentos as $tipo_documento): ?>
                                            <option value="<?php echo htmlspecialchars((string)($tipo_documento['id_tipo_documento']), ENT_QUOTES, "UTF-8"); ?>"
                                                <?php echo htmlspecialchars((string)($tipo_documento['id_tipo_documento'] == $usuario['tipo_documento_id'] ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>
                                                <?php echo htmlspecialchars((string)($tipo_documento['td_nombre']), ENT_QUOTES, "UTF-8"); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="dni" id="dni"
                                        value="<?php echo htmlspecialchars((string)($usuario['dni']), ENT_QUOTES, "UTF-8"); ?>" placeholder="DNI e.g. 1712345678" required autofocus>
                                    <div id="error-dni" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                                <div class="col-sm-10">
                                    <select name="nacionalidad" id="nacionalidad" class="form-control">
                                        <option value="" disabled selected>Seleccionar...</option>
                                        <?php foreach($nacionalidades as $nacionalidad): ?>
                                            <option value="<?php echo htmlspecialchars((string)($nacionalidad['id_def_nacionalidad']), ENT_QUOTES, "UTF-8"); ?>"
                                                <?php echo htmlspecialchars((string)($nacionalidad['id_def_nacionalidad'] == $usuario['nacionalidad_id'] ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>
                                                <?php echo htmlspecialchars((string)($nacionalidad['dn_nombre']), ENT_QUOTES, "UTF-8"); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="titulo" class="col-sm-2 col-form-label">Título (Abrev.):</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="titulo" id="titulo"
                                        value="<?php echo htmlspecialchars((string)($usuario['titulo']), ENT_QUOTES, "UTF-8"); ?>" placeholder="Abreviatura del Título e.g. Lic."
                                        required>
                                    <div id="error-titulo" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="descripcion_titulo" class="col-sm-2 col-form-label">Descripción del
                                    Título:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="descripcion_titulo" id="descripcion_titulo" rows="2"
                                        placeholder="Descripción del Título e.g. Licenciado en Ciencias de la Educación"><?php echo htmlspecialchars((string)($usuario['descripcion_titulo']), ENT_QUOTES, "UTF-8"); ?></textarea>
                                    <div id="error-descripcion_titulo" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="primer_apellido" class="col-sm-2 col-form-label">Primer Apellido:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="primer_apellido" id="primer_apellido"
                                        value="<?php echo htmlspecialchars((string)($usuario['primer_apellido']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-primer_apellido" class="invalid-feedback" style="display:none;"></div>
                                </div>
                                <label for="segundo_apellido" class="col-sm-2 col-form-label">Segundo Apellido:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="segundo_apellido" id="segundo_apellido"
                                        value="<?php echo htmlspecialchars((string)($usuario['segundo_apellido']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-segundo_apellido" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="primer_nombre" class="col-sm-2 col-form-label">Primer Nombre:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="primer_nombre" id="primer_nombre"
                                        value="<?php echo htmlspecialchars((string)($usuario['primer_nombre']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-primer_nombre" class="invalid-feedback" style="display:none;"></div>
                                </div>
                                <label for="segundo_nombre" class="col-sm-2 col-form-label">Segundo Nombre:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="segundo_nombre" id="segundo_nombre"
                                        value="<?php echo htmlspecialchars((string)($usuario['segundo_nombre']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-segundo_nombre" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="nombre_corto" class="col-sm-2 col-form-label">Nombre Corto:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre_corto" id="nombre_corto"
                                        value="<?php echo htmlspecialchars((string)($usuario['nombre_corto']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-nombre_corto" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="nombre_completo" class="col-sm-2 col-form-label">Nombre Completo:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre_completo"
                                        id="nombre_completo" value="<?php echo htmlspecialchars((string)($usuario['nombre_completo']), ENT_QUOTES, "UTF-8"); ?>" disabled>
                                    <div id="error-nombre_completo" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="username" class="col-sm-2 control-label">Nombre de Usuario:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="username" id="username"
                                        value="<?php echo htmlspecialchars((string)($usuario['username']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-username" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="email" class="col-sm-2 control-label">Email:</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="<?php echo htmlspecialchars((string)($usuario['email']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-email" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="password" class="col-sm-2 control-label">Contraseña:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="password" id="password"
                                        value="<?php echo htmlspecialchars((string)($usuario['password']), ENT_QUOTES, "UTF-8"); ?>" required>
                                    <div id="error-password" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="genero" class="col-sm-2 control-label">Género:</label>
                                <div class="col-sm-10">
                                    <select name="genero" id="genero" class="form-control">
                                        <option value="Femenino" <?php echo htmlspecialchars((string)($usuario['genero'] == 'Femenino' ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>
                                            Femenino</option>
                                        <option value="Masculino"
                                            <?php echo htmlspecialchars((string)($usuario['genero'] == 'Masculino' ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>Masculino</option>
                                    </select>
                                    <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="activo" class="col-sm-2 control-label">Activo:</label>
                                <div class="col-sm-10">
                                    <select name="activo" id="activo" class="form-control">
                                        <option value="1" <?php echo htmlspecialchars((string)($usuario['activo'] == 1 ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>Sí</option>
                                        <option value="0" <?php echo htmlspecialchars((string)($usuario['activo'] == 0 ? 'selected' : ''), ENT_QUOTES, "UTF-8"); ?>>No</option>
                                    </select>
                                    <div id="error-activo" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div id="roles-container" class="row mb-2">
                                <label for="roles" class="col-sm-2 control-label">Rol:</label>
                                <div class="col-sm-10">
                                    <?php foreach($roles as $role): ?>
                                        <div>
                                            <input type="checkbox" name="roles[]" value="<?php echo htmlspecialchars((string)($role['id']), ENT_QUOTES, "UTF-8"); ?>"
                                                <?php echo htmlspecialchars((string)(in_array($role['id'], $userRoles) ? 'checked' : ''), ENT_QUOTES, "UTF-8"); ?>>
                                            <?php echo htmlspecialchars((string)($role['nombre']), ENT_QUOTES, "UTF-8"); ?>
                                        </div>
                                    <?php endforeach; ?>

                                    <!-- Bloque donde se inyectará el mensaje de error de JS -->
                                    <div id="error-roles" class="text-danger mt-1"
                                        style="display: none; font-size: 0.875em;"></div>
                                </div>

                            </div>
                            <div class="row mb-2">
                                <label for="us_avatar" class="col-sm-2 control-label"></label>

                                <?php 
                                    $fotoNombre = !empty($usuario['avatar'])
                                        ? $usuario['avatar']
                                        : 'no-disponible.png';
                                    $rutaFisica = dirname($_SERVER['SCRIPT_FILENAME']) . '/uploads/' . $fotoNombre;
                                    if (!file_exists($rutaFisica)) {
                                        $fotoNombre = 'no-disponible.png';
                                    }
                                    $avatarUrl = RUTA_URL . '/public/uploads/' . $fotoNombre;
                                 ?>

                                <div id="img_div" class="col-sm-10">
                                    <img id="us_avatar"
                                        src="<?php echo htmlspecialchars((string)($avatarUrl), ENT_QUOTES, "UTF-8"); ?>"
                                        name="us_avatar" class="img-thumbnail" width="75" alt="Avatar del usuario">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="avatar" class="col-sm-2 control-label"
                                    style="margin-top: -4px;">Imagen:</label>

                                <div class="col-sm-10">
                                    <input type="file" name="avatar" id="avatar">
                                    <div id="error-avatar" class="invalid-feedback" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <button id="btn-save" type="submit" class="btn btn-success"><i
                                            class="fa fa-pencil"></i> Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/public/assets/js/pages/admin/usuarios/crear.js"></script>
<?php $this->sections[$this->currentSection] = ob_get_clean(); ?>
