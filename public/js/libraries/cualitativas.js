const referencias = () => {
    $.ajax({
        type: "POST",
        url: "scripts/obtener_referencias_cualitativas.php",
        dataType: "json",
        success: function (response) {
            console.log(response);
        }
    });
}