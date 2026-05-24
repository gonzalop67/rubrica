<div class="content-wrapper">
    <br>
    <div id="informeQuimestralApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Informe Quimestral</h4>
            </div>
            <div class="panel-body">
                <form id="form_quimestre" action="" class="app-form">
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
                    <form id="formulario_periodo" action="php_excel/informe_quimestral.php" method="post">
                        <div id="escala_calificaciones" class="text-center"> Debe elegir un per&iacute;odo de
                            evaluaci&oacute;n.... </div>
                        <div id="lista_estudiantes">
                            <!-- Aqui va la lista de estudiantes con bajo rendimiento -->
                        </div>
                        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                            <input id="id_asignatura" name="id_asignatura" type="hidden" />
                            <input id="id_paralelo" name="id_paralelo" type="hidden" />
                            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
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
            $("#cboAsignaturas").val(0);
            $("#ver_reporte").css("display", "none");
            $("#id_periodo_evaluacion").val($(this).val());
            $("#escala_calificaciones").html("Debe seleccionar una asignatura...");
        });
        $("#cboAsignaturas").change(function(e) {
            e.preventDefault();
            if ($(this).val() == 0) {
                $("#escala_calificaciones").html("Debe seleccionar una asignatura...");
            } else {
                cargarEscalaCalificaciones();
                cargarEstudiantesBajoRendimiento();
            }
        });
    });

    function cargarPeriodosEvaluacionPrincipales() {
        $.get("scripts/cargar_sub_periodos.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
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
            $("#escala_calificaciones").html("Debe seleccionar un periodo de evaluaci&oacute;n...");
        } else {
            var codigos = $("#cboAsignaturas").val();
            var array_codigos = codigos.split("*");
            $("#escala_calificaciones").removeClass("error");
            $("#id_paralelo").val(array_codigos[1]);
            $("#id_asignatura").val(array_codigos[0]);
            $("#escala_calificaciones").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                url: "scripts/informe_quimestral.php",
                type: "POST",
                data: {
                    id_paralelo: array_codigos[1],
                    id_asignatura: array_codigos[0],
                    id_periodo_evaluacion: $("#cboPeriodosEvaluacion").val()
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
            $("#cboPeriodosEvaluacion option:selected").text();
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

    function cargarEstudiantesBajoRendimiento() {
        //
    }
</script>