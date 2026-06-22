@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5><strong>Editar Permiso: {{ $permission['nombre'] }}</strong></h5>
                        <div>
                            <a href="{{ RUTA_URL }}/permissions" class="btn-volver">Volver al Listado de Permisos</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formulario" action="" method="post">
                            <input type="text" name="id_permiso" id="id_permiso" value="{{ $permission['id_permiso'] }}" hidden>
                            <div class="row mb-2">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $permission['nombre'] }}"
                                        placeholder="Nombre del Permiso e.g. Ver Usuarios" required autofocus>
                                    <div id="error-nombre" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label for="slug" class="col-sm-2 col-form-label">Slug:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="slug" id="slug" value="{{ $permission['slug'] }}"
                                        placeholder="Slug del Permiso e.g. ver-usuarios" required>
                                    <div id="error-slug" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label for="descripcion" class="col-sm-2 col-form-label">Descripción:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="2" placeholder="Descripción del Permiso e.g. Permite ver el listado de usuarios" required>{{ $permission['descripcion'] }}</textarea>
                                    <div id="error-descripcion" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <button id="btn-submit" type="submit" class="btn btn-success"><i class="fa fa-pencil"></i> Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ RUTA_URL }}/public/assets/js/pages/admin/permisos/crear.js"></script>
@endsection
