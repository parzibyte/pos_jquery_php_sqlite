$(document).ready(function () {
    /*****************************************************
     * DECLARACIÓN DE VARIABLES
     ******************************************************/
    var datosProductos = [],
        arrayCodProductos = [],
        subtotal = 0,
        total = 0,
        contador = 0,
        TECLA_ENTER = 13,
        TECLA_F1 = 112,
        TECLA_F3 = 114,
        TECLA_F5 = 116,
        TECLA_MAS = 107,
        TECLA_MENOS = 109,
        TECLA_POR = 106,
        TECLA_ENTRE = 111,
        TECLA_ESC = 27,
        contadorIntentos = 0,
        LIMITE_INTENTOS = 10,
        cambio = 0,
        pagoCliente,
        conDescuento = false,
        URL_AJAX = "./inc/manager.php",
        OCUPADO = false;
    /*****************************************************
     * DECLARACIÓN DE SELECTORES
     ******************************************************/
    var $DIV_PRODUCTOS_VENTA = $(document).find("#productos-para-venta"),
        $INPUT_CODIGO_PROD = $(document).find("#cod-prod"),
        $DIV_COBRAR_VENTA = $(document).find("#cobrar-venta"),
        $MENSAJES_VENTA = $(document).find("#contenedor-mensajes-productos"),
        $CONTENEDOR_TOTAL_DINAMICO = $(document).find("#contenedor-total-venta"),
        $INPUT_PAGO_CLIENTE = $(document).find("#input-pago-cliente"),
        $MENSAJE_TOTAL_VENTA = $(document).find("#mostrar-total-venta"),
        $MENSAJE_IVA_VENTA = $(document).find("#mostrar-iva-venta"),
        $MENSAJE_CAMBIO = $(document).find("#mostrar-cambio-venta"),
        $MENSAJES_COBRAR_VENTA = $(document).find("#mostrar-texto-venta"),
        $TABLA_PRODUCTOS = $(document).find("#tabla-productos-venta"),
        $SPAN_REALIZA_VENTA = $(document).find("#span-realiza-venta"),
        $SPAN_CANCELA_VENTA = $(document).find("#span-cancela-venta"),
        $SPAN_REM_ULT_PROD = $(document).find("#span-remueve-ult-prod"),
        $CB_DESCUENTO = $(document).find("#cb-descuento"),
        $INPUT_DESCUENTO = $(document).find("#input-descuento");

    /*****************************************************
     * PREPARACIÓN DE LOS ELEMENTOS
     ******************************************************/
    $INPUT_CODIGO_PROD.focus();
    $DIV_COBRAR_VENTA.hide();
    $CB_DESCUENTO.bootstrapSwitch();
    $INPUT_DESCUENTO.hide();
    /*****************************************************
     * ESCUCHAMOS A LOS ELEMENTOS
     ******************************************************/
    $CB_DESCUENTO.on('switchChange.bootstrapSwitch', function(event, state) {
        if (state) {
            $INPUT_DESCUENTO.show("fast");
            $INPUT_DESCUENTO.tooltip("show");
            $INPUT_DESCUENTO.focus();
            conDescuento = true;
        }else{
            $INPUT_DESCUENTO.hide("fast");
            $INPUT_DESCUENTO.tooltip("hide");
            total = subtotal;
            $INPUT_PAGO_CLIENTE.focus();
            conDescuento = false;
        }
        actualizaTotal();
        $INPUT_DESCUENTO.val("");
        $INPUT_PAGO_CLIENTE.val("");
    });
    $SPAN_REALIZA_VENTA.click(function () {
        cobrarVenta();
    });
    $SPAN_CANCELA_VENTA.click(function () {
        cancelarVenta();
    });
    $SPAN_REM_ULT_PROD.click(function () {
        remueveUltimoProducto();
    });
    $INPUT_CODIGO_PROD.keydown(function (e) {
        $MENSAJES_VENTA.text("");
        switch (e.keyCode) {
            case TECLA_ENTER:
                var codigoProducto = $(this).val();
                if (codigoProducto) {
                    var datos = {
                        which: "dameProd",
                        producto: codigoProducto
                    };
                    dameProducto(datos);
                    $(this).val("");
                }
                break;
            case TECLA_MAS:
                cobrarVenta();
                e.preventDefault();
                break;
            case TECLA_ENTRE:
                cancelarVenta();
                e.preventDefault();
                break;
            case TECLA_MENOS:
                OCUPADO = false;
                if (OCUPADO) {
                    console.log("Ocupado");
                } else {
                    console.log("No ocupado");
                    OCUPADO = true;
                    remueveUltimoProducto();
                    OCUPADO = false;
                }
                e.preventDefault();
                break;
            default :
                break;
        }
    });

    $INPUT_PAGO_CLIENTE.keyup(function () {
        pagoCliente = $(this).val();
        if (pagoCliente) {
            if (pagoCliente >= total) {
                cambio = pagoCliente - total;
            }
        }
        actualizaMensaje();
    });
    $INPUT_PAGO_CLIENTE.keydown(function (e) {
        var pagoCliente = $(this).val();
        switch(e.keyCode){
            case TECLA_ENTER:
                if (pagoCliente && pagoCliente>=total) {
                    if (confirm("¿Realizar la venta?")) {
                        console.log("El subtotal antes es",total,"luego", subtotal);
                        var datos = {
                            which: "procesaVenta",
                            productos: arrayCodProductos,
                            total: total,
                            subtotal: subtotal,
                            conDescuento: conDescuento
                        };
                        console.log("El subtotal es",total,"luego", subtotal);
                        procesaVenta(datos);
                        $MENSAJES_VENTA.html("<p class='bg-success'>Venta realizada correctamente.</p>");
                    }
                }
                e.preventDefault();
                break;

            case TECLA_ENTRE:
                cancelarVenta();
                e.preventDefault();
                break;
            case TECLA_MENOS:
                $CB_DESCUENTO.bootstrapSwitch("state", true);
                if (pagoCliente && pagoCliente>=total) {
                }
                $INPUT_DESCUENTO.focus();
                e.preventDefault();
                break;
        }
    });
    $(document).on("click", "button.quitar-producto", function () {
        var idFila = $(this).attr("id");
        remueveProducto(idFila);
        $INPUT_CODIGO_PROD.focus();
    });
    $INPUT_DESCUENTO.keyup(function (e) {
        if (e.keyCode == TECLA_ESC) {
            $CB_DESCUENTO.bootstrapSwitch("state", false);
        } else {
            var porcentajeDesc = $(this).val();
            if (porcentajeDesc && !isNaN(porcentajeDesc) && porcentajeDesc > 0 && porcentajeDesc < 100) {
                $(this).tooltip("hide");
                var descuento = (subtotal * porcentajeDesc) / 100;
                total = subtotal - descuento;
                actualizaTotal();
                switch (e.keyCode) {
                    case TECLA_ENTER:
                        $INPUT_PAGO_CLIENTE.focus();
                        break;
                }
            } else {
                total = subtotal;
                actualizaTotal();
                $(this).tooltip("show");
            }
        }
    });
    /*****************************************************
     * FUNCIONES DE USO COMÚN
     ******************************************************/
    function remueveUltimoProducto() {
        if (arrayCodProductos.length > 0) {
            remueveProducto(arrayCodProductos.length - 1);
        }
    }

    function cobrarVenta() {
        if (arrayCodProductos.length > 0) {
            deshabilitaDiv();
            $DIV_COBRAR_VENTA.show();
            $INPUT_PAGO_CLIENTE.focus();
            total = subtotal;
            actualizaTotal();
        }
    }

    function refrescaTablaProductos() {
        console.log("Refrescando");
        subtotal = 0;
        arrayCodProductos.length = 0;
        remueveTodosProductos();
        for (var i = datosProductos.length - 1; i >= 0; i--) {
            var idFila = datosProductos[i].idFila,
                idProducto = datosProductos[i].idProd,
                nombre = datosProductos[i].nombre,
                descripcion = datosProductos[i].descripcion,
                precio = datosProductos[i].precio
                ;
            $TABLA_PRODUCTOS.append('<tr class = "producto-removible">' +
                '<td>' + idProducto + '</td>' +
                '<td>' + nombre + '</td>' +
                '<td>' + descripcion + '</td>' +
                '<td>' + precio + '</td>' +
                '<td><button class = "quitar-producto btn btn-danger" id = "' + idFila + '"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;</button></td>' +
                '</tr>');
            arrayCodProductos.push(idProducto);
            subtotal += precio;
        }
    }

    function dameProducto(datos) {
        $.ajax({
            url: URL_AJAX,
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                $MENSAJES_VENTA.html("<p class='bg-info'>Buscando producto...</p>");
            },
            success: function (data) {
                var datosProd = eval(data);
                if (!datosProd[0]) {
                    $MENSAJES_VENTA.html("<p class='bg-danger'>El producto no está en el inventario.</p>");
                } else {
                    var idProd = datosProd[0],
                        nombre = datosProd[1],
                        descripcion = datosProd[2],
                        existencia = datosProd[3],
                        precio = datosProd[4];
                    if (existencia >= 1) {
                        agregaProducto(idProd, nombre, descripcion, precio);
                        $MENSAJES_VENTA.html("");
                        actualizaTotal();
                    } else {
                        $MENSAJES_VENTA.html("<p class='bg-warning'>Ya no quedan productos con este código</p>");
                    }

                }
            },
            error: function (errorThrown) {
                console.log("Hubo un error: " + errorThrown);
            }
        });
    }

    function agregaProducto(idProd, nombre, descripcion, precio) {
        datosProductos.push({
            idFila: contador,
            idProd: idProd,
            nombre: nombre,
            descripcion: descripcion,
            precio: precio
        });
        contador++;
        refrescaTablaProductos();
    }

    function actualizaTotal() {
        $MENSAJE_TOTAL_VENTA.html("Subtotal:&nbsp;<b>$" + subtotal + "</b><br>Total:&nbsp;<b>$" + total + "</b>");
        $CONTENEDOR_TOTAL_DINAMICO.html("Subtotal:&nbsp;<b>$" + subtotal + "</b>");
    }
    function actualizaMensaje(){
        if (pagoCliente >= total) {
            $MENSAJE_CAMBIO.text("Cambio: $" + cambio);
            $MENSAJES_COBRAR_VENTA.html("<span class='bg-success'>Todo está listo. Cuando quiera, presione <kbd>Enter</kbd>.</span>");
        } else {
            $MENSAJES_COBRAR_VENTA.html("<p class='bg-warning'>¡Alto! El dinero es menor que el pago.</p>");
            $MENSAJE_CAMBIO.text("Cambio: $0.0");
        }
    }
    function remueveProducto(id) {
        var arrayTemp = jQuery.grep(datosProductos, function (a) {
            return a.idFila == id;
        });
        var precio = arrayTemp[0].precio;
        datosProductos = jQuery.grep(datosProductos, function (a) {
            return a.idFila != id;
        });
        subtotal -= precio;
        contador--;
        actualizaTotal();
        refrescaTablaProductos();
    }

    function cancelarVenta() {
        if (arrayCodProductos.length > 0) {
            if (confirm("¿En serio quieres cancelar la venta?\nPresiona Esc para cancelar y Enter para aceptar.")) {
                preparaVentaNueva();
            }
        }

    }

    function preparaVentaNueva() {
        habilitaDiv();
        $INPUT_CODIGO_PROD.val("");
        arrayCodProductos.length = 0;
        datosProductos.length = 0;
        subtotal = 0;
        total = 0;
        cambio = 0;
        pagoCliente = 0;
        $INPUT_PAGO_CLIENTE.val("");
        refrescaTablaProductos();
        remueveTodosProductos();
        $MENSAJE_TOTAL_VENTA.text("");
        $DIV_COBRAR_VENTA.hide();
        $INPUT_CODIGO_PROD.focus();
        actualizaTotal();
    }

    function deshabilitaDiv() {
        $DIV_PRODUCTOS_VENTA.attr("disabled", true);
        $INPUT_CODIGO_PROD.attr("disabled", true);
        $SPAN_CANCELA_VENTA.attr("disabled", true);
        $SPAN_REALIZA_VENTA.attr("disabled", true);
        $SPAN_REM_ULT_PROD.attr("disabled", true);
        $(".producto-removible").attr("disabled", true);
        $TABLA_PRODUCTOS.attr("disabled", true);
        $(".quitar-producto").attr("disabled", true);
    }

    function habilitaDiv() {
        $DIV_PRODUCTOS_VENTA.removeAttr("disabled");
        $INPUT_CODIGO_PROD.removeAttr("disabled");
        $SPAN_CANCELA_VENTA.removeAttr("disabled");
        $SPAN_REALIZA_VENTA.removeAttr("disabled");
        $SPAN_REM_ULT_PROD.removeAttr("disabled");
        $(".producto-removible").removeAttr("disabled");
        $TABLA_PRODUCTOS.removeAttr("disabled");
        $(".quitar-producto").removeAttr("disabled");
    }

    function remueveTodosProductos() {
        $("tr").remove(".producto-removible");
    }

    function procesaVenta(datos) {
        $.ajax({
            url: URL_AJAX,
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                console.log("Conectando con el servidor...");
            },
            success: function (data) {
                console.log(data);
                console.log("Conexión establecida. Intentando realizar el proceso...");
                switch (parseInt(data)) {
                    case 0:
                        console.log("¡Venta realizada correctamente!");
                        preparaVentaNueva();
                        break;
                    case 1:
                    default:
                        console.log("Error al procesar la venta");
                        contadorIntentos++;
                        if (contadorIntentos <= LIMITE_INTENTOS) {
                            console.log("Intentando de nuevo...", "\nIntento", contadorIntentos + "/" + LIMITE_INTENTOS);
                            procesaVenta(datos);
                        } else {
                            console.log("Error fatal al realizar la venta. Reporte el problema al administrador.");
                        }
                        break;
                }
            },
            error: function (errorThrown) {
                console.log("Error en la conexión:\n" + errorThrown.message);
                contadorIntentos++;
                if (contadorIntentos <= LIMITE_INTENTOS) {
                    console.log("Intentando de nuevo...", contadorIntentos + "/" + LIMITE_INTENTOS);
                    procesaVenta(datos);
                } else {
                    console.log("Error fatal al realizar la venta. Reporte el problema al administrador.");
                }
            }
        });
    }

});
