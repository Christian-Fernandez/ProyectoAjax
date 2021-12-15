<?php
    require_once "bd.php";

    require "sesiones_json.php";

    if(!comprobar_sesion()) return;
    $productos_array = [];
    $productos = cargar_productos_categoria($_GET["categorias"]);
    $cat_json = json_encode(iterator_to_array($productos));
    echo $cat_json;