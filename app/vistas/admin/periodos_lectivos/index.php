<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Periodos Lectivos
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
                <a href="<?php echo RUTA_URL . "/periodos_lectivos/create"; ?>" class="btn btn-danger">Crear Periodo Lectivo</a>
                <hr>
                <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                </div>
                <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                <div id="alert-success" class="alert alert-success alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_exito']) ? 'block' : 'none' ?>">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"><?php echo isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : '' ?></span></p>
                </div>
                <?php if (isset($_SESSION['mensaje_exito'])) unset($_SESSION['mensaje_exito']) ?>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="form-group">
                            <label for="id_modalidad">Modalidad:</label>
                            <select name="id_modalidad" id="id_modalidad" class="form-control">
                                <option value="0">Seleccione...</option>
                            </select>
                        </div>
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Id</th>
                                    <th>Año Inicial</th>
                                    <th>Año Final</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_periodos_lectivos">
                                <!-- Aqui vamos a poblar los periodos lectivos ingresados en la BDD mediante AJAX  -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                </div>
            </div>
        </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        cargarModalidades();
        $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");
        $("#id_modalidad").change(function() {
            let id_modalidad = $(this).val();
            if (id_modalidad == 0) {
                $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");
            } else {
                pagination(1, id_modalidad);;
            }
        });
    });

    function cargarModalidades() {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/modalidades/cargar",
            dataType: "html",
            success: function(data) {
                $("#id_modalidad").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function pagination(partida, id_modalidad) {
        $("#pagina_actual").val(partida);
        var url = "<?php echo RUTA_URL; ?>/periodos_lectivos/paginar";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida,
                id_modalidad: id_modalidad
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_periodos_lectivos").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }
</script>