<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Sistema Integrado de Administración Estudiantil</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <?php
                        $result = $db->consulta("SELECT u.id_usuario
                                                   FROM sw_usuario u, 
                                                        sw_perfil p,
                                                        sw_usuario_perfil up
                                                  WHERE p.id_perfil = up.id_perfil
                                                    AND u.id_usuario = up.id_usuario
                                                    AND pe_nombre = 'Autoridad'
                                                    AND us_activo = 1");
                        $num_autoridades = $db->num_rows($result);
                        ?>
                        <h3><?php echo $num_autoridades; ?></h3>

                        <p><?php echo ($num_autoridades == 1) ? "Autoridad" : "Autoridades"; ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-stalker"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <?php
                        $result = $db->consulta("SELECT u.id_usuario
                                                   FROM sw_usuario u, 
                                                        sw_perfil p,
                                                        sw_usuario_perfil up
                                                  WHERE p.id_perfil = up.id_perfil
                                                    AND u.id_usuario = up.id_usuario
                                                    AND pe_nombre = 'Docente'
                                                    AND us_activo = 1");
                        $num_docentes = $db->num_rows($result);
                        ?>
                        <h3><?php echo $num_docentes; ?></h3>

                        <p><?php echo ($num_docentes == 1) ? "Docente" : "Docentes"; ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <?php
                        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
                        $result = $db->consulta("SELECT e.id_estudiante
                                                    FROM sw_estudiante e, 
                                                        sw_estudiante_periodo_lectivo ep
                                                    WHERE e.id_estudiante = ep.id_estudiante
                                                    AND activo = 1 
                                                    AND id_periodo_lectivo = $id_periodo_lectivo");
                        $num_estudiantes = $db->num_rows($result);
                        ?>
                        <h3><?php echo $num_estudiantes; ?></h3>

                        <p><?php echo ($num_estudiantes == 1) ? "Estudiante" : "Estudiantes"; ?></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-3">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <?php
                        $result = $db->consulta("SELECT id_representante
                                                   FROM sw_representante r,
                                                        sw_estudiante_periodo_lectivo ep
                                                  WHERE r.id_estudiante = ep.id_estudiante
                                                    AND id_periodo_lectivo = $id_periodo_lectivo");
                        $num_representantes = $db->num_rows($result);
                        ?>
                        <h3><?php echo $num_representantes; ?></h3>

                        <p><?php echo ($num_representantes == 1) ? "Representante" : "Representantes"; ?></p>
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
                            <?php
                            //Obtengo el listado de las sección definidas en la BDD
                            $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
                            $consulta = $db->consulta("SELECT id_paralelo, CONCAT(cu_nombre,' ',pa_nombre,' - ',es_figura,' - ',jo_nombre) AS pa_nombre FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_jornada j WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND j.id_jornada = p.id_jornada AND id_periodo_lectivo = $id_periodo_lectivo ORDER BY pa_orden ASC");
                            ?>
                            <select name="id_paralelo" id="id_paralelo" class="form-control">
                                <?php
                                while ($paralelo = $db->fetch_object($consulta)) {
                                ?>
                                    <option value="<?php echo $paralelo->id_paralelo; ?>"><?php echo $paralelo->pa_nombre; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 id="tutor" class="text-center"></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div id="graficoBarras" style="min-width:310px; height: 600px; margin: 0 auto"></div>
                            </div>
                            <div class="col-md-6">
                                <div id="tablaNumEstudiantes" style="min-width:310px; margin: 0 auto">
                                    <!-- Aquí irá el cuadro estadístico -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Estadística de Géneros</h3>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div id="estadisticaDeGeneros" style="min-width:310px; margin: 0 auto">
                                                                <!-- Aquí va el cuadro estadístico -->
                                                                <div id="lista_generos">
                                                                    <!-- Aquí van los datos poblados desde la BD -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <h4 class="text-center" style="font-family: Arial; font-weight:100;">APROVECHAMIENTO FINAL</h4>
                                            </div>
                                            <div class="row">
                                                <div class="chartjs text-center">
                                                    <canvas id="chartjs_doughnut"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- ./box-body -->
                </div>
            </div>
        </div>

        <!-- Aquí irá el cuadro estadístico -->
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cuadro estadístico</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="cuadroEstadistico">
                                    <!-- Aquí va el cuadro estadístico -->
                                    <div id="lista_items">
                                        <!-- Aquí van los datos poblados desde la BD -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>

<script type="text/javascript">
    var myChart = null;

    $(document).ready(function() {
        contarEstudiantesPeriodoLectivo();

        // $("#id_paralelo").change(function() {
        //     numEstudiantesParalelo($(this).val());
        //     estadistica_generos($(this).val());
        //     actualizar_estadistica_escalas($(this).val(), $("#id_periodo_lectivo").val());
        // });
    });

    function contarEstudiantesPeriodoLectivo() {
        var id_paralelo = $("#id_paralelo").val();
        var id_periodo_lectivo = $("#id_periodo_lectivo").val();
        $.ajax({
            type: "post",
            url: "contarEstudiantesPeriodoLectivo.php",
            dataType: "json",
            success: function(response) {
                if (response > 0) {
                    numEstudiantesParalelo(id_paralelo);
                    estadistica_generos(id_paralelo);
                    estadistica_escalas(id_paralelo, id_periodo_lectivo);
                    cuadro_estadistico(id_periodo_lectivo);
                }
            }
        });
    }

    function numEstudiantesParalelo(id_paralelo) {
        $.ajax({
            url: "numEstudiantesParalelo.php",
            type: "POST",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "json",
            success: function(resp) {
                // console.log(resp);
                var paralelo = [];
                var numero_mujeres = [];
                var numero_hombres = [];
                $("#tutor").html("Tutor: " + resp.tutor);
                paralelo.push(resp.paralelo);
                numero_mujeres.push(resp.numero_mujeres);
                numero_hombres.push(resp.numero_hombres);

                graficar(paralelo, numero_mujeres, numero_hombres, "graficoBarras");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }

    function graficar(paralelo, numero_mujeres, numero_hombres, idDiv) {
        var xValue = paralelo;
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

    function estadistica_generos(id_paralelo) {
        $.ajax({
            type: "POST",
            url: "estadistica_generos.php",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "html",
            success: function(response) {
                $("#lista_generos").html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }

    function estadistica_escalas(id_paralelo, id_periodo_lectivo) {
        $("canvas#chartjs_doughnut").remove();
        $("div.chartjs").append('<canvas id="chartjs_doughnut" style="min-width:310px; height: 50%; margin: 0 auto"></canvas>');
        var ctx = document.getElementById("chartjs_doughnut").getContext("2d");
        $("div.chartjs").append("<div id='img-loader' class='text-center'><img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' /></div>");
        $.ajax({
            type: "POST",
            url: "estadistica_escalas.php",
            data: {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "json",
            success: function(response) {
                // console.log(response);
                $("div#img-loader").remove();
                // ctx.innerHTLM = "";
                var escalas = new Array();
                var cuantos = new Array();
                $.each(response, function(key, value) {
                    escalas.push(value.escala);
                    cantidad = Number(value.contador);
                    cuantos.push(cantidad);
                });
                // console.log(cuantos)
                // Configuración de chartjs
                var data = {
                    labels: escalas,
                    datasets: [{
                        label: 'Nro. de estudiantes',
                        data: cuantos,
                        backgroundColor: [
                            'rgb(51, 153, 102)',
                            'rgb(0, 181, 226)',
                            'rgb(255, 255, 0)',
                            'rgb(255, 80, 80)'
                        ],
                        hoverOffset: 4
                    }]
                };
                var config = {
                    type: 'doughnut',
                    data: data,
                };

                myChart = new Chart(ctx, config);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }

    function actualizar_estadistica_escalas(id_paralelo, id_periodo_lectivo) {
        $("canvas#chartjs_doughnut").remove();
        $("div.chartjs").append('<canvas id="chartjs_doughnut" style="min-width:310px; height: 50%; margin: 0 auto"></canvas>');
        var ctx = document.getElementById("chartjs_doughnut").getContext("2d");
        $("div.chartjs").append("<div id='img-loader' class='text-center'><img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' /></div>");

        $.ajax({
            type: "POST",
            url: "estadistica_escalas.php",
            data: {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "json",
            success: function(response) {
                $("div#img-loader").remove();
                // ctx.innerHTLM = "";
                var escalas = new Array();
                var cuantos = new Array();
                $.each(response, function(key, value) {
                    escalas.push(value.escala);
                    cantidad = Number(value.contador);
                    cuantos.push(cantidad);
                });
                // console.log(cuantos)
                // Configuración de chartjs
                var data = {
                    labels: escalas,
                    datasets: [{
                        label: 'Nro. de estudiantes',
                        data: cuantos,
                        backgroundColor: [
                            'rgb(51, 153, 102)',
                            'rgb(0, 181, 226)',
                            'rgb(255, 255, 0)',
                            'rgb(255, 80, 80)'
                        ],
                        hoverOffset: 4
                    }]
                };
                var config = {
                    type: 'doughnut',
                    data: data,
                };

                myChart = new Chart(ctx, config);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }

    function cuadro_estadistico(id_periodo_lectivo) {
        $.ajax({
            type: "POST",
            url: "cuadro_estadistico_periodo_lectivo.php",
            data: {
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "html",
            success: function(response) {
                // console.log(response);
                $("#lista_items").html(response);
            }
        });
    }
</script>