<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="min-height: 100vh;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Perfiles
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
                        <table id="t_perfiles" class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_perfiles">
                                <!-- Aquí se van a poblar los perfiles ingresados en la base de datos -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Nuevo Perfil</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-perfiles" action="" method="post">
                                <input type="hidden" name="id_perfil" id="id_perfil" value="0">
                                <div class="form-group">
                                    <label for="pe_nombre">Nombre:</label>
                                    <input type="text" name="pe_nombre" id="pe_nombre" class="form-control" autofocus>
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
<!-- /.content-wrapper -->
<script>
    $(document).ready(function() {
        pagination(1);

        $("#btn-cancel").click(function() {
            $("#frm-perfiles")[0].reset();
            $("#titulo").html("Nuevo Perfil");
            $("#btn-save").html("Guardar");
            $("#pe_nombre").focus();
        });

        $('#tbody_perfiles').on('click', '.item-edit', function() {
            var id_perfil = $(this).attr('data');
            $("#titulo").html("Editar Perfil");
            $("#btn-save").html("Actualizar");
            $.ajax({
                url: "perfiles/obtener_perfiles.php",
                data: {
                    id: id_perfil
                },
                method: "POST",
                dataType: "json",
                success: function(data) {
                    $("#id_perfil").val(id_perfil);
                    $("#pe_nombre").val(data.pe_nombre);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        });

        $('#tbody_perfiles').on('click', '.item-delete', function() {
            var id_perfil = $(this).attr('data');
            // Swal.fire({
            //     title: "¿Está seguro que quiere eliminar el registro?",
            //     text: "No podrá recuperar el registro que va a ser eliminado!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#d33",
            //     cancelButtonColor: "#3085d6",
            //     confirmButtonText: "Sí, elimínelo!"
            // }
            Swal.fire({
                title: "¿Está seguro que quiere eliminar el registro?",
                text: "No podrá recuperar el registro que va a ser eliminado!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, ¡elimínelo!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Swal.fire({
                    //     title: "Deleted!",
                    //     text: "Your file has been deleted.",
                    //     icon: "success"
                    // });
                    $.ajax({
                        url: "perfiles/eliminar_perfil.php",
                        data: {
                            id: id_perfil
                        },
                        method: "post",
                        dataType: "json",
                        success: function(data) {
                            Swal.fire({
                                title: data.titulo,
                                text: data.mensaje,
                                icon: data.tipo_mensaje
                            });

                            var pagina_actual = $("#pagina_actual").val();
                            pagination(pagina_actual);

                            $("#frm-perfiles")[0].reset();

                            if ($("#btn-save").html() == "Actualizar") {
                                $("#btn-save").html("Guardar");
                                $("#titulo").html("Nuevo Perfil");
                            }
                        },
                        error: function(jqXHR, textStatus) {
                            alert(jqXHR.responseText);
                        }
                    });
                }
            });

        });

        $("#frm-perfiles").submit(function(e) {
            e.preventDefault();
            var url;
            var id_perfil = $("#id_perfil").val();
            var pe_nombre = $.trim($("#pe_nombre").val());

            if (pe_nombre == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el nombre del perfil.", "error");
            } else {

                if ($("#btn-save").html() == "Guardar")
                    url = "perfiles/insertar_perfil.php";
                else if ($("#btn-save").html() == "Actualizar")
                    url = "perfiles/actualizar_perfil.php";

                $.ajax({
                    url: url,
                    method: "post",
                    data: {
                        id: id_perfil,
                        pe_nombre: pe_nombre
                    },
                    dataType: "json",
                    success: function(response) {

                        Swal.fire({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje
                        });

                        var pagina_actual = $("#pagina_actual").val();
                        pagination(pagina_actual);

                        $("#frm-perfiles")[0].reset();

                        if ($("#btn-save").html() == "Actualizar") {
                            $("#btn-save").html("Guardar");
                            $("#titulo").html("Nuevo Perfil");
                        }

                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            }
        });
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        var url = "perfiles/paginar_perfiles.php";
        $.ajax({
            type: 'POST',
            url: url,
            data: 'partida=' + partida,
            success: function(data) {
                var array = eval(data);
                $("#tbody_perfiles").html(array[0]);
                $("#pagination").html(array[1]);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    }
</script>