<!-- Editar Especialidad Modal -->
<div class="modal fade" id="editarEspecialidadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Especialidad</h4>
            </div>
            <form id="form_update" action="#" autocomplete="off">
                <input type="hidden" name="id_especialidad" id="id_especialidad">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="es_nombreu" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="es_nombreu" value="">
                        <span id="mensaje4" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="es_figurau" class="col-form-label">Figura:</label>
                        <input type="text" class="form-control" id="es_figurau" value="">
                        <span id="mensaje5" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="es_abreviaturau" class="col-form-label">Abreviatura:</label>
                        <input type="text" class="form-control" id="es_abreviaturau" value="">
                        <span id="mensaje6" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Guardar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Editar Especialidad Modal -->