<style>
    table {
        border: none;
        margin-top: 4px;
    }

    table td {
        padding-left: 2px;
        padding-top: 2px;
    }

    .ocultar {
        display: none;
    }

    .barra_principal {
        background: #f5f5f5;
        height: 36px;
    }
</style>
<div id="pagina">
    <div id="titulo_pagina">
        <?php echo "CUADRO FINAL BGU INTENSIVOS" ?>
    </div>
    <div class="barra_principal">
        <form id="form-intensivos" action="php_excel/reporte_bgu_intensivos.php" method="post">
            <table id="tabla_navegacion">
                <tr>
                    <td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
                    <td>
                        <select id="id_paralelo" name="id_paralelo" class="fuente9">
                            <option value="">Seleccione...</option>
                        </select>
                    </td>
                    <td class="fuente9">&nbsp;Asignatura: &nbsp;</td>
                    <td>
                        <select id="id_asignatura" name="id_asignatura" class="fuente9">
                            <option value="">Seleccione...</option>
                        </select>
                    </td>
                    <td>
                        &nbsp;
                        <button type="submit" id="export_to_excel" class="btn btn-primary btn-sm ocultar">
                            <i class="fa fa-file-excel-o"></i> Exportar a Excel
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div id="mensaje" style="font-size: 12px; margin-top: 2px;" class="text-center"></div>
</div>
<script>
    $(document).ready(function() {
        $.get("intensivos/verificar_si_es_intensivo.php",
            function(resultado) {
                if (resultado == "0") {
                    swal("Mensaje", "No se trata de un periodo de modalidad intensiva...", "error");
                } else if (resultado == "2") {
                    swal("Mensaje", "No se trata de un periodo de BGU Intensivo...", "error");
                } else {
                    // Aqui va el codigo para obtener los paralelos correspondientes al periodo lectivo
                    cargarParalelos();
                    $("#export_to_excel").show();
                }
            }
        );

        $("#id_paralelo").change(function(e) {
            e.preventDefault();
            cargarAsignaturas($(this).val());
        });

        $("#form-intensivos").submit(function(e) {
            
            let id_paralelo = $("#id_paralelo").val();
            let id_asignatura = $("#id_asignatura").val();

            if (id_paralelo == 0) {
                swal("Mensaje", "Debe seleccionar un paralelo...", "error");
                return false;
            } else if (id_asignatura == 0) {
                swal("Mensaje", "Debe seleccionar una asignatura...", "error");
                return false;
            } else {
                return true;
            }
        });
    });

    function cargarParalelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#id_paralelo").append(resultado);
                    $("#mensaje").html("Debe seleccionar un paralelo...");
                }
            }
        );
    }

    function cargarAsignaturas(id_paralelo) {
        $.post("scripts/cargar_asignaturas_por_paralelo.php", {
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    document.getElementById("id_asignatura").length = 1;
                    $("#id_asignatura").append(resultado);
                    $("#mensaje").html("Debe seleccionar una asignatura...");
                }
            }
        );
    }
</script>