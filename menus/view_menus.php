<div class="row">
    <!-- COLUMNA IZQUIERDA: Formulario de Inserción Fijo (Tamaño 4) -->
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-plus-circle"></i> Crear Nuevo Menú</h3>
            </div>
            <div class="box-body">
                <form id="form_insert" action="{{ RUTA_URL }}/menus/store" method="POST">
                    
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

                    <!-- Menú Padre (Carga dinámica) -->
                    <div class="form-group" id="group-padre_id">
                        <label for="padre_id">Menú Padre (Ubicación):</label>
                        <select class="form-control" name="padre_id" id="padre_id">
                            <option value="">-- Ninguno (Es un menú principal) --</option>
                            @foreach ($menus_principales as $padre)
                                <option value="{{ $padre['id'] }}">{{ $padre['nombre'] }}</option>
                            @endforeach
                        </select>
                        <span id="error-padre_id" class="help-block" style="display:none;"></span>
                    </div>

                    <!-- Permiso Requerido / Slug -->
                    <div class="form-group" id="group-permiso_slug">
                        <label for="permiso_slug">Permiso Requerido:</label>
                        <select class="form-control" name="permiso_slug" id="permiso_slug">
                            <option value="">-- Público / Contenedor (Sin Permiso) --</option>
                            @foreach ($permisos_disponibles as $permiso)
                                <option value="{{ $permiso['slug'] }}">{{ $permiso['nombre'] }} ({{ $permiso['slug'] }}) </option>
                            @endforeach
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
                        @foreach ($roles as $role)
                            <option value="{{ $role['id'] }}">{{ $role['nombre'] }}</option>
                        @endforeach
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
