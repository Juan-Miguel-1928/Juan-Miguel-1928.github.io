<?php
session_start();  // 1. Inicia la sesion 
session_destroy();         // 2. Borra todala info de la sesión actual
                               
header("Location: login.php");  // 3. Redirige al usuario de vuelta al login
exit();         // 4. Termina la ejecución para que no siga cargando nada más
?>