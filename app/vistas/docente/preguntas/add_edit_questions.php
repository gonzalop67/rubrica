<?php
$categoriaModelo = $this->modelo('Categoria');
$id = $datos['id_categoria'];
$exam_category = '';
$exam_category = $categoriaModelo->obtenerNombreCategoria($id);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h3>
        Añadir Preguntas dentro de <span style="color: red"><?php echo $exam_category ?></span>
    </h3>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-body">
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
                        <div class="col-lg-6">
                            <form action="<?php echo RUTA_URL ?>/preguntas/insert" method="post">
                                <input type="hidden" name="id_category" value="<?php echo $id ?>">
                                <input type="hidden" name="exam_category" value="<?php echo $exam_category ?>">
                                <div class="panel">
                                    <div class="panel-body">
                                        <strong>Añadir Nuevas Preguntas con texto</strong>
                                        <hr>
                                        <div class="form-group">
                                            <label for="question">Pregunta:</label>
                                            <textarea name="question" id="question" cols="30" rows="3" class="form-control" placeholder="Añadir una pregunta" autofocus required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt1">Opción 1:</label>
                                            <textarea name="opt1" id="opt1" cols="30" rows="3" class="form-control" placeholder="Añadir opción 1" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt2">Opción 2:</label>
                                            <textarea name="opt2" id="opt2" cols="30" rows="3" class="form-control" placeholder="Añadir opción 2" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt3">Opción 3:</label>
                                            <textarea name="opt3" id="opt3" cols="30" rows="3" class="form-control" placeholder="Añadir opción 3" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="answer">Respuesta:</label>
                                            <textarea name="answer" id="answer" cols="30" rows="3" class="form-control" placeholder="Añadir respuesta" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="submit1" class="btn btn-success" value="Añadir Pregunta">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pregunta</th>
                                    <th>Opc1</th>
                                    <th>Opc2</th>
                                    <th>Opc3</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_questions">
                                <!-- Aqui vamos a poblar las preguntas ingresadas en la BDD mediante AJAX  -->
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
        pagination(1);
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        var url = "<?php echo RUTA_URL; ?>/preguntas/paginar/<?php echo $id ?>";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_questions").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }
</script>