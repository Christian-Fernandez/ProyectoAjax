<?php
    require_once 'bd.php';
    /*comprueba que el usuario haya abierto sesión o devuelve*/
    require 'sesiones_json.php';
    if(!comprobar_sesion()) return;
    
    $resul = insertar_pedido($_SESSION['carrito'], $_SESSION['usuario']);
    if($resul === FALSE){
        echo "FALSE";			
    }else{
        echo "TRUE";
        $productos = cargar_productos(array_keys($_SESSION["carrito"]));
        foreach ($_SESSION["carrito"] as $key => $value) {
            restar_stock($key, $value);
        }
        $_SESSION['carrito'] = [];
    }		
	