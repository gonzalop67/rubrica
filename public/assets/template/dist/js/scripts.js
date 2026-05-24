$(document).ready(function () {
    // $('ul.sidebar-menu').find('li.active').parent('li').addClass('active');
    $("ul.sidebar-menu").find("li.active").parent().parent().addClass("active");

    //Autoclose
    window.setTimeout(function () {
        $(".alert").fadeOut(1500, 0);
    }, 3000); //3 segundos y desaparece
});
