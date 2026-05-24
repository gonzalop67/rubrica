<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Aportes de Evaluación
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
                    <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/aportes_evaluacion/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
                    <hr>
                    
                    <?php 
                        include RUTA_APP . "/vistas/inc/mensaje.php";
                    ?>

                    <table id="t_aportes_evaluacion" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th>Descripción</th>
                                <th width="100px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_aportes_evaluacion">
                            <?php
                            if (count($datos['aportes_evaluacion']) > 0) {
                                $contador = 0;
                            ?>
                                <?php foreach ($datos['aportes_evaluacion'] as $v) : ?>
                                    <tr>
                                        <td><?= $v->id_aporte_evaluacion ?></td>
                                        <td><?= $v->ap_nombre ?></td>
                                        <td><?= $v->ap_abreviatura ?></td>
                                        <td><?= $v->ap_descripcion ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo RUTA_URL; ?>/aportes_evaluacion/edit/<?php echo $v->id_aporte_evaluacion; ?>" class="btn btn-warning btn-sm" title="Editar"><span class="fa fa-pencil"></span></a>
                                                <a data-id="<?= $v->id_aporte_evaluacion ?>" class="btn btn-danger btn-sm item-delete" title="Eliminar"><span class="fa fa-trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php } else { ?>
                                <tr class="text-center">
                                    <td colspan="5">Aún no se han ingresado aportes de evaluación...</td>
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
            const id = $(this).attr('data-id');
            const tr = $(this).closest('tr');
            // tr.addClass('rojo');
            Swal.fire({
                title: "¿Estás seguro de eliminar este registro?",
                text: "¡Una vez eliminado no podrá recuperarse!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, ¡elimínelo!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo RUTA_URL; ?>/aportes_evaluacion/delete",
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.text,
                                icon: response.icon
                            });
                            tr.remove();
                        }
                    });
                }
            });
        });

        // showAlert("Esto es una prueba...");
    });

    // function showAlert(message) {
    //     document.getElementById("alertMessage").textContent = message;
    //     document.getElementById("customAlert").style.display = "block";
    // }

    // function closeAlert() {
    //     document.getElementById("customAlert").style.display = "none";
    // }
</script>