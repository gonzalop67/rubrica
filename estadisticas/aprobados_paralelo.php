<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Aprobados por Paralelo</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cboModalidades">Modalidad:</label>
                            <select name="cboModalidades" id="cboModalidades" class="form-control">
                                <option value="0">Seleccione una modalidad...</option>
                            </select>
                        </div>
                    </div>
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
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!-- message -->
                        <div id="text_message" class="fuente9 text-center" style="color: #e73d4a"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="appAprobadosParalelo">
                            <div id="pag_nomina_estudiantes" style="margin-top:2px;">
                                <input type="hidden" id="titulo" value="">
                                <div id="aprobados_por_paralelo" style="text-align:center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        cargarModalidades();
        $("#cboPeriodosLectivos").attr("disabled", true);
        $("#cboParalelos").attr("disabled", true);
        $("#text_message").html("Debe seleccionar una modalidad...");
        $("#cboModalidades").change(function(e) {
            e.preventDefault();
            var id_modalidad = $(this).val();
            $("#aprobados_por_paralelo").html("");
            $("#ver_reporte").css("display", "none");
            if (id_modalidad == 0) {
                $("#cboPeriodosLectivos").attr("disabled", true);
                $("#cboParalelos").attr("disabled", true);
                $("#text_message").html("Debe seleccionar una modalidad...");
            } else {
                cargarPeriodosLectivos();
                document.getElementById("cboParalelos").length = 1;
                $("#cboPeriodosLectivos").attr("disabled", false);
                $("#text_message").html("Debe seleccionar un periodo lectivo...");
            }
        });
        $("#cboPeriodosLectivos").change(function(e) {
            e.preventDefault();
            var id_periodo_lectivo = $(this).val();
            $("#aprobados_por_paralelo").html("");
            $("#ver_reporte").css("display", "none");
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
            var id_paralelo = $(this).val();
            var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
            $("#ver_reporte").css("display", "none");
            if (id_paralelo == 0) {
                $("#aprobados_por_paralelo").html("Debe elegir un paralelo...");
            } else {
                $("#text_message").html("");
                $("#aprobados_por_paralelo").html("");
                //Aqui va la llamada a ajax para recuperar los porcentajes de aprobados y no aprobados
                cargarPorcentajesdeAprobados();
            }
        });
    });

    function cargarModalidades() {
        $.ajax({
            url: "periodos_lectivos/cargar_modalidades.php",
            dataType: "html",
            success: function(data) {
                // console.log(data);
                $("#cboModalidades").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function cargarPeriodosLectivos() {
        var id_modalidad = $("#cboModalidades").val();
        document.getElementById("cboPeriodosLectivos").length = 1;
        $.ajax({
            type: "POST",
            url: "scripts/cargarPeriodosLectivosPorIdModalidad.php",
            data: "id_modalidad=" + id_modalidad,
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

    function cargarPorcentajesdeAprobados() {
        // Procedimiento para recuperar los porcentajes de aprobados y no aprobados
        var id_paralelo = $("#cboParalelos").val();
        var id_periodo_lectivo = $("#cboPeriodosLectivos").val();
        $("#aprobados_por_paralelo").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
        $.ajax({
            url: "estadisticas/obtener_aprobados_por_paralelo.php",
            type: "POST",
            data: {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "json",
            success: function(data) {
                console.log(data);
                $("#aprobados_por_paralelo").html("");
                var etiquetas = new Array();
                var porcentajes = new Array();
                $.each(data, function(key, value) {
                    etiquetas.push(value.etiqueta);
                    porcentaje = Number(value.porcentaje);
                    porcentajes.push(porcentaje);
                });
                graficar(etiquetas, porcentajes, "aprobados_por_paralelo");
                $("#ver_reporte").css("display", "block");
            }
        });
    }

    function graficar(etiquetas, porcentajes, idDiv) {
        var title = "APROBADOS POR PARALELO" +
            "<br>" +
            $("#cboParalelos option:selected").text();
        var data = [{
            values: porcentajes,
            labels: etiquetas,
            hoverinfo: "label",
            type: 'pie',
            sort: false,
            marker: {
                colors: ['rgb(51, 153, 102)', 'rgb(255, 80, 80)']
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