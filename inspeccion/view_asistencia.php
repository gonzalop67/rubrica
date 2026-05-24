<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>R&uacute;brica Web 2.0</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="js/funciones.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function () {
            cargarParalelosInspector();

            $("#fecha_asistencia").datepicker({
                dateFormat : 'yy-mm-dd',
                showOn: "both",
                buttonImage: "imagenes/calendario.png",
                buttonImageOnly: true,
                buttonText: "Seleccione la fecha...",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0",
                firstDay: 1,
                monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá']
            });

            $("#fecha_asistencia").change(function(){
                $("#lista_estudiantes_paralelo").html("");
            });

        });

        //Recibe fecha en formato DD/MM/YYYY
        function dia_semana(fecha) {
            fecha = fecha.split('-');
            if (fecha.length != 3) {
                return null;
            }
            //Vector para calcular día de la semana de un año regular.
            var regular = [0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5];
            //Vector para calcular día de la semana de un año bisiesto.
            var bisiesto = [0, 3, 4, 0, 2, 5, 0, 3, 6, 1, 4, 6];
            //Vector para hacer la traducción de resultado en día de la semana.
            //var semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            var semana = [7, 1, 2, 3, 4, 5, 6];
            //Día especificado en la fecha recibida por parametro.
            var dia = fecha[2];
            //Módulo acumulado del mes especificado en la fecha recibida por parametro.
            var mes = fecha[1] - 1;
            //Año especificado por la fecha recibida por parametros.
            var anno = fecha[0];
            //Comparación para saber si el año recibido es bisiesto.
            if ((anno % 4 == 0) && !(anno % 100 == 0 && anno % 400 != 0))
                mes = bisiesto[mes];
            else
                mes = regular[mes];
            //Se retorna el resultado del calculo del día de la semana.
            return semana[Math.ceil(Math.ceil(Math.ceil((anno - 1) % 7) + Math.ceil((Math.floor((anno - 1) / 4) - Math.floor((3 * (Math.floor((anno - 1) / 100) + 1)) / 4)) % 7) + mes + dia % 7) % 7)];
        }

        function cargarParalelosInspector()
        {
            contarParalelosInspector(); //Esta funcion desencadena las demas funciones de paginacion
        }

        function contarParalelosInspector()
        {
            $.post("inspeccion/contar_paralelos_inspector.php", { },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        var JSONNumRegistros = eval('(' + resultado + ')');
                        var total_registros = JSONNumRegistros.num_registros;
                        $("#num_paralelos").html("N&uacute;mero de Paralelos encontrados: "+total_registros);
                        paginarParalelosInspector(4,1,total_registros);
                    }
                }
            );
        }
            
        function paginarParalelosInspector(cantidad_registros, num_pagina, total_registros)
        {
            $.post("inspeccion/paginar_paralelos_inspector.php",
                {
                    cantidad_registros: cantidad_registros,
                    num_pagina: num_pagina,
                    total_registros: total_registros
                },
                function(resultado)
                {
                    $("#paginacion_paralelos").html(resultado);
                }
            );
            listarParalelosInspector(num_pagina);
        }

        function listarParalelosInspector(numero_pagina)
        {
            $.post("scripts/listar_paralelos_inspector.php", 
                {
                    cantidad_registros: 4,
                    numero_pagina: numero_pagina
                },
                function(resultado)
                {
                    if(resultado == false)
                    {
                        alert("No se han asociado paralelos al inspector actual...");
                    }
                    else
                    {
                        $("#lista_paralelos").html(resultado);
                    }
                }
            );
        }

        function seleccionarParalelo(id_curso, id_paralelo, curso, paralelo)
        {
            var fecha = document.getElementById("fecha_asistencia").value;
            if (fecha == "") {
                alert("Debe elegir una Fecha...");
            } else {
                document.getElementById("tituloNomina").innerHTML="NOMINA DE ESTUDIANTES [" + curso + " " + paralelo + "]";
                $("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader-blue.GIF' alt='Procesando...'>");
                //Consultar el dia de la semana
                var ds_ordinal = dia_semana(fecha);
                var id_periodo_lectivo = document.getElementById("id_periodo_lectivo").value;
                
                $.post("horarios/consultar_id_dia_semana.php", 
                    {
                        ds_ordinal: ds_ordinal,
                        id_periodo_lectivo: id_periodo_lectivo
                    },
                    function (resultado)
                    {
                        $("#lista_estudiantes_paralelo").html("<img src='./imagenes/ajax-loader-blue.GIF' alt='Procesando...'>");
                        if (resultado == false) {
                            alert("No se ha definido el Dia de la Semana...");
                            $("#lista_estudiantes_paralelo").html("");
                        } else {
                            var JSONIdDiaSemana = eval('(' + resultado + ')');
                            var id_dia_semana = JSONIdDiaSemana.id_dia_semana;
                            if(id_dia_semana==null) {
                                alert("No se ha definido el Dia de la Semana...");
                                $("#lista_estudiantes_paralelo").html("");
                            } else {
                                $.post("inspeccion/consultar_horario_paralelo.php",
                                    {
                                        id_paralelo: id_paralelo,
                                        id_dia_semana: id_dia_semana
                                    },
                                    function(resultado)
                                    {
                                        if(resultado==false){
                                            alert("No se ha definido el horario para el Dia de la Semana y Paralelo seleccionados...");
                                            $("#lista_estudiantes_paralelo").html("");
                                        } else {
                                            $.post("inspeccion/listar_inasistencia_paralelo.php", 
                                                { 
                                                    id_paralelo: id_paralelo,
                                                    at_fecha: fecha
                                                },
                                                function(resultado)
                                                {
                                                    //anadir el resultado al DOM
                                                    $("#lista_estudiantes_paralelo").html(resultado);
                                                }
                                            );
                                        }
                                    }
                                );
                            }
                        }
                    }
                );
            }
        }
        
        function actualizar_asistencia(obj, id_asistencia_estudiante)
        {
            if(obj.checked) abreviatura = "A";
            else abreviatura = "I";
            $.ajax({
                    type: "POST",
                    url: "inspeccion/actualizar_inasistencia_estudiante.php",
                    data: {
                        id_asistencia_estudiante: id_asistencia_estudiante,
                        in_abreviatura: abreviatura
                    },
                    success: function(resultado){
                        // No desplega nada... esto es solo para ejecutar el codigo php
                        console.log(resultado);
                }
            });			
        }

    </script>

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
        <div id="mensaje" class="error"></div>
        <div id="pag_paralelos">
            <!-- Aqui va la paginacion de los paralelos asociados al docente -->
            <div id="total_registros" class="paginacion">
                <table class="fuente8" width="100%" cellspacing=4 cellpadding=0 border=0>
                    <tr>
                        <td>
                            <div id="num_paralelos">&nbsp;N&uacute;mero de Paralelos encontrados:&nbsp;</div>
                        </td>
                        <td>
                            <div id="paginacion_paralelos"> 
                                <!-- Aqui va la paginacion de asignaturas --> 
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="header2"> LISTA DE PARALELOS ASOCIADOS </div>
            <div class="cabeceraTabla">
                <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                    <tr class="cabeceraTabla">
                        <td width="5%">Nro.</td>
                        <td width="71%" align="left">Curso</td>
                        <td width="6%" align="left">Paralelo</td>
                        <td width="18%" align="center">Acciones</td>
                    </tr>
                </table>
            </div>
            <div id="lista_paralelos" style="text-align:center"> </div>
        </div>
        <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
        <div class="cabeceraTabla">
            <table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
                <tr class="cabeceraTabla">
                    <td width="5%">Nro.</td>
                    <td width="5%">Id.</td>
                    <td width="30%" align="left">N&oacute;mina</td>
                    <td width="60%" align="left"><div id="txt_rubricas">Asistencia</div></td>
                    <!-- <td width="18%" align="center">Acciones</td> -->
                </tr>
            </table>
        </div>
        <form id="formulario_asistencia" action="reportes/reporte_asistencia_docente.php" method="post" target="_blank">
            <div id="img_loader_estudiantes" style="text-align:center"> </div>
            <div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
        </form>
        <input id="id_periodo_lectivo" type="hidden" value="<?php echo $_SESSION['id_periodo_lectivo'] ?>" />
        <input id="id_usuario" type="hidden" value="<?php echo $id_usuario ?>" />
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    </body>
</html>
