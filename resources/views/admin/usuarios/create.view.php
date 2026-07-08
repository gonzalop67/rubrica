@extends('layout.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card m-t-10">
                <div class="card-header d-flex justify-content-between">
                    <h5><strong>Nuevo Usuario</strong></h5>
                    <div>
                        <a href="{{ RUTA_URL }}/usuarios">Volver al Listado de Usuarios</a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formulario" action="" enctype="multipart/form-data" method="post">
                        <div class="row mb-2">
                            <label for="tipo_documento" class="col-sm-2 col-form-label">DNI:</label>
                            <div class="col-sm-4">
                                <select name="tipo_documento" id="tipo_documento" class="form-control">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    @foreach ($tipos_documentos as $tipo_documento)
                                    <option value="{{ $tipo_documento['id_tipo_documento'] }}">{{ $tipo_documento['td_nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="dni" id="dni" value="" placeholder="DNI e.g. 1712345678" required autofocus>
                                <div id="error-dni" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                            <div class="col-sm-10">
                                <select name="nacionalidad" id="nacionalidad" class="form-control">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad['id_def_nacionalidad'] }}">{{ $nacionalidad['dn_nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="titulo" class="col-sm-2 col-form-label">Título (Abrev.):</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="titulo" id="titulo" value="" placeholder="Abreviatura del Título e.g. Lic." required>
                                <div id="error-titulo" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="descripcion_titulo" class="col-sm-2 col-form-label">Descripción del Título:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="descripcion_titulo" id="descripcion_titulo" rows="2" placeholder="Descripción del Título e.g. Licenciado en Ciencias de la Educación"></textarea>
                                <div id="error-descripcion_titulo" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="primer_apellido" class="col-sm-2 col-form-label">Primer Apellido:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="primer_apellido" id="primer_apellido" value="" required>
                                <div id="error-primer_apellido" class="invalid-feedback" style="display:none;"></div>
                            </div>
                            <label for="segundo_apellido" class="col-sm-2 col-form-label">Segundo Apellido:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="segundo_apellido" id="segundo_apellido" value="" required>
                                <div id="error-segundo_apellido" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="primer_nombre" class="col-sm-2 col-form-label">Primer Nombre:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="primer_nombre" id="primer_nombre" value="" required>
                                <div id="error-primer_nombre" class="invalid-feedback" style="display:none;"></div>
                            </div>
                            <label for="segundo_nombre" class="col-sm-2 col-form-label">Segundo Nombre:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="segundo_nombre" id="segundo_nombre" value="" required>
                                <div id="error-segundo_nombre" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="nombre_corto" class="col-sm-2 col-form-label">Nombre Corto:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nombre_corto" id="nombre_corto" value="" required>
                                <div id="error-nombre_corto" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="nombre_completo" class="col-sm-2 col-form-label">Nombre Completo:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="nombre_completo" id="nombre_completo" value="" disabled>
                                <div id="error-nombre_completo" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="username" class="col-sm-2 control-label">Nombre de Usuario:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="username" id="username" value="" required>
                                <div id="error-username" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="email" class="col-sm-2 control-label">Email:</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" value="" required>
                                <div id="error-email" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="password" class="col-sm-2 control-label">Contraseña:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="password" id="password" value="" required>
                                <div id="error-password" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="genero" class="col-sm-2 control-label">Género:</label>
                            <div class="col-sm-10">
                                <select name="genero" id="genero" class="form-control">
                                    <option value="Femenino">Femenino</option>
                                    <option value="Masculino">Masculino</option>
                                </select>
                                <div id="error-genero" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="activo" class="col-sm-2 control-label">Activo:</label>
                            <div class="col-sm-10">
                                <select name="activo" id="activo" class="form-control">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                                <div id="error-activo" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div id="roles-container" class="row mb-2">
                            <label for="roles" class="col-sm-2 control-label">Rol:</label>
                            <div class="col-sm-10">
                                @foreach ($roles as $role)
                                <div>
                                    <input type="checkbox" name="roles[]" value="{{ $role['id_perfil'] }}">
                                    {{ $role['pe_nombre'] }}
                                </div>
                                @endforeach

                                <!-- Bloque donde se inyectará el mensaje de error de JS -->
                                <div id="error-roles" class="text-danger mt-1" style="display: none; font-size: 0.875em;"></div>
                            </div>

                        </div>
                        <div class="row mb-2">
                            <label for="us_avatar" class="col-sm-2 control-label"></label>

                            <div id="img_div" class="col-sm-10">
                                <img id="us_avatar" src="{{ RUTA_URL }}/public/img/vecteezy_blue-profile-icon_36885313.png" name="us_avatar" class="img-thumbnail" width="75" alt="Avatar del usuario">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="avatar" class="col-sm-2 control-label" style="margin-top: -4px;">Imagen:</label>

                            <div class="col-sm-10">
                                <input type="file" name="avatar" id="avatar">
                                <div id="error-avatar" class="invalid-feedback" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-10">
                                <button id="btn-save" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ RUTA_URL }}/public/assets/js/pages/admin/usuarios/crear.js"></script>
@endsection