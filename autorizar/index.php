<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="text-center">Autorizar cambiar la calificación</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cboPeriodosLectivos">Periodo Lectivo:</label>
                            <select name="cboPeriodosLectivos" id="cboPeriodosLectivos" class="form-control">
                                <option value="0">Seleccione un periodo lectivo...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cboParalelos">Paralelo:</label>
                            <select name="cboParalelos" id="cboParalelos" class="form-control">
                                <option value="0">Seleccione un paralelo...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cboAportesEvaluacion">Aporte de Evaluación:</label>
                            <select name="cboAportesEvaluacion" id="cboAportesEvaluacion" class="form-control">
                                <option value="0">Seleccione un aporte de evaluación...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- message -->
                        <div id="text_message" class="fuente9 text-center" style="color: #e73d4a"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- Lista de estudiantes -->
                        <div id="lista_estudiantes_paralelo" class="fuente9 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        cargarPeriodosLectivos();
        $("#cboParalelos").attr("disabled", true);
        $("#cboAportesEvaluacion").attr("disabled", true);
        $("#text_message").html("Debe seleccionar un periodo lectivo...");

        $("#cboPeriodosLectivos").change(function(e) {
            e.preventDefault();
            var id_periodo_lectivo = $(this).val();
            if (id_periodo_lectivo == 0) {
                $("#cboParalelos").attr("disabled", true);
                $("#text_message").html("Debe seleccionar un periodo lectivo...");
            } else {
                cargarParalelos();
                $("#cboParalelos").attr("disabled", false);
                $("#text_message").html("Debe seleccionar un paralelo...");
            }
        });

        $("#cboParalelos").change(function(e) {
            e.preventDefault();
            var id_aporte_evaluacion = $(this).val();
            if (id_aporte_evaluacion == 0) {
                $("#cboAportesEvaluacion").attr("disabled", true);
                $("#text_message").html("Debe seleccionar un paralelo...");
            } else {
                cargarAportesEvaluacion();
                $("#cboAportesEvaluacion").attr("disabled", false);
                $("#lista_estudiantes_paralelo").html("");
                $("#text_message").html("Debe seleccionar un aporte de evaluación...");
            }
        });

        $("#cboAportesEvaluacion").change(function(e) {
            e.preventDefault();
            var codigos = $(this).val();
            var array_codigos = codigos.split("*");
            // var id_periodo_evaluacion = array_codigos[0];
            const id_aporte_evaluacion = array_codigos[1];
            const id_paralelo = $("#cboParalelos").val();
            $("#text_message").html("<img src='imagenes/ajax-loader-blue.GIF' width='15px' /> Cargando...");
            cargarEstudiantes(id_paralelo, id_aporte_evaluacion);
        });
    });

    function cargarPeriodosLectivos() {
        $.ajax({
            type: "GET",
            url: "scripts/cargar_periodos_lectivos_vigentes.php",
            dataType: "html",
            success: function(resultado) {
                $('#cboPeriodosLectivos').append(resultado);
            }
        });
    }

    function cargarParalelos() {
        var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
        document.getElementById("cboParalelos").length = 1;
        $.post("scripts/cargar_paralelos_periodo_lectivo.php", {
                id_periodo_lectivo: id_periodo_lectivo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboParalelos").append(resultado);
                }
            }
        );
    }

    function cargarAportesEvaluacion() {
        var id_paralelo = document.getElementById("cboParalelos").value;
        var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
        $('#cboAportesEvaluacion option').remove();
        $('#cboAportesEvaluacion optgroup').remove();
        $.post("autorizar/cargar_aportes_evaluacion_paralelo.php", {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            function(resultado) {
                if (resultado == false) {
                    $("#text_message").html("No existen aportes de evaluaci&oacute;n asociados a este paralelo...");
                } else {
                    $("#cboAportesEvaluacion").append(resultado);
                    $("#text_message").html("Debe elegir un aporte de evaluaci&oacute;n...");
                }
            }
        );
    }

    function cargarEstudiantes(id_paralelo, id_aporte_evaluacion) {
        /* Cargar estudiantes del aporte de evaluación seleccionado */
        $.post("autorizar/cargar_estudiantes_autorizacion.php", {
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == "") {
                    $("#text_message").html("No existen estudiantes para autorizar cambio de calificación...");
                    $("#lista_estudiantes_paralelo").html("");
                } else {
                    $("#lista_estudiantes_paralelo").html(resultado);
                    $("#text_message").html("");
                }
            }
        );
    }

    function actualizar_estado_autorizado(obj, id_estudiante, id_paralelo, id_aporte_evaluacion) {
        if (obj.checked) estado_autorizado = "S";
        else estado_autorizado = "N";
        $.ajax({
            type: "POST",
            url: "autorizar/actualizar_estado_autorizado.php",
            data: "id_estudiante=" + id_estudiante + "&autorizado=" + estado_autorizado + "&id_paralelo=" + id_paralelo + "&id_aporte_evaluacion=" + id_aporte_evaluacion,
            success: function(resultado) {
                cargarEstudiantes(id_paralelo, id_aporte_evaluacion);
                // No desplega nada... esto es solo para ejecutar el codigo php
            }
        });
    }
</script>