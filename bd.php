<?php

define("CADENA_CONEXION", 'mysql:dbname=pedidos;host=127.0.0.1');
define("USUARIO_CONEXION", 'ciclost');
define("CLAVE_CONEXION", 'toor');

function comprobar_usuario($nombre, $clave){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "SELECT CodRes, Correo FROM restaurantes WHERE Correo='$nombre' and Clave = '$clave'";
        $resul = $bd->query($ins);
        if($resul ->rowCount() === 1){
            return TRUE;
        }
        else{
            //echo "<p><h2>Usuario no registrado</h2></p>";
            return FALSE;
        }
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}

function comprobarUsuarios($nombre, $clave){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "SELECT CodRes FROM restaurantes WHERE Correo='$nombre' and Clave = '$clave'";
        $resul = $bd->query($ins);
        if($resul ->rowCount() === 1){
            return $resul->fetch()["CodRes"];
        }
        else{
            //echo "<p><h2>Usuario no registrado</h2></p>";
            return FALSE;
        }
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}

function cargar_categorias(){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "SELECT CodCat, Nombre,Descripcion FROM categoria";
        $resul = $bd->query($ins);
        if(!$resul){
            return FALSE;            
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}

function cargar_categoria($codCat){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "SELECT Nombre, Descripcion,Codcat FROM categoria WHERE CodCat=$codCat";
        $resul = $bd->query($ins);
        
        if (!$resul) {
            return FALSE;
        }
        
        if ($resul->rowCount() === 0) {
            return FALSE;
        }
        // si hay 1 o más
        return $resul;
    } catch (PDOException $ex) {
        echo 'Error con la base de datos: ' .$ex->getMessage();
    }
}

function cargar_productos_categoria($codCat){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "SELECT * FROM productos WHERE CodCat=$codCat and Stock>0";
        $resul = $bd->query($ins);
        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }
        return $resul;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}
function cargar_productos($codigosProductos){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $texto_in = implode(",", $codigosProductos);
        $ins = "SELECT * FROM productos WHERE CodProd in($texto_in)";
        $resul = $bd->query($ins);
        if(!$resul){
            return FALSE;
        }
        return $resul;
    } catch (PDOException $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}
function insertar_pedido($carrito, $codRes){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $bd->beginTransaction();
        $hora = date("Y-m-d H:i:s", time());
        $sql = "INSERT into pedidos(Fecha, Enviado, Restaurante) VALUES ('$hora', 0, $codRes)";
        $resul = $bd->query($sql);
        if(!$resul){
            return FALSE;
        }
        $pedido = $bd->lastInsertId();
        foreach($carrito as $codProd=>$unidades){
            $ins = "INSERT into pedidosproductos(CodPed, CodProd, Unidades) VALUES ($pedido, $codProd, $unidades)";
            $resul = $bd->query($ins);
            if(!$resul){
                $bd->rollBack();
                return FALSE;
            }
        }
        $bd->commit();
        return $pedido;
    } catch (Exception $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();

    }
}

function cargarPedidos($res){
    try{
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);
        $ins = "select pd.CodPed,pd.Fecha,pd.Enviado,p.Nombre,p.Descripcion,ped.Unidades
                from pedidos as pd JOIN pedidosproductos as ped on pd.CodPed = ped.CodPed
                JOIN productos as p on p.CodProd = ped.CodProd
                where pd.Restaurante = $res ORDER BY pd.Fecha DESC;";
        $resul = $bd->query($ins);
        if(!$resul){
            return FALSE;
        }
        return $resul->fetchAll();
    } catch (PDOException $ex) {
        echo "Error con la base de datos: " . $ex->getMessage();
    }
}

function restar_stock($codProd,$numProductos){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $bd->beginTransaction();

        $up = "UPDATE productos SET Stock= Stock-'$numProductos' WHERE codProd='$codProd'";

        $resul = $bd->query($up);

        if(!$resul){
            return FALSE;
        }

        $bd->commit();

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function insertar_categoria($nombre,$descripcion){

    try {
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);

        $ins = "INSERT into categoria(Nombre, Descripcion) VALUES ('$nombre', '$descripcion')";
        $resul = $bd->query($ins);

        if (!$resul) {
            return FALSE;
        }

        return $resul;

    } catch (PDOException $e) {
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function eliminar_categoria($CodCat){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "DELETE FROM categoria WHERE CodCat='$CodCat'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function actualizar_categoria($CodCat,$nombre,$descripcion){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "UPDATE categoria SET Nombre='$nombre', Descripcion='$descripcion' WHERE CodCat='$CodCat'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function eliminar_usuario($CodRes){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "DELETE FROM restaurantes WHERE CodRes='$CodRes'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function cargar_usuario($CodRes){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "SELECT CodRes,Correo,Clave,Pais,CP,Ciudad,Direccion  FROM restaurantes WHERE CodRes='$CodRes'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function insertar_usuario($correo,$clave,$pais,$cp,$ciudad,$direccion){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "INSERT into restaurantes(Correo, Clave, Pais, CP, Ciudad, Direccion) VALUES ('$correo', '$clave', '$pais', '$cp', '$ciudad', '$direccion')";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function actualizar_usuario($CodRes,$correo,$clave,$pais,$cp,$ciudad,$direccion){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "UPDATE restaurantes SET Correo='$correo',Clave='$clave',Pais='$pais',CP='$cp',Ciudad='$ciudad',Direccion='$direccion' WHERE CodRes='$CodRes'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function  cargar_usuarios(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT * FROM restaurantes WHERE Correo!='admin'";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function  cargarProductos(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodProd,Nombre,Descripcion,Peso,Stock,CodCat FROM productos";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function eliminar_producto($CodProd){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "DELETE FROM productos WHERE codProd='$CodProd'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function insertar_producto($nombre,$descripcion,$peso,$stock,$codCat)
{

    try {
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);

        $ins = "INSERT into productos(Nombre, Descripcion, Peso, Stock, CodCat) VALUES ('$nombre', '$descripcion', '$peso', '$stock', '$codCat')";
        $resul = $bd->query($ins);

        if (!$resul) {
            return FALSE;
        }

        return $resul;

    } catch (PDOException $e) {
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function actualizar_producto($CodProd,$nombre,$descripcion,$peso,$stock,$codCat){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "UPDATE productos SET Nombre='$nombre', Descripcion='$descripcion', Peso='$peso' , Stock='$stock' , CodCat='$codCat' WHERE codProd='$CodProd'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function  cargarProducto($codProd){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodProd,Nombre,Descripcion,Peso,Stock,CodCat FROM productos WHERE CodProd='$codProd'";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}



function ticket(){

    $tickets=cargarPedidos($_SESSION['usuarios']);
    $contador=0;
    $name="";
    $datos="";
    foreach($tickets as $ele){
        if($lastId!=$ele["CodPed"]){
            $contador++;
        }
        if($lastId!=$ele["CodPed"] && $contador<=1){
            $name=$ele["CodPed"];
            $enviado=$ele["Enviado"]==1?'Si':'No';

            $datos="Código de pedido: ".$ele["CodPed"]."\n";

            $datos.="Enviado: ".$enviado."\n";

            $datos.="Fecha: ".$ele["Fecha"]."\n";

            $datos.="Productos del pedido: \n";

            $datos.="=========================\n";

            $datos.="Nombre: ".$ele["Nombre"]."\n";

            $datos.="Descripción: ".$ele["Descripcion"]."\n";

            $datos.="Unidades: ".$ele["Unidades"]."\n";
        }
        if($lastId==$ele["CodPed"] && $contador<=1){

            $datos.="=========================\n";

            $datos.="Nombre: ".$ele["Nombre"]."\n";

            $datos.="Descripción: ".$ele["Descripcion"]."\n";

            $datos.="Unidades: ".$ele["Unidades"]."\n";
        }
        $lastId=$ele["CodPed"];
    }
    file_put_contents("pedidos/".$name.".txt",$datos);
}

function  cargarPedido(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT * FROM pedidos";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function actualizar_pedidos($CodPed,$Fecha,$Enviado,$Restaurante){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "UPDATE pedidos SET Fecha='$Fecha',Enviado='$Enviado',Restaurante='$Restaurante' WHERE CodPed='$CodPed'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function  cargar_Pedido($cod){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT * FROM pedidos where CodPed = '$cod'";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function insertarPedido($Fecha,$Enviado,$Restaurante)
{

    try {
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);

        $ins = "INSERT into pedidos(Fecha, Enviado, Restaurante) VALUES ('$Fecha', '$Enviado', '$Restaurante')";
        $resul = $bd->query($ins);

        if (!$resul) {
            return FALSE;
        }

        return $resul;

    } catch (PDOException $e) {
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function eliminar_pedido($CodPed){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "DELETE FROM pedidos WHERE CodPed='$CodPed'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function insertarPedidoProducto($codPed,$codProd,$unidades)
{

    try {
        $bd = new PDO(CADENA_CONEXION, USUARIO_CONEXION, CLAVE_CONEXION);

        $ins = "INSERT into pedidosproductos(CodPed, CodProd,Unidades) VALUES ('$codPed', '$codProd', '$unidades')";
        $resul = $bd->query($ins);

        if (!$resul) {
            return FALSE;
        }

        return $resul;

    } catch (PDOException $e) {
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function cargarPedidosProductos(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodPredProd,CodPed,CodProd,Unidades FROM pedidosproductos";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
}

function  cargarPedidoProducto($CodPredProd){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodPredProd,CodPed,CodProd,Unidades FROM pedidosproductos WHERE CodPredProd='$CodPredProd'";
        $resul = $bd->query($ins);

        if(!$resul){
            return FALSE;
        }
        if($resul->rowCount() === 0){
            return FALSE;
        }

        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function actualizar_pedidoProducto($CodPredProd,$CodPed,$CodProd,$Unidades){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "UPDATE pedidosproductos SET CodPed='$CodPed', CodProd='$CodProd', Unidades='$Unidades' WHERE CodPredProd='$CodPredProd'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }


}

function eliminar_pedidoProducto($CodPredProd){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "DELETE FROM pedidosproductos WHERE CodPredProd='$CodPredProd'";

        $resul = $bd->query($sel);

        if(!$resul){
            return FALSE;
        }

        return $resul;

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}


