<style>
    .thead-dark {
        background-color: black;
        color: white;
    }

    .table-wrapper {
        max-height: 500px;
        overflow-y: scroll;

        margin: 0px;
        max-width: 100%;
        overflow-x: scroll;
    }

    .table-striped {
        border: solid 1px black;

        border-collapse: separate;
        border-spacing: 0px;
        min-width: max-content;
    }

    /* Freeze header row */
    .table-striped th {
        position: sticky;
        top: 0px;
        color: white;
        background: teal;
        text-align: center;
        z-index: 3;
    }

    /* First column */
    .table-striped td:nth-child(1) {
        background: white;
        left: 0;
        position: sticky;
        z-index: 2;
    }

    /* First column header */
    .table-striped thead th:nth-child(1) {
        position: sticky;
        left: 0px;
        background: teal;
        color: white;
        text-align: center;
        z-index: 3;
    }

    /* Second column */
    .table-striped td:nth-child(2) {
        position: sticky;
        left: 50px;
        background: white;
        z-index: 2;
    }

    /* Second column header */
    .table-striped thead th:nth-child(2) {
        position: sticky;
        left: 50px;
        background: teal;
        color: white;
        text-align: center;
        z-index: 3;
    }

    /* Third column */
    .table-striped td:nth-child(3) {
        position: sticky;
        left: 100px;
        background: white;
        z-index: 2;
    }

    /* Third column header */
    .table-striped thead th:nth-child(3) {
        position: sticky;
        left: 100px;
        background: teal;
        color: white;
        text-align: center;
        z-index: 3;
    }

    /* Top-left corner cells get higher z-index */
    .table-striped thead th:nth-child(1) {
        z-index: 4;
    }

    .table-striped thead th:nth-child(2) {
        z-index: 4;
    }

    .table-striped thead th:nth-child(3) {
        z-index: 4;
    }

    .table-striped th,
    .table-striped td {
        border: 1px solid black;
        padding: 10px;
    }

    .outer-wrapper {
        border: 1px solid black;
        box-shadow: 0px 0px 3px black;

        margin: 20px;
        border-radius: 5px;
        max-width: fit-content;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo "REPORTE " . $_SESSION['titulo_pagina'] . " (VISUAL)" ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div id="pag_nomina_estudiantes">
                    <!-- Aqui va la paginacion de los estudiantes encontrados -->
                    <div id="total_registros_estudiantes" class="paginacion">
                        <table class="fuente9" cellspacing=4 cellpadding=0 style="border: 0px;">
                            <tr>
                                <td>
                                    <div id="num_estudiantes">&nbsp;N&uacute;mero de estudiantes encontrados:&nbsp;</div>
                                </td>
                                <td>
                                    <div id="paginacion_estudiantes">
                                        <!-- Aqui va la paginacion de estudiantes -->
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
                <div class="table-wrapper">
                    <table class="table-striped">
                        <thead id="thead_notas">
                            <!-- Encabezado de la tabla -->
                            <!-- Las asignaturas se cargarán dinámicamente -->
                        </thead>
                        <tbody id="tbody_notas">
                            <!-- Aqui van las calificaciones del estudiante -->
                            <!-- El contenido de las asignaturas se cargará dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <form action="reportes/reporte_cuadro_final_tutor.php" method="post" target="_blank">
                        <input id="id_paralelo" name="id_paralelo" type="hidden" value="" />
                        <button type="submit">Reporte en PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#id_paralelo").val($.trim($("#id_paralelo_tutor").val()));
        contarEstudiantesParalelo($("#id_paralelo").val());
        listarAsignaturas($("#id_paralelo").val());
        //setInterval(function() {
            listarEstudiantesParalelo();
        //}, 3000); // Actualiza cada 3 segundos
        // listarEstudiantesParalelo();
    });

    function contarEstudiantesParalelo(id_paralelo) {
        var numero_pagina = $("#numero_pagina").val();
        $.post("calificaciones/contar_estudiantes_paralelo.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Ocurrió un error al contar los estudiantes.");
                } else {
                    var JSONNumRegistros = JSON.parse(resultado);
                    var total_registros = JSONNumRegistros.num_registros;
                    $("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: " + total_registros);
                }
            }
        );
    }

    function listarAsignaturas(id_paralelo) {
        // Luego obtengo las asignaturas asociadas al paralelo
        $.post("reportes/cargar_asignaturas_por_paralelo.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error al cargar las asignaturas.");
                } else {
                    // console.log(resultado);
                    $("#thead_notas").html(resultado);
                }
            }
        );
    }

    function listarEstudiantesParalelo() {
        // Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
        var id_paralelo = document.getElementById("id_paralelo").value;
        $.post("reportes/listar_estudiantes_cuadro_final_juntas.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error al cargar las asignaturas.");
                } else {
                    // console.log(resultado);
                    $("#tbody_notas").html(resultado);
                }
            }
        );
    }
</script>