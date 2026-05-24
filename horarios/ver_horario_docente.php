<div class="content-wrapper">
    <br>
    <div id="horarioApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>HORARIOS DE CLASE</h4>
            </div>
            <div class="panel-body">
                <form id="form_horario" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Periodo Lectivo:</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="cboPeriodos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_message"></span>
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Horario:</label>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="cboHorarios">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_message"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="message" class="fuente9 text-center"></div>
                <!-- Título del Horario de Clases -->
                <div id="tituloHorario" class="fuente10 text-center" style="font-weight: bold;">
                    <!-- Aquí va el título del horario de clases -->
                </div>
                <!-- table -->
                <table class="table table-bordered fuente9">
                    <thead id="horario_cabecera">
                        <!-- Aqui desplegamos los dias de la semana de este paralelo -->
                    </thead>
                    <tbody id="horario_clases">
                        <!-- Aqui desplegamos las horas clase con su asignatura y docente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        cargar_periodos();
        $("#cboPeriodos").change(function(e) {
            // Código para recuperar los horarios definidos para el Periodo Lectivo elegido
            cargarHorarios();
            $("#horario_clases").html("");
        });
        $("#cboHorarios").change(function(e) {
            // Código para recuperar el horario docente del día seleccionado
            listarHorarioDocente();
        })
        $("#horario_clases").html("<tr><td align='center'>Debes seleccionar un Periodo Lectivo...</td></tr>");
    });

    function cargar_periodos() {
        $.get("periodos_lectivos/cargar_periodos_lectivos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboPeriodos').append(resultado);
            }
        });
    }

    function cargarHorarios() {
        var id_periodo_lectivo = $("#cboPeriodos").val();
        var request = $.ajax({
            url: "horarios/cargar_titulos_horarios.php",
            method: "post",
            data: {
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "html"
        });

        request.done(function(data) {
            var x = document.getElementById("cboHorarios");
            var option = document.createElement("option");
            option.text = "Seleccione...";
            x.length = 0;
            x.add(option);
            $("#cboHorarios").append(data);
        });
    }

    function listarHorarioDocente() {
        var id_periodo_lectivo = $("#cboPeriodos").val();
        var id_horario_def = $("#cboHorarios").val();
        if (id_periodo_lectivo == "0") {
            $("#horario_clases").html("<tr><td align='center'>Debes seleccionar un Periodo Lectivo...</td></tr>");
            $("#cboPeriodos").focus();
        } else {
            $("#horario_clases").html(
                "<tr><td align='center'><img src='imagenes/ajax-loader.gif' alt='procesando...'></td></tr>");
            $.post("horarios/listar_horario_docente.php", {
                    id_periodo_lectivo: id_periodo_lectivo,
                    id_horario_def: id_horario_def
                },
                function(resultado) {
                    // Desplegar el horario del docente
                    jsonObject = JSON.parse(resultado);
                    $("#horario_cabecera").html(jsonObject.dias_semana);
                    $("#horario_clases").html(jsonObject.horas_clase);
                }
            );
        }
    }
</script>