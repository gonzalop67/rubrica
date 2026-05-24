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

    .thead-dark {
        background-color: black;
        color: white;
    }
</style>

<div id="pagina">
    <input type="hidden" id="in_copiar_y_pegar">
    <div id="titulo_pagina">
        <?php echo "INGRESAR CALIFICACIONES DE " . $_SESSION['titulo_pagina'] ?>
    </div>
    <div class="barra_principal">
        <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">
        <table id="tabla_navegacion" cellpadding="0" cellspacing="0">
            <tr>
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
                                <?php //echo $datos['numero_asignaturas']; 
                                ?>
                                <input type="hidden" id="numero_asignaturas" value="<?php //echo $datos['numero_asignaturas']; 
                                                                                    ?>">
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
                    <th class="text-center">Acciones</th>
                </thead>
                <tbody id="lista_asignaturas">
                    <!-- Aquí irán los datos poblados desde la base de datos mediante AJAX -->
                </tbody>
            </table>
        </div>
        <div id="pag_nomina_estudiantes">
            <!-- Aqui va la paginacion de los estudiantes encontrados -->
            <div id="tituloNomina" class="header2"> RESUMEN SUPLETORIOS </div>
            <form id="formulario_periodo" action="reportes/reporte_anual_docente.php" method="post" target="_blank">
                <div id="lista_supletorios" style="text-align:center"> Debe elegir una asignatura.... </div>
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

<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // obtenerPeriodosSupletorios();
        cargarAsignaturasDocente();
        // mostrarTitulosPeriodos();
    });

    function sel_texto(input) {
        $(input).select();
    }

    function obtenerPeriodosSupletorios() {
        $.get("calificaciones/obtenerPeriodosSupletorios.php", {},
            function(resultado) {
                // console.log(resultado);
                $("#cboPeriodosEvaluacion").append(resultado);
            }
        );
    }

    function cargarAsignaturasDocente() {
        contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
    }

    function contarAsignaturasDocente() {
        $.post("calificaciones/contar_asignaturas_docente.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    var JSONNumRegistros = eval('(' + resultado + ')');
                    var total_registros = JSONNumRegistros.num_registros;
                    $("#num_asignaturas").html("N&uacute;mero de Asignaturas encontradas: " + total_registros);
                    paginarAsignaturasDocente(4, 1, total_registros);
                }
            }
        );
    }

    function paginarAsignaturasDocente(cantidad_registros, num_pagina, total_registros) {
        $.post("calificaciones/paginar_asignaturas_docente.php", {
                cantidad_registros: cantidad_registros,
                num_pagina: num_pagina,
                total_registros: total_registros
            },
            function(resultado) {
                $("#paginacion_asignaturas").html(resultado);
            }
        );
        listarAsignaturasDocente(num_pagina);
    }

    function listarAsignaturasDocente(numero_pagina) {
        $.post("scripts/cargar_asignaturas_docente.php", {
                cantidad_registros: 4,
                numero_pagina: numero_pagina
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#lista_asignaturas").html(resultado);
                }
            }
        );
    }

    function mostrarEstadoRubrica(id_paralelo) {
        $.post("calificaciones/obtener_id_aporte_evaluacion.php", {
                id_paralelo: id_paralelo,
                pe_principal: 2
            },
            function(resultado) {
                // console.log(resultado);
                if (resultado == false) {
                    alert("Error");
                } else {
                    var JSONIdAporteEvaluacion = eval('(' + resultado + ')');
                    var id_aporte_evaluacion = JSONIdAporteEvaluacion.id_aporte_evaluacion;

                    $.post("calificaciones/mostrar_estado_rubrica.php", {
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

                    $.post("calificaciones/obtener_fecha_cierre_aporte.php", {
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

    function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo) {
        mostrarEstadoRubrica(id_paralelo);
        document.getElementById("id_asignatura").value = id_asignatura;
        document.getElementById("id_paralelo").value = id_paralelo;
        // document.getElementById("numero_pagina").value = 1;
        $("#mensaje").html("");
        $("#tituloNomina").html("RESUMEN SUPLETORIOS [" + asignatura + " - " + curso + " " + paralelo + "]");
        //Aqui va la llamada a la paginacion de estudiantes
        cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
        $("#ver_reporte").css("display", "block");
    }

    function cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
        $("#lista_supletorios").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
        $("#ver_reporte").css("display", "none");
        $.post("scripts/listar_supletorios.php", {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura
            },
            function(resultado) {
                $("#lista_supletorios").html(resultado);
                $("#ver_reporte").css("display", "block");
            }
        );
    }

    function editarCalificacion(obj, id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, calificacion_bd) {
        var id = obj.id;
        var calificacion = obj.value;
        var fila = id.substr(id.indexOf("_") + 1);
        var promedio_final = 0;
        var observacion = "";

        var frmFormulario = document.forms["formulario_periodo"];

        if (calificacion == "") calificacion = 0;

        //Validacion de la calificacion
        if (calificacion < 0 || calificacion > 10) {
            // swal("La calificacion debe estar en el rango de 0 a 10", "Se ha producido un error en el ingreso de calificaciones", "error");
            Swal.fire({
                icon: "error",
                title: "La calificacion debe estar en el rango de 0 a 10",
                text: "Se ha producido un error en el ingreso de calificaciones"
            });
            if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "" && calificacion_bd !== 0)
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
                        // Para el promedio debemos tener en cuenta el Articulo 212 del Reglamento de la LOEI
                        if (calificacion < 7) {
                            observacion = "NO APRUEBA";
                        } else {
                            observacion = "APRUEBA";
                        }
                        // document.getElementById("promedio_final_" + fila_elem).value = Math.round(promedio_final * 100) / 100;
                        // document.getElementById("observacion_" + fila_elem).value = observacion;
                        document.getElementById("observacion_" + fila_elem).innerHTML = observacion;
                    }
                }
            }
            $.post("calificaciones/editar_calificacion.php", {
                    id_estudiante: id_estudiante,
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    id_rubrica_personalizada: id_rubrica_personalizada,
                    re_calificacion: calificacion
                },
                function(resultado) {
                    if (resultado) { // Solo si existe resultado
                        $("#mensaje_rubrica").html(resultado);
                    }
                }
            );
        }
    }
</script>