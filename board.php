<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 6/10/15
 * Time: 11:46 AM
 */
include_once "./inc/header.html";
include_once "./inc/manager.php";
$u = new Usuario();
$u->iniciarSesion();
if (isset($_SESSION['user'])) {
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    } else {
        $page = "ventas";
    }
    $listaBlanca = array("ventas", "productos", "pruebas", "registros");
    include_once "./inc/menu.php";
    if (in_array($page, $listaBlanca)) {
        $datosUsuario = $_SESSION['user'];
        $isAdmin = $_SESSION['isAdmin'];
        if ($isAdmin) {
            include_once "./inc/" . $page . ".html";
        } else {
            if ($page == "ventas") {
                include_once "./inc/ventas.html";
            } else {
                echo '<img class="img img-responsive center-block" src = "./img/error403.png" width="600px">';
            }
        }
    } else {
        echo '<img class="img img-responsive center-block" src = "./img/error403.png" width="600px">';
    }

} else {
    header("location: login.php");
}
include_once "./inc/footer.html";
?>

