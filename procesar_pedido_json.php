<?php

require_once "bd.php";

require "sesiones_json.php";

if(!comprobar_sesion()) return;
$resul = insertar_pedido($_SESSION["carrito"],$_SESSION["usuario"]["codRes"]);
if ($resul === FALSE){
    echo "FALSE";
}else{
    $correo = $_SESSION["usuario"]["correo"];
    $conf = enviar_correos($_SESSION["carrito"],$resul,$correo);
    echo "TRUE";

    $_SESSION["carrito"] = [];
}