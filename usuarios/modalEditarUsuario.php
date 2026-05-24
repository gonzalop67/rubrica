<!-- Editar Usuario Modal -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel1">Editar Usuario</h4>
            </div>
            <form id="form_update" action="usuario/actualizar_usuario.php" method="post" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="edit_id_usuario" id="edit_id_usuario">
                <div class="modal-body fuente9">
                    <div class="form-group row">
                        <label for="edit_us_titulo" class="col-sm-2 col-form-label">Título:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="edit_us_titulo" name="edit_us_titulo" placeholder="Abreviatura..." value="">
                            <span id="error-edit_us_titulo" style="color: #e73d4a"></span>
                        </div>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="edit_us_titulo_descripcion" name="edit_us_titulo_descripcion" placeholder="Ingrese el Título del Usuario..." value="">
                            <span id="error-edit_us_titulo_descripcion" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_apellidos" name="edit_us_apellidos" placeholder="Ingrese los Apellidos del Usuario..." value="">
                            <span id="error-edit_us_apellidos" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_nombres" name="edit_us_nombres" placeholder="Ingrese los Nombres del Usuario..." value="">
                            <span id="error-edit_us_nombres" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_shortname" class="col-sm-2 col-form-label">Nombre Corto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_shortname" name="edit_us_shortname" placeholder="Ingrese el nombre corto del usuario..." value="">
                            <span id="error-edit_us_shortname" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_email" class="col-sm-2 col-form-label">Email:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_email" name="edit_us_email" placeholder="Email del usuario..." value="">
                            <span id="error-edit_us_email" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_login" class="col-sm-2 col-form-label">Usuario:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_login" name="edit_us_login" value="" placeholder="Ingrese el nombre de usuario..." maxlength="24">
                            <span id="error-edit_us_login" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_password" class="col-sm-2 col-form-label">Password:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_us_password" name="edit_us_password" placeholder="Ingrese el password de usuario..." value="">
                            <span id="error-edit_us_password" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_us_genero" class="col-sm-2 col-form-label">Género:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_us_genero" name="edit_us_genero">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <span id="error-edit_us_genero" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_us_activo" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_us_activo" name="edit_us_activo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="error-edit_us_activo" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_id_perfil" class="col-sm-2 control-label">Perfil:</label>
                        <div class="col-sm-10">
                            <select name="edit_id_perfil[]" id="edit_id_perfil" class="form-control" multiple size="4">
                                <!-- Aquí van los datos recuperados de la BD mediante Ajax -->
                            </select>
                            <span id="error-edit_id_perfil" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div id="edit_img_upload">
                        <div class="form-group">
                            <label for="edit_us_avatar" class="col-sm-2 control-label">Avatar</label>

                            <div id="edit_img_div" class="col-sm-10">
                                <img id="edit_us_avatar" name="edit_us_avatar" class="img-thumbnail" width="75" src="public/uploads/no-disponible.png">
                                <input type="hidden" name="bd_us_avatar" id="bd_us_avatar">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_us_foto" class="col-sm-2 control-label"></label>

                            <div class="col-sm-10">
                                <input type="file" name="edit_us_foto" id="edit_us_foto">
                                <span id="error-edit_us_foto" style="color: #e73d4a"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Editar Usuario Modal -->