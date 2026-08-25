<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_SESSION['titulo_pagina'] ?></title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <style>
        .ui-datepicker {
            z-index: 9999 !important;
            /* Obliga al calendario a ponerse por encima del modal */
        }
    </style>
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Definir Horarios
                <small>Listado</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-12 text-right">
                            <!-- Botón para abrir el Modal -->
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-horario" id="btn-nuevo-horario">
                                <i class="fa fa-plus" aria-hidden="true"></i> Crear Horario
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Tabla de contenidos ahora ocupa todo el ancho -->
                        <div class="col-md-12 table-responsive">
                            <div class="form-group">
                                <select name="cboPeriodos" id="cboPeriodos" class="form-control">
                                    <option value="0">Seleccione un periodo lectivo...</option>
                                </select>
                            </div>
                            <table id="example1" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Id</th>
                                        <th>Título</th>
                                        <th>Fecha de Creación</th>
                                        <th>Fecha Inicial</th>
                                        <th>Fecha Final</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_horarios">
                                    <!-- Se popula mediante AJAX -->
                                </tbody>
                            </table>
                            <div class="text-center">
                                <ul class="pagination" id="pagination"></ul>
                            </div>
                            <input type="hidden" id="pagina_actual">
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </section>
        <!-- /.content -->

        <!-- ========================================== -->
        <!-- VENTANA MODAL DEL FORMULARIO               -->
        <!-- ========================================== -->
        <div class="modal fade" id="modal-horario" tabindex="-1" role="dialog" aria-labelledby="titulo">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titulo" style="font-weight: bold;">Nuevo Horario</h4>
                    </div>
                    <div class="modal-body">
                        <form id="frm-horario" action="" method="post">
                            <input type="hidden" name="id_horario_def" id="id_horario_def" value="0">

                            <!-- Fila: Título -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ho_titulo">Título:</label>
                                        <input type="text" name="ho_titulo" id="ho_titulo" class="form-control" autofocus required>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila: Fechas -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_inicial">Fecha Inicial:</label>
                                        <!-- Input y Botón para Fecha Inicial -->
                                        <div class="input-group date">
                                            <input type="text" name="fecha_inicial" id="fecha_inicial" class="form-control" required>
                                            <!-- CAMBIADO: Ahora usa .datepicker('show') -->
                                            <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha_inicial').datepicker('show');">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_final">Fecha Final:</label>
                                        <!-- Input y Botón para Fecha Final -->
                                        <div class="input-group date">
                                            <input type="text" name="fecha_final" id="fecha_final" class="form-control" required>
                                            <!-- CAMBIADO: Ahora usa .datepicker('show') -->
                                            <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha_final').datepicker('show');">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila: Estado y Hora Entrada -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Estado:</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hora_entrada">Hora de entrada:</label>
                                        <input type="time" name="hora_entrada" id="hora_entrada" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila: Nro Horas y Duración -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nro_horas">Nro. de Horas:</label>
                                        <input type="number" name="nro_horas" id="nro_horas" class="form-control" min="1" max="20" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="duracion">Duración (min):</label>
                                        <input type="number" name="duracion" id="duracion" class="form-control" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenedor para las horas dinámicas -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="contenedor-bloques-horas" style="margin-top: 15px; max-height: 250px; overflow-y: auto;">
                                        <!-- Aquí se generarán los campos dinámicamente con JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción dentro del formulario -->
                            <div class="form-group text-right" style="margin-top: 20px; margin-bottom: 0;">
                                <button id="btn-cancel" type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
    <script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            cargarPeriodosLectivosVigentes();
            //pagination(1);
            $("#tbody_horarios").html("<tr><td colspan='8' align='center'>Debe seleccionar un periodo lectivo...</td></tr>");

            $("#cboPeriodos").change(function() {
                let id_periodo_lectivo = $(this).val();
                if (id_periodo_lectivo == 0) {
                    $("#tbody_horarios").html("<tr><td colspan='8' align='center'>Debe seleccionar un periodo lectivo...</td></tr>");
                } else {
                    pagination(1, id_periodo_lectivo);;
                }
            });

            // Escuchar cambios en Nro de Horas, Hora de entrada o Duración
            $('#nro_horas, #hora_entrada, #duracion').on('input change', function() {
                // Truco: Si el botón dice "Actualizar", significa que estamos editando. 
                // No recalculamos de golpe para no borrar lo que el usuario guardó.
                if ($("#btn-save").text().trim() !== "Actualizar") {
                    generarCamposHoras();
                }
            });

            // Limpiar el formulario y el contenedor dinámico al presionar el botón "Crear Horario"
            $('#btn-nuevo-horario').on('click', function() {
                $("#frm-horario")[0].reset();
                $("#id_horario_def").val("0");
                $("#contenedor-bloques-horas").empty();
                $("#btn-save").html("Guardar");
                $("#titulo").html("Nuevo Horario");
            });

            $("#fecha_inicial").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            $("#fecha_final").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            $("#btn-cancel").click(function() {
                $("#frm-horario")[0].reset();
                $("#titulo").html("Nuevo Horario");
                $("#btn-save").html("Guardar");
                $("#btn-save").prop("disabled", false);
                $("#ho_titulo").focus();
            });

            $('#tbody_horarios').on('click', '.item-edit', function() {
                var id_horario_def = $(this).attr('data');

                // 1. Cambiar títulos y textos
                $("#titulo").html("Editar Título de Horario");
                $("#btn-save").html("Actualizar");

                // Limpiamos el contenedor mientras carga la info
                $("#contenedor-bloques-horas").html('<div class="text-center" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Cargando bloques...</div>');

                // 2. Abrir el modal de Bootstrap
                $("#modal-horario").modal('show');

                // 3. Petición AJAX (Mantenemos tu parámetro "id" para no romper nada)
                $.ajax({
                    url: "horario_def/obtener_definicion_horario.php",
                    data: {
                        id: id_horario_def
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(data) {
                        // 4. Seteamos los datos de la tabla maestra
                        $("#id_horario_def").val(id_horario_def);
                        $("#ho_titulo").val(data.ho_titulo);
                        $("#fecha_inicial").val(data.fecha_inicial);
                        $("#fecha_final").val(data.fecha_final);

                        // Asignamos tus nuevos campos al formulario
                        $("#hora_entrada").val(data.hora_entrada);
                        $("#nro_horas").val(data.nro_horas);
                        $("#duracion").val(data.duracion);

                        setearIndice("status", data.estado); // CORREGIDO: cambié status por data.estado ya que tu columna real es 'estado'

                        // 5. Dibujar los bloques de horas guardados
                        var $contenedor = $('#contenedor-bloques-horas');
                        $contenedor.empty(); // Quitamos el spinner de carga

                        if (data.detalles && data.detalles.length > 0) {
                            $contenedor.append('<h5 style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Configuración de cada Bloque:</h5>');

                            // Recorremos las horas guardadas
                            $.each(data.detalles, function(index, bloque) {
                                // Cortamos los segundos (:00) para que el <input type="time"> los lea bien (ej: de "07:15:00" a "07:15")
                                var ini = bloque.hora_inicio.substring(0, 5);
                                var fin = bloque.hora_fin.substring(0, 5);

                                var filaHoraHtml = `
                                    <div class="row" style="margin-bottom: 8px; background: #fdfdfd; padding: 6px; border: 1px solid #e3e3e3; border-radius: 4px; display: flex; align-items: center;">
                                        <div class="col-xs-4">
                                            <input type="text" name="detalle_hora_nombre[]" class="form-control input-sm" value="${bloque.nombre}" placeholder="Nombre" required style="font-weight: bold;">
                                        </div>
                                        <div class="col-xs-4">
                                            <input type="time" name="detalle_hora_inicio[]" class="form-control input-sm" value="${ini}" required>
                                        </div>
                                        <div class="col-xs-4">
                                            <input type="time" name="detalle_hora_fin[]" class="form-control input-sm" value="${fin}" required>
                                        </div>
                                    </div>
                                `;
                                $contenedor.append(filaHoraHtml);
                            });
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            });

            $("#frm-horario").submit(function(e) {
                e.preventDefault();
                let url;
                const id_horario_def = $("#id_horario_def").val();
                const id_periodo_lectivo = $("#cboPeriodos").val();
                const ho_titulo = $.trim($("#ho_titulo").val());
                const fecha_inicial = $.trim($("#fecha_inicial").val());
                const fecha_final = $.trim($("#fecha_final").val());
                const status = $("#status").val();
                const hora_entrada = $.trim($("#hora_entrada").val());
                const nro_horas = $.trim($("#nro_horas").val());
                const duracion = $.trim($("#duracion").val());

                // ========================================================
                // NUEVO: Capturar las listas de las horas personalizadas
                // ========================================================
                const detalle_nombres = $("input[name='detalle_hora_nombre[]']").map(function() {
                    return $(this).val();
                }).get();
                const detalle_inicios = $("input[name='detalle_hora_inicio[]']").map(function() {
                    return $(this).val();
                }).get();
                const detalle_fines = $("input[name='detalle_hora_fin[]']").map(function() {
                    return $(this).val();
                }).get();

                // Validaciones de contenido básico
                if (ho_titulo === "") {
                    mostrarAlerta("Debe ingresar el título del horario.");
                } else if (fecha_inicial === "") {
                    mostrarAlerta("Debe ingresar la fecha inicial.");
                } else if (fecha_final === "") {
                    mostrarAlerta("Debe ingresar la fecha final.");
                } else if (fecha_inicial > fecha_final) {
                    mostrarAlerta("La fecha inicial no puede ser mayor a la fecha final.");
                } else if (!id_periodo_lectivo || id_periodo_lectivo == "0") {
                    mostrarAlerta("Debe seleccionar un periodo lectivo.");
                } else if (hora_entrada === "") {
                    mostrarAlerta("Debe ingresar la hora de entrada.");
                } else if (nro_horas === "" || isNaN(nro_horas)) {
                    mostrarAlerta("Debe ingresar un número de horas válido.");
                } else if (duracion === "" || isNaN(duracion)) {
                    mostrarAlerta("Debe ingresar la duración en minutos.");
                } else if (detalle_nombres.length === 0) {
                    // Nueva validación lógica por si el contenedor está vacío
                    mostrarAlerta("Debe configurar al menos un bloque de horas.");
                } else {

                    const botonSave = $("#btn-save");
                    if (botonSave.text().trim() === "Actualizar") {
                        url = "horario_def/actualizar_definicion_horario.php";
                    } else {
                        url = "horario_def/insertar_definicion_horario.php";
                    }

                    botonSave.prop('disabled', true); // Previene doble clic

                    $.ajax({
                        url: url,
                        method: "post",
                        data: {
                            id_horario_def: id_horario_def,
                            id_periodo_lectivo: id_periodo_lectivo,
                            ho_titulo: ho_titulo,
                            fecha_inicial: fecha_inicial,
                            fecha_final: fecha_final,
                            status: status,
                            hora_entrada: hora_entrada,
                            nro_horas: nro_horas,
                            duracion: duracion,
                            // ========================================================
                            // NUEVO: Enviamos las listas ordenadas al archivo PHP
                            // ========================================================
                            detalle_hora_nombre: detalle_nombres,
                            detalle_hora_inicio: detalle_inicios,
                            detalle_hora_fin: detalle_fines
                        },
                        dataType: "json",
                        success: function(response) {
                            botonSave.prop('disabled', false);

                            Swal.fire({
                                title: response.titulo || "Proceso Técnico",
                                text: response.mensaje,
                                icon: response.tipo_mensaje,
                                confirmButtonText: 'Aceptar'
                            });

                            var pagina_actual = $("#pagina_actual").val();
                            pagination(pagina_actual, id_periodo_lectivo);

                            $("#frm-horario")[0].reset();

                            // NUEVO: Limpiamos también el contenedor de horas dinámicas al guardar con éxito
                            $("#contenedor-bloques-horas").empty();

                            if (botonSave.text().trim() === "Actualizar") {
                                botonSave.html("Guardar");
                                $("#titulo").html("Nuevo Horario");
                            }

                            // Ocultar de forma automática el modal tras un guardado exitoso
                            $("#modal-horario").modal('hide');
                        },
                        error: function(jqXHR) {
                            botonSave.prop('disabled', false);
                            Swal.fire("Error del Servidor", "No se pudo procesar la solicitud.", "error");
                            console.error(jqXHR.responseText);
                        }
                    });
                }
            });

            $('table tbody').on('click', '.item-delete', function(e) {
                e.preventDefault();
                const id = $(this).attr('data');
                var id_periodo_lectivo = $("#cboPeriodos").val();

                Swal.fire({
                    title: "¿Está seguro que quiere eliminar el registro?",
                    text: "No podrá recuperar el registro que va a ser eliminado!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, elimínelo!",
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "post",
                            url: "horario_def/eliminar_titulo_horario.php",
                            method: "POST",
                            data: "id_horario_def=" + id,
                            dataType: "json",
                            success: function(response) {
                                // console.log(r);
                                var pagina_actual = $("#pagina_actual").val();
                                pagination(pagina_actual, id_periodo_lectivo);

                                Swal.fire({
                                    title: response.titulo,
                                    text: response.mensaje,
                                    icon: response.tipo_mensaje,
                                    confirmButtonText: 'Aceptar'
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                // Otro manejador error
                                console.log(jqXHR.responseText);
                            }
                        });
                    }
                });


            });
        });

        // Función auxiliar para no repetir código de alertas
        function mostrarAlerta(mensaje) {
            Swal.fire({
                title: "Ocurrió un error inesperado!",
                text: mensaje,
                icon: "error",
                confirmButtonText: 'Aceptar'
            });
        }

        function cargarPeriodosLectivosVigentes() {
            $.ajax({
                url: "horario_def/cargar_periodos_lectivos_vigentes.php",
                dataType: "html",
                success: function(data) {
                    // console.log(data);
                    $("#cboPeriodos").append(data);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        }

        function pagination(partida, id_periodo_lectivo) {
            $("#pagina_actual").val(partida);
            var url = "horario_def/paginar_horarios.php";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    partida: partida,
                    id_periodo_lectivo: id_periodo_lectivo
                },
                success: function(data) {
                    var array = eval(data);
                    $("#tbody_horarios").html(array[0]);
                    $("#pagination").html(array[1]);
                }
            });
            return false;
        }

        function generarCamposHoras() {
            // 1. Obtener valores y asegurar que sean números
            var nroHoras = parseInt($('#nro_horas').val(), 10) || 0;
            var horaEntrada = $('#hora_entrada').val();
            var duracionMinutos = parseInt($('#duracion').val(), 10) || 0;

            var $contenedor = $('#contenedor-bloques-horas');

            // Limpiamos el contenedor siempre al iniciar
            $contenedor.empty();

            // 2. Control de validación: si falta algo, nos detenemos
            if (nroHoras <= 0 || !horaEntrada || duracionMinutos <= 0) {
                return;
            }

            // Título de la sección dinámica
            $contenedor.append('<h5 style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Configuración de cada Bloque:</h5>');

            // 3. Convertir la hora de entrada a minutos totales
            var partesHora = horaEntrada.split(':');
            var horaBase = parseInt(partesHora[0], 10) || 0;
            var minutoBase = parseInt(partesHora[1], 10) || 0;
            var minutosActuales = (horaBase * 60) + minutoBase;

            // 4. Bucle para construir cada fila con el nombre editable
            for (var i = 1; i <= nroHoras; i++) {

                // Calculamos inicio y fin estimados
                var horaInicioStr = minutosAHoraTexto(minutosActuales);
                minutosActuales += duracionMinutos;
                var horaFinStr = minutosAHoraTexto(minutosActuales);

                // Armamos el diseño de la fila (Ahora con INPUT para el nombre)
                var filaHoraHtml = `
                    <div class="row" style="margin-bottom: 8px; background: #fdfdfd; padding: 6px; border: 1px solid #e3e3e3; border-radius: 4px; display: flex; align-items: center;">
                        <!-- Nombre editable de la hora -->
                        <div class="col-xs-4">
                            <input type="text" name="detalle_hora_nombre[]" class="form-control input-sm" value="Hora ${i}" placeholder="Nombre" required style="font-weight: bold;">
                        </div>
                        <!-- Hora Inicio -->
                        <div class="col-xs-4">
                            <input type="time" name="detalle_hora_inicio[]" class="form-control input-sm" value="${horaInicioStr}" required>
                        </div>
                        <!-- Hora Fin -->
                        <div class="col-xs-4">
                            <input type="time" name="detalle_hora_fin[]" class="form-control input-sm" value="${horaFinStr}" required>
                        </div>
                    </div>
                `;

                $contenedor.append(filaHoraHtml);
            }
        }

        // Función auxiliar para convertir minutos totales (ej. 480) a formato HH:MM (ej. 08:00)
        function minutosAHoraTexto(totalMinutos) {
            var horas = Math.floor(totalMinutos / 60) % 24; // % 24 por si pasa de la medianoche
            var minutos = totalMinutos % 60;

            // Agregar un cero a la izquierda si el número es menor a 10
            var horasStr = (horas < 10 ? '0' : '') + horas;
            var minutosStr = (minutos < 10 ? '0' : '') + minutos;

            return horasStr + ':' + minutosStr;
        }

        function setearIndice(nombreCombo, indice) {
            for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
                if (document.getElementById(nombreCombo).options[i].value == indice) {
                    document.getElementById(nombreCombo).options[i].selected = indice;
                }
        }
    </script>
</body>

</html>