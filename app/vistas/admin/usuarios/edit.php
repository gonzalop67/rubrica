<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Usuarios
        <small>Editar</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-warning">
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
                        <form id="frm_usuario" class="form-horizontal" action="<?php echo RUTA_URL; ?>/usuarios/update" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $datos['usuario']->id_usuario ?>">
                            <div class="form-group">
                                <label for="us_titulo" class="col-sm-2 control-label">Título:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_titulo" id="us_titulo" value="<?php echo $datos['usuario']->us_titulo ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_apellidos" class="col-sm-2 control-label">Apellidos:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_apellidos" id="us_apellidos" value="<?php echo $datos['usuario']->us_apellidos ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_nombres" class="col-sm-2 control-label">Nombres:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_nombres" id="us_nombres" value="<?php echo $datos['usuario']->us_nombres ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_login" class="col-sm-2 control-label">Usuario:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_login" id="us_login" value="<?php echo $datos['usuario']->us_login ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_password" class="col-sm-2 control-label">Password:</label>
                                <div class="col-sm-10">
                                    <?php $clave = encrypter::decrypt($datos['usuario']->us_password) ?>
                                    <input type="text" name="us_password" id="us_password" value="<?php echo $clave ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_genero" class="col-sm-2 control-label">Género:</label>
                                <div class="col-sm-10">
                                    <select name="us_genero" id="us_genero" class="form-control">
                                        <option value="F" <?php echo $datos['usuario']->us_genero == 'F' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="M" <?php echo $datos['usuario']->us_genero == 'M' ? 'selected' : '' ?>>Masculino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_activo" class="col-sm-2 control-label">Activo:</label>
                                <div class="col-sm-10">
                                    <select name="us_activo" id="us_activo" class="form-control">
                                        <option value="1" <?php echo $datos['usuario']->us_activo == 1 ? 'selected' : '' ?>>Sí</option>
                                        <option value="0" <?php echo $datos['usuario']->us_activo == 0 ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_perfil" class="col-sm-2 control-label">Perfil:</label>
                                <div class="col-sm-10">
                                    <?php foreach ($datos['perfiles'] as $v) : ?>
                                        <div>
                                            <input type="checkbox" name="id_perfil[]" value="<?= $v->id_perfil ?>" <?= (in_array($v->id_perfil, $datos['usuarios_perfil'])) ? 'checked' : '' ?>>
                                            <?= $v->pe_nombre ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div id="img_upload">
                                <div class="form-group">
                                    <label for="us_avatar" class="col-sm-2 control-label"></label>

                                    <div id="img_div" class="col-sm-10">
                                        <?php $us_foto = $datos['usuario']->us_foto == '' ? 'no-disponible.png' : $datos['usuario']->us_foto ?>
                                        <img id="us_avatar" name="us_avatar" src="<?php echo RUTA_URL . '/public/uploads/' . $us_foto ?>" class="img-thumbnail" width="75" alt="Avatar del usuario">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="us_foto" class="col-sm-2 control-label" style="margin-top: -4px;">Imagen:</label>
                                    <input type="hidden" name="us_foto_file" id="us_foto_file" value="<?php echo $datos['usuario']->us_foto ?>">
                                    <div class="col-sm-10">
                                        <input type="file" name="us_foto" id="us_foto">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                    <a href="<?php echo RUTA_URL; ?>/usuarios" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                                </div>
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
        $("#us_foto").change(function() {
            $("#img_div").removeClass("hide");
            filePreview(this);
        });
    });

    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                $("#us_avatar").attr("src", e.target.result);
            }
        }
    }
</script>