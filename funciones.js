var x;
x=$(document);
x.ready(inicializarEventos);

function inicializarEventos()
{
  var x;
  x=$("tr");
  x.click(presionFila);
}

function presionFila()
{
  var x;
  x=$(this);
  x.css("background-color","eeeeee");
}

function truncar(num, places)
{
  num = num.toString();
  num = num.slice(0, (num.indexOf("."))+places);
  return Number(num);
}