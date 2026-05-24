<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sub Periodos de Evaluación
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/subperiodos_evaluacion/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
                    <hr>

                    <?php 
                    include RUTA_APP . "/vistas/inc/mensaje.php";
                    ?>

                    <table id="t_subperiodos_evaluacion" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_subperiodos_evaluacion">
                            <?php foreach ($datos['subperiodos_evaluacion'] as $v) : ?>
                                <tr data-index="<?= $v->id_sub_periodo_evaluacion ?>" data-orden="<?= $v->pe_orden ?>">
                                    <td><?= $v->id_sub_periodo_evaluacion ?></td>
                                    <td><?= $v->pe_nombre ?></td>
                                    <td><?= $v->pe_abreviatura ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo RUTA_URL; ?>/subperiodos_evaluacion/edit/<?php echo $v->id_sub_periodo_evaluacion; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                            <a href="<?php echo RUTA_URL; ?>/subperiodos_evaluacion/delete/<?php echo $v->id_sub_periodo_evaluacion; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
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
                title: "¿Estás seguro de eliminar este registro?",
                text: "¡Una vez eliminado no podrá recuperarse!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, elimínalo!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>