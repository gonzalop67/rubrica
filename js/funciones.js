var cursor;
if (document.all) {
  // Está utilizando EXPLORER
  cursor = "hand";
} else {
  // Está utilizando MOZILLA/NETSCAPE
  cursor = "pointer";
}

function eliminaEspacios(cadena) {
  var x = 0,
    y = cadena.length - 1;
  while (cadena.charAt(x) == " ") x++;
  while (cadena.charAt(y) == " ") y--;
  return cadena.substr(x, y - x + 1);
}

function setearIndice(nombreCombo, indice) {
  for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
    if (document.getElementById(nombreCombo).options[i].value == indice) {
      document.getElementById(nombreCombo).options[i].selected = indice;
    }
}

function sel_texto(input) {
  $(input).select();
}

function generarSlug (nombre, inputSlug) {
  // 1. Eliminar espacios al inicio y final
  let slug = nombre.trim();

  // 2. Convertir a minúsculas
  slug = slug.toLowerCase();

  // 3. Eliminar acentos y caracteres especiales mapeando
  slug = slug.replace(/[àáäâèéëêìíïîòóöôùúüûñç]/g, function (match) {
    return {
      à: "a",
      á: "a",
      ä: "a",
      â: "a",
      è: "e",
      é: "e",
      ë: "e",
      ê: "e",
      ì: "i",
      í: "i",
      ï: "i",
      î: "i",
      ò: "o",
      ó: "o",
      ö: "o",
      ô: "o",
      ù: "u",
      ú: "u",
      ü: "u",
      û: "u",
      ñ: "n",
      ç: "c",
    }[match];
  });

  // 4. Reemplazar caracteres no permitidos (letras, números, guiones y espacios) por un guion
  slug = slug.replace(/[^a-z0-9 -]/g, "");

  // 5. Reemplazar espacios múltiples y guiones por un solo guion
  slug = slug.replace(/[\s-]+/g, "-");

  // 6. Eliminar guiones al inicio o al final
  slug = slug.replace(/^-+|-+$/g, "");

  document.getElementById(inputSlug).value = slug;
};

$.ajaxSetup({
  error: function (jqXHR, textStatus, errorThrown) {
    if (jqXHR.status === 0) {
      alert("Not connect: Verify Network.");
    } else if (jqXHR.status == 404) {
      alert("Requested page not found [404]");
    } else if (jqXHR.status == 500) {
      alert("Internal Server Error [500].");
    } else if (textStatus === "parsererror") {
      alert("Requested JSON parse failed.");
    } else if (textStatus === "timeout") {
      alert("Time out error.");
    } else if (textStatus === "abort") {
      alert("Ajax request aborted.");
    } else {
      alert("Uncaught Error: " + jqXHR.responseText);
    }
  },
});

var Biblioteca = (function () {
  return {
    validacionGeneral: function (id, reglas, mensajes) {
      const formulario = $("#" + id);
      formulario.validate({
        rules: reglas,
        messages: mensajes,
        errorElement: "span", //default input error message container
        errorClass: "help-block help-block-error", // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: "", // validate all fields including form hidden input
        highlight: function (element, errorClass, validClass) {
          // hightlight error inputs
          $(element).closest(".form-group").addClass("has-error"); // set error class to the control group
        },
        unhighlight: function (element) {
          // revert the change done by hightlight
          $(element).closest(".form-group").removeClass("has-error"); // set error class to the control group
        },
        success: function (label) {
          label.closest(".form-group").removeClass("has-error"); // set success class to the control group
        },
        errorPlacement: function (error, element) {
          if ($(element).is("select") && element.hasClass("bs-select")) {
            //PARA LOS SELECT BOOSTRAP
            error.insertAfter(element); //element.next().after(error);
          } else if (
            $(element).is("select") &&
            element.hasClass("select2-hidden-accessible")
          ) {
            element.next().after(error);
          } else if (element.attr("data-error-container")) {
            error.appendTo(element.attr("data-error-container"));
          } else {
            error.insertAfter(element); // default placement for everything else
          }
        },
        invalidHandler: function (event, validator) {
          //display error alert on form submit
        },
        submitHandler: function (form) {
          return true;
        },
      });
    },
    notificaciones: function (mensaje, titulo, tipo) {
      toastr.options = {
        closeButton: true,
        newestOnTop: true,
        positionClass: "toast-top-right",
        preventDuplicates: true,
        timeOut: "5000",
      };
      if (tipo == "error") {
        toastr.error(mensaje, titulo);
      } else if (tipo == "success") {
        toastr.success(mensaje, titulo);
      } else if (tipo == "info") {
        toastr.info(mensaje, titulo);
      } else if (tipo == "warning") {
        toastr.warning(mensaje, titulo);
      }
    },
  };
})();


