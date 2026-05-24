<div class="content-wrapper">
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
                            <input type="text" class="form-control fuente10" name="in_nombre" id="in_nombre" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_direccion" class="col-sm-2 control-label text-right requerido">Dirección:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente10" name="in_direccion" id="in_direccion" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_telefono" class="col-sm-2 control-label text-right requerido">Teléfono:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control fuente10" name="in_telefono" id="in_telefono" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje3"></span>
                        </div>
                        <label for="in_regimen" class="col-sm-2 control-label">Régimen:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control fuente9 mayusculas" id="in_regimen" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_nom_rector" class="col-sm-2 control-label text-right requerido">Rector (a):</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control fuente10" name="in_nom_rector" id="in_nom_rector" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje5"></span>
                        </div>
                        <label for="in_genero_rector" class="col-sm-2 control-label">Género:</label>
                        <div class="col-sm-4">
                            <select name="in_genero_rector" id="in_genero_rector" class="form-control">
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="in_nom_vicerrector" class="col-sm-2 control-label text-right">Vicerrector (a):</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control fuente10" name="in_nom_vicerrector" id="in_nom_vicerrector" value="">
                            <span class="error" id="mensaje7"></span>
                        </div>
                        <label for="in_genero_vicerrector" class="col-sm-2 control-label">Género:</label>
                        <div class="col-sm-4">
                            <select name="in_genero_vicerrector" id="in_genero_vicerrector" class="form-control">
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="in_nom_secretario" class="col-sm-2 control-label text-right">Secretario (a):</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control fuente10" name="in_nom_secretario" id="in_nom_secretario" value="" onfocus="sel_texto(this)">
                            <span class="error" id="mensaje6"></span>
                        </div>
                        <label for="in_genero_secretario" class="col-sm-2 control-label">Género:</label>
                        <div class="col-sm-4">
                            <select name="in_genero_secretario" id="in_genero_secretario" class="form-control">
                                <option value="F">Femenino</option>
                                <option value="M">Masculino</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="in_url" class="col-sm-2 control-label text-right">URL:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente10" name="in_url" id="in_url" value="" onfocus="sel_texto(this)">
                            <span class="error" id="mensaje8"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_amie" class="col-sm-2 control-label text-right requerido">AMIE:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente10" name="in_amie" id="in_amie" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje9"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_ciudad" class="col-sm-2 control-label text-right requerido">Ciudad:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente10" name="in_ciudad" id="in_ciudad" value="" onfocus="sel_texto(this)" required>
                            <span class="error" id="mensaje10"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="in_copiar_y_pegar" class="col-sm-2 control-label">Copy & Paste:</label>
                        <div class="col-sm-10" style="position: relative; top:7px;">
                            <input type="checkbox" id="in_copiar_y_pegar" name="in_copiar_y_pegar" onclick="actualizar_estado_copiar_y_pegar(this)">
                        </div>
                    </div>
                    <div id="img_upload">
                        <div class="form-group">
                            <label for="img_logo" class="col-sm-2 control-label">Imagen:</label>

                            <div id="img_div" class="col-sm-10">
                                <img id="img_logo" name="img_logo" src="" class="img-thumbnail" width="75" alt="Avatar de la institucion">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="in_logo" class="col-sm-2 control-label" style="margin-top: -4px;">Archivo:</label>
                            <input type="hidden" name="in_logo_file" id="in_logo_file" value="">
                            <div class="col-sm-10">
                                <input type="file" name="in_logo" id="in_logo">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                                Actualizar datos de la Institución
                            </button>
                        </div>
                    </div>
                </form>

                <div id="img_loader" class="text-center" style="display: none;">
                    <img src="public/img/ajax-loader.gif" alt="Procesando..." />
                </div>
                <!-- message -->
                <div id="mensaje" class="fuente9 text-center"></div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>

<script type="text/javascript" src="public/js/funciones.js"></script>
<script>
    $(document).ready(function() {
        $("#img_loader").hide();
        obtenerDatosInstitucion();
        document.getElementById("in_nombre").focus();
    });

    function obtenerDatosInstitucion() {
        $.ajax({
            type: "POST",
            url: "institucion/obtener_datos_institucion.php",
            success: function(resultado) {
                var JSONInstitucion = eval('(' + resultado + ')');
                // console.log(JSONInstitucion);
                //Aqui se van a pintar los datos de la institucion educativa
                document.getElementById("in_nombre").value = (JSONInstitucion.in_nombre) ? JSONInstitucion.in_nombre : "";
                document.getElementById("in_direccion").value = (JSONInstitucion.in_direccion) ? JSONInstitucion.in_direccion : "";
                document.getElementById("in_telefono").value = (JSONInstitucion.in_telefono) ? JSONInstitucion.in_telefono : "";
                document.getElementById("in_regimen").value = (JSONInstitucion.in_regimen) ? JSONInstitucion.in_regimen : "";
                document.getElementById("in_nom_rector").value = (JSONInstitucion.in_nom_rector) ? JSONInstitucion.in_nom_rector : "";

                var obj = document.getElementById("in_genero_rector");

                for (var opcombo = 0; opcombo < obj.length; opcombo++) {
                    if (obj[opcombo].value == JSONInstitucion.in_genero_rector) {
                        obj.selectedIndex = opcombo;
                    }
                }

                document.getElementById("in_nom_secretario").value = (JSONInstitucion.in_nom_secretario) ? JSONInstitucion.in_nom_secretario : "";

                var obj = document.getElementById("in_genero_secretario");

                for (var opcombo = 0; opcombo < obj.length; opcombo++) {
                    if (obj[opcombo].value == JSONInstitucion.in_genero_secretario) {
                        obj.selectedIndex = opcombo;
                    }
                }

                document.getElementById("in_nom_vicerrector").value = (JSONInstitucion.in_nom_vicerrector) ? JSONInstitucion.in_nom_vicerrector : "";

                var obj = document.getElementById("in_genero_vicerrector");

                for (var opcombo = 0; opcombo < obj.length; opcombo++) {
                    if (obj[opcombo].value == JSONInstitucion.in_genero_vicerrector) {
                        obj.selectedIndex = opcombo;
                    }
                }

                document.getElementById("in_url").value = (JSONInstitucion.in_url) ? JSONInstitucion.in_url : "";
                document.getElementById("in_amie").value = (JSONInstitucion.in_amie) ? JSONInstitucion.in_amie : "";
                document.getElementById("in_ciudad").value = (JSONInstitucion.in_ciudad) ? JSONInstitucion.in_ciudad : "";

                document.getElementById("in_copiar_y_pegar").checked = (JSONInstitucion.in_copiar_y_pegar == 1) ? true : false;

                const RUTA = "public/uploads/";
                const img_logo = JSONInstitucion.in_logo == "" ? RUTA + "No_image_available.svg.png" : RUTA + JSONInstitucion.in_logo;

                $("#img_logo").attr("src", img_logo);
                $("#in_logo_file").val(JSONInstitucion.in_logo);

                document.getElementById("in_nombre").focus();
            }
        });
    }

    $("#form_institucion").submit(function(e) {
        e.preventDefault();
        let in_nombre = eliminaEspacios(document.getElementById("in_nombre").value);
        let in_direccion = eliminaEspacios(document.getElementById("in_direccion").value);
        let in_telefono = eliminaEspacios(document.getElementById("in_telefono").value);
        let in_regimen = eliminaEspacios(document.getElementById("in_regimen").value);
        let in_nom_rector = eliminaEspacios(document.getElementById("in_nom_rector").value);
        let in_nom_secretario = eliminaEspacios(document.getElementById("in_nom_secretario").value);
        let in_nom_vicerrector = eliminaEspacios(document.getElementById("in_nom_vicerrector").value);
        let in_url = eliminaEspacios(document.getElementById("in_url").value);
        let in_amie = eliminaEspacios(document.getElementById("in_amie").value);
        let in_ciudad = eliminaEspacios(document.getElementById("in_ciudad").value);
        let in_copiar_y_pegar = document.getElementById("in_copiar_y_pegar").checked ? 1 : 0;
        let in_genero_rector = document.getElementById("in_genero_rector").value;
        let in_genero_secretario = document.getElementById("in_genero_secretario").value;
        let in_genero_vicerrector = document.getElementById("in_genero_vicerrector").value;
        $("#img_loader").show();
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
                if (CurrentFileSize >= 1.5) {
                    swal("¡Error!", "El archivo de imagen debe tener un tamaño máximo de 1.5 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.", "error");
                    cont_errores++;
                    return false;
                }
            }
        }
        $("#mensaje").html("");
        let formData = new FormData(this);
        if (cont_errores == 0) {
            $.ajax({
                url: "institucion/actualizar_datos_institucion.php",
                type: "POST",
                data: {
                    in_nombre: in_nombre,
                    in_direccion: in_direccion,
                    in_telefono: in_telefono,
                    in_regimen: in_regimen,
                    in_nom_rector: in_nom_rector,
                    in_nom_secretario: in_nom_secretario,
                    in_genero_rector: in_genero_rector,
                    in_genero_secretario: in_genero_secretario,
                    in_nom_vicerrector: in_nom_vicerrector,
                    in_genero_vicerrector: in_genero_vicerrector,
                    in_url: in_url,
                    in_amie: in_amie,
                    in_ciudad: in_ciudad,
                    in_copiar_y_pegar: in_copiar_y_pegar
                },
                success: function(resultado) {
                    $("#img_loader").hide();
                    $("#mensaje").html(resultado);
                    $("#form_institucion")[0].reset();
                    obtenerDatosInstitucion();
                }
            });
            $.ajax({
                url: "institucion/actualizar_logo_institucion.php",
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resultado) {
                    console.log(resultado);
                }
            });
        }
    });

    $("#in_logo").change(function() {
        filePreview(this);
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
</script>