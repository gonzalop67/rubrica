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
            Menús
            <small>Gestión por Perfil</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Columna centrada para optimizar la lectura del árbol Nestable -->
            <div class="col-md-8 col-md-offset-2 col-sm-12">

                <!-- Box de AdminLTE 2 -->
                <div class="box box-primary">
                    <div class="box-header with-border clearfix">
                        <!-- Botón de Nuevo Registro alineado a la izquierda -->
                        <button type="button" id="btn-add" class="btn btn-primary btn-sm pull-left" data-toggle="modal" data-target="#nuevoMenuModal">
                            <i class="fa fa-plus-circle"></i> Nuevo Menú
                        </button>

                        <!-- Mensajes de sesión -->
                        <div id="mensaje" class="fuente9 text-center pull-right">
                            <?php
                            if (isset($_SESSION["msg"])) {
                                echo $_SESSION["msg"];
                                unset($_SESSION["msg"]);
                            }
                            ?>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Selector de Perfiles Integrado -->
                        <div class="form-group">
                            <label for="select-perfil" class="text-bold">Selecciona un Perfil:</label>
                            <select id="select-perfil" class="form-control">
                                <option value="">-- Seleccione un Perfil --</option>
                                <?php
                                // Consulta nativa para listar los perfiles en el select
                                $res_perfiles = $db->consulta("SELECT id_perfil, pe_nombre FROM sw_perfil ORDER BY pe_nombre");
                                while ($perfil = $db->fetch_assoc($res_perfiles)) {
                                ?>
                                    <option value="<?php echo $perfil['id_perfil']; ?>"><?php echo $perfil['pe_nombre']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div id="img_loader" class="text-center" style="display: none; margin-bottom: 3px;">
                            <img src="public/img/ajax-loader.gif" alt="Procesando..." />
                        </div>

                        <!-- Estructura Nestable Dinámica -->
                        <div class="cf nestable-lists">
                            <div class="dd" id="nestable">

                                <!-- Contenedor/Placeholder Inicial -->
                                <div id="nestable-placeholder">

                                    <div class="text-muted text-center" style="padding: 30px 0; font-size: 15px;">
                                        <i class="fa fa-info-circle"></i> Selecciona un perfil para cargar sus menús asignados.
                                    </div>
                                </div>

                                <!-- Lista oculta por defecto hasta que AJAX devuelva la respuesta -->
                                <ol class="dd-list" id="lista-menus-dinamica" style="display: none;">
                                    <!-- Aquí inyectarás el HTML de los <li class="dd-item"> desde tu archivo AJAX mediante PHP -->
                                </ol>

                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
        </div>
    </section>
    <!-- /.content -->
</div>

<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>

<script src="public/js/jquery.nestable.js"></script>
<script type="text/javascript" src="public/js/funciones.js"></script>
<script>
    $(document).ready(function() {
        // $('#nestable').nestable('expandAll');

        // Inicializar el plugin Nestable si no lo has inicializado antes
        $('#nestable').nestable({
            group: 1,
            maxDepth: 2 // Limita la profundidad a 2 niveles según tu base de datos
        });

        $('#select-perfil').on('change', function() {
            var id_perfil = $(this).val();
            var listaMenus = $('#lista-menus-dinamica');
            var placeholder = $('#nestable-placeholder');
            var loader = $('#img_loader');

            // Si selecciona la opción vacía
            if (id_perfil === '') {
                loader.hide();
                listaMenus.hide().html('');
                placeholder.show();
                return;
            }

            // Configuración visual antes del envío
            placeholder.hide();
            listaMenus.hide().html('');
            loader.show();

            // Petición AJAX
            $.ajax({
                url: 'menus/cargar_menus_perfil.php', // Asegúrate de ajustar la ruta correcta
                type: 'GET',
                data: {
                    id_perfil: id_perfil,
                    id_menu_actual: '<?php echo isset($_GET["id_menu"]) ? $_GET["id_menu"] : 0; ?>'
                },
                dataType: 'html',
                success: function(response) {
                    loader.hide();
                    if (response.trim() !== '') {
                        // console.log(response);
                        listaMenus.html(response).fadeIn();
                        // Reinicializar o actualizar el estado de Nestable con la nueva data
                        $('#nestable').nestable('destroy');
                        $('#nestable').nestable({
                            group: 1,
                            maxDepth: 2
                        });
                    } else {
                        listaMenus.hide();
                        placeholder.html('<div class="text-warning text-center" style="padding: 30px 0;"><i class="fa fa-exclamation-triangle"></i> Este perfil no tiene menús asignados.</div>').show();
                    }
                },
                error: function() {
                    loader.hide();
                    placeholder.html('<div class="text-danger text-center" style="padding: 30px 0;"><i class="fa fa-times-circle"></i> Ocurrió un error al cargar los menús.</div>').show();
                }
            });

        });

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
        // Validar que el id sea un número válido antes de hacer la petición
        if (!id || isNaN(id)) {
            console.error("ID de menú no válido");
            return;
        }

        $.ajax({
            url: "menus/obtener_datos.php",
            type: "POST",
            // CORREGIDO: Envío seguro como objeto para evitar problemas de codificación
            data: {
                id: parseInt(id, 10)
            },
            dataType: "json",
            success: function(r) {
                // Se asume que 'r' contiene las propiedades del objeto menú retornado por PHP
                if (r && !r.error) {
                    // Poblar los campos de texto normales del modal
                    $("#id_menu").val(r.id_menu);
                    $("#textou").val(r.mnu_texto);
                    $("#enlaceu").val(r.mnu_enlace);
                    $("#iconou").val(r.mnu_icono);

                    // Asignar valores a los elementos select o checkboxes
                    // Si tienes cargado Select2 (común en AdminLTE), añade .trigger('change')
                    // $("#publicadou").val(r.mnu_publicado).trigger('change');
                    // $("#id_perfilu").val(r.id_perfil).trigger('change');

                    // Opcional: Si usas las funciones personalizadas 'setearIndice' de tu sistema:
                    setearIndice("publicadou", r.mnu_publicado);
                    setearIndice("id_perfilu", r.id_perfil);
                } else {
                    alert("Error al cargar los datos: " + (r.error || "Formato desconocido"));
                    // $("#editarMenuModal").hide();
                }
            },
            error: function(xhr, status, error) {
                // Captura errores de red, respuestas 404, 403 o 500 del servidor
                console.error("Error en la petición AJAX:", error);
                alert("No se pudieron recuperar los datos del menú. Intente nuevamente.");
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