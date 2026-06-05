<style>
    .menu_link a {
        color: #000;
        text-decoration: none;
    }

    .menu_link a:hover {
        color: #0066ff;
    }
</style>
<link rel="stylesheet" href="public/css/jquery.nestable.css">
<div class="content-wrapper">
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
                <span class="btn btn-light" data-toggle="modal" data-target="#nuevoMenuModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
                <div id="img_loader" class="text-center" style="display: none;">
                    <img src="public/img/ajax-loader.gif" alt="Procesando..." />
                </div>
                <!-- message -->
                <div id="mensaje" class="fuente9 text-center">
                    <?php
                    if (isset($_SESSION["msg"])) {
                        echo $_SESSION["msg"];
                        unset($_SESSION["msg"]);
                    }
                    ?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 table-responsive">
                        <div id="menu">
                            <div class="dd" id="nestable">
                                <ol class="dd-list">
                                    <?php
                                    $menusNivel1 = $db->consulta("SELECT m.*, pe_nombre FROM `sw_menu` m, `sw_menu_perfil` mp, `sw_perfil` p WHERE m.id_menu = mp.id_menu AND p.id_perfil = mp.id_perfil AND mnu_padre = 0 ORDER BY pe_nombre, mnu_padre, mnu_orden");
                                    while ($menu = $db->fetch_assoc($menusNivel1)) {
                                    ?>
                                        <li class="dd-item dd3-item" data-id="<?php echo $menu["id_menu"] ?>">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content menu_link">
                                                <a href="#" onclick="obtenerDatos(<?php echo $menu['id_menu'] ?>)" data-toggle="modal" data-target="#editarMenuModal">(<?php echo $menu["pe_nombre"] ?>) <?php echo $menu["mnu_texto"] ?></a>
                                                <a href="menus/eliminar_menu.php?id_menu=<?php echo $menu["id_menu"]; ?>&id_usuario=<?php echo $id_usuario; ?>&id_perfil=<?php echo $id_perfil; ?>&id_menu2=<?php echo $_GET['id_menu']; ?>" class="eliminar-menu pull-right" title="Eliminar este menú"><i class="text-danger fa fa-trash-o"></i></a>
                                            </div>
                                            <?php
                                            $menusNivel2 = $db->consulta("SELECT *
                                                                            FROM sw_menu
                                                                           WHERE mnu_padre = " . $menu["id_menu"] . " 
                                                                           ORDER BY mnu_orden");
                                            $num_submenus = $db->num_rows($menusNivel2);
                                            if ($num_submenus > 0) {
                                            ?>
                                                <ol class="dd-list">
                                                    <?php
                                                    while ($menu2 = $db->fetch_assoc($menusNivel2)) {
                                                    ?>
                                                        <li class="dd-item dd3-item" data-id="<?php echo $menu2["id_menu"] ?> ">
                                                            <div class="dd-handle dd3-handle"></div>
                                                            <div class="dd3-content menu_link">
                                                                <a href="#" onclick="obtenerDatos(<?php echo $menu2['id_menu'] ?>)" data-toggle="modal" data-target="#editarMenuModal"><?php echo $menu2["mnu_texto"] ?></a>
                                                                <a href="menus/eliminar_menu.php?id_menu=<?php echo $menu2["id_menu"]; ?>&id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil; ?>&id_menu2=<?php echo $_GET['id_menu']; ?>" class="eliminar-menu pull-right" title="Eliminar este menú"><i class="text-danger fa fa-trash-o"></i></a>
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
</div>
<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>

<script src="public/js/jquery.nestable.js"></script>
<script type="text/javascript" src="public/js/funciones.js"></script>
<script>
    $(document).ready(function() {
        $('#nestable').nestable('expandAll');

        $('#nestable').nestable().on('change', function() {
            $.ajax({
                url: "menus/guardar_orden.php",
                type: 'POST',
                data: {
                    menu: $('#nestable').nestable('serialize')
                },
                success: function(respuesta) {
                    //Redireccionar a la misma página para que repinte el nuevo orden de los menús.
                    window.location = "administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $_GET['id_menu'] ?>&nivel=2";
                }
            });
        });

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
                    window.location = url;
                }
            });
        });

        $('#icono').on('blur', function() {
            $('#mostrar-icono').removeClass().addClass('fa fa-fw ' + $(this).val());
        });

        $('#iconou').on('blur', function() {
            $('#mostrar-iconou').removeClass().addClass('fa fa-fw ' + $(this).val());
        });
    });

    function obtenerDatos(id) {
        $.ajax({
            url: "menus/obtener_datos.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                //console.log(r);
                $("#id_menu").val(r.id_menu);
                $("#textou").val(r.mnu_texto);
                $("#enlaceu").val(r.mnu_enlace);
                $("#iconou").val(r.mnu_icono);
                setearIndice("publicadou", r.mnu_publicado);
                setearIndice("id_perfilu", r.id_perfil);
            }
        });
    }

    function insertarMenu() {
        const cont_errores = 0;
        const texto = $("#texto").val().trim();
        const icono = $("#icono").val().trim();
        const enlace = $("#enlace").val().trim();
        const publicado = $("#publicado").val();
        const id_perfil = $("#id_perfil").val();

        var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (texto == "") {
            $("#error-texto").html("Debe ingresar el texto del menú...");
            $("#error-texto").fadeIn();
            cont_errores++;
        } else if (!reg_texto.test(texto)) {
            $("#error-texto").html("El texto del menú debe contener al menos tres caracteres alfabéticos.");
            $("#error-texto").fadeIn();
            cont_errores++;
        } else {
            $("#error-texto").fadeOut();
        }

        if (enlace == "") {
            $("#error-enlace").html("Debe ingresar el enlace del menú...");
            $("#error-enlace").fadeIn();
            cont_errores++;
        } else {
            $("#error-enlace").fadeOut();
        }

        if (cont_errores == 0) {
            $('#form_insert')[0].reset(); //limpiar formulario
            $("#nuevoMenuModal").modal('hide');
            $("#img_loader").show();
            $.ajax({
                type: "POST",
                url: "menus/insertar_menu.php",
                data: {
                    mnu_texto: texto,
                    mnu_enlace: enlace,
                    mnu_publicado: publicado,
                    mnu_icono: icono,
                    id_perfil: id_perfil
                },
                dataType: "html",
                success: function(r) {
                    $("#img_loader").hide();
                    //$("#mensaje").html(r);

                    window.location = "administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $_GET['id_menu'] ?>&nivel=2";
                }
            });
        }

        return false;
    }

    function actualizarMenu() {
        const cont_errores = 0;
        const id_menu = $("#id_menu").val();
        const texto = $("#textou").val().trim();
        const enlace = $("#enlaceu").val().trim();
        const icono = $("#iconou").val().trim();
        const publicado = $("#publicadou").val();
        const id_perfil = $("#id_perfilu").val();

        var reg_texto = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (texto == "") {
            $("#mensaje5").html("Debe ingresar el texto del menú...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else if (!reg_texto.test(texto)) {
            $("#mensaje5").html("El texto del menú debe contener al menos tres caracteres alfabéticos.");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (enlace == "") {
            $("#mensaje6").html("Debe ingresar el enlace del menú...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (cont_errores == 0) {
            $('#form_update')[0].reset(); //limpiar formulario
            $("#editarMenuModal").modal('hide');
            $("#img_loader").show();
            $.ajax({
                type: "POST",
                url: "menus/actualizar_menu.php",
                data: {
                    id_menu: id_menu,
                    mnu_texto: texto,
                    mnu_enlace: enlace,
                    mnu_icono: icono,
                    mnu_publicado: publicado,
                    id_perfil: id_perfil
                },
                dataType: "html",
                success: function(r) {
                    $("#img_loader").hide();
                    // $("#mensaje").html(r);

                    window.location = "administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $_GET['id_menu'] ?>&nivel=2";
                }
            });
        }

        return false;
    }
</script>