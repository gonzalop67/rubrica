// Corregido: Selectores con comillas
const formulario = document.getElementById('formulario');
const inputs = document.querySelectorAll('#formulario input');
const inputIdRole = document.getElementById('id_perfil');
const inputNombre = document.getElementById('nombre');
const inputSlug = document.getElementById('slug');
const buttonSubmit = document.getElementById('btn-submit');

const generarSlug = () => {
  // Corregido: Se añade 'let' para evitar variable global
  let nombre = inputNombre.value;
  let slug = nombre.trim();
  slug = slug.toLowerCase();

  slug = slug.replace(/[àáäâèéëêìíïîòóöôùúüûñç]/g, function (match) {
    return {
      à: 'a', á: 'a', ä: 'a', â: 'a', è: 'e', é: 'e', ë: 'e', ê: 'e',
      ì: 'i', í: 'i', ï: 'i', î: 'i', ò: 'o', ó: 'o', ö: 'o', ô: 'o',
      ù: 'u', ú: 'u', ü: 'u', ñ: 'n', ç: 'c'
    }[match];
  });

  slug = slug.replace(/[^a-z0-9 -]/g, '');
  slug = slug.replace(/[\s-]+/g, '-');
  slug = slug.replace(/^-+|-+$/g, '');
  inputSlug.value = slug;

  // Al generar el slug, lo validamos automáticamente
  validarCampo(expresiones.slug, inputSlug, 'slug');
};

inputNombre.addEventListener('blur', generarSlug);

const expresiones = {
  nombre: /^[a-zA-ZÀ-ÿ.\s]{4,64}$/,
  slug: /^[a-z0-9_-]{4,64}$/, // Corregido: Minúsculas para coincidir con generarSlug
};

const campos = { nombre: false, slug: false };

const validarFormulario = (e) => {
  // Si el usuario hizo clic en un elemento con la clase 'btn-volver', ignoramos el blur
  if (e.relatedTarget && e.relatedTarget.classList.contains('btn-volver')) {
    return;
  }
  switch (e.target.name) {
    case 'nombre': validarCampo(expresiones.nombre, e.target, 'nombre'); break;
    case 'slug': validarCampo(expresiones.slug, e.target, 'slug'); break;
  }
};

// Corregido: Ahora la función retorna un booleano para el evento submit
const validarCampo = (expresion, input, campo) => {
  if (expresion && expresion.test(input.value)) {
    input.classList.remove('is-invalid');
    const errorEl = document.getElementById(`error-${campo}`);
    if (errorEl) errorEl.style.display = 'none';
    campos[campo] = true;
    return true; // Retorno indispensable
  } else {
    input.classList.add('is-invalid');
    const errorEl = document.getElementById(`error-${campo}`);
    if (errorEl) errorEl.style.display = 'block';
    campos[campo] = false;
    return false; // Retorno indispensable
  }
};

inputs.forEach((input) => {
  input.addEventListener('keyup', validarFormulario);
  input.addEventListener('blur', validarFormulario);
});

async function fntProcesar() {
  const url = buttonSubmit.innerText.trim() === 'Actualizar'
    ? '/roles/' + inputIdRole.value + '/update'
    : '/roles';

  console.log(url);

  try {
    const formData = new FormData(formulario);
    let resp = await fetch(base_url + url, {
      method: 'POST',
      mode: 'cors',
      cache: 'no-cache',
      body: formData,
    });

    const json = await resp.json();

    if (json.ok) {
      Swal.fire({
        title: '¡Completado!',
        text: json.mensaje,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        formulario.reset();
        window.location.href = base_url + '/roles';
      });
    } else if (json.errors) {
      // Recorremos el diccionario de errores que envió el backend [campo => mensaje]
      Object.keys(json.errors).forEach((campo) => {
        const mensajeError = json.errors[campo];

        // 1. Buscamos el div contenedor del error (ej: error-nombre, error-slug)
        const errorContainer = document.getElementById(`error-${campo}`);

        // CORREGIDO: Buscamos el elemento visual asegurando extraer el índice [0] de la colección
        const elementosByName = document.getElementsByName(campo);
        let elemento = (elementosByName.length > 0) ? elementosByName[0] : document.getElementById(campo);

        // 2. Inyectamos el texto del backend y mostramos los estilos de alerta
        if (errorContainer) {
          errorContainer.innerHTML = mensajeError;
          errorContainer.style.display = 'block';
        }

        // 3. Agregamos la clase de Bootstrap al input para que se marque en rojo
        if (elemento) {
          elemento.classList.remove('is-valid');
          elemento.classList.add('is-invalid');
        }
      });

      // CORREGIDO: Alerta SweetAlert2 para notificar al usuario
      Swal.fire({
        title: 'Error de Validación',
        text: 'Por favor, corrige los campos remarcados en rojo.',
        icon: 'error'
      });
    } else if (json.mensaje) {
      Swal.fire({ title: 'Error de Proceso', text: json.mensaje, icon: 'error' });
    }
  } catch (error) {
    console.error('Error crítico en el servidor:', error);
  }
}

formulario.addEventListener('submit', (e) => {
  e.preventDefault();
  let formularioValido = true;

  inputs.forEach((input) => {
    const nombreCampo = input.name || input.id;
    if (!nombreCampo || !expresiones[nombreCampo]) return; // Salta si no hay reglas

    const esValido = validarCampo(expresiones[nombreCampo], input, nombreCampo);
    if (!esValido) {
      formularioValido = false;
    }
  });

  if (formularioValido) {
    fntProcesar();
  } else {
    Swal.fire({
      title: 'Formulario incompleto',
      text: 'Por favor, revisa las casillas marcadas en rojo antes de continuar.',
      icon: 'error',
    });
  }
});
