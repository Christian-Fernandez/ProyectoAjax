<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="js/cargarDatos.js"></script>
    <script type="text/javascript" src="js/sesion.js"></script>
    <title>Document</title>
</head>
    <body>
        <section id="login">
            <form onsubmit="return login()" method="post">
                Usuario <input id="usuario" type="text">
                Clave <input id="clave"  type="password">
                <input type="submit">
            </form>
        </section>

        <section id="principal" style="display:none">
            <header>
                <?php require 'cabezera_json.php' ?>
            </header>
            <h2 id="titulo"></h2>
            <section id="contenido">

            </section>
        </section>
    </body>
</html>
