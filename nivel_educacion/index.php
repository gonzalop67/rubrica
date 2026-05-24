<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Niveles de Educación
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <span id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevoNivelModal"><i class="fa fa-plus-circle"></i> Nuevo Nivel</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_niveles">
                                <!-- Aquí se van a poblar los niveles de educación ingresados en la base de datos -->
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
        cargar_niveles_educacion();

        $("#nombre").keyup(function() {
            generarSlug($(this).val(), "slug");
        });

        $("#nombreu").keyup(function() {
            generarSlug($(this).val(), "slugu");
        });
    });

    function cargar_niveles_educacion() {
        $.ajax({
            type: "GET",
            url: "nivel_educacion/cargar_niveles_educacion.php",
            dataType: "html",
            success: function (response) {
                $("#tbody_niveles").html(response);
            }
        });
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "nivel_educacion/obtener_nivel_educacion.php",
            type: "POST",
            data: "id_nivel_educacion=" + id,
            success: function(result) {
                var r = JSON.parse(result);
                $("#id_nivel_educacion").val(r.id);
                $("#nombreu").val(r.nombre);
                $("#slugu").val(r.slug);
            }
        });
    }

    function insertarNivel() {
        let cont_errores = 0;
        let nombre = $("#nombre").val();
        let slug = $("#slug").val();

        if (nombre == "") {
            $("#error-nombre").html("Debe ingresar el nombre del nivel de educación de evaluación...");
            $("#error-nombre").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombre").fadeOut();
        }

        if (slug == "") {
            $("#error-slug").html("Debe ingresar el slug del nivel de educación de evaluación...");
            $("#error-slug").fadeIn();
            cont_errores++;
        } else {
            $("#error-slug").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "nivel_educacion/insertar_nivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    nombre: nombre,
                    slug: slug
                },
                dataType: "json",
                success: function(response) {
                    cargar_niveles_educacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoNivelModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function actualizarNivel() {
        let cont_errores = 0;
        const id = $("#id_nivel_educacion").val();
        const nombre = $("#nombreu").val();
        const slug = $("#slugu").val();

        if (nombre == "") {
            $("#error-nombreu").html("Debe ingresar el nombre del nivel de educación de evaluación...");
            $("#error-nombreu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombreu").fadeOut();
        }

        if (slug == "") {
            $("#error-slugu").html("Debe ingresar el slug del nivel de educación de evaluación...");
            $("#error-slugu").fadeIn();
            cont_errores++;
        } else {
            $("#error-slugu").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "nivel_educacion/actualizar_nivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    id: id,
                    nombre: nombre,
                    slug: slug
                },
                dataType: "json",
                success: function(response) {
                    cargar_niveles_educacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarNivelModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function eliminarNivelEducacion(id) {
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
                    url: "nivel_educacion/eliminar_nivel_educacion.php",
                    data: {
                        id_nivel_educacion: id
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

                        cargar_niveles_educacion();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>