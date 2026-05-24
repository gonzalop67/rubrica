<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel2">Editar Estudiante</h4>
            </div>
            <form id="form_edit" action="" method="post" autocomplete="off">
                <input type="hidden" name="id_estudiante" id="id_estudiante">
                <div class="modal-body fuente9">
                    <div class="form-group row">
                        <label for="edit_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_id_tipo_documento" name="edit_id_tipo_documento" required>
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="mensaje13" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_dni" class="col-sm-1 col-form-label">DNI:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_dni" name="edit_dni" value="">
                            <span id="mensaje14" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_apellidos" name="edit_apellidos" value="">
                            <span id="mensaje15" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_nombres" name="edit_nombres" value="">
                            <span id="mensaje16" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="edit_fec_nac" name="edit_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
                            <span id="mensaje17" style="color: #e73d4a"></span>
                        </div>

                        <label for="edit_edad" class="col-sm-1 col-form-label">Edad:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_edad" name="edit_edad" value="" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_direccion" class="col-sm-2 col-form-label">Dirección:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_direccion" name="edit_direccion" value="">
                            <span id="mensaje18" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_sector" class="col-sm-2 col-form-label">Sector:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control mayusculas" id="edit_sector" name="edit_sector" value="">
                            <span id="mensaje19" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_telefono" class="col-sm-1 col-form-label">Celular:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_telefono" name="edit_telefono" value="">
                            <span id="mensaje20" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_email" class="col-sm-2 col-form-label">E-mail:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_email" name="edit_email" value="">
                            <span id="mensaje21" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_genero" class="col-sm-2 col-form-label">Género:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_genero" name="edit_genero">
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="mensaje22" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_nacionalidad" name="edit_nacionalidad">
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="mensaje23" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_id_paralelo" class="col-sm-2 col-form-label">Nuevo Paralelo:</label>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="edit_id_paralelo" name="edit_id_paralelo">
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="mensaje24" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarEstudiante()"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>