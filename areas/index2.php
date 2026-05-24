    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Areas
                <small>Listado</small>
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevaAreaModal"><i class="fa fa-plus-circle"></i> Nueva Area</button>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table id="t_areas" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_areas">

                                </tbody>
                            </table>
                            <div class="text-center" id="text_message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once "modalInsertar.php" ?>
    <?php require_once "modalActualizar.php" ?>
    <script>
        $(document).ready(function() {
            // JQuery Listo para utilizar
            cargarAreas();
            // Código para editar un area
            $('#form_insert').submit(function(event) {
                event.preventDefault();
                var cont_errores = 0;
                var ar_nombre = $('#ar_nombre').val();

                if (ar_nombre.trim() == "") {
                    $("#mensaje1").html("Debes ingresar el nombre del área...");
                    $("#mensaje1").fadeIn();
                    cont_errores++;
                } else {
                    $("#mensaje1").fadeOut();
                }

                if (cont_errores == 0) {
                    $.ajax({
                        url: "areas/insertar_area.php",
                        type: "POST",
                        data: {
                            ar_nombre: ar_nombre
                        },
                        dataType: "json",
                        success: function(r) {
                            cargarAreas();
                            // swal(r.titulo, r.mensaje, r.estado);
                            Swal.fire({
                                title: r.titulo,
                                text: r.mensaje,
                                icon: r.estado
                            });
                            $('#form_insert')[0].reset(); //limpiar formulario
                            $("#nuevaAreaModal").modal('hide');
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });
            $('#form_update').submit(function(event) {
                event.preventDefault();
                var cont_errores = 0;
                var id_area = $('#id_area').val();
                var ar_nombre = $('#ar_nombreu').val();

                if (ar_nombre.trim() == "") {
                    $("#mensaje2").html("Debes ingresar el nombre del área...");
                    $("#mensaje2").fadeIn();
                    cont_errores++;
                } else {
                    $("#mensaje2").fadeOut();
                }

                if (cont_errores == 0) {
                    $.ajax({
                        url: "areas/actualizar_area.php",
                        type: "POST",
                        data: {
                            id_area: id_area,
                            ar_nombre: ar_nombre
                        },
                        dataType: "json",
                        success: function(r) {
                            console.log(r);
                            cargarAreas();
                            // swal(r.titulo, r.mensaje, r.estado);
                            Swal.fire({
                                title: r.titulo,
                                text: r.mensaje,
                                icon: r.estado
                            });
                            $('#form_update')[0].reset(); //limpiar formulario
                            $("#editarAreaModal").modal('hide');
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            });
        });

        function cargarAreas() {
            // Obtengo todas las areas ingresadas en la base de datos
            $.ajax({
                url: "areas/cargar_areas.php",
                method: "GET",
                type: "html",
                success: function(response) {
                    $("#lista_areas").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function obtenerDatos(id) {
            //Primero obtengo el nombre del area seleccionada
            $.ajax({
                url: "areas/obtener_area.php",
                method: "POST",
                type: "html",
                data: {
                    id_area: id
                },
                success: function(response) {
                    var area = jQuery.parseJSON(response);
                    $("#ar_nombreu").val(area.ar_nombre);
                    $("#id_area").val(id);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        function eliminarArea(id) {
            //Elimino el area mediante AJAX
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
                        url: "areas/eliminar_area.php",
                        data: {
                            id_area: id
                        },
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                icon: response.estado,
                                title: response.titulo,
                                text: response.mensaje,
                            });
                            cargarAreas();
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            });
        }
    </script>