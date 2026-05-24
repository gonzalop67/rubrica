<style>
    .barra_principal {
        background: #f5f5f5;
        padding: 5px;
    }

    .row-active {
        background-color: #808080;
    }

    #tabla_paginacion,
    #tabla_cabecera {
        border: none;
    }
</style>
<div id="pagina">
    <input type="hidden" id="in_copiar_y_pegar">
    <div id="titulo_pagina">
        <?php echo "INGRESAR CALIFICACIONES " . $datos['titulo'] ?>
    </div>
    <div class="barra_principal">
        <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">
        <table id="tabla_navegacion" cellpadding="0" cellspacing="0">
            <tr>
                <td class="fuente10 text-right">&nbsp;Examen:&nbsp;</td>
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
                <td>
                    <div id="div_estado_rubrica" style="margin-left: 5px;"></div>
                </td>
                <td>
                    <div id="div_fecha_cierre" style="padding-left: 5px;"> </div>
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
        <div id="pag_nomina_estudiantes">
            <!-- Aqui va la paginacion de los estudiantes encontrados -->
            <div id="tituloNomina" class="header2"> RESUMEN SUPLETORIOS </div>
            <form id="formulario_rubrica" action="<?php echo RUTA_URL ?>/reporte_supletorios_docente/reporte" method="post" target="_blank">
                <div id="lista_estudiantes_paralelo" class="text-center"></div>
                <input id="id_asignatura" name="id_asignatura" type="hidden" />
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                <div id="ver_reporte" style="text-align:center;display:none;margin-top:5px">
                    <input class="btn btn-primary btn-sm" type="submit" value="Ver Reporte" />
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo RUTA_URL; ?>/public/js/keypress.js"></script>
<script>
    $(document).ready(function() {
        pagination(1);
        // Determinar si está activada la opción de copiar y pegar
        // para la institución educativa
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/instituciones/obtener_estado_copiar_y_pegar",
            type: "POST",
            success: function(response) {
                // console.log(response);
                $("#in_copiar_y_pegar").val(response);
                if (response == 1) {
                    $("#btn-guardar").show();
                } else {
                    $("#btn-guardar").hide();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
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
        const id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        if (id_periodo_evaluacion == 0) {
            swal("Mensaje", "Debe elegir un periodo de evaluación", "info");
        } else {
            mostrarEstadoRubrica(id_paralelo);
            document.getElementById("tituloNomina").innerHTML = "NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]";
            //Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
            cargarEstudiantesParalelo(id_paralelo, id_asignatura);
        }
    }

    function cargarEstudiantesParalelo(id_paralelo, id_asignatura) {
        $("#lista_estudiantes_paralelo").html("<img src='<?php echo RUTA_URL ?>/public/img/ajax-loader-blue.GIF' alt='Procesando...'>");
        $("#ver_reporte").css("display", "none");
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/calificaciones/listarEstudiantesSupletorio",
            method: "post",
            data: {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_periodo_evaluacion: $("#cboPeriodosEvaluacion").val()
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                $("#lista_estudiantes_paralelo").html(response.cadena);
                $("#ver_reporte").css("display", response.contador == 0 ? "none" : "block");
            },
            error: function(jqXHR, textStatus) {
                console.log(textStatus);
                console.log(jqXHR.responseText);
            }
        });
    }

    function mostrarEstadoRubrica(id_paralelo) {
        $.post("<?php echo RUTA_URL; ?>/calificaciones/obtener_id_aporte_evaluacion", {
                id_periodo_evaluacion: $("#cboPeriodosEvaluacion").val()
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    var JSONIdAporteEvaluacion = eval('(' + resultado + ')');
                    var id_aporte_evaluacion = JSONIdAporteEvaluacion.id_aporte_evaluacion;

                    $.post("<?php echo RUTA_URL; ?>/calificaciones/mostrarEstadoRubrica", {
                            id_aporte_evaluacion: id_aporte_evaluacion,
                            id_paralelo: id_paralelo
                        },
                        function(resultado) {
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                $("#div_estado_rubrica").html(resultado);
                            }
                        }
                    );

                    $.post("<?php echo RUTA_URL; ?>/calificaciones/mostrarFechaCierre", {
                            id_aporte_evaluacion: id_aporte_evaluacion,
                            id_paralelo: id_paralelo
                        },
                        function(resultado) {
                            if (resultado == false) {
                                alert("Error");
                            } else {
                                $("#div_fecha_cierre").html(resultado);
                            }
                        }
                    );
                }
            }
        );
    }

    function editarCalificacion(obj, id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada) {
        var calificacion = obj.value;
        var id = obj.id;
        var fila = id.substr(id.indexOf("_") + 1);
        var promedio_final = 0;
        var observacion = "";

        var frmFormulario = document.forms["formulario_rubrica"];
        //Validacion de la calificacion
        if (calificacion == "")
            calificacion = 0;
        if (calificacion < 0 || calificacion > 7) {
            swal("La calificacion debe estar en el rango de 0 a 10", "Se ha producido un error en el ingreso de calificaciones", "error");
            if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "")
                document.getElementById(id).value = calificacion_bd;
            else
                document.getElementById(id).value = "";
        } else {
            //Aqui va el codigo para calcular el promedio
            for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
                var objElemento = frmFormulario.elements[iCont];
                if (objElemento.type == 'text') {
                    var id_elem = objElemento.id;
                    var fila_elem = id_elem.substr(id_elem.indexOf("_") + 1);
                    if (fila_elem == fila) {
                        // Aca calculo la suma del promedio de los quimestres mas el examen supletorio
                        var promedio_final = document.getElementById("promedio_periodos_" + fila_elem).value;
                        // Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
                        if (calificacion < 7) {
                            observacion = "REMEDIAL";
                        } else {
                            promedio_final = 7;
                            observacion = "APRUEBA";
                        }
                        document.getElementById("promedio_final_" + fila_elem).value = Math.round(promedio_final * 100) / 100;
                        document.getElementById("observacion_" + fila_elem).value = observacion;
                    }
                }
            }
            //Editar la calificación en la BDD
            $.post("<?php echo RUTA_URL; ?>/Calificaciones/editarCalificacion", {
                    id_estudiante: id_estudiante,
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    id_rubrica_personalizada: id_rubrica_personalizada,
                    re_calificacion: calificacion
                },
                function(resultado) {
                    if (resultado) { // Solo si existe resultado
                        console.log(resultado);
                    }
                }
            );
        }
    }
</script>