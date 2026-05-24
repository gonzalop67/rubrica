<div class="content-wrapper">
    <br>
    <div id="informeAnualApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Reporte Periodo Lectivo</h4>
            </div>
            <div class="panel-body">
                <form id="form_anual" action="" class="app-form">
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
                <div id="pag_nomina_estudiantes" style="margin-top:4px;">
                    <div id="tituloNomina" class="header2"> RESUMEN ANUAL </div>
                    <form id="formulario_periodo" action="reportes/reporte_periodo_lectivo_docente.php" method="post" target="_blank">
                        <div id="resumen_anual" style="text-align:center"> Debe elegir una asignatura.... </div>
                        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                            <input id="id_asignatura" name="id_asignatura" type="hidden" />
                            <input id="id_paralelo" name="id_paralelo" type="hidden" />
                            <input type="submit" value="Ver Reporte" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        cargarAsignaturasDocente();
    });
    $("#cboAsignaturas").change(function(e) {
        e.preventDefault();
        if ($(this).val() != 0) {
            $("#resumen_anual").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $("#ver_reporte").css("display", "none");
            var codigos = $("#cboAsignaturas").val();
            var array_codigos = codigos.split("*");
            var id_asignatura = array_codigos[0];
            var id_paralelo = array_codigos[1];
            $("#id_asignatura").val(id_asignatura);
            $("#id_paralelo").val(id_paralelo);
            // mostrarTitulosRubricas(id_aporte_evaluacion);
            $.ajax({
                url: "calificaciones/reporte_anual_docente.php",
                type: "POST",
                data: {
                    id_asignatura: id_asignatura,
                    id_paralelo: id_paralelo
                },
                dataType: "html",
                success: function(data) {
                    $("#resumen_anual").html(data);
                    $("#ver_reporte").css("display", "block");
                }
            });
        } else {
            $("#ver_reporte").css("display", "none");
            $("#resumen_anual").html("Debe seleccionar una asignatura...");
        }
    });

    function cargarAsignaturasDocente() {
        $.ajax({
            url: "calificaciones/cargar_asignaturas_docente.php",
            type: "POST",
            dataType: "html",
            success: function(data) {
                $("#cboAsignaturas").html(data);
            }
        });
    }

    function mostrarTitulosRubricas(id_aporte_evaluacion) {
        // Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
        var codigos = $("#cboAportesEvaluacion").val();
        var array_codigos = codigos.split("*");

        var id_periodo_evaluacion = array_codigos[0];
        var id_aporte_evaluacion = array_codigos[1];

        var id_asignatura = $("#cboAsignaturas").val();
        var id_paralelo = $("#cboParalelos").val();
        var id_periodo_lectivo = $("#id_periodo_lectivo").val();

        $.ajax({
            type: "post",
            url: "scripts/obtener_id_curso_paralelo.php",
            data: {
                id_paralelo: id_paralelo
            },
            success: function(resultado) {
                var id_curso = resultado;
                $.post("calificaciones/mostrar_titulos_rubricas.php", {
                        id_periodo_evaluacion: id_periodo_evaluacion,
                        id_aporte_evaluacion: id_aporte_evaluacion,
                        alineacion: "left",
                        id_asignatura: id_asignatura,
                        id_curso: id_curso,
                        id_periodo_lectivo: id_periodo_lectivo
                    },
                    function(resultado) {
                        if (resultado == false) {
                            alert("Error");
                        } else {
                            $("#txt_rubricas").html(resultado);
                        }
                    }
                );
            },
            error: function(xhr, textStatus, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>