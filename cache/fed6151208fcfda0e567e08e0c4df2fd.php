<?php $this->layout = 'layout.app'; ?>

<?php ob_start(); $this->currentSection = 'content'; ?>
    <div class="row">
        <div class="col-md-12">
            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Lista de Usuarios</h1>

            <?php $search = isset($_GET['search']) ? $_GET['search'] : ''; ?>

            <nav class="navbar navbar-expand navbar-light bg-light mb-4">
                <div class="container-fluid d-flex justify-content-between align-items-center w-100">

                    <!-- Contenedor para los botones (Alineados a la izquierda) -->
                    <div class="d-flex align-items-center">
                        <a href="<?= RUTA_URL ?>/usuarios/create" class="btn btn-primary btn-sm mr-1">
                            <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                        </a>
                        <a href="<?= RUTA_URL ?>/usuarios/wastebasket" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-trash"></i> Papelera
                        </a>
                    </div>

                    <!-- Formulario de búsqueda (Alineado a la derecha) -->
                    <form action="<?= RUTA_URL ?>/usuarios" class="form-inline" role="search">
                        <!-- ✔ CORRECCIÓN: Se cambió <?php echo htmlspecialchars((string)($search), ENT_QUOTES, "UTF-8"); ?> por PHP nativo seguro -->
                        <input class="form-control form-control-sm mr-2" type="search" name="search"
                            value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Usuario a buscar..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
                    </form>

                </div>
            </nav>

            <?php if(count($usuarios['data']) > 0): ?>
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Avatar</th>
                                <th>Nombre de Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th class="text-center">Roles</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $contador = $usuarios['from'] - 1;
                             ?>
                            <?php foreach($usuarios['data'] as $usuario): ?>
                                <?php 
                                    $contador++;
                                 ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)($contador), ENT_QUOTES, "UTF-8"); ?></td>
                                    <?php 
                                        $fotoNombre = !empty($usuario['us_foto']) ? $usuario['us_foto'] : 'no-disponible.png';
                                        $rutaFisica = dirname($_SERVER['SCRIPT_FILENAME']) . '/uploads/' . $fotoNombre;

                                        if (!file_exists($rutaFisica)) {
                                            $fotoNombre = 'no-disponible.png';
                                        }

                                        $avatarUrl = RUTA_URL . '/public/uploads/' . $fotoNombre;
                                     ?>

                                    <td>
                                        <img src="<?php echo htmlspecialchars((string)($avatarUrl), ENT_QUOTES, "UTF-8"); ?>" style="border-radius: 50%" width="45" alt="Avatar">
                                    </td>
                                    <td><?php echo htmlspecialchars((string)($usuario['us_login']), ENT_QUOTES, "UTF-8"); ?></td>

                                    <!-- ✔ VERIFICADO: Extrae de forma dinámica el campo nombre_completo de tu matriz exitosa -->
                                    <td><?php echo htmlspecialchars((string)(!empty($usuario['nombre_completo']) ? $usuario['nombre_completo'] : 'Sin asignar'), ENT_QUOTES, "UTF-8"); ?></td>

                                    <td><?php echo htmlspecialchars((string)($usuario['us_email']), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/usuarios/<?php echo htmlspecialchars((string)($usuario['id_usuario']), ENT_QUOTES, "UTF-8"); ?>/roles" class="btn btn-sm btn-primary" title="Roles">
                                            <i class="fa-solid fa-user-gear"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/usuarios/<?php echo htmlspecialchars((string)($usuario['id_usuario']), ENT_QUOTES, "UTF-8"); ?>/edit" class="btn btn-success btn-sm" title="Editar">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo htmlspecialchars((string)($usuario['id_usuario']), ENT_QUOTES, "UTF-8"); ?>)" title="Eliminar">
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
                    Aún no se han registrado Usuarios o no coinciden con la búsqueda.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function confirmarEliminacion(idUsuario) {
            // 1. Mostrar alerta de confirmación previa al borrado
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El usuario será enviado a la papelera.",
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
                    fetch(`${base_url}/usuarios/${idUsuario}/delete`, {
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