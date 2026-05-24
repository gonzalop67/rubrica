<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jquery.nestable.css">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 id="titulo_principal">
        Menus
        <small>Listado</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <a class="btn btn-light" href="<?php echo RUTA_URL; ?>/menus/create"><i class="fa fa-plus-circle"></i> Nuevo Registro</a>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 table-responsive">
                    <!-- Aquí va el listado de menús -->
                    <div id="menu">
                        <div class="dd" id="nestable">
                            <ol class="dd-list">
                                <?php
                                $menuModelo = $this->modelo('Menu');
                                $menusNivel1 = $menuModelo->obtenerMenusPadres();
                                foreach ($menusNivel1 as $menu) {
                                ?>
                                    <li class="dd-item dd3-item" data-id="<?php echo $menu->id_menu; ?>">
                                        <div class="dd-handle dd3-handle"></div>
                                        <div class="dd3-content menu_link">
                                            <a href="<?php echo RUTA_URL; ?>/menus/edit/<?php echo $menu->id_menu; ?>"><?php echo "(" . $menu->pe_nombre . ") " . $menu->mnu_texto; ?></a>
                                            <a href="<?php echo RUTA_URL; ?>/menus/delete/<?php echo $menu->id_menu; ?>" class="eliminar-menu pull-right" title="Eliminar este menú"><i class="text-danger fa fa-trash-o"></i></a>
                                        </div>
                                        <?php
                                        $menusNivel2 = $menuModelo->obtenerMenusHijos($menu->id_menu);
                                        if (count($menusNivel2) > 0) {
                                        ?>
                                            <ol class="dd-list">
                                                <?php
                                                foreach ($menusNivel2 as $menu2) {
                                                ?>
                                                    <li class="dd-item dd3-item" data-id="<?php echo $menu2->id_menu; ?>">
                                                        <div class="dd-handle dd3-handle"></div>
                                                        <div class="dd3-content menu_link">
                                                            <a href="<?php echo RUTA_URL; ?>/menus/edit/<?php echo $menu2->id_menu; ?>"><?php echo $menu2->mnu_texto; ?></a>
                                                            <a href="<?php echo RUTA_URL; ?>/menus/delete/<?php echo $menu2->id_menu; ?>" class="eliminar-menu pull-right" title="Eliminar este menú"><i class="text-danger fa fa-trash-o"></i></a>
                                                        </div>
                                                    </li>
                                                <?php
                                                }
                                                ?>
                                            </ol>
                                        <?php
                                        }
                                        ?>
                                    </li>
                                <?php
                                }
                                ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<!-- <?php require_once "modalInsert.php" ?> -->

<script src="<?php echo RUTA_URL ?>/public/js/jquery.nestable.js"></script>
<script>
    $(document).ready(function() {
        $('#nestable').nestable().on('change', function() {
            $.ajax({
                url: "<?php echo RUTA_URL; ?>/menus/guardarOrden",
                type: 'POST',
                data: {
                    menu: $('#nestable').nestable('serialize')
                },
                success: function(respuesta) {
                    location.href = "<?php echo RUTA_URL ?>/menus";
                }
            });
        });
        $('#nestable').nestable('expandAll');
        $('.eliminar-menu').on('click', function(event) {
            event.preventDefault();
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
    });
</script>