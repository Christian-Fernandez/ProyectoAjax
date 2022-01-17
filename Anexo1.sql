CREATE DATABASE IF NOT EXISTS pedidos DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
use pedidos;
CREATE TABLE categoria (
                           Codcat integer(11) AUTO_INCREMENT PRIMARY KEY,
                           Nombre varchar(45) UNIQUE NOT NULL,
                           Descripcion varchar(200) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
INSERT INTO categoria (Codcat, Nombre, Descripcion)
VALUES
    (
        1, "Comida", "Platos e ingredientes"
    ),
    (
        2, "Bebidas sin", "Bebidas sin alcohol"
    ),
    (
        3, "Bebidas con", "Bebidas con alcohol"
    );
CREATE TABLE restaurantes (
                              CodRes integer(11) AUTO_INCREMENT PRIMARY KEY,
                              Correo varchar(90) NOT NULL,
                              Clave varchar(45) NOT NULL,
                              Pais varchar(45) NOT NULL,
                              CP integer(5) DEFAULT NULL,
                              Ciudad varchar(45) NOT NULL,
                              Direccion varchar(200) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
insert into restaurantes (
    CodRes, Correo, Clave, Pais, CP, Ciudad,
    Direccion
)
VALUES
    (
        1, "madrid1@empresa.com", "1234",
        "España", 28002, "Madrid", "C/ Padre Claret, 8"
    ),
    (
        2, "cadiz1@empresa.com", "1234", "España",
        11001, "Cádiz", "C/ Los Portales, 2"
    );
CREATE TABLE pedidos (
                         CodPed integer(11) AUTO_INCREMENT PRIMARY KEY,
                         Fecha datetime NOT NULL,
                         Enviado integer(11) NOT NULL,
                         Restaurante integer(11) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE
    pedidos
    add
        constraint fk1_pedidos_restaurante FOREIGN KEY (Restaurante) REFERENCES restaurantes (CodRes);
CREATE TABLE productos (
                           CodProd integer(11) AUTO_INCREMENT PRIMARY KEY,
                           Nombre varchar(45) DEFAULT NULL,
                           Descripcion varchar(90) NOT NULL,
                           Peso float not null,
                           Stock integer(11) NOT NULL,
                           CodCat integer(11) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE
    productos
    add
        constraint fk1_productos FOREIGN KEY (CodCat) REFERENCES categoria (Codcat);
INSERT INTO productos (
    CodProd, Nombre, Descripcion, Peso,
    Stock, CodCat
)
VALUES
    (
        1, "Harina", "8 paquetes de 1kg de harina cada uno",
        8, 100, 1
    ),
    (
        2, "Azúcar", "20 paquetes de 1kg cada uno",
        20, 3, 1
    ),
    (
        3, "Agua 0.5", "100 botellas de 0.5 litros cada una",
        51, 100, 2
    ),
    (
        4, "Agua 1.5", "20 botellas de 1.5 litros cada una",
        31, 50, 2
    ),
    (
        5, "Cerveza Alhambra tercio", "24 botellas de 33cl",
        10, 0, 3
    ),
    (
        6, "Vino tinto Rioja 0.75", "6 botellas de 0.75",
        5.5, 10, 3
    );
CREATE TABLE pedidosproductos (
                                  CodPredProd integer(11) AUTO_INCREMENT PRIMARY KEY,
                                  CodPed integer(11) NOT NULL,
                                  CodProd integer(11) NOT NULL,
                                  Unidades integer(11) NOT NULL,
                                  CONSTRAINT fk1_pedidos_productos FOREIGN KEY (CodPed) REFERENCES pedidos (CodPed),
                                  CONSTRAINT fk2_pedidos_productos FOREIGN KEY (CodProd) REFERENCES productos (CodProd)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

