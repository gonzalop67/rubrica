<!-- Edit Paralelo Modal -->
<div class="modal fade" id="editarParaleloModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel3">Editar Paralelo</h4>
            </div>
            <div class="modal-body">
                <form id="form_update" action="" class="form-horizontal">
                    <input type="hidden" id="id_paralelo">
                    <input type="hidden" id="id_curso">
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label class="control-label">Curso:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_cu_nombre" name="edit_cu_nombre" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label class="control-label">Jornada:</label>
                        </div>
                        <div class="col-lg-10">
                            <select class="form-control" id="edit_cboJornada">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="edit_pa_nombre" name="edit_pa_nombre" value="">
                            <span class="help-desk error" id="mensaje5"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarParalelo()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
            </div>
        </div>
    </div>
</div>