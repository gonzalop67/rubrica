<!-- Editar Subperiodo de Evaluacion Modal -->
<div class="modal fade" id="editarSubperiodoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Subperiodo de Evaluación</h4>
            </div>
            <form id="form_update" action="" method="POST" autocomplete="off">
                <div class="modal-body fuente10">
                    <input type="hidden" name="id_sub_periodo_evaluacion" id="id_sub_periodo_evaluacion">
                    <div class="form-group">
                        <label for="nombreu" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control text-uppercase" id="nombreu" value="">
                        <span id="error-nombreu" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="abreviaturau" class="col-form-label">Abreviatura:</label>
                        <input type="text" class="form-control text-uppercase" id="abreviaturau" value="">
                        <span id="error-abreviaturau" style="color: #e73d4a"></span>
                    </div>
                    <div class="form-group">
                        <label for="tipo_periodou" class="col-form-label">Tipo de Periodo:</label>
                        <?php
                        $consulta = $db->consulta("SELECT * FROM sw_tipo_periodo ORDER BY id_tipo_periodo ASC");
                        ?>
                        <select name="tipo_periodou" id="tipo_periodou" class="form-control">
                            <?php while ($tipo_periodo = $db->fetch_object($consulta)) { ?>
                                <option value="<?php echo $tipo_periodo->id_tipo_periodo ?>"><?php echo $tipo_periodo->tp_descripcion ?></option>
                            <?php } ?>
                        </select>
                        <span id="error-tipo_periodou" style="color: #e73d4a"></span>
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
<!-- Fin Editar SubPeriodo de Evaluación Modal -->