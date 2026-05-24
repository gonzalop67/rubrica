<style>
    /* Estilos CSS para el badge */
    .badge {
        padding: 5px 10px;
        border-radius: 10px;
        font-size: 12px;
        color: white;
    }

    .badge-active {
        background-color: #28a745;
    }

    .badge-inactive {
        background-color: #dc3545;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Usuarios Activos
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <!-- Default box -->
            <div class="box-header with-border">
                <a id="new_user" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#nuevoUsuarioModal"><i class="fa fa-plus-circle"></i> Nuevo Usuario</a>
                <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=usuarios/view_usuarios_eliminados.php&nivel=0" id="trashed_users" class="btn btn-danger btn-sm" data-toggle="modal" data-targer="#trashedUsersModal"><i class="fa fa-fw fa-trash"></i> Usuarios (Papelera)</a>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div id="mensajes"></div>
                        <table id="t_usuarios" class="table table-striped table-hover">
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

<?php require_once "modalNuevoUsuario.php" ?>
<?php require_once "modalEditarUsuario.php" ?>

<script>
    let table;

    $(document).ready(function() {
        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });

        // Primero recupero todos los usuarios de la base de datos
        listarUsuarios();
        cargar_perfiles();

        $("#form_insert")[0].reset();

        $("#new_us_foto").change(function() {
            $("#new_img_div").removeClass("hide");
            filePreview(this);
        });

        $("#edit_us_foto").change(function() {
            $("#new_img_div").removeClass("hide");
            filePreview(this);
        });

        $("#new_us_titulo").blur(function() {
            let us_titulo = $(this).val();
            let us_apellidos = $("#new_us_apellidos").val();
            let us_nombres = $("#new_us_nombres").val();

            let vec_us_apellidos = us_apellidos.split(" ");
            let vec_us_nombres = us_nombres.split(" ");
            $("#new_us_shortname").val(us_titulo + " " + vec_us_nombres[0] + " " + vec_us_apellidos[0]);
        });

        $("#new_us_apellidos").blur(function() {
            let us_titulo = $("#new_us_titulo").val();
            let us_apellidos = $(this).val();
            let us_nombres = $("#new_us_nombres").val();

            let vec_us_apellidos = us_apellidos.split(" ");
            let vec_us_nombres = us_nombres.split(" ");
            $("#new_us_shortname").val(us_titulo + " " + vec_us_nombres[0] + " " + vec_us_apellidos[0]);
        });

        $("#new_us_nombres").blur(function() {
            let us_titulo = $("#new_us_titulo").val();
            let us_apellidos = $("#new_us_apellidos").val();
            let us_nombres = $(this).val();

            let vec_us_apellidos = us_apellidos.split(" ");
            let vec_us_nombres = us_nombres.split(" ");
            $("#new_us_shortname").val(us_titulo + " " + vec_us_nombres[0] + " " + vec_us_apellidos[0]);
        });
    });

    function listarUsuarios() {
        table = $('#t_usuarios').DataTable({
            "processing": true, // Habilita el mensaje de carga
            "serverSide": true, // Procesamiento en el servidor
            "language": {
                "processing": "Consultando servidor, por favor espere...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "search": "Buscar:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "ajax": function(data, callback, settings) {
                // Usamos Fetch para realizar la petición
                fetch('usuarios/cargar_usuarios.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(json => {
                        callback(json); // Retornamos los datos a DataTables
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
            "columns": [{
                    "data": "id_usuario"
                },
                {
                    "data": "us_foto",
                    "render": function(data, type, row) {
                        // Crea la etiqueta img con la URL recibida
                        return '<img src="public/uploads/' + data + '" style="width:50px; height:50px; object-fit:cover;" alt="Foto del usuario" />';
                    }
                },
                {
                    "data": "nombre"
                },
                {
                    "data": "us_login"
                },
                {
                    "data": "us_activo",
                    "render": function(data, type, row) {
                        // Lógica para el badge
                        if (data == 1) {
                            return '<span class="badge badge-active">Activo</span>';
                        } else {
                            return '<span class="badge badge-inactive">Inactivo</span>';
                        }
                    }
                },
                {
                    "data": "perfiles"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class='btn btn-warning btn-sm' title='Editar' onclick="obtenerDatos(${row.id_usuario})" data-toggle='modal' data-target='#editarUsuarioModal'><i class="fa fa-fw fa-pencil"></i> Editar</button>
                            <button class='btn btn-danger btn-sm' onclick="eliminarUsuario(${row.id_usuario})"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                        `;
                    }
                }
            ]
        });
    }

    function cargar_perfiles() {
        $.ajax({
            type: "get",
            url: "usuarios/cargar_perfiles.php",
            dataType: "html",
            success: function(response) {
                $("#new_id_perfil").append(response);
                $("#edit_id_perfil").append(response);
            }
        });
    }

    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                $("#new_us_avatar").attr("src", e.target.result);
                $("#edit_us_avatar").attr("src", e.target.result);
            }
        }
    }

    $("#form_insert").submit(function(e) {

        e.preventDefault();

        const us_titulo = $("#new_us_titulo").val().trim();
        const us_titulo_descripcion = $("#new_us_titulo_descripcion").val().trim();
        const us_apellidos = $("#new_us_apellidos").val().trim();
        const us_nombres = $("#new_us_nombres").val().trim();
        const us_shortname = $("#new_us_shortname").val().trim();
        const us_login = $("#new_us_login").val().trim();
        const us_password = $("#new_us_password").val().trim();
        const us_email = $("#new_us_email").val().trim();

        const reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;

        let cont_errores = 0;

        if ($("#new_id_perfil").val().length == 0) {
            $("#mensaje10").html("Debe seleccionar al menos un perfil.");
            $("#mensaje10").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje10").fadeOut();
        }

        if (us_titulo === '') {
            $("#error-new_us_titulo").html("Debe ingresar la abreviatura del Título.");
            $("#error-new_us_titulo").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_titulo").fadeOut();
        }

        if (us_titulo_descripcion === '') {
            $("#error-new_us_titulo_descripcion").html("Debe ingresar la descripción del Título.");
            $("#error-new_us_titulo_descripcion").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_titulo_descripcion").fadeOut();
        }

        if (us_apellidos === '') {
            $("#error-new_us_apellidos").html("Debe ingresar los Apellidos del Usuario.");
            $("#error-new_us_apellidos").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_apellidos").fadeOut();
        }

        if (us_nombres === '') {
            $("#error-new_us_nombres").html("Debe ingresar los Nombres del Usuario.");
            $("#error-new_us_nombres").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_nombres").fadeOut();
        }

        if (us_shortname === '') {
            $("#error-new_us_shortname").html("Debe ingresar el nombre corto del Usuario.");
            $("#error-new_us_shortname").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_shortname").fadeOut();
        }

        if (us_login === '') {
            $("#error-new_us_login").html("Debe ingresar el nombre de usuario.");
            $("#error-new_us_login").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_login").fadeOut();
        }

        if (us_password === '') {
            $("#error-new_us_password").html("Debe ingresar el password del usuario.");
            $("#error-new_us_password").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_password").fadeOut();
        }

        if ($("#new_us_genero").val() === '') {
            $("#error-new_us_genero").html("Debe seleccionar el género del usuario.");
            $("#error-new_us_genero").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_genero").fadeOut();
        }

        if ($("#new_us_activo").val() === '') {
            $("#error-new_us_activo").html("Debe especificar si está activo el usuario.");
            $("#error-new_us_activo").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_activo").fadeOut();
        }

        if (us_email.length != 0 && !reg_email.test(us_email)) {
            $("#error-new_us_email").html("Dirección de correo electrónico no válida.");
            $("#error-new_us_email").fadeIn();
            cont_errores++;
        } else {
            $("#error-new_us_email").fadeOut();
        }

        var img = document.forms['form_insert']['new_us_foto'];
        var validExt = ["jpeg", "png", "jpg", "JPEG", "JPG", "PNG"];

        if (img.value != '') {
            var img_ext = img.value.substring(img.value.lastIndexOf('.') + 1);
            var result = validExt.includes(img_ext);

            if (result == false) {
                $("#error-new_us_foto").html("Debe cargar un archivo de imagen.");
                $("#error-new_us_foto").fadeIn();
                cont_errores++;
            } else {
                $("#error-new_us_foto").fadeOut();
            }

            var CurrentFileSize = parseFloat(img.files[0].size / (1024 * 1024));

            if (CurrentFileSize >= 1) {
                $("#error-new_us_foto").html("El archivo de imagen debe tener un tamaño máximo de 1 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.");
                $("#error-new_us_foto").fadeIn();
                cont_errores++;
            } else {
                $("#error-new_us_foto").fadeOut();
            }
        } else {
            $("#error-new_us_foto").html("Debe cargar un archivo de imagen.");
            $("#error-new_us_foto").fadeIn();
            cont_errores++;
        }

        if (cont_errores == 0) {
            // submit el formulario
            var data = new FormData($("#form_insert")[0]);

            $.ajax({
                url: "usuarios/insertar_usuario.php",
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    table.destroy();
                    listarUsuarios();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset();
                    $('#nuevoUsuarioModal').modal('hide');
                }
            });
        }
    });

    $("#form_update").submit(function(e) {

        e.preventDefault();

        const id_usuario = $("#edit_id_usuario").val();
        const us_titulo = $("#edit_us_titulo").val().trim();
        const us_titulo_descripcion = $("#edit_us_titulo_descripcion").val().trim();
        const us_apellidos = $("#edit_us_apellidos").val().trim();
        const us_nombres = $("#edit_us_nombres").val().trim();
        const us_shortname = $("#edit_us_shortname").val().trim();
        const us_email = $("#edit_us_email").val().trim();
        const us_login = $("#edit_us_login").val().trim();
        const us_password = $("#edit_us_password").val().trim();

        const reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;

        let cont_errores = 0;

        if ($("#edit_id_perfil").val().length == 0) {
            $("#error-edit_id_perfil").html("Debe seleccionar al menos un perfil.");
            $("#error-edit_id_perfil").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_id_perfil").fadeOut();
        }

        if (us_titulo === '') {
            $("#error-edit_us_titulo").html("Debe ingresar la abreviatura del Título.");
            $("#error-edit_us_titulo").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_titulo").fadeOut();
        }

        if (us_titulo_descripcion === '') {
            $("#error-edit_us_titulo_descripcion").html("Debe ingresar la descripción del Título.");
            $("#error-edit_us_titulo_descripcion").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_titulo_descripcion").fadeOut();
        }

        if (us_apellidos === '') {
            $("#error-edit_us_apellidos").html("Debe ingresar los Apellidos del Usuario.");
            $("#error-edit_us_apellidos").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_apellidos").fadeOut();
        }

        if (us_nombres === '') {
            $("#error-edit_us_nombres").html("Debe ingresar los Nombres del Usuario.");
            $("#error-edit_us_nombres").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_nombres").fadeOut();
        }

        if (us_shortname === '') {
            $("#error-edit_us_shortname").html("Debe ingresar el nombre corto del Usuario.");
            $("#error-edit_us_shortname").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_shortname").fadeOut();
        }

        if (us_email !== '' && !reg_email.test(us_email)) {
            $("#error-edit_us_email").html("El email ingresado no es válido.");
            $("#error-edit_us_email").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_email").fadeOut();
        }

        if (us_password === '') {
            $("#error-edit_us_password").html("Debe ingresar el password del usuario.");
            $("#error-edit_us_password").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_password").fadeOut();
        }

        if ($("#edit_us_genero").val() === '') {
            $("#error-edit_us_genero").html("Debe seleccionar el género del usuario.");
            $("#error-edit_us_genero").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_genero").fadeOut();
        }

        if ($("#edit_us_activo").val() === '') {
            $("#error-edit_us_activo").html("Debe especificar si está activo el usuario.");
            $("#error-edit_us_activo").fadeIn();
            cont_errores++;
        } else {
            $("#error-edit_us_activo").fadeOut();
        }

        var img = document.forms['form_insert']['new_us_foto'];
        var validExt = ["jpeg", "png", "jpg", "JPEG", "JPG", "PNG"];

        if (img.value != '') {
            var img_ext = img.value.substring(img.value.lastIndexOf('.') + 1);
            var result = validExt.includes(img_ext);

            if (result == false) {
                $("#error-edit_us_foto").html("Debe cargar un archivo de imagen.");
                $("#error-edit_us_foto").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_foto").fadeOut();
            }

            var CurrentFileSize = parseFloat(img.files[0].size / (1024 * 1024));

            if (CurrentFileSize >= 1) {
                $("#error-edit_us_foto").html("El archivo de imagen debe tener un tamaño máximo de 1 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.");
                $("#error-edit_us_foto").fadeIn();
                cont_errores++;
            } else {
                $("#error-edit_us_foto").fadeOut();
            }
        }

        if (cont_errores == 0) {
            // submit el formulario
            var data = new FormData($("#form_update")[0]);

            $.ajax({
                url: "usuarios/actualizar_usuario.php",
                method: 'POST',
                data: data,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    table.destroy();
                    listarUsuarios();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset();
                    $('#editarUsuarioModal').modal('hide');
                }
            });
        }
    });

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "usuarios/obtener_usuario.php",
            type: "POST",
            data: "id_usuario=" + id,
            dataType: "json",
            success: function(r) {
                console.log(r);
                $("#edit_id_usuario").val(r.id_usuario);
                $("#edit_us_titulo").val(r.us_titulo);
                $("#edit_us_titulo_descripcion").val(r.us_titulo_descripcion);
                $("#edit_us_apellidos").val(r.us_apellidos);
                $("#edit_us_nombres").val(r.us_nombres);
                $("#edit_us_shortname").val(r.us_shortname);
                $("#edit_us_email").val(r.us_email);
                $("#edit_us_login").val(r.us_login);
                $("#edit_us_password").val(r.us_password);
                setearIndice('edit_us_genero', r.us_genero);
                setearIndice('edit_us_activo', r.us_activo);
                // edit_us_avatar
                $('#edit_us_avatar').attr('src', 'public/uploads/' + r.us_foto);
                $("#bd_us_avatar").val(r.us_foto);
                // document.getElementById("edit_us_avatar").src= 'public/uploads/' + r.us_foto;
            }
        });

        // Recuperamos los perfiles del usuario
        $.ajax({
            type: "POST",
            url: "usuarios/obtener_perfiles_usuario.php",
            data: "id_usuario=" + id,
            dataType: "json",
            success: function(r) {
                edit_id_perfil = document.getElementById("edit_id_perfil");
                // Limpiar los perfiles seleccionados anteriormente
                for (let i = 0; i < edit_id_perfil.length; i++) {
                    edit_id_perfil.options[i].selected = '';
                }
                // Recuperar los perfiles desde la base de datos
                for (let i = 0; i < r.length; i++) {
                    const id_perfil_usuario = r[i];
                    for (let j = 0; j < edit_id_perfil.length; j++) {
                        const edit_perfil_usuario = edit_id_perfil[j];
                        if (edit_perfil_usuario.value === id_perfil_usuario) {
                            edit_id_perfil.options[j].selected = 'selected';
                        }
                    }
                }
            }
        });
    }

    function eliminarUsuario(id_usuario) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: "El usuario será enviado a la papelera de reciclaje.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, elimínelo!",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "usuarios/eliminar_usuario.php",
                    data: {
                        id_usuario: id_usuario
                    },
                    dataType: "json",
                    success: function(data) {
                        // OPTIMIZACIÓN: En lugar de destruir y recrear la tabla completa con table.destroy(),
                        // recargamos los datos del servidor manteniendo la paginación actual.
                        table.ajax.reload(null, false);

                        // table.destroy();
                        // listarUsuarios();

                        // SOFT DELETE: Mapeamos los campos 'success' y 'message' que retorna tu controlador
                        Swal.fire({
                            title: data.success ? '¡Completado!' : 'Atención',
                            text: data.message,
                            icon: data.success ? 'success' : 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>