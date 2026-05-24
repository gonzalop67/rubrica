<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cierre de Periodos
        <small>Listado</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a class="btn btn-primary btn-md" href="<?php echo RUTA_URL; ?>/cierre_periodos/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>

            <div class="box-tools">
                <form id="form-search" action="<?php echo RUTA_URL; ?>/cierre_periodos/search" method="POST">
                    <div class="input-group input-group-md" style="width: 300px;">
                        <input type="text" name="patron" id="patron" class="form-control pull-right text-uppercase" placeholder="Buscar Aporte de Evaluación...">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                    <div id="alert-success" class="alert alert-success alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_exito']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"><?php echo isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_exito'])) unset($_SESSION['mensaje_exito']) ?>
                    <table id="t_cierres_periodos" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Aporte</th>
                                <th>Paralelo</th>
                                <th>Figura</th>
                                <th>Jornada</th>
                                <th>Fecha de Apertura</th>
                                <th>Fecha de Cierre</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_cierres_periodos">
                            <!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- Edit CierresPeriodos Modal -->
    <div class="modal fade" id="editCierresPeriodos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel3">Editar Cierres de Periodos</h4>
                </div>
                <div class="modal-body fuente10">
                    <form>
                        <input type="hidden" name="id_aporte_paralelo_cierre" id="id_aporte_paralelo_cierre">
                        <div class="form-group row">
                            <label for="edit_cbo_paralelos" class="col-sm-2 col-form-label">Paralelo:</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="font:9pt helvetica;" id="edit_cbo_paralelos" name="edit_cbo_paralelos" disabled>
                                    <option value="">Seleccione...</option>
                                    <!-- Aqui se cargan los paralelos dinamicamente -->
                                </select>
                                <span class="help-desk error" id="mensaje4"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_cbo_aportes" class="col-sm-2 col-form-label">Parcial:</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="edit_cbo_aportes" name="edit_cbo_aportes" disabled>
                                    <option value="">Seleccione...</option>
                                    <!-- Aqui se cargan los aportes de evaluacion dinamicamente -->
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_ap_fecha_apertura" class="col-sm-2 col-form-label">Fecha de Apertura:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="edit_ap_fecha_apertura" name="edit_ap_fecha_apertura" value="">
                                <span style="color: #e73d4a; font-weight:600" id="mensaje5"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_ap_fecha_cierre" class="col-sm-2 col-form-label">Fecha de Cierre:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="edit_ap_fecha_cierre" name="edit_ap_fecha_cierre" value="">
                                <span style="color: #e73d4a; font-weight:600" id="mensaje6"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateCierrePeriodo()"><span class="glyphicon glyphicon-pencil"></span> Actualizar</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        listarCierresPeriodos();
        cargar_paralelos();
        cargar_aportes_evaluacion();

        $(document).on("blur", "#fecha_apertura", function() {
            var id = $(this).data("id_apertura");
            var fecha_apertura = $(this).text();

            actualizar_fecha_apertura(id, fecha_apertura);
        });

        $(document).on("blur", "#fecha_cierre", function() {
            var id = $(this).data("id_cierre");
            var fecha_cierre = $(this).text();

            actualizar_fecha_cierre(id, fecha_cierre);
        });

        $('#form-search').submit(function(e) {
            e.preventDefault();
            let url = $(this).attr('action');
            let method = $(this).attr('method');

            var request = $.ajax({
                url: url,
                method: method,
                data: {
                    patron: $('#patron').val()
                },
                dataType: "json"
            });

            request.done(function(data) {
                let html = '';
                if (data.length > 0) {
                    for (let i = 0; i < data.length; i++) {
                        let estado = data[i].ap_estado == 'C' ? 'CERRADO' : 'ABIERTO';
                        html += '<tr>\n' +
                            '<td>' + data[i].id_aporte_paralelo_cierre + '</td>\n' +
                            '<td>' + data[i].ap_nombre + '</td>\n' +
                            '<td>' + data[i].cu_nombre + ' ' + data[i].pa_nombre + '</td>\n' +
                            '<td>' + data[i].es_figura + '</td>\n' +
                            '<td>' + data[i].jo_nombre + '</td>\n' +
                            '<td id="fecha_apertura" contenteditable data-id_apertura=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_apertura + '</td>\n' +
                            '<td id="fecha_cierre" contenteditable data_id_cierre=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_cierre + '</td>\n' +
                            '<td>' + estado + '</td>\n' +
                            '<td>\n' +
                            '<div class="btn-group">\n' +
                            '<a href="javascript:;" class="btn btn-warning item-edit" data="' + data[i].id_aporte_paralelo_cierre +
                            '" title="Editar"><span class="fa fa-pencil"></span></a>\n' +
                            '</div>\n' +
                            '</td>\n' +
                            '</tr>\n';
                    }
                    $("#tbody_cierres_periodos").html(html);
                } else {
                    html += '<tr class="text-center">\n' +
                        '<td colspan="9">No se han encontrado coincidencias...</td>\n' +
                        '</tr>\n';
                    $("#tbody_cierres_periodos").html(html);
                }
            });

            request.fail(function(jqXHR, textStatus) {
                console.log("Requerimiento fallido: " + jqXHR.responseText);
            });
        });

        $('table tbody').on('click', '.item-edit', function() {
            let id_aporte_paralelo_cierre = $(this).attr('data');

            $.ajax({
                url: "<?php echo RUTA_URL; ?>/cierre_periodos/edit",
                method: "post",
                data: {
                    id_aporte_paralelo_cierre: id_aporte_paralelo_cierre
                },
                dataType: "json",
                success: function(data) {
                    $("#id_aporte_paralelo_cierre").val(id_aporte_paralelo_cierre);
                    setearIndice("edit_cbo_paralelos", data.id_paralelo);
                    setearIndice("edit_cbo_aportes", data.id_aporte_evaluacion);
                    $("#edit_ap_fecha_apertura").val(data.ap_fecha_apertura);
                    $("#edit_ap_fecha_cierre").val(data.ap_fecha_cierre);
                    $("#mensaje5").html("");
                    $("#mensaje6").html("");
                    $('#editCierresPeriodos').modal('show');
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        $("#edit_ap_fecha_apertura").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#edit_ap_fecha_cierre").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        toastr.options = {
            "positionClass": "toast-bottom-right",
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados
            "timeOut": "3000"
        };
    });

    $(document).on('keyup', '#patron', function() {
        var valor = $(this).val();
        buscarCierresPeriodos(valor);
    });

    function actualizar_fecha_apertura(id, fecha) {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/update_fecha_apertura",
            method: "POST",
            dataType: "json",
            data: {
                id_aporte_paralelo_cierre: id,
                ap_fecha_apertura: fecha
            },
            success: function(response) {
                $('#editCierresPeriodos').modal('hide');
                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                patron = $('#patron').val().trim();
                if (patron != '') {
                    buscarCierresPeriodos(patron);
                } else {
                    listarCierresPeriodos();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    function actualizar_fecha_cierre(id, fecha) {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/update_fecha_cierre",
            method: "POST",
            dataType: "json",
            data: {
                id_aporte_paralelo_cierre: id,
                ap_fecha_cierre: fecha
            },
            success: function(response) {
                $('#editCierresPeriodos').modal('hide');
                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                patron = $('#patron').val().trim();
                if (patron != '') {
                    buscarCierresPeriodos(patron);
                } else {
                    listarCierresPeriodos();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    function listarCierresPeriodos() {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/listar",
            method: "get",
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    let estado = data[i].ap_estado == 'C' ? 'CERRADO' : 'ABIERTO';
                    html += '<tr>' +
                        '<td>' + data[i].id_aporte_paralelo_cierre + '</td>' +
                        '<td>' + data[i].ap_nombre + '</td>' +
                        '<td>' + data[i].cu_nombre + ' ' + data[i].pa_nombre + '</td>' +
                        '<td>' + data[i].es_figura + '</td>' +
                        '<td>' + data[i].jo_nombre + '</td>' +
                        '<td id="fecha_apertura" contenteditable data-id_apertura=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_apertura + '</td>' +
                        '<td id="fecha_cierre" contenteditable data-id_cierre=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_cierre + '</td>' +
                        '<td>' + estado + '</td>' +
                        '<td>' +
                        '<div class="btn-group">' +
                        '<a href="javascript:;" class="btn btn-warning item-edit" data="' + data[i].id_aporte_paralelo_cierre +
                        '" title="Editar"><span class="fa fa-pencil"></span></a>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                }
                $("#tbody_cierres_periodos").html(html);
            } else {
                html += '<tr><td colspan="9">No se han ingresado Cierres de Periodos todavía...</td></tr>';
                $("#tbody_cierres_periodos").html(html);
            }
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }

    function cargar_paralelos() {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/obtenerParalelos",
            method: "get",
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += "<option value='" + data[i].id_paralelo + "'>" +
                        "[" + data[i].es_figura + "] " + data[i].cu_nombre + " " + data[i].pa_nombre + " (" + data[i].jo_nombre + ")</option>\n";
                }
            }
            $("#edit_cbo_paralelos").append(html);
        });

        request.fail(function(jqXHR, textStatus) {
            console.log("Requerimiento fallido: " + jqXHR.responseText);
        });
    }

    function cargar_aportes_evaluacion() {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/obtenerAportesEvaluacion",
            method: "get",
            dataType: "json"
        });

        request.done(function(data) {
            var html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    html += "<option value='" + data[i].id_aporte_evaluacion + "'>[" + data[i].pe_nombre + "] " + data[i].ap_nombre + "</option>\n";
                }
            }
            $("#edit_cbo_aportes").append(html);
        });

        request.fail(function(jqXHR, textStatus) {
            console.log("Requerimiento fallido: " + jqXHR.responseText);
        })
    }

    function buscarCierresPeriodos(valor) {
        var request = $.ajax({
            url: "<?php echo RUTA_URL; ?>/cierre_periodos/search",
            method: "POST",
            data: {
                patron: valor
            },
            dataType: "json"
        });
        request.done(function(data) {
            console.log(data);
            let html = '';
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    let estado = data[i].ap_estado == 'C' ? 'CERRADO' : 'ABIERTO';
                    html += '<tr>\n' +
                        '<td>' + data[i].id_aporte_paralelo_cierre + '</td>\n' +
                        '<td>' + data[i].ap_nombre + '</td>\n' +
                        '<td>' + data[i].cu_nombre + ' ' + data[i].pa_nombre + '</td>\n' +
                        '<td>' + data[i].es_figura + '</td>\n' +
                        '<td>' + data[i].jo_nombre + '</td>\n' +
                        '<td id="fecha_apertura" contenteditable data-id_apertura=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_apertura + '</td>' +
                        '<td id="fecha_cierre" contenteditable data-id_cierre=' + data[i].id_aporte_paralelo_cierre + '>' + data[i].ap_fecha_cierre + '</td>' +
                        '<td>' + estado + '</td>\n' +
                        '<td>\n' +
                        '<div class="btn-group">\n' +
                        '<a href="javascript:;" class="btn btn-warning item-edit" data="' + data[i].id_aporte_paralelo_cierre +
                        '" title="Editar"><span class="fa fa-pencil"></span></a>\n' +
                        '</div>\n' +
                        '</td>\n' +
                        '</tr>\n';
                }
                $("#tbody_cierres_periodos").html(html);
            } else {
                html += '<tr class="text-center">\n' +
                    '<td colspan="9">No se han encontrado coincidencias...</td>\n' +
                    '</tr>\n';
                $("#tbody_cierres_periodos").html(html);
            }
        });
        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }

    function updateCierrePeriodo() {
        var id_aporte_paralelo_cierre = $("#id_aporte_paralelo_cierre").val();
        var ap_fecha_apertura = $("#edit_ap_fecha_apertura").val();
        var ap_fecha_cierre = $("#edit_ap_fecha_cierre").val();

        // contador de errores
        var cont_errores = 0;

        if (ap_fecha_apertura.trim() == "") {
            $("#mensaje5").html("Debes ingresar la fecha de apertura en el formato aaaa-mm-dd");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (ap_fecha_cierre.trim() == "") {
            $("#mensaje6").html("Debes ingresar la fecha de cierre en el formato aaaa-mm-dd");
            $("#mensaje6").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/cierre_periodos/update",
                method: "POST",
                dataType: "json",
                data: {
                    id_aporte_paralelo_cierre: id_aporte_paralelo_cierre,
                    ap_fecha_apertura: ap_fecha_apertura,
                    ap_fecha_cierre: ap_fecha_cierre
                },
                success: function(response) {
                    $('#editCierresPeriodos').modal('hide');
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    patron = $('#patron').val().trim();
                    if (patron != '') {
                        buscarCierresPeriodos(patron);
                    } else {
                        listarCierresPeriodos();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }
</script>