<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Escalas de Calificaciones
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 table-responsive fuente9">
                        <table id="t_escalas" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Cualitativa</th>
                                    <th>Cuantitativa</th>
                                    <th>Nota Mínima</th>
                                    <th>Nota Máxima</th>
                                    <th>Equivalencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_escalas">
                                <!-- En este lugar se van a poblar las escalas de calificaciones mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="panel-body fuente9">
                            <!-- Horizontal Form -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 id="titulo" class="box-title">Nueva Escala de Calificación</h3>
                                </div>
                                <!-- /.box-header -->
                                <form id="frm-escala">
                                    <input type="hidden" name="id_escala_calificaciones" id="id_escala_calificaciones" value="0">
                                    <!-- form frm_id start -->
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="ec_cualitativa" class="col-form-label">Escala Cualitativa:</label>
                                            <input type="text" class="form-control" name="ec_cualitativa" id="ec_cualitativa">
                                        </div>
                                        <div class="form-group">
                                            <label for="ec_cuantitativa" class="col-form-label">Escala Cuantitativa:</label>
                                            <input type="text" class="form-control" name="ec_cuantitativa" id="ec_cuantitativa">
                                        </div>
                                        <div class="form-group">
                                            <label for="ec_nota_minima" class="col-form-label">Nota Mínima:</label>
                                            <input type="number" step="any" min="0" max="10" class="form-control" name="ec_nota_minima" id="ec_nota_minima">
                                        </div>
                                        <div class="form-group">
                                            <label for="ec_nota_maxima" class="col-form-label">Nota Máxima:</label>
                                            <input type="number" step="any" min="0" max="10" class="form-control" name="ec_nota_maxima" id="ec_nota_maxima">
                                        </div>
                                        <div class="form-group">
                                            <label for="ec_equivalencia" class="col-form-label">Equivalencia:</label>
                                            <input type="text" class="form-control" name="ec_equivalencia" id="ec_equivalencia">
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
                                        <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                    </div>
                                    <!-- /.box-footer -->
                                </form>
                                <!-- form frm-usuario end -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {

        listarEscalas();

        toastr.options = {
            //primeras opciones
            "closeButton": false, //boton cerrar
            "debug": false,
            "newestOnTop": false, //notificaciones mas nuevas van en la parte superior
            "progressBar": true, //barra de progreso hasta que se oculta la notificacion
            "preventDuplicates": false, //para prevenir mensajes duplicados

            "onclick": null,

            //Posición de la notificación
            //toast-bottom-left, toast-bottom-right, toast-bottom-left, toast-top-full-width, toast-top-center
            "positionClass": "toast-top-center",

            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };

        $('table tbody').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    if ($(this).attr('data-orden') != (index + 1)) {
                        $(this).attr('data-orden', (index + 1)).addClass('updated');
                    }
                });

                saveNewPositions();
                listarEscalas();
            }
        });

        $("#btn-cancel").click(function() {
            $("#frm-escala")[0].reset();
            $("#titulo").html("Nueva Escala de Calificaciones");
            $("#btn-save").html("Guardar");
            $("#id_escala_calificaciones").val(0);
        });

        $('#tbody_escalas').on('click', '.item-edit', function() {
            var id_escala_calificaciones = $(this).attr('data');
            $("#titulo").html("Editar Escala de Calificaciones");
            $("#btn-save").html("Actualizar");
            $.ajax({
                url: "escalas/obtener_escala.php",
                data: {
                    id: id_escala_calificaciones
                },
                method: "post",
                dataType: "json",
                success: function(data) {
                    //Aqui se va a pintar la escala de calificacion
                    document.getElementById("id_escala_calificaciones").value = id_escala_calificaciones;
                    document.getElementById("ec_cualitativa").value = data.ec_cualitativa;
                    document.getElementById("ec_cuantitativa").value = data.ec_cuantitativa;
                    document.getElementById("ec_nota_minima").value = data.ec_nota_minima;
                    document.getElementById("ec_nota_maxima").value = data.ec_nota_maxima;
                    document.getElementById("ec_equivalencia").value = data.ec_equivalencia;
                    document.getElementById("ec_cualitativa").focus();
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        $('#tbody_escalas').on('click', '.item-delete', function() {
            var id_escala_calificaciones = $(this).attr('data');
            $.ajax({
                url: "escalas/eliminar_escala.php",
                method: "POST",
                dataType: "json",
                data: {
                    id: id_escala_calificaciones
                },
                success: function(response) {
                    /* swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonTexto: 'Aceptar'
                    }); */

                    /* Swal.fire({
                        icon: response.tipo_mensaje,
                        title: response.titulo,
                        text: response.mensaje
                    }); */

                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);

                    listarEscalas();
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });

        $("#frm-escala").submit(function(e) {
            e.preventDefault();
            var url = "";

            if ($("#btn-save").html() == "Actualizar") {
                url = "escalas/actualizar_escala.php";
            } else {
                url = "escalas/insertar_escala.php";
            }

            // Validar la entrada de los campos input
            if ($("#ec_cualitativa").val().trim() === '') {
                /* swal({
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la escala cualitativa.",
                    type: "error",
                    confirmButtonTexto: 'Aceptar'
                }); */

                /* Swal.fire({
                    icon: "error",
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la escala cualitativa."
                }); */

                toastr["error"]("Debe ingresar la escala cualitativa", "Oops!");

                return false;
            } else if ($("#ec_cuantitativa").val().trim() === '') {
                /* swal({
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la escala cuantitativa.",
                    type: "error",
                    confirmButtonTexto: 'Aceptar'
                }); */

                /* Swal.fire({
                    icon: "error",
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la escala cuantitativa."
                }); */

                toastr["error"]("Debe ingresar la escala cuantitativa", "Oops!");

                return false;
            } else if ($("#ec_nota_minima").val().trim() === '') {
                /* swal({
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la nota mínima.",
                    type: "error",
                    confirmButtonTexto: 'Aceptar'
                }); */
                toastr["error"]("Debe ingresar la nota mínima", "Oops!");
                return false;
            } else if ($("#ec_nota_maxima").val().trim() === '') {
                /* swal({
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la nota máxima.",
                    type: "error",
                    confirmButtonTexto: 'Aceptar'
                }); */
                /* Swal.fire({
                    icon: "error",
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la nota máxima."
                }); */
                toastr["errotr"]("Debe ingresar la nota máxima", "Oops!");
                return false;
            } else if ($("#ec_equivalencia").val().trim() === '') {
                /* swal({
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la equivalencia.",
                    type: "error",
                    confirmButtonTexto: 'Aceptar'
                }); */

                /* Swal.fire({
                    icon: "error",
                    title: "Ocurrió un error inesperado",
                    text: "Debe ingresar la equivalencia."
                }); */
                toastr["error"]("Debe ingresar la equivalencia", "Oops!");
                return false;
            }

            //Procedimiento para actualizar una escala de calificacion
            let id = $("#id_escala_calificaciones").val();
            let cualitativa = $("#ec_cualitativa").val().trim();
            let cuantitativa = $("#ec_cuantitativa").val().trim();
            let minima = $("#ec_nota_minima").val().trim();
            let maxima = $("#ec_nota_maxima").val().trim();
            let equivalencia = $("#ec_equivalencia").val().trim();

            $.ajax({
                url: url,
                method: "POST",
                data: {
                    id: id,
                    cualitativa: cualitativa,
                    cuantitativa: cuantitativa,
                    minima: minima,
                    maxima: maxima,
                    equivalencia: equivalencia
                },
                dataType: "json",
                success: function(response) {
                    /* swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonTexto: 'Aceptar'
                    }); */

                    /* Swal.fire({
                        icon: response.tipo_mensaje,
                        title: response.titulo,
                        text: response.mensaje
                    }); */

                    toastr[response.tipo_mensaje](response.mensaje, response.titulo);

                    listarEscalas();

                    $("#frm-escala")[0].reset();
                    $("#btn-save").html("Guardar");
                    $("#titulo").html("Nueva Escala de Calificaciones");
                    $("#id_escala_calificaciones").val(0);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });

        });
    });

    function listarEscalas() {
        $.get("escalas/listar_escalas.php", {},
            function(resultado) {
                console.log(resultado)
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#tbody_escalas").html(resultado);
                }
            }
        );
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "escalas/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                console.log(response);
            }
        });
    }
</script>