<!-- Editar Paralelo Modal -->
<div class="modal fade" id="editarNivelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Editar Nivel de Educación</h4>
            </div>
            <form id="form_update" action="" class="form-horizontal">
                <input type="hidden" name="id_nivel_educacion" id="id_nivel_educacion">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="nombreu" value="">
                            <span id="error-nombreu" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-2">
                            <label class="control-label">Slug:</label>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" id="slugu" value="">
                            <span id="error-slugu" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarNivel()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>