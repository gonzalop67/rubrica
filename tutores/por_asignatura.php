<style>
	/* 1. Contenedor del reporte: Forzamos el contexto de posicionamiento */
	#resultado-reporte {
		width: 100%;
		max-height: 500px;
		overflow: auto;
		border: 1px solid #AAA;
		position: relative;
		/* Crea un nuevo origen de coordenadas para el sticky */
	}

	/* 2. Estilo base de la tabla */
	table {
		font-family: Arial, sans-serif;
		font-size: 9pt;
		border-collapse: separate !important;
		/* Obligatorio: destruye herencias de la plantilla */
		border-spacing: 0;
		width: max-content;
	}

	th,
	td {
		border-right: 1px solid #AAA;
		border-bottom: 1px solid #AAA;
		padding: 6px;
		text-align: center;
		background-color: #FFF;
		/* Evita transparencias al desplazar */
	}

	th {
		background-color: #F2F2F2;
	}

	th:first-child,
	td:first-child {
		border-left: 1px solid #AAA;
	}

	/* ==========================================
   CONGELAR FILAS SUPERIORES (EJE VERTICAL)
   ========================================== */
	/* En lugar de congelar los <th> de forma individual por fila con 'top' manual,
   congelamos el bloque general <thead> completo para que el navegador mantenga 
   las 3 filas unidas sin importar cuánto midan en píxeles. */
	thead {
		position: -webkit-sticky;
		position: sticky !important;
		top: 0 !important;
		z-index: 20;
	}

	/* Forzamos a todos los th a comportarse de manera uniforme dentro del bloque pegajoso */
	thead tr th {
		position: relative;
		/* Mantiene su posición relativa dentro del thead congelado */
		border-top: 1px solid #AAA;
		background-color: #F2F2F2 !important;
		/* Color gris unificado para toda la cabecera */
		box-sizing: border-box;
	}

	/* ==========================================
   CONGELAR COLUMNAS IZQUIERDAS (EJE HORIZONTAL)
   ========================================== */
	th.col-fija-1,
	td.col-fija-1 {
		position: -webkit-sticky;
		position: sticky !important;
		left: 0 !important;
		z-index: 10;
		width: 45px !important;
		min-width: 45px !important;
		max-width: 45px !important;
		box-sizing: border-box;
		padding: 6px 2px !important;
	}

	th.col-fija-2,
	td.col-fija-2 {
		position: -webkit-sticky;
		position: sticky !important;
		left: 45px !important;
		/* Acoplado milimétricamente al ancho de la col 1 */
		z-index: 10;
		width: 260px !important;
		min-width: 260px !important;
		max-width: 260px !important;
		box-sizing: border-box;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	/* ==========================================
   INTERSECCIÓN CRÍTICA (Las esquinas superiores)
   Al congelar el 'thead' por completo, las esquinas solo necesitan un z-index 
   superior para que los nombres de los alumnos no pasen sobre 'Nro.' ni 'Nómina' */
	thead .col-fija-1,
	thead .col-fija-2 {
		z-index: 30 !important;
	}

	/* Clases de utilidad */
	.text-left {
		text-align: left;
	}

	.rojo {
		color: #dd4b39;
		font-weight: bold;
	}
</style>
<div class="content-wrapper">
	<!-- Main content -->
	<section class="content">
		<div id="titulo_pagina">
			<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
		</div>
		<div id="barra_principal">
			<label for="selector-asignatura">Asignaturas:</label>
			<select id="selector-asignatura">
				<option value="">-- Seleccione --</option>
			</select>
		</div>
		<div id="contenedor-tabla">
			<!-- El loader se mantiene fijo aquí -->
			<div id="img_loader" style="display:none;text-align:center">
				<img src="imagenes/ajax-loader.gif" alt="Procesando...">
			</div>

			<!-- Aquí es donde volcaremos dinámicamente los textos o las tablas -->
			<div id="resultado-reporte">
				<p style="color: #666; text-align: center;">Seleccione una asignatura para ver las calificaciones.</p>
			</div>
		</div>
	</section>
</div>
<script>
	$(document).ready(function() {
		const loader = document.getElementById("img_loader");
		const resultado = document.getElementById("resultado-reporte");

		cargarAsignaturas();

		$("#selector-asignatura").change(function(event) {
			const idParalelo = $("#id_paralelo_tutor").val();
			const idAsignatura = $("#selector-asignatura").val();
			const idPeriodoLectivo = $("#id_periodo_lectivo").val();

			// MOSTRAR LOADER
			loader.style.display = "block";
			resultado.innerHTML = ""; // Limpiamos el texto mientras carga

			// Petición AJAX enviando los identificadores al script del reporte con jQuery
			$.ajax({
				url: "obtener_reporte.php",
				type: "POST",
				// jQuery mapea y formatea estos datos automáticamente de forma segura en $_POST
				data: {
					id_paralelo: idParalelo,
					id_asignatura: idAsignatura,
					id_periodo_lectivo: idPeriodoLectivo
				},
				success: function(htmlTabla) {
					// Reemplaza el contenedor interno con la estructura de la tabla dinámica
					$("#resultado-reporte").html(htmlTabla);
				},
				error: function(xhr, status, error) {
					// Captura errores de red o respuestas de error del servidor (como el código 500)
					console.error("Error:", error);
					$("#resultado-reporte").html('<p style="color: #dd4b39;">Error al generar el reporte de la asignatura. Verifique los registros de PHP.</p>');
				},
				complete: function() {
					// OCULTAR LOADER (Se ejecuta siempre al terminar, sea éxito o error)
					$("#img_loader").hide();
				}
			});

		});
	});

	function cargarAsignaturas() {
		// Luego obtengo las asignaturas asociadas al paralelo
		$.post("tutores/cargar_asignaturas_por_paralelo.php", {
				id_paralelo: $("#id_paralelo_tutor").val()
			},
			function(resultado) {
				// console.log(resultado);
				if (resultado == "") {
					alert("Error");
				} else {
					document.getElementById("selector-asignatura").length = 1;
					$("#selector-asignatura").append(resultado);
				}
			}
		);
	}
</script>