<style>
    /* Forzamos un contenedor con altura absoluta para que Chart.js dibuje la dona a gran tamaño */
    .chart-container-wrapper {
        position: relative;
        height: 380px;
        width: 100%;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Dashboard <small>Sistema Integrado de Administración Estudiantil</small> </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Fila de Tarjetas (Widgets de AdminLTE) -->
        <div class="row">
            <!-- Tarjeta: Autoridades -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <?php
                        $result = $db->consulta("SELECT COUNT(u.id_usuario) as total FROM sw_usuario u, sw_perfil p, sw_usuario_perfil up WHERE p.id_perfil = up.id_perfil AND u.id_usuario = up.id_usuario AND p.pe_nombre = 'Autoridad' AND u.us_activo = 1");
                        $num_autoridades = $db->fetch_object($result)->total;
                        ?>
                        <h3><?php echo $num_autoridades; ?></h3>
                        <p><?php echo ($num_autoridades == 1) ? "Autoridad" : "Autoridades"; ?></p>
                    </div>
                    <div class="icon"> <i class="ion ion-person-stalker"></i> </div>
                </div>
            </div>
            <!-- Tarjeta: Docentes -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <?php
                        $result = $db->consulta("SELECT COUNT(u.id_usuario) as total FROM sw_usuario u, sw_perfil p, sw_usuario_perfil up WHERE p.id_perfil = up.id_perfil AND u.id_usuario = up.id_usuario AND p.pe_nombre = 'Docente' AND u.us_activo = 1");
                        $num_docentes = $db->fetch_object($result)->total;
                        ?>
                        <h3><?php echo $num_docentes; ?></h3>
                        <p><?php echo ($num_docentes == 1) ? "Docente" : "Docentes"; ?></p>
                    </div>
                    <div class="icon"> <i class="ion ion-person"></i> </div>
                </div>
            </div>
            <!-- Tarjeta: Estudiantes -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <?php
                        // Sanitización básica del dato de sesión para evitar SQL injection
                        $id_periodo_lectivo = intval($_SESSION['id_periodo_lectivo']);

                        $consulta = $db->consulta("SELECT pe_estado FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo");
                        $estado = $db->fetch_object($consulta)->pe_estado;

                        if ($estado === 'A') {
                            $result = $db->consulta("SELECT COUNT(DISTINCT ep.id_estudiante) AS total_estudiantes FROM sw_estudiante_periodo_lectivo ep, sw_periodo_lectivo pl WHERE pl.id_periodo_lectivo = ep.id_periodo_lectivo AND pl.pe_estado = 'A' AND ep.activo = 1");
                        } else {
                            $result = $db->consulta("SELECT COUNT(DISTINCT ep.id_estudiante) AS total_estudiantes FROM sw_estudiante_periodo_lectivo ep WHERE ep.id_periodo_lectivo = $id_periodo_lectivo AND ep.activo = 1");
                        }

                        $num_estudiantes = $db->fetch_object($result)->total_estudiantes;
                        ?>
                        <h3><?php echo $num_estudiantes; ?></h3>
                        <p><?php echo ($num_estudiantes == 1) ? "Estudiante" : "Estudiantes"; ?></p>
                    </div>
                    <div class="icon"> <i class="ion ion-stats-bars"></i> </div>
                </div>
            </div>

            <!-- Tarjeta: Representantes -->
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <?php
                        // Se usa DISTINCT para no duplicar representantes con más de un estudiante asignado
                        $result = $db->consulta("SELECT COUNT(DISTINCT r.id_representante) as total FROM sw_representante r, sw_estudiante_periodo_lectivo ep WHERE r.id_estudiante = ep.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo");
                        $num_representantes = $db->fetch_object($result)->total;
                        ?>
                        <h3><?php echo $num_representantes; ?></h3>
                        <p><?php echo ($num_representantes == 1) ? "Representante" : "Representantes"; ?></p>
                    </div>
                    <div class="icon"> <i class="ion ion-woman"></i> </div>
                </div>
            </div>

        </div> <!-- /.row -->

        <!-- Sección de Gráficos Integrados en Cajas de AdminLTE -->
        <div class="row">
            <!-- Bloque izquierdo: Distribución de Género -->
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Distribución por Género</h3>
                        <div class="box-tools pull-right">
                            <?php
                            if ($estado === 'A') {
                                $consulta = $db->consulta("SELECT p.id_paralelo, CONCAT(c.cu_nombre,' ',p.pa_nombre,' - ',e.es_figura,' - ',j.jo_nombre) AS pa_nombre, pl.id_periodo_lectivo FROM sw_periodo_lectivo pl, sw_paralelo p, sw_curso c, sw_especialidad e, sw_jornada j WHERE pl.id_periodo_lectivo = p.id_periodo_lectivo AND c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND j.id_jornada = p.id_jornada AND pl.pe_estado = 'A' ORDER BY e.es_figura, p.pa_orden ASC");
                            } else {
                                $consulta = $db->consulta("SELECT p.id_paralelo, CONCAT(c.cu_nombre,' ',p.pa_nombre,' - ',e.es_figura,' - ',j.jo_nombre) AS pa_nombre, pl.id_periodo_lectivo FROM sw_periodo_lectivo pl, sw_paralelo p, sw_curso c, sw_especialidad e, sw_jornada j WHERE pl.id_periodo_lectivo = p.id_periodo_lectivo AND c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND j.id_jornada = p.id_jornada AND pl.id_periodo_lectivo = $id_periodo_lectivo ORDER BY e.es_figura, p.pa_orden ASC");
                            }

                            ?>
                            <select name="id_paralelo" id="id_paralelo" class="form-control">
                                <?php while ($paralelo = $db->fetch_object($consulta)) { ?>
                                    <option value="<?php echo $paralelo->id_paralelo . "*" . $paralelo->id_periodo_lectivo; ?>"><?php echo $paralelo->pa_nombre; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="box-body">
                        <h4 id="tutor" class="text-center text-bold text-blue" style="margin-bottom:15px;"></h4>
                        <div class="chart-container-wrapper">
                            <canvas id="graficoBarras"></canvas>
                        </div>
                        <div id="lista_generos" style="margin-top:15px;"></div>
                    </div>
                </div>
            </div>

            <!-- Bloque derecho: Aprovechamiento Escolar (Dona Corregida) -->
            <div class="col-md-5">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title text-center" style="display:block; width:100%;">Aprovechamiento Final</h3>
                    </div>
                    <div class="box-body">
                        <div class="chart-container-wrapper" id="contenedorDona">
                            <canvas id="chartjs_doughnut"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Cuadro Estadístico -->
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cuadro estadístico</h3>
                    </div>
                    <div class="box-body">
                        <div id="cuadroEstadistico">
                            <div id="lista_items"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Manejadores globales de gráficos para actualización limpia en memoria
    var chartBarrasInstance = null;
    var chartDonaInstance = null;

    $(document).ready(function() {
        contarEstudiantesPeriodoLectivo();

        $("#id_paralelo").change(function() {
            var arrayIds = $(this).val().split("*");
            var idParalelo = arrayIds[0];
            var idPeriodoLectivo = arrayIds[1];

            actualizarDashboard(idParalelo, idPeriodoLectivo);
        });
    });

    function contarEstudiantesPeriodoLectivo() {
        var arrayIds = $("#id_paralelo").val().split("*");
        $.ajax({
            type: "post",
            url: "contarEstudiantesPeriodoLectivo.php",
            dataType: "json",
            success: function(response) {
                if (Number(response) > 0) {
                    actualizarDashboard(arrayIds[0], arrayIds[1]);
                }
            }
        });
    }

    function actualizarDashboard(idParalelo, idPeriodoLectivo) {
        numEstudiantesParalelo(idParalelo, idPeriodoLectivo);
        estadistica_generos(idParalelo);
        cargar_o_actualizar_escalas(idParalelo, idPeriodoLectivo);
        cuadro_estadistico(idPeriodoLectivo);
    }

    function numEstudiantesParalelo(id_paralelo, id_periodo_lectivo) {
        $.ajax({
            url: "numEstudiantesParalelo.php",
            type: "POST",
            data: {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "json",
            success: function(resp) {
                $("#tutor").html("<i class='fa fa-user'></i> Tutor: " + (resp.tutor || "No asignado"));

                if (chartBarrasInstance !== null) {
                    chartBarrasInstance.destroy();
                }

                var ctx = document.getElementById('graficoBarras').getContext('2d');
                chartBarrasInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [resp.paralelo],
                        datasets: [{
                                label: 'Mujeres',
                                data: [resp.numero_mujeres],
                                backgroundColor: '#dd4b39'
                            },
                            {
                                label: 'Hombres',
                                data: [resp.numero_hombres],
                                backgroundColor: '#3c8dbc'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
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
            error: function(jqXHR) {
                console.error("Error en estadistica_generos:", jqXHR.responseText);
            }
        });
    }

    function cargar_o_actualizar_escalas(id_paralelo, id_periodo_lectivo) {
        // 1. ELIMINAR EL LOADER ANTERIOR SI EXISTE (Evita duplicados)
        $("#img-loader").remove();

        // 2. INYECTAR TU SPINNER GIF ORIGINAL (Centrado de forma absoluta)
        $("#contenedorDona").append("<div id='img-loader' class='text-center' style='position:absolute; top:45%; left:45%; z-index:10;'><img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' /></div>");

        $("#contenedorDona").append("");
        $.ajax({
            type: "POST",
            url: "estadistica_escalas.php",
            data: {
                id_paralelo: id_paralelo,
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "json",
            success: function(response) {
                // 3. REMOVER CUANDO LA PETICIÓN ENTRA EN SUCCESS
                $("#img-loader").remove();

                var escalas = [],
                    cuantos = [];
                $.each(response, function(key, value) {
                    escalas.push(value.escala);
                    cuantos.push(Number(value.contador));
                });

                if (chartDonaInstance !== null) {
                    chartDonaInstance.destroy();
                }

                var ctx = document.getElementById("chartjs_doughnut").getContext('2d');
                chartDonaInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: escalas,
                        datasets: [{
                            data: cuantos,
                            backgroundColor: ['#00a65a', '#00c0ef', '#f39c12', '#dd4b39'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 12
                                }
                            }
                        },
                        cutout: '60%' // Modificado para un anillo estilizado y grande
                    }
                });
            },
            error: function() {
                // 4. REMOVER TAMBIÉN SI OCURRE UN ERROR
                $("#img-loader").remove();
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
                $("#lista_items").html(response);
            }
        });
    }
</script>