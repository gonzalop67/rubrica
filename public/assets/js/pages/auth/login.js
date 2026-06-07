const usuario = document.getElementById("usuario");
const clave = document.getElementById("clave");
const perfil = document.getElementById("perfil");

const form = document.getElementById("frmLogin");

const img_loader = document.getElementById("img_loader");
const mensaje = document.getElementById("mensaje");

let perfilSeleccionado;

// Escuchar el evento submit
form.addEventListener('submit', function (event) {
    event.preventDefault(); // Evita el envío automático

    let errores = 0;

    // Elimino algún mensaje de error previo
    document.querySelector("#mensaje").innerHTML = "";

    if (usuario.value == "" || clave.value == "" || perfil.value == "") {
        if (usuario.value == "") {
            usuario.classList.add("is-invalid");
            document.getElementById("error-usuario").innerHTML = "El campo Usuario es obligatorio.";
            errores++;
        } else {
            usuario.classList.remove("is-invalid");
            document.getElementById("error-usuario").innerHTML = "";
        }

        if (clave.value == "") {
            clave.classList.add("is-invalid");
            document.getElementById("error-clave").innerHTML = "El campo Contraseña es obligatorio.";
            errores++;
        } else {
            clave.classList.remove("is-invalid");
            document.getElementById("error-clave").innerHTML = "";
        }

        if (perfil.value == "") {
            perfil.classList.add("is-invalid");
            document.getElementById("error-perfil").innerHTML = "El campo Perfil es obligatorio.";
            errores++;
        } else {
            perfil.classList.remove("is-invalid");
            document.getElementById("error-perfil").innerHTML = "";
        }
    }

    if (usuario.value.length < 5) {
        usuario.classList.add("is-invalid");
        document.getElementById("error-usuario").innerHTML = "El campo Nombre de Usuario debe tener al menos 5 caracteres.";
        errores++;
    }

    if (clave.value.length < 5) {
        clave.classList.add("is-invalid");
        document.getElementById("error-clave").innerHTML = "El campo Contraseña debe tener al menos 5 caracteres.";
        errores++;
    }

    if (!validarUsername(usuario.value)) {
        usuario.classList.add("is-invalid");
        document.getElementById("error-usuario").innerHTML = "Por favor ingrese solo caracteres alfanuméricos entre 5 y 64 caracteres.";
        errores++;
    }

    if (errores == 0) {
        verificar_login();
    }
});

function validarUsername(username) {
    const reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{5,64})$/i;
    return reg_nombres.test(username);
}

async function verificar_login() {
    // Eliminar todos los mensajes de error
    usuario.classList.remove("is-invalid");
    document.getElementById("error-usuario").innerHTML = "";
    clave.classList.remove("is-invalid");
    document.getElementById("error-clave").innerHTML = "";
    perfil.classList.remove("is-invalid");
    document.getElementById("error-perfil").innerHTML = "";
    // Desplegar el loader image
    document.querySelector("#img_loader").style.display = "block";
    // Obtener todos los campos a enviar mediante FormData
    let frmLogin = document.querySelector("#frmLogin");
    const data = new FormData(frmLogin);
    // Llamar al método auth/login que verifica si existe el email y clave
    try {
        let resp = await fetch(base_url + "/auth/login", {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            body: data,
        });
        json = await resp.json();
        // Desplegar el loader image
        document.querySelector("#img_loader").style.display = "none";
        if (!json.error) {
            // Construimos la base de la URL de forma limpia
            const urlDestino = base_url.endsWith('/') ? base_url : base_url + '/';

            switch (json.slug) {
                case 'administrador':
                    // console.log(urlDestino + "admin/dashboard")
                    location.href = urlDestino + "admin/dashboard";
                    break;
                case 'autoridad':
                    //location.href = urlDestino + "autoridad/dashboard";
                    break;
                case 'coordinador':
                    //location.href = urlDestino + "coordinador/dashboard";
                    break;
                case 'docente':
                    //location.href = urlDestino + "docentes/dashboard";
                    break;
                case 'secretaria':
                    //location.href = urlDestino + "secretaria/dashboard";
                    break;
                case 'tutor':
                    //location.href = urlDestino + "tutor/dashboard";
                    break;
                default:
                    alert("Rol no reconocido: " + json.slug);
                    break;
            }
        } else {
            // Existen errores de validación
            let errors = "";

            Object.entries(json.errors).forEach(([clave, valor]) => {
                errors = errors + `<li>${valor}</li>`
            });

            var error = '<div class="alert alert-danger" role="alert">' +
                '<p><i class="bi bi-ban"></i> Existen errores:</p>' +
                '<ul>' +
                errors +
                '</ul>' +
                '</div>';

            img_loader.style.display = "none";
            mensaje.innerHTML = error;
            usuario.focus();
        }
    } catch (error) {
        console.log("Ocurrió un error: " + error)
    }
}