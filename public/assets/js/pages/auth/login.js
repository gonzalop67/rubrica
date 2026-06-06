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