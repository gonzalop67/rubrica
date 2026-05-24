<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cursos
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
                        <button id="btn-new" class="btn btn-primary" data-toggle="modal" data-target="#nuevoCursoModal"><i class="fa fa-plus-circle"></i> Nuevo Curso</button>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 table-responsive">
                                <!-- table -->
                                <table class="table fuente9">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Figura</th>
                                            <th>Nivel de Educación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista_cursos">
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

<script src="js/funciones.js"></script>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        cargarEspecialidades();
        listarCursos();

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

    function cargarEspecialidades() {
        $.get("scripts/cargar_especialidades.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboEspecialidades").append(resultado);
                }
            }
        );
    }

    function listarCursos() {

        $.post("cursos/cargar_cursos.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#btn-new").attr("disabled", false);
                    $("#lista_cursos").html(resultado);
                }
            }
        );

    }

    function insertarCurso() {
        let cont_errores = 0;
        var id_especialidad = $("#cboEspecialidades").val();
        var cu_nombre = $("#new_cu_nombre").val().trim();
        var cu_abreviatura = $("#new_cu_abreviatura").val().trim();
        var cu_shortname = $("#new_cu_shortname").val().trim();
        var es_bach_tecnico = $("#new_es_bach_tecnico").val();
        var es_intensivo = $("#new_es_intensivo").val();
        var es_fin_subnivel = $("#new_es_fin_subnivel").val();

        if (cu_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre del curso...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cu_abreviatura == "") {
            $("#mensaje2").html("Debe ingresar la abreviatura del curso...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cu_shortname == "") {
            $("#mensaje3").html("Debe ingresar el nombre corto del curso...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "cursos/insertar_curso.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_especialidad: id_especialidad,
                    cu_nombre: cu_nombre,
                    cu_abreviatura: cu_abreviatura,
                    cu_shortname: cu_shortname,
                    es_bach_tecnico: es_bach_tecnico,
                    es_intensivo: es_intensivo,
                    es_fin_subnivel: es_fin_subnivel
                },
                success: function(r) {
                    // console.log(r)

                    listarCursos();

                    toastr[r.estado](r.mensaje, r.titulo);
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoCursoModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

    function obtenerDatos(id) {
        //Obtengo los datos del curso seleccionado
        $("#text_message").html("<img src='./imagenes/ajax-loader-blue.GIF' alt='procesando'>");
        $.ajax({
            url: "cursos/obtener_curso.php",
            method: "POST",
            type: "html",
            data: {
                id_curso: id
            },
            success: function(response) {
                $("#text_message").html("");
                $("#id_curso").val(id);
                var curso = jQuery.parseJSON(response);
                console.log(curso);
                $("#edit_cu_nombre").val(curso.cu_nombre);
                $("#edit_cu_abreviatura").val(curso.cu_abreviatura);
                $("#edit_cu_shortname").val(curso.cu_shortname);
                setearIndice("edit_es_bach_tecnico", curso.es_bach_tecnico);
                setearIndice("edit_es_intensivo", curso.es_intensivo);
                setearIndice("edit_es_fin_subnivel", curso.es_fin_subnivel);
                $('#editarCursoModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function actualizarCurso() {
        var id = $("#id_curso").val();
        var id_esp = $("#cboEspecialidades").val();
        var nombre = $("#edit_cu_nombre").val();
        var abreviatura = $("#edit_cu_abreviatura").val();
        var shortname = $("#edit_cu_shortname").val();
        var bach_tecnico = $("#edit_es_bach_tecnico").val();
        var es_intensivo = $("#edit_es_intensivo").val();
        var es_fin_subnivel = $("#edit_es_fin_subnivel").val();

        $.ajax({
            url: "cursos/actualizar_curso.php",
            type: "POST",
            dataType: "json",
            data: {
                id_curso: id,
                id_especialidad: id_esp,
                cu_nombre: nombre,
                cu_abreviatura: abreviatura,
                cu_shortname: shortname,
                bach_tecnico: bach_tecnico,
                es_intensivo: es_intensivo,
                es_fin_subnivel: es_fin_subnivel
            },
            success: function(r) {
                listarCursos();

                toastr[r.estado](r.mensaje, r.titulo);
                $('#form_update')[0].reset(); //limpiar formulario
                $("#editarCursoModal").modal('hide');
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    function eliminarCurso(id) {
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
                    url: "cursos/eliminar_curso.php",
                    data: {
                        id_curso: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.estado == "success") {
                            Swal.fire({
                                icon: response.estado,
                                title: response.titulo,
                                text: response.mensaje
                            });
                            listarCursos();
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
            url: "cursos/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                //
                listarCursos();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>