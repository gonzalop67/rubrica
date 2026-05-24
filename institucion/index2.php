<div class="container">
    <div id="diasSemanaApp" class="col-sm-9 col-sm-offset-1">
        <input type="hidden" id="id_dia">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Datos de la Institución Educativa</h4>
            </div>
            <div class="panel-body">
                <form id="form_institucion" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Nombre:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="in_nombre" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Dirección:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="in_direccion" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Teléfono:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="in_telefono1" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Rector(a):</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="in_nom_rector" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje4"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Secretario(a):</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control fuente9" id="in_nom_secretario" value="" onfocus="sel_texto(this)">
                            <span class="help-desk error" id="mensaje5"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                                Guardar
                            </button>
                        </div>
                    </div>
                </form>
                <div id="img_loader" class="text-center"> <img src="imagenes/ajax-loader.gif" alt="Procesando..." /> </div>
                <!-- message -->
                <div id="mensaje" class="fuente9 text-center"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script>
    $(document).ready(function(){
		obtenerDatosInstitucion();
        $("#img_loader").hide();
        $("#form_institucion").submit(function(e){
            e.preventDefault();
            var in_nombre = eliminaEspacios(document.getElementById("in_nombre").value);
            var in_direccion = eliminaEspacios(document.getElementById("in_direccion").value);
            var in_telefono1 = eliminaEspacios(document.getElementById("in_telefono1").value);
            var in_nom_rector = eliminaEspacios(document.getElementById("in_nom_rector").value);
            var in_nom_secretario = eliminaEspacios(document.getElementById("in_nom_secretario").value);
            $("#img_loader").show();
            $.post("institucion/actualizar_datos_institucion.php", 
                { 
                    in_nombre: in_nombre,
                    in_direccion: in_direccion,
                    in_telefono1: in_telefono1,
                    in_nom_rector: in_nom_rector,
                    in_nom_secretario: in_nom_secretario
                },
                function(resultado)
                {
                    $("#img_loader").hide();
                    if(resultado == false)
                    {
                        alert("Error");
                    }
                    else
                    {
                        $("#mensaje").html(resultado);
                        document.getElementById("in_nombre").focus();
                    }
                }
            );
        });
	});
	function sel_texto(input) {
		$(input).select();
	}
	function obtenerDatosInstitucion()
	{
		$.ajax({
            type: "POST",
            url: "institucion/obtener_datos_institucion.php",
            success: function(resultado){
                var JSONInstitucion = eval('(' + resultado + ')');
                //Aqui se van a pintar los datos de la institucion educativa
                document.getElementById("in_nombre").value=(JSONInstitucion.in_nombre) ? JSONInstitucion.in_nombre : "";
                document.getElementById("in_direccion").value=(JSONInstitucion.in_direccion) ? JSONInstitucion.in_direccion : "";
                document.getElementById("in_telefono1").value=(JSONInstitucion.in_telefono1) ? JSONInstitucion.in_telefono1 : "";
                document.getElementById("in_nom_rector").value=(JSONInstitucion.in_nom_rector) ? JSONInstitucion.in_nom_rector : "";
                document.getElementById("in_nom_secretario").value=(JSONInstitucion.in_nom_secretario) ? JSONInstitucion.in_nom_secretario : "";
                document.getElementById("in_nombre").focus();
            }
		});			
	}
</script>
