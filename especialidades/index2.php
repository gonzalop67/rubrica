<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Especialidades
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <!-- Default box -->
            <div class="box box-info">
                <div class="box-body">
                    <div class="box-header with-border">
                        <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevaEspecialidadModal"><i class="fa fa-plus-circle"></i> Nueva Especialidad</button>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <div class="form-group">
                                    <select id="cbo_nivel_educacion" class="form-control">
                                        <option value="0" selected>Seleccione un nivel de educación...</option>
                                    </select>
                                </div>
                                <!-- table -->
                                <table class="table fuente9">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Figura</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista_especialidades">
                                        <!-- Aqui desplegamos el contenido de la base de datos -->
                                    </tbody>
                                </table>
                                <div class="text-center" id="text_message"></div>
                            </div>
                        </div>
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
        $("#btn-new").attr("disabled", "true");
        cargarNivelesEducacion();
        $("#cbo_nivel_educacion").change(function(e) {
            // Código para recuperar las especialidades asociadas al nivel educativo seleccionado
            listarEspecialidades();
        });
        $("#lista_especialidades").html("<tr><td colspan='4' align='center'>Debes seleccionar un nivel educativo...</td></tr>");

        $('table tbody').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    if ($(this).attr('data-orden') != (index + 1)) {
                        $(this).attr('data-orden', (index + 1)).addClass('updated');
                    }
                });
                saveNewPositions();
            }
        });
    });

    function cargarNivelesEducacion() {
        const id_periodo_lectivo = $("#id_periodo_lectivo").val();
        $.get("scripts/cargar_tipos_educacion.php", {id_periodo_lectivo: id_periodo_lectivo},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cbo_nivel_educacion").append(resultado);
                    // console.log(resultado);
                }
            }
        );
    }

    function listarEspecialidades() {
        var id = $("#cbo_nivel_educacion").val();
        if (id == 0) {
            $("#lista_especialidades").html("<tr><td colspan='4' align='center'>Debes seleccionar un nivel educativo...</td></tr>");
            $("#btn-new").attr("disabled", true);
        } else {
            $.post("especialidades/cargar_especialidades.php", {
                    id_tipo_educacion: id
                },
                function(resultado) {
                    if (resultado == false) {
                        alert("Error");
                    } else {
                        $("#btn-new").attr("disabled", false);
                        $("#lista_especialidades").html(resultado);
                    }
                }
            );
        }
    }

    function insertarEspecialidad() {
        let cont_errores = 0;
        let es_nombre = $("#es_nombre").val();
        let es_figura = $("#es_figura").val();
        let es_abreviatura = $("#es_abreviatura").val();
        let id_tipo_educacion = $("#cbo_nivel_educacion").val();

        if (es_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre de la especialidad...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (es_figura == "") {
            $("#mensaje2").html("Debe ingresar la figura de la especialidad...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (es_abreviatura == "") {
            $("#mensaje3").html("Debe ingresar la abreviatura de la especialidad...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "especialidades/insertar_especialidad.php",
                type: "POST",
                data: {
                    es_nombre: es_nombre,
                    es_figura: es_figura,
                    es_abreviatura: es_abreviatura,
                    id_tipo_educacion: id_tipo_educacion
                },
                dataType: "json",
                success: function(response) {
                    listarEspecialidades();
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: response.estado,
                        title: response.titulo,
                        text: response.mensaje
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevaEspecialidadModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        //Obtengo los datos de la especialidad seleccionada
        $("#text_message").html("<img src='./imagenes/ajax-loader-blue.GIF' alt='procesando'>");
        $.ajax({
            url: "especialidades/obtener_especialidad.php",
            method: "POST",
            type: "html",
            data: {
                id_especialidad: id
            },
            success: function(response) {
                $("#text_message").html("");
                $("#id_especialidad").val(id);
                var especialidad = jQuery.parseJSON(response);
                $("#es_nombreu").val(especialidad.es_nombre);
                $("#es_figurau").val(especialidad.es_figura);
                $("#es_abreviaturau").val(especialidad.es_abreviatura);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    $('#form_update').submit(function(e) {
        e.preventDefault();

        let cont_errores = 0;

        let id_especialidad = $("#id_especialidad").val();
        let id_tipo_educacion = $("#cbo_nivel_educacion").val();
        let es_nombre = $("#es_nombreu").val().trim();
        let es_figura = $("#es_figurau").val().trim();
        let es_abreviatura = $("#es_abreviaturau").val().trim();

        if (es_nombre == "") {
            $("#mensaje4").html("Debe ingresar el nombre de la especialidad...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (es_figura == "") {
            $("#mensaje5").html("Debe ingresar la figura de la especialidad...");
            $("#mensaje5").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (es_abreviatura == "") {
            $("#mensaje6").html("Debe ingresar la abreviatura de la especialidad...");
            $("#mensaje6").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje6").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "especialidades/actualizar_especialidad.php",
                data: {
                    id_especialidad: id_especialidad,
                    id_tipo_educacion: id_tipo_educacion,
                    es_nombre: es_nombre,
                    es_figura: es_figura,
                    es_abreviatura: es_abreviatura
                },
                dataType: "json",
                success: function(response) {
                    listarEspecialidades();
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: response.estado,
                        title: response.titulo,
                        text: response.mensaje
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarEspecialidadModal").modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Otro manejador error
                    console.log(jqXHR.responseText);
                }
            });
        }
    });

    function eliminarEspecialidad(id) {
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
                    url: "especialidades/eliminar_especialidad.php",
                    data: {
                        id_especialidad: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.estado == "success") {
                            Swal.fire({
                                icon: response.estado,
                                title: response.titulo,
                                text: response.mensaje
                            });
                            listarEspecialidades();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });
        // console.log(positions);
        $.ajax({
            url: "especialidades/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                listarEspecialidades();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>