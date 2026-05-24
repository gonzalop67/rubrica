<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Comentarios
            <small id="subtitulo">Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div id="cuadro2" class="col-sm-12 col-md-12 col-lg-12">
                        <form id="comment_form" class="form-horizontal" action="" method="POST">
                            <div class="form-group">
                                <h3 class="text-center">Ingrese aqu&iacute; su comentario</h3>
                            </div>
                            <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">
                            <input type="hidden" id="id_perfil" name="id_perfil" value="<?php echo $id_perfil; ?>">
                            <input type="hidden" name="comment_id" id="comment_id" value="0" />
                            <div class="form-group">
                                <label for="id_usuario_para" class="col-sm-2 control-label">Para:</label>
                                <div class="col-sm-8">
                                    <select name="id_usuario_para" id="id_usuario_para" class="form-control">
                                        <option value="">Seleccione...</option>
                                    </select>
                                    <p id="mensaje1" class="mensaje"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="txt_mensaje" class="col-sm-2 control-label">Mensaje:</label>
                                <div class="col-sm-8">
                                    <textarea id="comment_content" name="comment_content" class="form-control text-uppercase" maxlength="250"></textarea>
                                    <p id="mensaje2" class="mensaje"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input id="" type="submit" class="btn btn-success" value="Enviar">
                                </div>
                            </div>
                        </form>
                        <!-- <div class="col-sm-offset-2 col-sm-8 fuente9">
                            <p class="mensaje"></p>
                        </div> -->
                    </div>
                </div>
                <span class="mensaje" id="comment_message"></span>
                <br />
                <div id="display_comment">
                    <!-- Aquí van los comentarios... -->
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- DataTables -->
<script src="assets/template/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/template/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        //listar(); //función que llama al plugin DataTables
        load_users();
        load_comment();
        $('#id_usuario_para').select2();
        $("#comment_form").on("submit", function(e) {
            e.preventDefault();
            var id_usuario_para = $("#id_usuario_para").val();
            var mensaje = $("#comment_content").val();
            if (id_usuario_para.trim() == "") {
                $("#mensaje1").html("Debe seleccionar un docente.").css({
                    "color": "#C9302C"
                });
                $("#mensaje1").fadeOut(5000, function() {
                    $(this).html("");
                    $(this).fadeIn(3000);
                });
                $("#comment_content").focus();
                return false;
            }
            if (mensaje.trim() == "") {
                $("#mensaje2").html("Debe ingresar el texto del mensaje.").css({
                    "color": "#C9302C"
                });
                $("#mensaje2").fadeOut(5000, function() {
                    $(this).html("");
                    $(this).fadeIn(3000);
                });
                $("#comment_content").focus();
                return false;
            }
            var form_data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: "comentarios/add_comment.php",
                data: form_data,
                dataType: "JSON",
            }).done(function(data) {
                if (data.error != '') {
                    $('#comment_form')[0].reset();
                    $('#comment_message').html(data.error);
                    $('#comment_id').val('0');
                    load_comment();
                    $('#titulo_formulario').html("Ingrese aqu&iacute; su comentario");
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
            });
        });
        $(document).on('click', '.reply', function() {
            var comment_id = $(this).attr("id");
            $('#comment_id').val(comment_id);
            $('#titulo_formulario').html("Ingrese aqu&iacute; su respuesta");
            $('#comment_content').focus();
        });
        $(document).on('click', '.delete', function() {
            var comment_id = $(this).attr("id");
            /* swal({
                    title: 'Está seguro de eliminar este comentario?',
                    text: "No podrá revertir esta acción!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, elimínelo!'
                },
                function(isConfirmed) {
                    if (isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: "comentarios/delete_comment.php",
                            data: {
                                comment_id: comment_id
                            },
                            dataType: "JSON",
                        }).done(function(response) {
                            $('#comment_form')[0].reset();
                            toastr[response.tipo_mensaje](response.mensaje, response.titulo);
                            $('#comment_id').val('0');
                            load_comment();
                            $('#titulo_formulario').html("Ingrese aqu&iacute; su comentario");
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            alert(jqXHR.responseText);
                        });
                    }
                }
            ); */

            Swal.fire({
                title: "¿Está seguro que quiere eliminar el registro?",
                text: "No podrá recuperar el registro que va a ser eliminado!",
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
                        url: "comentarios/delete_comment.php",
                        data: {
                            comment_id: comment_id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.estado == "success") {
                                Swal.fire({
                                    icon: response.estado,
                                    title: response.titulo,
                                    text: response.mensaje
                                });
                                $('#comment_id').val('0');
                                load_comment();
                                $('#titulo_formulario').html("Ingrese aqu&iacute; su comentario");
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            });
        });
    });
    var mostrar_mensaje = function(informacion) {
        var texto = "",
            color = "";
        texto = '<p class="text-center">';
        if (informacion.tipo_mensaje == "BIEN") {
            texto += "<strong>Bien!</strong> " + informacion.mensaje;
            color = "#379911";
        } else if (informacion.tipo_mensaje == "ERROR") {
            texto += "<strong>Error</strong>, " + informacion.mensaje;
            color = "#C9302C";
        } else if (informacion.tipo_mensaje == "EXISTE") {
            texto += "<strong>Información!</strong> el usuario ya existe.";
            color = "#5b94c5";
        } else if (informacion.tipo_mensaje == "VACIO") {
            texto += "<strong>Advertencia!</strong> debe llenar todos los campos solicitados.";
            color = "#ddb11d";
        } else if (informacion.tipo_mensaje == "OPCION_VACIA") {
            texto += "<strong>Advertencia!</strong> la opción no existe o esta vacia, recargar la página.";
            color = "#ddb11d";
        }
        texto += "</p>";
        $(".mensaje").html(texto).css({
            "color": color
        });
        $(".mensaje").fadeOut(5000, function() {
            $(this).html("");
            $(this).fadeIn(3000);
        });
    }
    var agregar_nuevo_mensaje = function() {
        limpiar_datos();

    }
    var limpiar_datos = function() {
        $("#txt_comentario").html("").focus();
        $("#titulo_formulario").html("Ingrese aqu&iacute; su comentario");
    }

    function load_comment() {
        $.ajax({
            url: "comentarios/fetch_comment.php",
            method: "POST",
            data: {
                nombrePerfil: $("#nombrePerfil").val(),
                id_usuario: $("#id_usuario").val()
            },
            success: function(data) {
                $('#display_comment').html(data);
            }
        })
    }

    function load_users() {
        $.ajax({
            url: "comentarios/fetch_users.php",
            method: "GET",
            success: function(data) {
                $('#id_usuario_para').append(data);
            }
        })
    }
</script>