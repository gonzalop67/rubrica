<?php
// 1. El rol que estamos editando
$perfil_id = isset($_GET['perfil_id']) ? intval($_GET['perfil_id']) : 0;

// El nombre del rol que estamos editando
$sql = "SELECT * FROM sw_perfil WHERE id_perfil = $perfil_id";
$result = $db->consulta($sql);
$nom_perfil = $db->fetch_object($result)->pe_nombre;

// 2. TODOS los permisos que existen en el sistema (para los checkboxes)
$sql = "SELECT * FROM sw_permiso ORDER BY nombre";
$permisos = $db->consulta($sql);

// 3. CORRECCIÓN: Ejecutar y llenar el array con los IDs de los permisos ya asignados
$sql_asignados = "SELECT id_permiso FROM sw_perfil_permiso WHERE id_perfil = $perfil_id";
$res_asignados = $db->consulta($sql_asignados);
$rolePermisos = [];

if ($res_asignados) {
    while ($asignado = $db->fetch_object($res_asignados)) {
        $rolePermisos[] = $asignado->id_permiso; // Guardamos solo el ID en el array plano
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="min-height: 100vh;">
    <!-- Content Header (Page header) -->
    <section class="content-header clearfix">
        <h1 class="pull-left">
            Asignar Permisos al Perfil: <?= htmlspecialchars($nom_perfil) ?>
        </h1>
        <!-- NUEVO: Botón para retornar al listado -->
        <a href="javascript:history.back()" class="btn btn-default btn-sm pull-right" style="margin-top: 5px;">
            <i class="fa fa-arrow-left"></i> Volver Atrás
        </a>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="perfiles/asignar_permisos_roles.php" method="POST" autocomplete="off">

                            <input type="hidden" name="perfil_id" value="<?= $perfil_id ?>">

                            <!-- Seleccionar Todos -->
                            <div class="form-group margin-bottom">
                                <div class="checkbox">
                                    <label for="select_all" style="font-weight: bold;">
                                        <input type="checkbox" id="select_all"> Seleccionar Todos
                                    </label>
                                </div>
                            </div>

                            <!-- Listado de Permisos -->
                            <div class="row">
                                <?php while ($permiso = $db->fetch_object($permisos)): ?>
                                    <div class="col-md-4" style="margin-bottom: 10px;">
                                        <div class="checkbox">
                                            <label for="permiso_<?= $permiso->id_permiso ?>">
                                                <input type="checkbox" name="permisos[]" value="<?= $permiso->id_permiso ?>" id="permiso_<?= $permiso->id_permiso ?>" <?= in_array($permiso->id_permiso, $rolePermisos) ? 'checked' : '' ?>>
                                                <?= htmlspecialchars($permiso->nombre) ?>
                                                <span class="text-muted">(<?= htmlspecialchars($permiso->slug) ?>)</span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <!-- Botón de Guardado -->
                            <div class="form-group" style="margin-top: 15px;">
                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // ---- Lógica de Checkboxes existente ----
        $('#select_all').on('change', function() {
            $("input[name='permisos[]']").prop('checked', this.checked);
        });

        $(document).on('change', "input[name='permisos[]']", function() {
            var total = $("input[name='permisos[]']").length;
            var marcados = $("input[name='permisos[]']:checked").length;
            $('#select_all').prop('checked', total === marcados);
        });

        // ---- NUEVO: Capturar el Submit y enviar por AJAX ----
        $('form').on('submit', function(e) {
            // 1. Evitar que la página se recargue de forma tradicional
            e.preventDefault();

            // Obtener el botón para deshabilitarlo y dar feedback visual
            var $btn = $(this).find('button[type="submit"]');
            var textoOriginal = $btn.text();
            $btn.prop('disabled', true).html('<i class="fa fa-refresh fa-spin"></i> Guardando...');

            // Remover alertas previas si existen
            $('.alert-ajax').remove();

            // 2. Realizar la petición AJAX
            $.ajax({
                url: $(this).attr('action'), // Lee 'perfiles/asignar_permisos_roles.php'
                type: $(this).attr('method'), // Lee 'POST'
                data: $(this).serialize(), // Empaqueta automáticamente perfil_id y permisos[]
                dataType: 'json', // Esperamos una respuesta en formato JSON desde PHP
                success: function(response) {
                    // Si el backend responde que todo salió bien
                    if (response.status === 'success') {
                        var alerta = '<div class="alert alert-success alert-dismissible alert-ajax">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                            '<h4><i class="icon fa fa-check"></i> ¡Éxito!</h4>' +
                            (response.message || 'Los permisos se actualizaron correctamente.') +
                            '</div>';

                        // Colocar la alerta arriba del formulario
                        $('form').before(alerta);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Manejo de errores de conexión, 404, 500, etc.
                    console.error(xhr.responseText);
                    alert('Ocurrió un error inesperado al procesar los datos.');
                },
                complete: function() {
                    // 3. Reactivar el botón al finalizar la operación (con éxito o error)
                    $btn.prop('disabled', false).text(textoOriginal);
                }
            });
        });
    });
</script>