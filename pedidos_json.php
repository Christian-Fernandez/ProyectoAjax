<?php

require_once "bd.php";
require "sesiones_json.php";
if (!comprobar_sesion()) {
    return;
}

echo json_encode([
    "pedidos" => cargarPedidos($_SESSION["usuarios"]),
]);

?>