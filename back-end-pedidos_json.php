<?php

require_once 'sesiones_json.php';
require_once 'bd.php';
if (comprobar_sesion_admin() === FALSE) {
    header("Location: una_sola_pagina_1.php");
}

function comprobarDatos()
{
    if (isset($_POST["CodPed"]) && isset($_POST["Fecha"]) && isset($_POST["Enviado"]) && isset($_POST["Restaurante"])){
        return true;
    } else {
        return false;
    }
}

function comprobarNuevosDatos()
{
    if (isset($_POST["nuevaFecha"]) && isset($_POST["nuevoEnviado"]) && isset($_POST["nuevoRestaurante"])) {
        return true;
    } else {
        return false;
    }
}


if (!isset($_GET["pedido"])) {

    if (isset($_GET["nuevoPedido"])) {
        echo json_encode([
            "usuarios" => iterator_to_array(cargar_usuarios()),
        ]);
    } else if (comprobarNuevosDatos()) {
        insertarPedido($_POST["nuevaFecha"], $_POST["nuevoEnviado"], $_POST["nuevoRestaurante"]);
    } else if (isset($_GET["id"]) || isset($_GET["borrar"])) {
        eliminar_pedido($_GET["id"]);
    } else if (comprobarDatos()) {
        actualizar_pedidos($_POST["CodPed"], $_POST["Fecha"], $_POST["Enviado"], $_POST["Restaurante"]);
    } else {

        echo json_encode([
            "pedido" => iterator_to_array(cargar_Pedido($_GET["editar"])),
            "usuarios" => iterator_to_array(cargar_usuarios()),
        ]);

    }
} else {

    $pedidos = cargarPedido();
    $ped_json = json_encode(iterator_to_array($pedidos), true);
    echo $ped_json;

}




