<?php
require_once 'conexion.php';

// 1. Definir los datos del admin
$email    = 'admin@farmacia.com';
$nombre   = 'marcos';
$rol      = 'admin';
$passwordPlano = 'hola2324';

// 2. Generar el hash seguro
$hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

// 3. Insertar o actualizar el usuario
$sql = "INSERT INTO usuarios (nombre, email, clave, rol, creado_en)
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            nombre = VALUES(nombre),
            clave  = VALUES(clave),
            rol    = VALUES(rol)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $hash, $rol);

if ($stmt->execute()) {
    echo "Usuario admin creado/actualizado correctamente.<br>";
    echo "Correo: $email<br>";
    echo "Contraseña: $passwordPlano<br>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
