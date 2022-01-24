<?php

require_once 'sesiones_json.php';
require_once 'bd.php';
if (comprobar_sesion_admin() == FALSE) {
    header("Location: una_sola_pagina_1.php");
}

function comprobarDatos(){
    if (isset($_POST["correo"]) && isset($_POST["clave"]) && isset($_POST["pais"]) && isset($_POST["cp"]) && isset($_POST["ciudad"]) && isset($_POST["direccion"]) && isset($_POST["cod"])) {
        return true;
    } else {
        return false;
    }
}

function comprobarNuevosDatos(){
    if (isset($_POST["nuevoCorreo"]) && isset($_POST["nuevaClave"]) && isset($_POST["nuevoPais"]) && isset($_POST["nuevoCp"]) && isset($_POST["nuevaCiudad"]) && isset($_POST["nuevaDireccion"])) {
        return true;
    } else {
        return false;
    }
}



if(!isset($_GET["usuario"])) {

    if(isset($_GET["nuevoUsuario"])){
        echo "true";
    }else if(comprobarNuevosDatos()) {
        insertar_usuario($_POST["nuevoCorreo"], $_POST["nuevaClave"], $_POST["nuevoPais"], $_POST["nuevoCp"], $_POST["nuevaCiudad"], $_POST["nuevaDireccion"]);
    }else if(isset($_GET["id"]) || isset($_GET["borrar"])){
        eliminar_usuario($_GET["id"]);
    }else if(comprobarDatos()){
        actualizar_usuario($_POST["cod"],$_POST["correo"], $_POST["clave"], $_POST["pais"], $_POST["cp"], $_POST["ciudad"], $_POST["direccion"]);
    }else{
        $usuario = cargar_usuario($_GET["editar"]);
        $usu_json = json_encode(iterator_to_array($usuario), true);
        echo $usu_json;

    }
}else{

    $usuarios = cargar_usuarios();
    $usus_json = json_encode(iterator_to_array($usuarios), true);
    echo $usus_json;

}

?>

