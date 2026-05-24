<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Modalidades
        <small>Listado</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-solid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevaModalidadModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
                <?php if (isset($datos['mensaje'])) : ?>
                    <div class="alert alert-<?= $datos['tipomensaje'] ?> alert-dismissible" style="margin-top: 2px">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-<?= $datos['iconomensaje'] ?>"></i> <?= $datos['mensaje'] ?></p>
                    </div>
                <?php endif ?>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="t_modalidades" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_modalidades">
                                <!-- Aquí se van a poblar las modalidades ingresados en la base de datos -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
</section>
<!-- /.content -->

<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>

<script>
    $(document).ready(function() {
        pagination(1);

        $('table tbody').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    if ($(this).attr('data-orden') != (index + 1)) {
                        $(this).attr('data-orden', (index + 1)).addClass('updated');
                    }
                });

                saveNewPositions();
            }
        });
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        var url = "<?php echo RUTA_URL; ?>/modalidades/paginar";
        $.ajax({
            type: 'POST',
            url: url,
            data: 'partida=' + partida,
            success: function(data) {
                var array = eval(data);
                $("#tbody_modalidades").html(array[0]);
                $("#pagination").html(array[1]);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    }

    function insertarModalidad() {
        let cont_errores = 0;
        let mo_nombre = $("#nombre").val().trim();
        let mo_activo = $("#activo").val().trim();

        var reg_nombre = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (mo_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre de la modalidad...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else if (!reg_nombre.test(mo_nombre)) {
            $("#mensaje1").html("El nombre de la modalidad debe contener al menos tres caracteres alfabéticos.");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo RUTA_URL; ?>/modalidades/insert",
                data: {
                    mo_nombre: mo_nombre.toUpperCase(),
                    mo_activo: mo_activo
                },
                dataType: "json",
                success: function(r) {
                    pagination(1);
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje,
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevaModalidadModal").modal('hide');
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/modalidades/obtener",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                $("#id_modalidad").val(r.id_modalidad);
                $("#nombreu").val(r.mo_nombre);
                setearIndice("activou", r.mo_activo);
            }
        });
    }

    function actualizarModalidad() {
        let cont_errores = 0;

        let id_modalidad = $("#id_modalidad").val().trim();
        let mo_nombre = $("#nombreu").val().trim();
        let mo_activo = $("#activou").val().trim();

        var reg_nombre = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (mo_nombre == "") {
            $("#mensaje3").html("Debe ingresar el nombre de la modalidad...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else if (!reg_nombre.test(mo_nombre)) {
            $("#mensaje3").html("El nombre de la modalidad debe contener al menos tres caracteres alfabéticos.");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo RUTA_URL; ?>/modalidades/update",
                data: {
                    id_modalidad: id_modalidad,
                    mo_nombre: mo_nombre.toUpperCase(),
                    mo_activo: mo_activo
                },
                dataType: "json",
                success: function(r) {
                    pagination(1);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje,
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarModalidadModal").modal('hide');
                }
            });
        }

        return false;
    }

    function eliminarModalidad(id) {        
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
                    url: "<?php echo RUTA_URL; ?>/modalidades/delete",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {
                        Swal.fire({
                            icon: response.estado,
                            title: response.titulo,
                            text: response.mensaje,
                        });
                        pagination(1);
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
            url: "<?php echo RUTA_URL; ?>/modalidades/saveNewPositions",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                // console.log(response);
                window.location.href = "<?php echo RUTA_URL; ?>/modalidades";
            }
        });
    }
</script>