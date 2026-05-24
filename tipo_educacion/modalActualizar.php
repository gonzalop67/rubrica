<!-- Editar Nivel de Educación Modal -->
<div class="modal fade" id="editarNivelEducacionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Nivel de Educación</h4>
            </div>
            <form id="form_update" action="#" autocomplete="off">
                <input type="hidden" name="id_tipo_educacion" id="id_tipo_educacion">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="te_nombreu" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="te_nombreu" value="">
                        <span id="mensaje2" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="te_bachilleratou" class="col-form-label">¿Es Bachillerato?:</label>
                        <select class="form-control" id="te_bachilleratou">
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
<!-- Fin Editar Nivel de Educación Modal -->