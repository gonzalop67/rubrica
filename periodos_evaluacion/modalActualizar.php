<!-- Editar Periodo de Evaluacion Modal -->
<div class="modal fade" id="editarPeriodoEvaluacionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel">Editar Periodo de Evaluación</h4>
            </div>
            <form id="form_update" action="" class="form-horizontal">
                <input type="hidden" id="id_periodo_evaluacion" name="id_periodo_evaluacion">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="edit_pe_nombre" class="control-label">Nombre:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_pe_nombre" name="edit_pe_nombre" value="">
                            <span id="mensaje8" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="edit_pe_abreviatura" class="control-label">Abreviatura:</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="edit_pe_abreviatura" name="edit_pe_abreviatura" value="">
                            <span id="mensaje9" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="edit_pe_principal" class="control-label">Tipo:</label>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control" id="edit_pe_principal" name="edit_pe_principal">
                                <option value=""> Seleccione... </option>
                                <!-- Definido en la tabla sw_tipo_periodo -->
                            </select>
                            <span id="mensaje10" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-3">
                            <label for="edit_pe_ponderacion" class="control-label">Ponderación</label>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" min="0.01" step="0.01" class="form-control" id="edit_pe_ponderacion" name="edit_pe_ponderacion" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                            <span id="mensaje11" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label for="edit_id_curso" class="control-label">Curso:</label>
                        </div>
                        <div class="col-sm-9">
                            <select name="edit_id_curso[]" id="edit_id_curso" class="form-control" multiple size="4">
                                <!-- Aquí van los datos recuperados de la BD mediante Ajax -->
                            </select>
                            <span id="mensaje12" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div id="div_rangou" style="display: none">
                        <fieldset>
                            <legend>Rango de obtención del examen:</legend>
                            <div class="row" style="margin-top: 2px;">
                                <div class="col-lg-3 text-right">
                                    <label for="nota_desdeu" class="control-label">Nota desde:</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" min="0.01" step="0.01" class="form-control" id="nota_desdeu" name="nota_desdeu" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                    <span id="mensaje13" style="color: #e73d4a"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 2px;">
                                <div class="col-lg-3 text-right">
                                    <label for="nota_hastau" class="control-label">Nota hasta:</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" min="0.01" step="0.01" class="form-control" id="nota_hastau" name="nota_hastau" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                    <span id="mensaje14" style="color: #e73d4a"></span>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarPeriodoEvaluacion()"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>