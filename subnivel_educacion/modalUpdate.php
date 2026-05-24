<!-- Editar Sub Nivel de Educación Modal -->
<div class="modal fade" id="editarSubNivelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Sub Nivel de Educación</h4>
            </div>
            <form id="form_update" onsubmit="return actualizarSubNivelEducacion()" autocomplete="off">
                <input type="hidden" name="id_subnivel_educacion" id="id_subnivel_educacion">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="nivel_idu" class="col-form-label">Nivel de educación:</label>
                        <select class="form-control" id="nivel_idu">
                            <option value="">Seleccione...</option>
                            <!-- Aquí van los registros recuperados mediante AJAX -->
                        </select>
                        <span id="error-nivel_idu" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="nombreu" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombreu" value="">
                        <span id="error-nombreu" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="slugu" class="col-form-label">Slug:</label>
                        <input type="text" class="form-control" id="slugu" value="">
                        <span id="error-slugu" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="es_bachilleratou" class="col-form-label">¿Es Bachillerato?:</label>
                        <select class="form-control" id="es_bachilleratou">
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        <span id="error-es_bachilleratou" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Editar Sub Nivel de Educación Modal -->