<?php

?>
<script src="./jquery.js"></script>
<script>
    var datos = {
        which: "prueba"
    };
    $(document).ready(function () {
        $.ajax({
            url: "./receptorPruebas.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                console.log("Procesando...");
            },
            success: function (data) {
                var a = eval(data);
                $.each(a, function (a,b,c) {
                    console.log(b);
                });
            },
            error: function (errorThrown) {
                console.log("Hubo un error: " + errorThrown);
            }
        });
    });
</script>
