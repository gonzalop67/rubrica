<section class="content-header">
    <h1>
        Aprobados Por Paralelo
    </h1>
</section>
<!-- Main content -->
<section class="content container-fluid">
    <!-- Aquí irá el gráfico estadístico -->
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Gráfico estadístico</h3>
                    <div class="box-tools pull-right">
                        <div class="form-group">
                            <select name="id_paralelo" id="id_paralelo" class="form-control fuente9">
                                <option value="">Seleccione un paralelo...</option>
                                <?php foreach ($datos['paralelos'] as $v) : ?>
                                    <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="aprobados_por_paralelo" class="text-center" style="min-width:310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./box-body -->
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });

        $("#id_paralelo").change(function(e) {
            e.preventDefault();
            var id_paralelo = $(this).val();
            if (id_paralelo == 0) {
                $("#aprobados_por_paralelo").html("Debe elegir un paralelo...");
                $("#ver_reporte").css("display", "none");
            } else {
                $("#aprobados_por_paralelo").html("");
                //Aqui va la llamada a ajax para recuperar los porcentajes de aprobados y no aprobados
                cargarPorcentajesdeAprobados(id_paralelo);
            }
        });
    });

    function cargarPorcentajesdeAprobados(id_paralelo) {
        // Procedimiento para recuperar los porcentajes de aprobados y no aprobados
        $("#aprobados_por_paralelo").html("<img src='<?php echo RUTA_URL ?>/public/img/ajax-loader.gif' alt='procesando...' />");
        $.ajax({
            url: "<?php echo RUTA_URL ?>/estadisticas/obtener_aprobados_por_paralelo",
            type: "POST",
            data: {
                id_paralelo: id_paralelo
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
                console.log(porcentajes);
                graficar(etiquetas, porcentajes, "aprobados_por_paralelo");
                // $("#ver_reporte").css("display","block");
            }
        });
    }

    function graficar(etiquetas, porcentajes, idDiv) {
        var title = "APROBADOS POR PARALELO" +
            "<br>" +
            $("#id_paralelo option:selected").text();
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