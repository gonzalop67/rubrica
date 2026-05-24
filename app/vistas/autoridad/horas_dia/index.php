<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Horas Clase - Día de la Semana</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form_id" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_dia_semana" class="col-sm-2 control-label text-right">Día de la Semana:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_dia_semana" id="id_dia_semana" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['dias_semana'] as $v) : ?>
                                <option value="<?= $v->id_dia_semana; ?>"><?= $v->ds_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_hora_clase" class="col-sm-2 control-label text-right">Hora Clase:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_hora_clase" id="id_hora_clase" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['horas_clase'] as $v) : ?>
                                <option value="<?= $v->id_hora_clase; ?>"><?= $v->hc_nombre . "(" . $v->hc_hora_inicio . " - " . $v->hc_hora_fin . ")"; ?></option>
                            <?php endforeach; ?>
                        </select>
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
            <table class="table fuente10">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Día</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista_items">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <div class="row">
                <div class="col-sm-10 text-right">
                    <label class="control-label" style="position:relative; top:7px;">Total Horas:</label>
                </div>
                <div class="col-sm-2" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente10 text-right" id="total_horas" value="0" disabled>
                </div>
            </div>
        </div>
        <!-- /.box-footer -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {

        Biblioteca.validacionGeneral('form_id');

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
            "positionClass": "toast-bottom-right",
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados
            "timeOut": "3000"
        };

        $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un día de la semana...</td></tr>");

        $("#id_dia_semana").change(function() {
            var id_dia_semana = $(this).val();
            if (id_dia_semana == "")
                $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un día de la semana...</td></tr>");
            else
                showHorasClaseAsociadas(id_dia_semana);
        });

        $("#form_id").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Día de la Semana con Hora Clase
            let id_dia_semana = document.getElementById("id_dia_semana").value;
            let id_hora_clase = document.getElementById("id_hora_clase").value;

            if (id_dia_semana !== "" && id_hora_clase !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/horas_dia/insert",
                    type: "post",
                    data: {
                        id_dia_semana: id_dia_semana,
                        id_hora_clase: id_hora_clase
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                        showHorasClaseAsociadas(id_dia_semana);
                    }
                });
            } // fin if (id_dia_semana !== "" && id_hora_clase !== "")
        });

        $('table tbody').on('click', '.item-delete', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const id = $(this).attr('data');
            const id_dia_semana = $("#id_dia_semana").val();
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
                                showHorasClaseAsociadas(id_dia_semana);
                            }
                        });
                    }
                });
        });
    });

    function showHorasClaseAsociadas(id_dia_semana) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/horas_dia/listar",
            type: "post",
            data: {
                id_dia_semana: id_dia_semana
            },
            dataType: "json"
        });

        request.done(function(data) {
            console.log(data)
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<tr>' +
                        '<td>' + data[i].id_hora_dia + '</td>' +
                        '<td>' + data[i].ds_nombre + '</td>' +
                        '<td>' + data[i].hc_nombre + ' (' + data[i].hc_hora_inicio + ' - ' + data[i].hc_hora_fin + ')' + '</td>' +
                        '<td>' +
                        '<div class="btn-group">' +
                        '<a href="<?php echo RUTA_URL ?>/horas_dia/delete" class="btn btn-danger btn-sm item-delete" data="' + data[i].id_hora_dia +
                        '" title="Eliminar"><span class="fa fa-remove"></span></a>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                }
                $("#lista_items").html(html);
                $("#total_horas").val(data.length);
            } else {
                $("#lista_items").html("<tr><td colspan='4' align='center'>No se han asociado horas clase a este día de la semana...</td></tr>");
                $("#total_horas").val(0);
            }
        });
    }
</script>