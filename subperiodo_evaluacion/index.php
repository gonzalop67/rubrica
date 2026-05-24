<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sub Períodos de Evaluación
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoSubPeriodoModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <!-- table -->
                        <table class="table fuente9">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Abreviatura</th>
                                    <th colspan=2>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="sub_periodos">
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
        cargarSubPeriodosEvaluacion();
    });

    function cargarSubPeriodosEvaluacion() {
        // Obtener todos los subperiodos de evaluación ingresados en la base de datos
        $.ajax({
            url: "subperiodo_evaluacion/cargar_subperiodos_evaluacion.php",
            method: "GET",
            type: "html",
            success: function(response) {
                $("#sub_periodos").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }

    function insertarSubperiodo() {
        let cont_errores = 0;
        const nombre = $("#nombre").val();
        const abreviatura = $("#abreviatura").val();
        const tipo_periodo = $("#tipo_periodo").val();

        if (nombre == "") {
            $("#error-nombre").html("Debe ingresar el nombre del subperiodo de evaluación...");
            $("#error-nombre").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombre").fadeOut();
        }

        if (abreviatura == "") {
            $("#error-abreviatura").html("Debe ingresar la abreviatura del subperiodo de evaluación...");
            $("#error-abreviatura").fadeIn();
            cont_errores++;
        } else {
            $("#error-abreviatura").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                url: "subperiodo_evaluacion/insertar_sub_periodo.php",
                method: "POST",
                type: "html",
                data: {
                    nombre: nombre,
                    abreviatura: abreviatura,
                    tipo_periodo: tipo_periodo
                },
                dataType: "json",
                success: function(response) {
                    cargarSubPeriodosEvaluacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoSubPeriodoModal").modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }

        return false;
    }

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "subperiodo_evaluacion/obtener_sub_periodo.php",
            type: "POST",
            data: "id_sub_periodo_evaluacion=" + id,
            success: function(result) {
                var r = JSON.parse(result);
                $("#id_sub_periodo_evaluacion").val(r.id_sub_periodo_evaluacion);
                $("#nombreu").val(r.pe_nombre);
                $("#abreviaturau").val(r.pe_abreviatura);

                setearIndice("tipo_periodou", r.id_tipo_periodo);
            }
        });
    }

    $('#form_update').submit(function(e) {
        e.preventDefault();

        let cont_errores = 0;

        let id_sub_periodo_evaluacion = $("#id_sub_periodo_evaluacion").val().trim();
        let nombre = $("#nombreu").val().trim();
        let abreviatura = $("#abreviaturau").val();
        let id_tipo_periodo = $("#tipo_periodou").val();

        if (nombre == "") {
            $("#error-nombreu").html("Debe ingresar el nombre del subperiodo de evaluación...");
            $("#error-nombreu").fadeIn();
            cont_errores++;
        } else {
            $("#error-nombreu").fadeOut();
        }

        if (abreviatura == "") {
            $("#error-abreviaturau").html("Debe ingresar la abreviatura del subperiodo de evaluación...");
            $("#error-abreviaturau").fadeIn();
            cont_errores++;
        } else {
            $("#error-abreviaturau").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "subperiodo_evaluacion/actualizar_sub_periodo.php",
                data: {
                    id_sub_periodo_evaluacion: id_sub_periodo_evaluacion,
                    pe_nombre: nombre,
                    pe_abreviatura: abreviatura,
                    id_tipo_periodo: id_tipo_periodo
                },
                dataType: "json",
                success: function(response) {
                    cargarSubPeriodosEvaluacion();
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.estado,
                        confirmButtonText: 'Aceptar'
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarSubperiodoModal").modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Otro manejador error
                    console.log(jqXHR.responseText);
                }
            });
        }
    });

    function eliminarSubPeriodo(id) {
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
                    url: "subperiodo_evaluacion/eliminar_sub_periodo.php",
                    data: {
                        id_sub_periodo_evaluacion: id
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

                        cargarSubPeriodosEvaluacion();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>