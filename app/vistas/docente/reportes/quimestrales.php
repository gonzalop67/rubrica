<div id="pagina">
    <div id="titulo_pagina">
        <?php echo "REPORTE DE CALIFICACIONES " . $datos['titulo'] ?>
    </div>
    <div class="barra_principal">
        <table id="tabla_navegacion" cellpadding="0" cellspacing="0">
            <tr>
                <td class="fuente10 text-right">&nbsp;Periodo:&nbsp;</td>
                <td>
                    <select id="cboPeriodosEvaluacion" class="fuente9">
                        <option value=""> Seleccione... </option>
                        <?php
                        foreach ($datos['periodos_evaluacion'] as $p) {
                        ?>
                            <option value="<?php echo $p->id_periodo_evaluacion; ?>">
                                <?php echo $p->pe_nombre; ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <div id="pag_asignaturas" style="background-color: #f5f5f5;">
        <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
        <div id="total_registros">
            <table id="tabla_paginacion" width="100%" cellspacing=4 cellpadding=0>
                <tr>
                    <td>
                        <div id="num_asignaturas" class="fuente10" style="margin-left: 4px;">
                            &nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;
                            <span>
                                <?php echo $datos['numero_asignaturas']; ?>
                                <input type="hidden" id="numero_asignaturas" value="<?php echo $datos['numero_asignaturas']; ?>">
                            </span>
                        </div>
                    </td>
                    <td>
                        <div id="paginacion_asignaturas">
                            <ul id="pagination" class="pagination">
                                <!-- Aqui va la paginacion de asignaturas -->
                            </ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </td>
                </tr>
            </table>
        </div>
        <div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
        <div class="table-responsive">
            <table class="table fuente8">
                <thead class="thead-dark fuente9">
                    <th>Nro.</th>
                    <th>Asignatura</th>
                    <th>Curso</th>
                    <th>Paralelo</th>
                    <th>Seleccionar</th>
                </thead>
                <tbody id="lista_asignaturas">
                    <!-- Aquí irán los datos poblados desde la base de datos mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
    <div id="pag_nomina_estudiantes">
        <!-- Aqui va la paginacion de los estudiantes encontrados -->
        <div id="total_registros_estudiantes" class="paginacion" style="height:25px;">
            <div id="num_estudiantes" class="fuente10" style="margin-left: 5px;">&nbsp;Número de Estudiantes:&nbsp;</div>
        </div>
    </div>
    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
    <div id="tbl_notas" class="table-responsive text-center">
        <!-- Aquí van las notas generadas dinámicamente mediante AJAX -->
    </div>
    <form id="formulario_periodo" target="_blank" action="<?php echo RUTA_URL ?>/reporte_docente_quimestral/reporte" method="post">
        <div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
        <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
            <input id="id_asignatura" name="id_asignatura" type="hidden" />
            <input id="id_paralelo" name="id_paralelo" type="hidden" />
            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
            <input class="btn btn-primary btn-sm" type="submit" value="Ver Reporte" />
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        pagination(1);
        //
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        const url = "<?php echo RUTA_URL; ?>/calificaciones/paginar_asignaturas";
        const numero_asignaturas = $("#numero_asignaturas").val();
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida,
                numero_asignaturas: numero_asignaturas
            },
            success: function(data) {
                var array = eval(data);
                $("#lista_asignaturas").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }

    function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo) {
        document.getElementById("id_asignatura").value = id_asignatura;
        document.getElementById("id_paralelo").value = id_paralelo;
        document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        if (id_periodo_evaluacion == 0) {
            swal("Información", "Debe elegir un periodo de evaluación...", "info");
        } else {
            $("#lista_estudiantes_paralelo").html("");
            $("#tituloNomina").html("CALIFICACIONES QUIMESTRALES [" + asignatura + " - " + curso + " " + paralelo + "]");
            //Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
            obtenerDetallePeriodoEvaluacion(id_paralelo, id_asignatura);
        }
    }

    function obtenerDetallePeriodoEvaluacion(id_paralelo, id_asignatura) {
        //Obtener los titulos de los insumos de evaluacion
        document.getElementById("id_asignatura").value = id_asignatura;
        document.getElementById("id_paralelo").value = id_paralelo;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
        $("#tbl_notas").html("<img src='<?php echo RUTA_URL ?>/public/img/ajax-loader-blue.GIF' alt='Procesando...'>");
        $("#ver_reporte").css("display", "none");
        $.post("<?php echo RUTA_URL; ?>/reporte_docente_quimestral/obtenerDetallePeriodoEvaluacion", {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {
                console.log(resultado);
                $("#tbl_notas").html(resultado);
                $("#ver_reporte").css("display", "block");
            }
        );
    }
</script>