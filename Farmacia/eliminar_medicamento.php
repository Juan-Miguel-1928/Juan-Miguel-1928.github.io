<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
   header("Location: login.php");
  exit();
}

require_once "conexion.php";

if (!isset($_GET['id'])) {
    header("Location: panel.php");
    exit();
}

$id = (int)$_GET['id'];

$sql = "DELETE FROM medicamentos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: panel.php?msg=eliminado");
} else {
    header("Location: panel.php?msg=error_eliminar");
}
exit();
