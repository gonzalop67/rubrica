<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sub Niveles de Educación
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoSubNivelEducacionModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <!-- table -->
                        <table class="table table-bordered table-striped table-hover fuente9">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nivel</th>
                                    <th>Subnivel</th>
                                    <th>¿Es Bachillerato?</th>
                                    <th colspan=2>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="subniveles_educacion">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once("modalInsert.php") ?>
<?php require_once("modalUpdate.php") ?>

<script>
    $(document).ready(function() {
        cargarSubNivelesEducacion();
        cargarNivelesEducacion();

        $("#nombre").keyup(function() {
            generarSlug($(this).val(), "slug");
        });

        $("#nombreu").keyup(function() {
            generarSlug($(this).val(), "slugu");
        });
    });

    function cargarSubNivelesEducacion() {
        $.ajax({
            type: "GET",
            url: "subnivel_educacion/cargar_subniveles_educacion.php",
            dataType: "html",
            success: function (response) {
                $("#subniveles_educacion").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function cargarNivelesEducacion() {
        $.ajax({
            type: "GET",
            url: "subnivel_educacion/cargar_niveles_educacion.php",
            dataType: "html",
            success: function (response) {
                $("#nivel_id").append(response);
                $("#nivel_idu").append(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "subnivel_educacion/obtener_subnivel_educacion.php",
            type: "POST",
            data: "id_subnivel_educacion=" + id,
            success: function(result) {
                var r = JSON.parse(result);
                $("#id_subnivel_educacion").val(r.id);
                setearIndice("nivel_idu", r.nivel_id);
                $("#nombreu").val(r.nombre);
                $("#slugu").val(r.slug);
                setearIndice("es_bachilleratou", r.es_bachillerato);
            }
        });
    }

    function insertarSubNivelEducacion() {
        let cont_errores = 0;
        const nivel_id = $("#nivel_id").val();
        const nombre = $("#nombre").val();
        const slug = $("#slug").val();
        const es_bachillerato = $("#es_bachillerato").val();

        if (nivel_id == "") {
            $("#error-nivel_id").html("Debe seleccionar un nivel de educación...");
            $("#error-nivel_id").fadeIn();
            cont_errores++;
        } else {
            $("#error-nivel_id").fadeOut();
        }

        if (nombre == "") {
            $("#error-nombre").html("Debe ingresar el nombre del sub nivel de educación...");
            $("#error-nombre").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombre").fadeOut();
        }

        if (slug == "") {
            $("#error-slug").html("Debe ingresar el slug del sub nivel de educación...");
            $("#error-slug").fadeIn();
            cont_errores++;
        } else {
            $("#error-slug").fadeOut();
        }

        if (es_bachillerato == "") {
            $("#error-es_bachillerato").html("Debe seleccionar si es subnivel de bachillerato o no...");
            $("#error-es_bachillerato").fadeIn();
            cont_errores++;
        } else {
            $("#error-es_bachillerato").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "subnivel_educacion/insertar_subnivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    nivel_id: nivel_id,
                    nombre: nombre,
                    slug: slug,
                    es_bachillerato: es_bachillerato
                },
                dataType: "json",
                success: function(response) {
                    cargarSubNivelesEducacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoSubNivelEducacionModal").modal('hide');
                }
            });
        }

        return false;
    }

    function actualizarSubNivelEducacion() {
        let cont_errores = 0;
        const id = $("#id_subnivel_educacion").val();
        const nivel_id = $("#nivel_idu").val();
        const nombre = $("#nombreu").val();
        const slug = $("#slugu").val();
        const es_bachillerato = $("#es_bachilleratou").val();

        if (nivel_idu == "") {
            $("#error-nivel_idu").html("Debe seleccionar un nivel de educación...");
            $("#error-nivel_idu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nivel_idu").fadeOut();
        }

        if (nombre == "") {
            $("#error-nombreu").html("Debe ingresar el nombre del sub nivel de educación...");
            $("#error-nombreu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombreu").fadeOut();
        }

        if (slug == "") {
            $("#error-slugu").html("Debe ingresar el slug del sub nivel de educación...");
            $("#error-slugu").fadeIn();
            cont_errores++;
        } else {
            $("#error-slugu").fadeOut();
        }

        if (es_bachillerato == "") {
            $("#error-es_bachilleratou").html("Debe seleccionar si es subnivel de bachillerato o no...");
            $("#error-es_bachilleratou").fadeIn();
            cont_errores++;
        } else {
            $("#error-es_bachilleratou").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "subnivel_educacion/actualizar_subnivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    id: id,
                    nivel_id: nivel_id,
                    nombre: nombre,
                    slug: slug,
                    es_bachillerato: es_bachillerato
                },
                dataType: "json",
                success: function(response) {
                    cargarSubNivelesEducacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarSubNivelModal").modal('hide');
                }
            });
        }

        return false;
    }

    function eliminarSubNivelEducacion(id) {
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
                    url: "subnivel_educacion/eliminar_subnivel_educacion.php",
                    data: {
                        id_subnivel_educacion: id
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });

                        cargarSubNivelesEducacion();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>