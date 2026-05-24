<!-- Editar Categoria Modal -->
<div class="modal fade" id="editarCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Categoria de Especialidad</h4>
            </div>
            <form id="form_update" onsubmit="return actualizarCategoria()" autocomplete="off">
                <input type="hidden" name="id_categoria" id="id_categoria">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="nombreu" class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nombreu" name="nombreu" value="" required>
                            <span id="error-nombreu" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Editar Modalidad Modal -->