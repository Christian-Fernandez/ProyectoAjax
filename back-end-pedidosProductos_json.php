<?php

require_once 'sesiones_json.php';
require_once 'bd.php';
if (comprobar_sesion_admin() === FALSE) {
    header("Location: login.php?redirigido=true");
}


function comprobarDatos(){
    if (isset($_POST["codPred"]) && isset($_POST["codPedido"]) && isset($_POST["codProd"]) && isset($_POST["unidades"])) {
        return true;
    } else {
        return false;
    }
}


function comprobarNuevosDatos(){
    if (isset($_POST["nuevoCodPed"]) && isset($_POST["nuevoCodProd"]) && isset($_POST["nuevaUnidades"])) {
        return true;
    } else {
        return false;
    }
}


if (!isset($_GET["pedidoProducto"])) {

    if (isset($_GET["nuevoPedidoProducto"])) {
        $pedidos = cargarPedidosProductos();
        $ped_json = json_encode(iterator_to_array($pedidos), true);
        echo $ped_json;
    } else if (comprobarNuevosDatos()) {
        insertarPedidoProducto($_POST["nuevoCodPed"], $_POST["nuevoCodProd"],$_POST["nuevaUnidades"]);
    } else if (isset($_GET["id"]) || isset($_GET["borrar"])) {
        eliminar_pedidoProducto($_GET["id"]);
    } else if (comprobarDatos()) {
        actualizar_pedidoProducto($_POST["codPred"],$_POST["codPedido"], $_POST["codProd"],$_POST["unidades"]);
    } else {

        $pedido = cargarPedidoProducto($_GET["editar"]);
        $ped_json = json_encode(iterator_to_array($pedido), true);
        echo $ped_json;

    }
} else {

    $pedidos = cargarPedidosProductos();
    $ped_json = json_encode(iterator_to_array($pedidos), true);
    echo $ped_json;

}


?>

