@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Permisos Eliminados (Papelera)</h1>

            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            ?>

            <nav class="navbar navbar-expand navbar-light bg-light mb-4">
                <div class="container-fluid d-flex justify-content-between align-items-center w-100">

                    <!-- Contenedor para los botones (Modificado: Botón para volver a la lista principal) -->
                    <div class="d-flex align-items-center">
                        <a href="<?= RUTA_URL ?>/permissions" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i>
                            Volver a Permisos</a>
                    </div>

                    <!-- Formulario de búsqueda (Modificado: Apunta a la ruta de la papelera) -->
                    <form action="<?= RUTA_URL ?>/permissions/wastebasket" class="form-inline" permiso="search">
                        <input class="form-control form-control-sm mr-2" type="search" name="search"
                            value="{{ $search }}" placeholder="Buscar en papelera..." aria-label="Search">
                        <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
                    </form>

                </div>
            </nav>

            @if (count($permisos) > 0)
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre del Permiso</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = $permisos['from'] - 1;
                            @endphp
                            @foreach ($permisos['data'] as $permiso)
                                @php
                                    $contador++;
                                @endphp
                                <tr>
                                    <td>{{ $contador }}</td>
                                    <td>{{ $permiso['nombre'] }}</td>
                                    <!-- Acciones de la Papelera: Restaurar y Eliminar Definitivamente -->
                                    <td class="text-center">
                                        <div class="btn-group" permiso="group" aria-label="Acciones Papelera">
                                            <!-- Botón Restaurar -->
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="confirmarRestauracion({{ $permiso['id_permiso'] }})"
                                                title="Restaurar Permiso">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                            <!-- Botón Eliminar Permanentemente -->
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarEliminacionDefinitiva({{ $permiso['id_permiso'] }})"
                                                title="Eliminar Permanentemente">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('includes.pagination')
            @else
                <div class="text-center">
                    La papelera está vacía.
                </div>
            @endif
        </div>
    </div>
    <!-- Scripts de Confirmación con SweetAlert2 -->
    <script>
        // 1. Función para Restaurar el Permiso
        function confirmarRestauracion(idPermiso) {
            Swal.fire({
                title: '¿Restaurar permiso?',
                text: "El permiso volverá a estar activo en la lista principal.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/permissions/${idPermiso}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Restaurado!', 'El permiso ha sido activado.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', 'No se pudo restaurar el permiso.', 'error');
                            }
                        });
                }
            });
        }

        // 2. Función para Eliminar Permanentemente de la Base de Datos
        function confirmarEliminacionDefinitiva(idPermiso) {
            Swal.fire({
                title: '¿Eliminar permanentemente?',
                text: "Esta acción no se puede deshacer de ninguna manera.",
                icon: 'warning', // 'danger' no es un icono válido en SweetAlert2, se usa 'warning' o 'error'
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar definitivamente',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${base_url}/permissions/${idPermiso}/destroy`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Eliminado!', 'El perfil ha sido borrado para siempre.', 'success')
                                    .then(() => location.reload());
                            } else {
                                // Muestra el mensaje específico enviado desde el controlador (Restricción de integridad)
                                Swal.fire({
                                    title: 'No se puede eliminar',
                                    text: data.mensaje,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Ocurrió un fallo en la comunicación con el servidor.', 'error');
                        });
                }
            });
        }
    </script>
@endsection
