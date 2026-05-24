<!-- Nuevo Periodo Lectivo Modal -->
<div class="modal fade" id="nuevoPeriodoLectivoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Periodo Lectivo</h4>
            </div>
            <form id="form_insert" onsubmit="return actualizarPeriodoLectivo()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="texto" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="texto" name="texto" value="" required>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="enlace" class="col-sm-2 col-form-label">Enlace:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="enlace" name="enlace" value="" required>
                            <span id="mensaje2" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="publicado" class="col-sm-2 col-form-label">Publicado:</label>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="publicado" name="publicado">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="mensaje3" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_perfil" class="col-sm-2 col-form-label">Perfil:</label>
                        <?php
                        $consulta = $db->consulta("SELECT * FROM sw_perfil ORDER BY pe_nombre ASC");
                        ?>
                        <div class="col-sm-10">
                            <select name="id_perfil" id="id_perfil" class="form-control">
                                <?php while ($perfil = $db->fetch_object($consulta)) { ?>
                                    <option value="<?php echo $perfil->id_perfil ?>"><?php echo $perfil->pe_nombre ?></option>
                                <?php } ?>
                            </select>
                            <span id="mensaje4" style="color: #e73d4a"></span>
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