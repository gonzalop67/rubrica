<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Asistencia
            <small>Registro de asistencia</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">

                <!-- Form Row -->
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <form id="frm-fecha" action="" method="post" autocomplete="off">
                            <!-- Hidden Inputs for PHP variables -->
                            <input type="hidden" id="id_curso" value="<?php echo $_GET['id_curso'] ?>">
                            <input type="hidden" id="id_paralelo" value="<?php echo $_GET['id_paralelo'] ?>">
                            <input type="hidden" id="id_asignatura" value="<?php echo $_GET['id_asignatura'] ?>">
                            <input type="hidden" id="id_periodo_lectivo" value="<?php echo $_GET['id_periodo_lectivo'] ?>">
                            <input type="hidden" id="id_dia_semana" name="id_dia_semana" />

                            <!-- Form Controls Grid (3 Columnas en MD y LG) -->
                            <div class="row">

                                <!-- Bloque 1: Horario -->
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="cboHorarios" class="control-label">Horario:</label>
                                        <select class="form-control fuente9" id="cboHorarios" name="cboHorarios" required>
                                            <option value="">Seleccione...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Bloque 2: Fecha -->
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="fecha" class="control-label">Fecha:</label>
                                        <div class="input-group date">
                                            <input type="text" name="fecha" id="fecha" class="form-control">
                                            <label class="input-group-addon generic-btn" style="cursor: pointer; margin-bottom: 0;" onclick="$('#fecha').focus();">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bloque 3: Hora Clase -->
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="cboHoraClase" class="control-label">Hora Clase:</label>
                                        <select class="form-control fuente9" id="cboHoraClase" name="cboHoraClase" required>
                                            <option value="">Seleccione...</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Title Row -->
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div id="tituloNomina" class="text-center" style="margin-top: 20px; margin-bottom: 20px; font-weight: bold;">
                            NÓMINA DE ESTUDIANTES
                        </div>
                    </div>
                </div>

                <!-- Table Row -->
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <table id="t_asistencia" class="table">
                            <thead>
                                <tr>
                                    <th>Nro.</th>
                                    <th>Id</th>
                                    <th>Nómina</th>
                                    <th>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody id="lista_estudiantes_paralelo">
                                <!-- Estudiantes cargados dinámicamente mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </section>
</div>
<!-- /.content-wrapper -->

<script src="../assets/template/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Configurar título dinámico
        document.getElementById("tituloNomina").innerHTML = "NÓMINA DE ESTUDIANTES (<?php echo htmlspecialchars($_GET['nombre'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($_GET['curso'], ENT_QUOTES, 'UTF-8'); ?>)";

        // Cargar horarios al iniciar
        cargarHorarios();

        // Evento al cambiar la hora clase
        $("#cboHoraClase").change(function() {
            cargarInasistencias();
        });

        // Configuración del Datepicker
        $("#fecha").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            onClose: function(selectDate) {
                // Si el usuario cierra el calendario sin seleccionar fecha, no hacer nada
                if (!selectDate) return;

                const $cboHoraClase = $("#cboHoraClase");
                const id_horario_def = document.getElementById("cboHorarios").value;

                // VALIDACIÓN: Si no hay horario seleccionado, advertir y frenar la petición
                if (!id_horario_def || id_horario_def === "") {
                    Swal.fire({
                        icon: "warning",
                        title: "Atención",
                        text: "Por favor, seleccione un Horario antes de elegir la fecha."
                    });
                    document.getElementById("fecha").value = ""; // Limpiar fecha
                    return;
                }

                // Deshabilitar control mientras procesa
                $cboHoraClase.prop("disabled", true);

                const ds_ordinal = dia_semana(selectDate);
                const id_periodo_lectivo = <?php echo intval($_GET['id_periodo_lectivo']); ?>;

                // 1. Consultar ID del día de la semana
                $.ajax({
                    type: "post",
                    url: "../horarios/consultar_id_dia_semana.php",
                    data: {
                        ds_ordinal: ds_ordinal,
                        id_periodo_lectivo: id_periodo_lectivo,
                        id_horario_def: id_horario_def
                    },
                    dataType: "html",
                    success: function(resultado) {
                        if (!resultado || resultado.trim() === "false") {
                            Swal.fire({
                                icon: "error",
                                title: "Oops! Ocurrió un error inesperado",
                                text: "No se han definido Días de la Semana..."
                            });
                            $cboHoraClase.prop("disabled", false);
                            return;
                        }

                        try {
                            // Reemplazo seguro de eval() por JSON.parse
                            const JSONIdDiaSemana = typeof resultado === "string" ? JSON.parse(resultado) : resultado;
                            const id_dia_semana = JSONIdDiaSemana.id_dia_semana;
                            const id_asignatura = $("#id_asignatura").val();
                            const id_paralelo = $("#id_paralelo").val();

                            // Mostrar loader en la tabla
                            $("#lista_estudiantes_paralelo").html("<tr><td colspan='4' align='center'><img src='../imagenes/ajax-loader-blue.GIF' alt='Procesando...'></td></tr>");

                            // 2. Consultar horas clase
                            $.ajax({
                                type: "post",
                                url: "../horarios/consultar_horas_clase.php",
                                data: {
                                    id_asignatura: id_asignatura,
                                    id_paralelo: id_paralelo,
                                    id_dia_semana: id_dia_semana,
                                    id_horario_def: id_horario_def // <- SOLUCIÓN: Aquí faltaba enviarlo al segundo script PHP
                                },
                                dataType: "html",
                                success: function(resultadoHoras) {
                                    try {
                                        const datos = typeof resultadoHoras === "string" ? JSON.parse(resultadoHoras) : resultadoHoras;
                                        console.log(datos);

                                        // Limpiar select dejando solo "Seleccione..."
                                        $cboHoraClase.prop("length", 1);

                                        if (parseInt(datos.num_registros) === 0) {
                                            Swal.fire({
                                                icon: "error",
                                                title: "Oops! Ocurrió un error inesperado",
                                                text: "No se han definido Horas Clase para la Asignatura seleccionada..."
                                            });
                                        } else {
                                            $cboHoraClase.append(datos.cadena);
                                        }
                                    } catch (e) {
                                        console.error("Error al procesar JSON de horas clase:", e);
                                    } finally {
                                        // Habilitar el select solo CUANDO TERMINA la petición real
                                        $cboHoraClase.prop("disabled", false);
                                        $("#lista_estudiantes_paralelo").html("");
                                    }
                                },
                                error: function() {
                                    $cboHoraClase.prop("disabled", false);
                                    $("#lista_estudiantes_paralelo").html("");
                                }
                            });

                        } catch (error) {
                            console.error("Error al procesar JSON del día:", error);
                            $cboHoraClase.prop("disabled", false);
                        }
                    },
                    error: function() {
                        $cboHoraClase.prop("disabled", false);
                    }
                });
            }
        });
    });

    function cargarHorarios() {
        var id_periodo_lectivo = $("#id_periodo_lectivo").val();
        $.ajax({
            url: "cargar_titulos_horarios.php",
            method: "POST",
            data: {
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "html",
            success: function(data) {
                $("#cboHorarios").append(data);
            }
        });
    }

    function dia_semana(fecha) {
        fecha = fecha.split('-');
        if (fecha.length != 3) {
            return null;
        }
        //Vector para calcular día de la semana de un año regular.
        var regular = [0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5];
        //Vector para calcular día de la semana de un año bisiesto.
        var bisiesto = [0, 3, 4, 0, 2, 5, 0, 3, 6, 1, 4, 6];
        //Vector para hacer la traducción de resultado en día de la semana.
        //var semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        var semana = [7, 1, 2, 3, 4, 5, 6];
        //Día especificado en la fecha recibida por parametro.
        var dia = fecha[2];
        //Módulo acumulado del mes especificado en la fecha recibida por parametro.
        var mes = fecha[1] - 1;
        //Año especificado por la fecha recibida por parametros.
        var anno = fecha[0];
        //Comparación para saber si el año recibido es bisiesto.
        if ((anno % 4 == 0) && !(anno % 100 == 0 && anno % 400 != 0))
            mes = bisiesto[mes];
        else
            mes = regular[mes];
        //Se retorna el resultado del calculo del día de la semana.
        return semana[Math.ceil(Math.ceil(Math.ceil((anno - 1) % 7) + Math.ceil((Math.floor((anno - 1) / 4) - Math.floor((3 * (Math.floor((anno - 1) / 100) + 1)) / 4)) % 7) + mes + dia % 7) % 7)];
    }

    const cargarHorasClase = () => {
        const fecha = document.getElementById("fecha").value;
        const id_horario_def = $("#cboHorarios").val(); // Obtener valor del select de horarios
        const $cboHoraClase = $("#cboHoraClase");

        // VALIDACIÓN: Si no hay horario seleccionado o está vacío, no continuar
        if (!id_horario_def || id_horario_def === "") {
            console.warn("cargarHorasClase cancelado: No se ha seleccionado un horario todavía.");
            $cboHoraClase.prop("length", 1); // Limpiar combo de horas clase
            $cboHoraClase.prop("disabled", true);
            return; // Detiene la ejecución aquí de forma segura
        }

        // VALIDACIÓN: Si no hay fecha seleccionada, tampoco continuar
        if (!fecha || fecha === "") {
            console.warn("cargarHorasClase cancelado: La fecha está vacía.");
            return;
        }

        $cboHoraClase.prop("disabled", true);

        // Consultar el día de la semana
        const ds_ordinal = dia_semana(fecha);
        const id_periodo_lectivo = <?php echo intval($_GET['id_periodo_lectivo']); ?>;

        // Primera petición: Consultar ID del día de la semana
        $.post("../horarios/consultar_id_dia_semana.php", {
            ds_ordinal: ds_ordinal,
            id_periodo_lectivo: id_periodo_lectivo,
            id_horario_def: id_horario_def
        }, function(resultado) {

            if (!resultado) {
                Swal.fire({
                    icon: "error",
                    title: "Oops! Ocurrió un error inesperado",
                    text: "No se han definido Días de la Semana..."
                });
                return;
            }

            try {
                const JSONIdDiaSemana = typeof resultado === "string" ? JSON.parse(resultado) : resultado;
                const id_dia_semana = JSONIdDiaSemana.id_dia_semana;

                if (!id_dia_semana) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops! Ocurrió un error inesperado",
                        text: "No se ha definido el Día de la Semana..."
                    });
                    return;
                }

                // Segunda petición: Consultar horas clase (Ahora garantizamos que id_horario_def tiene valor)
                $.post("../horarios/consultar_horas_clase.php", {
                    id_asignatura: id_asignatura,
                    id_paralelo: id_paralelo,
                    id_dia_semana: id_dia_semana,
                    id_horario_def: id_horario_def
                }, function(resultadoHoras) {

                    console.log(resultadoHoras);
                    const datos = typeof resultadoHoras === "string" ? JSON.parse(resultadoHoras) : resultadoHoras;

                    $cboHoraClase.prop("length", 1);

                    if (parseInt(datos.num_registros) === 0) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops! Ocurrió un error inesperado",
                            text: "No se han definido Horas Clase para la Asignatura seleccionada..."
                        });
                    } else {
                        $cboHoraClase.append(datos.cadena);
                    }

                    $cboHoraClase.prop("disabled", false);
                    $("#lista_estudiantes_paralelo").html("");
                });

            } catch (e) {
                console.error("Error al procesar JSON:", e);
                Swal.fire({
                    icon: "error",
                    title: "Error de formato",
                    text: "La respuesta del servidor no es un JSON válido."
                });
            }
        });
    };

    function cargarInasistencias() {
        // Procedimiento para cargar las inasistencia de los estudiantes
        const id_asignatura = document.getElementById("id_asignatura").value;
        const id_paralelo = document.getElementById("id_paralelo").value;
        const id_hora_clase = document.getElementById("cboHoraClase").value;
        const ae_fecha = document.getElementById("fecha").value;

        $("#lista_estudiantes_paralelo").html("<tr><td colspan='4' align='center'><img src='../imagenes/ajax-loader-blue.GIF' alt='Procesando...'></td></tr>");

        $.ajax({
            type: "post",
            url: "../horarios/listar_inasistencia_paralelo.php",
            data: {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_hora_clase: id_hora_clase,
                ae_fecha: ae_fecha
            },
            dataType: "html",
            success: function(resultado) {
                //anadir el resultado al DOM
                $("#lista_estudiantes_paralelo").html(resultado);
            }
        });
    }

    function actualizar_asistencia(obj, id_asistencia_estudiante) {
        if (obj.checked) abreviatura = "A";
        else abreviatura = "I";
        $.ajax({
            type: "POST",
            url: "../horarios/actualizar_inasistencia_estudiante.php",
            data: {
                id_asistencia_estudiante: id_asistencia_estudiante,
                in_abreviatura: abreviatura
            },
            success: function(resultado) {
                // No desplega nada... esto es solo para ejecutar el codigo php
                console.log(resultado);
            }
        });
    }
</script>