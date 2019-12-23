<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 30/09/15
 * Time: 09:47 AM
 */
const DB_NAME = "pos.db", LOG_FILE_NAME = "registros.log", TIME_BUSY = 5000;
$bd = new SQLite3(DB_NAME);
date_default_timezone_set('America/Mexico_City');
error_reporting(0);
//ini_set('display_errors', 0);
//$a = new Administrador();
//$a ->registrarAdministrador("Usuario", "de", "pruebas", "123");
if (isset($_POST['valores'])) {
    $arreglo = $_POST['valores'];
    $which = $arreglo['which'];
    switch ($which) {
        case "login":
            $u = new Usuario();
            $t = new Tablas();
            $t->crearUsuarios();
            $u->login($arreglo['idUsr'], $arreglo['pass']);
            break;
        case "logout":
            $u = new Usuario();
            $u->cerrarSesion();
            break;
        case "dameProd":
            $p = new Productos();
            $p->devolverProducto($arreglo['producto']);
            break;
        case "dameUser":
            $u = new Usuario();
            $u->devolverUsuario($arreglo["idUsuario"]);
            break;
        case "procesaVenta":
            $u = new Usuario();
            $u->iniciarSesion();
            $user = $_SESSION['user'];
            $v = new Ventas();
            $v->registrarVenta($user['idUsr'], $arreglo['productos'], $arreglo['total'], $arreglo['subtotal'], $arreglo["conDescuento"]);
            break;
        case "insertaProd":
            $p = new Productos();
            $datosProd = $arreglo["datosProd"];
            $idProd = $datosProd[0];
            $nombreProd = $datosProd[1];
            $descripcionProd = $datosProd[2];
            $cantidadProd = $datosProd[3];
            $precioProd = $datosProd[4];
            $p->registrarProducto($idProd, $nombreProd, $descripcionProd, $cantidadProd, $precioProd);
            break;
        case "actualizaProd":
            $p = new Productos();
            $datosProd = $arreglo["datosProd"];
            $idProd = $datosProd[0];
            $nombreProd = $datosProd[1];
            $descripcionProd = $datosProd[2];
            $cantidadProd = $datosProd[3];
            $precioProd = $datosProd[4];
            $p->editarProducto($idProd, $nombreProd, $descripcionProd, $cantidadProd, $precioProd);
            break;
        case "editaProductoNuevo":
            $p = new Productos();
            $p->editaProductoNuevo($arreglo['idProd'], $arreglo['columnaProd'], $arreglo['nuevoDato']);
            break;
        case "eliminaProd":
            $p = new Productos();
            $idProd = $arreglo["idProd"];
            $p->eliminarProducto($idProd);
            break;
        case "aumentaProd":
            $p = new Productos();
            $datosProd = $arreglo['datosProd'];
            $idProd = $datosProd[0];
            $cantidadProd = $datosProd[1];
            $p->aumentaExistenciaProducto($idProd, $cantidadProd);
            break;
        case "consulta":
            $table = $arreglo['table'];
            $begin = $arreglo["begin"];
            $end = $arreglo["end"];
            $orderby = $arreglo["orderby"];
            $order = $arreglo["order"];
            $a = new Administrador();
            $a->listarCualquierTabla($table, $begin, $end, $orderby, $order);
            break;
        case "columnasTabla":
            $tabla = $arreglo['table'];
            $a = new Administrador();
            $a->filasExistentes($tabla);
            break;
        case "cuantosHay":
            $tabla = $arreglo["table"];
            $a = new Administrador();
            $a->cuantosHay($tabla);
            break;
        case "consultaProductos":
            $p = new Productos();
            $p->listaProductosNew();
            break;
    }
}

/**
 * Class Tablas
 */
class Tablas
{
    /**
     *Esto simplemente crea la tabla productos,
     * no importa si la llamas cuando la tabla está creada
     * ya que solamente la creará si no existe.
     */
    public function crearProductos()
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $stmt = '
                CREATE TABLE IF NOT EXISTS "productos" (
                  "idProd" TEXT(255) NOT NULL,
                  "nombre" TEXT(255) NOT NULL,
                  "fecha" TEXT(255) NOT NULL,
                  "hora" TEXT(255) NOT NULL,
                  "descripcion" TEXT(255) NULL,
                  "existencia" INTEGER NOT NULL,
                  "precioCompra" REAL NOT NULL,
                  "precioVenta" REAL NOT NULL
                );
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se creó correctamente la tabla productos");
        } else {
            $adm->escribirRegistro("Error al crear la tabla productos");
        }
        $bd->close();
        unset($bd);
    }

    /**
     *Esto simplemente crea la tabla usuarios,
     * no importa si la llamas cuando la tabla está creada
     * ya que solamente la creará si no existe.
     */

    public function crearUsuarios()
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $stmt = '
                CREATE TABLE IF NOT EXISTS "usuarios" (
                  "idUsr" TEXT(255) NOT NULL,
                  "nombre" TEXT(255) NOT NULL,
                  "segNombre" TEXT(255) NULL,
                  "apPat" TEXT(255) NOT NULL,
                  "apMat" TEXT(255) NOT NULL,
                  "claveAcc" TEXT(255) NOT NULL,
                  "ventasHechas" INTEGER NOT NULL
                );
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se creó correctamente la tabla usuarios");
        } else {
            $adm->escribirRegistro("Error al crear la tabla usuarios");
        }
        $bd->close();
        unset($bd);
    }

    /**
     *Esto simplemente crea la tabla ventas,
     * no importa si la llamas cuando la tabla está creada
     * ya que solamente la creará si no existe.
     */
    public function crearVentas()
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $stmt = '
                CREATE TABLE IF NOT EXISTS "ventas" (
                  "idVenta" TEXT(255) NOT NULL,
                  "fecha" TEXT(255) NOT NULL,
                  "hora" TEXT(255) NOT NULL,
                  "idAutor" TEXT(255) NOT NULL,
                  "productos" TEXT(1024) NOT NULL,
                  "total" REAL NOT NULL,
                  "subtotal" REAL NOT NULL,
                  "conDescuento" INTEGER NOT NULL
                );
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se creó correctamente la tabla ventas");
        } else {
            $adm->escribirRegistro("Error al crear la tabla ventas");
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param string $tabla Nombre de la tabla que deseas eliminar.
     */
    public function eliminarTabla($tabla)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $stmt = '
                DROP TABLE IF EXISTS "' . $tabla . '";
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se eliminó la tabla " . $tabla);
        } else {
            $adm->escribirRegistro("Error al eliminar la tabla " . $tabla);
        }
        $bd->close();
        unset($bd);
    }
    /**
     * @param string $tabla Nombre de la tabla que quieres eliminar
     * @return bool Verdadero si el proceso fue correcto. Falso en caso contrario.
     * */
    public function vaciarTabla($tabla){
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $stmt = '
        DELETE FROM "'.$tabla.'";
        ';
        $resultado = $bd->exec($stmt);
        $bd->close();
        unset($bd);
        return $resultado;
    }
}

/**
 * Class Fecha
 */
class Fecha
{

    /**
     * @return bool|string
     */
    public function diaAño()
    {
        return date("z");
    }

    /**
     * @return string
     */
    public function fechaCompletaNum()
    {
        return $this->diaMes() . "-" . $this->mesAño() . "-" . $this->año();
    }

    /**
     * @return bool|string
     */
    public function diaMes()
    {
        return date("d");
    }

    /**
     * @return bool|string
     * Y laclsadlsadlsaldlsadlsa
     */
    public function mesAño()
    {
        return date("n");
    }

    /**
     * @return bool|string
     */
    public function año()
    {
        return date("Y");
    }

    /**
     * @return string
     */
    public function fechaYHoraString()
    {
        return $this->fechaCompletaString() . " " . $this->horaCompleta();

    }

    /**
     * @return string
     */
    public function fechaCompletaString()
    {
        return $this->diaSemanaString() . ", " . $this->diaMes() . " de " . $this->mesAñoString() . " del " . $this->año();
    }

    /**
     * @return string
     */
    public function diaSemanaString()
    {
        $diaSemana = "Domingo";
        switch ($this->diaSemana()) {
            case 1:
                $diaSemana = "Lunes";
                break;
            case 2:
                $diaSemana = "Martes";
                break;
            case 3:
                $diaSemana = "Miércoles";
                break;
            case 4:
                $diaSemana = "Jueves";
                break;
            case 5:
                $diaSemana = "Viernes";
                break;
            case 6:
                $diaSemana = "Sábado";
                break;
        }
        return $diaSemana;
    }

    /**
     * @return bool|string
     */
    public function diaSemana()
    {
        return date("w");
    }

    /**
     * @return string
     */
    public function mesAñoString()
    {
        $mes = "Enero";
        switch ($this->mesAño()) {
            case 2:
                $mes = "Febrero";
                break;
            case 3:
                $mes = "Marzo";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Mayo";
                break;
            case 6:
                $mes = "Junio";
                break;
            case 7:
                $mes = "Julio";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Septiembre";
                break;
            case 10:
                $mes = "Octubre";
                break;
            case 11:
                $mes = "Noviembre";
                break;
            case 12:
                $mes = "Diciembre";
                break;
        }
        return $mes;
    }

    /**
     * @return string
     */
    public function horaCompleta()
    {
        return $this->hora() . ":" . $this->minuto() . ":" . $this->segundo();
    }

    /**
     * @return bool|string
     */
    public function hora()
    {
        return date("G");
    }

    /**
     * @return bool|string
     */
    public function minuto()
    {
        return date("i");
    }

    /**
     * @return bool|string
     */
    public function segundo()
    {
        return date("s");
    }


}

/**
 * Class Usuario
 */
class Usuario
{
    /**Esta función aumenta las ventas del usuario. Es llamada cuando
     * se realiza una venta, entonces así se puede saber cuál es el usuario
     * que más ventas ha hecho en determinado tiempo. Y con esos datos podemos
     * generar estadísticas.
     * @param string $idUsr El <b>id</b> del usuario que quieres eliminar.
     */
    public function aumentarVentasUsuario($idUsr)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                    SELECT "ventasHechas" FROM "usuarios" WHERE "idUsr" = "' . $idUsr . '";
        ';
        $result = $bd->query($query);
        $fila = $result->fetchArray();
        $ventas = $fila[0];
        $ventas++;
        $stmt = '
                UPDATE "usuarios"
                SET "ventasHechas" = "' . $ventas . '"
                WHERE "idUsr" = "' . $idUsr . '";
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se añadió una venta al usuario con el id " . $idUsr);
        } else {
            $adm->escribirRegistro("Error al añadir venta al usuario con el id " . $idUsr);
        }
        $bd->close();
        unset($db);
    }

    /**
     * @param $idUsr
     */
    public function reducirVentaUsuario($idUsr)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                    SELECT "ventasHechas" FROM "usuarios" WHERE "idUsr" = "' . $idUsr . '";
        ';
        $result = $bd->query($query);
        $fila = $result->fetchArray();
        $ventas = $fila[0];
        $ventas--;
        $stmt = '
                UPDATE "usuarios"
                SET "ventasHechas" = "' . $ventas . '"
                WHERE "idUsr" = "' . $idUsr . '";
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se redujo una venta al usuario con el id " . $idUsr);
        } else {
            $adm->escribirRegistro("Error al reducir venta al usuario con el id " . $idUsr);
        }
        $bd->close();
        unset($db);
    }

    /**
     * @param $nombre
     * @param $apPat
     * @param $apMat
     * @param $claveAcc
     * @param string $segNombre
     */
    public function registrarUsuario($nombre, $apPat, $apMat, $claveAcc, $segNombre = "")
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $id = "u" . substr($adm->generaId(), -5);
        $claveAcc = $adm->encriptaPass($claveAcc);
        $stmt = '
                INSERT INTO "usuarios"
                ("idUsr", "nombre", "segNombre", "apPat", "apMat", "claveAcc", "ventasHechas")
                VALUES
                ("' . $id . '","' . $nombre . '","' . $segNombre . '","' . $apPat . '","' . $apMat . '","' . $claveAcc . '", "0")
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se insertó un usuario con el id " . $id);
        } else {
            $adm->escribirRegistro("Error al insertar un usuario con el id " . $id);
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idUsr
     */
    public function eliminarUsuario($idUsr)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeUsuario($idUsr)) {
            $stmt = '
                DELETE FROM "usuarios"
                WHERE "idUsr" = "' . $idUsr . '";
            ';
            $bd = new SQLite3(DB_NAME);
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se eliminó correctamenta al usuario con el id " . $idUsr);
            } else {
                $adm->escribirRegistro("Error al eliminar al usuario con el id " . $idUsr);
            }
        } else {
            echo "El usuario no existe";
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idUsr
     * @return bool
     */
    public function existeUsuario($idUsr)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                    SELECT count(*) FROM "usuarios"
                    WHERE "idUsr" = "' . $idUsr . '";
        ';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $existe = $fila[0];
        $val = false;
        if ($existe !== 0) {
            $val = true;
        }
        $bd->close();
        unset($bd);
        return $val;
    }

    /**
     *
     */
    public function listarUsuarios()
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                    SELECT * FROM "usuarios" LIMIT 50;
        ';
        $resultado = $bd->query($query);
        $tabla = '
        <table border = "1" class="datos" id="tabla-lista-usuarios">
        <tr>
            <td>Id</td>
            <td>Nombre</td>
            <td>Segundo nombre</td>
            <td>Apellido paterno</td>
            <td>Apellido materno</td>
            <td>Ventas hechas</td>
        </tr>
        ';
        while ($fila = $resultado->fetchArray()) {
            $idUsr = $fila['idUsr'];
            $nombre = $fila['nombre'];
            $segNombre = $fila['segNombre'];
            $apPat = $fila['apPat'];
            $apMat = $fila['apMat'];
            $ventasHechas = $fila['ventasHechas'];
            $tabla .= '<tr>
            <td>' . $idUsr . '</td>
            <td>' . $nombre . '</td>
            <td>' . $segNombre . '</td>
            <td>' . $apPat . '</td>
            <td>' . $apMat . '</td>
            <td>' . $ventasHechas . '</td>
        </tr>';
        }
        $tabla .= '</table>';
        echo $tabla;
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idUsr
     */
    public function buscarUsuario($idUsr)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeUsuario($idUsr)) {
            $query = '
                SELECT * FROM "usuarios"
                WHERE "idUsr" = "' . $idUsr . '";
        ';
            $resultado = $bd->query($query);
            $fila = $resultado->fetchArray();
            $tabla = '
        <table border = "1" class="datos" id="tabla-lista-usuarios">
        <tr>
            <td>Id</td>
            <td>Nombre</td>
            <td>Segundo nombre</td>
            <td>Apellido paterno</td>
            <td>Apellido materno</td>
            <td>Ventas hechas</td>
        </tr>
        ';
            $idUsr = $fila['idUsr'];
            $nombre = $fila['nombre'];
            $segNombre = $fila['segNombre'];
            $apPat = $fila['apPat'];
            $apMat = $fila['apMat'];
            $ventasHechas = $fila['ventasHechas'];
            $tabla .= '
        <tr>
            <td>' . $idUsr . '</td>
            <td>' . $nombre . '</td>
            <td>' . $segNombre . '</td>
            <td>' . $apPat . '</td>
            <td>' . $apMat . '</td>
            <td>' . $ventasHechas . '</td>
        </tr>';
            $tabla .= '</table>';
            echo $tabla;
        } else {
            echo "El usuario no existe";
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idUsr
     * @param $nombre
     * @param $apPat
     * @param $apMat
     * @param $claveAcc
     * @param string $segNombre
     */
    public function editarUsuario($idUsr, $nombre, $apPat, $apMat, $claveAcc, $segNombre = "")
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $claveAcc = $adm->encriptaPass($claveAcc);
        $stmt = '
                    UPDATE "usuarios"
                    SET
                    "nombre" = "' . $nombre . '",
                    "segNombre" = "' . $segNombre . '",
                    "apPat" = "' . $apPat . '",
                    "apMat" = "' . $apMat . '",
                    "claveAcc" = "' . $claveAcc . '"
                    WHERE "idUsr" = "' . $idUsr . '";
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se actualizaron los datos del usuario con el id " . $idUsr);
        } else {
            $adm->escribirRegistro("Error al actualizar los datos del usuario con el id " . $idUsr);
        }
        $bd->close();
        unset($bd);
    }

    /**
     *
     */
    public function cerrarSesion()
    {
        $this->iniciarSesion();
        $a = new Administrador();
        $data = $_SESSION['user'];
        $idUsr = $data["idUsr"];
        $a->escribirRegistro("El usuario " . $idUsr . " cerró sesión.");
        session_destroy();
        unset($_SESSION);
        echo 0;
    }

    /**
     *
     */
    public function iniciarSesion()
    {
        if (session_status() !== 2) {
            session_start();
        }
    }

    /**
     * @param $idUsr
     * @param $pass
     */
    public function login($idUsr, $pass)
    {
        if (is_array($user = $this->devolverUsuario($idUsr, false))) {
            if (password_verify($pass, $user['claveAcc'])) {
                $letraInicio = $idUsr[0];
                $isAdmin = false;
                if ($letraInicio == "a") {
                    $isAdmin = true;
                }
                $this->propagaDatos($user, $isAdmin);
                $a = new Administrador();
                $a->escribirRegistro("El usuario " . $idUsr . " inició sesión.");
                echo 0;
            } else {
                echo 1;
            }
        } else {
            echo 2;
        }
    }

    /**
     * @param $idUsr
     * @return array|bool
     */
    public function devolverUsuario($idUsr, $asJson = true)
    {
        if ($this->existeUsuario($idUsr)) {
            $bd = new SQLite3(DB_NAME);
            $bd->busyTimeout(TIME_BUSY);
            $query = '
                       SELECT * FROM "usuarios"
                       WHERE "idUsr" = "' . $idUsr . '";
                       ';
            $resultado = $bd->query($query);
            $fila = $resultado->fetchArray();
            $bd->close();
            unset($bd);
            $arrayReturn = array($fila["idUsr"], $fila["nombre"], $fila["segNombre"], $fila["apPat"], $fila["apMat"], $fila["ventasHechas"]);
            if ($asJson) {
                echo json_encode($arrayReturn);
            } else {
                return $fila;
            }
        }
    }

    /**
     * @param $user
     */
    public function propagaDatos($user, $isAdmin)
    {
        $this->iniciarSesion();
        $_SESSION['user'] = $user;
        $_SESSION['isAdmin'] = $isAdmin;
    }
}


/**
 * Class Ventas
 */
class Ventas
{

    /**
     * @param $who
     * @param $products
     */
    public function registrarVenta($who, $products, $totalOriginal, $subtotal, $conDescuento)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $prod = new Productos();
        $usr = new Usuario();
        $fecha = new Fecha();
        $t = new Tablas();
        $t->crearVentas();
        $bd->busyTimeout(TIME_BUSY);
        $cadenaProd = "";
        $total = 0;
        foreach ($products as $clave) {
            $queryTemp = '
                            SELECT * FROM "productos"
                            WHERE "idProd" = "' . $clave . '";
            ';
            $bd = new SQLite3(DB_NAME);
            $resultado = $bd->query($queryTemp);
            $fila = $resultado->fetchArray();
            $precio = $fila['precioVenta'];
            $total += $precio;
            $prod->retirarProductos($clave, 1);
            $cadenaProd .= $clave . ",";
        }
        $cadenaProd = trim($cadenaProd, ",");
        $id = "venta" . $adm->generaId();
        $stmt = '
                    INSERT INTO "ventas"
                    ("idVenta", "fecha", "hora", "idAutor", "productos", "total", "subtotal", "conDescuento")
                    VALUES
                    ("' . $id . '","' . $fecha->fechaCompletaNum() . '","' . $fecha->horaCompleta() . '","' . $who . '" , "' . $cadenaProd . '","' . $total . '", "' . $totalOriginal . '", "' . $conDescuento . '")
        ';
        $bd = new SQLite3(DB_NAME);
        if ($total == $subtotal) {
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se realizó correctamente una venta con el id " . $id);
                $usr->aumentarVentasUsuario($who);
                echo 0;
            } else {
                $adm->escribirRegistro("Error al realizar una venta con el id " . $id);
                echo 1;
            }
        }

        $bd->close();
        unset($db);
    }

    /**
     * @param $idVenta
     */
    public function eliminarVenta($idVenta)
    {
        $bd = new SQLite3(DB_NAME);
        $prod = new Productos();
        $usr = new Usuario();
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeVenta($idVenta)) {
            $query = '
                    SELECT * FROM "ventas"
                    WHERE "idVenta" = "' . $idVenta . '";
            ';
            $resultado = $bd->query($query);
            $filaP = $resultado->fetchArray();
            $productos = $filaP['productos'];
            $arrayProd = explode(',', $productos);
            foreach ($arrayProd as $value) {
                $fila = $prod->devolverProducto($value, false);
                $prod->registrarProducto($fila['idProd'], $fila['nombre'], $fila['descripcion'], 1, $fila['precio']);
            }
            $usr->reducirVentaUsuario($filaP['idAutor']);
            $stmt = '
                DELETE FROM "ventas"
                WHERE "idVenta" = "' . $idVenta . '";
            ';
            unset($bd);
            $bd = new SQLite3(DB_NAME);
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se eliminó la venta con el id " . $idVenta);
            } else {
                $adm->escribirRegistro("Error al eliminar la venta con el id " . $idVenta);
            }
        } else {
            echo "La venta no existe";
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idVenta
     * @return bool
     */
    public function existeVenta($idVenta)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                    SELECT count(*) FROM "ventas"
                    WHERE "idVenta" = "' . $idVenta . '";
        ';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $count = $fila[0];
        $existe = false;
        if ($count !== 0) {
            $existe = true;
        }
        $bd->close();
        unset($bd);
        return $existe;
    }

    /**
     *
     */
    public function listarVentas($begin, $end, $orderby = "idVenta", $order = "ASC")
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(5000);
        $query = '
            SELECT * FROM "ventas" ORDER BY "' . $orderby . '" ' . $order . ' LIMIT ' . $begin . ',' . $end . ' ;
        ';
        $tabla = '
        <table border = "2" class="datos" id = "tabla-lista-usuarios" >
                <tr>
                    <td > Id</td >
                    <td > Fecha</td >
                    <td>Hora</td>
                    <td>Autor</td>
                    <td>Productos</td>
                    <td>Total</td>
                </tr>
        ';
        $resultado = $bd->query($query);
        while ($fila = $resultado->fetchArray()) {
            $idVenta = $fila['idVenta'];
            $fecha = $fila['fecha'];
            $hora = $fila['hora'];
            $autor = $fila['idAutor'];
            $productos = $fila['productos'];
            $total = "$" . $fila['total'];
            $tabla .= '
                 <tr>
                    <td>' . $idVenta . '</td>
                    <td>' . $fecha . '</td>
                    <td>' . $hora . '</td>
                    <td>' . $autor . '</td>
                    <td>' . $productos . '</td>
                    <td>' . $total . '</td>
                </tr>
            ';
        }
        $tabla .= '</table>';
        $bd->close();
        unset($bd);
        echo $tabla;
    }

    /**
     *
     */
    public function buscarVenta()
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(5000);
        $query = '
            SELECT * FROM "ventas" LIMIT 50;
        ';
        $tabla = '
            <table border = "1" class="datos" id="tabla-lista-usuarios">
                <tr>
                    <td>Id</td>
                    <td>Fecha</td>
                    <td>Hora</td>
                    <td>Autor</td>
                    <td>Productos</td>
                    <td>Total</td>
                </tr>
        ';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $idVenta = $fila['idVenta'];
        $fecha = $fila['fecha'];
        $hora = $fila['hora'];
        $autor = $fila['idAutor'];
        $productos = $fila['productos'];
        $total = "$" . $fila['total'];
        $tabla .= '
             <tr>
                <td>' . $idVenta . '</td>
                <td>' . $fecha . '</td>
                <td>' . $hora . '</td>
                <td>' . $autor . '</td>
                <td>' . $productos . '</td>
                <td>' . $total . '</td>
            </tr>
        ';
        $tabla .= '</table>';
        $bd->close();
        unset($bd);
        echo $tabla;
    }
}

/**
 * Class Productos
 */
class Productos
{
    /**
     * Productos constructor.
     */

    /**
     * @param $idProd
     * @param $nombre
     * @param $descripcion
     * @param $existencia
     * @param $precio
     */
    public function registrarProducto($idProd, $nombre, $descripcion, $existencia, $precio)
    {
        if (!$this->existeProducto($idProd)) {
            $bd = new SQLite3(DB_NAME);
            $bd->busyTimeout(TIME_BUSY);
            $adm = new Administrador();
            $f = new Fecha();
            $fecha = $f->fechaCompletaNum();
            $hora = $f->horaCompleta();
            $stmt = '
                    INSERT INTO "productos"
                    ("idProd", "nombre", "fecha" ,"hora", "descripcion", "existencia", "precio")
                    VALUES
                    ("' . $idProd . '","' . $nombre . '","' . $fecha . '","' . $hora . '","' . $descripcion . '","' . $existencia . '","' . $precio . '")
            ';
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se insertó un nuevo producto con el id " . $idProd . " y el nombre " . $nombre);
                echo 0;
            } else {
                $adm->escribirRegistro("Eror al insertar un nuevo producto con el id " . $idProd . " y el nombre " . $nombre);
                echo 1;
            }
            $bd->close();
            unset($bd);
        } else {
            echo 2;
        }


    }

    /**
     * @param $idProd
     * @return bool
     */
    public function existeProducto($idProd)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
            SELECT count(*) FROM "productos"
            WHERE "idProd" = "' . $idProd . '";
        ';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $contador = $fila[0];
        $existe = false;
        if ($contador !== 0) {
            $existe = true;
        }
        $bd->close();
        unset($bd);
        return $existe;
    }

    public function aumentaExistenciaProducto($idProd, $cantidad)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeProducto($idProd)) {
            $adm = new Administrador();
            $query = '
                        SELECT "existencia" FROM "productos"
                        WHERE "idProd" = "' . $idProd . '";
            ';
            $resultado = $bd->query($query);
            $fila = $resultado->fetchArray();
            $valor = $fila[0];
            $valor += $cantidad;
            $stmt = '
                    UPDATE "productos"
                    SET "existencia" = " ' . $valor . ' "
                    WHERE "idProd" = "' . $idProd . '";
            ';
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se aumentó la existencia de un producto con el id " . $idProd);
                echo 0;
            } else {
                $adm->escribirRegistro("Error al aumentar la existencia de un producto con el id " . $idProd);
                echo 1;
            }
        } else {
            echo 2;
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idProd
     * @param $cuanto
     */
    public function retirarProductos($idProd, $cuanto)
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeProducto($idProd)) {
            $query = '
                SELECT "existencia" FROM "productos"
                WHERE "idProd" = "' . $idProd . '";
        ';
            $resultado = $bd->query($query);
            $fila = $resultado->fetchArray();
            $existencia = $fila['existencia'];
            $existencia -= $cuanto;
            $stmt = '
                    UPDATE "productos"
                    SET "existencia" = "' . $existencia . '"
                    WHERE "idProd" = "' . $idProd . '";
        ';
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se restó la existencia de un producto con el id " . $idProd);
            } else {
                $adm->escribirRegistro("Error al restar la existencia de un producto con el id " . $idProd);
            }
        }

        $bd->close();
        unset ($bd);
    }

    /**
     * @param $idProd
     */
    public function eliminarProducto($idProd)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeProducto($idProd)) {
            $stmt = '
                DELETE FROM "productos"
                WHERE "idProd" = "' . $idProd . '";
        ';
            $adm = new Administrador();
            if ($bd->exec($stmt)) {
                $adm->escribirRegistro("Se eliminó un producto con el id " . $idProd);
                echo 0;
            } else {
                $adm->escribirRegistro("Error al eliminar un producto con el id " . $idProd);
                echo 1;
            }
        } else {
            echo 2;
        }
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idProd
     * @return array
     */
    public function devolverProducto($idProd, $asJson = true)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                SELECT * FROM "productos" WHERE "idProd" = "' . $idProd . '";
        ';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $bd->close();
        unset($bd);
        $array_return = array($fila['idProd'], $fila['nombre'], $fila['descripcion'], $fila['existencia'], $fila['precioVenta']);
        if ($asJson) {
            echo json_encode($array_return);
        } else
            return $array_return;{
        }

    }

    /**
     *
     */
    public function listarProductos()
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                SELECT * FROM "productos" LIMIT 50;
        ';
        $resultado = $bd->query($query);
        $tabla = '<table border = "1" class = "datos" id = "tabla-lista-productos">
        <tr>
            <td>Clave</td>
            <td>Nombre</td>
            <td>Descripción</td>
            <td>Existencia</td>
            <td>Precio</td>
        </tr>';
        while ($fila = $resultado->fetchArray()) {
            $idProd = $fila['idProd'];
            $nombre = $fila['nombre'];
            $descripcion = $fila['descripcion'];
            $existencia = ($fila['existencia'] > 0) ? $fila['existencia'] : "Agotado";
            $precio = $fila['precio'];
            $tabla .= '
            <tr>
                <td>' . $idProd . '</td>
                <td>' . $nombre . '</td>
                <td>' . $descripcion . '</td>
                <td>' . $existencia . '</td>
                <td>' . $precio . '</td>
            </tr>
            ';
        }
        $tabla .= '</table>';
        echo $tabla;
        $bd->close();
        unset($bd);
    }

    public function listaProductosNew()
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $t = new Tablas();
        $t->crearProductos();
        $query = '
                SELECT * FROM "productos" LIMIT 10;
        ';
        $resultado = $bd->query($query);
        $tabla = '<table class="table table-striped table-hover table-condensed table-responsive table-bordered"><thead>';
        $tabla .= '
            <th>Código</th>
            <th>Nombre</th>
            <th>Fecha de registro</th>
            <th>Hora de registro</th>
            <th>Descripción</th>
            <th>Existencia</th>
            <th>Precio de compra</th>
            <th>Precio de venta</th>
        </thead>';
        while ($fila = $resultado->fetchArray()) {
            $idProd = $fila['idProd'];
            $nombre = $fila['nombre'];
            $fecha = $fila['fecha'];
            $hora = $fila['hora'];
            $descripcion = $fila['descripcion'];
            $existencia = ($fila['existencia'] > 0) ? $fila['existencia'] : "Agotado";
            $precioCompra = $fila['precioCompra'];
            $precioVenta = $fila['precioVenta'];
            $tabla .= '
                <tr>
                <td>' . $idProd . '</td>
                <td contenteditable = "true" data-container = "body" data-toggle = "tooltip" data-trigger = "manual" data-placement = "top"  class = "celda-editable" nombreCol = "nombre" id = "' . $idProd . '">' . $nombre . '</td>

                <td>' . $fecha . '</td>

                <td>' . $hora . '</td>

                <td contenteditable = "true" data-container = "body" data-toggle = "tooltip" data-trigger = "manual" data-placement = "top"  class = "celda-editable" nombreCol = "descripcion" id = "' . $idProd . '">' . $descripcion . '</td>

                <td contenteditable = "true" data-container = "body" data-toggle = "tooltip" data-trigger = "manual" data-placement = "top"  class = "celda-editable" nombreCol = "existencia" id = "' . $idProd . '">' . $existencia . '</td>

                <td contenteditable = "true" data-container = "body" data-toggle = "tooltip" data-trigger = "manual" data-placement = "top"  class = "celda-editable" nombreCol = "precioCompra" id = "' . $idProd . '">' . $precioCompra . '</td>

                <td contenteditable = "true" data-container = "body" data-toggle = "tooltip" data-trigger = "manual" data-placement = "top"  class = "celda-editable" nombreCol = "precioVenta" id = "' . $idProd . '">' . $precioVenta . '</td>
            </tr>
            ';
        }
        $tabla .= '</table>';
        echo $tabla;
        $bd->close();
        unset($bd);
    }

    /**
     *
     */
    public function buscarProducto()
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = '
                SELECT * FROM "productos" LIMIT 50;
        ';
        $resultado = $bd->query($query);
        $tabla = '<table border = "1" class = "datos" id = "tabla-lista-productos">
        <tr>
            <td>Clave</td>
            <td>Nombre</td>
            <td>Descripción</td>
            <td>Existencia</td>
            <td>Precio</td>
        </tr>';
        $fila = $resultado->fetchArray();
        $idProd = $fila['idProd'];
        $nombre = $fila['nombre'];
        $descripcion = $fila['descripcion'];
        $existencia = ($fila['existencia'] > 0) ? $fila['existencia'] : "Agotado";
        $precio = $fila['precio'];
        $tabla .= '
            <tr>
                <td>' . $idProd . '</td>
                <td>' . $nombre . '</td>
                <td>' . $descripcion . '</td>
                <td>' . $existencia . '</td>
                <td>' . $precio . '</td>
            </tr>
            ';
        $tabla .= '</table>';
        echo $tabla;
        $bd->close();
        unset($bd);
    }

    /**
     * @param $idProd
     * @param $nombre
     * @param $descripcion
     * @param $precio
     */
    public function editaProductoNuevo($idProd, $columna, $nuevoDato){
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeProducto($idProd)) {
            $stmt = '
                    UPDATE "productos"
                    SET "'.$columna.'" = "' . $nuevoDato . '"
                    WHERE "idProd" = "' . $idProd . '";
            ';
            if ($bd->exec($stmt)) {
                $adm = new Administrador();
                $adm->escribirRegistro("Se editó la columna ".$columna." del producto con el id " . $idProd);
                echo 0;
            } else {
                $adm = new Administrador();
                $adm->escribirRegistro("Error al editar la columna ".$columna." del producto con el id " . $idProd);
                echo 1;
            }
        } else {
            echo 2;
        }
        $bd->close();
        unset($bd);
    }
    public function editarProducto($idProd, $nombre, $descripcion, $existencia, $precio)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        if ($this->existeProducto($idProd)) {
            $stmt = '
                    UPDATE "productos"
                    SET "nombre" = "' . $nombre . '",
                    "descripcion" = "' . $descripcion . '",
                    "existencia" = "' . $existencia . '",
                    "precio" = "' . $precio . '"
                    WHERE "idProd" = "' . $idProd . '";
            ';
            if ($bd->exec($stmt)) {
                $adm = new Administrador();
                $adm->escribirRegistro("Se editaron los datos del producto con el id " . $idProd);
                echo 0;
            } else {
                $adm = new Administrador();
                $adm->escribirRegistro("Error al editar los datos del producto con el id " . $idProd);
                echo 1;
            }
        } else {
            echo 2;
        }
        $bd->close();
        unset($bd);
    }
}

/**
 * Class Administrador
 */
class Administrador
{
    public function listarCualquierTabla($table, $begin, $end, $orderby = "idVenta", $order = "ASC")
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(5000);
        $query = '
            SELECT * FROM "' . $table . '" ORDER BY "' . $orderby . '" ' . $order . ' LIMIT ' . $begin . ',' . $end . ' ;
        ';
        $cols = $this->devuelveFilasExistentes($table);
        $tabla = '<table class="table table-striped table-hover table-condensed table-responsive table-bordered"><thead>';
        foreach ($cols as $nameCol) {
            $tabla .= '<th>' . $nameCol . '</th>';
        }
        if ($table != "ventas") {
            $tabla .= "<th>Editar</th>";
            $tabla .= "<th>Eliminar</th>";
        }
        $tabla .= '</thead>';
        $resultado = $bd->query($query);
        while ($fila = $resultado->fetchArray()) {
            $tabla .= '<tr>';
            foreach ($cols as $nameCol) {
                $tabla .= '<td>' . $fila[$nameCol] . '</td>';
            }
            if ($table != "ventas") {
                $tabla .= '<td><button class = "btn btn-warning btn-editar-datos" id = "' . $fila[0] . '"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td>';
                $tabla .= '<td><button class = "btn btn-danger btn-eliminar-datos" id = "' . $fila[0] . '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>';
            }
            $tabla .= '</tr>';
        }
        $tabla .= '</table>';
        $bd->close();
        unset($bd);
        echo $tabla;
    }

    public function devuelveFilasExistentes($table)
    {

        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = 'PRAGMA table_info("' . $table . '");';
        $resultado = $bd->query($query);
        $nombres = array();
        while ($fila = $resultado->fetchArray()) {
            $nombres [] = $fila["name"];
        }
        $bd->close();
        unset($db);
        return $nombres;
    }

    public function cuantosHay($table)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = 'SELECT count(*) FROM "' . $table . '"';
        $resultado = $bd->query($query);
        $fila = $resultado->fetchArray();
        $count = $fila[0];
        echo $count;
        $bd->close();
        unset($db);
    }

    public function filasExistentes($table)
    {
        $bd = new SQLite3(DB_NAME);
        $bd->busyTimeout(TIME_BUSY);
        $query = 'PRAGMA table_info("' . $table . '");';
        $resultado = $bd->query($query);
        $nombres = array();
        while ($fila = $resultado->fetchArray()) {
            $nombres [] = $fila["name"];
        }
        echo json_encode($nombres);
        $bd->close();
        unset($db);
        return $nombres;
    }

    public function registrarAdministrador($nombre, $apPat, $apMat, $claveAcc, $segNombre = "")
    {
        $bd = new SQLite3(DB_NAME);
        $adm = new Administrador();
        $bd->busyTimeout(TIME_BUSY);
        $id = "a" . substr($adm->generaId(), -5);
        $claveAcc = $adm->encriptaPass($claveAcc);
        $stmt = '
                INSERT INTO "usuarios"
                ("idUsr", "nombre", "segNombre", "apPat", "apMat", "claveAcc", "ventasHechas")
                VALUES
                ("' . $id . '","' . $nombre . '","' . $segNombre . '","' . $apPat . '","' . $apMat . '","' . $claveAcc . '", "0")
        ';
        if ($bd->exec($stmt)) {
            $adm->escribirRegistro("Se insertó un administrador con el id " . $id);
        } else {
            $adm->escribirRegistro("Error al insertar un administrador con el id " . $id);
    }
        $bd->close();
        unset($bd);
    }

    /**
     * @return mixed
     */
    public function generaId()
    {
        return str_replace(".", "", microtime(true));
    }

    /**
     * @param $pass
     * @return bool|string
     */
    public function encriptaPass($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * @param $val
     */
    public function escribirRegistro($val)
    {
        $f = new Fecha();
        $log = fopen(LOG_FILE_NAME, "a");
        $text = $f->fechaCompletaString() . " , " . $f->horaCompleta() . " , " . $val . PHP_EOL;
        fwrite($log, $text);
        fclose($log);
    }

    public function ejecutaSQL($bd, $stmt)
    {
        $ok = $bd->exec($stmt);
        if (!$ok) {
            $this->ejecutaSQL($bd, $stmt);
        }
    }

}

?>