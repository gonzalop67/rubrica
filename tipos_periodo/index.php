<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tipos de Periodo de Evaluación
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoTipoPeriodoModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th colspan=2>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tipos_periodo">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        cargarTiposPeriodosEvaluacion();

        $("#nombre").keyup(function() {
            generarSlug($(this).val(), "slug");
        });

        $("#nombreu").keyup(function() {
            generarSlug($(this).val(), "slugu");
        });

        $("#nombre").blur(function() {
            generarSlug($(this).val(), "slug");
        });

        $("#nombreu").blur(function() {
            generarSlug($(this).val(), "slugu");
        });
    });

    // function generarSlug se movió a funciones.js

    function cargarTiposPeriodosEvaluacion() {
        // Obtengo todas los tipos de periodo de evaluación ingresados en la base de datos
        $.ajax({
            url: "tipos_periodo/cargar_tipos_de_periodo.php",
            method: "GET",
            type: "html",
            success: function(response) {
                $("#tipos_periodo").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarTipoPeriodo() {
        let cont_errores = 0;
        let nombre = $("#nombre").val();
        let slug = $("#slug").val();

        if (nombre == "") {
            $("#error-nombre").html("Debe ingresar el nombre del tipo de periodo de evaluación...");
            $("#error-nombre").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombre").fadeOut();
        }

        if (slug == "") {
            $("#error-slug").html("Debe ingresar el slug del tipo de periodo de evaluación...");
            $("#error-slug").fadeIn();
            cont_errores++;
        } else {
            $("#error-slug").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "tipos_periodo/insertar_tipo_periodo.php",
                method: "POST",
                type: "html",
                data: {
                    nombre: nombre,
                    slug: slug
                },
                dataType: "json",
                success: function(response) {
                    cargarTiposPeriodosEvaluacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoTipoPeriodoModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "tipos_periodo/obtener_tipo_periodo.php",
            type: "POST",
            data: "id_tipo_periodo=" + id,
            success: function(result) {
                var r = JSON.parse(result);
                $("#id_tipo_periodo").val(r.id_tipo_periodo);
                $("#nombreu").val(r.tp_descripcion);
                $("#slugu").val(r.tp_slug);
            }
        });
    }

    $('#form_update').submit(function(e) {
        e.preventDefault();

        let cont_errores = 0;

        let id_tipo_periodo = $("#id_tipo_periodo").val().trim();
        let nombre = $("#nombreu").val().trim();
        let slug = $("#slugu").val();

        if (nombre == "") {
            $("#error-nombreu").html("Debe ingresar el nombre del tipo de periodo de evaluación...");
            $("#error-nombreu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombreu").fadeOut();
        }

        if (slug == "") {
            $("#error-slugu").html("Debe ingresar el slug del tipo de periodo de evaluación...");
            $("#error-slugu").fadeIn();
            cont_errores++;
        } else {
            $("#error-slugu").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "tipos_periodo/actualizar_tipo_periodo.php",
                data: {
                    id_tipo_periodo: id_tipo_periodo,
                    nombre: nombre,
                    slug: slug
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    cargarTiposPeriodosEvaluacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarTipoPeriodoModal").modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Otro manejador error
                    console.log(jqXHR.responseText);
                }
            });
        }
    });

    function eliminarTipoPeriodo(id) {
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
                    url: "tipos_periodo/eliminar_tipo_periodo.php",
                    data: {
                        id_tipo_periodo: id
                    },
                    dataType: "json",
                    success: function(data) {
                        // console.log(data)
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });

                        cargarTiposPeriodosEvaluacion();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "tipo_educacion/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                console.log(response);
                cargarNivelesEducacion();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>