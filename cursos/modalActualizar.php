<!-- Edit Curso Modal -->
<div class="modal fade" id="editarCursoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Editar Curso</h4>
            </div>
            <form id="form_update" action="" class="form-horizontal">
                <input type="hidden" id="id_curso">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_nombre" value="">
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_abreviatura" value="">
                            <span id="mensaje2" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre Corto:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_cu_shortname" value="">
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">¿Bachillerato Técnico?:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_es_bach_tecnico">
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
                            <select class="form-control" id="edit_es_intensivo">
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
                            <select class="form-control" id="edit_es_fin_subnivel">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarCurso()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>