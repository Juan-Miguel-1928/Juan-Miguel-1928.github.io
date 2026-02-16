CREATE DATABASE IF NOT EXISTS proyectoWeb;
USE proyectoWeb;

CREATE TABLE proyectoWeb.usuarios(
id_usuario INT(100) NOT NULL AUTO_INCREMENT,
nombre VARCHAR(200) NOT NULL,
correo VARCHAR(100) NOT NULL UNIQUE,
telefono VARCHAR(20) NOT NULL,
contraseña VARCHAR(200) NOT NULL,
rol ENUM('cliente','admin') NOT NULL,
PRIMARY KEY (id_usuario)
);

INSERT INTO proyectoWeb.usuarios (nombre, correo , telefono , contraseña , rol) VALUES 
('Juan', '202400249@upqroo.edu.mx' , '9982183447' , '117','admin'),
('Dara', '202400289@upqroo.edu.mx', '9983767889' ,'1234','cliente'),
('Alexis', '202400214@upqroo.edu.mx' , '9981059616', '1234', 'admin'),
('Rosario', '202400248@upqroo.edu.mx', '9984806010','1234','cliente'),
('Ignacio', '202400255@upqroo.edu.mx', '9831674169','1234','cliente');

CREATE TABLE proyectoWeb.clientes(
id_cliente INT(100) NOT NULL AUTO_INCREMENT,
id_usuario INT(100) NOT NULL,
PRIMARY KEY(id_cliente),
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

INSERT INTO proyectoWeb.clientes(id_cliente, id_usuario) VALUES
('1', '2'),
('2', '4'),
('3', '5');

CREATE TABLE proyectoWeb.administradores(
id_admin INT(100) NOT NULL AUTO_INCREMENT,
id_usuario INT(100) NOT NULL,
PRIMARY KEY(id_admin),
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

INSERT INTO proyectoWeb.administradores(id_admin, id_usuario) VALUES
('1', '1'),
('2', '3');

CREATE TABLE proyectoWeb.venta(
id_venta INT(100) NOT NULL AUTO_INCREMENT,
fechaVenta DATE NOT NULL,
totalVenta DECIMAL(10,2) NOT NULL,
id_cliente INT(100) NOT NULL,
id_admin INT(100) NOT NULL,
PRIMARY KEY(id_venta),
FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
FOREIGN KEY(id_admin) REFERENCES administradores(id_admin)
);

CREATE TABLE proyectoWeb.carrito(
id_carrito INT(100) NOT NULL AUTO_INCREMENT,
fechaCreacion DATE NOT NULL,
id_cliente INT(100) NOT NULL,
PRIMARY KEY(id_carrito),
FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

CREATE TABLE proyectoWeb.productos(
id_producto INT(100) NOT NULL AUTO_INCREMENT,
nombreProducto VARCHAR(200) NOT NULL,
precio DECIMAL(10,2) NOT NULL,
tipo VARCHAR(100) NOT NULL,
imagenProducto VARCHAR(255),
PRIMARY KEY(id_producto)
);

INSERT INTO proyectoWeb.productos(nombreProducto, precio, tipo, imagenProducto) VALUES
('Concreto Gris Claro', '390','Concrete', 'img/cc.avif'),
('Concreto Gris Oscuro', '410','Concrete', 'img/cgo.avif'),
('Concreto Claro', '420','Concrete', 'img/ct.jpg'),
('Concreto Pulido', '425','Concrete', 'img/cp.jpg'),
('Roble Natural', '420','Forest', 'img/rb.jpeg'),
('Roble Ahumado', '435','Forest', 'img/ra.jpg'),
('Nogal Suave', '450','Forest', 'img/ns.jpg'),
('Nogal Oscuro', '470','Forest', 'img/no.jpeg'),
('Encino Gris', '445','Forest', 'img/eg.jpeg'),
('Herringbone Roble', '480','Herringbone', 'img/hr.jpg'),
('Herringbone Nogal', '510','Herringbone', 'img/hn.jpg'),
('Herringbone Gris', '465','Herringbone', 'img/hg.jpeg'),
('Herringbone Blanco', '495','Herringbone', 'img/hb.jpg'),
('Futura blanca', '430','Futura', 'img/fb.jpg'),
('Futura Negra', '445','Futura', 'img/fn.jpeg'),
('Futura Geometrica', '460','Futura', 'img/fg.jpg'),
('Futura Cemento Suave', '415','Futura', 'img/fc.jpg'),
('Max Ébano', '520','Max', 'img/me.jpeg'),
('Max Roble XL', '550','Max', 'img/mr.jpg'),
('Max Gris Suave', '490','Max', 'img/mg.jpg'),
('Max Arena', '475','Max', 'img/ma.jpg');

CREATE TABLE proyectoWeb.carrito_detalle(
id_carrito_detalle INT(100) NOT NULL AUTO_INCREMENT,
cantidad INT(200) NOT NULL,
precioUnitario DECIMAL(10,2) NOT NULL,
subtotal DECIMAL(10,2) NOT NULL,
id_carrito INT(100) NOT NULL,
id_producto INT(100) NOT NULL,
PRIMARY KEY(id_carrito_detalle),
FOREIGN KEY (id_carrito) REFERENCES carrito(id_carrito),
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

CREATE TABLE proyectoWeb.compra(
id_compra INT(100) NOT NULL AUTO_INCREMENT,
fechaCompra DATE NOT NULL,
totalCompra DECIMAL(10,2) NOT NULL,
id_admin INT(100) NOT NULL,
PRIMARY KEY(id_compra),
FOREIGN KEY(id_admin) REFERENCES administradores(id_admin)
);

CREATE TABLE proyectoWeb.compra_detalle(
id_compra_detalle INT(100) NOT NULL AUTO_INCREMENT,
cantidad INT(200) NOT NULL,
costoUnitario DECIMAL(10,2) NOT NULL,
subtotal DECIMAL(10,2) NOT NULL,
id_compra INT(100) NOT NULL,
id_producto INT(100) NOT NULL,
PRIMARY KEY(id_compra_detalle),
FOREIGN KEY(id_compra) REFERENCES compra(id_compra),
FOREIGN KEY(id_producto) REFERENCES productos(id_producto)
);
CREATE TABLE proyectoWeb.inventario(
id_inventario INT(100) NOT NULL AUTO_INCREMENT,
cantidadActual INT(200) NOT NULL,
ultimaActualizacion DATE NOT NULL,
porcetanjeVenta INT(200) NOT NULL,
id_producto INT(100) NOT NULL,
id_admin INT(100) NOT NULL,
PRIMARY KEY(id_inventario),
FOREIGN KEY(id_producto) REFERENCES productos(id_producto),
FOREIGN KEY(id_admin) REFERENCES administradores(id_admin)
);

INSERT INTO proyectoWeb.inventario( cantidadActual,porcetanjeVenta, ultimaActualizacion, id_producto, id_admin) VALUES
('0','72', '2025-11-24','1', '1'),
('21','45', '2025-11-11','2', '2'),
('64','91', '2025-09-05','3', '2'),
('120','88', '2025-10-01','4', '2'),
('12','64', '2025-05-30','5', '1'),
('80','33', '2025-03-06','6', '2'),
('71','77', '2025-11-02','7', '1'),
('0','51', '2025-07-30','8', '1'),
('34','82', '2025-09-15','9', '1'),
('63','95', '2025-05-23','10', '1'),
('119','8', '2025-02-21','11', '2'),
('45','41', '2025-11-12','12', '2'),
('60','67', '2025-08-05','13', '1'),
('81','29', '2025-07-25','14', '2'),
('20','54', '2025-11-20','15', '2'),
('45','38', '2025-01-16','16', '2'),
('30','89', '2025-11-26','17', '2'),
('120','93', '2025-06-04','18', '1'),
('0','61', '2025-04-25','19', '2'),
('20','44', '2025-11-20','20', '2'),
('50','74', '2025-11-20','21', '2');


CREATE TABLE proyectoWeb.contacto(
id_contacto INT(100) NOT NULL AUTO_INCREMENT,
nombre VARCHAR(200) NOT NULL,
apellidos VARCHAR(200) NOT NULL,
correo VARCHAR(200) NOT NULL,
telefono VARCHAR(200) NOT NULL,
mensaje TEXT NOT NULL,
fechaMensaje DATE DEFAULT(CURRENT_DATE),
PRIMARY KEY(id_contacto)
);
INSERT INTO proyectoWeb.carrito (fechaCreacion, id_cliente) VALUES
('2025-11-20', 1), -- Dara
('2025-11-21', 2), -- Rosario
('2025-11-22', 3); -- Ignacio

INSERT INTO proyectoWeb.carrito_detalle (cantidad, precioUnitario, subtotal, id_carrito, id_producto) VALUES
(1, 420, 420, 1, 4),
 (1, 550, 550, 1, 18),
 (3, 450, 1350, 1, 6),
(2, 495, 990, 1, 12),  -- Dara compra 2 Concreto Gris Claro
(1, 420, 420, 2, 4),  -- Rosario compra 1 Roble Natural
(3, 450, 1350, 3, 6); -- Ignacio compra 3 Nogal Suave


INSERT INTO proyectoWeb.venta (fechaVenta, totalVenta, id_cliente, id_admin) VALUES
('2025-11-20', 780, 1, 1),   -- Venta de Dara gestionada por Juan
('2025-11-21', 420, 2, 2),   -- Venta de Rosario gestionada por Alexis
('2025-11-22', 1350, 3, 1);  -- Venta de Ignacio gestionada por Juan

CREATE TABLE proyectoWeb.venta_detalle (
    id_venta_detalle INT NOT NULL AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    PRIMARY KEY(id_venta_detalle),
    FOREIGN KEY(id_venta) REFERENCES venta(id_venta),
    FOREIGN KEY(id_producto) REFERENCES productos(id_producto)
);

INSERT INTO proyectoWeb.venta_detalle
(id_venta, id_producto, cantidad, precioUnitario, subtotal)
VALUES
(1, 4, 1, 420, 420),   
(2, 4, 1, 420, 420),   
(3, 6, 3, 450, 1350);