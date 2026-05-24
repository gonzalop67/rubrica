<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Distributivo Docente</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-asociar" action="" class="form-horizontal">
                <input type="hidden" name="id_distributivo" id="id_distributivo">
                <div class="form-group">
                    <label for="id_usuario" class="col-sm-2 control-label text-right">Docente:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_usuario" id="id_usuario" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['docentes'] as $v) : ?>
                                <option value="<?= $v->id_usuario; ?>"><?= $v->us_titulo . " " . $v->us_fullname; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="mensaje1" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelos:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="mensaje2" style="color: #e73d4a"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_asignatura" class="col-sm-2 control-label text-right">Asignatura:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_asignatura" id="id_asignatura" required>
                            <option value="">Seleccione...</option>
                            <!-- Aquí se va a poblar las asignaturas dinámicamente mediante AJAX -->
                        </select>
                        <span id="mensaje3" style="color: #e73d4a"></span>
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
                        <th>Paralelo</th>
                        <th>Asignatura</th>
                        <th>Presencial</th>
                        <th>Autónomo</th>
                        <th>Tutoría</th>
                        <th>SubTotal</th>
                        <th>
                            <!-- Aqui va el boton de eliminar -->
                        </th>
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
                <div class="col-sm-3 text-right">
                    <label class="control-label" style="position:relative; top:7px;">Presenciales:</label>
                </div>
                <div class="col-sm-1" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente9 text-right" id="horas_presenciales" value="0" disabled>
                </div>
                <div class="col-sm-3 text-right">
                    <label class="control-label" style="position:relative; top:7px;">Tutorias:</label>
                </div>
                <div class="col-sm-1" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente9 text-right" id="horas_tutorias" value="0" disabled>
                </div>
                <div class="col-sm-3 text-right">
                    <label class="control-label" style="position:relative; top:7px;">Total Horas:</label>
                </div>
                <div class="col-sm-1" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente9 text-right" id="total_horas" value="0" disabled>
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

        Biblioteca.validacionGeneral('form-asociar');

        $("#lista_items").html("<tr><td colspan='8' align='center'>Debe seleccionar un docente...</td></tr>");

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

        $('#id_usuario').select2();

        $("#id_usuario").change(function() {
            var id_usuario = $(this).val();
            if (id_usuario == "") {
                $("#horas_presenciales").val("0");
                $("#horas_tutorias").val("0");
                $("#total_horas").val("0");
                $("#mensaje1").html("Debe seleccionar un docente...");
                $("#mensaje1").fadeIn();
            } else {
                $("#mensaje1").fadeOut("slow");
                listarDistributivo();
            }
        });

        $('#id_paralelo').select2();

        $("#id_paralelo").change(function() {
            var id_paralelo = $(this).val();
            if (id_paralelo == "") {
                $("#mensaje2").html("Debe seleccionar un paralelo...");
                $("#mensaje2").fadeIn();
            } else {
                $("#mensaje2").fadeOut("slow");
                showAsignaturasAsociadas($(this).val());
            }
        });

        $('#id_asignatura').select2();

        $("#form-asociar").submit(function(e) {
            e.preventDefault();
            // Recolección de datos
            var id_usuario = $("#id_usuario").val();
            var id_paralelo = $("#id_paralelo").val();
            var id_asignatura = $("#id_asignatura").val();
            if (id_usuario !== "" && id_paralelo !== "" && id_asignatura !== "") {
                // Se procede a la inserción del item del distributivo
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                var request = $.ajax({
                    url: "<?php echo RUTA_URL; ?>/distributivos/insert",
                    type: "POST",
                    data: {
                        id_usuario: id_usuario,
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura
                    },
                    dataType: "json"
                });

                request.done(function(response) {
                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                    listarDistributivo();
                    $("#text_message").html("");
                });
            }
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
                                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                                listarDistributivo();
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
            type: "POST",
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
                $("#lista_items").html("<tr><td colspan='7' align='center'>No se han asociado asignaturas a este paralelo...</td></tr>");
                $("#total_horas").val(0);
            }
        });
    }

    function listarDistributivo() {
        var id_usuario = $("#id_usuario").val();
        if (id_usuario == "") {
            $("#mensaje1").html("Debe seleccionar un docente...");
            $("#mensaje1").fadein();
        } else {
            var request = $.ajax({
                url: "<?php echo RUTA_URL; ?>/distributivos/getByUsuarioId",
                type: "POST",
                data: {
                    id_usuario: id_usuario
                },
                dataType: "json"
            });

            request.done(function(data) {
                var datos = JSON.parse(data);
                $("#lista_items").html(datos.cadena);
                $("#horas_presenciales").val(datos.horas_presenciales);
                $("#horas_tutorias").val(datos.horas_tutorias);
                $("#total_horas").val(datos.total_horas);
                $("#text_message").html("");
            });
        }
    }
</script>