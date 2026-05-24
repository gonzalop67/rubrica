<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sub Niveles de educación
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoNivelEducacionModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
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
                                    <th>¿Es Bachillerato?</th>
                                    <th colspan=2>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="niveles_educacion">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
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
        cargarNivelesEducacion();

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
        // Obtengo todas los perfiles ingresados en la base de datos
        id_periodo_lectivo = $("#id_periodo_lectivo").val();
        $.ajax({
            type: "POST",
            url: "tipo_educacion/cargar_tipos_de_educacion.php",
            data: {
                id_periodo_lectivo: id_periodo_lectivo
            },
            dataType: "html",
            success: function (response) {
                // console.log(response);
                $("#niveles_educacion").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarNivelEducacion() {
        let cont_errores = 0;
        const te_nombre = $("#te_nombre").val();
        const te_bachillerato = $("#te_bachillerato").val();
        const id_periodo_lectivo = $("#id_periodo_lectivo").val(); 

        if (te_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre del nivel de educación...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "tipo_educacion/insertar_nivel_educacion.php",
                method: "POST",
                type: "html",
                data: {
                    te_nombre: te_nombre,
                    te_bachillerato: te_bachillerato,
                    id_periodo_lectivo: id_periodo_lectivo
                },
                dataType: "json",
                success: function(response) {
                    cargarNivelesEducacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoNivelEducacionModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "tipo_educacion/obtener_tipo_educacion.php",
            type: "POST",
            data: "id_tipo_educacion=" + id,
            success: function(result) {
                var r = JSON.parse(result);
                $("#id_tipo_educacion").val(r.id_tipo_educacion);
                $("#te_nombreu").val(r.te_nombre);

                var obj = document.getElementById("te_bachilleratou");

                for (var opcombo = 0; opcombo < obj.length; opcombo++) {
                    if (obj[opcombo].value == r.te_bachillerato) {
                        obj.selectedIndex = opcombo;
                    }
                }
            }
        });
    }

    $('#form_update').submit(function(e) {
        e.preventDefault();

        let cont_errores = 0;

        let id_tipo_educacion = $("#id_tipo_educacion").val().trim();
        let te_nombre = $("#te_nombreu").val().trim();
        let te_bachillerato = $("#te_bachilleratou").val();

        if (te_nombre == "") {
            $("#mensaje2").html("Debe ingresar el nivel de educación...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "tipo_educacion/actualizar_nivel_educacion.php",
                data: {
                    id_tipo_educacion: id_tipo_educacion,
                    te_nombre: te_nombre,
                    te_bachillerato: te_bachillerato
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    cargarNivelesEducacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarNivelEducacionModal").modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Otro manejador error
                    console.log(jqXHR.responseText);
                }
            });
        }
    });

    function eliminarNivelEducacion(id) {
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
                    url: "tipo_educacion/eliminar_tipo_educacion.php",
                    data: {
                        id_tipo_educacion: id
                    },
                    dataType: "json",
                    success: function(data) {
                        // console.log(data)
                        Swal.fire({
                            title: data.titulo,
                            text: data.mensaje,
                            icon: data.estado,
                            confirmButtonText: 'Aceptar'
                        });

                        cargarNivelesEducacion();
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

        $.ajax({
            url: "tipo_educacion/saveNewPositions.php",
            method: 'POST',
            dataType: 'text',
            data: {
                positions: positions
            },
            success: function(response) {
                console.log(response);
                cargarNivelesEducacion();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>