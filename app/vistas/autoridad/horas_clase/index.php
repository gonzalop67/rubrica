<!-- Main content -->
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
                        <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="form-control fuente9" id="hc_nombre" value="" autofocus>
                        <span style="color: #e73d4a" id="mensaje1"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Hora de Inicio:</label>
                    </div>
                    <div class="col-sm-4" style="margin-top: 3px;">
                        <input type="text" class="form-control fuente9" placeholder="formato hh:mm" id="hc_hora_inicio" value="">
                        <span style="color: #e73d4a" id="mensaje2"></span>
                    </div>
                    <div class="col-sm-2 text-right">
                        <label class="control-label" style="position:relative; top:7px;">Hora de Fin:</label>
                    </div>
                    <div class="col-sm-4" style="margin-top: 3px;">
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
                        <th>Ordinal</th>
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
<!-- /.content -->

<script>
    $(document).ready(function() {

        listarHorasClase();

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
                    url: "<?php echo RUTA_URL; ?>/horas_clase/insert",
                    type: "POST",
                    data: {
                        hc_nombre: nombre,
                        hc_hora_inicio: hora_inicio,
                        hc_hora_fin: hora_fin
                    },
                    dataType: "json",
                    success: function(response) {
                        swal({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });
                        listarHorasClase();
                        $("#form-asociar")[0].reset();
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
                url: "<?php echo RUTA_URL; ?>/horas_clase/edit",
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
            const url = $(this).attr('href');
            const id = $(this).attr('data');
            swal({
                    title: "¿Estás seguro de eliminar este registro?",
                    text: "¡Una vez eliminado no podrá recuperarse!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                id: id
                            },
                            dataType: "json",
                            success: function(response) {
                                swal({
                                    title: response.titulo,
                                    text: response.mensaje,
                                    icon: response.tipo_mensaje,
                                    confirmButtonText: 'Aceptar'
                                });
                                listarHorasClase();
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

    function cancelarEdicion() {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
        $("#form-asociar")[0].reset();
    }

    function listarHorasClase() {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/horas_clase/listar",
            type: "get",
            dataType: "html"
        });

        request.done(function(data) {
            $("#lista_items").html(data);
        });
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
                url: "<?php echo RUTA_URL; ?>/horas_clase/update",
                type: "POST",
                dataType: "json",
                data: {
                    id_hora_clase: id_hora_clase,
                    hc_nombre: nombre,
                    hc_hora_inicio: hora_inicio,
                    hc_hora_fin: hora_fin
                },
                success: function(response) {
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
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
            url: "<?php echo RUTA_URL; ?>/horas_clase/saveNewPositions",
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