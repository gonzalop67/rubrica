<?php
include_once "../scripts/clases/class.mysql.php";

$db = new MySQL();

session_start();

$question_no = $_POST['questionno'];
$id_category = $_POST['id_category'];

$query = "SELECT * FROM sw_questions WHERE id_category = $id_category && question_no = $question_no ORDER BY id ASC";
$consulta = $db->consulta($query);

$pregunta = $db->fetch_object($consulta);

$question_no = "";
$question = "";
$ans = "";

$cadena = "";

if (empty($pregunta)) {
    $cadena = "over";
} else {
    $id_question = $pregunta->id;
    $question_no = $pregunta->question_no;
    $question = htmlentities($pregunta->question);

    if (isset($_SESSION["answer"][$question_no])) {
        $ans = $_SESSION["answer"][$question_no];
    }

    $cadena .= "<br>\n";
    $cadena .= "<div class=\"row\">\n"; // begin div.row (1)
    $cadena .= "<br>\n";
    $cadena .= "<div class=\"col-lg-2 col-lg-push-10\">\n"; // begin div.col-lg-2 col-lg-push-10 (1)

    $cadena .= "<div id=\"current_que\" style=\"float: left;\">$question_no</div>\n";
    $cadena .= "<div style=\"float: left;\">&nbsp;/&nbsp;</div>\n";

    $query = "SELECT * FROM sw_questions WHERE id_category = $id_category";
    $consulta = $db->consulta($query);

    $numeroPreguntas = $db->num_rows($consulta);

    $cadena .= "<div id=\"total_que\" style=\"float: left;\">$numeroPreguntas</div>\n";
    $cadena .= "</div>\n"; // end div.col-lg-2 col-lg-push-10 (1)

    $cadena .= "<div class=\"row\">\n"; // begin div.row (2)
    $cadena .= "<div class=\"row\">\n"; // begin div.row (3)
    $cadena .= "<div class=\"col-lg-10 col-lg-push-1\" style=\"min-height: 300px; background-color: white;\" id=\"load_questions\">\n";

    // Aqui van los campos de la pregunta desde la base de datos
    $cadena .= "<br>\n";
    $cadena .= "<table>\n";
    $cadena .= "<tr>\n";
    $cadena .= "<td style=\"font-weight: bold; font-size: 18px; padding-left: 5px;\" colspan=\"2\">\n";
    $cadena .= "( " . $question_no . " ) " . $question . "\n";
    $cadena .= "</td>\n";
    $cadena .= "</tr>\n";
    $cadena .= "</table>\n";

    $cadena .= "<table style=\"margin-left: 10px;\">\n";

    $query = "SELECT * FROM sw_choices WHERE id_question = $id_question";
    $consulta = $db->consulta($query);

    $checked = "";

    while ($choice = $db->fetch_object($consulta)) {
        if ($ans == $choice->id) {
            $checked = "checked";
        }

        $cadena .= "<tr>\n";
        $cadena .= "<td>\n";
        $cadena .= "<input type=\"radio\" name=\"r1\" id=\"r1\" value=\"" . htmlentities($choice->text) . "\" onclick=\"radioclick($choice->id, $question_no)\" $checked>\n";
        $cadena .= "<td style=\"padding-left: 10px;\">\n";
        $cadena .= htmlentities($choice->text) . "\n";
        $cadena .= "</td>\n";
        $cadena .= "</td>\n";
        $cadena .= "</tr>\n";
    }

    $cadena .= "</table>\n";

    $cadena .= "</div>\n"; // end div.col-lg-10 col-lg-push-1 (1)
    $cadena .= "</div>\n"; // end div.row (3)

    $cadena .= "<div class=\"row\" style=\"margin-top: 10px\">\n"; // begin div.row (4)
    $cadena .= "<div class=\"col-lg-6 col-lg-push-3\" style=\"min-height: 50px;\">\n"; // end div.col-lg-6 col-lg-push-3 (1)
    $cadena .= "<div class=\"col-lg-12 text-center\">\n"; // begin div.col-lg-12 text-center (1)
    $cadena .= "<input type=\"button\" class=\"btn btn-warning\" value=\"Anterior\" onclick=\"load_previous($question_no);\">\n";
    $cadena .= "<input type=\"button\" class=\"btn btn-success\" value=\"Siguiente\" onclick=\"load_next($question_no);\">\n";
    $cadena .= "</div>\n"; // end div.col-lg-12 text-center (1)
    $cadena .= "</div>\n"; // end div.col-lg-6 col-lg-push-3 (1)
    $cadena .= "</div>\n"; // end div.row (4)

    $cadena .= "</div>\n"; // end div.row (2)

    $cadena .= "</div>\n"; // end div.row (1)
}

echo $cadena;
