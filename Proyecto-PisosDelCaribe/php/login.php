<?php
session_start();
require "enlaze_base_de_datos.php";

$username = $_POST["username"];
$password = $_POST["password"];

$stmt = $mysqli->prepare("SELECT * FROM proyectoWeb.usuarios WHERE nombre = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($usuario && $password === $usuario["contraseña"]) {
session_regenerate_id(true);
    $_SESSION["usuario"] = $usuario["nombre"];  
    $_SESSION["rol"] = $usuario["rol"];
    $_SESSION['uid'] = $usuario['id_usuario'];
    if ($usuario["rol"] === "admin") {
        header("Location: ../Inventario.php");
    } elseif ($usuario["rol"] === "cliente") {
        header("Location: ../home.php");
    } 
    exit();
} else {
    echo "<script>alert('Usuario o contraseña incorrectos.'); window.location='../login.html';</script>";
    exit();
}
?>
