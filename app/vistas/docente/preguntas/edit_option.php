<?php

?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h3>
        Actualizar Pregunta
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
                        <div class="col-lg-12">
                            <form action="<?php echo RUTA_URL ?>/preguntas/update" method="post">
                                <input type="hidden" name="id_question" value="<?php echo $datos['pregunta']->id ?>">
                                <input type="hidden" name="id_category" value="<?php echo $datos['pregunta']->id_category ?>">
                                <div class="panel">
                                    <div class="panel-body">
                                        <strong>Actualizar Preguntas con texto</strong>
                                        <hr>
                                        <div class="form-group">
                                            <label for="question">Pregunta:</label>
                                            <textarea name="question" id="question" cols="30" rows="3" class="form-control" placeholder="Añadir una pregunta" autofocus required><?php echo $datos['pregunta']->question ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt1">Opción 1:</label>
                                            <textarea name="opt1" id="opt1" cols="30" rows="3" class="form-control" placeholder="Añadir opción 1" required><?php echo $datos['pregunta']->opt1 ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt2">Opción 2:</label>
                                            <textarea name="opt2" id="opt2" cols="30" rows="3" class="form-control" placeholder="Añadir opción 2" required><?php echo $datos['pregunta']->opt2 ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="opt3">Opción 3:</label>
                                            <textarea name="opt3" id="opt3" cols="30" rows="3" class="form-control" placeholder="Añadir opción 3" required><?php echo $datos['pregunta']->opt3 ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="answer">Respuesta:</label>
                                            <textarea name="answer" id="answer" cols="30" rows="3" class="form-control" placeholder="Añadir respuesta" required><?php echo $datos['pregunta']->answer ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="submit1" class="btn btn-success" value="Actualizar Pregunta">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
