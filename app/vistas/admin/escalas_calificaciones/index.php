<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Escalas de Calificaciones
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/escalas_calificaciones/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
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
                    <table id="t_escalas" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Cualitativa</th>
                                <th>Cuantitativa</th>
                                <th>Nota Mínima</th>
                                <th>Nota Máxima</th>
                                <th>Equivalencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_escalas">
                            <?php
                            if (count($datos['escalas_calificaciones']) > 0) {
                                $contador = 0;
                            ?>
                                <?php foreach ($datos['escalas_calificaciones'] as $v) : ?>
                                    <tr data-index="<?= $v->id_escala_calificaciones ?>" data-orden="<?= $v->ec_orden ?>">
                                        <?php $contador++; ?>
                                        <td><?= $contador ?></td>
                                        <td><?= $v->id_escala_calificaciones ?></td>
                                        <td><?= $v->ec_cualitativa ?></td>
                                        <td><?= $v->ec_cuantitativa ?></td>
                                        <td><?= $v->ec_nota_minima ?></td>
                                        <td><?= $v->ec_nota_maxima ?></td>
                                        <td><?= $v->ec_equivalencia ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo RUTA_URL; ?>/escalas_calificaciones/edit/<?php echo $v->id_escala_calificaciones; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                                <a href="<?php echo RUTA_URL; ?>/escalas_calificaciones/delete/<?php echo $v->id_escala_calificaciones; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php } else { ?>
                                <tr class="text-center">
                                    <td colspan="8">Aún no se han ingresado escalas de calificaciones...</td>
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
            swal({
                    title: "¿Estás seguro de eliminar este registro?",
                    text: "¡Una vez eliminado no podrá recuperarse!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = url;
                    }
                });
        });
        //Hacer la tabla "sortable"
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
            url: "<?php echo RUTA_URL; ?>/escalas_calificaciones/saveNewPositions",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //console.log(response);
                window.location.href = "<?php echo RUTA_URL; ?>/escalas_calificaciones";
            }
        });
    }
</script>