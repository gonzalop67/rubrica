<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Asignaturas
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevaAsignaturaModal"><i class="fa fa-plus-circle"></i> Nueva Asignatura</button>
                <div id="buscar_asignatura" class="box-tools" style="margin-top: 6px;">
                    <form id="form-search" action="" method="POST">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="input_search" id="input_search" class="form-control pull-right text-uppercase fuente9" placeholder="Asignatura a Buscar...">

                            <!-- <div class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div> -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="t_asignaturas" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Area</th>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="lista_asignaturas">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script src="js/funciones.js"></script>

<script>
    $(document).ready(function() {
        cargar_areas();
        cargar_tipos();
        listarAsignaturas();

        $("#form-search").submit(function(e) {
            e.preventDefault();
        });

        $("#input_search").keyup(function() {
            var search = $(this).val().trim();
            if (search != '') {
                load_data(search);
            } else {
                listarAsignaturas();
            }
        });
    });

    function listarAsignaturas() {
        $.get("asignaturas/cargar_asignaturas.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#lista_asignaturas').html(resultado);
            }
        });
    }

    function cargar_areas() {
        $.get("scripts/cargar_areas.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_cbo_areas').append(resultado);
                $('#edit_cbo_areas').append(resultado);
            }
        });
    }

    function cargar_tipos() {
        $.get("scripts/cargar_tipos_asignatura.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_cbo_tipos').append(resultado);
                $('#edit_cbo_tipos').append(resultado);
            }
        });
    }

    function insertarAsignatura() {
        var id_area = $("#new_cbo_areas").val();
        var as_nombre = $("#new_as_nombre").val();
        var as_abreviatura = $("#new_as_abreviatura").val();
        var as_tipo = $("#new_cbo_tipos").val();

        // expresiones regulares para validar el ingreso de datos
        var reg_nombre = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ,]{4,120})$/i;
        var reg_abreviatura = /^([a-zA-Z.]{3,16})$/i;

        // contador de errores
        var cont_errores = 0;

        if (id_area == 0) {
            $("#mensaje1").html("Debes seleccionar el Area");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (as_nombre.trim() == "") {
            $("#mensaje2").html("Debes ingresar el nombre de la Asignatura");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombre.test(as_nombre)) {
            $("#mensaje2").html("Debes ingresar un nombre válido para la Asignatura");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (as_abreviatura.trim() == "") {
            $("#mensaje3").html("Debes ingresar la abreviatura de la Asignatura");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else if (!reg_abreviatura.test(as_abreviatura)) {
            $("#mensaje3").html("Debes ingresar una abreviatura válida para la Asignatura");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "asignaturas/insertar_asignatura.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_area: id_area,
                    as_nombre: as_nombre,
                    as_abreviatura: as_abreviatura,
                    id_tipo_asignatura: as_tipo
                },
                success: function(r) {
                    listarAsignaturas();
                    Swal.fire({
                        title: r.titulo,
                        text: r.mensaje,
                        icon: r.estado
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $('#nuevaAsignaturaModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        //Primero obtengo el nombre del area seleccionada
        $.ajax({
            url: "asignaturas/obtener_asignatura.php",
            method: "POST",
            type: "html",
            data: {
                id_asignatura: id
            },
            success: function(resultado) {
                var asignatura = eval('(' + resultado + ')');
                $("#id_asignatura").val(id);
                $("#id_area").val(asignatura.id_area);
                $("#edit_ar_nombre").val(asignatura.ar_nombre);
                $("#edit_as_nombre").val(asignatura.as_nombre);
                $("#edit_as_abreviatura").val(asignatura.as_abreviatura);
                setearIndice("edit_cbo_tipos", asignatura.id_tipo_asignatura);
                setearIndice("edit_cbo_areas", asignatura.id_area);
                $('#editarAsignaturaModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function actualizarAsignatura() {
        var id_area = $("#edit_cbo_areas").val();
        var id_asignatura = $("#id_asignatura").val();
        var as_nombre = $("#edit_as_nombre").val();
        var as_abreviatura = $("#edit_as_abreviatura").val();
        var as_tipo = $("#edit_cbo_tipos").val();

        // expresiones regulares para validar el ingreso de datos
        var reg_nombre = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{4,84})$/i;
        var reg_abreviatura = /^([a-zA-Z.]{3,8})$/i;

        // contador de errores
        var cont_errores = 0;

        if (as_nombre.trim() == "") {
            $("#mensaje5").html("Debes ingresar el nombre de la Asignatura");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombre.test(as_nombre)) {
            $("#mensaje5").html("Debes ingresar un nombre válido para la Asignatura");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (as_abreviatura.trim() == "") {
            $("#mensaje6").html("Debes ingresar la abreviatura de la Asignatura");
            $("#mensaje6").fadeIn("slow");
            cont_errores++;
        } else if (!reg_abreviatura.test(as_abreviatura)) {
            $("#mensaje6").html("Debes ingresar una abreviatura válida para la Asignatura");
            $("#mensaje6").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "asignaturas/actualizar_asignatura.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_asignatura: id_asignatura,
                    id_area: id_area,
                    as_nombre: as_nombre,
                    as_abreviatura: as_abreviatura,
                    id_tipo_asignatura: as_tipo
                },
                success: function(r) {
                    listarAsignaturas();
                    Swal.fire({
                        title: r.titulo,
                        text: r.mensaje,
                        icon: r.estado
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarAsignaturaModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function eliminarAsignatura(id) {
        //Elimino la asignatura mediante AJAX

        Swal.fire({
            title: "¿Está seguro que quiere eliminar el registro?",
            text: "No podrá recuperar el registro que va a ser eliminado!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, elimínelo!",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "asignaturas/eliminar_asignatura.php",
                    data: {
                        id_asignatura: id
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: response.estado,
                            title: response.titulo,
                            text: response.mensaje
                        });
                        listarAsignaturas();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    function load_data(query) {
        // Aqui vamos a consultar las asignaturas que coincidan con el patron de busqueda
        var request = $.ajax({
            url: "asignaturas/buscar_asignaturas.php",
            data: {
                input_search: query
            },
            method: "post",
            dataType: "html"
        });

        request.done(function(resultado) {
            // console.log(resultado);
            $('#lista_asignaturas').html(resultado);
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }
</script>