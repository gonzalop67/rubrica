<style>
    .header2 {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10pt;
        background-color: #0066FF;
        color: #fff;
        text-align: center;
        padding-top: 1px;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Reporte de Promociones</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="formulario_promocion" action="<?php echo RUTA_URL ?>/promociones/certificado" method="post" target="_blank" class="form-horizontal">
                <input id="id_paralelo" name="id_paralelo" type="hidden" />
                <input id="id_estudiante" name="id_estudiante" type="hidden" />
                <div class="form-group">
                    <label for="cboParalelos" class="col-sm-2 control-label text-right">Paralelo:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="cboParalelos" id="cboParalelos" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-solid">
        <div id="pag_nomina_estudiantes">
            <!-- Aqui va la paginacion de los estudiantes encontrados -->
            <div id="total_registros_estudiantes" class="paginacion">
                <div id="num_estudiantes"></div>
            </div>
            <div id="pag_estudiantes">
                <!-- Aqui va la paginacion de los estudiantes encontrados -->
                <div class="header2"> LISTA DE ESTUDIANTES MATRICULADOS </div>
                <div id="tabla" class="table-responsive">
                    <div id="lista_estudiantes" style="text-align:center">Debe elegir un paralelo...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="mensaje" class="error"></div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $("#cboParalelos").change(function(e) {
            e.preventDefault();
            document.getElementById("id_paralelo").value = $(this).val();
            if ($(this).val() == 0) {
                $("#num_estudiantes").html("");
                $("#lista_estudiantes").html("Debe elegir un paralelo...");
            } else
                contarEstudiantesParalelo($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
        });
    });

    function contarEstudiantesParalelo(id_paralelo) {
        $.post("<?php echo RUTA_URL; ?>/calificaciones/contarEstudiantesParalelo", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                console.log(resultado);
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: " + resultado);
                    if (parseInt(resultado) == 0) {
                        $("#paginacion_estudiantes").html("");
                        $("#lista_estudiantes").html("No existen estudiantes matriculados en este paralelo...");
                    } else {
                        listarEstudiantesParalelo(id_paralelo);
                    }
                }
            }
        );
    }

    function listarEstudiantesParalelo(id_paralelo) {
        var id_paralelo = document.getElementById("cboParalelos").value;
        $("#lista_estudiantes").html("<img src='<?php echo RUTA_URL ?>/public/img/ajax-loader-blue.GIF' alt='Procesando...'>");
        $.post("<?php echo RUTA_URL; ?>/promociones/listarEstudiantesPromocion", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                    $("#lista_estudiantes").html('Error...');
                } else {
                    // console.log(resultado);
                    $("#lista_estudiantes").html(resultado);
                }
            }
        );
    }

    function seleccionarEstudiante(id_estudiante) {
        var id_paralelo = document.getElementById("cboParalelos").value;
        var es_intensivo = 0;
        document.getElementById("id_paralelo").value = id_paralelo;
        document.getElementById("id_estudiante").value = id_estudiante;
        document.getElementById("formulario_promocion").submit();
    }
</script>