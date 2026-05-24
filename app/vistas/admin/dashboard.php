<section class="content-header">
    <h1>
        Dashboard
        <small>Sistema Integrado de Administración Estudiantil</small>
    </h1>
</section>
<!-- Main content -->
<section class="content container-fluid">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $datos['numero_autoridades']; ?></h3>
                    <p><?php echo ($datos['numero_autoridades'] == 1) ? "Autoridad" : "Autoridades"; ?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-stalker"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $datos['numero_docentes']; ?></h3>
                    <p><?php echo ($datos['numero_docentes'] == 1) ? "Docente" : "Docentes"; ?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo $datos['numero_estudiantes']; ?></h3>
                    <p><?php echo ($datos['numero_estudiantes'] == 1) ? "Estudiante" : "Estudiantes"; ?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $datos['num_representantes']; ?></h3>
                    <p><?php echo ($datos['num_representantes'] == 1) ? "Representante" : "Representantes"; ?></p>
                </div>
                <div class="icon">
                    <i class="ion ion-woman"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Aquí irá el gráfico estadístico -->
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Gráfico estadístico</h3>
                    <div class="box-tools pull-right">
                        <select name="id_jornada" id="id_jornada" class="form-control">
                            <?php
                            foreach ($datos['jornadas'] as $jornada) {
                                $result = $jornadaModelo->obtenerJornada($jornada->id_jornada);
                            ?>
                                <option value="<?php echo $result->id_jornada; ?>"><?php echo $result->jo_nombre; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="graficoBarras" style="min-width:310px; height: 400px; margin: 0 auto"></div>
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        var id_jornada = $("#id_jornada").val();
        getNumberOfStudents(id_jornada);
    });

    function getNumberOfStudents(id_jornada)
    {
        $.ajax({
            url: "<?php echo RUTA_URL . "/estudiantes/numero_estudiantes_por_jornada"; ?>",
            type: "POST",
            data: {
                id_jornada: id_jornada
            },
            dataType: "json",
            success: function(data) {
                // console.log(data);
                var paralelos = new Array();
                var mujeres = new Array();
                var hombres = new Array();
                $.each(data, function(key, value) {
                    paralelos.push(value.paralelo);
                    mujeres.push(value.numero_mujeres);
                    hombres.push(value.numero_hombres);
                });
                graficar2(paralelos, mujeres, hombres, "graficoBarras");
            }
        });
    }

    function graficar2(paralelos, numero_mujeres, numero_hombres, idDiv) {
        var xValue = paralelos;
        var mujeres = numero_mujeres;

        var trace1 = {
            x: xValue,
            y: mujeres,
            name: 'Mujeres',
            type: 'bar'
        };

        var hombres = numero_hombres;

        var trace2 = {
            x: xValue,
            y: hombres,
            name: 'Hombres',
            type: 'bar'
        };

        var data = [trace1, trace2];

        var layout = {
            barmode: 'stack'
        };

        Plotly.newPlot(idDiv, data, layout);
    }
</script>