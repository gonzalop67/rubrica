<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Matriculación
        <small>Paralelos</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <a id="new_student" class="btn btn-primary btn-sm hide" data-toggle="modal" data-target="#newStudentModal"><i class="fa fa-plus-circle"></i> Nuevo Estudiante</a>
            <a id="inactive_students" class="btn btn-danger btn-sm hide" data-toggle="modal" data-target="#deletedStudentModal"><i class="fa fa-gear"></i> Estudiantes Inactivos</a>

            <div id="search_student" class="box-tools fuente9 hide">
                <form id="form-search" action="<?= RUTA_URL ?>/matriculacion/search" method="POST">
                    <div class="input-group input-group-sm" style="width: 400px;">
                        <input type="text" name="input_search" id="input_search" class="form-control pull-right text-uppercase fuente9" placeholder="Ingrese Nombre para Histórico del Estudiante...">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div style="margin-top: 10px;">
                <form id="form_id" action="" class="form-horizontal">
                    <div class="form-group">
                        <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                        <div class="col-sm-11">
                            <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                <option value="">Seleccione...</option>
                                <?php foreach ($datos['paralelos'] as $v) : ?>
                                    <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="contar_estudiantes_por_genero" class="fuente9">
                        <!-- Aqui va el conteo de estudiantes por genero -->
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table id="t_estudiantes" class="table fuente8">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Mat.</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>DNI</th>
                                <th>Fec.Nacim.</th>
                                <th>Edad</th>
                                <th>Género</th>
                                <th>Retirado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_estudiantes">
                            <!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->
    <!-- New Student Modal -->
    <div class="modal fade" id="newStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel1">Estudiante Nuevo</h4>
                </div>
                <form id="form_insert" action="" method="post" autocomplete="off">
                    <div class="modal-body fuente9">
                        <div class="form-group row">
                            <label for="new_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="new_id_tipo_documento" name="new_id_tipo_documento" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['tipos_documento'] as $v) { ?>
                                        <option value="<?php echo $v->id_tipo_documento; ?>"><?php echo $v->td_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje2" style="color: #e73d4a"></span>
                            </div>
                            <label for="new_dni" class="col-sm-1 col-form-label">DNI:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="new_dni" name="new_dni" value="">
                                <span id="mensaje3" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="new_apellidos" name="new_apellidos" value="">
                                <span id="mensaje4" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="new_nombres" name="new_nombres" value="">
                                <span id="mensaje5" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="new_fec_nac" name="new_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
                                <span id="mensaje6" style="color: #e73d4a"></span>
                            </div>

                            <label for="new_edad" class="col-sm-1 col-form-label">Edad:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="new_edad" name="new_edad" value="" disabled>
                                <span id="mensaje6" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_direccion" class="col-sm-2 col-form-label">Dirección:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="new_direccion" name="new_direccion" value="">
                                <span id="mensaje7" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_sector" class="col-sm-2 col-form-label">Sector:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mayusculas" id="new_sector" name="new_sector" value="">
                                <span id="mensaje8" style="color: #e73d4a"></span>
                            </div>
                            <label for="new_telefono" class="col-sm-1 col-form-label">Celular:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="new_telefono" name="new_telefono" value="">
                                <span id="mensaje9" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_email" class="col-sm-2 col-form-label">E-mail:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="new_email" name="new_email" value="">
                                <span id="mensaje10" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="new_genero" class="col-sm-2 col-form-label">Género:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="new_genero" name="new_genero">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['def_generos'] as $v) { ?>
                                        <option value="<?php echo $v->id_def_genero; ?>"><?php echo $v->dg_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje11" style="color: #e73d4a"></span>
                            </div>
                            <label for="new_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="new_nacionalidad" name="new_nacionalidad">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['def_nacionalidades'] as $v) { ?>
                                        <option value="<?php echo $v->id_def_nacionalidad; ?>"><?php echo $v->dn_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje12" style="color: #e73d4a"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="insertarEstudiante()"><span class="glyphicon glyphicon-save"></span> Insertar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel2">Editar Estudiante</h4>
                </div>
                <form id="form_edit" action="" method="post" autocomplete="off">
                    <input type="hidden" name="id_estudiante" id="id_estudiante">
                    <div class="modal-body fuente9">
                        <div class="form-group row">
                            <label for="edit_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="edit_id_tipo_documento" name="edit_id_tipo_documento" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['tipos_documento'] as $v) { ?>
                                        <option value="<?php echo $v->id_tipo_documento; ?>"><?php echo $v->td_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje2" style="color: #e73d4a"></span>
                            </div>
                            <label for="edit_dni" class="col-sm-1 col-form-label">DNI:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="edit_dni" name="edit_dni" value="">
                                <span id="mensaje3" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="edit_apellidos" name="edit_apellidos" value="">
                                <span id="mensaje4" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="edit_nombres" name="edit_nombres" value="">
                                <span id="mensaje5" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="edit_fec_nac" name="edit_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
                                <span id="mensaje6" style="color: #e73d4a"></span>
                            </div>

                            <label for="edit_edad" class="col-sm-1 col-form-label">Edad:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="edit_edad" name="edit_edad" value="" disabled>
                                <span id="mensaje6" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_direccion" class="col-sm-2 col-form-label">Dirección:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mayusculas" id="edit_direccion" name="edit_direccion" value="">
                                <span id="mensaje7" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_sector" class="col-sm-2 col-form-label">Sector:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mayusculas" id="edit_sector" name="edit_sector" value="">
                                <span id="mensaje8" style="color: #e73d4a"></span>
                            </div>
                            <label for="edit_telefono" class="col-sm-1 col-form-label">Celular:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="edit_telefono" name="edit_telefono" value="">
                                <span id="mensaje9" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_email" class="col-sm-2 col-form-label">E-mail:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="edit_email" name="edit_email" value="">
                                <span id="mensaje10" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_genero" class="col-sm-2 col-form-label">Género:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="edit_genero" name="edit_genero">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['def_generos'] as $v) { ?>
                                        <option value="<?php echo $v->id_def_genero; ?>"><?php echo $v->dg_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje11" style="color: #e73d4a"></span>
                            </div>
                            <label for="edit_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                            <div class="col-sm-4">
                                <select class="form-control fuente9" id="edit_nacionalidad" name="edit_nacionalidad">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['def_nacionalidades'] as $v) { ?>
                                        <option value="<?php echo $v->id_def_nacionalidad; ?>"><?php echo $v->dn_nombre; ?></option>
                                    <?php } ?>
                                </select>
                                <span id="mensaje12" style="color: #e73d4a"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="edit_id_paralelo" class="col-sm-2 col-form-label">Nuevo Paralelo:</label>
                            <div class="col-sm-10">
                                <select class="form-control fuente9" id="edit_id_paralelo" name="edit_id_paralelo">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($datos['paralelos'] as $v) : ?>
                                        <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span id="mensaje13" style="color: #e73d4a"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="actualizarEstudiante()"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Estudiantes Des-matriculados Modal -->
    <div class="modal fade" id="deletedStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title text-center" id="myModalLabel3">Estudiante Des-matriculados</h4>
                </div>
                <div class="col-md-12 table-responsive">
                    <table id="t_deleted" class="table fuente8">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Mat.</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_deleted">
                            <!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        // Poner todos los mensajes en vacío
        $("#mensaje1").html("");
        $("#mensaje2").html("");
        $("#mensaje3").html("");

        // Obtener el rango de años para el datepicker
        var fecha = new Date();
        var year = fecha.getFullYear();
        var minYear = year - 70;
        var maxYear = year - 5;

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

        $("#id_paralelo").on('change', function() {
            let id_paralelo = $(this).val();

            if (id_paralelo == "") {
                $("#mensaje1").html("Debe seleccionar un paralelo...");
                $("#mensaje1").fadeIn("slow");
                $("#new_student").addClass('hide');
                $("#history_student").addClass('hide');
                $("#search_student").addClass('hide');
                $("#inactive_students").addClass('hide');
                $("#t_estudiantes tbody").html("");
                $("#t_deleted tbody").html("");
                $("#contar_estudiantes_por_genero").html("");
                $("#id_paralelo_export").val("");
            } else {
                $("#mensaje1").fadeOut();
                listarEstudiantesParalelo(id_paralelo);
                cargar_estudiantes_des_matriculados();
                contarEstudiantesParalelo(id_paralelo);
                $("#new_student").removeClass('hide');
                $("#history_student").removeClass('hide');
                $("#search_student").removeClass('hide');
                $("#inactive_students").removeClass('hide');
                $("#id_paralelo_export").val(id_paralelo);
            }
        });

        $("#new_id_tipo_documento").on('change', function() {
            let id_tipo_documento = $(this).val();

            if (id_tipo_documento == "") {
                $("#mensaje2").html("Debe seleccionar un tipo de documento...");
                $("#mensaje2").fadeIn("slow");
            } else {
                $("#mensaje2").fadeOut();
            }
        });

        $("#new_genero").on('change', function() {
            let id_def_genero = $(this).val();

            if (id_def_genero == "") {
                $("#mensaje11").html("Debe seleccionar el género...");
                $("#mensaje11").fadeIn("slow");
            } else {
                $("#mensaje11").fadeOut();
            }
        });

        $("#new_nacionalidad").on('change', function() {
            let id_def_nacionalidad = $(this).val();

            if (id_def_nacionalidad == "") {
                $("#mensaje12").html("Debe seleccionar la nacionalidad...");
                $("#mensaje12").fadeIn("slow");
            } else {
                $("#mensaje12").fadeOut();
            }
        });

        $("#new_fec_nac").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            changeMonth: true,
            changeYear: true,
            yearRange: minYear + ":" + maxYear
        });

        $("#new_fec_nac").change(function() {
            let fec_nac = $(this).val().trim();
            if (fec_nac == "") {
                $("#mensaje6").html("Debe ingresar la fecha de nacimiento...");
                $("#mensaje6").fadeIn("slow");
            } else {
                $("#mensaje6").fadeOut();
            }
        });

        const reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

        $("#new_fec_nac").keyup(function() {
            if (reg_fecnac.test($(this).val())) {
                $("#new_edad").val(calcularEdad($(this).val()));
            }
        });

        $('#t_estudiantes tbody').on('click', '.item-edit', function() {
            let id_estudiante = $(this).attr('data');
            $.ajax({
                url: "<?= RUTA_URL ?>/matriculacion/edit",
                method: "post",
                data: {
                    id_estudiante: id_estudiante
                },
                dataType: "json",
                success: function(data) {
                    $("#id_estudiante").val(id_estudiante);
                    setearIndice("edit_id_tipo_documento", data.id_tipo_documento);
                    $("#edit_dni").val(data.es_cedula);
                    $("#edit_apellidos").val(data.es_apellidos);
                    $("#edit_nombres").val(data.es_nombres);
                    $("#edit_fec_nac").val(data.es_fec_nacim);
                    $("#edit_edad").val(calcularEdad(data.es_fec_nacim));
                    $("#edit_direccion").val(data.es_direccion);
                    $("#edit_sector").val(data.es_sector);
                    $("#edit_telefono").val(data.es_telefono);
                    $("#edit_email").val(data.es_email);
                    setearIndice("edit_genero", data.id_def_genero);
                    setearIndice("edit_nacionalidad", data.id_def_nacionalidad);

                    $('#editStudentModal').modal('show');
                }
            });
        });

        $('table tbody').on('click', '.item-delete', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const id_estudiante = $(this).attr('data');
            const id_paralelo = $("#id_paralelo").val();
            swal({
                    title: "¿Está seguro de inactivar este estudiante?",
                    text: "¡Ud. podrá volver a matricularlo más tarde!",
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
                                id_estudiante: id_estudiante,
                                id_paralelo: id_paralelo
                            },
                            dataType: "json",
                            success: function(response) {
                                swal({
                                    title: response.titulo,
                                    text: response.mensaje,
                                    icon: response.tipo_mensaje,
                                    confirmButtonText: 'Aceptar'
                                });
                                listarEstudiantesParalelo(id_paralelo);
                                cargar_estudiantes_des_matriculados();
                            }
                        });
                    }
                });
        });

        $('#t_deleted tbody').on('click', '.item-deleted', function() {
            let id_estudiante_periodo_lectivo = $(this).attr('data');
            let id_paralelo = $("#id_paralelo").val();
            $.ajax({
                url: "<?= RUTA_URL ?>/matriculacion/undelete",
                method: "post",
                data: {
                    id_estudiante_periodo_lectivo: id_estudiante_periodo_lectivo
                },
                dataType: "json",
                success: function(response) {
                    $('#deletedStudentModal').modal('hide');
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        button: 'Aceptar'
                    });
                    listarEstudiantesParalelo(id_paralelo);
                    cargar_estudiantes_des_matriculados();
                },
                error: function(jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                }
            });
        });

        $("#input_search").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?= RUTA_URL ?>/matriculacion/buscar_estudiante",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        valor: request.term
                    },
                    success: function(data) {
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        // alert(xhr.responseText);
                        console.log(xhr.responseText);
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                $("#id_estudiante").val(ui.item.id);
            },
        });
    });

    function cedulaValida(cedula) {
        var total = 0;
        var longitud = cedula.length;
        var longcheck = longitud - 1;

        if (longitud != 10) {
            return false;
        }

        if (cedula !== "" && longitud === 10) {
            for (i = 0; i < longcheck; i++) {
                if (i % 2 === 0) {
                    var aux = cedula.charAt(i) * 2;
                    if (aux > 9) aux -= 9;
                    total += aux;
                } else {
                    total += parseInt(cedula.charAt(i)); // parseInt o concatenará en lugar de sumar
                }
            }

            total = total % 10 ? 10 - total % 10 : 0;

            return cedula.charAt(longitud - 1) == total;
        }
    }

    function calcularEdad(es_fec_nacim) {
        //Aqui se va a calcular la edad a partir de la fecha de nacimiento
        var hoy = new Date();
        var fec_nac = new Date(es_fec_nacim);
        var edad = hoy.getFullYear() - fec_nac.getFullYear();
        var m = hoy.getMonth() - fec_nac.getMonth();

        if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
            edad--;
        }

        return edad;
    }

    function insertarEstudiante() {
        let cont_errores = 0;
        
        let es_cedula = $("#new_dni").val().trim();
        let es_email = $("#new_email").val().trim();
        let es_sector = $("#new_sector").val().trim();
        let es_nombres = $("#new_nombres").val().trim();
        let id_paralelo = $("#id_paralelo").val().trim();
        let id_def_genero = $("#new_genero").val().trim();
        let es_telefono = $("#new_telefono").val().trim();
        let es_fec_nacim = $("#new_fec_nac").val().trim();
        let es_apellidos = $("#new_apellidos").val().trim();
        let es_direccion = $("#new_direccion").val().trim();
        let id_def_nacionalidad = $("#new_nacionalidad").val().trim();
        let id_tipo_documento = $("#new_id_tipo_documento").val().trim();

        var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
        var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
        var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

        if (id_paralelo == "") {
            swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
            $("#mensaje1").html("Debe seleccionar un paralelo...");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_tipo_documento == "") {
            $("#mensaje2").html("Debe seleccionar un tipo de documento...");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (es_cedula == "") {
            $("#mensaje3").html("Debe ingresar el DNI...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (es_cedula.length != 0 && !reg_cedula.test(es_cedula)) {
            $("#mensaje3").html("El DNI del estudiante no tiene un formato válido.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
            $("#mensaje3").html("La cédula ingresada no es válida.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (es_apellidos == "") {
            $("#mensaje4").html("Debe ingresar los apellidos...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_apellidos)) {
            $("#mensaje4").html("Los apellidos del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (es_nombres == "") {
            $("#mensaje5").html("Debe ingresar los nombres...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_nombres)) {
            $("#mensaje5").html("Los nombres del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (es_fec_nacim == "") {
            $("#mensaje6").html("Debe ingresar la fecha de nacimiento...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else if (!reg_fecnac.test(es_fec_nacim)) {
            $("#mensaje6").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
            $("#new_edad").val(calcularEdad(es_fec_nacim));
        }

        if (es_direccion == "") {
            $("#mensaje7").html("Debe ingresar la dirección de su domicilio...");
            $("#mensaje7").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje7").fadeOut();
        }

        if (es_sector == "") {
            $("#mensaje8").html("Debe ingresar el sector de su domicilio...");
            $("#mensaje8").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje8").fadeOut();
        }

        if (es_telefono == "") {
            $("#mensaje9").html("Debe ingresar el número de celular...");
            $("#mensaje9").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje9").fadeOut();
        }

        if (es_email.length != 0 && !reg_email.test(es_email)) {
            $("#mensaje10").html("Dirección de correo electrónico no válida.");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (id_def_genero == "") {
            $("#mensaje11").html("Debe seleccionar el género...");
            $("#mensaje11").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje11").fadeOut();
        }

        if (id_def_nacionalidad == "") {
            $("#mensaje12").html("Debe seleccionar la nacionalidad...");
            $("#mensaje12").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje12").fadeOut();
        }

        if (cont_errores == 0) {
            // Aquí vamos a insertar el estudiante mediante AJAX
            $.ajax({
                url: "<?= RUTA_URL ?>/matriculacion/insert",
                method: "POST",
                data: {
                    id_tipo_documento: id_tipo_documento,
                    id_def_genero: id_def_genero,
                    id_def_nacionalidad: id_def_nacionalidad,
                    es_apellidos: es_apellidos.toUpperCase(),
                    es_nombres: es_nombres.toUpperCase(),
                    es_cedula: es_cedula.toUpperCase(),
                    es_email: es_email,
                    es_sector: es_sector.toUpperCase(),
                    es_direccion: es_direccion.toUpperCase(),
                    es_telefono: es_telefono,
                    es_fec_nacim: es_fec_nacim,
                    id_paralelo: id_paralelo
                },
                dataType: "json",
                success: function(response) {
                    $('#newStudentModal').modal('hide');
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    listarEstudiantesParalelo(id_paralelo);
                    contarEstudiantesParalelo(id_paralelo);
                    $("#form_insert")[0].reset();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        }
    }

    function actualizarEstudiante() {
        let cont_errores = 0;
        let id_estudiante = $("#id_estudiante").val();
        let es_cedula = $("#edit_dni").val().trim();
        let es_email = $("#edit_email").val().trim();
        let es_sector = $("#edit_sector").val().trim();
        let es_nombres = $("#edit_nombres").val().trim();
        let id_paralelo = $("#id_paralelo").val().trim();
        let id_def_genero = $("#edit_genero").val().trim();
        let es_telefono = $("#edit_telefono").val().trim();
        let es_fec_nacim = $("#edit_fec_nac").val().trim();
        let es_apellidos = $("#edit_apellidos").val().trim();
        let es_direccion = $("#edit_direccion").val().trim();
        let edit_id_paralelo = $("#edit_id_paralelo").val().trim();
        let id_def_nacionalidad = $("#edit_nacionalidad").val().trim();
        let id_tipo_documento = $("#edit_id_tipo_documento").val().trim();

        var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
        var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
        var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

        if (id_paralelo == "") {
            swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
            $("#mensaje1").html("Debe seleccionar un paralelo...");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_tipo_documento == "") {
            $("#mensaje2").html("Debe seleccionar un tipo de documento...");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (es_cedula == "") {
            $("#mensaje3").html("Debe ingresar el DNI...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (es_cedula.length != 0 && !reg_cedula.test(es_cedula)) {
            $("#mensaje3").html("El DNI del estudiante no tiene un formato válido.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
            $("#mensaje3").html("La cédula ingresada no es válida.");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (es_apellidos == "") {
            $("#mensaje4").html("Debe ingresar los apellidos...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_apellidos)) {
            $("#mensaje4").html("Los apellidos del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (es_nombres == "") {
            $("#mensaje5").html("Debe ingresar los nombres...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (!reg_nombres.test(es_nombres)) {
            $("#mensaje5").html("Los nombres del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (es_fec_nacim == "") {
            $("#mensaje6").html("Debe ingresar la fecha de nacimiento...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else if (!reg_fecnac.test(es_fec_nacim)) {
            $("#mensaje6").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
            $("#edit_edad").val(calcularEdad(es_fec_nacim));
        }

        if (es_direccion == "") {
            $("#mensaje7").html("Debe ingresar la dirección de su domicilio...");
            $("#mensaje7").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje7").fadeOut();
        }

        if (es_sector == "") {
            $("#mensaje8").html("Debe ingresar el sector de su domicilio...");
            $("#mensaje8").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje8").fadeOut();
        }

        if (es_telefono == "") {
            $("#mensaje9").html("Debe ingresar el número de celular...");
            $("#mensaje9").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje9").fadeOut();
        }

        if (es_email.length != 0 && !reg_email.test(es_email)) {
            $("#mensaje10").html("Dirección de correo electrónico no válida.");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (id_def_genero == "") {
            $("#mensaje11").html("Debe seleccionar el género...");
            $("#mensaje11").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje11").fadeOut();
        }

        if (id_def_nacionalidad == "") {
            $("#mensaje12").html("Debe seleccionar la nacionalidad...");
            $("#mensaje12").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje12").fadeOut();
        }

        if (cont_errores == 0) {
            // Aquí vamos a actualizar el estudiante mediante AJAX
            $.ajax({
                url: "<?= RUTA_URL ?>/matriculacion/update",
                method: "POST",
                data: {
                    id_estudiante: id_estudiante,
                    id_tipo_documento: id_tipo_documento,
                    id_def_genero: id_def_genero,
                    id_def_nacionalidad: id_def_nacionalidad,
                    es_apellidos: es_apellidos.toUpperCase(),
                    es_nombres: es_nombres.toUpperCase(),
                    es_cedula: es_cedula.toUpperCase(),
                    es_email: es_email,
                    es_sector: es_sector.toUpperCase(),
                    es_direccion: es_direccion.toUpperCase(),
                    es_telefono: es_telefono,
                    es_fec_nacim: es_fec_nacim,
                    id_paralelo: edit_id_paralelo
                },
                dataType: "json",
                success: function(response) {
                    $('#editStudentModal').modal('hide');
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    listarEstudiantesParalelo(id_paralelo);
                    $("#form_edit")[0].reset();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

    function actualizar_estado_retirado(obj, id_estudiante) {
        if (obj.checked) estado_retirado = "S";
        else estado_retirado = "N";
        $.ajax({
            type: "POST",
            url: "<?= RUTA_URL ?>/matriculacion/actualizar_retirado",
            data: "id_estudiante=" + id_estudiante + "&es_retirado=" + estado_retirado,
            dataType: "json",
            success: function(response) {
                console.log(response.mensaje);
                toastr[response.tipo_mensaje](response.mensaje, response.titulo);
            }
        });
    }

    function listarEstudiantesParalelo(id_paralelo) {
        $.ajax({
            url: "<?= RUTA_URL ?>/matriculacion/listar",
            method: "POST",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "html",
            success: function(response) {
                $("#t_estudiantes tbody").html(response);
            }
        });
    }

    function cargar_estudiantes_des_matriculados() {
        // Aquí vamos a recuperar los estudiantes "des-matriculados"
        var id_paralelo = $("#id_paralelo").val();
        var request = $.ajax({
            url: "<?= RUTA_URL ?>/matriculacion/listar_inactivados",
            method: "post",
            data: {
                id_paralelo: id_paralelo
            },
            dataType: "html"
        });

        request.done(function(response) {
            $("#t_deleted tbody").html(response);
        });
    }

    function contarEstudiantesParalelo(id_paralelo) {
        $.ajax({
            type: "post",
            url: "<?= RUTA_URL ?>/matriculacion/contar_estudiantes_por_genero",
            data: "id_paralelo=" + id_paralelo,
            dataType: "html",
            success: function(response) {
                console.log(response);
                $("#contar_estudiantes_por_genero").html(response);
            }
        });
    }
</script>