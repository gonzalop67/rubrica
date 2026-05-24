<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Definir Horario Semanal</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form_id" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelo:</label>

                    <div class="col-sm-4">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_abreviatura . " " . $v->pa_nombre . " - " . $v->es_abreviatura . " - " . $v->jo_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="mensaje1" style="color: #e73d4a"></span>
                    </div>

                    <label for="id_asignatura" class="col-sm-2 control-label text-right">Asignatura:</label>

                    <div class="col-sm-4">
                        <select class="form-control fuente10" name="id_asignatura" id="id_asignatura" required>
                            <option value="">Seleccione...</option>
                        </select>
                        <span id="mensaje2" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_dia_semana" class="col-sm-2 control-label text-right">Día de la Semana:</label>

                    <div class="col-sm-4">
                        <select class="form-control fuente10" name="id_dia_semana" id="id_dia_semana" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['dias_semana'] as $v) : ?>
                                <option value="<?= $v->id_dia_semana; ?>"><?= $v->ds_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="mensaje3" style="color: #e73d4a"></span>
                    </div>

                    <label for="id_hora_clase" class="col-sm-2 control-label text-right">Hora Clase:</label>

                    <div class="col-sm-4">
                        <select class="form-control fuente10" name="id_hora_clase" id="id_hora_clase" required>
                            <option value="">Seleccione...</option>
                        </select>
                        <span id="mensaje4" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="row" id="botones_insercion">
                    <div class="col-sm-12" style="margin-top: 4px;">
                        <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                            Asociar
                        </button>
                    </div>
                </div>
            </form>
            <!-- Línea de división -->
            <hr>
            <!-- message -->
            <div id="text_message" class="fuente10 text-center"></div>
            <!-- table -->
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Hora Clase</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
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

        Biblioteca.validacionGeneral('form_id');

        $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un paralelo...</td></tr>");

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

        $("#id_paralelo").change(function() {
            var id_paralelo = $(this).val();
            var id_dia_semana = $("#id_dia_semana").val();
            if (id_paralelo == "") {
                $("#mensaje1").html("Debe seleccionar un paralelo...");
                $("#mensaje1").fadeIn();
            } else {
                $("#mensaje1").fadeOut("slow");
                showAsignaturasAsociadas($(this).val());
                showHorarioAsociado(id_paralelo, id_dia_semana);
            }
        });

        $("#id_dia_semana").change(function() {
            var id_dia_semana = $(this).val();
            var id_paralelo = $("#id_paralelo").val();
            if (id_dia_semana == "")
                $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un día de la semana...</td></tr>");
            else {
                showHorasClaseAsociadas(id_dia_semana);
                showHorarioAsociado(id_paralelo, id_dia_semana);
            }
        });

        $("#form_id").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Paralelo, Asignatura, Día de la Semana y Hora Clase
            const id_paralelo = document.getElementById("id_paralelo").value;
            const id_asignatura = document.getElementById("id_asignatura").value;
            const id_dia_semana = document.getElementById("id_dia_semana").value;
            const id_hora_clase = document.getElementById("id_hora_clase").value;

            if (id_paralelo != "" && id_asignatura != "" && id_dia_semana !== "" && id_hora_clase !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/horarios/insert",
                    type: "POST",
                    data: {
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura,
                        id_dia_semana: id_dia_semana,
                        id_hora_clase: id_hora_clase
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        if (response.cruce == 1) {
                            swal({
                                    title: response.titulo,
                                    text: response.mensaje,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willInsert) => {
                                    if (willInsert) {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo RUTA_URL; ?>/horarios/insertar",
                                            data: {
                                                id_paralelo: id_paralelo,
                                                id_asignatura: id_asignatura,
                                                id_dia_semana: id_dia_semana,
                                                id_hora_clase: id_hora_clase
                                            },
                                            dataType: "json",
                                            success: function(response) {
                                                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                                                showHorarioAsociado(id_paralelo, id_dia_semana);
                                            }
                                        });
                                    }
                                });
                        } else {
                            toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                            showHorarioAsociado(id_paralelo, id_dia_semana);
                        }
                    }
                });
            } // fin if (id_dia_semana !== "" && id_hora_clase !== "")
        });

        $('table tbody').on('click', '.item-delete', function(e) {
            e.preventDefault();

            const url = $(this).attr('href');
            const id = $(this).attr('data');

            const id_paralelo = document.getElementById("id_paralelo").value;
            const id_dia_semana = document.getElementById("id_dia_semana").value;

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
                                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                                showHorarioAsociado(id_paralelo, id_dia_semana);
                            }
                        });
                    }
                });
        });

    });

    function showAsignaturasAsociadas(id_paralelo) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/asignaturas_cursos/getByParaleloId",
            method: "post",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                document.getElementById("id_asignatura").length = 0;
                $("#id_asignatura").append("<option value=''>Seleccione...</option>");
                for (let i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id_asignatura + '">' + data[i].as_nombre + '</option>';
                }
                $("#id_asignatura").append(html);
            } else {
                $("#lista_items").html("<tr><td colspan='4' align='center'>No se han asociado asignaturas a este paralelo...</td></tr>");
            }
        });
    }

    function showHorasClaseAsociadas(id_dia_semana) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        document.getElementById("id_hora_clase").length = 0;

        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/horas_dia/listar",
            method: "post",
            data: {
                id_dia_semana: id_dia_semana
            },
            dataType: "json"
        });

        request.done(function(data) {
            var html = '<option value="">Seleccione...</option>';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id_hora_clase + '">' +
                        data[i].hc_nombre + ' (' + data[i].hc_hora_inicio + ' - ' + data[i].hc_hora_fin + ')' + '</option>';
                }
            } else {
                $("#lista_items").html("<tr><td colspan='4' align='center'>No se han asociado horas clase a este día de la semana...</td></tr>");
            }
            $('#id_hora_clase').append(html);
        });
    }

    function showHorarioAsociado(id_paralelo, id_dia_semana) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        if (id_paralelo != "" && id_dia_semana != "") {

            var request = $.ajax({
                url: "<?php echo RUTA_URL; ?>/horarios/listar",
                method: "post",
                data: {
                    id_paralelo: id_paralelo,
                    id_dia_semana: id_dia_semana
                },
                dataType: "html"
            });

            request.done(function(data) {
                $("#lista_items").html(data);
                $("#text_message").html("");
            });
        }
    }
</script>