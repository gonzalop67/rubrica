<!-- Main content -->
<div class="content-wrapper">
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Días de la semana</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <form id="form-asociar" action="" method="POST" class="form-horizontal">
                    <input type="hidden" name="id_dia_semana" id="id_dia_semana">
                    <div class="form-group">
                        <label for="cboHorarios" class="col-sm-2 control-label">Horario:</label>
                        <div class="col-sm-10">
                            <select name="cboHorarios" id="cboHorarios" class="form-control" required autofocus>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ds_nombre" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" name="ds_nombre" id="ds_nombre" value="" class="form-control" style="text-transform:uppercase" required>
                            <span id="mensaje1" class="help-block" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12">
                            <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                                Añadir
                            </button>
                        </div>
                    </div>
                    <div class="row" style="display: none;" id="botones_edicion">
                        <div class="col-sm-6">
                            <button id="btn-cancel" type="button" class="btn btn-block" onclick="cancelarEdicion()">
                                Cancelar
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarDiaSemana()">
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

<script src="js/funciones.js"></script>
<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('form-asociar');

        $("#lista_items").html("<tr><td colspan='3' align='center'>Elija un horario...</td></tr>");

        cargarHorarios();

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

        toastr.options = {
            "positionClass": "toast-top-center",
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados
            "timeOut": "5000"
        };

        $("#cboHorarios").change(function() {
            var id_horario_def = $(this).val();
            if (id_horario_def == "") {
                $("#lista_items").html("<tr><td colspan='3' align='center'>Elija un horario...</td></tr>");
            } else {
                cargarDiasSemana();
                $('#ds_nombre').focus();
            }
        });

        $("#form-asociar").submit(function(e) {
            e.preventDefault();
            // Procedimiento para insertar un dia de la semana
            var nombre = $('#ds_nombre').val().trim();
            var id_horario_def = $("#cboHorarios").val();

            $.ajax({
                url: "dias_semana/insertar_dia_semana.php",
                type: "POST",
                data: {
                    ds_nombre: nombre,
                    id_horario_def: id_horario_def
                },
                dataType: "json",
                success: function(response) {
                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                    if (response.tipo_mensaje == "success") {
                        cargarDiasSemana();
                        $('#ds_nombre').val("");
                        $('#ds_nombre').focus();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

        $('table tbody').on('click', '.item-edit', function() {
            var id_dia_semana = $(this).attr('data');
            $("#id_dia_semana").val(id_dia_semana);
            $("#botones_insercion").hide();
            $("#botones_edicion").show();
            //Recupero los datos del dia de la semana
            $.ajax({
                url: "dias_semana/obtener_dia_semana.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_dia_semana: id_dia_semana
                },
                success: function(response) {
                    $("#ds_nombre").val(response.ds_nombre);
                    $("#text_message").html("");
                }
            });
        });

        $('table tbody').on('click', '.item-delete', function(e) {
            e.preventDefault();
            const id = $(this).attr('data');

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
                        url: "dias_semana/eliminar_dia_semana.php",
                        data: {
                            id_dia_semana: id
                        },
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                icon: response.estado,
                                title: response.titulo,
                                text: response.mensaje
                            });
                            cargarDiasSemana();
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

    function cargarDiasSemana() {
        var id_horario_def = $("#cboHorarios").val();

        var request = $.ajax({
            url: "dias_semana/cargar_dias_semana.php",
            method: "post",
            data: {
                id_horario_def: id_horario_def
            },
            dataType: "html"
        });

        request.done(function(data) {
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

    function actualizarDiaSemana() {
        // Recolección de datos
        var id_dia_semana = $("#id_dia_semana").val();
        var nombre = $("#ds_nombre").val();

        // Se procede a la actualización del dia de la semana
        $.ajax({
            type: "POST",
            url: "dias_semana/actualizar_dia_semana.php",
            data: {
                id_dia_semana: id_dia_semana,
                ds_nombre: nombre
            },
            dataType: "json",
            success: function(response) {
                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                $("#text_message").html("");
                cargarDiasSemana();
                cancelarEdicion();
            }
        });
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "dias_semana/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                cargarDiasSemana();
            }
        });
    }
</script>