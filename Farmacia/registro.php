<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
   header("Location: login.php");
   exit();
}

require_once "conexion.php";

// Obtener proveedores para el select
$proveedores = $conexion->query("SELECT id, nombre FROM proveedores ORDER BY nombre");

$exito = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre       = trim($_POST['nombre']);
    $categoria    = trim($_POST['categoria']);
    $cantidad     = (int)$_POST['cantidad'];
    $precio       = (float)$_POST['precio'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;

    $sql = "INSERT INTO medicamentos (nombre, categoria, cantidad, precio, proveedor_id)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssidi", $nombre, $categoria, $cantidad, $precio, $proveedor_id);

    if ($stmt->execute()) {
        $exito = true;
        $_POST = [];
    } else {
        $error = "Error al guardar: " . $conexion->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Medicamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
      body{background-color:#ecf0f5;}
      .navbar { box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">
      Registro de medicamentos
    </span>
    <a href="panel.php" class="btn btn-sm btn-outline-light">
      Volver al panel
    </a>
  </div>
</nav>

<div class="container">
    <div class="card col-md-6 mx-auto p-4">
        <h3 class="mb-3 text-center">Registrar nuevo medicamento</h3>

        <?php if ($exito): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                El medicamento se registró correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required 
                       value="<?php echo $_POST['nombre'] ?? ''; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" 
                       placeholder="Antibiótico, Analgésico, etc." required 
                       value="<?php echo $_POST['categoria'] ?? ''; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="0" required 
                       value="<?php echo $_POST['cantidad'] ?? '0'; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" step="0.01" name="precio" class="form-control" min="0" required 
                       value="<?php echo $_POST['precio'] ?? ''; ?>">
            </div>

            <!-- CAMPO PROVEEDOR CON BOTÓN AMARILLO -->
            <div class="mb-3">
                <label class="form-label">Proveedor</label>
                <div class="input-group">
                    <select name="proveedor_id" class="form-select">
                        <option value="">Sin proveedor</option>
                        <?php while ($p = $proveedores->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>"
                                <?php echo (isset($_POST['proveedor_id']) && $_POST['proveedor_id'] == $p['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <a href="editar_proveedor.php" target="_blank" class="btn btn-warning">
                        Editar Proveedores
                    </a>
                </div>
                <small class="text-muted">Haz clic en el botón para agregar o modificar proveedores</small>
            </div>

            <button class="btn btn-success w-100">
                Registrar Medicamento
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conexion->close(); ?>