<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 id="titulo_principal">
            Usuarios
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 table-responsive">
                        <!-- form start -->
                        <form id="frm-asociar" class="form-horizontal">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="cboPerfiles" class="col-sm-2 control-label">Perfil</label>
                                    <div class="col-sm-10">
                                        <select name="cboPerfiles" id="cboPerfiles" class="form-control">
                                            <option value="">Seleccione un perfil...</option>
                                            <?php foreach ($perfiles as $perfil) : ?>
                                                <option value="<?php echo $perfil->id_perfil ?>">
                                                    <?php echo $perfil->pe_nombre; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="search_user" class="col-sm-2 control-label">Buscar</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="search_user" placeholder="Escriba el nombre del usuario a ser asociado...">
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success pull-right" onclick="asociarUsuarioPerfil();return false;">Asociar</button>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                        <hr>
                        <form id="form-search" class="pull-right" action="usuario/buscar_usuario.php" method="POST">
                            <div class="input-group input-group-md" style="width: 350px;">
                                <input type="text" name="search_pattern" id="search_pattern" class="form-control pull-right text-uppercase" placeholder="Buscar Usuario...">

                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Usuario</th>
                                    <th>Nombre corto</th>
                                    <th>Activo</th>
                                    <th>Imagen</th>
                                    <th>
                                        <!-- Opciones -->
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody_usuarios">
                                <!-- En este lugar se van a poblar los usuarios mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="panel-body fuente9">
                            <!-- Horizontal Form -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 id="titulo" class="box-title">Nuevo Usuario</h3>
                                </div>
                                <!-- /.box-header -->
                                <form id="frm-usuario" class="form-horizontal" enctype="multipart/form-data">
                                    <input type="hidden" name="id_usuario" id="id_usuario" value="0">
                                    <input type="hidden" name="id_perfil_process" id="id_perfil_process">
                                    <!-- form frm-usuario start -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="us_titulo" class="col-sm-2 control-label">Título</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="us_titulo" id="us_titulo" placeholder="Por ejemplo: Ing.">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_apellidos" class="col-sm-2 control-label">Apellidos</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="us_apellidos" id="us_apellidos">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_nombres" class="col-sm-2 control-label">Nombres</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="us_nombres" id="us_nombres">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_login" class="col-sm-2 control-label">Usuario</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="us_login" id="us_login">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_password" class="col-sm-2 control-label">Password</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="us_password" id="us_password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_genero" class="col-sm-2 control-label">Género</label>

                                            <div class="col-sm-10">
                                                <select class="form-control" name="us_genero" id="us_genero">
                                                    <option value="M">Masculino</option>
                                                    <option value="F">Femenino</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="us_activo" class="col-sm-2 control-label">Activo</label>

                                            <div class="col-sm-10">
                                                <select class="form-control" name="us_activo" id="us_activo">
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="img_upload">
                                            <div class="form-group">
                                                <label for="us_avatar" class="col-sm-2 control-label">Avatar</label>

                                                <div id="img_div" class="col-sm-10 hide">
                                                    <img id="us_avatar" name="us_avatar" class="img-thumbnail" width="75">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="us_foto" class="col-sm-2 control-label"></label>

                                                <div class="col-sm-10">
                                                    <input type="file" name="us_foto" id="us_foto">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button id="btn-cancel" type="button" class="btn btn-default">Cancelar</button>
                                        <button id="btn-save" type="submit" class="btn btn-success pull-right">Guardar</button>
                                    </div>
                                    <!-- /.box-footer -->
                                </form>
                                <!-- form frm-usuario end -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
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

        $("#tbody_usuarios").html("<tr><td colspan='6' align='center'>Debe seleccionar un perfil...</td></tr>");

        cargar_perfiles();

        $("#cboPerfiles").change(function() {
            var id_perfil = $(this).val();
            $("#id_perfil_process").val(id_perfil);
            if (id_perfil == 0)
                $("#tbody_usuarios").html("<tr><td colspan='6' align='center'>Debe seleccionar un perfil...</td></tr>");
            else
                showUsuariosPerfil(id_perfil);
            $("#btn-save").html("Guardar");
        });

        $("#search_user").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: 'usuario/obtener_usuarios.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        valor: request.term
                    },
                    success: function(data) {
                        response(data);
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                $("#id_usuario").val(ui.item.id);
            },
        });

        $("#btn-cancel").click(function() {
            $("#frm-usuario")[0].reset();
            $("#titulo").html("Nuevo Usuario");
            $("#btn-save").html("Guardar");
            $("#cboPerfiles").prop("disabled", false);
            $("#img_div").addClass("hide");
            $("#id_usuario").val(0);
        });

        $('#tbody_usuarios').on('click', '.item-des-asociar', function() {
            var id_usuario = $(this).attr('data');
            var id_perfil = $("#cboPerfiles").val();
            $.ajax({
                url: "usuario/desasociar_usuario_perfil.php",
                method: "POST",
                dataType: "json",
                data: {
                    id_usuario: id_usuario,
                    id_perfil: id_perfil
                },
                success: function(response) {
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonTexto: 'Aceptar'
                    });

                    showUsuariosPerfil($("#cboPerfiles").val());

                    $("#search_user").val("");
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });

        $('#tbody_usuarios').on('click', '.item-edit', function() {
            var id_usuario = $(this).attr('data');
            $("#titulo").html("Editar Usuario");
            $("#btn-save").html("Actualizar");
            $("#cboPerfiles").prop("disabled", true);
            $.ajax({
                url: "usuario/obtener_usuario.php",
                data: {
                    id_usuario: id_usuario
                },
                method: "post",
                dataType: "json",
                success: function(data) {
                    $("#id_usuario").val(id_usuario);
                    $("#us_titulo").val(data.us_titulo);
                    $("#us_apellidos").val(data.us_apellidos);
                    $("#us_nombres").val(data.us_nombres);
                    $("#us_login").val(data.us_login);
                    $("#us_password").val(data.us_password);
                    setearIndice('us_genero', data.us_genero);
                    setearIndice('us_activo', data.us_activo);
                    $("#img_div").removeClass("hide");
                    $("#us_avatar").attr("src", "assets/images/" + data.us_foto);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        $("#frm-usuario").submit(function(e) {
            e.preventDefault();
            var url = "";

            if ($("#btn-save").html() == "Actualizar") {
                url = "usuario/actualizar_usuario.php";
            } else {
                url = "usuario/insertar_usuario.php";
            }

            // Validar la entrada del campo id_perfil
            if ($("#cboPerfiles").val().trim() === '') {
                alert("Debe seleccionar el Perfil.");
                $("#cboPerfiles").focus();
                return false;
            }

            // Validar la entrada de los campos input
            if ($("#us_titulo").val().trim() === '') {
                alert("Debe ingresar el valor del campo Título.");
                $("#us_titulo").focus();
                return false;
            } else if ($("#us_apellidos").val().trim() === '') {
                alert("Debe ingresar el valor del campo Apellidos.");
                $("#us_apellidos").focus();
                return false;
            } else if ($("#us_nombres").val().trim() === '') {
                alert("Debe ingresar el valor del campo Nombres.");
                $("#us_nombres").focus();
                return false;
            } else if ($("#us_login").val().trim() === '') {
                alert("Debe ingresar el valor del campo Usuario.");
                $("#us_login").focus();
                return false;
            } else if ($("#us_password").val().trim() === '') {
                alert("Debe ingresar el valor del campo Password.");
                $("#us_password").focus();
                return false;
            }

            var extension = $('#us_foto').val().split('.').pop().toLowerCase();

            if (extension != '') {
                if (jQuery.inArray(extension, ['png', 'jpg', 'jpeg']) == -1) {
                    alert("La extensión del archivo de imagen debe ser .png o .jpg o .jpeg");
                    $('#user_image').val('');
                    return false;
                }
            }

            // Restringir el tamaño del archivo de imagen a un máximo de 1 Mb
            var img = document.forms['frm-usuario']['us_foto'];
            if (img.value === '') {
                alert("No ha seleccionado un archivo de imagen");
                return false;
            } else {
                if (parseFloat(img.files[0].size / (1024 * 1024)) >= 1) {
                    alert("El tamaño del archivo de imagen debe ser menor que 1 MB");
                    return false;
                }
            }

            var data = new FormData(this);
            console.log(data);

            $.ajax({
                url: url,
                method: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonTexto: 'Aceptar'
                    });

                    showUsuariosPerfil($("#cboPerfiles").val());

                    $("#frm-usuario")[0].reset();
                    $("#btn-save").html("Guardar");
                    $("#titulo").html("Nuevo Usuario");
                    $("#cboPerfiles").prop("disabled", false);
                    $("#img_div").addClass("hide");
                    $("#id_usuario").val(0);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });

        });

        $("#us_foto").change(function() {
            $("#img_div").removeClass("hide");
            filePreview(this);
        });

        $("#form-search").submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var search = $('#search_pattern').val();
            var id_perfil = $('#cboPerfiles').val();
            if (id_perfil == "") {
                swal('¡Error!', 'Debe seleccionar un perfil...', 'error');
            } else if (search.trim() == "") {
                showUsuariosPerfil(id_perfil);
            } else {
                // Aqui vamos a consultar los usuarios que coincidan con el patron de busqueda
                var request = $.ajax({
                    url: url,
                    data: {
                        query: search,
                        id_perfil: id_perfil
                    },
                    method: "post",
                    dataType: "html"
                });

                request.done(function(resultado) {
                    $("#tbody_usuarios").html(resultado);
                });

                request.fail(function(jqXHR, textStatus) {
                    alert("Requerimiento fallido: " + jqXHR.responseText);
                    console.log(jqXHR.responseText);
                });
            }
        });

        $("#search_pattern").keyup(function() {
            var search = $(this).val();
            var id_perfil = $('#cboPerfiles').val();
            if (id_perfil == "") {
                swal('¡Error!', 'Debe seleccionar un perfil...', 'error');
            } else if (search.trim() == "") {
                showUsuariosPerfil(id_perfil);
            } else {
                // Aqui vamos a consultar los usuarios que coincidan con el patron de busqueda
                var request = $.ajax({
                    url: "usuario/buscar_usuario.php",
                    data: {
                        query: search,
                        id_perfil: id_perfil
                    },
                    type: "post",
                    dataType: "html"
                });

                request.done(function(resultado) {
                    $("#tbody_usuarios").html(resultado);
                });
            }
        });

    });

    function cargar_perfiles() {
        $.get("scripts/cargar_perfiles.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboPerfiles').append(resultado);
            }
        });
    }

    function showUsuariosPerfil(id_perfil) {
        $.get("usuario/listar_usuarios.php", {
                id_perfil: id_perfil
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#tbody_usuarios").html(resultado);
                }
            }
        );
    }

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function filePreview(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.readAsDataURL(input.files[0]);
            reader.onload = function(e) {
                $("#us_avatar").attr("src", e.target.result);
            }
        }
    }

    function asociarUsuarioPerfil() {
        var id_perfil = $("#cboPerfiles").val();
        var id_usuario = $("#id_usuario").val();
        var usuario = $("#search_user").val();

        if (usuario.trim() == "") {
            alert("Debes \"buscar\" el usuario que va a ser asociado...");
            $("#search_user").focus();
        } else if (id_perfil == 0) {
            alert("Debes \"seleccionar\" el perfil que va a ser asociado...");
            $("#cboPerfiles").focus();
        } else {
            $.ajax({
                url: "usuario/asociar_usuario_perfil.php",
                method: "POST",
                dataType: "json",
                data: {
                    id_usuario: id_usuario,
                    id_perfil: id_perfil
                },
                success: function(response) {
                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonTexto: 'Aceptar'
                    });

                    showUsuariosPerfil($("#cboPerfiles").val());

                    $("#search_user").val("");
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

    }
</script>