<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 6/10/15
 * Time: 12:32 PM
 */
include_once "./inc/manager.php";
include_once "./inc/header.html";
$u = new Usuario();
$u->iniciarSesion();
if(isset($_SESSION['user'])){
    header("location: board.php");
}
?>
    <script src="./js/login.js"></script>
    <div class="col-md-4"></div>
    <div class="container container-fluid col-md-4" id="container-form-login">
    <form method="post" action="#" name="form-login" id="form-login">
        <input class="form-control" type="text" name="user" id="user" placeholder="Usuario"><span id="tooltip-user"
                                                                                                  class="tooltip"
                                                                                                  hidden>Rellena este campo</span><br><br>
        <input class="form-control" type="password" name="pass" id="pass" placeholder="Contraseña"><span
            id="tooltip-pass" class="tooltip" hidden>Rellena este campo</span><br><br>
        <input class="form-control btn btn-primary" type="button" name="login" id="login" value="Ingresar">
    </form>
    <span id="container-response"></span>
</div>
    <div class="col-md-4"></div>
<?php
include_once "./inc/footer.html";
?>