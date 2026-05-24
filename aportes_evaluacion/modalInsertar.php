<!-- Nuevo Aporte de Evaluacion Modal -->
<div class="modal fade" id="nuevoAporteEvaluacionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Nuevo Aporte de Evaluación</h4>
            </div>
            <form id="form_insert" action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_ap_nombre" value="">
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_ap_abreviatura" value="">
                            <span id="mensaje2" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Descripción:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_ap_descripcion" value="">
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Tipo:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_ap_tipo">
                                <option value=""> Seleccione... </option>
                                <!-- Definido en la tabla sw_tipo_periodo
                                <option value="1"> 	PARCIAL </option>
                                <option value="2"> 	EXAMEN QUIMESTRAL </option>
                                <option value="3"> 	SUPLETORIO </option> -->
                            </select>
                            <span id="mensaje4" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Ponderación</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" min="0.01" max="1.00" step="0.01" class="form-control" id="new_ap_ponderacion" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span id="mensaje5" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Fecha Inicial:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_ap_fecha_apertura" value="">
                            <span id="mensaje6" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label class="control-label">Fecha Final:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_ap_fecha_cierre" value="">
                            <span id="mensaje7" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="insertarAporteEvaluacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </form>
        </div>
    </div>
</div>