<?php
$id_usuario = $_SESSION["id_usuario"];
/* $categoriaModelo = $this->modelo('Categoria');
$categorias = $categoriaModelo->obtenerCategoriasPorUsuario($id_usuario); */
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h3 id="titulo">
        <!-- Aquí va el nombre de la Categoría obtenido mediante AJAX -->
    </h3>
    <ol class="breadcrumb">
        <li>
            <div id="countdowntimer" style="font-size: 14px;">00:00:00</div>
        </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-solid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <input type="hidden" id="id_category">
                <div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">
                    <div id="load_info" class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: white;">
                        <!-- Aqui se va a poblar la información mediante AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    setInterval(function() {
        timer();
    }, 1000);

    function timer() {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/load_timer",
            type: "GET",
            success: function(r) {
                console.log(r);
                if (r == "00:00:01") {
                    window.location = "<?php echo RUTA_URL; ?>/cuestionarios/resultados";
                }
                document.getElementById("countdowntimer").innerHTML = r;
            }
        });
    }

    load_categories();

    function load_categories() {
        // Aquí se va a poblar el listado de categorías mediante AJAX
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/categorias/cargar",
            type: "POST",
            data: "id_usuario=" + <?php echo $id_usuario; ?>,
            dataType: "html",
            success: function(r) {
                //console.log(r);
                $("#load_info").html(r);
            }
        });
    }

    function set_exam_type_session(id_category) {
        $("#id_category").val(id_category);
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/set_exam_type_session",
            type: "POST",
            data: "id_category=" + id_category,
            success: function(r) {
                load_questions(1);
            }
        });
    }

    function radioclick(radiovalue, questionno) {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/save_answer_in_session",
            type: "POST",
            data: {
                questionno: questionno,
                value1: radiovalue
            },
            success: function(r) {
                console.log(r);
            }
        });
    }

    function load_questions(questionno) {
        id_category = $("#id_category").val();
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/obtenerNombreCategoria",
            type: "POST",
            data: {
                id_category: id_category
            },
            success: function(r) {
                console.log(r);
                $("#titulo").html(r);
            }
        });
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/cuestionarios/load_question",
            type: "POST",
            data: {
                questionno: questionno,
                id_category: id_category
            },
            success: function(r) {
                console.log(r);
                if (r == "over") {
                    window.location = "<?php echo RUTA_URL; ?>/cuestionarios/resultados";
                } else {
                    document.getElementById("load_info").innerHTML = r;
                }
            }
        });
    }

    function load_previous(questionno) {
        if (questionno == "1") {
            load_questions(questionno);
        } else {
            questionno = eval(questionno) - 1;
            load_questions(questionno);
        }
    }

    function load_next(questionno) {
        questionno = eval(questionno) + 1;
        load_questions(questionno);
    }
</script>