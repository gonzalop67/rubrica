<!-- Nueva Especialidad Modal -->
<div class="modal fade" id="nuevaEspecialidadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nueva Especialidad</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarEspecialidad()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="es_nombre" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="es_nombre" value="">
                        <span id="mensaje1" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="es_figura" class="col-form-label">Figura:</label>
                        <input type="text" class="form-control" id="es_figura" value="">
                        <span id="mensaje2" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="es_abreviatura" class="col-form-label">Abreviatura:</label>
                        <input type="text" class="form-control" id="es_abreviatura" value="">
                        <span id="mensaje3" style="color: #e73d4a"></span>
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
<!-- Fin Nueva Especialidad Modal -->