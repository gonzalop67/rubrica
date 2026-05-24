<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Usuarios
        <small>Crear</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-body">
                        <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($datos['mensaje_error']) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($datos['mensaje_error']) ? $datos['mensaje_error'] : '' ?></span></p>
                        </div>
                        <div id="alert-exito" class="alert alert-success alert-dismissible" style="display:none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"></span></p>
                        </div>
                        <form id="frm_usuario" class="form-horizontal" action="<?php echo RUTA_URL; ?>/usuarios/insert" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="us_titulo" class="col-sm-2 control-label">Título:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_titulo" id="us_titulo" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_titulo_descripcion" class="col-sm-2 control-label">Descripción del Título:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_titulo_descripcion" id="us_titulo_descripcion" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_apellidos" class="col-sm-2 control-label">Apellidos:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_apellidos" id="us_apellidos" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_nombres" class="col-sm-2 control-label">Nombres:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_nombres" id="us_nombres" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_login" class="col-sm-2 control-label">Usuario:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_login" id="us_login" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_password" class="col-sm-2 control-label">Password:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="us_password" id="us_password" value="" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_genero" class="col-sm-2 control-label">Género:</label>
                                <div class="col-sm-10">
                                    <select name="us_genero" id="us_genero" class="form-control">
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="us_activo" class="col-sm-2 control-label">Activo:</label>
                                <div class="col-sm-10">
                                    <select name="us_activo" id="us_activo" class="form-control">
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_perfil" class="col-sm-2 control-label">Perfil:</label>
                                <div class="col-sm-10">
                                    <!-- <select name="id_perfil[]" id="id_perfil" class="form-control" multiple size="7">
                                        <?php foreach ($datos['perfiles'] as $perfil) : ?>
                                            <option value="<?= $perfil->id_perfil; ?>"><?= $perfil->pe_nombre; ?></option>
                                        <?php endforeach; ?>
                                    </select> -->
                                    <?php foreach ($datos['perfiles'] as $v) : ?>
                                        <div>
                                            <input type="checkbox" name="id_perfil[]" value="<?= $v->id_perfil ?>">
                                            <?= $v->pe_nombre ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div id="img_upload">
                                <div class="form-group">
                                    <label for="us_avatar" class="col-sm-2 control-label"></label>

                                    <div id="img_div" class="col-sm-10 hide">
                                        <img id="us_avatar" name="us_avatar" class="img-thumbnail" width="75" alt="Avatar del usuario">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="us_foto" class="col-sm-2 control-label" style="margin-top: -4px;">Imagen:</label>

                                    <div class="col-sm-10">
                                        <input type="file" name="us_foto" id="us_foto">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
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

        $("#frm_usuario").on("submit", function(e) {
            e.preventDefault();

            let cont_errores = 0;
            const url = $(this).attr("action");

            // Validar los campos de entrada
            const formulario = document.getElementById("frm_usuario");
            const inputs = document.querySelectorAll("#frm_usuario input");

            return true;
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