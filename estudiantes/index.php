<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SIAE Web 2 | Log in</title>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

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
    .cover{
        background: url('../assets/images/fondo_escritorio_estudiante1.jpg') no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
    .blanco {
        color: #ffffff;
    }
  </style>

</head>
<body class="hold-transition login-page cover">
    <div class="login-box">
        <div class="login-logo blanco">
            <h2>S. I. A. E.</h2>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Introduzca sus datos de ingreso</p>
            <form id="form-login" action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="C&eacute;dula/Pasaporte" id="uname" name="uname" autocomplete="on">
                    <span class="form-control-feedback">
                      <img src="../assets/images/if_user_male_172625.png" height="16px" width="16px">
                    </span>
                    <span class="help-desk error" id="mensaje1">Debe ingresar su nombre de Usuario</span>
                </div>
                <div class="form-group has-feedback">
                    <select class="form-control" id="cboPeriodo" name="cboPeriodo">
                    	<option value="">Seleccione el periodo lectivo...</option>
                	</select>
                  <span class="help-desk error" id="mensaje3">Debe seleccionar el periodo lectivo</span>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-raised btn-danger btn-block" id="btnEnviar">Ingresar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <div id="img_loader" style="display:none;text-align:center">
                <img src="../imagenes/ajax-loader6.GIF" alt="Procesando...">  
            </div>
            <div id="mensaje" class="error">
                <!-- Aqui van los mensajes de error -->
            </div>
        </div>
        <!-- /.login-box-body -->
       
    </div>
    <!-- /.login-box --> 

	<footer style="text-align: center; font-size: 1.1em; color: #fff; font-weight: normal;">
		.: &copy; <?php echo date("  Y"); ?> - <span id="nom_institucion"></span> :.
	</footer>

	<!-- jQuery 3 -->
	<script src="../assets/template/jquery/jquery.min.js"></script>
	<!-- Bootstrap 3.3.7 -->
	<script src="../assets/template/bootstrap/js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function(){
        cargar_periodos();
        cargar_nombre_institucion();
        $("#form-login").submit(function(event){
            event.preventDefault();
            nombre = $("#uname").val();
            periodo = $("#cboPeriodo").val();

            if (nombre == "" || periodo == "") {
                if (nombre == "") {
                    $("#mensaje1").fadeIn("slow");
                }else{
                    $("#mensaje1").fadeOut();
                }
                if (periodo == "") {
                    $("#mensaje3").fadeIn("slow");
                }else{
                    $("#mensaje3").fadeOut();
                }
                return false;
            }

            $("#mensaje").fadeOut();

            $("#img_loader").css("display","block");

            $.ajax({
                url: "../scripts/verificar_login_estudiante.php",
                type: "POST",
                data: {
					cedula: nombre,
					id_periodo_lectivo: periodo
				},
                dataType: "json",
                success: function(resp) {
                    console.log(resp);
                    if (!resp.error) {
						
                        //No hay error se redirecciona al admin
                        location.href = "admin2.php?id_estudiante=" + resp['id_estudiante'];
						
					} else {
					
					    //No existe el usuario
                        var error = '<span class="rojo">' +
                                    'C&eacute;dula/Pasaporte incorrecto.' +
                                    '</span>';
                        $("#img_loader").css("display","none");
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
        $("#uname").focus();
    });

    function cargar_periodos()
	{
		$.get("cargar_periodos_lectivos.php", function(resultado){
			if(resultado == false)
			{
				alert("Error");
			}
			else
			{
				$('#cboPeriodo').append(resultado);			
			}
		});	
	}

    function cargar_nombre_institucion()
    {
        $.get("../scripts/cargar_nombre_institucion.php", function(resultado){
            if(resultado == false)
            {
                alert("Error");
            }
            else
            {
                $("#nom_institucion").html(resultado);
            }
        });
    }
  </script>

</body>
</html>
