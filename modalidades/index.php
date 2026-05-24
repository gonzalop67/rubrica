<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Modalidades
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 table-responsive">
                        <hr>
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_modalidades">
                                <!-- Aquí se van a poblar los modalidades ingresados en la base de datos -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Nueva Modalidad</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-modalidad" action="" method="post">
                                <input type="hidden" name="id_modalidad" id="id_modalidad" value="0">
                                <div class="form-group">
                                    <label for="mo_nombre">Nombre:</label>
                                    <input type="text" name="mo_nombre" id="mo_nombre" class="form-control" autofocus>
                                </div>
                                <div class="form-group">
                                    <label for="mo_activo">Activo:</label>
                                    <input type="checkbox" name="mo_activo" id="mo_activo" style="margin-top: 4px;">
                                </div>
                                <div class="form-group">
                                    <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                    <button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
                                </div>
                            </form>
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

<script>
    $(document).ready(function() {

        cargarModalidades();

        $("#btn-cancel").click(function() {
            $("#frm-modalidad")[0].reset();
            $("#titulo").html("Nueva Modalidad");
            $("#btn-save").html("Guardar");
            $("#mo_nombre").focus();
        });

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

        $('#tbody_modalidades').on('click', '.item-edit', function() {
            var id_modalidad = $(this).attr('data');
            $("#titulo").html("Editar Modalidad");
            $("#btn-save").html("Actualizar");
            $.ajax({
                url: "modalidades/obtener_modalidad.php",
                data: {
                    id_modalidad: id_modalidad
                },
                method: "POST",
                dataType: "json",
                success: function(data) {
                    $("#id_modalidad").val(id_modalidad);
                    $("#mo_nombre").val(data.mo_nombre);
                    document.getElementById("mo_activo").checked = (data.mo_activo == 1) ? true : false;
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        $('#tbody_modalidades').on('click', '.item-delete', function() {
            var id_modalidad = $(this).attr('data');

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
                        url: "modalidades/eliminar_modalidad.php",
                        data: {
                            id_modalidad: id_modalidad
                        },
                        dataType: "json",
                        success: function(data) {
                            Swal.fire({
                                title: data.titulo,
                                text: data.mensaje,
                                icon: data.tipo_mensaje,
                                confirmButtonText: 'Aceptar'
                            });

                            cargarModalidades();
                            $("#frm-modalidad")[0].reset();

                            if ($("#btn-save").html() == "Actualizar") {
                                $("#btn-save").html("Guardar");
                                $("#titulo").html("Nueva Modalidad");
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            });
        });

        $("#frm-modalidad").submit(function(e) {
            e.preventDefault();
            var url;
            var id_modalidad = $("#id_modalidad").val();
            var mo_nombre = $.trim($("#mo_nombre").val());
            var mo_activo = document.getElementById("mo_activo").checked ? 1 : 0;

            if (mo_nombre == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el nombre de la modalidad.", "error");
            } else {

                if ($("#btn-save").html() == "Guardar")
                    url = "modalidades/insertar_modalidad.php";
                else if ($("#btn-save").html() == "Actualizar")
                    url = "modalidades/actualizar_modalidad.php";

                $.ajax({
                    url: url,
                    method: "post",
                    data: {
                        id_modalidad: id_modalidad,
                        mo_nombre: mo_nombre,
                        mo_activo: mo_activo
                    },
                    dataType: "json",
                    success: function(response) {

                        Swal.fire({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });

                        cargarModalidades();
                        $("#frm-modalidad")[0].reset();

                        if ($("#btn-save").html() == "Actualizar") {
                            $("#btn-save").html("Guardar");
                            $("#titulo").html("Nueva Modalidad");
                        }

                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            }
        });
    });

    function cargarModalidades() {
        var url = "modalidades/cargar_modalidades.php";
        $.ajax({
            url: url,
            success: function(data) {
                $("#tbody_modalidades").html(data);
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        })
    }

    function saveNewPositions() {
        var positions = [];
        $('.updated').each(function() {
            positions.push([$(this).attr('data-index'), $(this).attr('data-orden')]);
            $(this).removeClass('updated');
        });

        $.ajax({
            url: "modalidades/saveNewPositions.php",
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