<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tareas
            <small>Lista de tareas por hacer</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevaTareaModal"><i class="fa fa-plus-circle"></i> Nueva Tarea</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="form-group">
                            <label for="cbo_filtro">Filtrar por:</label>
                            <select id="cbo_filtro" class="form-control">
                                <option value="0" selected>Seleccione una opción...</option>
                                <option value="1">Todas Las Tareas</option>
                                <option value="2">Tareas Completadas</option>
                                <option value="3">Tareas No Completadas</option>
                            </select>
                        </div>
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>Hecho</th>
                                    <th>Tarea</th>
                                    <th width="100px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tareas">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                        <input type="hidden" id="filtro">
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsertar.php" ?>
<?php require_once "modalActualizar.php" ?>

<script>
    var num_registros_paginacion = 5;

    $(document).ready(function() {
        // JQuery Listo para utilizar
        paginarTareas(1, num_registros_paginacion, " ");

        $('#form_update').submit(function(e) {
            e.preventDefault();

            let cont_errores = 0;

            let id_tarea = $("#id_tarea").val().trim();
            let tarea = $("#tareau").val().trim();
            let pagina_actual = $("#pagina_actual").val();
            let filtro = $("#filtro").val();

            if (tarea == "") {
                $("#mensaje2").html("Debe ingresar la tarea...");
                $("#mensaje2").fadeIn();
                cont_errores++;
            } else {
                $("#mensaje2").fadeOut();
            }

            if (cont_errores == 0) {
                $.ajax({
                    type: "POST",
                    url: "por_hacer/update_tarea.php",
                    data: {
                        id_tarea: id_tarea,
                        tarea: tarea
                    },
                    dataType: "json",
                    success: function(data) {
                        paginarTareas(1, num_registros_paginacion, filtro);

                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });

                        $('#form_update')[0].reset(); //limpiar formulario
                        $("#editarTareaModal").modal('hide');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Otro manejador error
                        console.log(jqXHR.responseText);
                    }
                });
            }
        });

        // Código para filtrar las tareas
        $("#cbo_filtro").change(function() {
            var tipo = $(this).val();
            var where = "";
            if (tipo == 0) {
                $("#tareas").html("<tr><td colspan='4' align='center'>Debes seleccionar una opción del filtro...</td></tr>");
                return false;
            } else if (tipo == 1) { // Todas las tareas
                where = " ";
            } else if (tipo == 2) { // Tareas completadas
                where = " WHERE hecho = 1 ";
            } else if (tipo == 3) { // Tareas no completadas
                where = " WHERE hecho = 0 ";
            }
            $("#filtro").val(where);
            // Llamada mediante AJAX a la consulta que mostrará las tareas requeridas
            $.ajax({
                url: "por_hacer/consultar_tareas.php",
                method: "POST",
                data: {
                    where: where
                },
                type: "html",
                success: function(response) {
                    paginarTareas(1, num_registros_paginacion, where);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        });
    });

    function paginarTareas(partida, num_registros, filtro) {
        // Obtengo todas las tareas ingresadas en la base de datos
        $("#pagina_actual").val(partida);
        $("#filtro").val(filtro);
        $.ajax({
            url: "por_hacer/paginar_tareas.php",
            type: "POST",
            data: {
                partida: partida,
                num_registros: num_registros,
                filtro: filtro
            },
            success: function(response) {
                // console.log(response);
                var array = eval(response);
                // console.log(array);
                $("#tareas").html(array[0]);
                if (array[2] > num_registros) {
                    $("#pagination").html(array[1]);
                } else {
                    $("#pagination").html("");
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarTarea() {
        let cont_errores = 0;
        let tarea = $("#tarea").val().trim();
        let pagina_actual = $("#pagina_actual").val();
        let filtro = $("#filtro").val();

        if (tarea == "") {
            $("#mensaje1").html("Debe ingresar la nueva tarea...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "por_hacer/guardar_tarea.php",
                data: {
                    tarea: tarea
                },
                dataType: "json",
                success: function(data) {
                    paginarTareas(1, num_registros_paginacion, filtro);

                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevaTareaModal").modal('hide');

                    console.log(data)

                    Swal.fire({
                        title: data.titulo,
                        text: data.mensaje,
                        icon: data.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "por_hacer/obtener_tarea.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                // console.log(r);
                $("#id_tarea").val(r.id);
                $("#tareau").val(r.tarea);
            }
        });
    }

    function eliminarTarea(id) {
        let pagina_actual = $("#pagina_actual").val();
        let filtro = $("#filtro").val();

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
                    url: "por_hacer/delete_tarea.php",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });

                        paginarTareas(pagina_actual, num_registros_paginacion, filtro);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }

    function checkTask(obj, id) {
        var done = obj.checked;
        let filtro = $("#filtro").val();
        $.ajax({
            type: "POST",
            url: "por_hacer/actualizar_hecho.php",
            data: "id=" + id + "&done=" + done,
            success: function(resultado) {
                paginarTareas(1, num_registros_paginacion, filtro); //Para refrescar la lista de tareas
            }
        });
    }
</script>