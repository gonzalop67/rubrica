<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Datos de la Institución Educativa</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form_institucion" action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="in_nombre" class="col-sm-2 control-label text-right requerido">Nombre:</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente10" name="in_nombre" id="in_nombre" value="<?php echo $datos['institucion']->in_nombre ?>" onfocus="sel_texto(this)" required>
                        <span class="error" id="mensaje1"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_direccion" class="col-sm-2 control-label text-right requerido">Dirección:</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente10" name="in_direccion" id="in_direccion" value="<?php echo $datos['institucion']->in_direccion ?>" required>
                        <span class="error" id="mensaje2"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_telefono" class="col-sm-2 control-label text-right requerido">Teléfono:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control fuente10" name="in_telefono" id="in_telefono" value="<?php echo $datos['institucion']->in_telefono ?>" required>
                        <span class="error" id="mensaje3"></span>
                    </div>

                    <label for="in_regimen" class="col-sm-2 control-label">Régimen:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control fuente9 mayusculas" name="in_regimen" id="in_regimen" value="<?php echo $datos['institucion']->in_regimen ?>" onfocus="sel_texto(this)">
                        <span class="help-desk error" id="mensaje4"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_nom_rector" class="col-sm-2 control-label text-right requerido">Rector (a):</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control fuente10" name="in_nom_rector" id="in_nom_rector" value="<?php echo $datos['institucion']->in_nom_rector ?>" required>
                        <span class="error" id="mensaje5"></span>
                    </div>

                    <label for="in_genero_rector" class="col-sm-2 control-label">Género:</label>
                    <div class="col-sm-4">
                        <select name="in_genero_rector" id="in_genero_rector" class="form-control">
                            <option value="F" <?php echo $datos['institucion']->in_genero_rector == "F" ? 'selected' : '' ?>>Femenino</option>
                            <option value="M" <?php echo $datos['institucion']->in_genero_rector == "M" ? 'selected' : '' ?>>Masculino</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_nom_vicerrector" class="col-sm-2 control-label text-right">Vicerrector (a):</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control fuente10" name="in_nom_vicerrector" id="in_nom_vicerrector" value="<?php echo $datos['institucion']->in_nom_vicerrector ?>">
                        <span class="error" id="mensaje6"></span>
                    </div>

                    <label for="in_genero_vicerrector" class="col-sm-2 control-label">Género:</label>
                    <div class="col-sm-4">
                        <select name="in_genero_vicerrector" id="in_genero_vicerrector" class="form-control">
                            <option value="F" <?php echo $datos['institucion']->in_genero_vicerrector == "F" ? 'selected' : '' ?>>Femenino</option>
                            <option value="M" <?php echo $datos['institucion']->in_genero_vicerrector == "M" ? 'selected' : '' ?>>Masculino</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_nom_secretario" class="col-sm-2 control-label text-right">Secretario (a):</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control fuente10" name="in_nom_secretario" id="in_nom_secretario" value="<?php echo $datos['institucion']->in_nom_secretario ?>">
                        <span class="error" id="mensaje5"></span>
                    </div>

                    <label for="in_genero_secretario" class="col-sm-2 control-label">Género:</label>
                    
                    <div class="col-sm-4">
                        <select name="in_genero_secretario" id="in_genero_secretario" class="form-control">
                            <option value="F" <?php echo $datos['institucion']->in_genero_secretario == "F" ? 'selected' : '' ?>>Femenino</option>
                            <option value="M" <?php echo $datos['institucion']->in_genero_secretario == "M" ? 'selected' : '' ?>>Masculino</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_url" class="col-sm-2 control-label text-right">URL:</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente10" name="in_url" id="in_url" value="<?php echo $datos['institucion']->in_url ?>">
                        <span class="error" id="mensaje7"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_amie" class="col-sm-2 control-label text-right requerido">AMIE:</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente10" name="in_amie" id="in_amie" value="<?php echo $datos['institucion']->in_amie ?>" required>
                        <span class="error" id="mensaje8"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_ciudad" class="col-sm-2 control-label text-right requerido">Ciudad:</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente10" name="in_ciudad" id="in_ciudad" value="<?php echo $datos['institucion']->in_ciudad ?>" required>
                        <span class="error" id="mensaje9"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="in_copiar_y_pegar" class="col-sm-2 control-label">Copy & Paste:</label>
                    <div class="col-sm-10" style="position: relative; top:7px;">
                        <input type="checkbox" id="in_copiar_y_pegar" name="in_copiar_y_pegar" <?php echo ($datos['institucion']->in_copiar_y_pegar == 1) ? "checked" : "" ?> onclick="actualizar_estado_copiar_y_pegar(this)">
                    </div>
                </div>
                <div id="img_upload">
                    <div class="form-group">
                        <label for="img_logo" class="col-sm-2 control-label">Imagen:</label>

                        <div id="img_div" class="col-sm-10">
                            <?php $in_logo = $datos['institucion']->in_logo == '' ? 'no-disponible.png' : $datos['institucion']->in_logo ?>
                            <img id="img_logo" name="img_logo" src="<?php echo RUTA_URL . '/public/uploads/' . $in_logo ?>" class="img-thumbnail" width="75" alt="Avatar de la institucion">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_logo" class="col-sm-2 control-label" style="margin-top: -4px;">Archivo:</label>
                        <input type="hidden" name="in_logo_file" id="in_logo_file" value="<?php echo $datos['institucion']->in_logo ?>">
                        <div class="col-sm-10">
                            <input type="file" name="in_logo" id="in_logo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="margin-top: 4px;">
                        <button id="btn-add-item" type="submit" class="btn btn-block" style="background-color: royalblue; color: white;">
                            Actualizar Información de la Institución Educativa
                        </button>
                    </div>
                </div>
            </form>

            <div id="img_loader" class="text-center" style="display: none;"> <img src="<?php echo RUTA_URL ?>/public/img/ajax-loader.gif" alt="Procesando..." /> </div>
            <!-- message -->
            <div id="mensaje" class="fuente9 text-center"></div>
        </div>
    </div>
</section>
<!-- /.content -->

<script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/funciones.js"></script>
<script>
    $(document).ready(function() {
        document.getElementById("in_nombre").focus();

        $("#in_logo").change(function() {
            filePreview(this);
        });

        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });

        $("#form_institucion").submit(function(e) {
            e.preventDefault();
            //Insertar o Actualizar los datos de la IE
            let in_nombre = document.getElementById("in_nombre").value;
            let in_direccion = document.getElementById("in_direccion").value;
            let in_telefono = document.getElementById("in_telefono").value;
            let in_regimen = document.getElementById("in_regimen").value;
            let in_nom_rector = document.getElementById("in_nom_rector").value;
            let in_genero_rector = document.getElementById("in_genero_rector").value;
            let in_nom_secretario = document.getElementById("in_nom_secretario").value;
            let in_genero_secretario = document.getElementById("in_genero_secretario").value;
            let in_nom_vicerrector = document.getElementById("in_nom_vicerrector").value;
            let in_url = document.getElementById("in_url").value;
            let in_amie = document.getElementById("in_amie").value;
            let in_ciudad = document.getElementById("in_ciudad").value;
            let in_copiar_y_pegar = document.getElementById("in_copiar_y_pegar").checked ? 1 : 0;

            let cont_errores = 0;

            var img = document.forms['form_institucion']['in_logo'];
            var validExt = ["jpeg", "png", "jpg", "gif", "JPEG", "JPG", "PNG", "GIF"];

            if (img.value != '') {
                var img_ext = img.value.substring(img.value.lastIndexOf('.') + 1);

                var result = validExt.includes(img_ext);

                if (result == false) {
                    swal("¡Error!", "Debe cargar un archivo .jpg o .jpeg o .png", "error");
                    cont_errores++;
                    return false;
                } else {
                    var CurrentFileSize = parseFloat(img.files[0].size / (1024 * 1024));
                    if (CurrentFileSize >= 1) {
                        swal("¡Error!", "El archivo de imagen debe tener un tamaño máximo de 1 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.", "error");
                        cont_errores++;
                        return false;
                    }
                }
            }

            $("#img_loader").show();
            $("#mensaje").html("");

            var data = new FormData(this);

            if (cont_errores == 0) {
                $.ajax({
                    url: "<?php echo RUTA_URL ?>/instituciones/actualizar",
                    type: "POST",
                    contentType: false,
                    cache: false,
                    data: data,
                    processData: false,
                    dataType: "html",
                    success: function(resultado) {
                        console.log(resultado);
                        $("#img_loader").hide();
                        // $("#mensaje").html(resultado);
                        Swal.fire({
                            title: "Logrado",
                            text: "Se han actualizado los datos de la Institución Educativa con éxito...",
                            icon: "success"
                        });
                        document.getElementById("in_nombre").focus();
                    }
                });
            }
        });
    });

    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                $("#img_logo").attr("src", e.target.result);
            }
        }
    }

    function actualizar_estado_copiar_y_pegar(obj) {
        if (obj.checked) estado_copiar_y_pegar = "1";
        else estado_copiar_y_pegar = "0";
        $.ajax({
            type: "POST",
            url: "<?= RUTA_URL ?>/instituciones/actualizar_copiar_y_pegar",
            data: "in_copiar_y_pegar=" + estado_copiar_y_pegar,
            dataType: "json",
            success: function(response) {
                console.log(response.mensaje);
                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
            }
        });
    }
</script>