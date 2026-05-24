<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asignaturas
        <small>Listado</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/asignaturas/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>

            <div class="box-tools">
                <form id="form-search" action="<?php echo RUTA_URL; ?>/asignaturas/search" method="POST">
                    <div class="input-group input-group-md" style="width: 250px;">
                        <input type="text" name="patron" id="patron" class="form-control pull-right text-uppercase" placeholder="Buscar Asignatura...">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box-body">
            <input type="hidden" name="id_area" id="id_area">
            <input type="hidden" name="id_asignatura" id="id_asignatura">
            <div class="row">
                <div class="col-md-12 table-responsive">
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
                    <table id="t_asignaturas" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Area</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_asignaturas">
                            <?php
                            if (count($datos['asignaturas']) > 0) {
                                $contador = 0;
                            ?>
                                <?php foreach ($datos['asignaturas'] as $v) : ?>
                                    <tr>
                                        <?php $contador++; ?>
                                        <td><?= $contador ?></td>
                                        <td><?= $v->id_asignatura ?></td>
                                        <td><?= $v->ar_nombre ?></td>
                                        <td><?= $v->as_nombre ?></td>
                                        <td><?= $v->as_abreviatura ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo RUTA_URL; ?>/asignaturas/edit/<?php echo $v->id_asignatura; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                                <a href="javascript:;" class="btn btn-danger item-delete" data="<?php echo $v->id_asignatura; ?>" title="Eliminar"><span class="fa fa-trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php } else { ?>
                                <tr class="text-center">
                                    <td colspan="6">Aún no se han ingresado asignaturas...</td>
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
        $('table tbody').on('click', '.item-delete', function() {
            // e.preventDefault();
            const id_asignatura = $(this).attr('data');
            const url = "<?php echo RUTA_URL; ?>/asignaturas/delete/" + id_asignatura;
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

        $("#patron").keyup(function() {
            var request = $.ajax({
                url: "<?php echo RUTA_URL; ?>/asignaturas/search",
                type: "POST",
                data: {
                    patron: $(this).val().trim()
                },
                dataType: "json"
            });
            request.done(function(data) {
                var array = eval(data);
                if (array[1] == 1) {
                    swal("¡Información!", "No se han encontrado coincidencias...", "info");
                } else {
                    console.log(eval(array[0]));
                    let registros = eval(array[0]);

                    listarAsignaturasEncontradas(registros)
                }
            });
        });
    });

    function listarAsignaturasEncontradas(registros) {
        let html = "";
        for (let i = 0; i < registros.length; i++) {
            html += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + registros[i].id_asignatura + '</td>' +
                '<td>' + registros[i].ar_nombre + '</td>' +
                '<td>' + registros[i].as_nombre + '</td>' +
                '<td>' + registros[i].as_abreviatura + '</td>' +
                '<td>' +
                '<div class="btn-group">' +
                '<a href="<?php echo RUTA_URL; ?>/asignaturas/edit/' + registros[i].id_asignatura + '" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>' +
                '<a href="javascript:;" class="btn btn-danger item-delete" data="' + registros[i].id_asignatura + '" title="Eliminar"><span class="fa fa-trash"></span></a>' +
                '</div>' +
                '</td>' +
                '</tr>';
        }
        $("#tbody_asignaturas").html(html);
    }
</script>