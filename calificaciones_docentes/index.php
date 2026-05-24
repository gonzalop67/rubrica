<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAE Web | Login Calificaciones</title>
    <link rel="shortcut icon" type="image/x-icon" href="./../favicon.ico" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="../assets/template/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/template/font-awesome/css/font-awesome.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/template/dist/css/AdminLTE.min.css">

    <!-- Estilos propios de esta pagina -->
    <style type="text/css">
        .error {
            color: #ff0000;
            display: none;
        }

        .rojo {
            color: #ff0000;
        }

        .cover {
            background: 50% 50% no-repeat;
            background-size: cover;
        }

        .blanco {
            color: #ffffff;
        }
    </style>
</head>

<body class="hold-transition login-page cover" style="background: url('../assets/images/loginFont.jpg')">
    <div class="login-box">
        <div class="login-logo blanco">
            <h2>S. I. A. E.</h2>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Introduzca sus datos de ingreso</p>
            <form id="form-login" action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Usuario" id="uname" name="uname" autocomplete="on" autofocus>
                    <span class="form-control-feedback">
                        <img src="../assets/images/if_user_male_172625.png" height="16px" width="16px" alt="icono usuario" />
                    </span>
                    <span class="help-desk error" id="mensaje1">Debe ingresar su nombre de Usuario</span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" id="passwd" name="passwd" autocomplete="on">
                    <span class="form-control-feedback">
                        <img src="../assets/images/if_91_171450.png" height="16px" width="16px" alt="icono password">
                    </span>
                    <span class="help-desk error" id="mensaje2">Debe ingresar su Password</span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-raised btn-danger btn-block" id="btnEnviar">Ingresar</button>
                    </div>
                </div>
            </form>
            <div id="img_loader" style="display:none;text-align:center">
                <img src="../imagenes/ajax-loader6.GIF" alt="Procesando...">
            </div>
            <div id="mensaje" class="error text-center" style="margin-top: 10px">
                <!-- Aqui van los mensajes de error -->
            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <footer style="text-align: center; color: white; margin-top: -15px;">
        .: <span id="nom_institucion"></span> :.
    </footer>

    <!-- jQuery 3 -->
    <script src="../assets/template/jquery/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="../assets/template/bootstrap/js/bootstrap.min.js"></script>
</body>

<script>
    $(document).ready(function() {
        cargar_nombre_institucion();

        $("#form-login").submit(function(event) {
            event.preventDefault();
            nombre = $("#uname").val();
            password = $("#passwd").val();

            if (nombre == "" || password == "") {
                if (nombre == "") {
                    $("#mensaje1").fadeIn("slow");
                } else {
                    $("#mensaje1").fadeOut();
                }
                if (password == "") {
                    $("#mensaje2").fadeIn("slow");
                }
            }

            $("#mensaje").fadeOut();

            $("#img_loader").css("display", "block");

            $.ajax({
                url: "verificar_login.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(resp) {
                    console.log(resp);
                    if (!resp.error) {
                        if (resp.activo == 0) {
                            //Usuario inactivo
                            var error = '<span class="rojo">' +
                                'Usuario inactivo.' +
                                '</span>';
                            $("#img_loader").css("display", "none");
                            $("#mensaje").html(error);
                            $("#mensaje").fadeIn("slow");
                            document.getElementById("uname").focus();
                        } else {
                            //No hay error se redirecciona al admin
                            location.href = "admin2.php?id_usuario=" + resp['id_usuario'];
                        }
                    } else {
                        //No existe el usuario
                        var error = '<span class="rojo">' +
                            'Usuario o password incorrectos.' +
                            '</span>';
                        $("#img_loader").css("display", "none");
                        $("#mensaje").html(error);
                        $("#mensaje").fadeIn("slow");
                        document.getElementById("uname").focus();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Otro manejador error
                    console.log(jqXHR.responseText);
                }
            });

        });
    });

    function cargar_nombre_institucion() {
        $.get("../scripts/cargar_nombre_institucion.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $("#nom_institucion").html(resultado);
                // console.log(resultado);
            }
        });
    }
</script>

</html>