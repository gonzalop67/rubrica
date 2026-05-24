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
    $(document).ready(function() {
        $("#error-receptor_id").hide();
        $("#error-mensaje").hide();

        load_users($("#id_usuario").val());

        // Quitar error al seleccionar
        $("#receptor_id").on('change', function() {
            if ($(this).val() == '') {
                $(this).closest('.form-group').addClass('has-error');
                $("#error-receptor_id").fadeIn("slow");
            } else {
                load_messages($(this).val());
                $(this).closest('.form-group').removeClass('has-error');
                $("#error-receptor_id").fadeOut();
            }
        })

        // Quitar error al escribir
        $('#mensaje').on('input', function() {
            $(this).closest('.form-group').removeClass('has-error');
            $("#error-mensaje").fadeOut();
        });
    });

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
        })
    }

    function load_messages(receptor_id) {
        $.ajax({
            url: "comentarios/fetch_messages.php",
            method: "POST",
            data: {
                receptor_id: receptor_id,
                emisor_id: $("#id_usuario").val()
            },
            success: function(data) {
                console.log(data);
                //$('#display_comment').html(data);
            }
        });
    }

    $("#form-messages").submit(function(e) {
        // e.preventDefault();
        var mensaje = $("#mensaje").val().trim();
        var receptor_id = $("#receptor_id").val();
        // alert(receptor_id);
        if (mensaje == '') {
            $('#mensaje').closest('.form-group').addClass('has-error');
            $("#error-mensaje").fadeIn("slow");
        } else {
            $("#error-mensaje").fadeOut();
        }
        if (receptor_id == '') {
            $('#receptor_id').closest('.form-group').addClass('has-error');
            $('#error-receptor_id').fadeIn("slow");
        } else {
            $("#error-recepetor_id").fadeOut();
        }
        if (mensaje !== '' && receptor_id !== '') {
            // alert($("#receptor_id").val());
            $.ajax({
                type: "POST",
                url: "comentarios/add_comment.php",
                data: {
                    mensaje: mensaje,
                    emisor_id: $("#id_usuario").val(),
                    receptor_id: $("#receptor_id").val()
                },
                dataType: "json",
                success: function (response) {
                    console.log(response)
                }
            });
        }

        return false;
    });
</script>