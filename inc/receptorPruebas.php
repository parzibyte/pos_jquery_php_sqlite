<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 11/10/15
 * Time: 05:21 PM
 */
$cadena = "insert into 'foo' ('bar') VALUES ('2')";
inserta($cadena);
function inserta($cadena)
{
    @$bd = new SQLite3("pruebas2.db");
    $a = @$bd->exec($cadena);
    if (!$a) {
        inserta($cadena);
    }
}