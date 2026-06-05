<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_SESSION['titulo_pagina'] ?></title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Definir Título Horario
                <small>Listado</small>
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box box-solid">
                <div class="box-body">
                    <hr>
                    <div class="row">
                        <div class="col-md-8 table-responsive">
                            <div class="form-group">
                                <!-- <label for="id_periodo_lectivo">Modalidad:</label> -->
                                <select name="cboPeriodos" id="cboPeriodos" class="form-control">
                                    <option value="0">Seleccione un periodo lectivo...</option>
                                </select>
                            </div>
                            <table id="example1" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Id</th>
                                        <th>Títiulo</th>
                                        <th>Fecha de Creación</th>
                                        <th>Fecha Inicial</th>
                                        <th>Fecha Final</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_horarios">
                                    <!-- Aqui vamos a poblar los periodos lectivos ingresados en la BDD mediante AJAX  -->
                                </tbody>
                            </table>
                            <div class="text-center">
                                <ul class="pagination" id="pagination"></ul>
                            </div>
                            <input type="hidden" id="pagina_actual">
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-success">
                                <div id="titulo" class="panel-heading">Nuevo Horario</div>
                            </div>
                            <div class="panel-body">
                                <form id="frm-horario" action="" method="post">
                                    <input type="hidden" name="id_horario_def" id="id_horario_def" value="0">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="ho_titulo">Título:</label>
                                                <input type="text" name="ho_titulo" id="ho_titulo" class="form-control" autofocus>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fecha_inicial">Fecha Inicial:</label>
                                                <div class="controls">
                                                    <div class="input-group date">
                                                        <input type="text" name="fecha_inicial" id="fecha_inicial" class="form-control">
                                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha_inicial').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="fecha_final">Fecha Final:</label>
                                                <div class="controls">
                                                    <div class="input-group date">
                                                        <input type="text" name="fecha_final" id="fecha_final" class="form-control">
                                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha_final').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="status">Estado:</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="status">Hora de entrada:</label>
                                            <input type="text" name="hora_entrada" id="hora_entrada" class="form-control">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="nro_horas">Nro. de Horas:</label>
                                            <input type="text" name="nro_horas" id="nro_horas" class="form-control">
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="duracion">Duración (minutos):</label>
                                            <input type="text" name="duracion" id="duracion" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-top: 5px;">
                                        <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                        <button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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

            $("#fecha_inicial").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            $("#fecha_final").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            // $('#hora_entrada').inputmask('datetime', {
            //     inputFormat: 'HH:MM',
            //     placeholder: 'hh:mm',
            //     insertMode: false // Mantiene fijo el marcador de posición
            // });

            $("#btn-cancel").click(function() {
                $("#frm-horario")[0].reset();
                $("#titulo").html("Nuevo Horario");
                $("#btn-save").html("Guardar");
                $("#btn-save").prop("disabled", false);
                $("#ho_titulo").focus();
            });

            $('#tbody_horarios').on('click', '.item-edit', function() {
                var id_horario_def = $(this).attr('data');
                $("#titulo").html("Editar Título de Horario");
                $("#btn-save").html("Actualizar");
                $.ajax({
                    url: "horario_def/obtener_titulo_horario.php",
                    data: {
                        id: id_horario_def
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        $("#id_horario_def").val(id_horario_def);
                        $("#ho_titulo").val(data.ho_titulo);
                        $("#fecha_inicial").val(data.fecha_inicial);
                        $("#fecha_final").val(data.fecha_final);

                        setearIndice("status", data.status);
                        // if (data.id_periodo_estado == 1)
                        //     $("#btn-save").prop("disabled", false);
                        // else
                        //     $("#btn-save").prop("disabled", true);
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

                // Validaciones de contenido básico
                if (ho_titulo === "") {
                    mostrarAlerta("Debe ingresar el título del horario.");
                } else if (fecha_inicial === "") {
                    mostrarAlerta("Debe ingresar la fecha inicial.");
                } else if (fecha_final === "") {
                    mostrarAlerta("Debe ingresar la fecha final.");
                } else if (fecha_inicial > fecha_final) { // NUEVA VALIDACIÓN DE LOGICA
                    mostrarAlerta("La fecha inicial no puede ser mayor a la fecha final.");
                } else if (!id_periodo_lectivo || id_periodo_lectivo == "0") {
                    mostrarAlerta("Debe seleccionar un periodo lectivo.");
                } else if (hora_entrada === "") {
                    mostrarAlerta("Debe ingresar la hora de entrada.");
                } else if (nro_horas === "" || isNaN(nro_horas)) {
                    mostrarAlerta("Debe ingresar un número de horas válido.");
                } else if (duracion === "" || isNaN(duracion)) {
                    mostrarAlerta("Debe ingresar la duración en minutos.");
                } else {

                    // MEJORA: Usa una clase o atributo data en vez de validar por texto HTML plano
                    const botonSave = $("#btn-save");
                    if (botonSave.text().trim() === "Actualizar") {
                        url = "horario_def/actualizar_titulo_horario.php";
                    } else {
                        url = "horario_def/insertar_titulo_horario.php";
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
                            // CORREGIDO: Ahora sí se envían al servidor
                            hora_entrada: hora_entrada,
                            nro_horas: nro_horas,
                            duracion: duracion
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

                            if (botonSave.text().trim() === "Actualizar") {
                                botonSave.html("Guardar");
                                $("#titulo").html("Nuevo Horario");
                            }
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
                    console.log(data);
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

        function setearIndice(nombreCombo, indice) {
            for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
                if (document.getElementById(nombreCombo).options[i].value == indice) {
                    document.getElementById(nombreCombo).options[i].selected = indice;
                }
        }
    </script>
</body>

</html>