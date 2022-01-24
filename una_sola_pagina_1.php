<!DOCTYPE html>
<html>
    <head>
        <title>Formulario de login</title>
        <meta charset = "UTF-8">
        <script type = "text/javascript" src = "js/sesion.js"></script>
        <script type = "text/javascript" src = "js/cargarDatos.js"></script>
        <link rel="stylesheet" href="css/index.css">
    </head>	
    <body>

        <section id = "login">

            <form onsubmit="return login()" method = "POST">
                <h2>Login</h2>
               <input id = "usuario" type = "text">
                <input id = "clave" type = "password">
                <input type = "submit">
            </form>
        </section>
        <section id = "principal" style="display:none">
            <header id="header">

            </header>
            <div id="admin"></div>
            <div id="error"></div>
            <h2 id = "titulo"></h2>
            <section id = "contenido"></section>			
        </section>
    </body>
</html>
