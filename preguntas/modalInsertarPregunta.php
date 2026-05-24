<!-- Nueva Pregunta Modal -->
<div class="modal fade" id="nuevaPreguntaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nueva Pregunta</h4>
            </div>
            <form id="form_insert_question" onsubmit="return insertarPregunta()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="question" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <textarea name="question" id="question" cols="30" rows="3" class="form-control" placeholder="Añadir una pregunta" autofocus required></textarea>
                            <span id="mensaje1" style="color: #e73d4a"></span>
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
<!-- Fin Nueva Pregunta Modal -->