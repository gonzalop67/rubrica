<!-- Main content -->
<div class="content-wrapper">
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Horas Clase</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <form id="form-asociar" action="" method="POST" class="app-form">
                    <input type="hidden" name="id_hora_clase" id="id_hora_clase">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Horario:</label>
                        </div>
                        <div class="col-sm-10">
                            <select name="cboHorarios" id="cboHorarios" class="form-control" required autofocus>
                                <option value="">Seleccione...</option>
                            </select>
                            <span style="color: #e73d4a" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 4px;">
                            <input type="text" class="form-control fuente9" id="hc_nombre" value="">
                            <span style="color: #e73d4a" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora de Inicio:</label>
                        </div>
                        <div class="col-sm-4" style="margin-top: 4px;">
                            <input type="text" class="form-control fuente9" placeholder="formato hh:mm" id="hc_hora_inicio" value="">
                            <span style="color: #e73d4a" id="mensaje2"></span>
                        </div>
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Hora de Fin:</label>
                        </div>
                        <div class="col-sm-4" style="margin-top: 4px;">
                            <input type="text" class="form-control fuente9" placeholder="formato hh:mm" id="hc_hora_fin" value="">
                            <span style="color: #e73d4a" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                                Añadir
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 4px; display: none;" id="botones_edicion">
                        <div class="col-sm-6">
                            <button id="btn-cancel" type="button" class="btn btn-block" onclick="cancelarEdicion()">
                                Cancelar
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarHoraClase()">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message" class="text-center"></div>
                <!-- table -->
                <table class="table fuente10">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Hora de Inicio</th>
                            <th>Hora de Fin</th>
                            <th>Orden</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
</div>
<!-- /.content -->

<script>
    $(document).ready(function() {

        $("#lista_items").html("<tr><td colspan='6' align='center'>Elija un horario...</td></tr>");

        cargarHorarios();

        $("#cboHorarios").change(function() {
            var id_horario_def = $(this).val();
            if (id_horario_def == "") {
                $("#lista_items").html("<tr><td colspan='6' align='center'>Elija un horario...</td></tr>");
            } else {
                listarHorasClase();
                $('#hc_nombre').focus();
            }
        });

        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });

        $('input').on('focus', function() {
            $(this).select();
        });

        $("#form-asociar").submit(function(e) {
            e.preventDefault();

            // Recolección de datos
            var cont_errores = 0;
            var nombre = $("#hc_nombre").val().trim();
            var hora_inicio = $("#hc_hora_inicio").val().trim();
            var hora_fin = $("#hc_hora_fin").val().trim();
            var id_horario_def = $("#cboHorarios").val();

            // Expresiones regulares para la validación de datos
            var reg_hora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/i;

            // Validación de ingreso de datos
            if (nombre == "") {
                $("#mensaje1").html("Debe ingresar el nombre de la hora clase.");
                $("#mensaje1").fadeIn();
                cont_errores++;
            } else {
                $("#mensaje1").fadeOut("slow");
            }

            if (hora_inicio == "") {
                $("#mensaje2").html("Debe ingresar la hora de inicio de la hora clase.");
                $("#mensaje2").fadeIn("slow");
                cont_errores++;
            } else if (!reg_hora.test(hora_inicio)) {
                $("#mensaje2").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
                $("#mensaje2").fadeIn("slow");
                cont_errores++;
            } else {
                $("#mensaje2").fadeOut();
            }

            if (hora_fin == "") {
                $("#mensaje3").html("Debe ingresar la hora de fin de la hora clase.");
                $("#mensaje3").fadeIn("slow");
                cont_errores++;
            } else if (!reg_hora.test(hora_fin)) {
                $("#mensaje3").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
                $("#mensaje3").fadeIn("slow");
                cont_errores++;
            } else {
                $("#mensaje3").fadeOut();
            }

            if (cont_errores == 0) {
                $.ajax({
                    url: "hora_clase/insertar_hora_clase.php",
                    type: "POST",
                    data: {
                        hc_nombre: nombre,
                        hc_hora_inicio: hora_inicio,
                        hc_hora_fin: hora_fin,
                        id_horario_def: id_horario_def
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                        listarHorasClase();
                        // $("#form-asociar")[0].reset();
                        $("#hc_nombre").val("");
                        $("#hc_hora_inicio").val("");
                        $("#hc_hora_fin").val("");
                        $("#hc_nombre").focus();
                    }
                });
            }
        });

        $('table tbody').on('click', '.item-edit', function() {
            var id_hora_clase = $(this).attr('data');
            $("#id_hora_clase").val(id_hora_clase);
            $("#botones_insercion").hide();
            $("#botones_edicion").show();
            //Recupero los datos de la hora clase
            $.ajax({
                url: "hora_clase/obtener_hora_clase.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_hora_clase: id_hora_clase
                },
                success: function(response) {
                    $("#hc_nombre").val(response.hc_nombre);
                    $("#hc_hora_inicio").val(response.hora_inicio);
                    $("#hc_hora_fin").val(response.hora_fin);
                    $("#text_message").html("");
                }
            });
        });

        $('table tbody').on('click', '.item-delete', function(e) {
            e.preventDefault();
            const id = $(this).attr('data');

            // swal({
            //         title: "¿Está seguro que quiere eliminar el registro?",
            //         text: "No podrá recuperar el registro que va a ser eliminado!",
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonClass: "btn-danger",
            //         confirmButtonText: "Sí, elimínelo!"
            //     },
            //     function() {
            //         $.ajax({
            //             url: "hora_clase/eliminar_hora_clase.php",
            //             method: "POST",
            //             data: "id_hora_clase=" + id,
            //             dataType: "json",
            //             success: function(r) {
            //                 // console.log(r);
            //                 listarHorasClase();
            //                 toastr[r.tipo_mensaje](r.mensaje, r.titulo);
            //             },
            //             error: function(jqXHR, textStatus, errorThrown) {
            //                 // Otro manejador error
            //                 console.log(jqXHR.responseText);
            //             }
            //         });
            //     });

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
                        url: "hora_clase/eliminar_hora_clase.php",
                        data: {
                            id_hora_clase: id
                        },
                        dataType: "json",
                        success: function(r) {
                            listarHorasClase();
                            // toastr[r.tipo_mensaje](r.mensaje, r.titulo);
                            Swal.fire({
                                title: r.titulo,
                                text: r.mensaje,
                                icon: r.tipo_mensaje
                            });
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            });
        });

        $('table tbody').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    if ($(this).attr('data-orden') != (index + 1)) {
                        $(this).attr('data-orden', (index + 1)).addClass('updated');
                    }
                });
                saveNewPositions();
            }
        });
    });

    function listarHorasClase() {
        var id_horario_def = $("#cboHorarios").val();

        var request = $.ajax({
            url: "hora_clase/listar_horas_clase.php",
            method: "post",
            data: {
                id_horario_def: id_horario_def
            },
            dataType: "html"
        });

        request.done(function(data) {
            // console.log(data);
            $("#lista_items").html(data);
        });
    }

    function cargarHorarios() {
        var request = $.ajax({
            url: "dias_semana/cargar_titulos_horarios.php",
            method: "get",
            dataType: "html"
        });

        request.done(function(data) {
            $("#cboHorarios").append(data);
        });
    }

    function cancelarEdicion() {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
        $("#form-asociar")[0].reset();
    }

    function actualizarHoraClase() {
        // Recolección de datos
        var cont_errores = 0;
        var id_hora_clase = $("#id_hora_clase").val();
        var nombre = $("#hc_nombre").val();
        var hora_inicio = $("#hc_hora_inicio").val();
        var hora_fin = $("#hc_hora_fin").val();

        // Expresiones regulares para la validación de datos
        var reg_hora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/i;

        // Validación de ingreso de datos
        if (nombre.trim() == "") {
            $("#mensaje1").html("Debe ingresar el nombre de la hora clase.");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut("slow");
        }

        if (hora_inicio.trim() == "") {
            $("#mensaje2").html("Debe ingresar la hora de inicio de la hora clase.");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else if (!reg_hora.test(hora_inicio)) {
            $("#mensaje2").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (hora_fin.trim() == "") {
            $("#mensaje3").html("Debe ingresar la hora de fin de la hora clase.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else if (!reg_hora.test(hora_fin)) {
            $("#mensaje3").html("Debe ingresar la hora en el formato hh:mm de 24 horas.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la actualización de la hora clase
            $.ajax({
                url: "hora_clase/actualizar_hora_clase.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_hora_clase: id_hora_clase,
                    hc_nombre: nombre,
                    hc_hora_inicio: hora_inicio,
                    hc_hora_fin: hora_fin
                },
                success: function(response) {
                    console.log(response);
                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                    listarHorasClase();
                    cancelarEdicion();
                    $("#text_message").html("");
                }
            });
        }
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "hora_clase/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                listarHorasClase();
            }
        });
    }
</script>