    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Sistema Integrado de Administración Estudiantil</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
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
                    <!-- <a href="#" class="small-box-footer">Ver Autoridades <i class="fa fa-arrow-circle-right"></i></a> -->
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
                    <!-- <a href="#" class="small-box-footer">Ver Docentes <i class="fa fa-arrow-circle-right"></i></a> -->
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
                    <!-- <a href="#" class="small-box-footer">Ver Estudiantes <i class="fa fa-arrow-circle-right"></i></a> -->
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
                    <!-- <a href="#" class="small-box-footer">Ver Representantes <i class="fa fa-arrow-circle-right"></i></a> -->
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
                            $consulta = $db->consulta("SELECT DISTINCT(id_jornada) AS id_jornada FROM sw_paralelo WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY id_jornada ASC");
                            ?>
                            <select name="id_jornada" id="id_jornada" class="form-control">
                                <?php
                                while ($jornada = $db->fetch_object($consulta)) {
                                    $query = $db->consulta("SELECT * FROM sw_jornada WHERE id_jornada = $jornada->id_jornada");
                                    $result = $db->fetch_object($query);
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
    <!-- /.content -->



<script type="text/javascript">
    $(document).ready(function() {
        var id_periodo_lectivo = $("#id_periodo_lectivo").val();
        var id_jornada = $("#id_jornada").val();

        getNumberOfStudents(id_periodo_lectivo, id_jornada);

        cuadro_estadistico(id_jornada);

        $("#id_jornada").change(function() {
            var id_periodo_lectivo = $("#id_periodo_lectivo").val();
            var id_jornada = $("#id_jornada").val();
            getNumberOfStudents(id_periodo_lectivo, id_jornada);
            cuadro_estadistico(id_jornada);
        })
    });

    function getNumberOfStudents(id_periodo_lectivo, id_jornada) {
        $.ajax({
            url: "getNumberOfStudents.php",
            type: "POST",
            data: {
                id_periodo_lectivo: id_periodo_lectivo,
                id_jornada: id_jornada
            },
            dataType: "json",
            success: function(data) {
                //console.log(data);
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

    function cuadro_estadistico(id_jornada) {
        $.ajax({
            type: "POST",
            url: "cuadro_estadistico.php",
            data: {
                id_jornada: id_jornada
            },
            dataType: "html",
            success: function(response) {
                $("#lista_items").html(response);
            }
        });
    }

    function graficar(paralelos, cuantos, idDiv, per_lectivo) {
        var xValue = paralelos;
        var yValue = cuantos;

        var trace1 = {
            x: xValue,
            y: yValue,
            type: 'bar',
            text: yValue,
            textposition: 'auto',
            hoverinfo: 'none',
            marker: {
                color: 'rgb(158,202,225)',
                opacity: 0.6,
                line: {
                    color: 'rbg(8,48,107)',
                    width: 1.5
                }
            }
        };

        var data = [trace1];

        var layout = {
            title: 'Número de estudiantes por paralelo ' + per_lectivo
        };

        Plotly.newPlot(idDiv, data, layout);
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