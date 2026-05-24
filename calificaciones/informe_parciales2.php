<div class="content-wrapper">
    <br>
    <div id="informeParcialesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Informes Parciales</h4>
            </div>
            <div class="panel-body">
                <form id="form_distributivo" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Período:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboPeriodosEvaluacion">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 2px;">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Aporte:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboAportesEvaluacion">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 2px;">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboAsignaturas">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="pag_nomina_estudiantes" style="margin-top:2px;">
                    <input type="hidden" id="titulo" value="">
                    <form id="formulario_periodo" action="php_excel/informe_de_parciales.php" method="post">
                        <div id="escala_calificaciones" class="text-center"> Debe elegir un per&iacute;odo de
                            evaluaci&oacute;n.... </div>
                        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                            <input id="id_asignatura" name="id_asignatura" type="hidden" />
                            <input id="id_paralelo" name="id_paralelo" type="hidden" />
                            <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                            <input id="id_paralelo_asignatura" name="id_paralelo_asignatura" type="hidden" />
                            <input type="submit" value="Generar Informe" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        cargarPeriodosEvaluacionPrincipales();
        cargarAsignaturasDocente();
        $("#cboPeriodosEvaluacion").change(function(e) {
            e.preventDefault();
            document.getElementById("cboAportesEvaluacion").value = 0;
            document.getElementById("cboAsignaturas").value = 0;
            cargarAportesEvaluacion();
            $("#ver_reporte").css("display", "none");
            $("#escala_calificaciones").html("Debe elegir un aporte...");
        });
        $("#cboAportesEvaluacion").change(function(e) {
            e.preventDefault();
            $("#ver_reporte").css("display", "none");
            document.getElementById("cboAsignaturas").value = 0;
            $("#escala_calificaciones").html("Debe elegir una asignatura...");
        });
        $("#cboAsignaturas").change(function(e) {
            e.preventDefault();
            cargarEscalaCalificaciones();
        })
    });

    function cargarPeriodosEvaluacionPrincipales() {
        $.get("scripts/cargar_periodos_evaluacion_principales3.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
            }
        );
    }

    function cargarAportesEvaluacion() {
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        document.getElementById("cboAportesEvaluacion").options.length = 1;
        document.getElementById("cboAportesEvaluacion").enabled = false;
        $.get("scripts/cargar_aportes_principales_evaluacion.php", {
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {
                $("#cboAportesEvaluacion").append(resultado);
                document.getElementById("cboAportesEvaluacion").enabled = true;
                $("#escala_calificaciones").html("Debe elegir un aporte de evaluaci&oacute;n...");
            }
        );
    }

    function cargarAsignaturasDocente() {
        $.post("calificaciones/cargar_asignaturas_docente.php", {
                // el id_usuario está registrado en la variable de sesión
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboAsignaturas").html(resultado);
                }
            }
        );
    }

    function cargarEscalaCalificaciones() {
        if ($("#cboPeriodosEvaluacion").val() == 0) {
            $("#escala_calificaciones").addClass("error");
            $("#escala_calificaciones").html("Debe seleccionar un periodo de evaluaci&oacute;n...");
        } else if ($("#cboAportesEvaluacion").val() == 0) {
            $("#escala_calificaciones").addClass("error");
            $("#escala_calificaciones").html("Debe seleccionar un aporte de evaluaci&oacute;n...");
        } else {
            var codigos = $("#cboAsignaturas").val();
            var array_codigos = codigos.split("*");
            var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
            $("#escala_calificaciones").removeClass("error");
            $("#id_paralelo").val(array_codigos[1]);
            $("#id_asignatura").val(array_codigos[0]);
            $("#id_aporte_evaluacion").val(id_aporte_evaluacion);
            $("#escala_calificaciones").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                url: "scripts/informe_parciales.php",
                type: "POST",
                data: {
                    id_paralelo: array_codigos[1],
                    id_asignatura: array_codigos[0],
                    id_aporte_evaluacion: id_aporte_evaluacion
                },
                dataType: "json",
                success: function(data) {
                    $("#escala_calificaciones").html("");
                    var escalas = new Array();
                    var porcentajes = new Array();
                    $.each(data, function(key, value) {
                        escalas.push(value.escala);
                        porcentaje = Number(value.porcentaje);
                        porcentajes.push(porcentaje);
                    });
                    graficar(escalas, porcentajes, "escala_calificaciones");
                    $("#ver_reporte").css("display", "block");
                }
            });
        }
    }

    function graficar(escalas, porcentajes, idDiv) {
        var title = $("#titulo").val() +
            "<br>" +
            $("#cboPeriodosEvaluacion option:selected").text() +
            " - " +
            $("#cboAportesEvaluacion option:selected").text();
        var data = [{
            values: porcentajes,
            labels: escalas,
            hoverinfo: "label",
            type: 'pie',
            sort: false,
            marker: {
                colors: ['rgb(51, 153, 102)', 'rgb(255, 192, 0)', 'rgb(255, 255, 0)', 'rgb(255, 80, 80)']
            }
        }];

        var layout = {
            title: title,
            "titlefont": {
                "size": 12
            },
        };

        Plotly.newPlot(idDiv, data, layout);
    }
</script>