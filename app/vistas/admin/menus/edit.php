<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Menús
        <small>Editar</small>
    </h1>
</section>
<section class="content">
    <!-- Default box -->
    <div class="box box-warning">
        <div class="box-body">
            <!-- <div class="row"> -->
            <!-- <div class="col-md-12"> -->
            <div class="panel-body">
                <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"></span></p>
                </div>
                <div id="alert-exito" class="alert alert-success alert-dismissible" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"></span></p>
                </div>
                <form id="frm-menu" action="<?php echo RUTA_URL; ?>/menus/update" method="post" class="form-horizontal">
                    <input type="hidden" name="id_menu" id="id_menu" value="<?php echo $datos['menu']->id_menu ?>">
                    <div class="form-group row">
                        <label for="mnu_texto" class="col-sm-2 control-label text-right">Texto:</label>
                        <div class="col-sm-10">
                            <input type="text" name="mnu_texto" id="mnu_texto" value="<?php echo $datos['menu']->mnu_texto ?>" class="form-control" required autofocus>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mnu_link" class="col-sm-2 control-label text-right">Enlace:</label>
                        <div class="col-sm-10">
                            <input type="text" name="mnu_link" id="mnu_link" value="<?php echo $datos['menu']->mnu_link ?>" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mnu_publicado" class="col-sm-2 control-label text-right">Publicado:</label>
                        <div class="col-sm-10">
                            <select name="mnu_publicado" id="mnu_publicado" class="form-control">
                                <option value="1" <?php echo ($datos['menu']->mnu_publicado == 1) ? 'selected' : '' ?>>Sí</option>
                                <option value="0" <?php echo ($datos['menu']->mnu_publicado == 0) ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="mnu_icono" class="col-sm-2 control-label text-right">Icono:</label>
                        <div class="col-sm-9">
                            <input type="text" name="mnu_icono" id="mnu_icono" value="<?php echo $datos['menu']->mnu_icono ?>" class="form-control">
                        </div>
                        <div class="col-sm-1">
                            <span id="mostrar-icono" class="fa fa-fw <?php echo $datos['menu']->mnu_icono ?>"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_perfil" class="col-sm-2 control-label text-right">Perfil:</label>
                        <div class="col-sm-10">
                            <select name="id_perfil" id="id_perfil" class="form-control">
                                <?php foreach ($datos['perfiles'] as $perfil) { ?>
                                    <option value="<?php echo $perfil->id_perfil ?>" <?php echo ($perfil->id_perfil == $datos['menu']->id_perfil) ? 'selected' : '' ?>><?php echo $perfil->pe_nombre ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10">
                            <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                            <a href="<?php echo RUTA_URL; ?>/menus" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- </div> -->
            <!-- </div> -->
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('frm-menu');
        $('#mnu_icono').on('blur', function() {
            $('#mostrar-icono').removeClass().addClass('fa fa-fw ' + $(this).val());
        });
    });
</script>