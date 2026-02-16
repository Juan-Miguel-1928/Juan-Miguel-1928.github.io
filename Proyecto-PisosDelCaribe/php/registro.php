<?php
require "enlaze_base_de_datos.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre     = $_POST["nombre"];
    $correo     = $_POST["correo"];
    $telefono   = $_POST["telefono"];
    $contraseña = $_POST["contraseña"];
    $mensaje = '';
    $sql = "SELECT nombre, correo, telefono 
            FROM proyectoWeb.usuarios 
            WHERE nombre = ? OR correo = ? OR telefono = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $telefono);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['nombre'] === $nombre) {
            
            echo "<script>alert('El nombre ya está registrado.'); window.location='../registro.html';</script>";
        } elseif ($row['correo'] === $correo) {
            echo "<script>alert('El correo ya está registrado.'); window.location='../registro.html';</script>";
        } elseif ($row['telefono'] === $telefono) {
            echo "<script>alert('El teléfono ya está registrado.'); window.location='../registro.html';</script>";
        }
        exit();
    }

    $sql = "INSERT INTO proyectoWeb.usuarios (nombre, correo, telefono, contraseña, rol) VALUES (?, ?, ?, ?, 'cliente')";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo, $telefono, $contraseña);
    $stmt->execute();

    $id_usuario = $stmt->insert_id;

    $sqlCliente = "INSERT INTO proyectoWeb.clientes (id_usuario) VALUES (?)";
    $stmtCliente = $mysqli->prepare($sqlCliente);
    $stmtCliente->bind_param("i", $id_usuario);
    $stmtCliente->execute();

    $id_cliente = $stmtCliente->insert_id;

    $sqlCarrito = "INSERT INTO proyectoWeb.carrito (fechaCreacion, id_cliente) VALUES (CURDATE(), ?)";
    $stmtCarrito = $mysqli->prepare($sqlCarrito);
    $stmtCarrito->bind_param("i", $id_cliente);
    $stmtCarrito->execute();

    echo "<script>alert('Usuario registrado correctamente.'); window.location='../login.html';</script>";    exit();
    exit();
}
?>
