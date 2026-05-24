<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Paralelos
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevoParaleloModal"><i class="fa fa-plus-circle"></i> Nuevo Paralelo</button>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>Nro</th>
                                    <th>Especialidad</th>
                                    <th>Curso</th>
                                    <th>Nombre</th>
                                    <th>Jornada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="lista_paralelos">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                        <!-- message -->
                        <div id="text_message" class="fuente9 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script src="js/funciones.js"></script>

<script>
    $(document).ready(function() {
        cargar_cursos();
        cargar_jornadas();
        listarParalelos();

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

    function cargar_cursos() {
        $.get("scripts/cargar_cursos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#new_cbo_cursos').append(resultado);
            }
        });
    }

    function cargar_jornadas() {
        $.get("scripts/cargar_jornadas.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#new_cboJornada").append(resultado);
                    $("#edit_cboJornada").append(resultado);
                }
            }
        );
    }

    function listarParalelos() {
        $.get("paralelos/cargar_paralelos.php", function(resultado) {
            $('#lista_paralelos').html(resultado);
        });
    }

    function insertarParalelo() {
        var id_curso = $("#new_cbo_cursos").val();
        var pa_nombre = $("#new_pa_nombre").val();
        var id_jornada = $("#new_cboJornada").val();

        // expresion regular para validar el ingreso del nombre
        var reg_nombre = /^([a-zA-Z. ]{1,16})$/i;

        // contador de errores
        var cont_errores = 0;

        if (id_curso == 0) {
            $("#mensaje1").html("Debes seleccionar el Curso");
            $("#mensaje1").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_jornada == 0) {
            $("#mensaje2").html("Debes seleccionar la Jornada");
            $("#mensaje2").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (pa_nombre.trim() == "") {
            $("#mensaje3").html("Debes ingresar el nombre del Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombre.test(pa_nombre)) {
            $("#mensaje3").html("Debes ingresar un nombre válido para el Paralelo");
            $("#mensaje3").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "paralelos/insertar_paralelo.php",
                method: "POST",
                dataType: "json",
                data: {
                    id_curso: id_curso,
                    id_jornada: id_jornada,
                    pa_nombre: pa_nombre
                },
                success: function(response) {
                    listarParalelos();
                    Swal.fire({
                        icon: response.estado,
                        title: response.titulo,
                        text: response.mensaje
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $('#nuevoParaleloModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        $.ajax({
            type: "POST",
            url: "paralelos/obtener_paralelo.php",
            data: "id_paralelo=" + id,
            success: function(resultado) {
                var paralelo = eval('(' + resultado + ')');
                $("#id_paralelo").val(id);
                $("#id_curso").val(paralelo.id_curso);
                $("#edit_cu_nombre").val("[" + paralelo.es_figura + "] " + paralelo.cu_nombre);
                $("#edit_pa_nombre").val(paralelo.pa_nombre);
                setearIndice("edit_cboJornada", paralelo.id_jornada);
            }
        });
    }

    function actualizarParalelo() {
        var id_curso = $("#id_curso").val();
        var id_paralelo = $("#id_paralelo").val();
        var id_jornada = $("#edit_cboJornada").val();
        var pa_nombre = $("#edit_pa_nombre").val();

        // expresion regular para validar el ingreso del nombre
        var reg_nombre = /^([a-zA-Z.]{1,5})$/i;

        // contador de errores
        var cont_errores = 0;

        if (id_jornada == 0) {
            $("#mensaje4").html("Debes seleccionar la Jornada");
            $("#mensaje4").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (pa_nombre.trim() == "") {
            $("#mensaje5").html("Debes ingresar el nombre del Paralelo");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombre.test(pa_nombre)) {
            $("#mensaje5").html("Debes ingresar un nombre válido para el Paralelo");
            $("#mensaje5").fadeIn("slow");
            cont_errores++;
        } else {
            $("#mensaje5").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "paralelos/actualizar_paralelo.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_paralelo: id_paralelo,
                    id_curso: id_curso,
                    id_jornada: id_jornada,
                    pa_nombre: pa_nombre
                },
                success: function(response) {
                    listarParalelos();
                    Swal.fire({
                        icon: response.estado,
                        title: response.titulo,
                        text: response.mensaje
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarParaleloModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

    function eliminarParalelo(id) {
        // Swal.fire({
        //         title: "¿Está seguro que quiere eliminar el registro?",
        //         text: "No podrá recuperar el registro que va a ser eliminado!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonClass: "btn-danger",
        //         confirmButtonText: "Sí, elimínelo!"
        //     },
        //     function() {
        //         $.ajax({
        //             url: "paralelos/eliminar_paralelo.php",
        //             method: "POST",
        //             data: "id_paralelo=" + id,
        //             dataType: "json",
        //             success: function(response) {
        //                 // console.log(r);
        //                 listarParalelos();
        //                 Swal.fire({
        //                     icon: response.estado,
        //                     title: response.titulo,
        //                     text: response.mensaje
        //                 });
        //             },
        //             error: function(jqXHR, textStatus, errorThrown) {
        //                 // Otro manejador error
        //                 alert.log(jqXHR.responseText);
        //             }
        //         });
        //     });

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
                    url: "paralelos/eliminar_paralelo.php",
                    data: {
                        id_paralelo: id
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });

                        listarParalelos();
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
            url: "paralelos/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                listarParalelos();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                alert.log(jqXHR.responseText);
            }
        });
    }
</script>