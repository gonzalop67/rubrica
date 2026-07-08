<?php $this->layout = 'layout.app'; ?>

<?php ob_start(); $this->currentSection = 'content'; ?>
    <div class="container m-t-10">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5><strong>Nuevo Rol</strong></h5>
                        <div>
                            <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/roles" class="btn-volver">Volver al Listado de Roles</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formulario" action="" method="post">
                            <div class="row mb-2">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre" id="nombre" value=""
                                        placeholder="Nombre del Perfil e.g. Estudiante" required autofocus>
                                    <!-- CORREGIDO: Se eliminó el style="display:none;" manual -->
                                    <div id="error-nombre" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label for="slug" class="col-sm-2 col-form-label">Slug:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="slug" id="slug" value=""
                                        placeholder="Slug del Perfil e.g. estudiante" required autofocus>
                                    <!-- CORREGIDO: Se eliminó el style="display:none;" manual -->
                                    <div id="error-slug" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <button id="btn-submit" type="submit" class="btn btn-primary"><i
                                            class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/public/assets/js/pages/admin/perfiles/crear.js"></script>
<?php $this->sections[$this->currentSection] = ob_get_clean(); ?>
