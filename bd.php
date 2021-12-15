<?php

define("CADENA_CONEXION","mysql:dbname=pedidos;host=127.0.0.1");
define ("USUARIO_CONEXION","root");
define ("CLAVE_CONEXION","");

function comprobar_usuario($nombre,$clave){
    
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $ins = "SELECT CodRes, Correo FROM restaurantes WHERE Correo='$nombre' and Clave='$clave'";
        $resul = $bd->query($ins);
        
        if($resul->rowCount()===1){
            return TRUE;
            
        }else{
            return FALSE;
        }
        
    }catch (PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
    
}

function comprobar_admin($nombre,$clave){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT Clave, Correo FROM restaurantes WHERE Correo='$nombre' and Clave='$clave'";
        $resul = $bd->query($ins);

        if(!$resul){
            return $resul;
        }

        $resultado=$resul->fetch();

        if($resultado["Clave"]==1234 && $resultado["Correo"]=="admin"){
            return TRUE;
        };

        return FALSE;

    }catch (PDOException $e){
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

function  cargar_categorias(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $ins = "SELECT CodCat,Nombre FROM categoria";
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



function  cargar_categoria($codCat){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $ins = "SELECT Nombre,Descripcion FROM categoria WHERE CodCat=$codCat";
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


function  cargar_productos_categoria($codCat){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $ins = "SELECT * FROM productos WHERE CodCat=$codCat";
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

function  cargar_productos($codigosProductos){

    if(empty($codigosProductos)){
        return false;
    }

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $texto_in= implode(",",$codigosProductos);
        
        $ins = "SELECT * FROM productos WHERE codProd in($texto_in)";
        $resul = $bd->query($ins);
            
        if(!$resul){
            return FALSE;
        }
        
        return $resul;
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }
    
}

function insertar_pedido($carrito, $codRes){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        
        $bd->beginTransaction();
        $hora = date("Y-m-d H:i:s",time());
        
        $sql = "INSERT into pedidos(Fecha, Enviado, Restaurante) VALUES ('$hora',0,'$codRes')";
        
        $resul = $bd->query($sql);
            
        if(!$resul){
            return FALSE;
        }
        
        $pedido = $bd->lastInsertId();
        
        foreach ($carrito as $codProd=>$unidades){
            $ins = "INSERT into pedidosproductos(CodPed, CodProd, Unidades) VALUES ('$pedido', '$codProd', '$unidades')";
            $resul= $bd->query($ins);
            
            if(!$resul){
                $bd->rollBack();
                return FALSE;
            }
        } 
        
        $bd->commit();
        
        return $pedido;
        
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
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

function comprobar_producto($codProd){
    
    try{
       $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);
        
        $sel = "SELECT * FROM productos WHERE codProd='$codProd'";
        $resul = $bd->query($sel);
            
        if(!$resul){
            return FALSE;
        }
        
        return $resul;
        
    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }   
    
}

function get_stock($codProd){
    
    try{
       $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "SELECT Stock FROM productos WHERE codProd='$codProd'";
        $resul = $bd->query($sel);
            
        if(!$resul){
            return $resul;
        }

        $resultado=$resul->fetch();

        return $resultado["Stock"];

    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }   
    
}

function get_cat($codProd){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "SELECT CodCat FROM productos WHERE codProd='$codProd'";
        $resul = $bd->query($sel);

        if(!$resul){
            return $resul;
        }

       $resultado = $resul->fetch();
        return $resultado["CodCat"];


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

function insertar_categoria($nombre,$descripcion)
{

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

function  cargarCategorias(){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodCat,Nombre,Descripcion FROM categoria";
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

function  cargarCategoria($codCat){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT CodCat,Nombre,Descripcion FROM categoria WHERE CodCat='$codCat'";
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

        $ins = "SELECT codProd,Nombre,Descripcion,Peso,Stock,CodCat FROM productos";
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

function  cargarProducto($codProd){
    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $ins = "SELECT codProd,Nombre,Descripcion,Peso,Stock,CodCat FROM productos WHERE codProd='$codProd'";
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

function get_ultimoPedido(){

    try{
        $bd=new PDO(CADENA_CONEXION,USUARIO_CONEXION,CLAVE_CONEXION);

        $sel = "SELECT CodPredProd FROM pedidosproductos WHERE CodPredProd=(SELECT max(CodPredProd) FROM pedidosproductos)";
        $resul = $bd-> query($sel);

        if(!$resul){
            return  $resul;
        }

        $resultado = $resul->fetch();
        return $resultado["CodPredProd"];


    }catch(PDOException $e){
        echo "Error con la base de datos:" . $e->getMessage();
    }

}

function insertarPedido($codPed,$codProd,$unidades)
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

function  cargarPedidos(){
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

function  cargarPedido($CodPredProd){
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

function actualizar_pedido($CodPredProd,$CodPed,$CodProd,$Unidades){

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

function eliminar_pedido($CodPredProd){

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



