<!-- Nuevo Tipo de Periodo de Evaluacion Modal -->
<div class="modal fade" id="nuevoTipoPeriodoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Tipo de Periodo de Evaluación</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarTipoPeriodo()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="nombre" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control text-uppercase" id="nombre" value="">
                        <span id="error-nombre" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="slug" class="col-form-label">Slug:</label>
                        <input type="text" class="form-control" id="slug" value="">
                        <span id="error-slug" style="color: #e73d4a"></span>
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
<!-- Fin Nuevo Tipo de Periodo de Evaluación Modal -->