<?php

require_once 'sesiones_json.php';
require_once 'bd.php';
if (comprobar_sesion_admin() == FALSE) {
    header("Location: una_sola_pagina_1.php");
}


function comprobarDatos(){
    if (isset($_POST["Codcat"]) && isset($_POST["nombre"]) && isset($_POST["descripcion"])) {
        return true;
    } else {
        return false;
    }
}

function comprobarNuevosDatos(){
    if (isset($_POST["nuevoNombre"]) && isset($_POST["nuevaDescripcion"])) {
        return true;
    } else {
        return false;
    }
}


if(!isset($_GET["categoria"])) {

    if(isset($_GET["nuevaCategoria"])){
        echo "true";
    }else if(comprobarNuevosDatos()) {
        insertar_categoria($_POST["nuevoNombre"], $_POST["nuevaDescripcion"]);
    }else if(isset($_GET["id"]) || isset($_GET["borrar"])){
        eliminar_categoria($_GET["id"]);
    }else if(comprobarDatos()){
        actualizar_categoria($_POST["Codcat"],$_POST["nombre"], $_POST["descripcion"]);
    }else{
        $categoria = cargar_categoria($_GET["editar"]);
        $cat_json = json_encode(iterator_to_array($categoria), true);

        echo $cat_json;

    }
}else{

    $categorias = cargar_categorias();
    $cats_json = json_encode(iterator_to_array($categorias), true);
    echo $cats_json;

}

?>


