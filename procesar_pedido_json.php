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
        //vaciar carrito	
        $_SESSION['carrito'] = [];
    }		
	