<!-- Nueva Alternativa Modal -->
<div class="modal fade" id="nuevaAlternativaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nueva Alternativa</h4>
            </div>
            <form id="form_insert_choice" onsubmit="return insertarAlternativa()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="choice" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <textarea name="choice" id="choice" cols="30" rows="3" class="form-control" placeholder="Añadir una alternativa" autofocus required></textarea>
                            <span id="mensaje3" style="color: #e73d4a"></span>
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
<!-- Fin Nueva Alternativa Modal -->