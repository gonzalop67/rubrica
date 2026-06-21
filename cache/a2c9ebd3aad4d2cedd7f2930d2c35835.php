<?php $this->layout = 'layout.app'; ?>

<?php ob_start(); $this->currentSection = 'content'; ?>
    <div class="row">
        <div class="col-md-12">
            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Lista de Permisos</h1>

            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            ?>

            <nav class="navbar navbar-expand navbar-light bg-light mb-4">
                <div class="container-fluid d-flex justify-content-between align-items-center w-100">

                    <!-- Contenedor para los botones (Alineados a la izquierda) -->
                    <div class="d-flex align-items-center">

                        <!-- Se cambió me-3 por mr-3 (Bootstrap 4) y se quitó el mb-3 para alinearlos bien -->
                        <a href="<?= RUTA_URL ?>/permisos/create" class="btn btn-primary btn-sm mr-1"><i
                                class="fa-solid fa-user-gear"></i> Nuevo Permiso</a>

                    </div>

                    <!-- Formulario de búsqueda (Alineado a la derecha) -->
                    <form action="<?= RUTA_URL ?>/permisos" class="form-inline" role="search">
                        <!-- Se cambió me-2 por mr-2 (Bootstrap 4) -->
                        <input class="form-control form-control-sm mr-2" type="search" name="search"
                            value="<?php echo htmlspecialchars((string)($search), ENT_QUOTES, "UTF-8"); ?>" placeholder="Permiso a buscar..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
                    </form>

                </div>
            </nav>

            <?php if(count($permisos) > 0): ?>
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Descripción</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $contador = $permisos['from'] - 1;
                             ?>
                            <?php foreach($permisos['data'] as $permiso): ?>
                                <?php 
                                    $contador++;
                                 ?>
                                <tr>
                                    <td><?php echo htmlspecialchars((string)($contador), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td><?php echo htmlspecialchars((string)($permiso['nombre']), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td><?php echo htmlspecialchars((string)($permiso['slug']), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td><?php echo htmlspecialchars((string)($permiso['descripcion']), ENT_QUOTES, "UTF-8"); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/permisos/<?php echo htmlspecialchars((string)($permiso['id_permiso']), ENT_QUOTES, "UTF-8"); ?>/edit"
                                                type="button" class="btn btn-success btn-sm" title="Editar Permiso"><i
                                                    class="fa-solid fa-pencil"></i></a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarEliminacion(<?php echo htmlspecialchars((string)($permiso['id_permiso']), ENT_QUOTES, "UTF-8"); ?>)"
                                                title="Eliminar Permiso">
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
                    Aún no se han registrado Permisos.
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->sections[$this->currentSection] = ob_get_clean(); ?>
