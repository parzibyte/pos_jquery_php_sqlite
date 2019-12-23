/**
 * Created by luis on 12/10/15.
 */

$(document).ready(function () {

    /*
     * ******************************************************************************************
     * Definición de todos los elementos y variables.
     * NOTA: Los que comienzan con $ es porque son selectores.
     * ******************************************************************************************
     * */
    var $DIV_MUESTRA_PRODUCTOS = $("#muestra-productos"),
        $DIV_NUEVO_PRODUCTO = $("#nuevo-producto"),
        $DIV_AUMENTA_PRODUCTO = $("#aumenta-producto"),
        $CONT_MSJS_INS_PROD = $("#mensajes-inserta-productos"),
        $CONT_MSJS_AUM_PROD = $("#mensajes-aumenta-productos"),
        $LISTA_OPCIONES = $("#selecciona-accion"),
        arrayProductos = [],// Arreglo para mandar los datos del producto a insertar
        estaInsertando = true; //Para saber si está insertando o editando, ya que el botón es el mismo para ambos casos, pero esta variable ayuda.
    $LISTA_OPCIONES.val(0); //Se elige el primer valor de la lista
    cambiaOpciones(); //Para que refresque la tabla
    $DIV_AUMENTA_PRODUCTO.hide(); //Ocultamos ambos divs
    $DIV_NUEVO_PRODUCTO.hide();

    /*
     * **********************************************************************************************
     * Escuchamos a los elementos
     * **********************************************************************************************
     * */
    $LISTA_OPCIONES.change(function () {
        limpiaContenedoresMensajes();
        preparaNuevaOperacion();
        cambiaOpciones();
    });
    $DIV_NUEVO_PRODUCTO.find("#registrar-producto").click(function () {
        var idProducto = $DIV_NUEVO_PRODUCTO.find("#id-producto").val(),
            nombreProducto = $DIV_NUEVO_PRODUCTO.find("#nombre-producto").val(),
            descripcionProducto = $DIV_NUEVO_PRODUCTO.find("#descripcion-producto").val(),
            cantidadProducto = $DIV_NUEVO_PRODUCTO.find("#cantidad-producto").val(),
            precioProducto = $DIV_NUEVO_PRODUCTO.find("#precio-producto").val()
            ;
        if (idProducto && nombreProducto && descripcionProducto && cantidadProducto && precioProducto) {
            arrayProductos.push(idProducto, nombreProducto, descripcionProducto, cantidadProducto, precioProducto);
            $(this).hide("slow");
            var datos;
            if (estaInsertando) {
                datos = {
                    which: "insertaProd",
                    datosProd: arrayProductos
                };
                insertaProducto(datos);
            } else {
                datos = {
                    which: "actualizaProd",
                    datosProd: arrayProductos
                };
                actualizaProducto(datos);
            }
            $DIV_NUEVO_PRODUCTO.find(".tooltip-ins-prod").text("");
            $DIV_NUEVO_PRODUCTO.find("#id-producto").focus();
        } else {
            if (!idProducto) {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-id-producto").text("Rellena este campo.");
            } else {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-id-producto").text("");
            }
            if (!nombreProducto) {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-nombre-producto").text("Rellena este campo.");
            } else {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-nombre-producto").text("");
            }
            if (!descripcionProducto) {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-descripcion-producto").text("Rellena este campo.");
            } else {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-descripcion-producto").text("");
            }
            if (!cantidadProducto) {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-cantidad-producto").text("Rellena este campo.");
            } else {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-cantidad-producto").text("");
            }
            if (!precioProducto) {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-precio-producto").text("Rellena este campo.");
            } else {
                $DIV_NUEVO_PRODUCTO.find("#tooltip-precio-producto").text("");
            }
        }

    });
    $DIV_AUMENTA_PRODUCTO.find("#btn-aumentar-cantidad").click(function () {
        var codProd = $DIV_AUMENTA_PRODUCTO.find("#id-producto").val(),
            cantidadProd = $DIV_AUMENTA_PRODUCTO.find("#cantidad-producto").val();
        if (codProd && cantidadProd) {
            arrayProductos.push(codProd, cantidadProd);
            var datos = {
                which: "aumentaProd",
                datosProd: arrayProductos
            };
            aumentaProducto(datos);
            $DIV_AUMENTA_PRODUCTO.find(".tooltip-aum-prod").text("");
            $DIV_AUMENTA_PRODUCTO.find("#id-producto").focus();
        } else {
            if (!codProd) {
                $DIV_AUMENTA_PRODUCTO.find("#tooltip-codigo-producto").text("Rellena este campo");
            } else {
                $DIV_AUMENTA_PRODUCTO.find("#tooltip-codigo-producto").text("");
            }
            if (!cantidadProd) {
                $DIV_AUMENTA_PRODUCTO.find("#tooltip-cantidad-producto").text("Rellena este campo");
            } else {
                $DIV_AUMENTA_PRODUCTO.find("#tooltip-cantidad-producto").text("");

            }
        }
    });
    $(document).on("click", "button.btn-editar-datos", function () {
        limpiaContenedoresMensajes();
        var idProd = $(this).attr("id");
        var datos = {
            which: "dameProd",
            producto: idProd
        };
        dameProducto(datos);
    });
    $(document).on("click", "button.btn-eliminar-datos", function () {
        limpiaContenedoresMensajes();
        var id = $(this).attr("id");
        if (confirm("¿Eliminar producto con el id " + id + "?")) {
            datos = {
                which: "eliminaProd",
                idProd: id
            };
            eliminaProducto(datos)
        }
    });
    $(document).on("click", "button#btn-cancelar-edicion", function () {
        preparaNuevaOperacion();
    });


    /*
     * **********************************************************************************************
     * Funciones normales
     * **********************************************************************************************
     * */

    function insertaProducto(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                $CONT_MSJS_INS_PROD.text("Registrando producto...");
            },
            success: function (data) {
                switch (parseInt(data)) {
                    case 0:
                        $CONT_MSJS_INS_PROD.text("Producto agregado correctamente");
                        break;
                    case 1:
                        $CONT_MSJS_INS_PROD.text("Error al registrar el producto");
                        break;
                    case 2:
                        $CONT_MSJS_INS_PROD.text("El producto ya existe. Para aumentar la existencia selecciona otra opcion");
                        break;
                }
                $DIV_NUEVO_PRODUCTO.find("#registrar-producto").show("slow");
                preparaNuevaOperacion();
            },
            error: function (errorThrown) {
                $CONT_MSJS_INS_PROD.text("Error con la conexión. El mensaje es: " + errorThrown);
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
                $CONT_MSJS_INS_PROD.text("Actualizando producto...");
            },
            success: function (data) {
                switch (parseInt(data)) {
                    case 0:
                        $CONT_MSJS_INS_PROD.text("Producto actualizado correctamente");
                        break;
                    case 1:
                        $CONT_MSJS_INS_PROD.text("Error al actualizar el producto");
                        break;
                    case 2:
                        $CONT_MSJS_INS_PROD.text("El producto que intentas editar no existe.");
                        break;
                }
                $DIV_NUEVO_PRODUCTO.find("#registrar-producto").show("slow");
                preparaNuevaOperacion();
            },
            error: function (errorThrown) {
                $CONT_MSJS_INS_PROD.text("Error con la conexión. El mensaje es: " + errorThrown);
            }
        });
    }

    function eliminaProducto(datos) {
        $.ajax({
            url: "./inc/manager.php",
            data: {
                valores: datos
            },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                $CONT_MSJS_INS_PROD.text("Eliminando producto...");
            },
            success: function (data) {
                switch (parseInt(data)) {
                    case 0:
                        $CONT_MSJS_INS_PROD.text("Producto eliminado correctamente");
                        break;
                    case 1:
                        $CONT_MSJS_INS_PROD.text("Error al eliminar el producto");
                        break;
                    case 2:
                        $CONT_MSJS_INS_PROD.text("El producto que intentas eliminar no existe.");
                        break;
                }
                preparaNuevaOperacion();
            },
            error: function (errorThrown) {
                $CONT_MSJS_INS_PROD.text("Error con la conexión. El mensaje es: " + errorThrown);
            }
        });
    }

    function aumentaProducto(datos) {
        $.ajax({
                url: "./inc/manager.php",
                data: {
                    valores: datos
                },
                type: "POST",
                dataType: "html",
                beforeSend: function () {
                    $CONT_MSJS_AUM_PROD.text("Trabajando...");
                },
                success: function (data) {
                    switch (parseInt(data)) {
                        case 0:
                            $CONT_MSJS_AUM_PROD.text("Existencia aumentada correctamente.");
                            break;
                        case 1:
                            $CONT_MSJS_AUM_PROD.text("Error al aumentar la existencia del producto.");
                            break;
                        case 2:
                            $CONT_MSJS_AUM_PROD.text("El producto no existe.");
                            break;
                    }
                    preparaNuevaOperacion();
                },
                error: function (errorThrown) {
                    $CONT_MSJS_AUM_PROD.text("Error al realizar la conexión:\n" + errorThrown);
                }
            }
        );
    }

    function preparaNuevaOperacion() {
        arrayProductos.length = 0;
        estaInsertando = true;
        limpiaContenedoresMensajes();
        $DIV_NUEVO_PRODUCTO.find(".input-inserta-productos").val("");
        $DIV_AUMENTA_PRODUCTO.find(".input-aumenta-productos").val("");
        $DIV_NUEVO_PRODUCTO.find("#registrar-producto").html("<span class='glyphicon glyphicon-plus-sign' aria-hidden='true' hidden></span>Registrar");
        $DIV_NUEVO_PRODUCTO.find("#btn-cancelar-edicion").hide("slow");
        $DIV_NUEVO_PRODUCTO.find("#id-producto").prop("readonly", false);
        $DIV_NUEVO_PRODUCTO.find("#id-producto").focus();
        $DIV_NUEVO_PRODUCTO.find("#title-nuevo-prod").text("Registrar nuevo producto");
        datos = {
            which: "consulta",
            table: "productos",
            begin: 0,
            end: 10,
            orderby: "fecha",
            order: "DESC"
        };
        consulta(datos);
    }

    function limpiaContenedoresMensajes() {
        $CONT_MSJS_AUM_PROD.text("");
        $CONT_MSJS_INS_PROD.text("");
        $DIV_NUEVO_PRODUCTO.find(".tooltip-ins-prod").text("");
        $DIV_AUMENTA_PRODUCTO.find(".tooltip-aum-prod").text("");
    }

    function cambiaOpciones() {
        $DIV_NUEVO_PRODUCTO.find("#btn-cancelar-edicion").hide("fast");
        switch ($LISTA_OPCIONES.val()) {
            case "1":
                $DIV_NUEVO_PRODUCTO.show("slow");
                $DIV_AUMENTA_PRODUCTO.hide("slow");
                $DIV_NUEVO_PRODUCTO.find("#id-producto").focus();
                break;
            case "2":
                $DIV_NUEVO_PRODUCTO.hide("slow");
                $DIV_AUMENTA_PRODUCTO.show("slow");
                $DIV_AUMENTA_PRODUCTO.find("#id-producto").focus();
                break;
        }
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
                $DIV_MUESTRA_PRODUCTOS.html("<h3>Buscando...</h3>");
            },
            success: function (data) {
                $DIV_MUESTRA_PRODUCTOS.html(data);
            }, error: function (errorThrown) {
                $DIV_MUESTRA_PRODUCTOS.html("<h3>Error:" + errorThrown + "</h3>");
            }
        });
    }

    function llenaFormulario(id, nombre, descripcion, existencia, precio) {
        $DIV_AUMENTA_PRODUCTO.hide("slow");
        $DIV_NUEVO_PRODUCTO.show("slow");
        $DIV_NUEVO_PRODUCTO.find("#id-producto").val(id);
        $DIV_NUEVO_PRODUCTO.find("#id-producto").attr("readonly", true);
        $DIV_NUEVO_PRODUCTO.find("#nombre-producto").val(nombre);
        $DIV_NUEVO_PRODUCTO.find("#descripcion-producto").val(descripcion);
        $DIV_NUEVO_PRODUCTO.find("#cantidad-producto").val(existencia);
        $DIV_NUEVO_PRODUCTO.find("#precio-producto").val(precio);
        estaInsertando = false;
        $DIV_NUEVO_PRODUCTO.find("#registrar-producto").html("<span class='glyphicon glyphicon-download' aria-hidden='true'></span>Guardar cambios");
        $DIV_NUEVO_PRODUCTO.find("#btn-cancelar-edicion").show("slow");
        $DIV_NUEVO_PRODUCTO.find("#title-nuevo-prod").text("Editar datos de producto");

    }

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
                llenaFormulario(idProd, nombre, descripcion, existencia, precio);
            },
            error: function (errorThrown) {
                console.log("Hubo un error:\n" + errorThrown);
            }
        });
    }

});