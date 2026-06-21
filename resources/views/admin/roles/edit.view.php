@extends('layout.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5><strong>Editar Perfil: {{ $role['pe_nombre'] }}</strong></h5>
                    <div>
                        <a href="{{ RUTA_URL }}/roles" class="btn-volver">Volver al Listado de Perfiles</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formulario" action="" method="post">
                        <input type="text" id="id_perfil" value="{{ $role['id_perfil'] }}" hidden>
                        <div class="row mb-2">
                            <label for="nombre" class="col-sm-2 col-form-label">Nombre:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $role['pe_nombre'] }}" placeholder="Nombre del Perfil e.g. Estudiante" required autofocus>
                                <!-- CORREGIDO: Se eliminó el style="display:none;" manual -->
                                <div id="error-nombre" class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label for="slug" class="col-sm-2 col-form-label">Slug:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="slug" id="slug" value="{{ $role['pe_slug'] }}" placeholder="Slug del Perfil e.g. estudiante" required autofocus>
                                <!-- CORREGIDO: Se eliminó el style="display:none;" manual -->
                                <div id="error-slug" class="invalid-feedback"></div>
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
<script src="{{ RUTA_URL }}/public/assets/js/pages/admin/perfiles/crear.js"></script>
@endsection