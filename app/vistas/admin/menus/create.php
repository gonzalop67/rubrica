<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Menús
        <small>Crear</small>
    </h1>
</section>
<section class="content">
    <!-- Default box -->
    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-body">
                        <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"></span></p>
                        </div>
                        <div id="alert-exito" class="alert alert-success alert-dismissible" style="display:none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"></span></p>
                        </div>
                        <form id="frm-menu" action="<?php echo RUTA_URL; ?>/menus/insert" method="post">
                            <div class="form-group">
                                <label for="mnu_texto">Texto:</label>
                                <input type="text" name="mnu_texto" id="mnu_texto" value="" class="form-control" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="mnu_link">Enlace:</label>
                                <input type="text" name="mnu_link" id="mnu_link" value="" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="mnu_publicado">Publicado:</label>
                                <select name="mnu_publicado" id="mnu_publicado" class="form-control">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_perfil">Perfil:</label>
                                <select name="id_perfil" id="id_perfil" class="form-control">
                                    <?php foreach ($datos['perfiles'] as $perfil) { ?>
                                        <option value="<?php echo $perfil->id_perfil ?>"><?php echo $perfil->pe_nombre ?></option>    
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                <a href="<?php echo RUTA_URL; ?>/menus" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('frm-menu');
    });
</script>