<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Especialidades
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/especialidades/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
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
                    <table id="t_especialidades" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Figura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_especialidades">
                            <?php
                            if (count($datos['especialidades']) > 0) {
                            ?>
                            <?php foreach ($datos['especialidades'] as $v) : ?>
                                <tr data-index="<?= $v->id_especialidad ?>" data-orden="<?= $v->es_orden ?>">
                                    <td><?= $v->id_especialidad ?></td>
                                    <td><?= $v->es_nombre ?></td>
                                    <td><?= $v->es_figura ?></td>
                                    <td>
                                        <a href="<?php echo RUTA_URL; ?>/especialidades/edit/<?php echo $v->id_especialidad; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                        <a href="<?php echo RUTA_URL; ?>/especialidades/delete/<?php echo $v->id_especialidad; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                            <?php } else { ?>
                                <tr class="text-center">
                                    <td colspan="5">Aún no se han ingresado especialidades...</td>
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
    $(document).ready(function(){
        $('.item-delete').on('click', function(e){
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
    });
</script>