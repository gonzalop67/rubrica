<!-- Nuevo Menú Modal -->
<div class="modal fade" id="nuevaModalidadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Menú</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarMenu()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="nombre" class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="nombre" name="nombre" value="" required>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="activo" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="activo" name="activo">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="mensaje2" style="color: #e73d4a"></span>
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
<!-- Fin Nueva Modalidad Modal -->