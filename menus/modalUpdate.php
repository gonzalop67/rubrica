<!-- Editar Menu Modal -->
<div class="modal fade" id="editarMenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Editar Menú</h4>
            </div>
            <form id="form_update" onsubmit="return actualizarMenu()" autocomplete="off">
                <input type="hidden" name="id_menuu" id="id_menuu">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="textou" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="textou" name="textou" value="" required>
                            <span id="error-textou" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="enlaceu" class="col-sm-2 col-form-label">Enlace:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="enlaceu" name="enlaceu" value="" required>
                            <span id="error-enlaceu" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="iconou" class="col-sm-2 col-form-label">Icono:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="iconou" name="iconou" value="">
                            <span id="error-iconou" style="color: #e73d4a"></span>
                        </div>
                        <div class="col-sm-1">
                            <span id="mostrar-iconou" class="fa fa-fw "></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="publicadou" class="col-sm-2 col-form-label">Publicado:</label>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="publicadou" name="publicadou">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="error-publicadou" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_perfilu" class="col-sm-2 col-form-label">Perfil:</label>
                        <?php
                        $consulta = $db->consulta("SELECT * FROM sw_perfil ORDER BY pe_nombre ASC");
                        ?>
                        <div class="col-sm-10">
                            <select name="id_perfilu" id="id_perfilu" class="form-control">
                                <?php while ($perfil = $db->fetch_object($consulta)) { ?>
                                    <option value="<?php echo $perfil->id_perfil ?>"><?php echo $perfil->pe_nombre ?></option>
                                <?php } ?>
                            </select>
                            <span id="error-id_perfilu" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-pencil"></i> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Nueva Modalidad Modal -->