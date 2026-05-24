<!-- Content Header (Page header) -->
<section class="content-header">
    <h4 id="titulo">
        <!-- Aquí va el nombre de la Categoría obtenido mediante AJAX -->
        Seleccione el cuestionario:
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
    load_categories();

    function load_categories() {
        // Aquí se va a poblar el listado de categorías mediante AJAX
        $.ajax({
            url: "./cargar_categorias.php",
            type: "POST",
            data: "id_estudiante=" + <?php echo $id_estudiante; ?>,
            dataType: "html",
            success: function(r) {
                // console.log(r);
                $("#load_info").html(r);
            }
        });
    }

    function set_exam_type_session(id_category) {
        $("#id_category").val(id_category);
        $.ajax({
            url: "../cuestionarios/set_exam_type_session.php",
            type: "POST",
            data: "id_category=" + id_category,
            success: function(r) {
                console.log(r);
                // load_questions(1);
            }
        });
    }
</script>