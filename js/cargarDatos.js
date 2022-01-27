function cargarCategorias() {
    var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                var cats =  JSON.parse(xhttp.responseText);	
                var lista = document.createElement("ul");
                lista.classList.add("ul");
                document.getElementById("admin").innerHTML="";
                cargarMiniCarrito();
                for(var i = 0; i < cats.length; i++){
                    var elem = document.createElement("li");
                    //creamos los vínculos de cada categoría
                    var vinculo = document.createElement("a");
                    var ruta = "productos_json.php?categoria=" + cats[i].CodCat;
                    vinculo.href = ruta;
                    vinculo.innerHTML = cats[i].Nombre;
                    vinculo.onclick = function(){return cargarProductos(this);};
                    elem.appendChild(vinculo);
                    lista.appendChild(elem);
                }
                var contenido = document.getElementById("contenido");
                contenido.innerHTML = "";	
                var titulo = document.getElementById("titulo");
                titulo.innerHTML ="Categorías";
                contenido.appendChild(lista);
            }
        };
    xhttp.open("GET", "categorias_json.php", true);
    xhttp.send();
    return false;
}

function cargarProductos(destino){
    var xhttp = new XMLHttpRequest();	
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {			
            var prod = document.getElementById("contenido");
            var titulo = document.getElementById("titulo");
            titulo.innerHTML ="Productos";
            try{
                cargarMiniCarrito();
                document.getElementById("admin").innerHTML="";
                var filas =  JSON.parse(this.responseText);
                // creamos una tabla con los productos de la categoría seleccionada
                var tabla = crearTablaProductos(filas);				
                prod.innerHTML = "";
                prod.appendChild(tabla);												
            }catch(e){
                var mensaje = document.createElement("p");
                mensaje.innerHTML = "Categoría sin productos";
                prod.innerHTML = "";
                prod.appendChild(mensaje);
            }					
        }
    };	
    xhttp.open("GET", destino, true);
    xhttp.send();
    return false;
}
                    
function crearTablaProductos(productos){
    var tabla = document.createElement("table");
    var cabecera = crear_fila(["Código", "Nombre", "Descripción", "Stock", "Comprar"], "th");
    tabla.appendChild(cabecera);
    for(var i = 0; i < productos.length; i++){
        // creamos el formulario para añadir unidades del producto al carrito (mediante la función anadirProductos())
        formu = crearFormulario("Añadir", productos[i].CodProd, anadirProductos); 
        //creamos la fila en la tabla a mostrar con los productos
        fila = crear_fila([productos[i].CodProd, productos[i].Nombre, productos[i].Descripcion, productos[i].Stock], "td");
        celda_form = document.createElement("td");
        celda_form.appendChild(formu);
        fila.appendChild(celda_form);		
        tabla.appendChild(fila);		
    }	
    return tabla;		
}

function crearFormulario(texto, cod, funcion){
    var formu = document.createElement("form");		
    var unidades = document.createElement("input");
    unidades.value = 1;
    unidades.name = "unidades";
    unidades.type="number";
    var codigo = document.createElement("input");
    codigo.value = cod;
    codigo.type = "hidden";
    codigo.name = "cod";
    var bsubmit = document.createElement("input");
    bsubmit.type = "submit";
    bsubmit.value = texto;
    formu.onsubmit = function(){return funcion(this);};
    formu.appendChild(unidades);
    formu.appendChild(codigo);
    formu.appendChild(bsubmit);
    return formu;
}

function crear_fila(campos, tipo){
    var fila = document.createElement("tr");
    for(var i = 0; i < campos.length; i++){
        var celda = document.createElement(tipo);
        celda.classList.add(tipo);
        celda.innerHTML = campos[i];
        fila.appendChild(celda);
    }
    return fila;
}

function anadirProductos(formulario){
    var xhttp = new XMLHttpRequest();		
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            alert("Producto añadido con éxito");
            cargarMiniCarrito();
        }
    };
    var params = "cod=" + formulario.elements['cod'].value + "&unidades=" + formulario.elements['unidades'].value;
    xhttp.open("POST", "anadir_json.php", true);
    // el envío por POST requiere cabecera y cadena de parámetros
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);	
    return false;
}

function cargarCarrito(){
    var xhttp = new XMLHttpRequest();		
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
                var contenido = document.getElementById("contenido");
                contenido.innerHTML = "";

                var titulo = document.getElementById("titulo");
                titulo.innerHTML = "Carrito de la compra";
                try{

                    var filas =  JSON.parse(this.responseText);
                    //creamos la tabla de los productos añadidos al carrito
                    tabla = crearTablaCarrito(filas);				
                    contenido.appendChild(tabla);		
                    //añadimos el vínculo de "procesar pedido"
                    var procesar = document.createElement("a");
                    procesar.href ="#";
                    procesar.id ="procesar_pedido";
                    procesar.innerHTML= "Realizar pedido";
                    procesar.onclick = function() {
                        if (window.confirm("¿Deseas confirmar el pedido?")) {
                            procesarPedido();
                        }
                    }
                    contenido.appendChild(procesar);
                }catch(e){
                    document.getElementById("carri").innerHTML="";
                    var mensaje = document.createElement("p");
                    mensaje.innerHTML = "Todavía no tienes productos";
                    contenido.appendChild(mensaje);
                }			

        }else{
            document.getElementById("carri").innerHTML="";
            var contenido = document.getElementById("contenido");
            contenido.innerHTML = "";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML = "Carrito de la compra";
            var mensaje = document.createElement("p");
            mensaje.innerHTML = "Todavía no tienes productos";
            contenido.appendChild(mensaje);
        }
    };
    xhttp.open("GET", "carrito_json.php", true);
    xhttp.send();
    return false;
}

function crearTablaCarrito(productos){
    var tabla = document.createElement("table");
    var cabecera = 	crear_fila(["Código", "Nombre", "Descripción", "Unidades", "Eliminar"], "th");
    tabla.appendChild(cabecera);
    for(var i = 0; i < productos.length; i++){
        //creamos el formulario que se muestra en el carrito con la opción de eliminar prodcutos
        formu = crearFormulario("Eliminar", productos[i].CodProd, eliminarProductos);
        formu.classList.add("formu");
        //creamos la fila con los productos que contiene el carrito
        fila = crear_fila([productos[i].CodProd, productos[i].Nombre, productos[i].Descripcion,productos[i].unidades], "td");
        celda_form = document.createElement("td");
        celda_form.appendChild(formu);
        fila.appendChild(celda_form);		
        tabla.appendChild(fila);		
    }						
    return tabla;
}

function crearTablaMiniCarrito(productos){

    var tabla = document.createElement("table");
    tabla.classList.add("miniTabla");
    var cabecera = 	crear_fila(["Nombre","Unidades"], "th");
    tabla.appendChild(cabecera);
    var tr = document.createElement("tr");
    tr.innerHTML=`<td colspan="2"><a href='#' onclick='if(window.confirm("¿Deseas confirmar el pedido?"))procesarPedido();' class='procesarMini'>Realizar Pedido</a></td>`;
    for(var i = 0; i < productos.length; i++){

        //creamos la fila con los productos que contiene el carrito
        fila = crear_fila([productos[i].Nombre,productos[i].unidades], "td");
        tabla.appendChild(fila);
    }

    tabla.appendChild(tr);
    return tabla;
}

function eliminarProductos(formulario){
    var xhttp = new XMLHttpRequest();		
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {                                
            cargarCarrito();
            alert("Producto eliminado con éxito");
        }
    };
    var params = "cod=" + formulario.elements['cod'].value +  "&unidades=" + formulario.elements['unidades'].value;
    xhttp.open("POST", "eliminar_json.php", true);	
    // el envío por POST requiere cabecera y cadena de parámetros
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);	
    return false;
}

function procesarPedido(){
    var xhttp = new XMLHttpRequest();		
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var contenido = document.getElementById("contenido");
            contenido.innerHTML = "";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML ="Estado del pedido";
            if(this.responseText=="TRUE"){
                contenido.innerHTML = "<p>Pedido realizado</p>";

                cargarMiniCarrito();
            }else{
                contenido.innerHTML = "Error al procesar el pedido";
            }
        }
    };
    xhttp.open("GET", "procesar_pedido_json.php", true);
    xhttp.send();
    return false;
}


function cargarPedidos(){
    fetch("pedidos_json.php")
        .then(response => {
            if (response.ok)
                return response.json();
            else
                throw new Error(response.status);
        })
        .then(data => {
            cargarMiniCarrito();
            document.getElementById("admin").innerHTML="";
            titulo.innerHTML = "Pedidos";
            var contenido = document.getElementById("contenido");
           let tabla = "<table><tr class='primera'><th>Núm.Pedido</th><th>Fecha</th><th>Enviado</th><th>Descargar Ticket</th> </tr>";
           let enviado;
           let cod="null";
           let num = 0;
           for(let i=0;i<data.pedidos.length;i++){
               data.pedidos[i].Enviado == 0  ? enviado="No" : enviado="Si";

               if(num<5) {

                   if (data.pedidos[i].CodPed == cod) {
                       tabla += `<tr><td>${data.pedidos[i].Nombre}</td><td>${data.pedidos[i].Descripcion}</td><td>${data.pedidos[i].Unidades}</td><td></td></tr>`;
                   } else {
                       num++;
                       tabla += `<tr class="segunda"><th>${data.pedidos[i].CodPed}</th><th>${data.pedidos[i].Fecha}</th><th>${enviado}</th><th><a title=\"Descargar Archivo\" href=\"pedidos/${data.pedidos[i].CodPed}.txt\" download=\"Ticket_${data.pedidos[i].CodPed}.txt\"> Descargar Ticket </a></th></tr><tr class="thProductos"><th>Producto</th><th>Descripción</th><th>Cantidad</th><th></th> </tr>` +
                           `<tr><td>${data.pedidos[i].Nombre}</td><td>${data.pedidos[i].Descripcion}</td><td>${data.pedidos[i].Unidades}</td><td></td></tr>`;
                   }
                   cod = data.pedidos[i].CodPed;
               }
            }
           tabla += "</table>";
            contenido.innerHTML = tabla;
        })
        .catch(err => {
            console.error("ERROR: ", err.message)
        });
}

function cargarCabecera(){

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("header").innerHTML= this.responseText;
        }
    };
    xhttp.open("GET", "cabecera_json.php", true);
    xhttp.send();
    return false;

}


function cargarCategoriasBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            var pedidos = document.getElementById("carri");
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="ADMINISTRACIÓN"
            pedidos.innerHTML="";
            contenido.innerHTML="";
            let tabla = "<table><tr><th>CodCat</th><th>Nombre</th><th>Descripción</th><th></th><th></th></tr>";
            var cats =  JSON.parse(xhttp.responseText);
            contenido.innerHTML=""
            for(var i = 0; i < cats.length; i++){
                tabla += `<tr><td> ${cats[i].CodCat} </td><td> ${cats[i].Nombre} </td><td> ${cats[i].Descripcion} </td><td><a href='#' onclick="categoriasBack(${cats[i].CodCat})">Editar</a></td><td><a href='#' onclick="eliminarCategoriasBack(${cats[i].CodCat})">Eliminar</a></td></tr>`;
            }

            tabla += "<tr><td colspan='5'><a href='#' onclick='nuevasCategoriasBack()'>Crear Nueva Categoria</a></td></tr></table>";
            contenido.innerHTML=tabla;

        }
    };
    xhttp.open("GET", "back-end-categorias_json.php?categoria", true);
    xhttp.send();
    return false;
}

function eliminarCategoriasBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            cargarCategoriasBack();
        }
    };
    xhttp.open("GET", `back-end-categorias_json.php?id=${id}&borrar=true`, true);
    xhttp.send();
    return false;
}

function nuevasCategoriasBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Crear Nueva Categoria";
            contenido.innerHTML="";
            contenido.innerHTML = `<form onsubmit="return crearNuevasCategoriasBack()"   method = 'POST'><label for='nuevoNombre'>Nombre:</label><input type='text' id='nuevoNombre' name='nuevoNombre'  required><label for='nuevaDescripcion'>Descripción:</label><textarea name='nuevaDescripcion' id='nuevaDescripcion' cols='30' rows='5'  required></textarea><div> <a href='#' onclick='cargarCategoriasBack()'>Cancelar</a><input type='submit' value='Crear'></div></form>`;

        }
    };
    xhttp.open("GET", `back-end-categorias_json.php?nuevaCategoria=true`, true);
    xhttp.send();
    return false;
}

function crearNuevasCategoriasBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido añadir la categoria";
            }else{
                cargarCategoriasBack();
            }
        }
    };

    let nombre = document.getElementById("nuevoNombre").value;
    let descripcion= document.getElementById("nuevaDescripcion").value;
    var params = "nuevoNombre=" + nombre + "&nuevaDescripcion=" + descripcion;
    xhttp.open("POST", "back-end-categorias_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function categoriasBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            let categoria = JSON.parse(xhttp.responseText);
            console.log();
            document.getElementById("titulo").innerHTML="Editar Categoria";
            contenido.innerHTML="";
            contenido.innerHTML = `<form onsubmit="return editarCategoriasBack()" method = 'POST'><label for='nombre'>Nombre:</label><input type='text' id='nombre' name='nombre' value="${categoria[0].Nombre}"  required><label for='descripcion'>Descripción:</label><textarea name='descripcion' id='descripcion' cols='30' rows='5'  required>${categoria[0].Descripcion}</textarea><div> <a href='#' onclick='cargarCategoriasBack()'>Cancelar</a><input type='hidden' id="cod" value="${categoria[0].Codcat}"><input type='submit' value='Actualizar'></div></form>`;

        }
    };
    xhttp.open("GET", `back-end-categorias_json.php?editar=${id}`, true);
    xhttp.send();
    return false;
}

function editarCategoriasBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido actualizar la categoria";
            }else{
                cargarCategoriasBack();
            }
        }
    };

    let nombre = document.getElementById("nombre").value;
    let descripcion= document.getElementById("descripcion").value;
    let cod= document.getElementById("cod").value;
    var params = "nombre=" + nombre + "&descripcion=" + descripcion + "&Codcat=" + cod ;
    xhttp.open("POST", "back-end-categorias_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function cargarUsuariosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            contenido.innerHTML="";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="ADMINISTRACIÓN"
            document.getElementById("carri").innerHTML="";
            let tabla = "<table><tr><th>CodRes</th><th>Correo</th><th>Clave</th><th>Pais</th><th>CP</th> <th>Ciudad</th> <th>Dirección</th><th></th><th> </th></tr>";
            var usus =  JSON.parse(xhttp.responseText);
            contenido.innerHTML=""
            for(var i = 0; i < usus.length; i++){
                tabla += `<tr><td> ${usus[i].CodRes} </td><td> ${usus[i].Correo} </td><td> ${usus[i].Clave} </td><td> ${usus[i].Pais}</td><td> ${usus[i].CP}</td><td> ${usus[i].Ciudad}</td><td> ${usus[i].Direccion}</td><td><a href='#' onclick="usuariosBack(${usus[i].CodRes})">Editar</a></td><td><a href='#' onclick="eliminarUsuariosBack(${usus[i].CodRes})">Eliminar</a></td></tr>`;
            }

            tabla += "<tr><td colspan='9'><a href='#' onclick='nuevosUsuariosBack()'>Crear Nuevo Usuario</a></td></tr></table>";
            contenido.innerHTML=tabla;

        }
    };
    xhttp.open("GET", "back-end-usuarios_json.php?usuario=true", true);
    xhttp.send();
    return false;
}

function nuevosUsuariosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Crear Nuevo Usuario";
            contenido.innerHTML="";
            contenido.innerHTML = `<form onsubmit="return crearNuevosUsuariosBack()"  method = 'POST'><label for='nuevoCorreo'>Correo:</label> <input type='email' id='nuevoCorreo' name='nuevoCorreo' required> <label for='nuevaClave'>Contraseña:</label> <input type='password' id='nuevaClave' name='nuevaClave' required> <label for='nuevoPais'>Pais:</label> <input type='text' id='nuevoPais' name='nuevoPais' required> <label for='nuevoCp'>Código Postal:</label> <input type='text' id='nuevoCp' name='nuevoCp'> <label for='nuevaCiudad'>Ciudad:</label> <input type='text' id='nuevaCiudad' name='nuevaCiudad' required> <label for='nuevaDireccion'>Dirección:</label> <input type='text' id='nuevaDireccion' name='nuevaDireccion' required> <div> <a href='#' onclick="cargarUsuariosBack()">Cancelar</a><input type='submit' value='Crear'></div> </form>`;

        }
    };
    xhttp.open("GET", `back-end-usuarios_json.php?nuevoUsuario=true`, true);
    xhttp.send();
    return false;
}

function crearNuevosUsuariosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido añadir el usuario";
            }else{
                cargarUsuariosBack();
            }
        }
    };

    let nuevoCorreo = document.getElementById("nuevoCorreo").value;
    let nuevaClave= document.getElementById("nuevaClave").value;
    let nuevoPais = document.getElementById("nuevoPais").value;
    let nuevoCp= document.getElementById("nuevoCp").value;
    let nuevaCiudad= document.getElementById("nuevaCiudad").value;
    let nuevaDireccion= document.getElementById("nuevaDireccion").value;

    var params = "nuevoCorreo=" + nuevoCorreo + "&nuevaClave=" + nuevaClave + "&nuevoPais=" + nuevoPais + "&nuevoCp=" + nuevoCp + "&nuevaCiudad=" + nuevaCiudad  +  "&nuevaDireccion=" + nuevaDireccion;
    xhttp.open("POST", "back-end-usuarios_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function usuariosBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            let us = JSON.parse(xhttp.responseText);
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Editar Usuario";
            contenido.innerHTML="";
            contenido.innerHTML=`<form  onsubmit="return editarUsuariosBack()"  method = 'POST'> <label for='correo'>Correo:</label> <input type='email' id='correo' name='correo' value='${us[0].Correo}' required> <label for='Clave'>Contraseña:</label> <input type='password' id='Clave' name='clave' value='${us[0].Clave}' required> <label for='Pais'>Pais:</label> <input type='text' id='Pais' name='pais' value='${us[0].Pais}' required> <label for='CP'>Código Postal:</label> <input type='text' id='cp' name='cp' value='${us[0].CP}'> <label for='Ciudad'>Ciudad:</label> <input type='text' id='Ciudad' name='ciudad' value='${us[0].Ciudad}' required> <label for='Direccion'>Dirección:</label> <input type='text' id='Direccion' name='direccion' value='${us[0].Direccion}' required> <input type='hidden' id='cod' value='${us[0].CodRes}'> <div> <a href='#' onclick="cargarUsuariosBack()">Cancelar</a> <input type='submit' value='Actualizar'></div> </form>`;
        }
    };
    xhttp.open("GET", `back-end-usuarios_json.php?editar=${id}`, true);
    xhttp.send();
    return false;
}

function editarUsuariosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido actualizar el usuario";
            }else{
                cargarUsuariosBack();
            }
        }
    };

    let correo = document.getElementById("correo").value;
    let clave= document.getElementById("Clave").value;
    let pais = document.getElementById("Pais").value;
    let cp= document.getElementById("cp").value;
    let ciudad= document.getElementById("Ciudad").value;
    let direccion= document.getElementById("Direccion").value;
    let cod= document.getElementById("cod").value;
    var params = "correo=" + correo + "&clave=" + clave + "&pais=" + pais + "&cp=" + cp + "&cod=" + cod + "&ciudad=" + ciudad  +  "&direccion=" + direccion;
    xhttp.open("POST", "back-end-usuarios_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function eliminarUsuariosBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            cargarUsuariosBack();
        }
    };
    xhttp.open("GET", `back-end-usuarios_json.php?id=${id}&borrar=true`, true);
    xhttp.send();
    return false;
}

function cargarProductosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            contenido.innerHTML="";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="ADMINISTRACIÓN"
            let tabla = "<table><tr><th>CodProd</th><th>Nombre</th><th>Descripción</th><th>Peso</th><th>Stock</th><th>CodCat</th><th></th><th> </th></tr>";
            var prods =  JSON.parse(xhttp.responseText);
            contenido.innerHTML=""
            for(var i = 0; i < prods.length; i++){
                tabla += `<tr><td> ${prods[i].CodProd} </td><td> ${prods[i].Nombre} </td><td> ${prods[i].Descripcion} </td><td> ${prods[i].Peso}</td><td> ${prods[i].Stock}</td><td> ${prods[i].CodCat}</td><td><a href='#' onclick="productosBack(${prods[i].CodProd},${prods[i].CodCat})">Editar</a></td><td><a href='#' onclick="eliminarProductosBack(${prods[i].CodProd})">Eliminar</a></td></tr>`;
            }

            tabla += "<tr><td colspan='8'><a href='#' onclick='nuevosProductosBack()'>Crear Nuevo Producto</a></td></tr></table>";
            contenido.innerHTML=tabla;
        }
    };
    xhttp.open("GET", "back-end-productos_json.php?producto=true", true);
    xhttp.send();
    return false;
}

function nuevosProductosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var cats =  JSON.parse(xhttp.responseText);
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Crear Nuevo Producto";
            contenido.innerHTML="";
            let form="";
            form = `<form onsubmit="return crearNuevosProductosBack()"  method = 'POST'><label for='nombre'>Nombre:</label> <input type='text' id='nombre' name='nuevoNombre'  required> <label for='descripcion'>Descripción:</label> <textarea name='nuevaDescripcion' id='descripcion' cols='30' rows='5'  required></textarea> <label for='peso'>Peso:</label> <input type='number' id='peso' name='nuevoPeso' min='0' required> <label for='stock'>Stock:</label> <input type='number' id='stock' name='nuevoStock' min='0' required> <label for='CodCat'>Categoría:</label><select name='nuevoCodCat' id='CodCat'>`;
            for(var i = 0; i < cats.length; i++){
                form += `<option value="${cats[i].CodCat}">${cats[i].Nombre}</option>`;
            }
            form += `</select> <div> <a href='#' onclick="cargarProductosBack()">Cancelar</a><input type='submit' value='Crear'></div></form>`;
            contenido.innerHTML=form;
        }
    };
    xhttp.open("GET", `back-end-productos_json.php?nuevoProducto=true`, true);
    xhttp.send();
    return false;
}

function crearNuevosProductosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido añadir el usuario";
            }else{
                cargarProductosBack();
            }
        }
    };

    let nombre = document.getElementById("nombre").value;
    let descripcion= document.getElementById("descripcion").value;
    let peso = document.getElementById("peso").value;
    let stock= document.getElementById("stock").value;
    let codCat= document.getElementById("CodCat").value;

    var params = "nuevoNombre=" + nombre + "&nuevaDescripcion=" + descripcion + "&nuevoPeso=" + peso + "&nuevoStock=" + stock + "&nuevoCodCat=" + codCat;
    xhttp.open("POST", "back-end-productos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function productosBack(codProd,codCat) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            let prod =  JSON.parse(xhttp.responseText);

            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Editar Productos";
            contenido.innerHTML="";
            let form = `<form  onsubmit="return editarProductosBack()"  method = 'POST'> <label for='nombre'>Nombre:</label> <input type='text' id='nombre' name='nuevoNombre' value="${prod.productos[0].Nombre}" required> <label for='descripcion'>Descripción:</label> <textarea name='nuevaDescripcion' id='descripcion' cols='30' rows='5'  required>${prod.productos[0].Descripcion}</textarea> <label for='peso'>Peso:</label> <input type='number' id='peso' name='nuevoPeso' value="${prod.productos[0].Peso}" min='0' required> <label for='stock'>Stock:</label> <input type='number' id='stock' value="${prod.productos[0].Stock}" name='nuevoStock' min='0' required> <label for='CodCat'>Categoría:</label><select name='nuevoCodCat' id='CodCat'>`;
            for(let i=0;i<prod.categorias.length;i++){
                if(prod.categorias[i].CodCat == codCat){
                    form +=  `<option value="${prod.categorias[i].CodCat}" selected>${prod.categorias[i].Nombre}</option>`;
                }else{
                    form +=  `<option value="${prod.categorias[i].CodCat}">${prod.categorias[i].Nombre}</option>`;
                }
            }
            form += `</select><div><input id="cod" type="hidden" value="${codProd}"> <a href='#' onclick="cargarProductosBack()">Cancelar</a><input type='submit' value='Actualizar'></div></form>`;
            contenido.innerHTML = form;
        }
    };
    xhttp.open("GET", `back-end-productos_json.php?editar=${codProd}`, true);
    xhttp.send();
    return false;
}

function editarProductosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido actualizar el usuario";
            }else{
                cargarProductosBack();
            }
        }
    };

    let nombre = document.getElementById("nombre").value;
    let descripcion= document.getElementById("descripcion").value;
    let peso = document.getElementById("peso").value;
    let stock= document.getElementById("stock").value;
    let codCat= document.getElementById("CodCat").value;
    let cod= document.getElementById("cod").value;
    var params = "nombre=" + nombre + "&descripcion=" + descripcion + "&peso=" + peso + "&stock=" + stock + "&CodCat=" + codCat + "&codProd=" + cod;
    xhttp.open("POST", "back-end-productos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function eliminarProductosBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            cargarProductosBack();
        }
    };
    xhttp.open("GET", `back-end-productos_json.php?id=${id}&borrar=true`, true);
    xhttp.send();
    return false;
}

function cargarPedidosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            contenido.innerHTML="";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="ADMINISTRACIÓN"
            let tabla = "<table><tr><th>CodPed</th><th>Fecha</th><th>Enviado</th><th>Restaurante</th><th></th><th> </th></tr>";
            var ped =  JSON.parse(xhttp.responseText);
            contenido.innerHTML=""
            for(var i = 0; i < ped.length; i++){
                tabla += `<tr><td> ${ped[i].CodPed} </td><td> ${ped[i].Fecha} </td><td>${ped[i].Enviado}</td><td>${ped[i].Restaurante}</td><td><a href='#' onclick="pedidosBack(${ped[i].CodPed})">Editar</a></td><td><a href='#' onclick="eliminarPedidosBack(${ped[i].CodPed})">Eliminar</a></td></tr>`;
            }

            tabla += "<tr><td colspan='8'><a href='#' onclick='nuevosPedidosBack()'>Crear Nuevo Pedido</a></td></tr></table>";
            contenido.innerHTML=tabla;
        }
    };
    xhttp.open("GET", "back-end-pedidos_json.php?pedido=true", true);
    xhttp.send();
    return false;
}

function pedidosBack(CodPed) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            let ped =  JSON.parse(xhttp.responseText);
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Editar Pedidos";
            contenido.innerHTML="";
            let form = `<form  onsubmit="return editarPedidosBack()"  method = 'POST'> <label for='Fecha'>Fecha:</label> <input type='text' id='fecha' value="${ped.pedido[0].Fecha}" required> <label for='enviado'>Enviado:</label> <select id="enviado">` ;

                if(ped.pedido[0].Enviado == 0){
                    form +=  `<option value="0" selected>No</option><option value="1">Si</option>`;
                }else{
                    form +=  `<option value="0">No</option><option selected value="1">Si</option>`;
                }
            form += `</select> <label for='restaurante'>Restaurante:</label><select id="restaurante">`
            for (let i=0 ; i<ped.usuarios.length;i++){
                if(ped.usuarios[i]==ped.pedido.Restaurante){
                    form += `<option value="${ped.usuarios[i].CodRes}" selected>${ped.usuarios[i].CodRes}</option>`;
                }else{
                    form += `<option value="${ped.usuarios[i].CodRes}">${ped.usuarios[i].CodRes}</option>`;
                }
            }
            form += `</select><div><input id="cod" type="hidden" value="${ped.pedido[0].CodPed}"> <a href='#' onclick="cargarPedidosBack()">Cancelar</a><input type='submit' value='Actualizar'></div></form>`;
            contenido.innerHTML = form;
        }
    };
    xhttp.open("GET", `back-end-pedidos_json.php?editar=${CodPed}`, true);
    xhttp.send();
    return false;
}

function editarPedidosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido actualizar el usuario";
            }else{
                cargarPedidosBack();
            }
        }
    };

    let codped = document.getElementById("cod").value;
    let fecha = document.getElementById("fecha").value;
    let enviado = document.getElementById("enviado").value;
    let restaurante = document.getElementById("restaurante").value;
    var params = "CodPed=" + codped + "&Fecha=" + fecha + "&Enviado=" + enviado + "&Restaurante=" + restaurante;
    xhttp.open("POST", "back-end-pedidos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function nuevosPedidosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            let ped =  JSON.parse(xhttp.responseText);
            document.getElementById("titulo").innerHTML="Crear Nuevos Pedidos";
            contenido.innerHTML="";
            let form = `<form  onsubmit="return crearNuevosPedidosBack()"  method = 'POST'> <label for='Fecha'>Fecha:</label> <input type='text' id='fecha' required> <label for='enviado'>Enviado:</label> <select id="enviado">` ;
            form +=  `<option value="0">No</option><option value="1">Si</option>`;
            form += `</select> <label for="restaurante">Restaurante:</label><select id="restaurante">`
            for (let i=0 ; i<ped.usuarios.length;i++){
                form += `<option value="${ped.usuarios[i].CodRes}">${ped.usuarios[i].CodRes}</option>`;
            }
            form += `</select><div><a href='#' onclick="cargarPedidosBack()">Cancelar</a><input type='submit' value='Crear'></div></form>`;
            contenido.innerHTML = form;
        }
    };
    xhttp.open("GET", `back-end-pedidos_json.php?nuevoPedido=true`, true);
    xhttp.send();
    return false;
}

function crearNuevosPedidosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido añadir el pedido";
            }else{
                cargarPedidosBack();
            }
        }
    };

    let fecha = document.getElementById("fecha").value;
    let enviado = document.getElementById("enviado").value;
    let restaurante = document.getElementById("restaurante").value;

    var params = "nuevaFecha=" + fecha + "&nuevoEnviado=" + enviado + "&nuevoRestaurante=" + restaurante;
    xhttp.open("POST", "back-end-pedidos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}


function eliminarPedidosBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            cargarPedidosBack();
        }
    };
    xhttp.open("GET", `back-end-pedidos_json.php?id=${id}&borrar=true`, true);
    xhttp.send();
    return false;
}

//PedidosProductos

function cargarPedidosProductosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="ADMINISTRACION"
            contenido.innerHTML="";
            let tabla = "<table><tr><th>CodPredProd</th><th>CodPed</th><th>CodProd</th><th>Unidades</th><th></th><th> </th></tr>";
            var ped =  JSON.parse(xhttp.responseText);
            contenido.innerHTML=""
            for(var i = 0; i < ped.length; i++){
                tabla += `<tr><td> ${ped[i].CodPredProd} </td><td> ${ped[i].CodPed} </td><td>${ped[i].CodProd}</td><td>${ped[i].Unidades}</td><td><a href='#' onclick="pedidosProductosBack(${ped[i].CodPredProd})">Editar</a></td><td><a href='#' onclick="eliminarPedidosProductosBack(${ped[i].CodPredProd})">Eliminar</a></td></tr>`;
            }

            tabla += "<tr><td colspan='8'><a href='#' onclick='nuevosPedidosProductosBack()'>Crear Nuevo PedidoProducto</a></td></tr></table>";
            contenido.innerHTML=tabla;
        }
    };
    xhttp.open("GET", "back-end-pedidosProductos_json.php?pedidoProducto=true", true);
    xhttp.send();
    return false;
}

function pedidosProductosBack(CodPed) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            let ped =  JSON.parse(xhttp.responseText);
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Editar Pedidos";
            contenido.innerHTML="";
            let form = `<form onsubmit='return editarPedidosProductosBack()'  method = 'POST'> <label for='nuevoCodPed'>Código Pedido:</label> <input type='number' id='nuevoCodPed' name='nuevoCodPed' value='${ped[0].CodPed}' required> <label for='nuevoCodProd'>Código Producto:</label> <input type='number' value="${ped[0].CodProd}" name='nuevoCodProd' id='nuevoCodProd' required> <label for='nuevaUnidades'>Unidades:</label> <input type='number' value="${ped[0].Unidades}" name='nuevaUnidades' id='nuevaUnidades' required><input type='hidden' value="${ped[0].CodPredProd}"  id='CodPredProd' required> <div> <a href='#' onclick='cargarPedidosProductosBack()'>Cancelar</a> <input type='submit' value='Actualizar'></div> </form>`;
            contenido.innerHTML=form;
        }
    };
    xhttp.open("GET", `back-end-pedidosProductos_json.php?editar=${CodPed}`, true);
    xhttp.send();
    return false;
}

function editarPedidosProductosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido actualizar el usuario";
            }else{
                cargarPedidosProductosBack();
            }
        }
    };

    let CodPed = document.getElementById("nuevoCodPed").value;
    let CodProd = document.getElementById("nuevoCodProd").value;
    let Unidades = document.getElementById("nuevaUnidades").value;
    let CodPredProd = document.getElementById("CodPredProd").value;

    var params = "codPedido=" + CodPed + "&codProd=" + CodProd + "&unidades=" + Unidades + "&codPred=" + CodPredProd;
    xhttp.open("POST", "back-end-pedidosProductos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}

function nuevosPedidosProductosBack() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var contenido = document.getElementById("contenido");
            document.getElementById("titulo").innerHTML="Nuevos Pedidos";
            contenido.innerHTML="";
            let form = "<form onsubmit='return crearNuevosPedidosProductosBack()'  method = 'POST'> <label for='nuevoCodPed'>Código Pedido:</label> <input type='number' id='nuevoCodPed' name='nuevoCodPed'required> <label for='nuevoCodProd'>Código Producto:</label> <input type='number' name='nuevoCodProd' id='nuevoCodProd' required> <label for='nuevaUnidades'>Unidades:</label> <input type='number' name='nuevaUnidades' id='nuevaUnidades' required> <div> <a href='#' onclick='cargarPedidosProductosBack()'>Cancelar</a> <input type='submit' value='Crear'></div> </form>";
            contenido.innerHTML = form;
        }
    };
    xhttp.open("GET", `back-end-pedidosProductos_json.php?nuevoPedidoProducto=true`, true);
    xhttp.send();
    return false;
}

function crearNuevosPedidosProductosBack() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {

            if(this.responseText=="false"){
                document.getElementById("error").innerHTML="No se ha podido añadir el pedido";
            }else{
                cargarPedidosProductosBack();
            }
        }
    };

    let CodPed = document.getElementById("nuevoCodPed").value;
    let CodProd = document.getElementById("nuevoCodProd").value;
    let Unidades = document.getElementById("nuevaUnidades").value;

    var params = "nuevoCodPed=" + CodPed + "&nuevoCodProd=" + CodProd + "&nuevaUnidades=" + Unidades;
    xhttp.open("POST", "back-end-pedidosProductos_json.php", true);
    xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xhttp.send(params);
    return false;
}


function eliminarPedidosProductosBack(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            cargarPedidosProductosBack();
        }
    };
    xhttp.open("GET", `back-end-pedidosProductos_json.php?id=${id}&borrar=true`, true);
    xhttp.send();
    return false;
}


function cargarMiniCarrito(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("carri").innerHTML = "";

            try{
                document.getElementById("admin").innerHTML="";
                var filas =  JSON.parse(this.responseText);
                tabla = crearTablaMiniCarrito(filas);

                document.getElementById("carri").innerHTML = tabla.outerHTML;

            }catch(e){
                document.getElementById("carri").innerHTML = `<p>Todavia no tienes Productos en el Carrito</p>`;
            }

        }else{
            document.getElementById("carri").innerHTML = `<p>Todavia no tienes Productos en el Carrito</p>`;
        }
    };
    xhttp.open("GET", "carrito_json.php", true);
    xhttp.send();
    return false;
}

function cargarNavAdmin(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

            try{
                document.getElementById("titulo").innerHTML="ADMINISTRACIÓN"
                document.getElementById("contenido").innerHTML=this.responseText;
                document.getElementById("carri").innerHTML="";

            }catch(e){
                document.getElementById("contenido").innerHTML = `<p>Error</p>`;
            }

        }else{
            document.getElementById("contenido").innerHTML = `<p>Error</p>`;
        }
    };
    xhttp.open("GET", "navAdmin_json.php", true);
    xhttp.send();
    return false;
}
