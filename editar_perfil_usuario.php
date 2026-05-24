<?php
if (!isset($_SESSION['usuario_logueado']) or !$_SESSION['usuario_logueado'])
    header("Location: index.php");
else {
    if (!isset($_SESSION['id_usuario']) or !$_SESSION['id_usuario'])
        header("Location: index.php");
    else {
        require_once("scripts/clases/class.mysql.php");
        require_once("scripts/clases/class.usuarios.php");
        require_once("scripts/clases/class.encrypter.php");
        $usuario = new usuarios();
        $usuarios = $usuario->obtenerUsuario($_SESSION['id_usuario']);
        $us_fullname = $usuarios->us_fullname;
        $password = encrypter::decrypt($usuarios->us_password);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <h1>
            Editar Perfil de Usuario
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="<?php echo $userImage ?>" alt="User profile picture">
                        <h3 class="profile-username text-center"><?php echo $nombreUsuario ?></h3>
                        <p class="text-muted text-center"><?php echo $nombrePerfil ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#datos" data-toggle="tab">Datos</a></li>
                        <li><a href="#contrasenia" data-toggle="tab">Contraseña</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="datos">
                            <div class="row">
                                <div class="col-md-12">
                                    <form id="form_update" action="usuario/actualizar_perfil_usuario.php" method="post" enctype="multipart/form-data" autocomplete="off">
                                        <input type="hidden" name="edit_id_usuario" id="edit_id_usuario" value="<?php echo $id_usuario ?>">
                                        <div class="fuente9">
                                            <div class="form-group row">
                                                <label for="edit_us_titulo" class="col-md-2 col-form-label text-right">Título:</label>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" id="edit_us_titulo" name="edit_us_titulo" placeholder="Abreviatura..." value="" required>
                                                    <span id="error-edit_us_titulo" style="color: #e73d4a"></span>
                                                </div>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" id="edit_us_titulo_descripcion" name="edit_us_titulo_descripcion" placeholder="Ingrese el Título del Usuario..." value="" required>
                                                    <span id="error-edit_us_titulo_descripcion" style="color: #e73d4a"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="edit_us_apellidos" class="col-md-2 col-form-label text-right">Apellidos:</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" id="edit_us_apellidos" name="edit_us_apellidos" placeholder="Ingrese los Apellidos del Usuario..." value="" required>
                                                    <span id="error-edit_us_apellidos" style="color: #e73d4a"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="edit_us_nombres" class="col-md-2 col-form-label text-right">Nombres:</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" id="edit_us_nombres" name="edit_us_nombres" placeholder="Ingrese los Nombres del Usuario..." value="" required>
                                                    <span id="error-edit_us_nombres" style="color: #e73d4a"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="edit_us_genero" class="col-md-2 col-form-label text-right">Género:</label>
                                                <div class="col-md-3">
                                                    <select class="form-control" id="edit_us_genero" name="edit_us_genero">
                                                        <option value="M">Masculino</option>
                                                        <option value="F">Femenino</option>
                                                    </select>
                                                    <span id="error-edit_us_genero" style="color: #e73d4a"></span>
                                                </div>
                                                <label for="edit_us_email" class="col-md-1 col-form-label">Email:</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="edit_us_email" name="edit_us_email" placeholder="Ingrese el Email del Usuario..." value="" required>
                                                    <span id="error-edit_us_email" style="color: #e73d4a"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="edit_us_foto" class="col-md-2 control-label text-right">Imagen:</label>
                                                <div id="edit_img_div" class="col-md-3">
                                                    <img id="edit_us_foto" name="edit_us_foto" class="img-thumbnail" width="75" src="<?php echo $userImage ?>" alt="Avatar del Usuario">
                                                    <input type="hidden" name="bd_us_avatar" id="bd_us_avatar">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="us_foto" class="col-sm-2 control-label"></label>

                                                <div class="col-sm-10">
                                                    <input type="file" name="us_foto" id="us_foto">
                                                    <span id="error-us_foto" style="color: #e73d4a;"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-offset-2 col-md-10">
                                                    <button type="submit" class="btn btn-success btn-md"><i class="fa fa-pencil"></i> Actualizar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="message" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="contrasenia">
                            <!-- Default box -->
                            <div class="box box-solid">
                                <div class="box-body">
                                    <div class="login-box-body">
                                        <div class="row">
                                            <div class="col-md-6 col-md-offset-3">
                                                <p class="text-center">Use el formulario de abajo para cambiar su clave. Su clave no puede ser la misma que su nombre de usuario.</p>
                                                <form action="scripts/actualizar_clave.php" method="post" id="passwordForm">
                                                    <input type="hidden" name="fullname" id="fullname" value="<?php echo $us_fullname ?>">
                                                    <input type="hidden" name="bdpassword" id="bdpassword" value="<?php echo $password ?>">
                                                    <input type="password" class="input-lg form-control" name="password" id="password" placeholder="Clave Actual" autocomplete="off" autofocus>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span id="bdpwdmatch" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Coincide la Clave Actual
                                                        </div>
                                                    </div>
                                                    <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="Clave Nueva" autocomplete="off">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span id="8char" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> 8 Caracteres de Longitud<br>
                                                            <span id="ucase" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Una Letra May&uacute;scula
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span id="lcase" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Una Letra Min&uacute;scula<br>
                                                            <span id="num" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Un N&uacute;mero
                                                        </div>
                                                    </div>
                                                    <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Redigite la Clave Nueva" autocomplete="off">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span id="pwmatch" class="glyphicon glyphicon-remove" style="color:#FF0004;"></span> Claves Coincidentes
                                                        </div>
                                                    </div>
                                                    <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" data-loading-text="Cambiando la Clave..." value="Cambiar la Clave">
                                                </form>
                                                <span id="mensaje" class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>

<script>
    var b_minlenchar = false;
    var b_ucase = false;
    var b_lcase = false;
    var b_num = false;
    var b_pwmatch = false;
    var b_bdpwdmatch = false;

    var bdpassword = $("#bdpassword").val();

    $(document).ready(function() {
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

        $("#us_foto").change(function() {
            $("#edit_img_div").removeClass("hide");
            filePreview(this);
        });

        $("#form_update").submit(function(e) {

            e.preventDefault();

            const id_usuario = $("#edit_id_usuario").val();
            const us_titulo = $("#edit_us_titulo").val().trim();
            const us_titulo_descripcion = $("#edit_us_titulo_descripcion").val().trim();
            const us_apellidos = $("#edit_us_apellidos").val().trim();
            const us_nombres = $("#edit_us_nombres").val().trim();
            const us_genero = $("#edit_us_genero").val().trim();
            const us_email = $("#edit_us_email").val().trim();

            const reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;

            let cont_errores = 0;

            if (us_titulo === '') {
                $("#error-edit_us_titulo").html("Debe ingresar la abreviatura del Título.");
                $("#error-edit_us_titulo").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_titulo").fadeOut();
            }

            if (us_titulo_descripcion === '') {
                $("#error-edit_us_titulo_descripcion").html("Debe ingresar la descripción del Título.");
                $("#error-edit_us_titulo_descripcion").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_titulo_descripcion").fadeOut();
            }

            if (us_apellidos === '') {
                $("#error-edit_us_apellidos").html("Debe ingresar los Apellidos del Usuario.");
                $("#error-edit_us_apellidos").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_apellidos").fadeOut();
            }

            if (us_nombres === '') {
                $("#error-edit_us_nombres").html("Debe ingresar los Nombres del Usuario.");
                $("#error-edit_us_nombres").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_nombres").fadeOut();
            }

            if ($("#edit_us_genero").val() === '') {
                $("#error-edit_us_genero").html("Debe seleccionar el género del usuario.");
                $("#error-edit_us_genero").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_genero").fadeOut();
            }

            if (us_email.length != 0 && !reg_email.test(us_email)) {
                $("#error-edit_us_email").html("Dirección de correo electrónico no válida.");
                $("#error-edit_us_email").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_email").fadeOut();
            }

            var img = document.forms['form_update']['us_foto'];
            var validExt = ["jpeg", "png", "jpg", "JPEG", "JPG", "PNG"];

            if (img.value != '') {
                var img_ext = img.value.substring(img.value.lastIndexOf('.') + 1);
                var result = validExt.includes(img_ext);

                if (result == false) {
                    $("#error-us_foto").html("Debe cargar un archivo de imagen.");
                    $("#error-us_foto").fadeIn();
                    cont_errores++;
                } else {
                    $("#error-us_foto").fadeOut();
                }

                var CurrentFileSize = parseFloat(img.files[0].size / (1024 * 1024));

                if (CurrentFileSize >= 1) {
                    $("#error-us_foto").html("El archivo de imagen debe tener un tamaño máximo de 1 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.");
                    $("#error-us_foto").fadeIn();
                    cont_errores++;
                } else {
                    $("#error-us_foto").fadeOut();
                }
            }

            if (cont_errores == 0) {
                // submit el formulario
                var data = new FormData($("#form_update")[0]);

                $.ajax({
                    url: "scripts/actualizar_perfil_usuario.php",
                    method: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        $("#message").html("Reinicio de sesión requerido para aplicar los cambios. Por favor, cierre sesión y vuelva a iniciar sesión para ver los cambios reflejados.");
                        Swal.fire({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });

        $("#passwordForm").submit(function(e) {
            e.preventDefault();
            var url = $(this).attr("action");
            var password = $("#password").val();

            if (!b_minlenchar) {
                $("#mensaje").html("La nueva clave debe contener al menos 8 caracteres.");
                return false;
            } else if (!b_ucase) {
                $("#mensaje").html("La nueva clave debe contener al menos una letra may&uacute;scula.");
                return false;
            } else if (!b_lcase) {
                $("#mensaje").html("La nueva clave debe contener al menos una letra min&uacute;scula.");
                return false;
            } else if (!b_num) {
                $("#mensaje").html("La nueva clave debe contener al menos un n&uacute;mero.");
                return false;
            } else if (!b_pwmatch) {
                $("#mensaje").html("La nueva clave y redigitada no coinciden.");
                return false;
            } else if (!b_bdpwdmatch) {
                $("#mensaje").html("La Clave Actual no coincide con la clave guardada en el sistema.");
                return false;
            }

            $("#mensaje").html("");
            // Si pasa todas las validaciones procedemos a actualizar la nueva clave en la base de datos
            $("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='Cargando...' /> Actualizando la clave, por favor espere...");
            $.post(url, $(this).serialize(), function(resp) {
                if (!resp.error) {
                    $("#mensaje").removeClass("error");
                    $("#mensaje").addClass("success");
                } else {
                    $("#mensaje").removeClass("success");
                    $("#mensaje").addClass("error");
                }
                $("#mensaje").html(resp.mensaje);
            }, 'json');

        });

        $("input[type=password]").keyup(function() {
            var ucase = new RegExp("[A-Z]+");
            var lcase = new RegExp("[a-z]+");
            var num = new RegExp("[0-9]+");

            var passwd = $("#password").val();

            if (passwd == bdpassword) {
                $("#bdpwdmatch").removeClass("glyphicon-remove");
                $("#bdpwdmatch").addClass("glyphicon-ok");
                $("#bdpwdmatch").css("color", "#00A41E");
                b_bdpwdmatch = true;
            } else {
                $("#bdpwdmatch").removeClass("glyphicon-ok");
                $("#bdpwdmatch").addClass("glyphicon-remove");
                $("#bdpwdmatch").css("color", "#FF0004");
                b_bdpwdmatch = false;
            }

            if ($("#password1").val().length >= 8) {
                $("#8char").removeClass("glyphicon-remove");
                $("#8char").addClass("glyphicon-ok");
                $("#8char").css("color", "#00A41E");
                b_minlenchar = true;
            } else {
                $("#8char").removeClass("glyphicon-ok");
                $("#8char").addClass("glyphicon-remove");
                $("#8char").css("color", "#FF0004");
                b_minlenchar = false;
            }

            if (ucase.test($("#password1").val())) {
                $("#ucase").removeClass("glyphicon-remove");
                $("#ucase").addClass("glyphicon-ok");
                $("#ucase").css("color", "#00A41E");
                b_ucase = true;
            } else {
                $("#ucase").removeClass("glyphicon-ok");
                $("#ucase").addClass("glyphicon-remove");
                $("#ucase").css("color", "#FF0004");
                b_ucase = false;
            }

            if (lcase.test($("#password1").val())) {
                $("#lcase").removeClass("glyphicon-remove");
                $("#lcase").addClass("glyphicon-ok");
                $("#lcase").css("color", "#00A41E");
                b_lcase = true;
            } else {
                $("#lcase").removeClass("glyphicon-ok");
                $("#lcase").addClass("glyphicon-remove");
                $("#lcase").css("color", "#FF0004");
                b_lcase = false;
            }

            if (num.test($("#password1").val())) {
                $("#num").removeClass("glyphicon-remove");
                $("#num").addClass("glyphicon-ok");
                $("#num").css("color", "#00A41E");
                b_num = true;
            } else {
                $("#num").removeClass("glyphicon-ok");
                $("#num").addClass("glyphicon-remove");
                $("#num").css("color", "#FF0004");
                b_num = false;
            }

            if ($("#password1").val() != "" && $("#password1").val() == $("#password2").val()) {
                $("#pwmatch").removeClass("glyphicon-remove");
                $("#pwmatch").addClass("glyphicon-ok");
                $("#pwmatch").css("color", "#00A41E");
                b_pwmatch = true;
            } else {
                $("#pwmatch").removeClass("glyphicon-ok");
                $("#pwmatch").addClass("glyphicon-remove");
                $("#pwmatch").css("color", "#FF0004");
                b_pwmatch = false;
            }
        });

        // Cargar los datos del usuario al abrir la página
        $.ajax({
            url: 'usuarios/obtener_usuario.php',
            type: 'POST',
            data: {
                id_usuario: $('#edit_id_usuario').val()
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data); // Verificar la estructura de los datos recibidos
                // Llenar los campos del formulario con los datos del usuario
                $('#edit_us_titulo').val(data.us_titulo);
                $('#edit_us_titulo_descripcion').val(data.us_titulo_descripcion);
                $('#edit_us_apellidos').val(data.us_apellidos);
                $('#edit_us_nombres').val(data.us_nombres);
                $('#edit_us_email').val(data.us_email);
                $("#bd_us_avatar").val(data.us_foto);
            },
            error: function() {
                alert('Error al cargar los datos del usuario.');
            }
        });
    });

    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                $("#edit_us_foto").attr("src", e.target.result);
            }
        }
    }
</script>