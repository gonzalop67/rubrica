// ==========================================
// 1. SELECCIÓN DE ELEMENTOS DEL DOM
// ==========================================
const formulario = document.getElementById("formulario");
// MODIFICACIÓN: Selecciona inputs, textareas Y ALSO SELECTS del formulario
const inputs = document.querySelectorAll("#formulario input:not([type='checkbox']):not([type='file']), #formulario textarea, #formulario select");

const inputIdUsuario = document.getElementById("id_usuario");
const inputTipoDocumento = document.getElementById("tipo_documento");
const inputNacionalidad = document.getElementById("nacionalidad");
const inputAbreviatura = document.getElementById("titulo");
const inputDescripcion = document.getElementById("descripcion_titulo");
const inputPrimerApellido = document.getElementById("primer_apellido");
const inputSegundoApellido = document.getElementById("segundo_apellido");
const inputPrimerNombre = document.getElementById("primer_nombre");
const inputSegundoNombre = document.getElementById("segundo_nombre");
const inputNombreCorto = document.getElementById("nombre_corto");
const inputNombreCompleto = document.getElementById("nombre_completo");
const inputUsuario = document.getElementById("username");
const inputEmail = document.getElementById("email");
const inputPassword = document.getElementById("password");
const buttonSubmit = document.getElementById("btn-save");
const inputFoto = document.getElementById("avatar");

// ==========================================
// 2. CONFIGURACIÓN DE REGLAS Y BLINDAJE (JS)
// ==========================================
const expresiones = {
    abreviatura: /^[a-zA-Z\.]{3,7}$/,
    descripcion: /^[a-zA-ZÀ-ÿ\s\.\,\-\_\:\;\(\)\n]{4,500}$/,
    apellidos: /^[a-zA-ZÀ-ÿ\s]{3,32}$/,
    nombres: /^[a-zA-ZÀ-ÿ\s]{3,32}$/,
    nombre_corto: /^[a-zA-ZÀ-ÿ\s\.]{3,32}$/,
    usuario: /^[a-zA-Z0-9\_\-]{4,16}$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,
    correo: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/
};

// BLINDAJE F12: Lista de campos estrictamente obligatorios
const camposObligatorios = {
    tipo_documento: true, // NUEVO
    nacionalidad: true,   // NUEVO
    abreviatura: true,
    descripcion: true,
    primer_apellido: true,
    primer_nombre: true,
    usuario: true,
    correo: true,
    password: true
};

const campos = {};

// ==========================================
// 3. LÓGICA DE GENERACIÓN AUTOMÁTICA Y MEDIOS
// ==========================================
const generarNombreCorto = () => {
    const primer_nombre = inputPrimerNombre.value.trim().split(" ");
    const primer_apellido = inputPrimerApellido.value.trim().split(" ");

    if (primer_nombre.length && primer_apellido.length && primer_nombre[0] !== "" && primer_apellido[0] !== "") {
        const prefijo = inputAbreviatura.value.trim() ? inputAbreviatura.value.trim() + " " : "";
        inputNombreCorto.value = `${prefijo}${primer_nombre[0]} ${primer_apellido[0]}`;
        inputNombreCompleto.value = inputPrimerApellido.value.trim() + " " + inputSegundoApellido.value.trim() + " " + inputPrimerNombre.value.trim() + " " + inputSegundoNombre.value.trim();
        validarCampo(expresiones.nombre_corto, inputNombreCorto, "nombre_corto");
    }
};

inputAbreviatura.addEventListener("blur", generarNombreCorto);
inputPrimerApellido.addEventListener("blur", generarNombreCorto);
inputSegundoApellido.addEventListener("blur", generarNombreCorto);
inputPrimerNombre.addEventListener("blur", generarNombreCorto);
inputSegundoNombre.addEventListener("blur", generarNombreCorto);

inputFoto.addEventListener("change", function (e) {
    const input = e.target;
    imgPreview = document.querySelector("#us_avatar");
    if (!input.files.length) return;
    file = input.files[0];
    objURL = URL.createObjectURL(file);
    imgPreview.src = objURL;
});

// ==========================================
// 4. NÚCLEO DE VALIDACIÓN DINÁMICA (TEXTO/TEXTAREA)
// ==========================================
const validarCampo = (expresion, input, campo) => {
    const errorContainer = document.getElementById(`error-${campo}`);
    const valor = input.value.trim();

    const esRequerido = input.hasAttribute("required") || camposObligatorios[campo];
    if (esRequerido && valor === "") {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        if (errorContainer) {
            errorContainer.innerHTML = "Este campo es estrictamente obligatorio.";
            errorContainer.style.display = "block";
        }
        campos[campo] = false;
        return false;
    }

    if (!esRequerido && valor === "") {
        input.classList.remove("is-invalid");
        input.classList.remove("is-valid");
        if (errorContainer) errorContainer.style.display = "none";
        campos[campo] = true;
        return true;
    }

    if (expresion && !expresion.test(valor)) {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        if (errorContainer) {
            if (errorContainer.innerHTML === "" || errorContainer.innerHTML.includes("obligatorio")) {
                errorContainer.innerHTML = "El formato ingresado no es válido.";
            }
            errorContainer.style.display = "block";
        }
        campos[campo] = false;
        return false;
    }

    if ((campo === "primer_nombre" || campo === "primer_apellido") && inputPrimerNombre.value !== "" && inputPrimerApellido.value !== "") {
        if (inputPrimerApellido.value.trim().toLowerCase() === inputPrimerNombre.value.trim().toLowerCase()) {
            input.classList.remove("is-valid");
            input.classList.add("is-invalid");
            if (errorContainer) {
                errorContainer.innerHTML = "El primer apellido y el primer nombre no pueden ser iguales.";
                errorContainer.style.display = "block";
            }
            campos[campo] = false;
            return false;
        }
    }

    input.classList.remove("is-invalid");
    input.classList.add("is-valid");
    if (errorContainer) errorContainer.style.display = "none";
    campos[campo] = true;
    return true;
};

// ==========================================
// NUEVO: FUNCIÓN EXCLUSIVA PARA VALIDAR SELECTS
// ==========================================
const validarSelect = (input, campo) => {
    const errorContainer = document.getElementById(`error-${campo}`);
    const valor = input.value;

    // Si el valor está vacío (generalmente la opción por defecto: value="")
    if (camposObligatorios[campo] && (valor === "" || valor === null)) {
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        if (errorContainer) {
            errorContainer.innerHTML = "Por favor, seleccione una opción válida de la lista.";
            errorContainer.style.display = "block";
        }
        campos[campo] = false;
        return false;
    }

    input.classList.remove("is-invalid");
    input.classList.add("is-valid");
    if (errorContainer) errorContainer.style.display = "none";
    campos[campo] = true;
    return true;
};

// Escucha en tiempo real para inputs, textareas y selects
const validarFormulario = (e) => {
    const nombreCampo = e.target.name || e.target.id;
    
    // Si el elemento es un SELECT, usamos su validador especializado
    if (e.target.tagName === "SELECT") {
        validarSelect(e.target, nombreCampo);
    } else {
        const expresionAsociada = expresiones[nombreCampo] || null;
        validarCampo(expresionAsociada, e.target, nombreCampo);
    }
};

inputs.forEach((input) => {
    input.addEventListener("keyup", validarFormulario);
    input.addEventListener("blur", validarFormulario);
    input.addEventListener("input", validarFormulario);
    input.addEventListener("change", validarFormulario); // Crucial para los <select>
});

// ==========================================
// 5. FUNCIÓN EXCLUSIVA PARA VALIDAR CHECKBOXES
// ==========================================
const validarCheckboxesPerfiles = () => {
    const perfilesChecked = $('input[type="checkbox"][name="roles[]"]:checked').length;
    const perfilErrorBlock = document.getElementById("error-roles");
    const perfilContainer = document.getElementById("roles-container");

    if (perfilesChecked === 0) {
        if (perfilContainer) perfilContainer.classList.add("is-invalid", "border", "border-danger", "p-2", "rounded");
        if (perfilErrorBlock) {
            perfilErrorBlock.innerHTML = "Debe asignar al menos un rol al usuario.";
            perfilErrorBlock.style.display = "block";
        }
        return false;
    } else {
        if (perfilContainer) perfilContainer.classList.remove("is-invalid", "border", "border-danger", "p-2", "rounded");
        if (perfilErrorBlock) perfilErrorBlock.style.display = "none";
        return true;
    }
};

// Escucha en tiempo real cuando cambia un checkbox
document.addEventListener("change", function (e) {
    if (e.target && e.target.type === "checkbox" && e.target.name === "roles[]") {
        validarCheckboxesPerfiles();
    }
});

// ==========================================
// 6. PROCESAMIENTO Y ENVÍO DE DATOS (FETCH)
// ==========================================
async function fntProcesar() {
    const url = buttonSubmit.innerText.trim() === "Actualizar" ? "/usuarios/" + inputIdUsuario.value + "/update" : "/usuarios/";
    
    // -------------------------------------------------------------------------
    // LIMPIEZA PREVIA: Resetea todos los errores anteriores antes de enviar
    // -------------------------------------------------------------------------
    inputs.forEach((input) => {
        input.classList.remove("is-invalid");
        const nombreCampo = input.name || input.id;
        const errorContainer = document.getElementById(`error-${nombreCampo}`);
        if (errorContainer) {
            errorContainer.innerHTML = "";
            errorContainer.style.display = "none";
        }
    });
    
    // Limpieza específica para el contenedor de roles/checkboxes
    const rolesContainer = document.getElementById("roles-container");
    const errorRoles = document.getElementById("error-roles");
    
    if (rolesContainer) {
        rolesContainer.classList.remove("is-invalid", "border", "border-danger", "p-2", "rounded");
    }
    if (errorRoles) {
        errorRoles.innerHTML = "";
        errorRoles.style.display = "none";
    }
    // -------------------------------------------------------------------------

    try {
        const formData = new FormData(formulario);
        let resp = await fetch(base_url + url, {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            body: formData,
        });
        const json = await resp.json();
        if (json.ok) {
            Swal.fire({
                title: "¡Completado!",
                text: json.mensaje,
                icon: "success",
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                formulario.reset();
                window.location.href = base_url + "/usuarios";
            });
        } else if (json.errors) {
            // CAPA DE DESPLIEGUE DINÁMICO EN CADA DIV DE ERROR (Del Backend)
            Object.keys(json.errors).forEach((campo) => {
                const mensajeError = json.errors[campo];

                // 1. Buscamos el div contenedor del error (ej: error-tipo_documento, error-username)
                const errorContainer = document.getElementById(`error-${campo}`);

                // 2. Buscamos el elemento visual (input, textarea, select o contenedor especial)
                let elemento = document.getElementsByName(campo)[0] || document.getElementById(campo);
                if (campo === "roles") {
                    elemento = document.getElementById("roles-container");
                }

                // 3. Inyectamos el texto del backend y mostramos los estilos de alerta
                if (errorContainer) {
                    errorContainer.innerHTML = mensajeError;
                    errorContainer.style.display = "block";
                }

                if (elemento) {
                    elemento.classList.remove("is-valid");
                    elemento.classList.add("is-invalid");
                    // Si es el bloque de roles, agregamos bordes adicionales de Bootstrap
                    if (campo === "roles") {
                        elemento.classList.add("border", "border-danger", "p-2", "rounded");
                    }
                }
            });

            // Alerta flotante para avisar al usuario que hay anomalías abajo
            Swal.fire({
                title: "Error de Validación",
                text: "Por favor, corrige los campos remarcados en rojo.",
                icon: "error"
            });
        } else if (json.mensaje) {
            Swal.fire({
                title: "Error de Proceso",
                text: json.mensaje,
                icon: "error"
            });
        }
    } catch (error) {
        console.error("Error crítico en el servidor: ", error);
        Swal.fire({
            title: "Error de Conexión",
            text: "No se pudo establecer comunicación con el servidor de la plataforma.",
            icon: "error"
        });
    }
}

// Evento Submit Final Sincronizado
formulario.addEventListener("submit", (e) => {
    e.preventDefault();
    let formularioValido = true;

    // 1. Validar dinámicamente todos los inputs, textareas y selects en el cliente
    inputs.forEach((input) => {
        const nombreCampo = input.name || input.id;
        if (!nombreCampo) return;

        let esValido = true;

        // Si el elemento evaluado en la cola es una lista desplegable (SELECT)
        if (input.tagName === "SELECT") {
            esValido = validarSelect(input, nombreCampo);
        } else {
            // Campos normales de texto con expresiones regulares
            const expresionAsociada = expresiones[nombreCampo] || null;
            esValido = validarCampo(expresionAsociada, input, nombreCampo);
        }

        if (!esValido) {
            formularioValido = false;
        }
    });

    // 2. Validar obligatoriamente los checkboxes llamando a la función
    const perfilesValidos = validarCheckboxesPerfiles();
    if (!perfilesValidos) {
        formularioValido = false;
    }

    // 3. Resolución final de envío
    if (formularioValido) {
        fntProcesar();
    } else {
        Swal.fire({
            title: "Formulario incompleto",
            text: "Por favor, revisa las casillas marcadas en rojo antes de continuar.",
            icon: "error",
        });
        
        // Foco automático en el primer elemento con error de validación frontend (Usabilidad)
        const primerError = document.querySelector(".is-invalid");
        if (primerError) {
            primerError.focus();
        }
    }
});
