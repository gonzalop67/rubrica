<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cambiar Periodo Lectivo
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="login-box-body">
                    <h3 class="text-center">Seleccione el periodo lectivo</h3>
                    <form id="form-cambiar-periodo" action="calificaciones/cambiar_periodo_lectivo.php" method="post">
                        <div class="form-group row">
                            <select class="form-control col-xs-12" id="cboPeriodosUsuario" name="cboPeriodosUsuario">
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-xs-4"></div>

                            <div class="col-xs-4">
                                <button type="submit" class="btn btn-primary btn-block">Seleccionar</button>
                            </div>

                            <div class="col-xs-4"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        cargar_periodos();

        $("#form-cambiar-periodo").submit(function(event) {
            event.preventDefault();
            let id_periodo_lectivo = $("#cboPeriodosUsuario").val();
            let id_usuario = $("#id_usuario").val();
            let id_perfil = <?= $id_perfil ?>;

            if (id_periodo_lectivo == 0) {
                Swal.fire({
                    'title': "Error!",
                    'text': "Debe seleccionar un periodo lectivo...",
                    'icon': 'error'
                })
                return false;
            }

            // Aqui consultamos el nuevo nombre de periodo lectivo, cambiamos la variable de sesion 
            // $_SESSION['id_periodo_lectivo']
            $.ajax({
                type: "POST",
                url: "calificaciones/cambiar_periodo_lectivo.php",
                data: {
                    id_periodo_lectivo: id_periodo_lectivo,
                    id_usuario: id_usuario,
                    id_perfil: id_perfil
                },
                dataType: "json",
                success: function(resp) {
                    // console.log(resp);

                    location.href = "admin.php?id_usuario=" + resp['id_usuario'] + "&id_perfil=" + resp['id_perfil'];
                }
            });
            //

        });
    });

    function cargar_periodos() {
        $.get("periodos_lectivos/cargar_periodos_lectivos.php", function(resultado) {
            // console.log(resultado);
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboPeriodosUsuario').append(resultado);
            }
        });
    }
</script>