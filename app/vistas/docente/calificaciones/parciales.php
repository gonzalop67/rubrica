<style>
    .barra_principal {
        background: #f5f5f5;
        padding: 5px;
    }

    .row-active {
        background-color: #808080;
    }

    #tabla_paginacion {
        border: none;
    }

    .header2 {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 9pt;
        background-color: #0066FF;
        color: #fff;
        text-align: center;
        padding-top: 1px;
    }
</style>
<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Ingreso de Calificaciones Parciales</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">
            <div class="row">
                <form class="form-horizontal">
                    <div class="row">
                        <div class="form-group">
                            <label for="cboPeriodosEvaluacion" class="col-sm-6 col-md-2 col-lg-1 control-label text-right">Periodo:</label>
                            <div class="col-sm-6 col-md-4 col-lg-2">
                                <select id="cboPeriodosEvaluacion" class="form-control fuente7">
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
                                <span id="mensaje1" style="color: #e73d4a"></span>
                            </div>
                            <label for="cboAportesEvaluacion" class="col-sm-6 col-md-2 col-lg-1 control-label text-right">Aporte:</label>
                            <div class="col-sm-6 col-md-4 col-lg-2">
                                <select id="cboAportesEvaluacion" class="form-control fuente7">
                                    <option value="0"> Seleccione... </option>
                                </select>
                                <span id="mensaje2" style="color: #e73d4a"></span>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-3">
                                <label id="div_estado_rubrica" class="control-label text-center"></label>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-3">
                                <label id="div_fecha_cierre" class="control-label text-center"></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-6 col-md-6 col-lg-6" style="margin-top: -5px;">
                                <label class="col-sm-6 col-md-6 col-lg-6 text-right">&nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;</label>
                                <label class="col-sm-6 col-md-6 col-lg-6 text-left"><?php echo $datos['numero_asignaturas']; ?></label>
                                <input type="hidden" id="numero_asignaturas" value="<?php echo $datos['numero_asignaturas']; ?>">
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6 fuente8" style="margin-top: -35px;">
                                <div id="paginacion_asignaturas">
                                    <ul id="pagination" class="pagination">
                                        <!-- Aqui va la paginacion de asignaturas -->
                                    </ul>
                                </div>
                                <input type="hidden" id="pagina_actual">
                            </div>
                        </div>
                    </div>
                    <div class="row col-sm-12 col-md-12 col-lg-12" style="margin-left: 0px; margin-top: -30px">
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
                </form>
            </div>
        </div>
    </div>

    <div class="box box-solid">
        <div id="pag_nomina_estudiantes">
            <!-- Aqui va la paginacion de los estudiantes encontrados -->
            <div id="total_registros_estudiantes" class="paginacion" style="height:25px;">
                <table class="fuente10" width="100%" cellspacing=4 cellpadding=0 style="border:none">
                    <tr>
                        <td>
                            <div id="num_estudiantes" style="margin-left: 5px;"></div>
                        </td>
                        <td>
                            <div id="leyendas_rubricas" class="fuente9">
                                <!-- Aqui van las leyendas de las rubricas -->
                            </div>
                        </td>
                        <td>
                            <div id="btn-guardar" style="text-align: right; margin-right: 2px;">
                                <button type="button" id="save_all" class="btn btn-success btn-xs" onclick="guardarTabla()" title="Guardar Todas las Calificaciones">Guardar</button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
            <form id="formulario_rubrica" action="<?php echo RUTA_URL ?>/reporte_parciales_docente/reporte" method="post" target="_blank">
                <div id="lista_estudiantes_paralelo" class="text-center"></div>
                <input id="id_asignatura" name="id_asignatura" type="hidden" />
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input id="id_curso" name="id_curso" type="hidden" />
                <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                <input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
                <div id="ver_reporte" style="text-align:center;display:none">
                    <input class="btn btn-primary btn-sm" type="submit" value="Ver Reporte" />
                </div>
            </form>
        </div>
    </div>
</section>

<script src="<?php echo RUTA_URL; ?>/public/js/keypress.js"></script>
<script>
    $(document).ready(function() {
        pagination(1);
        $("#lista_estudiantes_paralelo").html("<div style=\"color: red; font-size: 14px; text-align: center; margin-top: 5px;\">Debe seleccionar un Periodo...</div>");
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

        $("#cboPeriodosEvaluacion").change(function(e) {
            cargarAportesEvaluacion();
            $("#div_estado_rubrica").html("");
            $("#div_fecha_cierre").html("");
            $("#ver_reporte").hide();
            $("#num_estudiantes").html("");
            $("#leyendas_rubricas").html("");
            // $("#paginacion_estudiantes").html("");
            $("#tituloNomina").html("NOMINA DE ESTUDIANTES");
            $("#lista_estudiantes_paralelo").html("<div style=\"color: red; font-size: 14px; text-align: center; margin-top: 5px;\">Debe seleccionar un Parcial...</div>");
        });

        $("#cboAportesEvaluacion").change(function(e) {
            $("#div_estado_rubrica").html("");
            $("#div_fecha_cierre").html("");
            document.getElementById('id_aporte_evaluacion').value = $(this).val();
            $("#ver_reporte").hide();
            $("#num_estudiantes").html("");
            $("#leyendas_rubricas").html("");
            // $("#paginacion_estudiantes").html("");
            $("#lista_estudiantes_paralelo").html("<div style=\"color: red; font-size: 14px; text-align: center; margin-top: 5px;\">Debe seleccionar una Asignatura...</div>");
            $("#tituloNomina").html("NOMINA DE ESTUDIANTES");
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

    function cargarAportesEvaluacion() {
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        document.getElementById("cboAportesEvaluacion").options.length = 1;
        $.post("<?php echo RUTA_URL; ?>/aportes_evaluacion/obtenerAportesEvaluacion", {
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {

                // console.log(resultado);

                if (resultado == false) {
                    alert(resultado);
                } else {
                    $("#cboAportesEvaluacion").append(resultado);
                }
            }
        );
    }

    function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo) {
        document.getElementById("id_asignatura").value = id_asignatura;
        document.getElementById("id_paralelo").value = id_paralelo;
        document.getElementById("id_curso").value = id_curso;
        document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
        document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
        const id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
        const id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        if (id_periodo_evaluacion == 0) {
            swal("Mensaje", "Debe elegir un periodo de evaluación", "info");
        } else if (id_aporte_evaluacion == 0) {
            swal("Mensaje", "Debe elegir un aporte de evaluación", "info");
        } else {
            mostrarEstadoRubrica(id_paralelo);
            mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura, id_curso);
            document.getElementById("tituloNomina").innerHTML = "NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]";
            //Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
            //Primero debo determinar si se trata del examen quimestral de Proyectos Escolares
            cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
        }
    }

    function cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
        $.post("<?php echo RUTA_URL; ?>/calificaciones/contarEstudiantesParalelo", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: " + resultado);
                    //Listar los estudiantes del paralelo
                    listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
                    $("#ver_reporte").show();
                }
            }
        );
    }

    function listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
        var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
        var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
        $("#lista_estudiantes_paralelo").html("<img src='<?php echo RUTA_URL ?>/public/img/ajax-loader-blue.GIF' alt='Procesando...'>");
        $.post("<?php echo RUTA_URL; ?>/calificaciones/listarEstudiantesParalelo", {
                id_curso: id_curso,
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            function(resultado) {
                //anadir el resultado al DOM
                $("#lista_estudiantes_paralelo").html(resultado);
                //adicionar el evento de escucha para "pegar" desde el portapapeles
                var id_primer_input;
                var frmFormulario = document.forms["formulario_rubrica"];
                for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
                    var objElemento = frmFormulario.elements[iCont];
                    if (objElemento.type == 'text') {
                        var id_elem = objElemento.id;
                        var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
                        var campos = id_elem.split("_");
                        if (fila_elem == 1 && (campos[0] == 'puntaje' || campos[0] == 'examenquimestral' || campos[0] == 'cualitativa')) {
                            id_primer_input = id_elem;
                            break;
                        }
                    }
                }
                // console.log(resultado);
                document.getElementById(id_primer_input).addEventListener('paste', handlePaste);
            }
        );
    }

    function mostrarEstadoRubrica(id_paralelo) {
        const id_aporte_evaluacion = $("#cboAportesEvaluacion").val();

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

    function mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura, id_curso) {
        $.post("<?php echo RUTA_URL; ?>/calificaciones/mostrarLeyendasRubricas", {
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_asignatura: id_asignatura,
                id_curso: id_curso
            },
            function(resultado) {
                $("#leyendas_rubricas").html(resultado);
            }
        );
    }

    function trunc(x, posiciones = 0) {
        var s = x.toString()
        var l = s.length
        var decimalLength = s.indexOf('.') + 1
        var numStr = s.substr(0, decimalLength + posiciones)
        return Number(numStr)
    }

    function editarCalificacion(obj, id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, tipo_aporte, calificacion_bd) {
        var calificacion = obj.value;
        var puntaje = 0;
        var id = obj.id;
        var fila = id.substr(id.lastIndexOf("_") + 1);
        var suma_total_aporte = 0;
        var promedio_aporte = 0;
        var contador_calificaciones = 0;
        var suma_ponderados = 0;
        var ponderado_examen = 0;
        var frmFormulario = document.forms["formulario_rubrica"];
        calificacion = replace(calificacion);
        //Validacion de la calificacion
        if (calificacion == "")
            calificacion = 0;
        if (calificacion < 0 || calificacion > 10) {
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
                    var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
                    var campos = id_elem.split("_");
                    if (fila_elem == fila) {
                        if (tipo_aporte == 1) {
                            if (campos[0] == "puntaje") {
                                //Aqui calculo la suma de las calificaciones de cada estudiante
                                objElemento.value = replace(objElemento.value);
                                puntaje = objElemento.value == "" ? 0 : objElemento.value;
                                suma_total_aporte += parseFloat(puntaje);
                                contador_calificaciones++;
                            } else {
                                //Aqui calculo el promedio del aporte y salto
                                promedio_aporte = suma_total_aporte / contador_calificaciones;
                                if (!isNaN(promedio_aporte)) {
                                    if (promedio_aporte !== 0) {
                                        document.getElementById("promedio_" + campos[1] + "_" + fila_elem).value = trunc(promedio_aporte, 2);
                                    } else {
                                        document.getElementById("promedio_" + campos[1] + "_" + fila_elem).value = "";
                                    }
                                    break;
                                }
                            }
                        } else if (tipo_aporte == 2) {
                            if (id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoaportes" || id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoexamen") {
                                //Aqui calculo la suma de los ponderados de los aportes y del examen
                                puntaje = objElemento.value == "" ? 0 : objElemento.value;
                                suma_ponderados += parseFloat(puntaje);
                            } else if (id_elem.substr(0, id_elem.indexOf("_")) == "examenquimestral") {
                                //Aqui calculo el ponderado del examen quimestral
                                objElemento.value = replace(objElemento.value);
                                ponderado_examen = parseFloat(objElemento.value) * 0.2;
                                if (!isNaN(ponderado_examen))
                                    document.getElementById("ponderadoexamen_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(ponderado_examen, 3);
                                else
                                    document.getElementById("ponderadoexamen_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "";
                            } else if (id_elem.substr(0, id_elem.indexOf("_")) == "calificacionquimestral") {
                                document.getElementById("calificacionquimestral_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(suma_ponderados, 2);
                            }
                        }
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
                        // console.log(resultado);
                    }
                }
            );
        }
    }

    function editarCalificacionComportamiento(obj, id_estudiante, id_paralelo, id_asignatura, id_aporte_evaluacion) {
        var str = obj.value;
        var id = obj.id;
        var fila = id.substr(id.indexOf("_") + 1);
        //Validacion de la calificacion
        str = eliminaEspacios(str);
        var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
        var idx = permitidos.indexOf(str);
        if (str != '') {
            if (idx == -1) {
                alert("La calificacion debe estar en el rango de A a E");
                obj.value = "";
            } else {
                $.post("<?php echo RUTA_URL; ?>/calificaciones/editarCalificacionComportamiento", {
                        id_estudiante: id_estudiante,
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura,
                        id_aporte_evaluacion: id_aporte_evaluacion,
                        co_calificacion: str.toUpperCase()
                    },
                    function(resultado) {
                        if (resultado) { // Solo si existe resultado
                            // console.log(resultado);
                        }
                    }
                );
            }
        } else {
            $.post("<?php echo RUTA_URL; ?>/calificaciones/eliminarCalificacionComportamiento", {
                    id_estudiante: id_estudiante,
                    id_paralelo: id_paralelo,
                    id_asignatura: id_asignatura,
                    id_aporte_evaluacion: id_aporte_evaluacion
                },
                function(resultado) {
                    if (resultado) { // Solo si existe resultado
                        // console.log(resultado);
                    }
                }
            );
        }
    }

    function replace(str) {
        let str_aux = "";
        for (let i = 0; i < str.length; i++) {
            const element = str[i];
            if (element === ",") {
                str_aux += ".";
            } else {
                str_aux += element;
            }
        }
        return str_aux;
    }

    function handlePaste(e) {
        var clipboardData, pastedData;

        // Stop data actually being pasted into div
        e.stopPropagation();
        e.preventDefault();

        // Get pasted data via clipboard API
        clipboardData = e.clipboardData || window.clipboardData;
        pastedData = clipboardData.getData('Text');

        // Do whatever with pasteddata
        pegarDatos(pastedData);
    }

    function pegarDatos(data) {
        var rows = data.split("\n");
        var fila = 0;
        var x, z, suma, promedio, nota;
        //alert('Cantidad de filas: ' + rows.length);
        for (var y in rows) {
            var cells = rows[y].split("\t");
            if (fila < rows.length) {
                suma = 0;
                is_number = 1;
                for (var x in cells) {
                    z = parseInt(x) + 1;
                    //alert('.nota'+parseInt(z)+':eq('+y+')');
                    //alert(cells[x]);
                    $('.nota' + parseInt(z) + ':eq(' + y + ')').val(cells[x]);
                    nota = cells[x].replace("", 0);
                    if (!isNaN(nota)) suma += parseFloat(nota);
                    else is_number = 0;
                }
                //Lo siguiente solo funciona para los parciales
                if (is_number == 1) {
                    x++;
                    promedio = trunc(parseFloat(suma / x), 2);
                    if (promedio != 0)
                        $('.promedio:eq(' + fila + ')').val(promedio);
                    else
                        $('.promedio:eq(' + fila + ')').val("");
                }
                fila++;
            }
        }
    }

    function guardarTabla() {
        var frmFormulario = document.forms["formulario_rubrica"];
        var puntaje = 0;
        var id_estudiante, id_paralelo, id_asignatura;
        var id_curso = document.getElementById("id_curso").value;
        for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
            var objElemento = frmFormulario.elements[iCont];
            if (objElemento.type == 'text') {
                var id_elem = objElemento.id;
                var campos = id_elem.split("_");
                if (campos[0] == "puntaje" || campos[0] == "examenquimestral") {
                    puntaje = objElemento.value == "" ? 0 : objElemento.value;
                    puntaje = replace(puntaje);
                    //console.log('puntaje: '+puntaje);
                    id_rubrica_evaluacion = campos[1];
                    id_estudiante = campos[2];
                    id_paralelo = campos[3];
                    id_asignatura = campos[4];

                    //Aqui procesamos la calificacion dentro de la BD
                    $.post("<?php echo RUTA_URL; ?>/Calificaciones/editarCalificacion", {
                            id_estudiante: id_estudiante,
                            id_paralelo: id_paralelo,
                            id_asignatura: id_asignatura,
                            id_rubrica_personalizada: id_rubrica_evaluacion,
                            re_calificacion: puntaje
                        },
                        function(resultado) {
                            if (resultado) { // Solo si existe resultado
                                console.log(resultado);
                            }
                        }
                    );
                } else if (campos[0] == "cualitativa") {
                    //Aqui procesamos la calificacion cualitativa
                    puntaje = objElemento.value == " " ? 0 : objElemento.value;
                    puntaje = replace(puntaje);
                    //console.log('puntaje: '+puntaje);
                    id_estudiante = campos[1];
                    id_paralelo = campos[2];
                    id_asignatura = campos[3];
                    id_aporte_evaluacion = campos[4];
                    if (puntaje !== 0) {
                        //Almacena la calificación cualitativa en la base de datos
                        $.post("docentes/editar_calificacion_cualitativa.php", {
                                id_estudiante: id_estudiante,
                                id_paralelo: id_paralelo,
                                id_asignatura: id_asignatura,
                                id_aporte_evaluacion: id_aporte_evaluacion,
                                rc_calificacion: puntaje
                            },
                            function(resultado) {
                                //alert(resultado);
                                $("#mensaje_rubrica").html(resultado);
                            }
                        );
                    } else {
                        //Elimina la calificación cualitativa de la base de datos
                        $.post("docentes/eliminar_calificacion_cualitativa.php", {
                                id_estudiante: id_estudiante,
                                id_paralelo: id_paralelo,
                                id_asignatura: id_asignatura,
                                id_aporte_evaluacion: id_aporte_evaluacion
                            },
                            function(resultado) {
                                //alert(resultado);
                                $("#mensaje_rubrica").html(resultado);
                            }
                        );
                    }
                }
            }
        }
        swal("Calificaciones ingresadas satisfactoriamente...", "Felicitaciones!", "success");
        listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
    }
</script>