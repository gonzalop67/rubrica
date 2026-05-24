<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo NOMBRESITIO . $datos['titulo'] ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo RUTA_URL ?>/img/favicon.ico" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/assets/template/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/assets/template/font-awesome/css/font-awesome.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo RUTA_URL ?>/assets/template/dist/css/AdminLTE.min.css">

    <!-- Estilos propios de esta pagina -->
    <style type="text/css">
        .error {
            color: #F00;
            display: none;
        }

        .blanco {
            color: #FFF;
        }
    </style>

</head>

<body class="hold-transition login-page" style="background: url('<?php echo RUTA_URL ?>/assets/images/loginFont.jpg')">
    <div class="login-box blanco">
        <div class="login-logo">
            <h2>S. I. A. E.</h2>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Introduzca sus datos de ingreso</p>
            <form id="form-login" action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Usuario" id="uname" name="uname" autocomplete="on" autofocus>
                    <span class="form-control-feedback">
                        <img src="assets/images/if_user_male_172625.png" height="16px" width="16px">
                    </span>
                    <span class="error" id="mensaje1">Debe ingresar su nombre de Usuario</span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" id="passwd" name="passwd" autocomplete="on">
                    <span class="form-control-feedback">
                        <img src="assets/images/if_91_171450.png" height="16px" width="16px">
                    </span>
                    <span class="error" id="mensaje2">Debe ingresar su Password</span>
                </div>
                <div class="form-group has-feedback">
                    <select class="form-control" id="cboPerfil" name="cboPerfil">
                        <option value="">Seleccione su perfil...</option>
                        <?php foreach ($datos['perfiles'] as $perfil) : ?>
                            <option value="<?php echo $perfil->id_perfil; ?>"><?php echo $perfil->pe_nombre; ?></option>
                        <?php endforeach ?>
                    </select>
                    <span class="error" id="mensaje4">Debe seleccionar su perfil</span>
                </div>
                <div class="form-group has-feedback">
                    <select class="form-control" id="cboPeriodo" name="cboPeriodo">
                        <option value="">Seleccione el periodo lectivo...</option>
                        <?php
                        $meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
                        $modalidadModelo = $this->modelo('Modalidad');
                        $modalidades = $modalidadModelo->obtenerModalidades();
                        $periodoLectivoModelo = $this->modelo('PeriodoLectivo');
                        foreach ($modalidades as $modalidad) {
                            $code = $modalidad->id_modalidad;
                            $name = $modalidad->mo_nombre;
                        ?>
                            <optgroup label='<?php echo $name; ?>'>
                                <?php $periodos = $periodoLectivoModelo->obtenerPeriodosL($code);
                                foreach ($periodos as $periodo) {
                                    $code2 = $periodo->id_periodo_lectivo;
                                    $fecha_inicial = explode("-", $periodo->pe_fecha_inicio);
                                    $fecha_final = explode("-", $periodo->pe_fecha_fin);
                                    $name2 = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0] . " [" . $periodo->pe_descripcion . "]";
                                ?>
                                    <option value="<?php echo $code2; ?>"><?php echo $name2; ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                    <span class="error" id="mensaje3">Debe seleccionar el periodo lectivo</span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-raised btn-danger btn-block" id="btnEnviar">Ingresar</button>
                    </div>
                </div>
            </form>
            <div id="img_loader" style="display:none;text-align:center">
                <img src="<?php echo RUTA_URL ?>/img/ajax-loader6.GIF" alt="Procesando...">
            </div>
            <div id="mensaje" class="error">
                <!-- Aqui van los mensajes de error -->
            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <footer style="text-align: center; color: white; margin-top: -40px;">
        .: &copy; <?php echo date("  Y"); ?> - <?php echo $datos['nombreInstitucion']; ?> :.
    </footer>

    <!-- jQuery 3 -->
    <script src="<?php echo RUTA_URL ?>/assets/template/jquery/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#form-login").submit(function(event) {
                event.preventDefault();
                nombre = $("#uname").val().trim();
                password = $("#passwd").val().trim();
                periodo = $("#cboPeriodo").val().trim();
                perfil = $("#cboPerfil").val().trim();

                if (nombre == "" || password == "" || periodo == "" || perfil == "") {
                    if (nombre == "") {
                        $("#mensaje1").fadeIn("slow");
                    } else {
                        $("#mensaje1").fadeOut();
                    }
                    if (password == "") {
                        $("#mensaje2").fadeIn("slow");
                    } else {
                        $("#mensaje2").fadeOut();
                    }
                    if (perfil == "") {
                        $("#mensaje4").fadeIn("slow");
                    } else {
                        $("#mensaje4").fadeOut();
                    }
                    if (periodo == "") {
                        $("#mensaje3").fadeIn("slow");
                    } else {
                        $("#mensaje3").fadeOut();
                    }
                    return false;
                }

                $("#mensaje").fadeOut();
                $("#mensaje1").fadeOut();
                $("#mensaje2").fadeOut();
                $("#mensaje3").fadeOut();
                $("#mensaje4").fadeOut();

                $("#img_loader").css("display", "block");

                $.ajax({
                    url: "<?php echo RUTA_URL ?>/auth/login",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(resp) {
                        console.log('response: ' + resp);
                        if (!resp.error) {

                            //No hay error se redirecciona al dashboard correspondiente
                            alert(resp.pe_nombre);
                            
                            location.href = "<?php echo RUTA_URL ?>/auth/dashboard";

                        } else {

                            //No existe el usuario
                            var error = '<span class="rojo">' +
                                'Usuario o password o perfil incorrectos.' +
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
    </script>

</body>

</html>