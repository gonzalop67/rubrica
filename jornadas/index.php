<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Jornadas
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevaJornadaModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
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
                                    <th colspan=2>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="jornadas">
                                <!-- Aqui desplegamos el contenido de la base de datos -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // JQuery Listo para utilizar
        cargarJornadas();
    });

    function cargarJornadas() {
        // Obtener todos las jornadas ingresadas en la base de datos
        $.ajax({
            url: "jornadas/cargar_jornadas.php",
            method: "GET",
            type: "html",
            success: function(response) {
                $("#jornadas").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
</script>