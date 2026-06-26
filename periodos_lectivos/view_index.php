<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Periodos Lectivos
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <!-- Default box -->
            <div class="box box-info">
                <div class="box-body">
                    <button class="btn btn-danger" data-toggle="modal" data-target="#nuevoPeriodoLectivoModal">Crear Periodo Lectivo</button>
                    <hr>

                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <div class="form-group">
                                <label for="id_oferta_educativa">Oferta Educativa:</label>
                                <select name="id_oferta_educativa" id="id_oferta_educativa" class="form-control">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <table id="example1" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Id</th>
                                <th>Año Inicial</th>
                                <th>Año Final</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_periodos_lectivos">
                            <!-- Aqui vamos a poblar los periodos lectivos ingresados en la BDD mediante AJAX  -->
                        </tbody>
                    </table>

                    <div class="text-center">
                        <ul class="pagination" id="pagination"></ul>
                    </div>
                    <input type="hidden" id="pagina_actual">
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once "modalInsert.php" ?>

<!-- <script src="../public/js/pages/periodo_lectivo/create.js"></script> -->

<script type="text/javascript">
    $(document).ready(function() {
        cargarOfertasEducativas();

        $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una oferta educativa...</td></tr>");

        $("#id_oferta_educativa").change(function() {
            let id_oferta_educativa = $(this).val();
            if (id_oferta_educativa == 0) {
                $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");
            } else {
                pagination(1, id_oferta_educativa);
            }
        });

        //Datemask yyyy/mm/dd
        $('#pe_fecha_inicio').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });

        $('#pe_fecha_fin').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });

        $("#pe_fecha_inicio").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#pe_fecha_fin").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $('#tbody_periodos_lectivos').on('click', '.item-edit', function() {
            var id_periodo_lectivo = $(this).attr('data');
            //alert(id_periodo_lectivo);
            // $.ajax({
            //     url: "periodos_lectivos/obtener_periodo_lectivo.php",
            //     data: {
            //         id: id_periodo_lectivo
            //     },
            //     method: "POST",
            //     dataType: "json",
            //     success: function(data) {
            //         console.log(data);
            //     }
            // });
            location.href = "admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=periodos_lectivos/view_edit.php&id_periodo_lectivo=" + id_periodo_lectivo + "&nivel=0";
        });
    });

    function cargarOfertasEducativas() {
        $.ajax({
            url: "periodos_lectivos/cargar_ofertas_educativas.php",
            dataType: "html",
            success: function(data) {
                $("#id_oferta_educativa").append(data);
            },
            error: function(jqXHR, textStatus) {
                alert(jqXHR.responseText);
            }
        });
    }

    function pagination(partida, id_oferta_educativa) {
        $("#pagina_actual").val(partida);
        var url = "periodos_lectivos/paginar_periodos_lectivos.php";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida,
                id_oferta_educativa: id_oferta_educativa
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_periodos_lectivos").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }

    function insertarPeriodoLectivo() {
        const pe_anio_inicio = $("#pe_anio_inicio").val().trim();
        const pe_anio_fin = $("#pe_anio_fin").val().trim();
        const pe_fecha_inicio = $("#pe_fecha_inicio").val().trim();
        const pe_fecha_fin = $("#pe_fecha_fin").val().trim();
        const pe_nota_minima = $("#pe_nota_minima").val().trim();
        const pe_nota_aprobacion = $("#pe_nota_aprobacion").val().trim();
        const id_oferta_educativa = $("#id_oferta_educativa").val();
        const quien_inserta_comp_id = $("#quien_inserta_comp_id").val();

        let cont_errores = 0;

        // Obtener el año actual
        const anioActual = new Date().getFullYear();

        // Verificar si se ha introducido un valor de año
        if (pe_anio_inicio) {
            if (parseInt(pe_anio_inicio) > anioActual) {
                // Si el año ingresado es mayor que el actual
                document.getElementById('error-pe_anio_inicio').textContent = 'El año inicial no puede ser mayor al año actual.';
                cont_errores++;
            } else {
                document.getElementById('error-pe_anio_inicio').textContent = '';
            }
        } else {
            document.getElementById('error-pe_anio_inicio').textContent = 'Por favor, introduce el año inicial.';
        }

        if (pe_anio_fin) {
            if (parseInt(pe_anio_inicio) > parseInt(pe_anio_fin)) {
                // Si el año inicial ingresado es mayor que el año final
                document.getElementById('error-pe_anio_inicio').textContent = 'El año inicial no puede ser mayor al año final.';
                cont_errores++;
            } else {
                document.getElementById('error-pe_anio_inicio').textContent = '';
            }
        } else {
            document.getElementById('error-pe_anio_fin').textContent = 'Por favor, introduce el año final.';
        }

        const fecha_inicial = new Date(pe_fecha_inicio);
        const fecha_final = new Date(pe_fecha_fin);

        if (fecha_inicial > fecha_final) {
            // Si la fecha inicial es mayor que la fecha final
            document.getElementById('error-pe_fecha_inicio').textContent = 'La fecha inicial no puede ser mayor a la fecha final.';
            cont_errores++;
        } else {
            document.getElementById('error-pe_fecha_inicio').textContent = '';
        }

        if (pe_nota_minima) {
            if (parseFloat(pe_nota_minima) <= 0) {
                // Si la nota mínima ingresada es menor o igual que cero
                document.getElementById('error-pe_nota_minima').textContent = 'La nota minima no puede ser menor o igual que cero.';
                cont_errores++;
            } else {
                document.getElementById('error-pe_nota_minima').textContent = '';
            }
        } else {
            document.getElementById('error-pe_nota_minima').textContent = 'Por favor, introduce la nota mínima.';
        }

        if (pe_nota_aprobacion) {
            if (parseFloat(pe_nota_aprobacion) <= 0) {
                // Si la nota de aprobación ingresada es menor o igual que cero
                document.getElementById('error-pe_nota_aprobacion').textContent = 'La nota minima de aprobación no puede ser menor o igual que cero.';
                cont_errores++;
            } else {
                document.getElementById('error-pe_nota_aprobacion').textContent = '';
            }
        } else {
            document.getElementById('error-pe_nota_aprobacion').textContent = 'Por favor, introduce la nota mínima de aprobación.';
        }

        if (id_oferta_educativa === "") {
            // Si no se ha elegido una oferta educativa
            Swal.fire({
				title: "Oops!",
				text: "¡Debes escoger una oferta educativa!",
				icon: "error"
			});
            $("#nuevoPeriodoLectivoModal").modal("hide");
            cont_errores++;
        }

        if (cont_errores == 0) {
            // alert("Se puede ingresar la información");
            $.ajax({
                type: "POST",
                url: "periodos_lectivos/insertar_periodo_lectivo.php",
                data: {
                    id_oferta_educativa: id_oferta_educativa,
                    anio_inicial: pe_anio_inicio,
                    anio_final: pe_anio_fin,
                    fec_ini: pe_fecha_inicio,
                    fec_fin: pe_fecha_fin,
                    nota_minima: pe_nota_minima,
                    nota_aprobacion: pe_nota_aprobacion,
                    quien_inserta_comp: quien_inserta_comp_id
                },
                dataType: "json",
                success: function (r) {
                    Swal.fire({
                        title: r.titulo,
                        text: r.mensaje,
                        icon: r.tipo_mensaje,
                        confirmButtonText: 'Cool'
                    });
                    $("#nuevoPeriodoLectivoModal").modal("hide");
                    pagination(1, id_modalidad);
                }
            });
        }

        return false;
    }
</script>