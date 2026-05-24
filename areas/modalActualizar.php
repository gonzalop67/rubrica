<!-- Editar Area Modal -->
<div class="modal fade" id="editarAreaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Area</h4>
            </div>
            <form id="form_update" action="" autocomplete="off">
                <input type="hidden" id="id_area">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="ar_nombreu" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="ar_nombreu" value="">
                        <span id="mensaje2" style="color: #e73d4a"></span>
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
<!-- Fin Editar Area Modal -->