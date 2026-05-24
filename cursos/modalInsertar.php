<!-- New Curso Modal -->
<div class="modal fade" id="nuevoCursoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Nuevo Curso</h4>
            </div>
            <form id="form_insert" action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_nombre" value="">
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_abreviatura" value="">
                            <span id="mensaje2" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre Corto:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_cu_shortname" value="">
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Bachillerato Técnico?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_es_bach_tecnico">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Es Intensivo?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_es_intensivo">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Es Fin Subnivel?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_es_fin_subnivel">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Especialidad:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="cboEspecialidades">
                                <!-- Aquí se llenará el combobox mediante AJAX -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="insertarCurso()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </form>
        </div>
    </div>
</div>