<?php
require_once 'sesiones_json.php';

    $usuario=$_SESSION["correo"];
    if(comprobar_sesion_admin()){
        echo "<span id='cab_usuario'>Usuario: $usuario</span><nav><ul><li><a href='#' onclick='cargarCategorias();'>Home</a></li><li><a href='#' onclick='cargarCarrito();'>Carrito</a></li><li><a href='#' onclick='cargarPedidos();'>Pedidos</a></li><li><a href='#' class='back-end' onclick='cargarNavAdmin();'>Administración</a></li><li><a href='#' onclick='cerrarSesionUnaPagina();'>Cerrar sesión</a></li></ul></nav>";
    }else{
        echo "<span id='cab_usuario'>$usuario</span><nav><ul><li><a href='#' onclick='cargarCategorias();'>Home</a></li><li><a href='#' onclick='cargarCarrito();'>Carrito</a></li><li><a href='#' onclick='cargarPedidos();'>Pedidos</a></li><li><a href='#' onclick='cerrarSesionUnaPagina();'>Cerrar sesión</a></li></ul></nav>";
    }
