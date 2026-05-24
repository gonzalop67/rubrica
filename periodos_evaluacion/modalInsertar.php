<!-- Nuevo Periodo de Evaluacion Modal -->
<div class="modal fade" id="nuevoPeriodoEvaluacionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Nuevo Periodo de Evaluación</h4>
            </div>
            <form id="form_insert" action="" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="new_pe_nombre" class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_pe_nombre" name="new_pe_nombre" value="">
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="new_pe_abreviatura" class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="new_pe_abreviatura" name="new_pe_abreviatura" value="">
                            <span id="mensaje2" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="new_pe_principal" class="control-label">Tipo:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="new_pe_principal" name="new_pe_principal">
                                <option value=""> Seleccione... </option>
                                <!-- Definido en la tabla sw_tipo_periodo -->
                            </select>
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label for="new_pe_ponderacion" class="control-label">Ponderación:</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" min="0.01" max="1.00" step="0.01" class="form-control" id="new_pe_ponderacion" name="new_pe_ponderacion" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span id="mensaje4" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label for="new_id_curso" class="control-label">Curso:</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="new_id_curso[]" id="new_id_curso" class="form-control" multiple size="4">
                                <!-- Aquí van los datos recuperados de la BD mediante Ajax -->
                            </select>
                            <span id="mensaje5" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div id="div_rango" style="display: none">
                        <fieldset>
                            <legend>Rango de obtención del examen:</legend>
                            <div class="row" style="margin-top: 2px;">
                                <div class="col-lg-3 text-right">
                                    <label for="new_nota_desde" class="control-label">Nota desde:</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" min="0.01" step="0.01" class="form-control" id="new_nota_desde" name="new_nota_desde" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                    <span id="mensaje6" style="color: #e73d4a"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 2px;">
                                <div class="col-lg-3 text-right">
                                    <label for="new_nota_hasta" class="control-label">Nota hasta:</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" min="0.01" step="0.01" class="form-control" id="new_nota_hasta" name="new_nota_hasta" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                    <span id="mensaje7" style="color: #e73d4a"></span>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="insertarPeriodoEvaluacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Añadir</a>
                </div>
            </form>
        </div>
    </div>
</div>