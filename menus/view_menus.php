<link rel="stylesheet" href="public/css/jquery.nestable.css">

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">

            <!-- COLUMNA IZQUIERDA: Formulario de Inserción Fijo (Tamaño 4) -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-plus-circle"></i> Crear Nuevo Menú</h3>
                    </div>
                    <div class="box-body">
                        <form id="form_insert" action="menus/insertar_menu.php" method="POST">

                            <!-- Texto del Menú -->
                            <div class="form-group" id="group-nombre">
                                <label for="nombre">Texto / Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Registrar Venta" required>
                                <span id="error-nombre" class="help-block" style="display:none;"></span>
                            </div>

                            <!-- Enlace / URL -->
                            <div class="form-group" id="group-url">
                                <label for="url">Enlace / URL:</label>
                                <input type="text" class="form-control" name="url" id="url" placeholder="Ej: /ventas/nuevo o #" required>
                                <span id="error-url" class="help-block" style="display:none;"></span>
                            </div>

                            <!-- Ícono FontAwesome por defecto -->
                            <div class="form-group" id="group-icono">
                                <label for="icono">Ícono:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="icono" id="icono" placeholder="Ej: fa fa-dashboard">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default disabled" style="opacity: 1;">
                                            <i id="mi-previsualizador-icono" class="fa fa-question text-muted"></i>
                                        </span>
                                    </span>
                                </div>
                                <span id="error-icono" class="help-block" style="display:none;"></span>
                            </div>

                            <!-- Menú Padre (Carga dinámica controlada) -->
                            <div class="form-group" id="group-padre_id">
                                <label for="padre_id">Menú Padre (Ubicación):</label>
                                <select class="form-control" name="padre_id" id="padre_id">
                                    <option value="">-- Ninguno (Es un menú principal) --</option>
                                    <?php
                                    $res_menus = $db->consulta("SELECT m.id_menu, m.mnu_texto, p.pe_nombre FROM sw_menu m INNER JOIN sw_menu_perfil mp ON m.id_menu = mp.id_menu INNER JOIN sw_perfil p ON p.id_perfil = mp.id_perfil WHERE m.mnu_padre = 0 ORDER BY p.pe_nombre, m.mnu_orden ASC");
                                    if ($res_menus) {
                                        while ($menu = $db->fetch_assoc($res_menus)) {
                                            echo '<option value="' . intval($menu['id_menu']) . '">' . htmlspecialchars("(" . $menu['pe_nombre'] . ") " . $menu['mnu_texto'], ENT_QUOTES, 'UTF-8') . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <span id="error-padre_id" class="help-block" style="display:none;"></span>
                            </div>

                            <!-- Permiso Requerido / Slug -->
                            <div class="form-group" id="group-permiso_slug">
                                <label for="permiso_slug">Permiso Requerido:</label>
                                <select class="form-control" name="permiso_slug" id="permiso_slug">
                                    <option value="">-- Público / Contenedor (Sin Permiso) --</option>
                                    <?php
                                    $res_permisos = $db->consulta("SELECT nombre, slug FROM sw_permiso ORDER BY nombre");
                                    if ($res_permisos) {
                                        while ($permiso = $db->fetch_assoc($res_permisos)) {
                                            echo '<option value="' . htmlspecialchars($permiso['slug'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($permiso['nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <span id="error-permiso_slug" class="help-block" style="display:none;"></span>
                            </div>

                            <button type="submit" id="button-save" class="btn btn-primary btn-block">
                                <i class="fa fa-save"></i> Registrar Menú
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Selector y Árbol Nestable (Tamaño 8) -->
            <div class="col-md-8">
                <div class="box box-default">
                    <div class="box-body">
                        <!-- Botón para abrir la papelera -->
                        <div class="pull-right" style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-default btn-sm" onclick="cargarPapelera()">
                                <i class="fa fa-trash text-danger"></i> Ver Papelera de Menús
                            </button>
                        </div>
                        <div class="clearfix"></div>

                        <!-- Selector de Roles -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="select-perfil">Selecciona un Perfil / Rol:</label>
                            <select id="select-perfil" class="form-control">
                                <option value="">-- Seleccione un Perfil --</option>
                                <?php
                                $res_perfiles = $db->consulta("SELECT id_perfil, pe_nombre FROM sw_perfil ORDER BY pe_nombre");
                                if ($res_perfiles) {
                                    while ($perfil = $db->fetch_assoc($res_perfiles)) {
                                        echo '<option value="' . intval($perfil['id_perfil']) . '">' . htmlspecialchars($perfil['pe_nombre'], ENT_QUOTES, 'UTF-8') . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Contenedor Nestable -->
                        <div class="cf nestable-lists">
                            <div id="nestable" class="dd">
                                <div id="nestable-placeholder">
                                    <div class="text-muted text-center" style="padding: 20px 0;">
                                        Selecciona un perfil para cargar y organizar sus menús.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Se cargan al final de la vista para asegurar que el HTML ya exista en pantalla -->
<script src="js/funciones.js"></script>
<script src="public/js/jquery.nestable.js"></script>

<script>
    // Usamos el evento nativo del navegador. Este SIEMPRE corre, incluso si jQuery tiene problemas.
    document.addEventListener("DOMContentLoaded", function() {

        // Verificamos de forma segura si jQuery está vivo
        if (typeof $ !== 'undefined') {
            console.log("jQuery v" + $.fn.jquery + " cargado correctamente.");
        } else {
            console.error("CRÍTICO: jQuery no está definido en esta vista. Revisa la ruta en admin.php");
            return; // Detiene la ejecución limpia si falta la librería base
        }

        // 1. Escuchador directo del Selector de Perfiles (Nativo + Soporte Select2)
        $(document).on('change change.select2', '#select-perfil', function() {
            let perfilId = $(this).val();
            console.log("Perfil seleccionado:", perfilId);

            if (!perfilId) {
                $('#nestable').html(
                    '<div id="nestable-placeholder"><div class="text-muted text-center" style="padding: 20px 0;">Selecciona un perfil para cargar y organizar sus menús.</div></div>'
                );
                return;
            }

            cargar_menus_asociados(perfilId);
        });

        // 2. Previsualizador dinámico de íconos en tiempo real
        $(document).on('input', '#icono', function() {
            let iconoClase = $(this).val().trim();
            let $preview = $('#mi-previsualizador-icono');
            $preview.attr('class', iconoClase === '' ? 'fa fa-question text-muted' : iconoClase);
        });

        // 3. Inicialización protegida del formulario izquierdo de validación
        try {
            if (typeof Biblioteca !== 'undefined' && $.isFunction($.fn.validate)) {
                Biblioteca.validacionGeneral('form_insert', {
                    nombre: {
                        required: true
                    },
                    url: {
                        required: true
                    }
                }, {
                    nombre: {
                        required: "El nombre es obligatorio."
                    },
                    url: {
                        required: "La URL es obligatoria."
                    }
                });
            }
        } catch (e) {
            console.warn("Falta inicializar la librería jquery.validate.js.", e);
        }
    });

    // Variable de control para peticiones AJAX concurrentes
    var ajaxCargaEnCurso = false;

    function cargar_menus_asociados(idPerfil) {
        if (ajaxCargaEnCurso) return;
        ajaxCargaEnCurso = true;

        $.ajax({
            url: 'menus/get_menu_ajax.php',
            type: 'POST',
            data: {
                perfil_id: idPerfil
            },
            beforeSend: function() {
                // Capa gris semitransparente con spinner nativo de AdminLTE 2
                $('#nestable').closest('.box').append('<div class="overlay" id="nestable-overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function(htmlResponse) {
                try {
                    // Destrucción absoluta previa para evitar fugas de memoria en el DOM
                    $('#nestable').removeData('nestable').off().empty();

                    // Inyección del árbol HTML limpio enviado por el servidor PHP
                    $('#nestable').html(htmlResponse);

                    // Activación nativa de Nestable
                    $('#nestable').nestable({
                        maxDepth: 3
                    });

                    // Monitoreo del arrastre de elementos para guardar automáticamente
                    $('#nestable').on('change', function() {
                        setTimeout(function() {
                            const dataSerializada = $('#nestable').nestable('serialize');

                            $.ajax({
                                url: 'menus/guardar_orden_ajax',
                                type: 'POST',
                                data: {
                                    estructura: window.JSON.stringify(dataSerializada)
                                },
                                dataType: 'json',
                                success: function(r) {
                                    if (r.ok) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: r.mensaje,
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    } else {
                                        Swal.fire('Error', r.mensaje, 'error');
                                    }
                                }
                            });
                        }, 50);
                    });

                } catch (err) {
                    console.error("Error al procesar el árbol Nestable internamente:", err);
                }
            },
            error: function() {
                $('#nestable').html(
                    '<div class="alert alert-danger" style="margin: 10px 0;">' +
                    '<h4><i class="icon fa fa-ban"></i> ¡Error de Servidor!</h4>' +
                    'No se pudo establecer la conexión asíncrona con el controlador.' +
                    '</div>'
                );
            },
            complete: function() {
                $('#nestable-overlay').remove();
                ajaxCargaEnCurso = false;
            }
        });
    }
</script>