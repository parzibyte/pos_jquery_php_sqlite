<script src="./js/menu.js"></script>
<script src="./js/login.js"></script>
<nav class="navbar navbar-default navbar-static-top" id="menu-principal">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span>&nbsp;Sublime
                POS</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a>Le atiende <b><?php $datos = $_SESSION['user'];
                            echo $datos['nombre'] . " " . $datos['apPat'] . " " .  $datos['apMat']; ?></b></a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a id="menu-ventas"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span>&nbsp;Ventas</a>
                </li>
                <li><a id="menu-productos"><span class="glyphicon glyphicon-book" aria-hidden="true"></span>&nbsp;Productos</a>
                </li>
                <li><a id="menu-registros"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>&nbsp;Registros</a>
                </li>
                <li><a id="menu-usuarios"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Usuarios</a>
                </li>
                <li><a id="logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Cerrar
                        sesión</a></li>
            </ul>
        </div>
    </div>
</nav>