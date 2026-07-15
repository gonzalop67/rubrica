<?php 
session_start(); 
$_SESSION = array(); // Vacía el array de sesión 
session_destroy(); // Destruye la sesión en el servidor 
?> 
<!DOCTYPE html> 
<html> 
<head> 
    <meta charset="utf-8"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <title>SIAE Web 2 | Log in</title> 
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" /> 
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> 
    
    <!-- Bootstrap 3.3.7 --> 
    <link rel="stylesheet" href="assets/template/bootstrap/css/bootstrap.min.css"> 
    <!-- Font Awesome --> 
    <link rel="stylesheet" href="assets/template/font-awesome/css/font-awesome.min.css"> 
    <!-- Theme style --> 
    <link rel="stylesheet" href="assets/template/dist/css/AdminLTE.min.css"> 
    
    <style type="text/css"> 
        .error { color: #ff0000; display: none; } 
        .rojo { color: #ff0000; } 
        .cover{ background: 50% 50% no-repeat; background-size: cover; } 
        .blanco { color: #ffffff; } 
        .opaco { height: 100%; width: 100%; background-color: #000a; } 
    </style> 
</head> 
<body class="hold-transition login-page cover" style="background: url('./assets/images/loginFont.jpg')"> 
<div class="login-box"> 
    <div class="login-logo blanco"> 
        <h2>S. I. A. E.</h2> 
    </div> 
    
    <div class="login-box-body"> 
        <p class="login-box-msg">Introduzca sus datos de ingreso</p> 
        
        <form id="form-login" action="" method="post" autocomplete="off"> 
            <div class="form-group has-feedback"> 
                <input type="text" class="form-control" placeholder="Usuario" id="uname" name="uname" autocomplete="username" autofocus> 
                <span class="form-control-feedback"> 
                    <img src="assets/images/if_user_male_172625.png" height="16px" width="16px"> 
                </span> 
                <span class="help-desk error" id="error-uname">Debe ingresar su nombre de Usuario</span> 
            </div> 
            
            <div class="form-group has-feedback"> 
                <input type="password" class="form-control" placeholder="Password" id="passwd" name="passwd" autocomplete="current-password"> 
                <span class="form-control-feedback"> 
                    <img src="assets/images/if_91_171450.png" height="16px" width="16px"> 
                </span> 
                <span class="help-desk error" id="error-passwd">Debe ingresar su Password</span> 
            </div> 
            
            <div class="form-group has-feedback"> 
                <select class="form-control" id="cboPeriodo" name="cboPeriodo"> 
                    <option value="">Seleccione el periodo lectivo...</option> 
                </select> 
                <span class="help-desk error" id="error-cboPeriodo">Debe seleccionar el periodo lectivo</span> 
            </div> 
            
            <div class="form-group has-feedback"> 
                <select class="form-control" id="cboPerfil" name="cboPerfil"> 
                    <option value="">Seleccione su perfil...</option> 
                </select> 
                <span class="help-desk error" id="error-cboPerfil">Debe seleccionar su perfil</span> 
            </div> 
            
            <div class="row"> 
                <div class="col-xs-12"> 
                    <button type="submit" class="btn btn-raised btn-danger btn-block" id="btnEnviar">Ingresar</button> 
                </div> 
            </div> 
        </form> 
        
        <div id="img_loader" style="display:none;text-align:center; margin-top: 10px;"> 
            <img src="./imagenes/ajax-loader6.GIF" alt="Procesando..."> 
        </div> 
        
        <div id="mensaje" class="error text-center" style="margin-top: 10px;"></div> 
    </div> 
    
    <footer style="text-align: center; background-color: #000a; color: white; padding: 5px; margin-top: 10px;"> 
        .: &copy; <?php echo date("Y"); ?> - <span id="nom_institucion"></span> :. 
    </footer> 
</div> 

<!-- jQuery 3 --> 
<script src="assets/template/jquery/jquery.min.js"></script> 
<!-- Bootstrap 3.3.7 --> 
<script src="assets/template/bootstrap/js/bootstrap.min.js"></script> 

<script> 
$(document).ready(function(){ 
    // Carga de datos iniciales
    cargar_periodos(); 
    cargar_perfiles(); 
    cargar_nombre_institucion(); 

    $("#form-login").submit(function(event){ 
        event.preventDefault(); 
        
        // Declaración correcta de variables locales usando var
        var nombre = $("#uname").val().trim(); 
        var password = $("#passwd").val().trim(); 
        var periodo = $("#cboPeriodo").val(); 
        var perfil = $("#cboPerfil").val(); 
        var valido = true;

        // Validación de campos con toggle visual limpio
        if (nombre === "") { 
            $("#error-uname").fadeIn("slow"); 
            valido = false;
        } else { 
            $("#error-uname").fadeOut(); 
        } 

        if (password === "") { 
            $("#error-passwd").fadeIn("slow"); 
            valido = false;
        } else { 
            $("#error-passwd").fadeOut(); 
        } 

        if (periodo === "") { 
            $("#error-cboPeriodo").fadeIn("slow"); 
            valido = false;
        } else { 
            $("#error-cboPeriodo").fadeOut(); 
        } 

        if (perfil === "") { 
            $("#error-cboPerfil").fadeIn("slow"); 
            valido = false;
        } else { 
            $("#error-cboPerfil").fadeOut(); 
        } 

        // Cancelar el envío si falta algún dato obligatorio
        if (!valido) {
            return false;
        }

        $("#mensaje").fadeOut(); 
        $("#img_loader").css("display","block"); 
        $("#btnEnviar").attr("disabled", true); // Evita doble submit accidental

        $.ajax({ 
            url: "scripts/verificar_login.php", 
            type: "POST", 
            data: $(this).serialize(), 
            dataType: "json", 
            success: function(resp) { 
                if (!resp.error) { 
                    // Redirección directa enviando el id_perfil seleccionado
                    location.href = "admin.php?id_usuario=" + resp.id_usuario + "&id_perfil=" + perfil; 
                } else { 
                    $("#img_loader").css("display","none"); 
                    $("#btnEnviar").attr("disabled", false);
                    $("#mensaje").html('<span class="rojo">Usuario, password o perfil incorrectos.</span>').fadeIn("slow"); 
                    $("#uname").focus(); 
                } 
            }, 
            error: function(jqXHR) { 
                $("#img_loader").css("display","none"); 
                $("#btnEnviar").attr("disabled", false);
                console.error("Error crítico en login:", jqXHR.responseText); 
            } 
        }); 
    }); 
}); 

function cargar_periodos() { 
    $.get("periodos_lectivos/cargar_periodos_lectivos.php", function(resultado){ 
        if(resultado === false) { 
            console.error("Error al cargar los periodos"); 
        } else { 
            $('#cboPeriodo').append(resultado); 
        } 
    }); 
} 

function cargar_perfiles() { 
    $.get("scripts/cargar_perfiles.php", function(resultado){ 
        if(resultado === false) { 
            console.error("Error al cargar los perfiles"); 
        } else { 
            $('#cboPerfil').append(resultado); 
        } 
    }); 
} 

function cargar_nombre_institucion() { 
    $.get("scripts/cargar_nombre_institucion.php", function(resultado){ 
        if(resultado === false) { 
            console.error("Error al cargar el nombre institucional"); 
        } else { 
            $("#nom_institucion").html(resultado); 
        } 
    }); 
} 
</script> 
</body> 
</html>
