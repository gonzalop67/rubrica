<!-- Editar Alternativa Modal -->
<div class="modal fade" id="editarAlternativaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Alternativa</h4>
            </div>
            <form id="form_update_choice" onsubmit="return actualizarAlternativa()" autocomplete="off">
                <input type="hidden" name="id_choice" id="id_choice">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="choice_u" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <textarea name="choice_u" id="choice_u" cols="30" rows="3" class="form-control" placeholder="Añadir una alternativa" autofocus required></textarea>
                            <span id="mensaje4" style="color: #e73d4a"></span>
                        </div>
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
<!-- Fin Nueva Alternativa Modal -->