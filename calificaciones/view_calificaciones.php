<div class="content-wrapper">
    <input type="hidden" id="in_copiar_y_pegar">

    <section class="content-header">
        <h1>Ingreso de Calificaciones <small>Panel de Docentes</small></h1>
    </section>

    <section class="content">

        <!-- PANEL DE FILTROS SUPERIOR (ESTRUCTURA MODULAR BLINDADA ANTI-DESCUADRES) -->
        <div class="box box-solid box-primary" style="margin-bottom: 15px;">
            <div class="box-body" style="padding: 10px;">
                <div id="barra_principal" class="well well-sm clearfix" style="background-color: #f9f9f9; border-radius: 3px; margin-bottom: 0; padding: 15px; height: auto !important;">

                    <!-- 1. Fila Superior: Texto Informativo Largo (Ocupa todo el ancho libre) -->
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-xs-12">
                            <label for="cboParalelos" class="control-label" style="font-size: 13px; font-weight: bold; margin-bottom: 0; color: #333; display: block; white-space: normal;">
                                <i class="fa fa-users text-blue"></i> Seleccione el Paralelo para abrir la Sábana de Calificaciones:
                            </label>
                        </div>
                    </div>

                    <!-- 2. Fila Inferior: Controles alineados (Select a la izquierda, Botones a la derecha) -->
                    <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">

                        <!-- Selector Desplegable -->
                        <div class="col-md-7 col-sm-6 col-xs-12" style="margin-bottom: 5px;">
                            <select id="cboParalelos" class="form-control" style="width: 100% !important; height: 34px;">
                                <!-- Carga dinámica de paralelos -->
                            </select>
                        </div>

                        <!-- Zona de Botones de Control -->
                        <div class="col-md-5 col-sm-6 col-xs-12 text-right" style="margin-bottom: 5px;">
                            <button type="submit" form="formulario_rubrica" id="ver_reporte" class="btn btn-sm" style="display: none; height: 34px; font-weight: bold; background-color: #ffffff !important; color: #1f7244 !important; border: 1px solid #1f7244 !important; padding: 6px 12px; border-radius: 3px; margin-right: 5px; transition: all 0.2s ease;">
                                <i class="fa fa-file-excel-o" style="color: #1f7244 !important; margin-right: 4px;"></i> Reporte Excel
                            </button>

                            <button type="button" id="save_all" class="btn btn-save-modern" style="display: none; height: 34px; font-weight: bold; padding: 6px 18px; border: none; border-radius: 4px; transition: all 0.3s ease;">
                                <i class="fa fa-save" style="margin-right: 6px;"></i> Guardar Todo
                            </button>
                        </div>

                    </div>

                    <!-- Inputs ocultos requeridos por tu sistema -->
                    <input id="id_estudiante" type="hidden" />
                    <input id="id_rubrica_personalizada" type="hidden" />
                    <input id="numero_pagina" type="hidden" />
                    <input id="id_asignatura" type="hidden" />
                </div>
            </div>
        </div>

        <!-- 🎯 REJILLA ARQUITECTÓNICA DE CONTROL ABSOLUTO (REEMPLAZA DESDE AQUÍ) -->
        <div class="row" style="margin-top: 15px;">

            <!-- ⬅️ COLUMNA IZQUIERDA: Lista de Asignaturas -->
            <div class="col-md-4 col-sm-12">
                <div class="box box-solid box-default" style="margin-bottom: 15px;">
                    <div class="box-header with-border" style="background-color: #f4f4f4;">
                        <h3 class="box-title" style="font-size: 14px; font-weight: bold; color: #444;">
                            <i class="fa fa-book text-blue"></i> Asignaturas Asociadas
                        </h3>
                    </div>

                    <div class="box-body p-0" style="max-height: 520px; overflow-y: auto;">
                        <div class="p-2 bg-light text-center text-muted border-bottom" id="num_asignaturas" style="font-size: 12px; padding: 8px; border-bottom: 1px solid #eee;">
                            Número de Asignaturas: 0
                        </div>
                        <div id="pag_asignaturas" class="text-center" style="margin: 5px 0;"></div>

                        <div id="contenedor_asignaturas_docente" class="list-group" style="margin-bottom: 0; border-radius: 0;">
                            <div class="text-muted text-center" style="padding: 40px 20px; color: #999;">
                                <i class="fa fa-arrow-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                                <p style="margin: 0;">Seleccione un paralelo arriba para cargar...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ➡️ COLUMNA DERECHA: Sábana de Calificaciones (Contenedor Hermético Anti-Cortes) -->
            <div class="col-md-8 col-sm-12">
                <div class="box box-solid box-success" id="pag_nomina_estudiantes" style="margin-bottom: 15px;">
                    <div class="box-header with-border" style="background-color: #00a65a; color: #fff;">
                        <h3 class="box-title" style="font-size: 14px; font-weight: bold; color: #fff; margin: 0; line-height: 30px;">
                            <i class="fa fa-users"></i> NÓMINA DE ESTUDIANTES
                        </h3>
                    </div>

                    <div class="box-body" style="padding: 10px;">
                        <!-- Barra de Resumen -->
                        <div id="total_registros_estudiantes" class="well well-sm" style="background-color: #f4f4f4; margin-bottom: 15px; padding: 8px 15px;">
                            <div id="num_estudiantes" style="font-weight: bold;">
                                Estudiantes Matriculados: <span class="label label-primary" id="lbl_total_estudiantes">0</span>
                            </div>
                        </div>

                        <form id="formulario_rubrica" action="calificaciones/reporte_sabana_calificaciones.php" method="post">
                            <input type="hidden" id="id_paralelo_excel" name="id_paralelo_excel" value="" />
                            <input type="hidden" id="id_asignatura_excel" name="id_asignatura_excel" value="" />

                            <div id="img_loader_estudiantes" class="text-center"></div>

                            <!-- 🔒 CONTENEDOR MAESTRO DE CONTROL DE BORDES -->
                            <div id="lista_estudiantes_paralelo" style="width: 100% !important; overflow: visible !important;">
                                <div class="text-muted text-center" style="padding: 50px 20px; color: #999;">
                                    <i class="fa fa-hand-o-left fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                                    <p style="margin: 0;">Seleccione una asignatura del panel izquierdo para cargar los alumnos...</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div> <!-- /.row -->

    </section>
</div>

<link rel="stylesheet" href="calificaciones/sabana_calificaciones_estilos.css">

<script src="calificaciones/index.js"></script>