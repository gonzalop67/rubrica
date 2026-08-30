<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>R&uacute;brica Web 2.0</title>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<style>
    table {
        border: none;
        border-collapse: collapse;
    }

    #barra_opciones {
        background-color: #f5f5f5;
        height: 28px;
        padding-top: 4px;
    }

    #num_asignaturas {
        padding-left: 3px;
    }
</style>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $_SESSION['titulo_pagina'] ?>
                <small>Listado</small>
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box box-solid">
                <div class="box-body">
                    <!-- Contenedor principal sin IDs conflictivos -->
                    <div class="well" style="background: #fdfdfd; padding: 15px; border: 1px solid #d2d6de; border-radius: 4px; margin-bottom: 20px; overflow: hidden; display: block; width: 100%;">
                        <div class="row">

                            <!-- Campo Horario -->
                            <div class="col-xs-12 col-sm-5 col-md-4" style="margin-bottom: 10px;">
                                <div style="display: table; width: 100%;">
                                    <!-- CORREGIDO: Se añade vertical-align: middle -->
                                    <span style="display: table-cell; width: 1%; white-space: nowrap; padding-right: 10px; font-weight: bold; color: #555; font-size: 13px; vertical-align: middle;">
                                        <i class="fa fa-clock-o text-blue"></i> Horario:
                                    </span>
                                    <div style="display: table-cell; vertical-align: middle;">
                                        <select id="cboHorarios" name="id_horario_def" class="form-control input-sm" style="width: 100%;">
                                            <!-- Lista de horarios definidos -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo Fecha -->
                            <div class="col-xs-12 col-sm-5 col-md-4" style="margin-bottom: 10px;">
                                <div style="display: table; width: 100%;">
                                    <!-- CORREGIDO: Se añade vertical-align: middle -->
                                    <span style="display: table-cell; width: 1%; white-space: nowrap; padding-right: 10px; font-weight: bold; color: #555; font-size: 13px; vertical-align: middle;">
                                        <i class="fa fa-calendar text-blue"></i> Fecha:
                                    </span>
                                    <div style="display: table-cell; vertical-align: middle;">
                                        <div class="input-group date" style="width: 100%; max-width: 150px; margin-bottom: 0;">
                                            <input id="fecha_asistencia" class="form-control input-sm" type="text" readonly style="background: #fff; cursor: pointer; font-weight: bold; text-align: center;" />
                                            <label class="input-group-addon generic-btn" style="cursor: pointer; background: #eee; color: #333; padding: 5px 10px;" onclick="$('#fecha_asistencia').datepicker('show');">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenedor para Mensajes Dinámicos (Derecha) -->
                            <div class="col-xs-12 col-sm-2 col-md-4 text-right" style="margin-bottom: 10px;">
                                <div id="mensaje_asistencia" style="font-weight: bold; font-size: 13px; color: #333; padding-top: 5px;"></div>
                            </div>

                        </div>
                    </div>
                    <div id="mensaje" class="error"></div>
                    <div id="pag_asignaturas">
                        <!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
                        <div id="total_registros" class="paginacion">
                            <table class="fuente8" width="100%">
                                <tr>
                                    <td>
                                        <div id="num_asignaturas">
                                            N&uacute;mero de Asignaturas encontradas:&nbsp;
                                        </div>
                                    </td>
                                    <td>
                                        <div id="paginacion_asignaturas">
                                            <!-- Aqui va la paginacion de asignaturas -->
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
                        <div class="cabeceraTabla" style="padding: 0; background: #222d32; border: 1px solid #222d32;">
                            <!-- Se generará dinámicamente con JavaScript -->
                        </div>
                        <div id="lista_asignaturas" class="text-center"> </div>
                    </div>
                    <!-- Aqui va la paginacion de los estudiantes encontrados -->
                    <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
                    <div class="cabeceraTabla">
                        <table class="fuente8" width="100%">
                            <tr class="cabeceraTabla">
                                <td width="5%">Nro.</td>
                                <td width="5%">Id.</td>
                                <td width="30%" class="text-left">N&oacute;mina</td>
                                <td width="60%" class="text-left">
                                    <div id="txt_rubricas">Asistencia</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <form id="formulario_asistencia" action="reportes/reporte_asistencia_docente.php" method="post"
                        target="_blank">
                        <div id="img_loader_estudiantes" style="text-align:center"> </div>
                        <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
                        <div id="ver_reporte" style="text-align:center; margin-top:15px; display:none">
                            <!-- Campos ocultos de control previos -->
                            <input id="id_asignatura" name="id_asignatura" type="hidden" />
                            <input id="id_paralelo" name="id_paralelo" type="hidden" />
                            <input id="id_dia_semana" name="id_dia_semana" type="hidden" />
                            <input id="id_hora_clase" name="id_hora_clase" type="hidden" />
                            <input id="id_horario_detalle" name="id_horario_detalle" type="hidden" />
                            <input id="ae_fecha" name="ae_fecha" type="hidden" />

                            <!-- NUEVO: Botón visual de acción para imprimir -->
                            <button type="submit" class="btn btn-danger btn-sm" style="font-weight: bold; padding: 6px 15px;">
                                <i class="fa fa-file-pdf-o"></i> Imprimir Reporte de Asistencia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // 1. Poner la fecha actual automáticamente en el campo bloqueado
            var hoy = new Date();
            var fechaFormateada = hoy.toISOString().split('T')[0]; // Genera AAAA-MM-DD
            $("#fecha_asistencia").val(fechaFormateada);
            $("#ae_fecha").val(fechaFormateada);

            // 2. Inicializar el selector de fechas con tu formato configurado antes
            $("#fecha_asistencia").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                onSelect: function(fechaTexto) {
                    // Guardamos la fecha seleccionada en el campo oculto del reporte
                    $("#ae_fecha").val(fechaTexto);

                    // Cada vez que el usuario escoja una fecha, procesamos el cambio
                    procesarCambioFechaOHorario();
                }
            });

            // 3. Poner la fecha de hoy por defecto al cargar la página por primera vez
            var fechaHoyDatepicker = $.datepicker.formatDate('yy-mm-dd', new Date());
            $("#fecha_asistencia").val(fechaHoyDatepicker);
            $("#ae_fecha").val(fechaHoyDatepicker);

            // 4. Escuchar también cuando cambie el combo de Horarios
            $("#cboHorarios").change(function() {
                procesarCambioFechaOHorario();
            });

            // 5. Función centralizada que calcula el día de la semana y manda a cargar las materias
            function procesarCambioFechaOHorario() {
                var id_horario = $("#cboHorarios").val();
                var fechaSeleccionada = $("#fecha_asistencia").val();

                // ====================================================================
                // NUEVO: Limpieza absoluta de la zona inferior al cambiar coordenadas
                // ====================================================================
                $("#lista_estudiantes_paralelo").html(''); // Borra la nómina de alumnos presente
                $(".cabeceraTabla").html(''); // Borra los títulos dinámicos (Nro, Id, Nómina)
                $("#ver_reporte").hide(); // Esconde el botón rojo de imprimir PDF

                // Si no han seleccionado un horario base, detenemos el proceso de carga
                if (!id_horario || id_horario === "0" || !fechaSeleccionada) {
                    $("#lista_asignaturas").html('');
                    return;
                }

                // Convertir la fecha texto (YYYY-MM-DD) a un objeto Date real en JavaScript
                var objetoFecha = new Date(fechaSeleccionada + 'T00:00:00');

                // `.getDay()` devuelve: 0=Domingo, 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado
                var diaSemanaNumero = objetoFecha.getDay();

                // Guardamos el día de la semana obtenido en el campo oculto para el reporte final
                $("#id_dia_semana").val(diaSemanaNumero);

                // Control de fin de semana: Si eligen un sábado (6) o domingo (0)
                if (diaSemanaNumero === 0 || diaSemanaNumero === 6) {
                    Swal.fire("Atención", "La fecha seleccionada corresponde a un fin de semana. No hay asignaturas registradas.", "info");
                    $("#lista_asignaturas").html('<p class="text-muted text-center"><small>Fin de semana sin clases.</small></p>');
                    return;
                }

                // Si todo está correcto, disparamos la carga AJAX
                listarAsignaturasDocenteFecha(id_horario, diaSemanaNumero);
            }

            // 6. Tu función AJAX que va al servidor a buscar la distributiva de ese día
            function listarAsignaturasDocenteFecha(id_horario, dia_semana) {
                var $contenedor = $('#lista_asignaturas');

                $contenedor.html(
                    '<p class="text-muted text-center">' +
                    '<i class="fa fa-refresh fa-spin"></i> <small>Buscando tus asignaturas asignadas para este día...</small>' +
                    '</p>'
                );

                $.ajax({
                    type: "POST",
                    url: "horarios/cargar_asignaturas_fecha.php",
                    dataType: "json",
                    data: {
                        id_horario_def: id_horario,
                        dia_semana: dia_semana
                    },
                    success: function(response) {
                        $contenedor.empty();
                        $("#num_asignaturas").html(`N&uacute;mero de Asignaturas encontradas: &nbsp; <b>${response.length}</b>`);

                        if (response.length === 0) {
                            $contenedor.html('<p class="text-muted text-center"><small>No tienes asignaturas configuradas para este día de la semana.</small></p>');
                            return;
                        }

                        // Recorrer el listado e inyectar el HTML de las filas de materias encontradas
                        $.each(response, function(index, item) {
                            var nro = index + 1;

                            // Cortamos los segundos (:00) de los tiempos para que luzca limpio (Ej: "07:15")
                            var inicio = item.hora_inicio.substring(0, 5);
                            var fin = item.hora_fin.substring(0, 5);

                            // Formateamos el texto del curso: Ej "Primero de Bachillerato 'A' - Informática"
                            var detalleCurso = `<b>${item.curso_nombre || 'N/A'}</b> '${item.paralelo_nombre || 'N/A'}'`;
                            if (item.es_nombre) {
                                // Si tiene especialidad, se la añadimos al lado con un estilo sutil en gris
                                detalleCurso += ` <span class="text-muted" style="font-size: 11px; display:block; margin-top:2px;"><i class="fa fa-graduation-cap"></i> ${item.es_nombre}</span>`;
                            }

                            var filaMateria = `
                            <div class="fila-asignatura-item" style="padding: 10px 0; border-bottom: 1px solid #f4f4f4; display: table; width: 100%; background: ${index % 2 === 0 ? '#fff' : '#f9f9f9'}">
                                
                                <!-- Número de orden -->
                                <div style="display: table-cell; width: 5%; text-align: center; vertical-align: middle; font-weight: bold; color: #777;">${nro}</div>
                                
                                <!-- Bloque de Hora Clase y Tiempos -->
                                <div style="display: table-cell; width: 18%; text-align: left; vertical-align: middle; padding-left: 5px;">
                                    <div style="margin-bottom: 3px; line-height: 1;">
                                        <span class="label label-primary" style="font-size: 10px; font-weight: bold; padding: 2px 6px; display: inline-block;">${item.hora_nombre}</span>
                                    </div>
                                    <div style="line-height: 1;">
                                        <small class="text-muted" style="font-weight: 600; font-size: 11px; white-space: nowrap;">
                                            <i class="fa fa-clock-o text-blue" style="font-size: 11px;"></i> ${inicio} - ${fin}
                                        </small>
                                    </div>
                                </div>

                                <!-- Asignatura (Letra estilizada a 12px) -->
                                <div style="display: table-cell; width: 27%; text-align: left; padding-left: 5px; font-size: 12px; color: #333; vertical-align: middle;">
                                    <b>${item.as_nombre}</b>
                                </div>
                                
                                <!-- COLUMNA REFINADA: Ahora fusiona Curso, Paralelo y la nueva Especialidad debajo -->
                                <div style="display: table-cell; width: 32%; text-align: left; color: #555; font-size: 12px; vertical-align: middle; padding-right: 5px;">
                                    ${detalleCurso}
                                </div>
                                
                                <!-- Botón de Acción -->
                                <div style="display: table-cell; width: 18%; text-align: center; vertical-align: middle;">
                                    <button type="button" class="btn btn-success btn-xs btn-cargar-nomina" 
                                            data-materia="${item.id_asignatura}" 
                                            data-paralelo="${item.id_paralelo}" 
                                            data-hora="${item.id_horario_detalle}"
                                            style="padding: 3px 8px; font-weight: bold;">
                                        <i class="fa fa-users"></i> Tomar Asistencia
                                    </button>
                                </div>
                            </div>`;
                            $contenedor.append(filaMateria);
                        });

                    },
                    error: function() {
                        $contenedor.html('<p class="text-danger text-center"><small>Error al conectar con el servidor para traer las asignaturas.</small></p>');
                    }
                });
            }

            // 7. Modificamos y movemos cargarHorarios() ADENTRO del ready para que tenga acceso total
            function cargarHorarios() {
                $.ajax({
                    url: "horarios/cargar_titulos_horarios.php",
                    method: "get",
                    dataType: "html",
                    success: function(data) {
                        // 1. Limpiamos y añadimos la opción por defecto más los datos que vienen del servidor
                        $("#cboHorarios").html('<option value="0">Seleccione un horario...</option>').append(data);

                        // 2. Si el servidor nos devolvió información, seleccionamos la primera opción real
                        if ($("#cboHorarios option").length > 1) {
                            $("#cboHorarios").val($("#cboHorarios option:eq(1)").val());
                        }

                        // 3. Forzamos la ejecución: No importa si se seleccionó o no, llamamos a la función
                        // para que el Rastreador 1 nos diga en la consola qué leyó exactamente el sistema
                        procesarCambioFechaOHorario();
                    }
                });
            }

            // Escuchar el clic en el botón "Tomar Asistencia"
            $(document).on('click', '.btn-cargar-nomina', function(e) {
                e.preventDefault();

                var $boton = $(this);
                $('.fila-asignatura-item').css('background', '');
                $boton.closest('.fila-asignatura-item').css('background', '#e0f2fe');

                // 1. EXTRAER COORDENADAS (Aseguramos que capture los atributos reales del botón)
                var id_asignatura = $boton.attr('data-materia');
                var id_paralelo = $boton.attr('data-paralelo');
                var id_hora_clase = $boton.attr('data-hora'); // Lee 'data-hora' que contiene el ID de la hora
                var fecha = $("#fecha_asistencia").val();
                var id_horario_def = $("#cboHorarios").val(); // Forzamos la lectura del combo actual
                var dia_semana = $("#id_dia_semana").val();

                // 2. RELLENAR CAMPOS OCULTOS INMEDIATAMENTE
                // Esto blinda que los inputs del HTML tengan los valores listos antes de cualquier consulta
                $("#id_asignatura").val(id_asignatura);
                $("#id_paralelo").val(id_paralelo);
                $("#id_hora_clase").val(id_hora_clase);
                $("#id_horario_detalle").val(id_hora_clase); // Clave: Guardamos el ID de la hora aquí
                $("#id_dia_semana").val(dia_semana);
                $("#ae_fecha").val(fecha);

                // 3. Mostrar cargador visual
                $("#lista_estudiantes_paralelo").html('<div style="padding:20px;"><i class="fa fa-refresh fa-spin fa-2x text-blue"></i><br><small>Cargando nómina...</small></div>');
                $("#ver_reporte").hide();

                // 4. DISPARAR PETICIÓN AJAX CON VARIABLES DIRECTAS Y LIMPIAS
                $.ajax({
                    type: "POST",
                    url: "horarios/cargar_estudiantes_paralelo.php",
                    dataType: "json",
                    data: {
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura,
                        id_horario_detalle: id_hora_clase, // Pasamos el ID de la hora limpio
                        fecha: fecha,
                        id_horario_def: id_horario_def
                    },
                    success: function(response) {
                        var $contenedor = $("#lista_estudiantes_paralelo");
                        var $cabecera = $(".cabeceraTabla");

                        $contenedor.empty();
                        $cabecera.empty();

                        if (!response.nomina || response.nomina.length === 0) {
                            $contenedor.html('<div class="alert alert-warning" style="margin:10px;"><small>No se encontraron estudiantes matriculados en este paralelo.</small></div>');
                            $cabecera.html('');
                            return;
                        }

                        $("#ver_reporte").show();

                        // Cabecera dinámica
                        var estructuraCabecera = `
                        <div style="display: table; width: 100%; padding: 8px 0; background: #222d32; font-weight: bold; color: #fff; font-size: 11px; text-transform: uppercase; border-radius: 3px 3px 0 0;">
                            <div style="display: table-cell; width: 5%; text-align: center; vertical-align: middle;">Nro.</div>
                            <div style="display: table-cell; width: 5%; text-align: center; vertical-align: middle;">Id.</div>
                            <div style="display: table-cell; width: 40%; text-align: left; vertical-align: middle; padding-left: 5px;">N&oacute;mina del Estudiante</div>
                            <div style="display: table-cell; width: 50%; text-align: left; vertical-align: middle; padding-left: 10px;">Asistencia / Novedad</div>
                        </div>`;
                        $cabecera.html(estructuraCabecera);

                        // Recorrer estudiantes e inyectar las filas con el select alineado a la izquierda
                        $.each(response.nomina, function(index, estudiante) {
                            var nro = index + 1;

                            var opcionesSelect = "";
                            $.each(response.types || response.tipos, function(i, tipo) {
                                var marcado = (estudiante.id_tipo_inasistencia == tipo.id_tipo_inasistencia) ? 'selected' : '';
                                opcionesSelect += `<option value="${tipo.id_tipo_inasistencia}" ${marcado}>${tipo.ti_nombre}</option>`;
                            });

                            var colorFondo = (estudiante.id_tipo_inasistencia == 1) ? '#edfbd8' : '#fce4e4';

                            var filaEstudiante = `
                            <div class="fila-estudiante-item" style="display: table; width: 100%; padding: 6px 0; border-bottom: 1px solid #f4f4f4; background: ${index % 2 === 0 ? '#fff' : '#fafdff'}">
                                <div style="display: table-cell; width: 5%; text-align: center; vertical-align: middle; font-weight: bold; color: #777;">${nro}</div>
                                <div style="display: table-cell; width: 5%; text-align: center; vertical-align: middle; color: #777; font-size: 11px;">${estudiante.id_estudiante}</div>
                                <div style="display: table-cell; width: 40%; text-align: left; vertical-align: middle; padding-left: 5px; color: #333; font-size: 13px;">
                                    <b>${estudiante.apellidos_nombres}</b>
                                </div>
                                <div style="display: table-cell; width: 50%; text-align: left; vertical-align: middle; padding-left: 10px;">
                                    <select class="cbo-asistencia-individual form-control input-sm" 
                                            data-estudiante="${estudiante.id_estudiante}" 
                                            style="width: 100%; max-width: 180px; display: inline-block; font-weight: bold; background-color: ${colorFondo}; cursor: pointer; border-radius: 3px;">
                                        ${opcionesSelect}
                                    </select>
                                </div>
                            </div>`;
                            $contenedor.append(filaEstudiante);
                        });
                    },
                    error: function() {
                        $("#lista_estudiantes_paralelo").html('<div class="text-danger" style="padding:10px;"><small>Error al traer la nómina.</small></div>');
                    }
                });
            });

            // 2. ESCUCHAR EL CAMBIO DEL SELECT EN TIEMPO REAL
            $(document).on('change', '.cbo-asistencia-individual', function() {
                var $select = $(this);
                var id_estudiante = $select.attr('data-estudiante');
                var id_tipo_inasistencia = $select.val(); // Captura la opción seleccionada (1, 2, 3...)

                var id_horario_def = $("#cboHorarios").val();
                var id_paralelo = $("#id_paralelo").val();
                var id_asignatura = $("#id_asignatura").val();
                var id_horario_detalle = $("#id_horario_detalle").val();
                var fecha = $("#fecha_asistencia").val();

                $.ajax({
                    type: "POST",
                    url: "horarios/guardar_asistencia_individual.php",
                    dataType: "json",
                    data: {
                        id_estudiante: id_estudiante,
                        id_horario_def: id_horario_def,
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura,
                        id_horario_detalle: id_horario_detalle,
                        fecha: fecha,
                        id_tipo_inasistencia: id_tipo_inasistencia
                    },
                    success: function(response) {
                        if (response.success) {
                            // Cambiamos el color de fondo del combo de manera interactiva para alertar visualmente
                            if (id_tipo_inasistencia == 1) {
                                $select.css('background-color', '#edfbd8'); // Verde tierno si asiste
                            } else {
                                $select.css('background-color', '#fce4e4'); // Rojo tierno si tiene novedad
                            }
                        } else {
                            Swal.fire("Error", "No se pudo actualizar el estado.", "error");
                        }
                    }
                });
            });

            // Ejecución inicial obligatoria al cargar la vista
            cargarHorarios();
        }); // Fin definitivo del document.ready
    </script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</body>

</html>