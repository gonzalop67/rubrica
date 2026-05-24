<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Valor del mes</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Agregar Valor del Mes</div>
                <div class="panel-body">
                    <form id="form-valor-mes" action="" method="post">
                        <input type="hidden" id="id_valor_mes">
                        <div class="col-md-12 form-group input-group">
                            <label for="cboMeses" class="input-group-addon">Mes</label>
                            <select id="cboMeses" class="form-control">
                                <option value="0">Seleccione ...</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group input-group">
                            <label for="valor" class="input-group-addon">Valor</label>
                            <input type="text" id="valor" id="" class="form-control">
                        </div>
                        <div class="col-md-12 text-center">
                            <a id="btn-nuevo" href="#" class="btn btn-primary">Nuevo Valor del Mes</a>
                            <button type="submit" class="btn btn-success">Guardar Valor del Mes</button>
                        </div>
                    </form>
                </div>
                <div id="mensaje"></div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Valores del Mes</div>
                <div class="panel-body">
                    <table class="table table-hover table-striped">
                        <thead>
                            <th>Nro.</th>
                            <th>Id</th>
                            <th>Mes</th>
                            <th>Valor</th>
                            <th></th>
                        </thead>
                        <tbody id="valor_mes">
                            <!-- Aqui desplegamos el contenido de la base de datos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/funciones.js"></script>
<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        cargar_valores_del_mes();
        $('#btn-nuevo').click(function(event){
            event.preventDefault();
            limpiar_valor_mes();
        });
        $('#form-valor-mes').submit(function(event){
            event.preventDefault();
            var id_valor_mes = $('#id_valor_mes').val();
            var vm_mes = $('#cboMeses').val();
            var vm_valor = $('#valor').val();
            // Saco los espacios en blanco al comienzo y al final de la cadena
		    vm_valor=eliminaEspacios(vm_valor);
            if(vm_mes==0){
                $("#mensaje").html("<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>Debe seleccionar el mes...</div>");
                $("#cboMeses").focus();
            }else if(vm_valor==""){
                $("#mensaje").html("<div class='alert alert-danger' style='margin-left: 4px; margin-right: 4px; color: #000;' role='alert'>Debe tipear el valor del mes...</div>");
                $("#valor").focus();
            }else{
                if(id_valor_mes==""){
                    //Nuevo Valor Mes
                    $.ajax({
                        url: "valor_del_mes/insertar_valor_mes.php",
                        method: "POST",
                        type: "html",
                        data: {
                            vm_mes: vm_mes,
                            vm_valor: vm_valor
                        },
                        success: function(response){
                            cargar_valores_del_mes();
                            $("#mensaje").html(response);
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }else{
                    //Actualizar Valor Mes
                    $.ajax({
                        url: "valor_del_mes/actualizar_valor_mes.php",
                        method: "POST",
                        type: "html",
                        data: {
                            id_valor_mes: id_valor_mes,
                            vm_valor: vm_valor
                        },
                        success: function(response){
                            cargar_valores_del_mes();
                            $("#mensaje").html(response);
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseText);
                        }
                    });
                }
            }
        });
    });
    function cargar_valores_del_mes(){
        $.ajax({
            url: "valor_del_mes/cargar_valores_del_mes.php",
            method: "POST",
            success: function(response){
                $("#valor_mes").html(response);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
    }
    function limpiar_valor_mes(){
        //Vuelvo a habilitar el combobox de meses para poder ingresar un nuevo valor de mes
        $("#cboMeses").prop("disabled", false);
        $("#mensaje").html("");
        $("#id_valor_mes").val("");
        $('#cboMeses').val(0);
        $("#valor").val("");
        $("#valor").focus();
    }
    function editarValor(id){
        //Primero obtengo los datos editables del valor del mes seleccionado
        $("#id_valor_mes").val(id);
        $.ajax({
            url: "valor_del_mes/obtener_valores_del_mes.php",
            method: "POST",
            data: {
                id: id
            },
            success: function(response){
                var valor_mes = jQuery.parseJSON(response);
                $("#valor").val(valor_mes.vm_valor);
                setearIndice("cboMeses",valor_mes.vm_mes);
                $("#mensaje").html("");
                $("#valor").focus();
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        //Ahora deshabilito el combobox de meses para evitar confusiones!
        $("#cboMeses").prop("disabled", true);
    }
    function setearIndice(nombreCombo,indice)
	{
		for (var i=0;i<document.getElementById(nombreCombo).options.length;i++)
			if (document.getElementById(nombreCombo).options[i].value == indice) {
				document.getElementById(nombreCombo).options[i].selected = indice;
			}
    }
    function eliminarValor(id){
        if(confirm("¿Está seguro que quiere eliminar este valor del mes?")){
            $.ajax({
                url: "valor_del_mes/eliminar_valor_mes.php",
                method: "POST",
                data: {
                    id: id
                },
                success: function(response){
                    console.log(response);
                    cargar_valores_del_mes();
                    $("#mensaje").html(response);
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }
    }

</script>