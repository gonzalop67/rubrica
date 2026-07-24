<?php
// Al inicio de tu archivo del chat (ej. mensajes.php)
// Captura el parámetro si existe, de lo contrario lo deja en vacío
$id_receptor_url = isset($_GET['id_receptor']) ? intval($_GET['id_receptor']) : '';
?>

<!-- Añade este campo oculto en cualquier parte de tu HTML para pasar el valor a JavaScript -->
<input type="hidden" id="receptor_url_id" value="<?php echo $id_receptor_url; ?>">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Mensajes
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form id="form-messages" action="#" method="post">
            <div class="box box-primary direct-chat direct-chat-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Destinatario</h3>
                    <!-- Selección de Destinatario -->
                    <div class="form-group has-feedback">
                        <select id="receptor_id" class="form-control">
                            <option value="">Seleccione...</option>
                        </select>
                        <span style="color: #950606" id="error-receptor_id">Debes seleccionar un destinatario...</span>
                    </div>
                </div>
                <div class="box-body">
                    <div class="direct-chat-messages" id="chat-box">
                        <!-- Los mensajes se cargan aquí con jQuery -->
                    </div>
                </div>
                <div class="box-footer">

                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="text" name="mensaje" id="mensaje" placeholder="Escribir mensaje..." class="form-control">
                            <span class="input-group-btn">
                                <button type="submit" id="enviar" class="btn btn-primary btn-flat">Enviar</button>
                            </span>
                        </div>
                        <span style="color: #950606" id="error-mensaje">Debes ingresar el mensaje a enviar...</span>
                    </div>

                </div>
            </div>
        </form>
    </section>
</div>

<script>
    var chatInterval = null;

    $(document).ready(function() {
        $("#error-receptor_id").hide();
        $("#error-mensaje").hide();

        load_users($("#id_usuario").val());
        $('#receptor_id').select2();

        // NUEVO: Detectar si el usuario viene redireccionado desde la barra superior
        var receptorPreseleccionado = $("#receptor_url_id").val();

        if (receptorPreseleccionado !== '') {
            // Un pequeño retraso (300ms) para dar tiempo a que 'load_users' termine de cargar el HTML en el select
            setTimeout(function() {
                // Asignar el valor al Select2 y disparar el evento 'change' para que cargue los mensajes
                $('#receptor_id').val(receptorPreseleccionado).trigger('change');
            }, 300);
        }

        // 1. CARGA INICIAL DE LA BARRA SUPERIOR
        load_navbar_messages();

        // Bucle global continuo cada 3 segundos para refrescar la barra superior todo el tiempo
        setInterval(function() {
            load_navbar_messages();
        }, 3000);

        // Evento al cambiar de destinatario en el select del chat
        $("#receptor_id").on('change', function() {
            var valor = $(this).val();
            clearInterval(chatInterval);

            if (valor == '') {
                $(this).closest('.form-group').addClass('has-error');
                $("#error-receptor_id").fadeIn("slow");
                $("#chat-box").html('');
            } else {
                $(this).closest('.form-group').removeClass('has-error');
                $("#error-receptor_id").fadeOut();

                load_messages(valor);

                // Sincroniza la actualización de la ventana del chat activo
                chatInterval = setInterval(function() {
                    load_messages(valor);
                }, 3000);
            }
        });

        $("#form-messages").submit(function(e) {
            e.preventDefault();
            var mensaje = $("#mensaje").val().trim();
            var receptor_id = $("#receptor_id").val();
            var emisor_id = $("#id_usuario").val();
            var valid = true;

            if (mensaje == '') {
                $('#mensaje').closest('.form-group').addClass('has-error');
                $("#error-mensaje").fadeIn("slow");
                valid = false;
            }
            if (receptor_id == '') {
                $('#receptor_id').closest('.form-group').addClass('has-error');
                $('#error-receptor_id').fadeIn("slow");
                valid = false;
            }

            if (valid) {
                $("#enviar").prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "comentarios/add_comment.php",
                    data: {
                        mensaje: mensaje,
                        emisor_id: emisor_id,
                        receptor_id: receptor_id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            $("#mensaje").val('');
                            load_messages(receptor_id);
                            load_navbar_messages(); // Fuerza la actualización inmediata del menú superior
                        }
                    },
                    complete: function() {
                        $("#enviar").prop('disabled', false);
                    }
                });
            }
        });
    });

    // NUEVA FUNCIÓN: Refresca el menú desplegable de notificaciones superiores
    function load_navbar_messages() {
        var emisor_id = $("#id_usuario").val();
        if (!emisor_id) return;

        $.ajax({
            url: "comentarios/fetch_navbar_messages.php",
            method: "POST",
            data: {
                emisor_id: emisor_id
            },
            success: function(data) {
                // Reemplaza todo el HTML interno del elemento de la barra superior
                $('#navbar-messages-container').html(data);
            }
        });
    }

    function load_users(emisor_id) {
        $.ajax({
            url: "comentarios/fetch_users.php",
            method: "POST",
            data: {
                emisor_id: emisor_id
            },
            success: function(data) {
                $('#receptor_id').append(data);
            }
        });
    }

    function load_messages(receptor_id) {
        var emisor_id = $("#id_usuario").val();
        if (!receptor_id) return;

        $.ajax({
            url: "comentarios/fetch_messages.php",
            method: "POST",
            data: {
                receptor_id: receptor_id,
                emisor_id: emisor_id
            },
            success: function(data) {
                var chatBox = $('#chat-box');
                var currentScroll = chatBox.scrollTop();
                var totalHeight = chatBox.prop("scrollHeight");
                var boxHeight = chatBox.innerHeight();
                var estaAlFinal = (totalHeight - currentScroll - boxHeight) < 150;

                chatBox.html(data);

                if (estaAlFinal || currentScroll === 0) {
                    chatBox.scrollTop(chatBox.prop("scrollHeight"));
                }
            }
        });
    }
</script>