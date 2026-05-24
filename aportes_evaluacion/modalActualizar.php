<!-- Editar Aporte de Evaluacion Modal -->
<div class="modal fade" id="editarAporteEvaluacionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Editar Aporte de Evaluación</h4>
            </div>
            <form id="form_update" action="" class="form-horizontal">
                <input type="hidden" id="id_aporte_evaluacion">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_ap_nombre" value="">
                            <span id="mensaje8" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_ap_abreviatura" value="">
                            <span id="mensaje9" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Descripción:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_ap_descripcion" value="">
                            <span id="mensaje10" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Tipo:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_ap_tipo">
                                <option value=""> Seleccione... </option>
                                <!-- Definido en la tabla sw_tipo_periodo
                                <option value="1"> 	PARCIAL </option>
                                <option value="2"> 	EXAMEN QUIMESTRAL </option>
                                <option value="3"> 	SUPLETORIO </option> -->
                            </select>
                            <span id="mensaje11" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Ponderación</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" min="0.01" max="1.00" step="0.01" class="form-control" id="edit_ap_ponderacion" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span id="mensaje12" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Fecha Inicial:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_ap_fecha_apertura" value="">
                            <span id="mensaje13" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Fecha Final:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_ap_fecha_cierre" value="">
                            <span id="mensaje14" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarAporteEvaluacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>