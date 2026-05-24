<div class="content-wrapper">
    <br>
    <div id="parcialesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones Parciales</h4>
            </div>
            <div class="panel-body">
                <form id="form_parciales" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Sub Periodo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select id="cboPeriodosEvaluacion" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span id="mensaje1" class="help-desk"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Parcial:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select id="cboAportesEvaluacion" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Asignatura:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select id="cboAsignaturas" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk" id="mensaje3"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <div class="text-center">
                    <form id="formulario_rubrica" action="php_excel/reporte_por_parcial_tutor.php" method="post" target="_self">
                        <!-- &nbsp;&nbsp;Impresi&oacute;n para juntas&nbsp;
                        <input type="checkbox" name="impresion_para_juntas" id="impresion_para_juntas"> -->
                        &nbsp;<button class="btn btn-info">Ver Consolidado</button>
                        <input id="id_paralelo" name="id_paralelo" type="hidden" value="" />
                        <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                        <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                    </form>
                </div>
                <hr>
                <div id="img_loader" class="text-center">
                    <!-- Aqui va la imagen del loader... -->
                </div>
                <div id="tbl_notas" class="table-responsive">
                    <!-- Aqui van las calificaciones del estudiante -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#tbl_notas").html("<p class='text-center'>Debe seleccionar un quimestre...</p>");
        $("#id_paralelo").val($.trim($("#id_paralelo_tutor").val()));
        $.ajaxSetup({
            error: function(xhr) {
                alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });
        cargarAsignaturas();
        cargarPeriodosEvaluacion();
        $("#cboPeriodosEvaluacion").change(function(e) {
            e.preventDefault();
            if ($(this).val() == 0) {
                $("#tbl_notas").html("<p class='text-center'>Debe seleccionar un subperiodo...</p>");
            } else {
                cargarAportesEvaluacion();
                $("#tbl_notas").html("<p class='text-center'>Debe seleccionar un parcial...</p>");
            }
        });
        $("#cboAportesEvaluacion").change(function(e) {
            e.preventDefault();
            if ($(this).val() == 0) {
                $("#tbl_notas").html("<p class='text-center'>Debe seleccionar un parcial...</p>");
            } else {
                $("#tbl_notas").html("<p class='text-center'>Debe seleccionar una asignatura...</p>");
                $("#cboAsignaturas").val(0);
            }
        });
        $("#cboAsignaturas").change(function(e) {
            e.preventDefault();
            if ($(this).val() !== 0)
                listarEstudiantesParalelo();
            else 
                $("#tbl_notas").html("<p class='text-center'>Debe seleccionar una asignatura...</p>");
        });
        $("#formulario_rubrica").submit(function(e) {
            e.preventDefault();

            var id_paralelo = document.getElementById("id_paralelo").value;
            var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
            var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
            document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
            document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;

            if (id_paralelo == 0) {
                $("#tbl_notas").html("<p class=\"text-center text-danger\">No se pudo obtener el id del paralelo asociado...</p>");
                return false;
            } else if (id_periodo_evaluacion == 0) {
                $("#tbl_notas").html("<p class=\"text-center text-danger\">Debe seleccionar un quimestre...</p>");
                return false;
            } else if (id_aporte_evaluacion == 0) {
                $("#tbl_notas").html("<p class=\"text-center text-danger\">Debe seleccionar un parcial...</p>");
                return false;
            }
            document.getElementById("formulario_rubrica").submit();
        });
    });

    function cargarAsignaturas() {
        // Obtengo las asignaturas asociadas al paralelo
        $.post("scripts/cargar_asignaturas_por_paralelo.php", {
                id_paralelo: $("#id_paralelo").val()
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    document.getElementById("cboAsignaturas").length = 1;
                    $("#cboAsignaturas").append(resultado);
                }
            }
        );
    }

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion_principales.php", {},
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
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").find(":selected").val();
        $.get("scripts/cargar_aportes_principales_evaluacion.php", {
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    document.getElementById("cboAportesEvaluacion").length = 1;
                    $("#cboAportesEvaluacion").append(resultado);
                }
            }
        );
    }

    function listarEstudiantesParalelo() {
        // Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
        var id_paralelo = document.getElementById("id_paralelo").value;
        var id_asignatura = document.getElementById("cboAsignaturas").value;
        if (id_periodo_evaluacion == 0) {
            $("#tbl_notas").html("Debe seleccionar un quimestre...");
            $("#cboPeriodosEvaluacion").focus();
        } else if (id_aporte_evaluacion == 0) {
            $("#tbl_notas").html("Debe seleccionar un parcial...");
            $("#cboAportesEvaluacion").focus();
        } else if (id_asignatura == 0) {
            $("#tbl_notas").html("Debe seleccionar una asignatura...");
            $("#cboAsignaturas").focus();
        } else {
            // Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
            $.ajax({
                url: "scripts/listar_estudiantes_paralelo_tutor.php",
                method: "post",
                data: {
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    id_aporte_evaluacion: id_aporte_evaluacion,
                    id_periodo_evaluacion: id_periodo_evaluacion
                },
                success: function(resultado) {
                    // console.log(resultado);
                    $("#tbl_notas").html(resultado);
                }
            });
        }
    }
</script>