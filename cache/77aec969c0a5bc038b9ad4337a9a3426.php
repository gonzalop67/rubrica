<?php $this->layout = 'layout.app'; ?>

<?php ob_start(); $this->currentSection = 'content'; ?>
    <div class="row">
        <div class="col-md-12">
            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Lista de Roles</h1>

            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            ?>

            <nav class="navbar navbar-expand navbar-light bg-light mb-4">
                <div class="container-fluid d-flex justify-content-between align-items-center w-100">

                    <!-- Contenedor para los botones (Alineados a la izquierda) -->
                    <div class="d-flex align-items-center">
                        <?php // if (tiene_permiso('crear-perfil')): ?>
                        <!-- Se cambió me-3 por mr-3 (Bootstrap 4) y se quitó el mb-3 para alinearlos bien -->
                        <a href="<?= RUTA_URL ?>/roles/create" class="btn btn-primary btn-sm mr-1"><i
                                class="fa-solid fa-user-gear"></i> Nuevo Rol</a>
                        <a href="<?= RUTA_URL ?>/roles/wastebasket" class="btn btn-danger btn-sm"><i
                                class="fa-solid fa-trash"></i> Papelera</a>
                        <?php // endif; ?>
                    </div>

                    <!-- Formulario de búsqueda (Alineado a la derecha) -->
                    <form action="<?= RUTA_URL ?>/roles" class="form-inline" role="search">
                        <!-- Se cambió me-2 por mr-2 (Bootstrap 4) -->
                        <input class="form-control form-control-sm mr-2" type="search" name="search"
                            value="<?php echo htmlspecialchars((string)($search), ENT_QUOTES, "UTF-8"); ?>" placeholder="Rol a buscar..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
                    </form>

                </div>
            </nav>

            <?php if(count($roles) > 0): ?>
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre del Rol</th>
                                <th class="text-center">Permisos</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $contador = $roles['from'] - 1;
                             ?>
                            <?php foreach($roles['data'] as $role): ?>
                                <?php 
                                    $contador++;
                                 ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)($contador), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td><?php echo htmlspecialchars((string)($role['pe_nombre']), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/roles/<?php echo htmlspecialchars((string)($role['id_perfil']), ENT_QUOTES, "UTF-8"); ?>/permissions"
                                            class="btn btn-sm btn-primary" title="Permisos">
                                            <i class="fa-solid fa-user-shield"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/roles/<?php echo htmlspecialchars((string)($role['id_perfil']), ENT_QUOTES, "UTF-8"); ?>/edit"
                                                type="button" class="btn btn-success btn-sm" title="Editar Perfil"><i
                                                    class="fa-solid fa-pencil"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarEliminacion(<?php echo htmlspecialchars((string)($role['id_perfil']), ENT_QUOTES, "UTF-8"); ?>)"
                                                title="Eliminar Perfil">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php echo $this->renderView('includes.pagination', get_defined_vars()); ?>
            <?php else: ?>
                <div class="text-center">
                    Aún no se han registrado Perfiles.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function confirmarEliminacion(idPerfil) {
            // 1. Mostrar alerta de confirmación previa al borrado
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El perfil será enviado a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // 2. Si el usuario confirma, enviamos la petición vía Fetch (AJAX)
                if (result.isConfirmed) {
                    // Reemplaza esta URL por la ruta real que apunte a tu método destroy
                    fetch(`${base_url}/roles/${idPerfil}/delete`, {
                            method: 'POST', // O 'DELETE' según manejes tus rutas en PHP puro
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 3. Alerta de éxito total
                                Swal.fire(
                                    '¡Eliminado!',
                                    data.message,
                                    'success'
                                ).then(() => {
                                    // Recargamos la página o removemos la fila de la tabla dinámicamente
                                    location.reload();
                                });
                            } else {
                                // Alerta en caso de error lógico
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            // Alerta en caso de error de red
                            Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                        });
                }
            });
        }
    </script>
<?php $this->sections[$this->currentSection] = ob_get_clean(); ?>