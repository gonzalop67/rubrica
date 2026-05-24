<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Malla Curricular</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-asociar" action="" class="form-horizontal">
                <input type="hidden" name="id_malla_curricular" id="id_malla_curricular">
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
                <div class="form-group">
                    <label class="col-sm-2 control-label text-right">Presenciales:</label>
                    <div class="col-sm-2">
                        <input type="number" min="0" class="form-control fuente9" id="horas_presenciales" value="0" onfocus="sel_texto(this)">
                        <span style="color: #e73d4a" id="mensaje3"></span>
                    </div>
                    <label class="col-sm-2 control-label text-right">Autónomas:</label>
                    <div class="col-sm-2">
                        <input type="number" min="0" class="form-control fuente9" id="horas_autonomas" value="0" onfocus="sel_texto(this)">
                        <span style="color: #e73d4a" id="mensaje4"></span>
                    </div>
                    <label class="col-sm-2 control-label text-right">Tutorías:</label>
                    <div class="col-sm-2">
                        <input type="number" min="0" class="form-control fuente9" id="horas_tutorias" value="0" onfocus="sel_texto(this)">
                        <span style="color: #e73d4a" id="mensaje5"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 4px;" id="botones_insercion">
                    <div class="col-sm-12">
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
                        <button id="btn-update" type="button" class="btn btn-block btn-primary" onclick="actualizarItemMalla()">
                            Actualizar
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
                        <th>Asignatura</th>
                        <th>Curso</th>
                        <th>Presencial</th>
                        <th>Autónomo</th>
                        <th>Tutoría</th>
                        <th></th>
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

        $("#lista_items").html("<tr><td colspan='7' align='center'>Debe seleccionar un curso...</td></tr>");

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

        $("#id_curso").change(function() {
            var id_curso = $(this).val();
            if (id_curso == "")
                $("#lista_items").html("<tr><td colspan='7' align='center'>Debe seleccionar un curso...</td></tr>");
            else {
                showAsignaturasAsociadas(id_curso);
                listarMalla();
            }
        });

        $("#form-asociar").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Asignatura Curso
            var id_curso = $("#id_curso").val();
            var id_asignatura = $("#id_asignatura").val();
            var presenciales = $("#horas_presenciales").val().trim();
            var autonomas = $("#horas_autonomas").val().trim();
            var tutorias = $("#horas_tutorias").val().trim();

            if (presenciales === "") {
                $("#mensaje3").html("Debe ingresar el número de horas presenciales.");
                $("#mensaje3").fadeIn();
            } else if (parseInt(presenciales) < 0) {
                $("#mensaje3").html("Debe ingresar un valor entero mayor que cero! para el número de horas presenciales.");
                $("#mensaje3").fadeIn();
            } else {
                $("#mensaje3").fadeOut();
            }

            if (autonomas === "") {
                $("#mensaje4").html("Debe ingresar el número de horas autónomas.");
                $("#mensaje4").fadeIn();
            } else if (parseInt(autonomas) < 0) {
                $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas autónomas.");
                $("#mensaje4").fadeIn();
            } else {
                $("#mensaje4").fadeOut();
            }

            if (tutorias == "") {
                $("#mensaje5").html("Debe ingresar el número de horas de tutorías.");
                $("#mensaje5").fadeIn();
                cont_errores++;
            } else if (parseInt(tutorias) < 0) {
                $("#mensaje5").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas de tutorías.");
                $("#mensaje5").fadeIn();
                cont_errores++;
            } else {
                $("#mensaje5").fadeOut();
            }

            if (id_curso !== "" && id_asignatura !== "" && presenciales !== "" && autonomas !== "" && tutorias !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/mallas_curriculares/insert",
                    method: "post",
                    data: {
                        id_curso: id_curso,
                        id_asignatura: id_asignatura,
                        ma_horas_presenciales: presenciales,
                        ma_horas_autonomas: autonomas,
                        ma_horas_tutorias: tutorias
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                        listarMalla();
                        $("#text_message").html("");
                    }
                });

            }
        });

        $('table tbody').on('click', '.item-edit', function() {
            var id_malla_curricular = $(this).attr('data');
            $("#id_malla_curricular").val(id_malla_curricular);
            $("#id_curso").attr("disabled", true);
            $("#id_asignatura").attr("disabled", true);
            $("#botones_insercion").hide();
            $("#botones_edicion").show();
            // Primero obtengo los datos del item elegido
            $.ajax({
                url: "<?= RUTA_URL ?>/mallas_curriculares/getById",
                type: "POST",
                dataType: "json",
                data: {
                    id_malla_curricular: id_malla_curricular
                },
                success: function(malla) {
                    console.log(malla.ma_horas_presenciales);
                    $("#horas_presenciales").val(malla.ma_horas_presenciales);
                    $("#horas_autonomas").val(malla.ma_horas_autonomas);
                    $("#horas_tutorias").val(malla.ma_horas_tutorias);
                    // Procedimiento para "setear" el índice de cboAsignaturas
                    var id_asignatura = malla.id_asignatura;
                    var sel = document.getElementById("id_asignatura");
                    for (var i = 0; i < sel.length; i++) {
                        if (sel[i].value == id_asignatura) {
                            document.getElementById("id_asignatura").selectedIndex = i;
                        }
                    }
                    $("#text_message").html("");
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
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
                                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                                listarMalla();
                            }
                        });
                    }
                });
        });
    });

    function cancelarEdicion() {
        $("#botones_edicion").hide();
        $("#botones_insercion").show();
        $("#id_curso").attr("disabled", false);
        $("#id_asignatura").attr("disabled", false);
    }

    function showAsignaturasAsociadas(id_curso) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        var request = $.ajax({
            url: "<?= RUTA_URL ?>/asignaturas_cursos/getByCursoId",
            method: "post",
            data: {
                id_curso: id_curso
            },
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            document.getElementById("id_asignatura").length = 0;
            $("#id_asignatura").append("<option value='0'>Seleccione...</option>");
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].id_asignatura + '">' + data[i].as_nombre + '</option>';
                }
                $("#id_asignatura").append(html);
            } else {
                $("#lista_items").html("<tr><td colspan='7' align='center'>No se han asociado asignaturas a este curso...</td></tr>");
                $("#total_horas").val(0);
            }
        });
    }

    function listarMalla() {
        var id_curso = $("#id_curso").val();
        if (id_curso == 0) {
            $("#lista_items").html("<tr><td colspan='7' align='center'>Debes seleccionar un curso...</td></tr>");
        } else {
            var request = $.ajax({
                url: "<?php echo RUTA_URL; ?>/mallas_curriculares/getByCursoId",
                method: "post",
                data: {
                    id_curso: id_curso
                },
                dataType: "json"
            });

            request.done(function(data) {
                console.log(data);
                var datos = JSON.parse(data);
                $("#lista_items").html(datos.cadena);
                $("#total_horas").val(datos.total_horas);
                $("#text_message").html("");
            });
        }
    }

    function actualizarItemMalla() {
        // Recolección de datos
        var cont_errores = 0;
        var id_malla = $("#id_malla_curricular").val();
        var presenciales = $("#horas_presenciales").val().trim();
        var autonomas = $("#horas_autonomas").val().trim();
        var tutorias = $("#horas_tutorias").val().trim();

        // Validación de ingreso de datos
        if (presenciales === "") {
            $("#mensaje3").html("Debe ingresar el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (parseInt(presenciales) < 0) {
            $("#mensaje3").html("Debe ingresar un valor entero mayor que cero! para el número de horas presenciales.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (autonomas === "") {
            $("#mensaje4").html("Debe ingresar el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (parseInt(autonomas) < 0) {
            $("#mensaje4").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas autónomas.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (tutorias === "") {
            $("#mensaje5").html("Debe ingresar el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (parseInt(tutorias) < 0) {
            $("#mensaje5").html("Debe ingresar un valor entero mayor o igual que cero! para el número de horas de tutorías.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (cont_errores == 0) {
            // Se procede a la inserción del item de la malla
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/mallas_curriculares/update",
                type: "POST",
                dataType: "json",
                data: {
                    id_malla_curricular: id_malla,
                    ma_horas_presenciales: presenciales,
                    ma_horas_autonomas: autonomas,
                    ma_horas_tutorias: tutorias
                },
                success: function(response) {
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    listarMalla();
                    cancelarEdicion();
                    $("#text_message").html("");
                }
            });
        }
    }
</script>