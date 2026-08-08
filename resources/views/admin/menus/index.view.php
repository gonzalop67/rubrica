@extends('layout.app')

@section('styles')
    <link rel="stylesheet" href="{{ RUTA_URL }}/public/css/jquery.nestable.css">
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        #nestable {
            max-width: 100%;
            width: 100%;
            float: none;
        }

        /* AISLAMIENTO EXCLUSIVO PARA EL FORMULARIO IZQUIERDO */
        #form_insert .box-visor {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            max-width: 38px !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        #form_insert #mi-previsualizador-icono,
        #form_insert #mi-previsualizador-icono::before {
            font-size: 16px !important;
            width: 16px !important;
            height: 16px !important;
            line-height: 16px !important;
            display: inline-block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row m-t-10">
        <!-- COLUMNA IZQUIERDA: Formulario de Inserción Fijo (Tamaño 4) -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-plus mr-2"></i> Crear Nuevo Menú</h6>
                </div>
                <div class="card-body">
                    <form id="form_insert" action="{{ RUTA_URL }}/menus/store" method="POST">

                        <!-- Texto del Menú -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="font-weight-bold">Texto / Nombre:</label>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                placeholder="Ej: Registrar Venta" required>
                            <div id="error-nombre" class="invalid-feedback"></div>
                        </div>

                        <!-- Enlace / URL -->
                        <div class="form-group mb-3">
                            <label for="url" class="font-weight-bold">Enlace / URL:</label>
                            <input type="text" class="form-control" name="url" id="url"
                                placeholder="Ej: /ventas/nuevo o #" required>
                            <div id="error-url" class="invalid-feedback"></div>
                        </div>

                        <!-- Ícono FontAwesome por defecto -->
                        <div class="form-group mb-3">
                            <label for="icono" class="font-weight-bold">Ícono:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icono" id="icono"
                                    placeholder="Ej: mdi mdi-airplay o fas fa-user">
                                <div class="input-group-append">
                                    <!-- Selector de Bootstrap con clase personalizada 'box-visor' -->
                                    <span
                                        class="input-group-text bg-light d-flex align-items-center justify-content-center p-0 box-visor">
                                        <i id="mi-previsualizador-icono" class="fas fa-question text-secondary"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="error-icono" class="invalid-feedback"></div>
                        </div>

                        <!-- Menú Padre (Carga dinámica) -->
                        <div class="form-group mb-3">
                            <label for="padre_id" class="font-weight-bold">Menú Padre (Ubicación):</label>
                            <select class="form-control" name="padre_id" id="padre_id">
                                <option value="">-- Ninguno (Es un menú principal) --</option>
                                @foreach ($menus_principales as $padre)
                                    <!-- Se incluye el nombre del perfil en el texto visible de la opción -->
                                    <option value="{{ $padre['id_menu'] }}">
                                        [{{ $padre['pe_nombre'] }}] — {{ $padre['mnu_texto'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="error-padre_id" class="invalid-feedback"></div>
                        </div>

                        <!-- Permiso Requerido / Slug -->
                        <div class="form-group mb-4">
                            <label for="permiso_slug" class="font-weight-bold">Permiso Requerido:</label>
                            <select class="form-control" name="permiso_slug" id="permiso_slug">
                                <option value="">-- Público / Contenedor (Sin Permiso) --</option>
                                @foreach ($permisos_disponibles as $permiso)
                                    <option value="{{ $permiso['slug'] }}">{{ $permiso['nombre'] }} ({{ $permiso['slug'] }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="error-permiso_slug" class="invalid-feedback"></div>
                        </div>

                        <button type="submit" id="button-save" class="btn btn-primary btn-block shadow-sm">
                            <i class="fa fa-save mr-1"></i> Registrar Menú
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Selector y Árbol Nestable (Tamaño 8) -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">

                    <!-- Botón para abrir la papelera (puedes ubicarlo arriba del selector de perfiles) -->
                    <button type="button" class="btn btn-outline-danger btn-sm float-right mb-3"
                        onclick="cargarPapelera()">
                        <i class="fas fa-trash-alt mr-1"></i> Ver Papelera de Menús
                    </button>

                    <!-- Selector de Roles -->
                    <div class="form-group mb-4">
                        <label for="select-perfil" class="font-weight-bold">Selecciona un Perfil / Rol:</label>
                        <select id="select-perfil" class="form-control">
                            <option value="">-- Seleccione un Perfil --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role['id_perfil'] }}">{{ $role['pe_nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Contenedor Nestable -->
                    <div class="cf nestable-lists">
                        <div id="nestable" class="dd">
                            <div id="nestable-placeholder">
                                <div class="text-muted text-center py-4">
                                    Selecciona un perfil para cargar y organizar sus menús.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Editar Menu Modal -->
    <div class="modal fade" id="editarMenuModal" tabindex="-1" role="dialog" aria-labelledby="editarMenuModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarMenuModalLabel"><i class="fas fa-edit mr-2"></i> Editar Menú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form_update" action="<?= RUTA_URL ?>/menus/update" method="POST">
                    <!-- Llave oculta del registro actual -->
                    <input type="hidden" name="id_update" id="id_update">

                    <div class="modal-body">

                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre_update" class="font-weight-bold">Texto / Nombre:</label>
                            <input type="text" class="form-control" name="nombre" id="nombre_update" required>
                            <div id="error-nombre_update" class="invalid-feedback"></div>
                        </div>

                        <!-- URL -->
                        <div class="form-group mb-3">
                            <label for="url_update" class="font-weight-bold">Enlace / URL:</label>
                            <input type="text" class="form-control" name="url" id="url_update" required>
                            <div id="error-url_update" class="invalid-feedback"></div>
                        </div>

                        <!-- Ícono -->
                        <div class="form-group mb-3">
                            <label for="icono_update" class="font-weight-bold">Ícono:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icono" id="icono_update">
                                <div class="input-group-append">
                                    <span
                                        class="input-group-text bg-light d-flex align-items-center justify-content-center p-0"
                                        style="width: 38px; height: 38px;">
                                        <i id="mostrar-icono-update" class="fas fa-question text-secondary"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="error-icono_update" class="invalid-feedback"></div>
                        </div>

                        <!-- Permiso Slug -->
                        <div class="form-group mb-3">
                            <label for="permiso_slug_update" class="font-weight-bold">Permiso Requerido:</label>
                            <select class="form-control" name="permiso_slug" id="permiso_slug_update">
                                <option value="">-- Público / Contenedor (Sin Permiso) --</option>
                                <?php 
                                    foreach($permisos_disponibles as $permiso):
                                ?>
                                <option value="<?= $permiso['slug'] ?>">
                                    <?= $permiso['nombre'] . ' (' . $permiso['slug'] . ')' ?>
                                </option>
                                <?php 
                                    endforeach;
                                ?>
                            </select>
                            <div id="error-permiso_slug_update" class="invalid-feedback"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" id="button-update-submit" class="btn btn-success"><i
                                class="fa fa-save mr-1"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once RAIZ_PROYECTO . '/resources/views/admin/menus/modalPapelera.php'; ?>
@endsection

@section('scripts')
    <script src="{{ RUTA_URL }}/public/assets/js/jquery.nestable.js"></script>
    <script src="{{ RUTA_URL }}/public/assets/js/funciones.js"></script>
    <script>
        $(document).ready(function() {
            // 1. Previsualización para el Formulario de Inserción (Fijo)
            $(document).on('input', '#icono', function() {
                let iconoClase = $(this).val().trim();
                let $inputGroupText = $('#mi-previsualizador-icono')
                    .parent(); // Apunta al contenedor SPAN

                if ($inputGroupText.length === 0) return;

                if (iconoClase !== "") {
                    // Reconstruimos una etiqueta <i> limpia con las clases del usuario
                    $inputGroupText.html(
                        `<i id="mi-previsualizador-icono" class="${iconoClase} text-primary"></i>`
                    );
                } else {
                    // Regresa al signo de pregunta por defecto
                    $inputGroupText.html(
                        '<i id="mi-previsualizador-icono" class="fas fa-question text-secondary"></i>'
                    );
                }

                // 🔥 PASO CRÍTICO: Obliga al script de FontAwesome a procesar el nuevo <i> si es necesario
                if (window.FontAwesome) {
                    window.FontAwesome.dom.i2svg({
                        node: $inputGroupText[0]
                    });
                }
            });

            // 2. Previsualización para el Formulario de Edición (Modal)
            $(document).on('input', '#icono_update', function() {
                let iconoClase = $(this).val().trim();
                let $inputGroupText = $('#mostrar-icono-update')
                    .parent(); // Apunta al contenedor SPAN

                if ($inputGroupText.length === 0) return;

                if (iconoClase !== "") {
                    $inputGroupText.html(
                        `<i id="mostrar-icono-update" class="${iconoClase} text-primary"></i>`);
                } else {
                    $inputGroupText.html(
                        '<i id="mostrar-icono-update" class="fas fa-question text-secondary"></i>'
                    );
                }

                if (window.FontAwesome) {
                    window.FontAwesome.dom.i2svg({
                        node: $inputGroupText[0]
                    });
                }
            });

            // 2. Cambio en el selector de Perfil / Rol
            $(document).on('change', '#select-perfil', function() {
                let perfilId = $(this).val();
                if (perfilId === '') {
                    $('#nestable').html(
                        '<div id="nestable-placeholder"><div class="text-muted text-center py-4">Selecciona un perfil para cargar sus menús.</div></div>'
                    );
                    return;
                }
                $('#nestable').html(
                    '<div id="nestable-placeholder"><div class="text-muted text-center py-4"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Cargando menús...</div></div>'
                );
                cargar_menus_asociados(perfilId);
            });

            // 3. Envío del Formulario Fijo mediante AJAX
            $('#form_insert').on('submit', function(e) {
                e.preventDefault();

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Limpieza nativa del formulario
                            if ($('#form_insert')[0]) {
                                $('#form_insert')[0].reset();
                            }

                            // Restauración aislada del marcador de posición
                            setTimeout(function() {
                                let visor = document.getElementById(
                                    'mi-previsualizador-icono');
                                if (visor) {
                                    visor.className =
                                        "fas fa-question text-secondary";
                                }
                            }, 10);

                            // Recargar el árbol Nestable si hay un rol seleccionado
                            let perfilActual = $('#select-perfil').val();
                            if (perfilActual !== '') {
                                cargar_menus_asociados(perfilActual);
                            }

                            // Recargar select de menús padres
                            if (response.nuevo_padre) {
                                $('#padre_id').append(new Option(response.nuevo_padre
                                    .nombre,
                                    response.nuevo_padre.id));
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;
                            $.each(errores, function(campo, mensaje) {
                                $('#' + campo).addClass('is-invalid');
                                $('#error-' + campo).text(mensaje);
                            });
                        } else {
                            Swal.fire('Error',
                                'Ocurrió un error inesperado al guardar.',
                                'error');
                        }
                    }
                });
            });

            // 4. Envío del Formulario del Modal (Edición) mediante AJAX
            $('#form_update').on('submit', function(e) {
                e.preventDefault();

                // Limpia únicamente los errores del formulario de actualización
                $('#form_update .form-control').removeClass('is-invalid');
                $('#form_update .invalid-feedback').text('');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Notificación de éxito flotante
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Cierra el modal de manera segura mediante Bootstrap
                            $('#editarMenuModal').modal('hide');

                            // Recargar el árbol Nestable si hay un rol seleccionado actualmente
                            let perfilActual = $('#select-perfil').val();
                            if (perfilActual !== '') {
                                cargar_menus_asociados(perfilActual);
                            }

                            // Opcional: Si manejas el select dinámico de menús padres en la columna izquierda,
                            // puedes actualizar el texto de la opción modificada aquí si fuese necesario.
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errores = xhr.responseJSON.errors;
                            $.each(errores, function(campo, mensaje) {
                                // Mapeo dinámico: Si el backend responde error en 'nombre', 
                                // afectará a '#nombre_update' y '#error-nombre_update'
                                let sufijo = (campo === 'id_update') ? '' :
                                    '_update';
                                $('#' + campo + sufijo).addClass('is-invalid');
                                $('#error-' + campo + sufijo).text(mensaje);
                            });
                        } else {
                            Swal.fire('Error',
                                'Ocurrió un error inesperado al actualizar los cambios.',
                                'error');
                        }
                    }
                });
            });

            // 6. Interceptación y eliminación de menús delegada (Para elementos dinámicos de Nestable)
            $(document).on('click', '.eliminar-menu', function(event) {
                event.preventDefault();

                let urlEliminar = $(this).attr('href');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "El permiso será enviado a la papelera.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: urlEliminar,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });

                                    let perfilActual = $('#select-perfil')
                                        .val();
                                    if (perfilActual !== '') {
                                        cargar_menus_asociados(perfilActual);
                                    }
                                }
                            },
                            error: function(xhr) {
                                let errorMsg =
                                    'No se pudo eliminar el menú en este momento.';
                                if (xhr.responseJSON && xhr.responseJSON
                                    .error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // 7. Interceptación y restauración de menús mediante AJAX
            $(document).on('click', '.restaurar-menu', function(e) {
                e.preventDefault();

                let urlRestaurar = $(this).attr('href');
                let filaTabla = $(this).closest('tr');

                Swal.fire({
                    title: '¿Deseas restaurar este menú?',
                    text: "El menú y sus submenús volverán a estar visibles para los roles asignados.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', // Verde éxito
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, restaurar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: urlRestaurar,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });

                                    // Remoción visual inmediata de la fila restaurada
                                    filaTabla.fadeOut(400, function() {
                                        $(this).remove();

                                        // PASO CRÍTICO: Si la papelera se queda sin registros, oculta el modal
                                        if ($('#tabla-papelera-body tr')
                                            .length === 0) {
                                            $('#papelera-vacia-msg')
                                                .removeClass('d-none');

                                            // Cierra el modal de la papelera automáticamente tras 1 segundo
                                            setTimeout(function() {
                                                $('#papeleraMenuModal')
                                                    .modal(
                                                        'hide');
                                            }, 1000);
                                        }
                                    });

                                    // Recargar el árbol Nestable actual en el fondo de la pantalla
                                    let perfilActual = $('#select-perfil')
                                        .val();
                                    if (perfilActual !== '') {
                                        cargar_menus_asociados(perfilActual);
                                    }
                                } else {
                                    Swal.fire('Atención', response.message,
                                        'warning');
                                }
                            },
                            error: function(xhr) {
                                let errorMsg =
                                    'No se pudo restaurar el menú en este momento.';
                                if (xhr.responseJSON && xhr.responseJSON
                                    .error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            // 9. Interceptación y eliminación definitiva de menús mediante AJAX
            $(document).on('click', '.destruir-menu', function(e) {
                e.preventDefault();

                let urlDestruir = $(this).attr('href');
                let filaTabla = $(this).closest('tr');

                Swal.fire({
                    title: '¿Estás completamente seguro?',
                    text: "¡Esta acción es irreversible y borrará el menú permanentemente de la base de datos!",
                    icon: 'warning', // Cambiado a 'warning' por estándares de SweetAlert2
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Rojo peligro
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, borrar permanentemente',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: urlDestruir,
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Notificación flotante de éxito
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });

                                    // Remoción visual inmediata de la fila en la tabla del modal
                                    filaTabla.fadeOut(400, function() {
                                        $(this).remove();

                                        // Si la papelera se queda vacía, muestra el mensaje correspondiente
                                        if ($('#tabla-papelera-body tr')
                                            .length === 0) {
                                            $('#papelera-vacia-msg')
                                                .removeClass('d-none');

                                            // OPCIONAL: Si la papelera se vacía del todo, cerramos el modal automáticamente tras 1 segundo
                                            setTimeout(function() {
                                                $('#papeleraMenuModal')
                                                    .modal(
                                                        'hide');
                                            }, 1000);
                                        }
                                    });
                                } else {
                                    // SI FALLA LA ELIMINACIÓN (Ej: Claves foráneas): El modal NO se oculta.
                                    // Se muestra una alerta detallada para que el usuario sepa por qué no se borró.
                                    Swal.fire({
                                        title: 'No se puede eliminar',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#3085d6'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMsg =
                                    'No se pudo eliminar definitivamente el menú.';
                                if (xhr.responseJSON && xhr.responseJSON
                                    .error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

        });

        function cargar_menus_asociados(idPerfil) {
            $.ajax({
                url: '<?= RUTA_URL ?>/menus/get_menu_ajax',
                type: 'POST',
                data: {
                    perfil_id: idPerfil
                },
                success: function(htmlResponse) {
                    // 1. DESTRUCCIÓN ABSOLUTA: Removemos datos, eventos y vaciamos el contenedor
                    $('#nestable').removeData('nestable');
                    $('#nestable').off();
                    $('#nestable').html('');

                    // 2. INYECCIÓN: Seteamos el nuevo árbol HTML limpio
                    $('#nestable').html(htmlResponse);

                    // 3. REINICIALIZACIÓN: Volvemos a encender Nestable con su configuración nativa
                    $('#nestable').nestable({
                        maxDepth: 3
                    });

                    // 4. ESCUCHAR EL CAMBIO DE ORDEN
                    $('#nestable').on('change', function() {
                        setTimeout(function() {
                            const dataSerializada = $('#nestable').nestable('serialize');
                            const jsonEstructura = window.JSON.stringify(dataSerializada);

                            $.ajax({
                                url: '<?= RUTA_URL ?>/menus/guardar_orden_ajax',
                                type: 'POST',
                                data: {
                                    estructura: jsonEstructura
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
                                },
                                error: function() {
                                    Swal.fire('Error',
                                        'No se pudo conectar con el servidor para guardar el orden.',
                                        'error');
                                }
                            });
                        }, 50);
                    });
                },
                error: function() {
                    $('#nestable').html(
                        '<div id="nestable-placeholder"><div class="dd-empty text-danger text-center py-4">Error al cargar los menús.</div></div>'
                    );
                }
            });
        }

        // 1. FUNCIÓN GLOBAL: Busca los datos del menú por ID y abre el modal
        function obtenerDatos(idMenu) {
            // Limpieza preventiva de errores previos en el modal
            $('#form_update .form-control').removeClass('is-invalid');
            $('#form_update .invalid-feedback').text('');

            $.ajax({
                url: '<?= RUTA_URL ?>/menus/' + idMenu + '/edit',
                type: 'POST',
                dataType: 'json',
                success: function(menu) {
                    console.log(menu);

                    // Seteamos los valores en los campos del modal de edición
                    $('#id_update').val(menu.id);
                    $('#nombre_update').val(menu.nombre);
                    $('#url_update').val(menu.url);
                    $('#icono_update').val(menu.icono);

                    // Seteamos el permiso correspondiente
                    setearIndice('permiso_slug_update', menu.permiso_slug);

                    // Dispara el evento input de forma nativa para que el visor actualice el ícono automáticamente
                    $('#icono_update').trigger('input');

                    // PASO CRÍTICO: Abre el modal en pantalla una vez los datos están cargados
                    $('#editarMenuModal').modal('show');
                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron extraer los datos del menú.', 'error');
                }
            });
        }

        // 8. FUNCIÓN GLOBAL: Carga los menús con Soft Delete y abre el modal de la papelera
        function cargarPapelera() {
            // Renderizador de carga temporal
            $('#tabla-papelera-body').html(
                '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Consultando papelera...</td></tr>'
            );
            $('#papelera-vacia-msg').addClass('d-none');

            // Abrimos el modal inmediatamente para mejorar la percepción de velocidad
            $('#papeleraMenuModal').modal('show');

            $.ajax({
                url: '<?= RUTA_URL ?>/menus/papelera', // Endpoint que crearemos en el controlador
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let html = '';

                    if (data && data.length > 0) {
                        $.each(data, function(index, menu) {
                            // Validamos si tiene ícono o usamos el signo de pregunta por defecto
                            let iconoClase = (menu.icono && menu.icono.trim() !== "") ? menu
                                .icono :
                                "fas fa-question text-muted";

                            html += `<tr>
                                <td class="text-center"><i class="${iconoClase} text-primary"></i></td>
                                <td class="font-weight-bold">${menu.nombre}</td>
                                <td><code>${menu.url}</code></td>
                                <td><small class="text-muted">${menu.deleted_at}</small></td>
                                <td class="text-center">
                                    <!-- Botón Restaurar (Verde) -->
                                    <a href="<?= RUTA_URL ?>/menus/restore/${menu.id}" class="restaurar-menu btn btn-sm btn-success shadow-sm mr-1" title="Restaurar Menú">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                    <!-- Botón Destruir Permanente (Rojo) -->
                                    <a href="<?= RUTA_URL ?>/menus/destroy/${menu.id}" class="destruir-menu btn btn-sm btn-danger shadow-sm" title="Eliminar Permanentemente">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                </td>
                            </tr>`;
                        });

                        $('#tabla-papelera-body').html(html);
                    } else {
                        // Si no hay datos, limpiamos la tabla y mostramos el mensaje de vacío
                        $('#tabla-papelera-body').empty();
                        $('#papelera-vacia-msg').removeClass('d-none');
                    }
                },
                error: function() {
                    $('#papeleraMenuModal').modal('hide');
                    Swal.fire('Error', 'No se pudieron extraer los datos de la papelera.', 'error');
                }
            });
        }
    </script>
@endsection
