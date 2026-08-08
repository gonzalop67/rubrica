function sel_texto(input) {
    $(input).select();
}

function setearIndice(nombreCombo, indice) {
  for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
    if (document.getElementById(nombreCombo).options[i].value == indice) {
      document.getElementById(nombreCombo).options[i].selected = indice;
    }
}