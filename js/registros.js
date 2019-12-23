/**
 * Created by luis on 14/10/15.
 */
$(document).ready(function () {
    ocultaTodo();
    var CONTAINER_MAESTRO = $(document).find("#container-universal"),
        CONTAINER_MENSAJES = $(document).find("#container-mensajes"),
        LISTA_TABLAS = $(document).find("#opciones-tablas"),
        LISTA_ORDERBY = $(document).find("#order-by"),
        LISTA_ORDER = $(document).find("#order"),
        LISTA_BEGIN = $(document).find("#begin"),
        LISTA_END = $(document).find("#end"),
        begin = 0,
        end = 0,
        order = "",
        datos = [],
        arrayDatos = [],
        arrayProductos = [],
        arrayUsuarios = [],
        tabla = "";
    LISTA_TABLAS.change(function () {
        var val = $(this).val();
        switch (parseInt(val)) {
            case 0:
                tabla = "ventas";
                break;
            case 1:
                tabla = "productos";
                break;
            case 2:
                tabla = "ventas";//es ventas porque ingresos todavía no existe
                break;
            case 3:
                tabla = "usuarios";
                break;
            case 4:
                tabla = "ventas";//es ventas porque registros todavía no existe
                break;
        }
        llenaTodo();
    });

    LISTA_ORDERBY.change(function () {
        datos.orderby = $(this).val();
        limpiaContainer();
        consulta(datos);
    });
    LISTA_ORDER.change(function () {
        datos.order = $(this).val();
        limpiaContainer();
        consulta(datos);
    });
    LISTA_BEGIN.change(function () {
        datos.begin = $(this).val();
        limpiaContainer();
        consulta(datos);
    });
    LISTA_END.change(function () {
        datos.end = $(this).val();
        limpiaContainer();
        consulta(datos);
    });
    function llenaTodo(){
        limpiaTodo();
        desocultaTodo();
        datos = {
            which: "columnasTabla",
            table: tabla
        };
        llenaOrderBy(datos);
        datos = {
            which: "cuantosHay",
            table: tabla
        };
        llenaLimites(datos);
        datos = {
            which: "consulta",
            table: tabla,
            begin: 0,
            end: 10,
            orderby: LISTA_ORDERBY.val(),
            order: LISTA_ORDER.val()
        };
        consulta(datos);
    }
    function limpiaTodo() {
        arrayDatos.length = 0;
        arrayProductos.length = 0;
        datos.length = 0;
        LISTA_ORDERBY.find("option").remove();
        LISTA_BEGIN.find("option").remove();
        LISTA_END.find("option").remove();
        limpiaContainer();
    }

    function limpiaContainer() {
        CONTAINER_MAESTRO.find("table").remove();
    }
    function desocultaTodo() {
        $(document).find(".ocultable").show();
    }
    function ocultaTodo() {
        $(document).find(".ocultable").hide();
    }

    function llenaOrderBy(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                CONTAINER_MENSAJES.text("Llenando lista de order by...");
            },
            success: function (data) {
                var listaColumnas = eval(data);
                CONTAINER_MENSAJES.text("Lista order by llena");
                $.each(listaColumnas, function (key, value) {
                    LISTA_ORDERBY.append(
                        $('<option>', {
                            value: value,
                            text: value
                        }));
                });
            },
            error: function (errorThrown) {
                console.log("Hubo un error: " + errorThrown);
            }
        });
    }

    function llenaLimites(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                CONTAINER_MENSAJES.text("Llenando lista de límites");
            },
            success: function (data) {
                var count = parseInt(data);
                for (var i = 0; i < count; i++) {
                    LISTA_BEGIN.append(
                        $('<option>', {
                            value: i,
                            text: i
                        }));
                }
                for (i = 0; i <= count; i++) {
                    LISTA_END.append(
                        $('<option>', {
                            value: i,
                            text: i
                        }));
                }
                CONTAINER_MENSAJES.text("Lista de límites llena");
            },
            error: function (errorThrown) {
                console.log("Hubo un error: " + errorThrown);
            }
        });
    }

    function consulta(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                CONTAINER_MENSAJES.text("Buscando...");
            },
            success: function (data) {
                CONTAINER_MAESTRO.html(data);
                CONTAINER_MENSAJES.text("Consulta realizada con éxito");
            }, error: function (errorThrown) {
                CONTAINER_MENSAJES.text("Error:\n" + errorThrown);
            }
        });
    }
    function modalProducto(idProd, nombreProd, descripcionProd, existenciaProd, precioProd){
        $(document).find("#modal-edit-producto").modal("show");
        var $idProd = $(document).find("#modal-id-producto"),
            $nombreProd = $(document).find("#modal-nombre-producto"),
            $descripcionProd = $(document).find("#modal-descripcion-producto"),
            $existenciaProd = $(document).find("#modal-existencia-producto"),
            $precioProd = $(document).find("#modal-precio-producto");
        $idProd.val(idProd);
        $nombreProd.val(nombreProd);
        $descripcionProd.val(descripcionProd);
        $existenciaProd.val(existenciaProd);
        $precioProd.val(precioProd);

    }
    function modalUsuario(idUsr, nombre, segNombre, apPat, apMat, ventasHechas){
        $(document).find("#modal-edit-usuario").modal("show");
        var $idUsr = $(document).find("#modal-id-usuario"),
            $nombre = $(document).find("#modal-nombre-usuario"),
            $segNombre = $(document).find("#modal-segNombre-usuario"),
            $apPat = $(document).find("#modal-apPat-usuario"),
            $apMat = $(document).find("#modal-apMat-usuario"),
            $ventasHechas = $(document).find("#modal-ventasHechas-usuario");
        $idUsr.val(idUsr);
        $nombre.val(nombre);
        $segNombre.val(segNombre);
        $apPat.val(apPat);
        $apMat.val(apMat);
        $ventasHechas.val(ventasHechas);

    }
    $(document).on("click", "button.btn-editar-datos", function () {
        var id = $(this).attr("id");
        var datos = [];
        switch (tabla) {
            case "productos":
                datos = {
                    which: "dameProd",
                    producto: id
                };
                dameProducto(datos);
                break;
            case "usuarios":
                datos = {
                    which: "dameUser",
                    idUsuario: id
                };
                dameUsuario(datos);
                break;
        }
    });
    $(document).find("#modal-edit-producto").find("#modal-actualizar-producto").click(function () {
        console.log("Presionado guardar datos");
        var idProd = $(document).find("#modal-id-producto").val(),
            nombreProd = $(document).find("#modal-nombre-producto").val(),
            descripcionProd = $(document).find("#modal-descripcion-producto").val(),
            existenciaProd = $(document).find("#modal-existencia-producto").val(),
            precioProd = $(document).find("#modal-precio-producto").val();
        if (idProd && nombreProd && descripcionProd && existenciaProd && precioProd) {
            arrayProductos.push(idProd,nombreProd,descripcionProd,existenciaProd,precioProd);
            datos = {
                which: "actualizaProd",
                datosProd: arrayProductos
            };
            actualizaProducto(datos);
            llenaTodo();
        }
    });
    $(document).on("click", "button.btn-eliminar-datos", function () {
    });
    function dameProducto(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            success: function (data) {
                var datosProd = eval(data);
                var idProd = datosProd[0],
                    nombre = datosProd[1],
                    descripcion = datosProd[2],
                    existencia = datosProd[3],
                    precio = datosProd[4];
                modalProducto(idProd, nombre, descripcion, existencia, precio);
            },
            error: function (errorThrown) {
                console.log("Hubo un error:\n" + errorThrown);
            }
        });
    }
    function dameUsuario(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            success: function (data) {
                var datosUsuario = eval(data);
                var idUsr = datosUsuario[0],
                    nombre = datosUsuario[1],
                    segNombre = datosUsuario[2],
                    apPat = datosUsuario[3],
                    apMat = datosUsuario[4],
                    ventasHechas = datosUsuario[5];
                modalUsuario(idUsr, nombre, segNombre, apPat, apMat, ventasHechas);
            },
            error: function (errorThrown) {
                console.log("Hubo un error:\n" + errorThrown);
            }
        });
    }

    function actualizaProducto(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                console.log("Actualizando producto...");
            },
            success: function (data) {
                switch (parseInt(data)) {
                    case 0:
                        $(document).find("#modal-edit-producto").modal("hide");
                        datos = {
                        which: "consulta",
                        table: tabla,
                        begin: 0,
                        end: 10,
                        orderby: LISTA_ORDERBY.val(),
                        order: LISTA_ORDER.val()
                    };
                        consulta(datos);
                        console.log("Producto actualizado correctamente");
                        break;
                    case 1:
                        console.log("Error al actualizar el producto");
                        break;
                    case 2:
                        console.log("El producto que intentas editar no existe.");
                        break;
                }
            },
            error: function (errorThrown) {
                console.log("Error con la conexión. El mensaje es: " + errorThrown);
            }
        });
    }

});