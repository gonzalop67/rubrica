<?php
// 1. Conexión a la base de datos
require_once("scripts/clases/class.mysql.php");
$db = new MySQL;

function cargarParalelosEspecialidad()
{
    global $db;

    // Usamos JOINs explícitos para optimizar la indexación de la base de datos
    $sql = "SELECT e.es_nombre, 
                   e.es_figura, 
                   c.cu_nombre, 
                   p.id_paralelo, 
                   p.pa_nombre, 
                   p.pa_orden, 
                   j.jo_nombre
              FROM sw_paralelo p
              INNER JOIN sw_curso c ON p.id_curso = c.id_curso
              INNER JOIN sw_especialidad e ON c.id_especialidad = e.id_especialidad
              INNER JOIN sw_tipo_educacion t ON e.id_tipo_educacion = t.id_tipo_educacion
              INNER JOIN sw_jornada j ON j.id_jornada = p.id_jornada
             WHERE t.id_periodo_lectivo = 34 
             ORDER BY p.pa_orden ASC";

    $consulta = $db->consulta($sql);
    $num_total_registros = $db->num_rows($consulta);
    $cadena = "";

    if ($num_total_registros > 0) {
        while ($paralelos = $db->fetch_assoc($consulta)) {
            $code = $paralelos["id_paralelo"];

            // Construcción limpia usando interpolación de variables
            $name = "{$paralelos['cu_nombre']} {$paralelos['pa_nombre']} - {$paralelos['es_figura']} - {$paralelos['jo_nombre']}";

            // Concatenación segura evitando escapar comillas dobles innecesariamente
            $cadena .= "<option value='{$code}'>{$name}</option>";
        }
    }
    return $cadena;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            font-family: Arial, sans-serif;
            font-size: 11px;
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #AAA;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #F2F2F2;
        }

        .text-left {
            text-align: left;
        }

        .rojo {
            color: #dd4b39;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <label for="selector-paralelo">Seleccione el Paralelo:</label>
    <select id="selector-paralelo">
        <option value="">-- Seleccione una opción --</option>
        <?php echo cargarParalelosEspecialidad(); ?>
    </select>

    <label for="selector-asignatura">Seleccione la Asignatura:</label>
    <select id="selector-asignatura" disabled>
        <option value="">-- Seleccione primero un paralelo --</option>
    </select>

    <!-- Contenedor del reporte -->
    <div id="contenedor-tabla">
        <!-- Aquí es donde volcaremos dinámicamente los textos o las tablas -->
        <div id="resultado-reporte">
            <p style="color: #666;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const selParalelo = document.getElementById("selector-paralelo");
            const selAsignatura = document.getElementById("selector-asignatura");
            const contenedor = document.getElementById("resultado-reporte");

            // Evento 1: Cambia el Paralelo -> Carga las Asignaturas
            selParalelo.addEventListener("change", () => {
                const idParalelo = selParalelo.value;

                // Limpiar selectores secundarios y tabla
                selAsignatura.innerHTML = '<option value="">-- Seleccione primero un paralelo --</option>';
                selAsignatura.disabled = true;
                contenedor.innerHTML = '<p style="color: #666;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>';

                if (!idParalelo) return;

                // Petición AJAX para obtener las opciones del select de asignaturas
                fetch("procesar_filtros.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `accion=cargar_asignaturas&id_paralelo=${encodeURIComponent(idParalelo)}`
                    })
                    .then(response => response.text())
                    .then(htmlOpciones => {
                        // console.log(htmlOpciones);
                        selAsignatura.innerHTML = htmlOpciones;
                        selAsignatura.disabled = false; // Activamos el campo
                    })
                    .catch(error => console.error("Error cargando asignaturas:", error));
            });

            // Evento 2: Cambia la Asignatura -> Genera el reporte final unificado
            selAsignatura.addEventListener("change", () => {
                const idParalelo = selParalelo.value;
                const idAsignatura = selAsignatura.value;

                // Validar que ambos selectores tengan un valor válido antes de enviar
                if (!idParalelo || !idAsignatura) {
                    contenedor.innerHTML = '<p style="color: #666;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>';
                    return;
                }

                // Indicador visual de carga
                contenedor.innerHTML = '<p style="color: #31708f;">Cargando lista de calificaciones de la asignatura...</p>';

                // SOLUCIÓN: Usar URLSearchParams para asegurar el correcto mapeo de los datos en $_POST
                const datosPost = new URLSearchParams();
                datosPost.append('id_paralelo', idParalelo);
                datosPost.append('id_asignatura', idAsignatura);
                datosPost.append('id_periodo_lectivo', 34);

                // Petición AJAX enviando los dos identificadores al script del reporte
                fetch("obtener_reporte.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: datosPost // Enviamos el objeto con los parámetros formateados de forma segura
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Si hay un error 500, lanzamos una excepción para capturarla en el .catch()
                            throw new Error(`Error en el servidor: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(htmlTabla => {
                        // Reemplaza el contenedor con la estructura completa de la tabla dinámica
                        contenedor.innerHTML = htmlTabla;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        contenedor.innerHTML = '<p style="color: #dd4b39;">Error al generar el reporte de la asignatura. Verifique los registros de PHP.</p>';
                    });
            });

        });
    </script>
</body>

</html>