<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Cuestionarios con Paralelos</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form-asociar" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_categoria" class="col-sm-2 control-label text-right">Cuestionario:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_categoria" id="id_categoria" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelo:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_asignatura" class="col-sm-2 control-label text-right">Asignatura:</label>
                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_asignatura" id="id_asignatura" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="botones_insercion">
                    <div class="col-sm-12" style="margin-top: 4px;">
                        <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                            Asociar
                        </button>
                    </div>
                </div>
            </form>
            <!-- Línea de división -->
            <hr>
            <!-- message -->
            <div id="text_message" class="fuente10 text-center"></div>
            <!-- table -->
            <table class="table fuente10">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cuestionario</th>
                        <th>Paralelo</th>
                        <th>Asignatura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista_items">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
</section>

<script src="js/funciones.js"></script>
<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('form-asociar');
        $("#lista_items").html("<tr><td colspan='5' align='center'>Debe seleccionar un cuestionario...</td></tr>");

        cargarCategorias();
        cargar_paralelos();
        cargar_asignaturas();

        $('#id_categoria').select2();
        $('#id_paralelo').select2();
        $('#id_asignatura').select2();

        $("#id_categoria").change(function() {
            var id_categoria = $(this).val();
            if (id_categoria === "")
                $("#lista_items").html("<tr><td colspan='5' align='center'>Debe seleccionar un cuestionario...</td></tr>");
            else
                showCuestionariosAsociados(id_categoria);
        });

        $("#form-asociar").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Cuestionario Paralelo
            let id_categoria = document.getElementById("id_categoria").value;
            let id_paralelo = document.getElementById("id_paralelo").value;
            let id_asignatura = document.getElementById("id_asignatura").value;

            if (id_categoria !== "" && id_paralelo !== "" && id_asignatura !== "") {
                $("#text_message").html("<img src='public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "cuestionarios/asociar_paralelo_insert.php",
                    method: "post",
                    data: {
                        id_categoria: id_categoria,
                        id_paralelo: id_paralelo,
                        id_asignatura: id_asignatura
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        // console.log(response);
                        toastr[response.tipo_mensaje](response.mensaje, response.titulo);

                        showCuestionariosAsociados(id_categoria);
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            }
        });

        $('#lista_items').on('click', '.item-delete', function(e) {
            e.preventDefault();
            let id = $(this).attr('data');
            $("#text_message").html("<img src='public/img/ajax-loader-blue.GIF' alt='procesando...' />");
            $.ajax({
                url: "cuestionarios/eliminarAsociacion.php",
                method: "post",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    $("#text_message").html("");
                    /* swal({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    }); */

                    Swal.fire({
                        icon: response.estado,
                        title: response.titulo,
                        text: response.tipo_mensaje
                    });
                    showCuestionariosAsociados($("#id_categoria").val());
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        toastr.options = {
            "positionClass": "toast-bottom-right",
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados
            "timeOut": "3000"
        };
    });

    function cargarCategorias() {
        var id_usuario = <?php echo $id_usuario; ?>;
        document.getElementById("id_categoria").options.length = 1;
        $.post("preguntas/obtener_categorias.php", {
                id_usuario: id_usuario
            },
            function(resultado) {
                if (resultado == false) {
                    alert(resultado);
                } else {
                    $("#id_categoria").append(resultado);
                }
            }
        );
    }

    function cargar_paralelos() {
        $.get("cuestionarios/cargar_paralelos.php", function(resultado) {
            console.log(resultado);
            if (resultado == false) {
                alert("Error");
            } else {
                $('#id_paralelo').append(resultado);
            }
        });
    }

    function cargar_asignaturas() {
        $.ajax({
            url: "cuestionarios/cargar_asignaturas.php",
            type: "POST",
            data: "id_usuario=" + <?php echo $id_usuario; ?>,
            dataType: "html",
            success: function(r) {
                // console.log(r);
                $("#id_asignatura").append(r);
            }
        });
    }

    function showCuestionariosAsociados(id_categoria) {
        $.each($('label'), function(key, value) {
            $(this).closest('.form-group').removeClass('has-error');
        });

        var request = $.ajax({
            url: "cuestionarios/getByCategoryId.php",
            method: "post",
            data: {
                id_categoria: $('#id_categoria').val()
            },
            dataType: "html"
        });

        request.done(function(data) {
            // console.log(data);
            $("#lista_items").html(data);
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }
</script>