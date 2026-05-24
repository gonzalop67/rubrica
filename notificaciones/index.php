<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Notificaciones
            <small id="subtitulo">Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <span id="notify_message"></span>
                <br />
                <div id="display_notify">
                    <!-- Aquí van las notificaciones... -->
                </div>
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
        // JQuery Listo para utilizar
        load_notifies();

        $(document).on('click', '.delete', function() {
            var notify_id = $(this).attr("id");
            swal({
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
                            url: "notificaciones/delete_notify.php",
                            data: {
                                notify_id: notify_id
                            },
                            dataType: "JSON",
                        }).done(function(data) {
                            if (data.error != '') {
                                $('#notify_message').html(data.error);
                                load_notifies();
                            }
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            alert(jqXHR.responseText);
                        });
                    }
                }
            );
        });
    });

    function load_notifies() {
        $.ajax({
            url: "notificaciones/fetch_notifies.php",
            type: "POST",
            data: {
                nombrePerfil: $("#nombrePerfil").val()
            },
            success: function(data) {
                $('#display_notify').html(data);
            }
        })
    }
</script>