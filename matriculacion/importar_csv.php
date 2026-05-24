<?php
include_once("../scripts/clases/class.mysql.php");

$db = new MySQL();

function existeNroCedula($es_cedula)
{
    global $db;

    $consulta = $db->consulta("SELECT * FROM sw_estudiante WHERE es_cedula = '$es_cedula'");

    return $db->num_rows($consulta) > 0;
}

function existeNombreEstudiante($apellidos, $nombres)
{
    global $db;

    $consulta = $db->consulta("SELECT * FROM sw_estudiante WHERE es_apellidos = '$apellidos' AND es_nombres = '$nombres'");

    return $db->num_rows($consulta) > 0;
}

function obtenerIdEstudiante($es_cedula)
{
    global $db;

    $consulta = $db->consulta("SELECT id_estudiante FROM sw_estudiante WHERE es_cedula = '$es_cedula'");

    return $db->fetch_object($consulta)->id_estudiante;
}

function existeEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo)
{
    global $db;

    $consulta = $db->consulta("SELECT * FROM sw_estudiante_periodo_lectivo WHERE id_estudiante = $id_estudiante AND id_periodo_lectivo = $id_periodo_lectivo");

    return $db->num_rows($consulta) > 0;
}

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_paralelo = $_POST["id_paralelo"];

$file = $_FILES['student_file']['tmp_name'];

$first = false;
$file = fopen($file, 'r');

$contador = 0;

while ($row = fgetcsv($file)) {
    if (!$first) {
        $first = true;
    } else {
        $td_nombre = trim($row[0]);     // Tipo de documento
        $es_cedula = trim($row[1]);     // Nro. de doc. de identificación
        $dn_nombre = trim($row[2]);     // Nacionalidad
        $es_apellidos = trim($row[3]);  // Apellidos
        $es_nombres = trim($row[4]);    // Nombres
        $dg_nombre = trim($row[5]);     // Género
        $es_email = trim($row[6]);      // Email
        $es_sector = trim($row[7]);     // Sector
        $es_direccion = trim($row[8]);  // Dirección
        $es_telefono = trim($row[9]);   // Teléfono(s)
        $es_fec_nacim = trim($row[10]); // Fecha de nacimiento

        if (!existeNroCedula($es_cedula) && !existeNombreEstudiante($es_apellidos, $es_nombres)) {
            // Obtener el id_tipo_documento
            try {
                $consulta = $db->consulta("SELECT id_tipo_documento FROM sw_tipo_documento WHERE td_nombre = '$td_nombre'");

                $id_tipo_documento = $db->fetch_object($consulta)->id_tipo_documento;

                $consulta = $db->consulta("SELECT id_def_genero FROM sw_def_genero WHERE dg_nombre = '$dg_nombre'");

                $id_def_genero =  $db->fetch_object($consulta)->id_def_genero;

                $consulta = $db->consulta("SELECT id_def_nacionalidad FROM sw_def_nacionalidad WHERE dn_nombre = '$dn_nombre'");

                $id_def_nacionalidad = $db->fetch_object($consulta)->id_def_nacionalidad;

                // Insertar el nuevo estudiante
                $qry = "INSERT INTO sw_estudiante
                                SET id_tipo_documento = $id_tipo_documento,
                                    id_def_genero = $id_def_genero,
                                    id_def_nacionalidad = $id_def_nacionalidad,
                                    es_apellidos = '$es_apellidos',
                                    es_nombres = '$es_nombres',
                                    es_nombre_completo = '$es_apellidos $es_nombres',
                                    es_cedula = '$es_cedula',
                                    es_email = '$es_email',
                                    es_sector = '$es_sector',
                                    es_direccion = '$es_direccion',
                                    es_telefono = '$es_telefono',
                                    es_fec_nacim = '$es_fec_nacim'";

                $consulta = $db->consulta($qry);

                // Se obtiene el id_estudiante del registro insertado
                $qry = "SELECT MAX(id_estudiante) AS lastInsertId FROM sw_estudiante";
                $consulta = $db->consulta($qry);
                $lastInsertId = $db->fetch_object($consulta)->lastInsertId;

                // Se obtiene en el número de matrícula
                $qry = "SELECT MAX(nro_matricula) AS lastNroMatricula FROM sw_estudiante_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
                $consulta = $db->consulta($qry);
                $lastNroMatricula = ($db->num_rows($consulta) == 0) ? 1 : $db->fetch_object($consulta)->lastNroMatricula + 1;

                // Se matricula al estudiante en el paralelo y periodo lectivo elegidos
                $qry = "INSERT INTO sw_estudiante_periodo_lectivo 
                                SET id_estudiante = $lastInsertId, 
                                    id_periodo_lectivo = $id_periodo_lectivo, 
                                    id_paralelo = $id_paralelo, 
                                    es_estado = 'N', 
                                    es_retirado = 'N', 
                                    nro_matricula = $lastNroMatricula, 
                                    activo = 1";

                $consulta = $db->consulta($qry);

                $contador++;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            // Ya existe el estudiante en la Base de Datos
            $id_estudiante = obtenerIdEstudiante($es_cedula);

            if (!existeEstudiantePeriodoLectivo($id_estudiante, $id_periodo_lectivo)) {
                try {
                    // Se obtiene en el número de matrícula
                    $qry = "SELECT MAX(nro_matricula) AS lastNroMatricula FROM sw_estudiante_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
                    $consulta = $db->consulta($qry);
                    $lastNroMatricula = ($db->num_rows($consulta) == 0) ? 1 : $db->fetch_object($consulta)->lastNroMatricula + 1;

                    // Se matricula al estudiante en el paralelo y periodo lectivo elegidos
                    $qry = "INSERT INTO sw_estudiante_periodo_lectivo 
                                    SET id_estudiante = $id_estudiante, 
                                        id_periodo_lectivo = $id_periodo_lectivo, 
                                        id_paralelo = $id_paralelo, 
                                        es_estado = 'A', 
                                        es_retirado = 'N', 
                                        nro_matricula = $lastNroMatricula, 
                                        activo = 1";

                    $consulta = $db->consulta($qry);

                    // Actualizar los datos del estudiante
                    $qry = "UPDATE sw_estudiante 
                               SET es_apellidos = '$es_apellidos', 
                                   es_nombres = '$es_nombres', 
                                   es_nombre_completo = '$es_apellidos $es_nombres', 
                                   es_email = '$es_email',
                                   es_sector = '$es_sector',
                                   es_direccion = '$es_direccion',
                                   es_telefono = '$es_telefono' 
                             WHERE id_estudiante = $id_estudiante";

                    $consulta = $db->consulta($qry);

                    $contador++;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}

fclose($file);

echo $contador;
