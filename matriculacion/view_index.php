<style>
    .ocultar {
        display: none;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
                <a id="new_student" class="btn btn-primary btn-sm ocultar" data-toggle="modal" data-target="#newStudentModal"><i class="fa fa-plus-circle"></i> Nuevo Estudiante</a>
                <!-- <a id="history_student" class="btn btn-info btn-sm ocultar"><i class="fa fa-search"></i> Histórico del Estudiante</a> -->
                <a id="inactive_students" class="btn btn-danger btn-sm ocultar" data-toggle="modal" data-target="#deletedStudentModal"><i class="fa fa-gear"></i> Estudiantes Inactivos</a>

                <div id="search_student" class="box-tools fuente9 ocultar">
                    <form id="form-search" action="" method="POST">
                        <div class="input-group input-group-sm" style="width: 400px;">
                            <input type="text" name="input_search" id="input_search" class="form-control pull-right text-uppercase" placeholder="Ingrese Nombre para Histórico del Estudiante...">

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
                                    <!-- Aquí voy a poblar los paralelos mediante AJAX -->
                                </select>
                                <span id="mensaje1" style="color: #e73d4a"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
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
                                    <th>Desertor</th>
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
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script type="text/javascript">
    $(document).ready(function() {
        cargar_paralelos();

        const expresiones = {
            reg_fecnac: /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i,
            reg_cedula_ecuatoriana: /^\d{10}/,
            reg_nombres: /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i,
            reg_email: /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i
        };

        $("#id_paralelo").on('change', function() {
            let id_paralelo = $(this).val();

            if (id_paralelo == "") {
                $("#mensaje1").html("Debe seleccionar un paralelo...");
                $("#mensaje1").fadeIn("slow");
                $("#new_student").hide();
                $("#history_student").hide();
                $("#search_student").hide();
                $("#inactive_students").hide();
                $("#t_estudiantes tbody").html("");
                //$("#t_deleted tbody").html("");
            } else {
                $("#mensaje1").fadeOut();
                listarEstudiantesParalelo(id_paralelo);
                //cargar_estudiantes_des_matriculados();
                $("#new_student").show();
                $("#history_student").show();
                $("#search_student").show();
                $("#inactive_students").show();
            }
        });

        $("#input_search").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "matriculacion/buscar_estudiantes.php",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        valor: request.term
                    },
                    success: function(data) {
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                //$("#id_usuario").val(ui.item.id);
            },
        });
    });

    function cargar_paralelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#id_paralelo').append(resultado);
            }
        });
    }

    function listarEstudiantesParalelo(id_paralelo) {
        $.post("matriculacion/listar_estudiantes.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#t_estudiantes tbody").html(resultado);
                }
            }
        );
    }
</script>