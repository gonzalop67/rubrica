<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cursos
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/cursos/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
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
                    <table id="t_cursos" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Figura</th>
                                <th>Nivel de Educación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_cursos">
                            <?php
                            if (count($datos['cursos']) > 0) {
                                $contador = 0;
                            ?>
                                <?php foreach ($datos['cursos'] as $v) : ?>
                                    <tr data-index="<?= $v->id_curso ?>" data-orden="<?= $v->cu_orden ?>">
                                        <?php $contador++; ?>
                                        <td><?= $contador ?></td>
                                        <td><?= $v->id_curso ?></td>
                                        <td><?= $v->cu_nombre ?></td>
                                        <td><?= $v->es_figura ?></td>
                                        <td><?= $v->nombre ?></td>
                                        <td>
                                            <a href="<?php echo RUTA_URL; ?>/cursos/edit/<?php echo $v->id_curso; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                            <a href="<?php echo RUTA_URL; ?>/cursos/delete/<?php echo $v->id_curso; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php } else { ?>
                                <tr class="text-center">
                                    <td colspan="6">Aún no se han ingresado cursos...</td>
                                </tr>
                            <?php } ?>
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

<script>
    $(document).ready(function() {
        $('.item-delete').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');

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
                    window.location.href = url;
                }
            });
        });

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

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cursos/saveNewPositions",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                // console.log(response);
                window.location.href = "<?php echo RUTA_URL; ?>/cursos";
            }
        });
    }
</script>