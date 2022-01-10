function cargarCategorias(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if(this.readyState == 4 && this.status == 200){
            var cats = JSON.parse(xhttp.responseText);
            var lista = document.createElement("ul");
            for (var i = 0;i<cats.length;i++){
                var elem = document.createElement("li");
                var vinculo = document.createElement("a");
                var ruta = "productos_json.php?categoria="+ cats[i].CodCat;
                vinculo.href=ruta;
                vinculo.innerHTML = cats[i].Nombre;
                vinculo.onclick = function (){return cargarProductos(this);};
                elem.appendChild(vinculo);
                lista.appendChild(elem);
            }
            var contenido = document.getElementById("contenido");
            contenido.innerHTML="";
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="Categorias";
            contenido.appendChild(lista);
        }
    };
    xhttp.open("GET","categorias_json.php",true);
    xhttp.send();
    return false;
}

function cargarProductos(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function (){
        if(this.readyState == 4 && this.status == 200){
            var prod = document.getElementById("contenido");
            var titulo = document.getElementById("titulo");
            titulo.innerHTML="Productos";
            try {
                var filas = JSON.parse(this.responseText);
                var tabla = crearTablaProductos(filas);
                prod.innerHTML="";
                prod.appendChild(tabla);
            }catch (e){
                var mensaje=document.createElement("p");
                mensaje.innerHTML = "Categorias sin productos";
                prod.innerHTML="";
                prod.appendChild(mensaje);
            }
        }
    };
    xhttp.open("GET",destino,true);
    xhttp.send();
    return false;
}

function crearTablaProductos(productos){
    var tabla = document.createElement("table");
    var cabecera = crear_fila(["Código","Nombre","Descripción","Stock","Comprar"],"th");
    tabla.appendChild(cabecera);
    for(var i = 0;i<productos.length;i++){
        formu = crearFormulario("Añadir",productos[i].CodProd,añadirProductos);
        fila = crear_fila([productos[i].CodProd,productos[i].Nombre,productos[i].Descripcion,productos[i].Stock],"td");
        celda_form = document.createElement("td");
        celda_form.appendChild(formu);
        fila.appendChild(celda_form);
        tabla.appendChild(fila);
    }

    return tabla;
}

function crear_fila(campos, tipo){
    var fila = document.createElement("tr");
    for(var i=0;i< campos.length;i++){
        var celda = document.createElement(tipo);
        celda.innerHTML=campos[i];
        fila.appendChild(celda);
    }
    return fila;
}