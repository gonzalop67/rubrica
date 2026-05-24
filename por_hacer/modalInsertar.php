<!-- Nueva Tarea Modal -->
<div class="modal fade" id="nuevaTareaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nueva Tarea</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarTarea()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="tarea" class="col-sm-2 col-form-label">Tarea:</label>
                        <div class="col-sm-10">
                            <textarea id="tarea" name="tarea" class="form-control" cols="40" rows="5" placeholder="Inserte una nueva tarea..."></textarea>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
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
<!-- Fin Nueva Tarea Modal -->