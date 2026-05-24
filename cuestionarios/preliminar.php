<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h4 id="titulo">
            <!-- Aquí va el nombre de la Categoría obtenido mediante AJAX -->
            Seleccione la categoria:
        </h4>
        <ol class="breadcrumb">
            <li>
                <div id="countdowntimer" style="font-size: 14px;">00:00:00</div>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <input type="hidden" id="id_category">
                <div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">
                    <div id="load_info" class="col-lg-6 col-lg-push-3" style="min-height: 400px; background-color: white;">
                        <!-- Aqui se va a poblar la información mediante AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    setInterval(function() {
        timer();
    }, 1000);

    function timer() {
        $.ajax({
            url: "cuestionarios/load_timer.php",
            type: "GET",
            success: function(r) {
                // console.log(r);
                if (r == "00:00:01") {
                    window.location = "admin2.php?id_usuario=<?php echo $id_usuario ?>&id_perfil=<?php echo $id_perfil ?>&enlace=cuestionarios/resultados.php&nivel=0";
                }
                document.getElementById("countdowntimer").innerHTML = r;
            }
        });
    }

    load_categories();

    function load_categories() {
        // Aquí se va a poblar el listado de categorías mediante AJAX
        $.ajax({
            url: "cuestionarios/cargar_categorias.php",
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
            url: "cuestionarios/set_exam_type_session.php",
            type: "POST",
            data: "id_category=" + id_category,
            success: function(r) {
                // console.log(r);
                load_questions(1);
            }
        });
    }

    function radioclick(radiovalue, questionno) {
        console.log(radiovalue, questionno);
        $.ajax({
            url: "cuestionarios/save_answer_in_session.php",
            type: "POST",
            data: {
                questionno: questionno,
                value1: radiovalue
            },
            success: function(r) {
                // console.log(r);
            }
        });
    }

    function load_questions(questionno) {
        $("#countdowntimer").show();
        id_category = $("#id_category").val();
        $.ajax({
            url: "cuestionarios/obtenerNombreCategoria.php",
            type: "POST",
            data: {
                id_category: id_category
            },
            success: function(r) {
                // console.log(r);
                $("#titulo").html(r);
            }
        });
        $.ajax({
            url: "cuestionarios/load_question.php",
            type: "POST",
            data: {
                questionno: questionno,
                id_category: id_category
            },
            success: function(r) {
                // console.log(r);
                if (r == "over") {
                    window.location = "admin2.php?id_usuario=<?php echo $id_usuario ?>&id_perfil=<?php echo $id_perfil ?>&enlace=cuestionarios/resultados.php&nivel=0";
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