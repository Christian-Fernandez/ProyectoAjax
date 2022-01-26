<?php

require_once 'sesiones_json.php';
require_once 'bd.php';
if (comprobar_sesion_admin() === FALSE) {
    header("Location: una_sola_pagina_1.php");
}

function comprobarDatos()
{
    if (isset($_POST["codProd"]) && isset($_POST["nombre"]) && isset($_POST["descripcion"]) && isset($_POST["peso"]) && isset($_POST["stock"]) && isset($_POST["CodCat"])) {
        return true;
    } else {
        return false;
    }
}

function comprobarNuevosDatos()
{
    if (isset($_POST["nuevoNombre"]) && isset($_POST["nuevaDescripcion"]) && isset($_POST["nuevoStock"]) && isset($_POST["nuevoPeso"]) && isset($_POST["nuevoCodCat"])) {
        return true;
    } else {
        return false;
    }
}


if (!isset($_GET["pedido"])) {

    if (isset($_GET["nuevoPedido"])) {
        $pedidos = cargarPedido();
        $ped_json = json_encode(iterator_to_array($pedidos), true);
        echo $ped_json;
    } else if (comprobarNuevosDatos()) {
        insertar_producto($_POST["nuevoNombre"], $_POST["nuevaDescripcion"], $_POST["nuevoPeso"], $_POST["nuevoStock"], $_POST["nuevoCodCat"]);
    } else if (isset($_GET["id"]) || isset($_GET["borrar"])) {
        eliminar_producto($_GET["id"]);
    } else if (comprobarDatos()) {
        actualizar_producto($_POST["codProd"], $_POST["nombre"], $_POST["descripcion"], $_POST["peso"], $_POST["stock"], $_POST["CodCat"]);
    } else {

        $pedidos = cargarPedido();
        $ped_json = json_encode(iterator_to_array($pedidos), true);
        echo $ped_json;
    }
} else {

    $pedidos = cargarPedido();
    $ped_json = json_encode(iterator_to_array($pedidos), true);
    echo $ped_json;

}




