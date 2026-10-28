<div class="container">
    <div id="parcialesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones Parciales</h4>
            </div>
            <div class="panel-body">
                <form id="form_parciales" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Parcial:</label>
                        </div>
                        <div class="col-sm-10">
                            <select id="cboPeriodosEvaluacion" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span id="mensaje1" class="help-desk"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <div id="img_loader" class="text-center">
                    <!-- Aqui va la imagen del loader... -->
                </div>
                <div id="tbl_notas" class="table-responsive">
                    <!-- Aqui van las calificaciones del estudiante -->
                </div>
                <!-- Contenedor del Promedio General -->
                <div id="contenedor_promedio" class="row" style="margin-top: 15px; display: none;">
                    <div class="col-sm-12 text-right">
                        <h4 style="font: bold 12pt helvetica; color: #2c3e50;">
                            Promedio General del Parcial: <span id="lbl_promedio_general" class="label label-success">0.00</span>
                        </h4>
                    </div>
                </div>
                <form id="form_califica" name="form_califica" target="_blank" action="../dompdf/resumen_aporte.php" method="post">
                    <input type="hidden" id="idestudiante" name="idestudiante" value="<?php echo $id_estudiante; ?>" />
                    <input type="hidden" id="idperiodolectivo" name="idperiodolectivo" value="<?php echo $id_periodo_lectivo; ?>" />
                    <input type="hidden" id="idparalelo" name="idparalelo" value="<?php echo $id_paralelo; ?>">
                    <input type="hidden" id="idaporteevaluacion" name="idaporteevaluacion" value="0" />
                    <div id="btn_enviar_pdf" class="row" style="margin-top: 4px;" id="botones_edicion">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary fuente9">Exportar a PDF</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#btn_enviar_pdf").hide();
        cargarPeriodosEvaluacion();
        $("#cboPeriodosEvaluacion").change(function(e) {
            if ($(this).val() == 0) {
                $("#mensaje1").html("Debe seleccionar un Parcial...");
                $("#tbl_notas").html("");
                $("#btn_enviar_pdf").hide();
            } else {
                $("#mensaje1").html("");
                obtenerDetalleAporteEvaluacion();
            }
            $("#tbl_notas").html("");
            $("#btn_enviar_pdf").hide();
        });
    });

    function cargarPeriodosEvaluacion() {
        $.get("../scripts/cargar_periodos_evaluacion_principales2.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodosEvaluacion").append(resultado);
                }
            }
        );
    }

    function obtenerDetalleAporteEvaluacion() {
        var id_aporte_evaluacion = $("#cboPeriodosEvaluacion").val();
        var id_estudiante = $("#idestudiante").val();
        var id_paralelo = $("#idparalelo").val();

        $("#idaporteevaluacion").val($("#cboPeriodosEvaluacion").val());

        // Ocultar contenedor de promedio anterior si existe
        $("#contenedor_promedio").hide();

        // Obtener los titulos de los insumos de evaluacion
        $("#img_loader").html("<img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' />");

        $.post("../scripts/obtener_calificaciones_parciales_id.php", {
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_estudiante: id_estudiante,
                id_paralelo: id_paralelo
            },
            function(resultado) {
                $("#img_loader").html("");
                if (resultado == false) {
                    alert("Error");
                } else {
                    console.log(resultado);
                    $("#tbl_notas").html(resultado);

                    // === LÓGICA PARA CALCULAR EL PROMEDIO GENERAL ===
                    var suma = 0;
                    var contador = 0;

                    // Expresión regular para detectar si el texto contiene letras (cualitativo)
                    var contieneLetras = /[a-zA-Z]/;

                    $("#tbl_notas table tbody tr").each(function() {
                        var textoNota = $(this).find("td:last").text().trim();

                        // 1. SI ES CUALITATIVA (Contiene letras), se ignora por completo de la matemática
                        if (contieneLetras.test(textoNota)) {
                            return true; // "return true" en jQuery .each() funciona como un 'continue' (salta a la siguiente materia)
                        }

                        // 2. SI ESTÁ EN BLANCO, matemáticamente vale 0.00 y SÍ se cuenta en el divisor
                        var nota = parseFloat(textoNota);
                        if (isNaN(nota) || textoNota === "") {
                            nota = 0.00;
                        }

                        // 3. Acumulamos solo los valores cuantitativos y las celdas vacías
                        suma += nota;
                        contador++;
                    });

                    // Generar el promedio general solo con el universo cuantitativo
                    if (contador > 0) {
                        var promedioGeneral = (suma / contador).toFixed(2);

                        $("#lbl_promedio_general").text(promedioGeneral);
                        $("#contenedor_promedio").show();
                    } else {
                        // Si todas las materias resultaron ser cualitativas
                        $("#contenedor_promedio").hide();
                    }
                    // ===============================================
                }
            }
        );
        $("#btn_enviar_pdf").show();
    }
</script>