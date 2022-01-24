<?php
session_start();
function comprobar_sesion(){
	if(!isset($_SESSION['usuario'])){	
		return false;
	}else return true;		
}

function comprobar_sesion_admin(){
    if(!isset($_SESSION["usuario"]) || $_SESSION["correo"]!="admin"){
        return FALSE;
    }else{
        return true;
    }
}
