<!-- Editar Nivel de Educación Modal -->
<div class="modal fade" id="editarItemMallaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Item de Malla</h4>
            </div>
            <form id="form_update" action="#" autocomplete="off">
                <input type="hidden" name="id_malla_curricular" id="id_malla_curricular">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="ma_horas_presenciales" class="col-form-label">Horas Presenciales:</label>
                        <input type="number" min="0" class="form-control fuente9" id="ma_horas_presenciales" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                        <!-- <span id="mensaje2" style="color: #e73d4a"></span> -->
                    </div>
                    <div class="form-group">
                        <label for="ma_horas_autonomas" class="col-form-label">Horas Autónomas:</label>
                        <input type="number" min="0" class="form-control fuente9" id="ma_horas_autonomas" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                    </div>
                    <div class="form-group">
                        <label for="ma_horas_tutorias" class="col-form-label">Horas de Tutoría:</label>
                        <input type="number" min="0" class="form-control fuente9" id="ma_horas_tutorias" value="0" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Editar Nivel de Educación Modal -->