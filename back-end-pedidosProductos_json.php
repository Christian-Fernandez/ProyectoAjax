<?php

require_once 'sesiones.php';
require_once 'bd.php';
if (comprobar_sesion_admin() === FALSE) {
    header("Location: login.php?redirigido=true");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de categorias</title>
    <style>

        *{
            font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
            font-size: 13px;
        }

        h1{
            font-size:25px
        }

        h2{
            font-size: 19px;
        }

        body {
            margin: auto auto;
        }

        label{
            font-size: 15px;
        }

        header p {
            float: left;
            padding: 10px 0 0 15px;
            text-decoration: none;
            font-size: large;
            font-size: 20px;
            font-weight: bold;
        }

        header {
            height: 80px;
            border: black 1px solid;
        }

        header nav {
            float: right;
            margin: 0 0;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            padding-right: 20px;
        }

        nav li {
            display: inline-block;
            line-height: 80px;

        }

        header a {
            display: inline-block;
            color: black;
            border: 1px black solid;
            border-radius: 5px;
            text-decoration: none;
            padding: 10px 20px;
            line-height: normal;
            font-size: 20px;
            font-weight: bold;

        }

        .back-end {
            background: #4285F4;
            color: white;
        }

        .backEnd-pedidos{
            background: #4285F4;
            color: white;
        }

        header li a:hover {
            background: #5691f1;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            padding-right: 20px;
        }

        nav li {
            display: inline-block;
            line-height: 80px;

        }

        .administracion li a:hover {
            background: #5691f1;
        }

        nav a {
            display: inline-block;
            color: black;
            border: 1px black solid;
            border-radius: 5px;
            text-decoration: none;
            padding: 10px 20px;
            line-height: normal;
            font-size: 20px;
            font-weight: bold;
            margin: 0 auto;

        }

        form {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 450px;
            border: 1px solid #E1E1E1;
            border-radius: 10px;
            padding: 15px;
            transform: translate(-50%, -50%);
            background-color: white;
            text-align: center;

        }


        input[type="password"], input[type="text"], input[type="email"] {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;

        }

        input[type="password"], input[type="text"], input[type="email"],input[type="number"],select {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;

        }

        textarea,select{

            border: black 2px solid;
            border-radius: 5px;
            resize: none;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            outline: none;
            background: #4285F4;
            width: 50%;
            border: 0;
            border-radius: 5px;
            padding: 12px 20px;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
            margin-left: 2px;
            text-decoration: none;



        }

        form a {
            outline: none;
            background: #f63333;
            width: 42%;
            border: 0;
            border-radius: 5px;
            padding: 12px 20px;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 20px;
            margin-top: 20px;
            margin-right: 2px;
            text-decoration: none;


        }

        form div {
            display: flex;
        }

        input[type="submit"]:hover {
            background: #5691f1;

        }

        form a:hover {
            background: #ef4444;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        label {
            display: block;
            padding-bottom 0.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
            font-size: 12px;
            margin: 45px;
            width:70%;
            border-collapse: collapse;
            text-align: center;
            margin: 0 auto;
            margin-bottom: 50px;

        }

        th {
            font-size: 13px;
            font-weight: normal;
            padding: 15px;
            background: #b9c9fe;
            border-top: 4px solid #aabcfe;
            border-bottom: 1px solid #fff;
            color: black;
            border-collapse: collapse

        }

        td {
            padding: 18px;
            background: #e8edff;
            border-bottom: 1px solid #fff;
            color: black;
            border-collapse: collapse

        }

        h1 {
            text-align: center;
        }

        table a {
            text-decoration: none;
            color: blue;
        }


        tr:hover td {
            background: #d0dafd;
            color: #339;
        }
    </style>
</head>
<body>

<?php

require_once 'cabecera.php';
require_once 'navAdmin.php';

function comprobarDatos(){
    if (isset($_POST["codPred"]) && isset($_POST["codPedido"]) && isset($_POST["codProd"]) && isset($_POST["unidades"])) {
        return true;
    } else {
        return false;
    }
}


if(isset($_GET["borrar"]) && isset($_GET["id"]) && $_GET["borrar"]=="true") {
    eliminar_pedido($_GET["id"]);
}

if(comprobarNuevosDatos()){
   insertarPedido($_POST["nuevoCodPed"], $_POST["nuevoCodProd"],$_POST["nuevaUnidades"]);
}

if (comprobarDatos()) {
    actualizar_pedido($_POST["codPred"],$_POST["codPedido"], $_POST["codProd"],$_POST["unidades"]);
}

if(!isset($_GET["nuevoPedido"])) {

    if (!isset($_GET["pedido"])) {
        echo "<h1>Lista de Pedidos</h1>";

        $pedidos = cargarPedidos();
        if ($pedidos === false) {
            echo "<p class='error'>Error al conectar con la base de datos</p>";
        } else {
            echo "<table>";
            echo "<tr><th>CodPredProd</th><th>CodPed</th><th>CodProd</th><th>Unidades</th><th> </th><th> </th></tr>";
            foreach ($pedidos as $ped) {
                echo "<tr><td>" . $ped["CodPredProd"] . "</td><td>" . $ped["CodPed"] . "</td><td>" . $ped["CodProd"] . "<td>" . $ped["Unidades"] . "</td><td><a href='back-end-pedidos.php?pedido=${ped["CodPredProd"]}'>Editar</a></td><td><a href='back-end-pedidos.php?id=${ped["CodPredProd"]}&borrar=true'>Eliminar</a></td></tr>";
            }

            echo "<tr><td colspan='6'><a href='back-end-pedidosProductos_json.php?nuevoPedido=true'>Crear Nuevo Pedido</a></td></tr></table>";
        }
    } else {
        $pedido = cargarPedido($_GET["pedido"]);

        if ($pedido) {
            foreach ($pedido as $ped) {
                echo "CodPredProd\"]}'>
                           <div> <a href=>Cancelar</a> <input type='submit' value='Actualizar'></div>              
                        </form>";
            }
        }
    }
}else{

    echo "<h1>Crear Nuevo Pedido</h1><form action = 'back-end-pedidosProductos_json.php'  method = 'POST'>
                            <label for='nuevoCodPed'>Código Pedido:</label>     
                            <input type='number' id='nuevoCodPed' name='nuevoCodPed'required>
                            <label for='nuevoCodProd'>Código Producto:</label>
                            <input type='number' name='nuevoCodProd' id='nuevoCodProd' required>
                             <label for='nuevaUnidades'>Unidades:</label>
                            <input type='number' name='nuevaUnidades' id='nuevaUnidades' required>
                           <div> <a href='back-end-pedidosProductos_json.php'>Cancelar</a> <input type='submit' value='Crear'></div>              
                        </form>";

}


?>

</body>
</html>

