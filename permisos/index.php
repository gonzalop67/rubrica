<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="min-height: 100vh;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Permisos
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
                                    <th>Slug</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_permisos">
                                <!-- Aquí se van a poblar los permisos ingresados en la base de datos -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Nuevo Permiso</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-permisos" action="" method="post">
                                <input type="hidden" name="id_permiso" id="id_permiso" value="0">
                                <div class="form-group">
                                    <label for="nombre">Nombre:</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" autofocus required>
                                </div>
                                <div class="form-group">
                                    <label for="slug">Slug:</label>
                                    <input type="text" name="slug" id="slug" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción:</label>
                                    <input type="text" name="descripcion" id="descripcion" class="form-control" required>
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
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        var url = "permisos/paginar_permisos.php";
        $.ajax({
            type: 'POST',
            url: url,
            data: 'partida=' + partida,
            success: function(data) {
                var array = eval(data);
                $("#tbody_permisos").html(array[0]);
                $("#pagination").html(array[1]);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    }
</script>