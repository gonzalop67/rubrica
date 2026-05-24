<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Tipos de Periodo de Evaluación
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/tipos_periodo/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
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
                    <table id="t_tipos_periodo" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_tipos_periodo">
                            <?php
                                $contador = 0;
                                foreach ($datos['tipos_periodo'] as $v) {
                                    $contador++;
                            ?>
                                <tr>
                                    <td><?= $contador  ?></td>
                                    <td><?= $v->id_tipo_periodo  ?></td>
                                    <td><?= $v->tp_descripcion ?></td>
                                    <td><?= $v->tp_slug ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo RUTA_URL; ?>/tipos_periodo/edit/<?php echo $v->id_tipo_periodo; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                            <a href="<?php echo RUTA_URL; ?>/tipos_periodo/delete/<?php echo $v->id_tipo_periodo; ?>" class="btn btn-danger item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>