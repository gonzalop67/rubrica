<!-- Nuevo Menu Modal -->
<div class="modal fade" id="nuevoPeriodoLectivoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Periodo Lectivo</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarPeriodoLectivo()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="pe_anio_inicio" class="col-sm-3 col-form-label">Año Inicial:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="pe_anio_inicio" name="pe_anio_inicio" value="" required>
                            <span id="error-pe_anio_inicio" class="text-danger"></span>
                        </div>
                        <label for="pe_anio_fin" class="col-sm-3 col-form-label">Año Final:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="pe_anio_fin" name="pe_anio_fin" value="" required>
                            <span id="error-pe_anio_fin" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pe_fecha_inicio" class="col-sm-3 col-form-label">Fecha de inicio:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="pe_fecha_inicio" name="pe_fecha_inicio" value="" autocomplete="off" required>
                            <span id="error-pe_fecha_inicio" class="text-danger"></span>
                        </div>
                        <label for="pe_fecha_fin" class="col-sm-3 col-form-label">Fecha de fin:</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="pe_fecha_fin" name="pe_fecha_fin" value="" required>
                            <span id="error-pe_fecha_fin" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pe_nota_minima" class="col-sm-3 col-form-label">Nota mínima:</label>
                        <div class="col-sm-3">
                            <input type="number" min="0.01" step="0.01" class="form-control" id="pe_nota_minima" name="pe_nota_minima" value="" required>
                            <span id="error-pe_nota_minima" class="text-danger"></span>
                        </div>
                        <label for="pe_nota_aprobacion" class="col-sm-3 col-form-label">Nota aprobación:</label>
                        <div class="col-sm-3">
                            <input type="number" min="7" max="10" step="0.01" class="form-control" id="pe_nota_aprobacion" name="pe_nota_aprobacion" value="" required>
                            <span id="error-pe_nota_aprobacion" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="quien_inserta_comp_id" class="col-sm-3 col-form-label">¿Quién inserta el comportamiento?:</label>
                        <?php
                        $consulta = $db->consulta("SELECT * FROM sw_quien_inserta_comp ORDER BY id ASC");
                        ?>
                        <div class="col-sm-9">
                            <select name="quien_inserta_comp_id" id="quien_inserta_comp_id" class="form-control">
                                <?php while ($row = $db->fetch_object($consulta)) { ?>
                                    <option value="<?php echo $row->id ?>"><?php echo $row->nombre ?></option>
                                <?php } ?>
                            </select>
                            <span id="error-quien_inserta_comp_id" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Guardar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Nueva Modalidad Modal -->