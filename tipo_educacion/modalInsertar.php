<!-- Nuevo Nivel de Educación Modal -->
<div class="modal fade" id="nuevoNivelEducacionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Nivel de Educación</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarNivelEducacion()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="te_nombre" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="te_nombre" value="">
                        <span id="mensaje1" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="te_bachillerato" class="col-form-label">¿Es Bachillerato?:</label>
                        <select class="form-control" id="te_bachillerato">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
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
<!-- Fin Nuevo Nivel de Educación Modal -->