<!-- Nuevo Menu Modal -->
<div class="modal fade" id="nuevoMenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="exampleModalLabel">Nuevo Menú</h4>
            </div>
            <form id="form_insert" onsubmit="return insertarMenu()" autocomplete="off">
                <div class="modal-body fuente10">
                    <div class="form-group row">
                        <label for="texto" class="col-sm-2 col-form-label">Texto:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="texto" name="texto" value="" required>
                            <span id="error-texto" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="enlace" class="col-sm-2 col-form-label">Enlace:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="enlace" name="enlace" value="" required>
                            <span id="error-enlace" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="icono" class="col-sm-2 col-form-label">Icono:</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="icono" name="icono" value="">
                            <span id="error-icono" style="color: #e73d4a"></span>
                        </div>
                        <div class="col-sm-1">
                            <span id="mostrar-icono" class="fa fa-fw "></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="publicado" class="col-sm-2 col-form-label">Publicado:</label>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="publicado" name="publicado">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                            <span id="error-publicado" style="color: #e73d4a"></span>
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
                            <span id="error-id_perfil" style="color: #e73d4a"></span>
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