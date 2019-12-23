<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 30/09/15
 * Time: 09:42 AM
 */
include_once "./inc/manager.php";
$u = new Usuario();
$u->iniciarSesion();
if(isset($_SESSION['user'])){
    header("location: board.php");
}else{
    header("location: login.php");
}
?>

