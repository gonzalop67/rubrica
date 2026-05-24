<style>
    /* Estilos CSS consistentes para los estados en la papelera */
    .badge {
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 12px;
        color: white;
    }
    .badge-deleted {
        background-color: #dc3545;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Usuarios Eliminados
            <small>Papelera de Reciclaje</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-danger">
            <div class="box-header with-border">
                <!-- Botón para regresar al listado principal de usuarios activos -->
                <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=usuarios/index.php&nivel=0" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Volver a Usuarios Activos
                </a>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="t_usuarios_eliminados" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Avatar</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Perfiles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
</div>

<script>
    let tableEliminados;

    $(document).ready(function() {
        // Inicializar el DataTables de la Papelera
        listarUsuariosEliminados();
    });

    function listarUsuariosEliminados() {
        tableEliminados = $('#t_usuarios_eliminados').DataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "processing": "Consultando papelera, por favor espere...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No hay usuarios en la papelera de reciclaje",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "search": "Buscar en papelera:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "ajax": function(data, callback, settings) {
                // Apunta al backend encargado de leer SOLO los eliminados (deleted_at IS NOT NULL)
                fetch('usuarios/cargar_usuarios_eliminados.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(json => {
                        callback(json);
                    })
                    .catch(error => {
                        console.error('Error al cargar la papelera:', error);
                    });
            },
            "columns": [
                { "data": "id_usuario" },
                {
                    "data": "us_foto",
                    "render": function(data, type, row) {
                        let foto = data ? data : 'default.png';
                        return '<img src="public/uploads/' + foto + '" style="width:50px; height:50px; object-fit:cover;" class="img-thumbnail" alt="Foto" />';
                    }
                },
                { "data": "nombre" },
                { "data": "us_login" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<span class="badge badge-deleted">Eliminado</span>';
                    }
                },
                { "data": "perfiles" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        // Botones de acción específicos de la papelera
                        return `
                            <button class='btn btn-success btn-sm' title='Restaurar' onclick="restaurarUsuario(${row.id_usuario})">
                                <i class="fa fa-fw fa-refresh"></i> Restaurar
                            </button>
                            <button class='btn btn-danger btn-sm' title='Eliminar Permanente' onclick="destruirUsuarioPermanente(${row.id_usuario})">
                                <i class="fa fa-fw fa-times"></i> Borrado Definitivo
                            </button>
                        `;
                    }
                }
            ]
        });
    }

    // ACCIÓN 1: Restaurar Usuario (Regresarlo a activos)
    function restaurarUsuario(id_usuario) {
        Swal.fire({
            title: "¿Deseas restaurar este usuario?",
            text: "El usuario volverá a aparecer en el listado de miembros activos.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, restaurar",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "usuarios/restaurar_usuario.php", // Tu archivo puente para el método restoreUsuario()
                    data: { id_usuario: id_usuario },
                    dataType: "json",
                    success: function(data) {
                        // Recargar la tabla de la papelera de forma fluida
                        tableEliminados.ajax.reload(null, false);
                        
                        Swal.fire({
                            title: data.titulo,
                            text: data.message,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert("Error al restaurar: " + thrownError);
                    }
                });
            }
        });
    }

    // ACCIÓN 2: Borrado físico y definitivo (Vaciar de la base de datos)
    function destruirUsuarioPermanente(id_usuario) {
        Swal.fire({
            title: "¿ELIMINAR DEFINITIVAMENTE?",
            text: "¡Esta acción es irreversible! Se borrarán sus datos y archivos del servidor.",
            icon: "error",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, borrar para siempre",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "usuarios/eliminar_usuario_permanente.php", // Tu archivo puente para el método destruirUsuarioPermanente()
                    data: { id_usuario: id_usuario },
                    dataType: "json",
                    success: function(data) {
                        tableEliminados.ajax.reload(null, false);
                        
                        Swal.fire({
                            title: data.titulo,
                            text: data.message,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert("Error al destruir: " + thrownError);
                    }
                });
            }
        });
    }
</script>
