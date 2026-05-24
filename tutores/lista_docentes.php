    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h2>
                <div id="titulo">
                    <!-- Aqui va el titulo de la pagina -->
                </div>
            </h2>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">
            <!-- Default box -->
            <div class="box box-info">
                <div class="box-body">
                    <div class="row">
                        <div id="listaDocentesApp" class="col-sm-12">
                            <!-- form -->
                            <div class="panel panel-default">
                                <table class="table fuente9">
                                    <thead>
                                        <tr>
                                            <th>Nro.</th>
                                            <th>Asignatura</th>
                                            <th>Docente</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista_docentes">
                                        <!-- Aqui desplegamos el contenido de la base de datos -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {

            $("#id_paralelo").val($("#id_paralelo_tutor").val());

            // Obtengo el nombre del curso para desplegar en el titulo
            $.ajax({
                url: "tutores/obtener_nombre_paralelo.php",
                data: {
                    id_paralelo: $("#id_paralelo_tutor").val()
                },
                method: "POST",
                type: "html",
                success: function(response) {
                    $("#titulo").html("Lista de Docentes de " + response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });

            // Luego obtengo la lista de docentes del paralelo
            $.ajax({
                url: "tutores/listar_docentes_paralelo.php",
                data: {
                    id_paralelo: $("#id_paralelo_tutor").val()
                },
                method: "POST",
                type: "html",
                success: function(response) {
                    //console.log(response);
                    $("#lista_docentes").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });

        });
    </script>