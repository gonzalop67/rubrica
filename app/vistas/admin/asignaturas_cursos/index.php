<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Asignaturas con Cursos</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-asociar" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_curso" class="col-sm-2 control-label text-right">Curso:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_curso" id="id_curso" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['cursos'] as $v) : ?>
                                <option value="<?= $v->id_curso; ?>"><?= "[" . $v->es_figura . "] - " . $v->cu_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_asignatura" class="col-sm-2 control-label text-right">Asignatura:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_asignatura" id="id_asignatura" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['asignaturas'] as $v) : ?>
                                <option value="<?= $v->id_asignatura; ?>"><?= "[" . $v->ar_nombre . "] - " . $v->as_nombre; ?></option>
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
                        <th>Curso</th>
                        <th>Asignatura</th>
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
                    <label class="control-label" style="position:relative; top:7px;">Total Asignaturas:</label>
                </div>
                <div class="col-sm-2" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente10 text-right" id="total_asignaturas" value="0" disabled>
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
        $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un curso...</td></tr>");
        $('#id_curso').select2();
        $("#id_curso").change(function() {
            var id_curso = $(this).val();
            if (id_curso == "")
                $("#lista_items").html("<tr><td colspan='4' align='center'>Debe seleccionar un curso...</td></tr>");
            else
                showAsignaturasAsociadas(id_curso);
        });
        $('#id_asignatura').select2();
        $("#form-asociar").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Asignatura Curso
            let id_curso = document.getElementById("id_curso").value;
            let id_asignatura = document.getElementById("id_asignatura").value;

            if (id_curso !== "" && id_asignatura !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/asignaturas_cursos/insert",
                    method: "post",
                    data: {
                        id_curso: id_curso,
                        id_asignatura: id_asignatura
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                        showAsignaturasAsociadas(id_curso);
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            } // fin if (id_curso !== "" && id_asignatura !== "")
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
        $('#lista_items').on('click', '.item-delete', function(e) {
            e.preventDefault();
            let id = $(this).attr('data');
            let id_curso = $("#id_curso").val();
            $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/asignaturas_cursos/delete",
                method: "post",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    $("#text_message").html("");
                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                    showAsignaturasAsociadas(id_curso);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });
        toastr.options = {
            "positionClass": "toast-bottom-right",
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados
            "timeOut": "3000"
        };
    });

    function showAsignaturasAsociadas(id_curso) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });
        
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/asignaturas_cursos/getByCursoId",
            method: "post",
            data: {
                id_curso: id_curso
            },
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<tr data-index="' + data[i].id_asignatura_curso + '" data-orden="' + data[i].ac_orden + '">' +
                        '<td>' + data[i].id_asignatura_curso + '</td>' +
                        '<td>' + data[i].es_figura + " - " + data[i].cu_nombre + '</td>' +
                        '<td>' + data[i].as_nombre + '</td>' +
                        '<td>' +
                        '<div class="btn-group">' +
                        '<a href="javascript:;" class="btn btn-danger btn-sm item-delete" data="' + data[i].id_asignatura_curso +
                        '" title="Eliminar"><span class="fa fa-remove"></span></a>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                }
                $("#lista_items").html(html);
                $("#total_asignaturas").val(data.length);
            } else {
                $("#lista_items").html("<tr><td colspan='4' align='center'>No se han asociado asignaturas a este curso...</td></tr>");
                $("#total_asignaturas").val(0);
            }
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "<?php echo RUTA_URL; ?>/asignaturas_cursos/saveNewPositions",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                console.log(response);
                showAsignaturasAsociadas($('#id_curso').val())
            }
        });
    }
</script>