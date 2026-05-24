<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Categorías de Especialidad
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <span class="btn btn-primary" data-toggle="modal" data-target="#nuevaCategoriaModal"><i class="fa fa-plus-circle"></i> Nueva Categoría</span>
                        <hr>
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
                        <table id="t_categories" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_categories">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>

<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>

<script>
    $(document).ready(function() {
        cargarCategoriasEspecialidad();
    });

    function cargarCategoriasEspecialidad() {
        $.ajax({
            url: "categorias_especialidad/cargar_categorias_especialidad.php",
            dataType: "html",
            success: function(data) {
                $("#tbody_categories").html(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function insertarCategoria() {
        let cont_errores = 0;
        const nombre = $("#nombre").val().trim();

        if (nombre == "") {
            $("#error-nombre").html("Debe ingresar el nombre de la categoria...");
            $("#error-nombre").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombre").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "categorias_especialidad/insertar_categoria.php",
                data: {
                    nombre: nombre.trim()
                },
                dataType: "json",
                success: function(r) {
                    // console.log(r);
                    cargarCategoriasEspecialidad();
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevaCategoriaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "categorias_especialidad/obtener_categoria.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                $("#id_categoria").val(r.id_categoria);
                $("#nombreu").val(r.nombre);
            }
        });
    }

    function actualizarCategoria() {
        let cont_errores = 0;
        const id_categoria = $("#id_categoria").val();
        const nombreu = $("#nombreu").val().trim();

        if (nombreu == "") {
            $("#error-nombreu").html("Debe ingresar el nombre de la categoria de la especialidad...");
            $("#error-nombreu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombreu").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "categorias_especialidad/actualizar_categoria.php",
                data: {
                    id_categoria: id_categoria,
                    nombreu: nombreu
                },
                dataType: "json",
                success: function(r) {
                    // console.log(r);
                    cargarCategoriasEspecialidad();
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarCategoriaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function eliminarCategoria(id_categoria) {

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
                    url: "categorias_especialidad/eliminar_categoria.php",
                    data: {
                        id_categoria: id_categoria
                    },
                    dataType: "json",
                    success: function(response) {
                        cargarCategoriasEspecialidad()
                        Swal.fire({
                            icon: response.estado,
                            title: response.titulo,
                            text: response.mensaje
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>