/**
 * Created by luis on 12/10/15.
 */
$(document).ready(function () {
    escuchaLogin();
});
function escuchaLogin() {
    $("#container-form-login").find("> #form-login > #login").click(function () {
        validaParaLogin();
    });
    $("#container-form-login").find("> #form-login > #user").keydown(function (e) {
        switch (e.keyCode) {
            case 13:
                validaParaLogin();
                break;
        }
    });
    $("#container-form-login").find("> #form-login > #pass").keydown(function (e) {
        switch (e.keyCode) {
            case 13:
                validaParaLogin();
                break;
        }
    });
}
function validaParaLogin() {
    var idUsr = $("#container-form-login").find("> #form-login > #user").val();
    var pass = $("#container-form-login").find("> #form-login > #pass").val();
    if (idUsr && pass) {
        ocultaTooltipsFormLogin();
        var datos = {which: "login", idUsr: idUsr, pass: pass};
        var contenedor = "#container-response";
        login(datos, contenedor, "./inc/manager.php");
    } else {
        if (!idUsr) {
            $("#container-form-login").find("> #form-login > #tooltip-user").show("fast");
        }
        if (!pass) {
            $("#container-form-login").find("> #form-login > #tooltip-pass").show("fast");
        }
    }
}
function ocultaTooltipsFormLogin() {
    $("#container-form-login").find("> #form-login > #tooltip-user").hide("fast");
    $("#container-form-login").find("> #form-login > #tooltip-pass").hide("fast");
    $("#container-form-login").find("> #form-login > #login").empty();
}
function login(datos, contenedor, url) {
    $.ajax({
        url: url,
        data: {
            valores: datos
        },
        type: "POST",
        dataType: "html",
        beforeSend: function () {
            $(contenedor).text("Trabajando...");
        },
        success: function (data) {
            console.log(data);
            switch (parseInt(data)) {
                case 0:
                    $(contenedor).show("slow");
                    $(contenedor).text("Bienvenido, cargando...");
                    redirecciona("board.php");
                    break;
                case 1:
                    $(contenedor).show("slow");
                    $(contenedor).text("Contraseña incorrecta.");
                    break;
                case 2:
                    $(contenedor).show("slow");
                    $(contenedor).text("El usuario con el que intentas acceder no existe.");
                    break;
            }
        },
        error: function (errorThrown) {
            $(contenedor).text("Hubo un error: " + errorThrown);
        }
    });
}

function logout(datos, url) {
    $.ajax({
        url: url,
        data: {
            valores: datos
        },
        type: "POST",
        dataType: "html",
        beforeSend: function () {
            console.log("Trabajando...");
        },
        success: function (data) {
            switch (parseInt(data)) {
                case 0:
                    redirecciona("login.php");
                    break;
                default :
                    console.log("Error fatal cerrando sesión");
                    break;
            }
        },
        error: function (errorThrown) {
            console.log("Hubo un error: " + errorThrown);
        }
    });
}
function redirecciona(url) {
    window.location.replace(url);
}


