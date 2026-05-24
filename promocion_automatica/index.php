<div class="content-wrapper">
    <div id="asociarAsignaturaCursoApp" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Promoción Automática</h4>
            </div>
            <div class="panel-body">
                <!-- message -->
                <div id="text_message" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Paralelo Actual</th>
                            <th>Paralelo Anterior</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        cargar_paralelos_promocion();
        $('#lista_items').on('click', '.item-procesar', function() {
            var id = $(this).attr('data');

            // alert(id);

            Swal.fire({
                title: "¿Está seguro que quiere procesar este paralelo?",
                text: "No podrá revertir el proceso que será ejecutado!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, procéselo!",
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
                    $.ajax({
                        url: "promocion_automatica/promocion_paralelo.php",
                        data: {
                            id: id
                        },
                        method: "post",
                        dataType: "json",
                        success: function(data) {
                            // alert(data.mensaje);
                            Swal.fire({
                                title: data.titulo,
                                text: data.mensaje,
                                icon: data.tipo_mensaje
                            });
                            $("#text_message").html("");
                            cargar_paralelos_promocion();
                        },
                        error: function(jqXHR, textStatus) {
                            console.log(jqXHR.responseText);
                            alert(jqXHR.responseText);
                        }
                    });
                }
            });

        });
    });

    function cargar_paralelos_promocion() {
        $.get("promocion_automatica/listar_paralelos_promocion.php",
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#mensaje").html("");
                    $("#lista_items").html(resultado);
                }
            }
        );
    }
</script>