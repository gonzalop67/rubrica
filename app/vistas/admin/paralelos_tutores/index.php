<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Paralelos con Tutores</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-paralelo-tutor" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelo:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_usuario" class="col-sm-2 control-label text-right">Tutor:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_usuario" id="id_usuario" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['tutores'] as $v) : ?>
                                <option value="<?= $v->id_usuario; ?>"><?= $v->us_titulo . " " . $v->us_apellidos . " " . $v->us_nombres; ?></option>
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
                        <th>Nro.</th>
                        <th>Id</th>
                        <th>Paralelo</th>
                        <th>Tutor</th>
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
                    <label class="control-label" style="position:relative; top:7px;">Total Paralelos Asociados:</label>
                </div>
                <div class="col-sm-2" style="margin-top: 2px;">
                    <input type="text" class="form-control fuente10 text-right" id="total_tutores" value="0" disabled>
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
        Biblioteca.validacionGeneral('form-paralelo-tutor');
        cargar_paralelos_tutores();
        $('#id_usuario').select2();
        $('#id_paralelo').select2();

        $("#form-paralelo-tutor").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Paralelo con Tutor
            let id_paralelo = document.getElementById("id_paralelo").value;
            let id_usuario = document.getElementById("id_usuario").value;

            if (id_paralelo !== "" && id_usuario !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/paralelos_tutores/insert",
                    method: "post",
                    data: {
                        id_paralelo: id_paralelo,
                        id_usuario: id_usuario
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
                        cargar_paralelos_tutores();
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            } // fin if (id_curso !== "" && id_asignatura !== "")
        });

        $('#lista_items').on('click', '.item-delete', function(e) {
            e.preventDefault();
            let id = $(this).attr('data');
            $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/paralelos_tutores/delete",
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
                    cargar_paralelos_tutores();
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });
    });

    function cargar_paralelos_tutores() {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/paralelos_tutores/listar",
            method: "get",
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + data[i].id_paralelo_tutor + '</td>' +
                        '<td>' + data[i].cu_nombre + ' ' + data[i].pa_nombre + ' - [' + data[i].es_figura + ']</td>' +
                        '<td>' + data[i].us_titulo + ' ' + data[i].us_fullname + '</td>' +
                        '<td>' +
                        '<div class="btn-group">' +
                        '<a href="javascript:;" class="btn btn-danger btn-sm item-delete" data="' + data[i].id_paralelo_tutor +
                        '" title="Eliminar"><span class="fa fa-remove"></span></a>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                }
                $("#lista_items").html(html);
                $("#total_tutores").val(data.length);
            } else {
                $("#lista_items").html("<tr><td colspan='5' align='center'>No se han asociado paralelos con tutores...</td></tr>");
                $("#total_tutores").val(0);
            }
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }
</script>