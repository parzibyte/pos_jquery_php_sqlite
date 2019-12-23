/**
 * Created by luis on 12/10/15.
 */
$(document).ready(function () {
    escuchaMenu();
});
function escuchaMenu() {
    $("#menu-principal").find("#logout").click(function () {
        var datos = {which: "logout"};
        logout(datos, "./inc/manager.php");
    });
    $("#menu-principal").find("#menu-ventas").click(function () {
        redirecciona("board.php?page=ventas");
    });
    $("#menu-principal").find("#menu-productos").click(function () {
        redirecciona("board.php?page=productos");
    });
    $("#menu-principal").find("#menu-usuarios").click(function () {
        redirecciona("board.php?page=usuarios");
    });
    $("#menu-principal").find("#menu-registros").click(function () {
        redirecciona("board.php?page=registros");
    });
}
function redirecciona(url) {
    window.location.replace(url);
}
