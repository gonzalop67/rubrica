<!-- Modal para actualizar la justificación -->
<div class="modal fade" id="editarJustificacionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Justificación</h4>
            </div>
            <form id="form_update" action="tutores/actualizar_justificacion.php" method="POST" autocomplete="off">
                <input type="hidden" name="id_asistencia_tutor" id="id_asistencia_tutor">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="justificacion" class="col-form-label">Justificacion:</label>
                        <input type="text" class="form-control text-uppercase" name="justificacion" id="justificacion" value="">
                        <span id="error-justificacion" style="color: #e73d4a"></span>
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
<!-- Fin Modal para actualizar la justificación -->