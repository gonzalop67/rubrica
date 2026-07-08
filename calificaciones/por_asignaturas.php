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
	<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	// 1. Conexión a la base de datos
	require_once("scripts/clases/class.mysql.php");
	$db = new MySQL;

	function cargarParalelosEspecialidad(int $id_periodo_lectivo)
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
             WHERE t.id_periodo_lectivo = $id_periodo_lectivo 
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
	<!-- Main content -->
	<section class="content">
		<div id="titulo_pagina">
			<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
		</div>
		<div id="barra_principal">
			<label for="selector-paralelo">Seleccione el Paralelo:</label>
			<select id="selector-paralelo">
				<option value="">-- Seleccione una opción --</option>
				<?php echo cargarParalelosEspecialidad($_SESSION['id_periodo_lectivo']); ?>
			</select>
			<label for="selector-asignatura">Seleccione la Asignatura:</label>
			<select id="selector-asignatura" disabled>
				<option value="">-- Seleccione primero un paralelo --</option>
			</select>
			<a id="btn-excel-php" href="#" target="_blank" style="display:none; margin-left: 10px; padding: 5px 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; font-family: Arial; font-size: 9pt;">
				Descargar Excel (.xlsx)
			</a>
		</div>
		<div id="contenedor-tabla" style="margin-top: 2px;">
			<!-- El loader se mantiene fijo aquí -->
			<div id="img_loader" style="display:none;text-align:center">
				<img src="imagenes/ajax-loader.gif" alt="Procesando...">
			</div>

			<!-- Aquí es donde volcaremos dinámicamente los textos o las tablas -->
			<div id="resultado-reporte">
				<p style="color: #666; text-align: center;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>
			</div>
		</div>
	</section>
</div>
<script>
	document.addEventListener("DOMContentLoaded", () => {
		const selParalelo = document.getElementById("selector-paralelo");
		const selAsignatura = document.getElementById("selector-asignatura");
		const selPeriodoLectivo = document.getElementById("id_periodo_lectivo");

		// Referencias al loader, al contenedor interno y al nuevo botón de Excel
		const loader = document.getElementById("img_loader");
		const resultado = document.getElementById("resultado-reporte");
		const btnExcel = document.getElementById("btn-excel-php");

		// Evento 1: Cambia el Paralelo -> Carga las Asignaturas
		selParalelo.addEventListener("change", () => {
			const idParalelo = selParalelo.value;

			// Limpiar selectores secundarios, ocultar botón de Excel y vaciar contenido previo
			selAsignatura.innerHTML = '<option value="">-- Seleccione primero un paralelo --</option>';
			selAsignatura.disabled = true;
			if (btnExcel) btnExcel.style.display = "none";
			resultado.innerHTML = '<p style="color: #666;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>';

			if (!idParalelo) return;

			// MOSTRAR LOADER
			loader.style.display = "block";
			resultado.innerHTML = ""; // Limpiamos el texto mientras carga

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
					selAsignatura.innerHTML = htmlOpciones;
					selAsignatura.disabled = false; // Activamos el campo
				})
				.catch(error => {
					console.error("Error cargando asignaturas:", error);
					resultado.innerHTML = '<p style="color: #dd4b39;">Error al cargar asignaturas.</p>';
				})
				.finally(() => {
					// OCULTAR LOADER (Se ejecuta siempre al terminar)
					loader.style.display = "none";
				});
		});

		// Evento 2: Cambia la Asignatura -> Genera el reporte final unificado
		selAsignatura.addEventListener("change", () => {
			const idParalelo = selParalelo.value;
			const idAsignatura = selAsignatura.value;
			const idPeriodoLectivo = selPeriodoLectivo ? selPeriodoLectivo.value : "";

			// Validar que ambos selectores tengan un valor válido antes de enviar
			if (!idParalelo || !idAsignatura) {
				if (btnExcel) btnExcel.style.display = "none";
				resultado.innerHTML = '<p style="color: #666;">Seleccione un paralelo y una asignatura para ver las calificaciones.</p>';
				return;
			}

			// MOSTRAR LOADER y ocultar botón de Excel mientras se procesa la nueva petición
			loader.style.display = "block";
			if (btnExcel) btnExcel.style.display = "none";
			resultado.innerHTML = ""; // Limpiamos la tabla o mensajes previos

			// Usar URLSearchParams para asegurar el correcto mapeo de los datos en $_POST
			const datosPost = new URLSearchParams();
			datosPost.append('id_paralelo', idParalelo);
			datosPost.append('id_asignatura', idAsignatura);
			datosPost.append('id_periodo_lectivo', idPeriodoLectivo);

			// Petición AJAX enviando los dos identificadores al script del reporte
			fetch("obtener_reporte.php", {
					method: "POST",
					headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					},
					body: datosPost
				})
				.then(response => {
					if (!response.ok) {
						throw new Error(`Error en el servidor: ${response.status}`);
					}
					return response.text();
				})
				.then(htmlTabla => {
					// Reemplaza el contenedor interno con la estructura de la tabla dinámica
					resultado.innerHTML = htmlTabla;

					// Si la respuesta contiene una tabla válida, configuramos y mostramos el botón Excel
					if (btnExcel && resultado.querySelector("table")) {
						btnExcel.href = `calificaciones/exportar_phpspreadsheet.php?id_paralelo=${encodeURIComponent(idParalelo)}&id_asignatura=${encodeURIComponent(idAsignatura)}&id_periodo_lectivo=${encodeURIComponent(idPeriodoLectivo)}`;
						btnExcel.style.display = "inline-block";
					}
				})
				.catch(error => {
					console.error("Error:", error);
					resultado.innerHTML = '<p style="color: #dd4b39;">Error al generar el reporte de la asignatura. Verifique los registros de PHP.</p>';
					if (btnExcel) btnExcel.style.display = "none";
				})
				.finally(() => {
					// OCULTAR LOADER (Se ejecuta siempre al terminar)
					loader.style.display = "none";
				});
		});
	});
</script>