<?php
	include("scripts/clases/class.tutores.php");
	$tutor = new tutores();
	$id_usuario = $_SESSION["id_usuario"];
	$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$id_paralelo = $tutor->obtenerIdParalelo($id_usuario, $id_periodo_lectivo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_SESSION['titulo_pagina']; ?></title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script type="text/javascript">
        $(document).ready(function () {
            var html = '<tr>'
                     + '<td colspan="6" class="text-center">Debe seleccionar una fecha... </td>'
                     + '</tr>';

            $("#tbl_estudiantes").html(html);

            // Luego obtengo el nombre del paralelo
            $.post("tutores/obtener_nombre_paralelo.php",
                {
                    id_paralelo: $("#id_paralelo_tutor").val()
                },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error: No se ha asociado un paralelo al docente actual...");
                    }
                    else
                    {
                        $("#titulo_pagina").html("JUSTIFICAR FALTAS DE " + resultado);
                    }
                }
            );

            $("#fecha_asistencia").datepicker({
                dateFormat : 'yy-mm-dd',
                showOn: "both",
                buttonImage: "imagenes/calendario.png",
                buttonImageOnly: true,
                buttonText: "Seleccione la fecha...",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0",
                monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá']
            });

            $("#fecha_asistencia").change(function(){
                var fecha = $(this).val();
                var id_paralelo = $("#id_paralelo_tutor").val();
                var html = '<tr>'
                         + '<td colspan="6" class="text-center">'
                         + '<img src="imagenes/ajax-loader-blue.GIF" alt="Procesando...">'
                         + '</td>'
                         + '</tr>';
                $("#tbl_estudiantes").html(html);
                //Consultar la asistencia de la fecha seleccionada
                $.post("inspeccion/obtener_inasistencia_paralelo.php", 
                    { 
                        id_paralelo: id_paralelo,
                        at_fecha: fecha
                    },
                    function(resultado)
                    {
                        //anadir el resultado al DOM
                        $("#tbl_estudiantes").html(resultado);
                    }
                );
            });

        });

        function editar_justificacion(obj, id_asistencia_estudiante)
        {
            var id = obj.id;
            var fila = id.substr(id.indexOf("_")+1);
            if(obj.checked) {
                document.getElementById("justificacion_"+fila).disabled = false;
                document.getElementById("save_"+fila).disabled = false;
            }else{
                document.getElementById("justificacion_"+fila).value = "";
                document.getElementById("justificacion_"+fila).disabled = true;
                document.getElementById("save_"+fila).disabled = true;
                $.post("inspeccion/actualizar_justificacion.php", 
                    { 
                        id_asistencia_estudiante: id_asistencia_estudiante,
                        in_abreviatura: "I",
                        at_justificacion: ""
                    },
                    function(resultado)
                    {
                        alert(resultado);
                    }
                );
            }
        }

        function actualizar_justificacion(obj, id_asistencia_estudiante)
        {
            var id = obj.id;
            var fila = id.substr(id.indexOf("_")+1);
            var justificacion = document.getElementById("justificacion_"+fila).value;
            $.post("inspeccion/actualizar_justificacion.php", 
                { 
                    id_asistencia_estudiante: id_asistencia_estudiante,
                    in_abreviatura: "J",
                    at_justificacion: justificacion
                },
                function(resultado)
                {
                    alert(resultado);
                }
            );
        }

    </script>
</head>
<body>
    <div id="pagina">
        <div id="titulo_pagina">
            <?php echo $_SESSION['titulo_pagina'] ?>
        </div>
        <div id="barra_opciones" style="background-color: #f5f5f5; height: 24px;">
            <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%" class="fuente9" align="right"> &nbsp;Fecha:&nbsp; </td>
                    <td width="16%" align="left">
                        <input id="fecha_asistencia" class="cajaPequenia" type="text" disabled /> 
                    </td>
                    <td width="*"><div id="mensaje_asistencia" style="text-align: right;"></div></td>
                </tr>
            </table>
        </div>
    </div>
    <div>
        <!-- Aqui va la paginacion del horario semanal del paralelo elegido -->
        <div id="tituloNomina" class="header2" style="margin-top:2px;"> NOMINA DE ESTUDIANTES </div>
        <div id="tabla" class="table-responsive fuente8">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">Nro.</th>
                        <th width="5%">Id.</th>
                        <th width="30%" align="left">N&oacute;mina</th>
                        <th width="10%" align="left">Justificado</th>
                        <th width="30%" align="left">Justificaci&oacute;n</th>
                        <th width="20%" align="left">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbl_estudiantes">
                    
                </tbody>
            </table>
        </div>
    </div>
    <input id="id_periodo_lectivo" type="hidden" value="<?php echo $_SESSION['id_periodo_lectivo'] ?>" />
    <input id="id_paralelo" type="hidden" value="<?php echo $id_paralelo ?>" />
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</body>
</html>
