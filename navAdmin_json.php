<?php

require_once 'sesiones_json.php';


if (comprobar_sesion_admin()) {
    echo '<ul class="ul"><li><a href="#" onclick="cargarUsuariosBack()">Usuarios</a></li><li><a href="#" onclick="cargarCategoriasBack()">Categorias</a></li><li><a href="#" onclick="cargarProductosBack()">Productos</a></li><li><a href="#" onclick="cargarPedidosBack()" >Pedidos</a></li><li><a href="#" onclick="cargarPedidosProductosBack()" >PedidosProductos</a></li></ul>';
}