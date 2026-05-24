<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sub Niveles de Educación
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/subniveles_educacion/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
                    <hr>

                    <div id="alert-message" class="alert alert-<?= isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'danger' ?> alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-<?= isset($_SESSION['icono']) ? $_SESSION['icono'] : 'ban' ?>"></i> <span id="mensaje"><?php echo isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje'])) unset($_SESSION['mensaje']) ?>
                    <?php if (isset($_SESSION['tipo'])) unset($_SESSION['tipo']) ?>
                    <?php if (isset($_SESSION['icono'])) unset($_SESSION['icono']) ?>

                    <table id="t_tipos_educacion" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>¿Es Bachillerato?</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_subniveles_educacion">
                            <?php foreach ($datos['subniveles_educacion'] as $v) : ?>
                                <tr data-index="<?= $v->id ?>" data-orden="<?= $v->orden ?>">
                                    <td><?= $v->id ?></td>
                                    <td><?= $v->nombre ?></td>
                                    <td><?= ($v->es_bachillerato == 1) ? 'Sí' : 'No' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo RUTA_URL; ?>/subniveles_educacion/edit/<?php echo $v->id; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                            <a href="<?php echo RUTA_URL; ?>/subniveles_educacion/delete/<?php echo $v->id; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
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