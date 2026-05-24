<!-- Nuevo Subperiodo de Evaluacion Modal -->
<div class="modal fade" id="nuevoSubPeriodoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Subperiodo de Evaluación</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarSubperiodo()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group">
                        <label for="nombre" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control text-uppercase" id="nombre" value="">
                        <span id="error-nombre" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="abreviatura" class="col-form-label">Abreviatura:</label>
                        <input type="text" class="form-control text-uppercase" id="abreviatura" value="">
                        <span id="error-abreviatura" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="tipo_periodo" class="col-form-label">Tipo de Periodo:</label>
                        <?php
                        $consulta = $db->consulta("SELECT * FROM sw_tipo_periodo ORDER BY id_tipo_periodo ASC");
                        ?>
                        <select name="tipo_periodo" id="tipo_periodo" class="form-control">
                            <?php while ($tipo_periodo = $db->fetch_object($consulta)) { ?>
                                <option value="<?php echo $tipo_periodo->id_tipo_periodo ?>"><?php echo $tipo_periodo->tp_descripcion ?></option>
                            <?php } ?>
                        </select>
                        <span id="error-tipo_periodo" style="color: #e73d4a"></span>
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
<!-- Fin Nuevo Tipo de Periodo de Evaluación Modal -->