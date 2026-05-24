<div class="content-wrapper">
    <div id="horarioApp" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>HORARIOS DE CLASE</h4>
            </div>
            <div class="panel-body">
                <form id="form_horario" action="" >

                    <div class="row" style="margin-bottom: 4px;">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Horario:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboHorarios">
                                <!-- En este lugar se cargan los horarios definidos en la BD -->
                            </select>
                            <span class="help-desk error" id="error_horario"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_paralelo"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="message" class="fuente9 text-center"></div>
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
        cargarParalelos();
        cargarHorarios();
        $("#cboParalelos").change(function(e) {
            e.preventDefault();
            if ($(this).val() == 0) {
                $("#message").html("Debe seleccionar un paralelo...");
                $("#horario_cabecera").html("");
                $("#horario_clases").html("");
            } else {
                // console.log($(this).val());
                $("#message").html("");
                $("#horario_clases").html("<tr><td colspan='100%' class='text-center'><img src='imagenes/ajax-loader.gif' alt='procesando...' /></td></tr>");

                // Obtengo los dias de la semana
                $.ajax({
                    url: "tutores/listar_dias_semana.php",
                    data: {
                        id_paralelo: $(this).val(),
                        id_horario_def: $("#cboHorarios").val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response) {
                        $("#horario_cabecera").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
                // Luego las horas clase con sus asignaturas y docentes
                $.ajax({
                    url: "tutores/listar_horas_clase.php",
                    data: {
                        id_paralelo: $(this).val(),
                        id_horario_def: $("#cboHorarios").val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response){
                        $("#horario_clases").html(response);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
        })
    });

    function cargarParalelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboParalelos").append(resultado);
                    $("#message").html("Debe seleccionar un paralelo...");
                }
            }
        );
    }

    function cargarHorarios() {
        var request = $.ajax({
            url: "dias_semana/cargar_titulos_horarios.php",
            method: "get",
            dataType: "html"
        });

        request.done(function(data) {
            $("#cboHorarios").append(data);
        });
    }
</script>