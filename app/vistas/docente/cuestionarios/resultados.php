<?php
function truncar($numero, $digitos)
{
    $truncar = pow(10, $digitos);
    return intval($numero * $truncar) / $truncar;
}

date_default_timezone_set('America/Guayaquil');
setlocale(LC_ALL, "es_ES");

$date = date("Y-m-d H:i.s");
$_SESSION["end_time"] = date("Y-m-d H:i:s", strtotime($date . "+$_SESSION[exam_time] minutes"));

$preguntaModelo = $this->modelo('Pregunta');
$id_category = $_SESSION["id_category"];

$categoriaModelo = $this->modelo('Categoria');
$nombreCategoria = $categoriaModelo->obtenerNombreCategoria($id_category);

$count = 0;
$count = $preguntaModelo->obtenerNumeroPreguntasPorCategoria($id_category);
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h3 id="titulo">
        <?php echo $nombreCategoria; ?>
    </h3>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-solid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">

                <div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">
                    <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: white;">
                        <?php
                        $correct = 0;
                        $wrong = 0;

                        if (isset($_SESSION["answer"])) {
                            for ($i = 1; $i <= sizeof($_SESSION["answer"]); $i++) {
                                $answer = "";
                                $pregunta = $preguntaModelo->obtenerPreguntaPorNumeroPregunta($id_category, $i);
                                $answer = $pregunta->id;

                                if (isset($_SESSION["answer"][$i])) {
                                    if ($answer == $_SESSION["answer"][$i]) {
                                        $correct = $correct + 1;
                                    } else {
                                        $wrong = $wrong + 1;
                                    }
                                } else {
                                    $wrong = $wrong + 1;
                                }
                            }
                        }

                        $count = 0;
                        $count = $preguntaModelo->obtenerNumeroPreguntasPorCategoria($id_category);
                        $wrong = $count - $correct;
                        echo "<br>";
                        echo "<br>";
                        echo "<center>";
                        echo "Total de Preguntas = " . $count;
                        echo "<br>";
                        echo "Respuestas Correctas = " . $correct;
                        echo "<br>";
                        echo "Respuestas Incorrectas = " . $wrong;
                        echo "<br>";
                        echo "<br>";
                        $calificacion = $correct / $count * 10;
                        echo "Su calificaci&oacute;n = " . truncar($calificacion, 2);
                        echo "</center>";
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php
if (isset($_SESSION["exam_start"])) {
    $date = date("Y-m-d H:i:s");
    //mysqli_query($link, "INSERT INTO exam_results(id,username,exam_type,total_question,correct_answer,wrong_answer,exam_time) VALUES(null,'$_SESSION[username]','$_SESSION[exam_category]','$count','$correct','$wrong','$date')") or die(mysqli_error($link));
}

if (isset($_SESSION["exam_start"])) {
    unset($_SESSION["exam_start"]);
    unset($_SESSION["end_time"]);
    //header("Location: result.php");
}

if (isset($_SESSION["answer"])) {
    for ($i = 1; $i <= $count; $i++) {
        unset($_SESSION["answer"][$i]);
    }
}
?>