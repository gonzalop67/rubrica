<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Cuestionarios con Paralelos</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-asociar" action="" class="form-horizontal">
                <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION["id_usuario"] ?>">
                <div class="form-group">
                    <label for="id_categoria" class="col-sm-2 control-label text-right">Cuestionario:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_categoria" id="id_categoria" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['categorias'] as $v) : ?>
                                <option value="<?= $v->id; ?>"><?= $v->category; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelo:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_abreviatura . $v->pa_nombre . " " . $v->es_abreviatura; ?></option>
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
                                <option value="<?= $v->id_asignatura; ?>"><?= $v->as_nombre; ?></option>
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
                        <th>Cuestionario</th>
                        <th>Paralelo</th>
                        <th>Asignatura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista_items">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('form-asociar');
        $("#lista_items").html("<tr><td colspan='5' align='center'>Debe seleccionar un cuestionario...</td></tr>");
        $('#id_categoria').select2();
        $("#id_categoria").change(function() {
            var id_categoria = $(this).val();
            if (id_categoria == "")
                $("#lista_items").html("<tr><td colspan='5' align='center'>Debe seleccionar un cuestionario...</td></tr>");
            else
                showCuestionariosAsociados(id_categoria);
        });

        $('#id_paralelo').select2();
        $('#id_asignatura').select2();

        $("#form-asociar").submit(function(e) {
            e.preventDefault();
            
            //Insertar Asociación de Cuestionario Paralelo
            let id_categoria = document.getElementById("id_categoria").value;
            let id_paralelo = document.getElementById("id_paralelo").value;
            let id_asignatura = document.getElementById("id_asignatura").value;

            if (id_categoria !== "" && id_paralelo !== "" && id_asignatura !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/cuestionarios/asociar_paralelo_insert",
                    method: "post",
                    data: {
                        id_categoria: id_categoria,
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        console.log(response);
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);

                        showCuestionariosAsociados(id_categoria);
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            }
        });

        $('#lista_items').on('click', '.item-delete', function(e) {
            e.preventDefault();
            let id = $(this).attr('data');
            //let id_curso = document.getElementById("id_curso").value;
            $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/cuestionarios/eliminarAsociacion",
                method: "post",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    $("#text_message").html("");
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    showCuestionariosAsociados($("#id_categoria").val());
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

    function showCuestionariosAsociados(id_categoria) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/getByCategoryId",
            method: "post",
            data: {
                id_categoria: $('#id_categoria').val()
            },
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<tr>' +
                        '<td>' + data[i].id + '</td>' +
                        '<td>' + data[i].category + '</td>' +
                        '<td>' + data[i].es_figura + " - " + data[i].cu_nombre + " " + data[i].pa_nombre + '</td>' +
                        '<td>' + data[i].as_nombre + '</td>' +
                        '<td>' +
                        '<div class="btn-group">' +
                        '<a href="javascript:;" class="btn btn-danger btn-sm item-delete" data="' + data[i].id +
                        '" title="Eliminar"><span class="fa fa-remove"></span></a>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                }
                $("#lista_items").html(html);
            } else {
                $("#lista_items").html("<tr><td colspan='5' align='center'>No se han asociado paralelos a este cuestionario...</td></tr>");
            }
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }
</script>