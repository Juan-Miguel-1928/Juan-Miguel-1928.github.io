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

// Obtener datos del medicamento
$sql = "SELECT * FROM medicamentos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$medRes = $stmt->get_result();

if ($medRes->num_rows == 0) {
    header("Location: panel.php?msg=noencontrado");
    exit();
}
$medicamento = $medRes->fetch_assoc();
$stmt->close();

// Proveedores
$proveedores = $conexion->query("SELECT id, nombre FROM proveedores ORDER BY nombre");

$error = "";

// ACTUALIZAR MEDICAMENTO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre       = trim($_POST['nombre']);
    $categoria    = trim($_POST['categoria']);
    $cantidad     = (int)$_POST['cantidad'];
    $precio       = (float)$_POST['precio'];
    $proveedor_id = !empty($_POST['proveedor_id']) ? (int)$_POST['proveedor_id'] : null;

    $sqlUpdate = "UPDATE medicamentos 
                  SET nombre = ?, categoria = ?, cantidad = ?, precio = ?, proveedor_id = ? 
                  WHERE id = ?";

    $stmtUp = $conexion->prepare($sqlUpdate);
    $stmtUp->bind_param("ssidii", $nombre, $categoria, $cantidad, $precio, $proveedor_id, $id);

    if ($stmtUp->execute()) {
        header("Location: editar_medicamento.php?id=$id&msg=actualizado");
        exit();
    } else {
        $error = "Error al actualizar: " . $conexion->error;
    }

    $stmtUp->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar medicamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color:#ecf0f5; }
        .navbar { box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Editar medicamento</span>
        <a href="panel.php" class="btn btn-sm btn-outline-light">Volver al panel</a>
    </div>
</nav>

<div class="container">

    <!-- ALERTA DE MEDICAMENTO ACTUALIZADO -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            El medicamento se actualizó correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card col-md-6 mx-auto p-4">
        <h3 class="mb-3 text-center">Editar medicamento</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($medicamento['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control"
                       value="<?= htmlspecialchars($medicamento['categoria']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="0"
                       value="<?= $medicamento['cantidad'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" step="0.01" name="precio" class="form-control" min="0"
                       value="<?= $medicamento['precio'] ?>" required>
            </div>

            <!-- PROVEEDORES -->
            <div class="mb-3">
                <label class="form-label">Proveedor</label>
                <div class="input-group">
                    <select name="proveedor_id" class="form-select">
                        <option value="">Sin proveedor</option>
                        <?php
                        $prov = $conexion->query("SELECT id, nombre FROM proveedores ORDER BY nombre");
                        while ($p = $prov->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>"
                                <?= (isset($medicamento['proveedor_id']) && $medicamento['proveedor_id'] == $p['id']) ||
                                   (isset($_POST['proveedor_id']) && $_POST['proveedor_id'] == $p['id'])
                                   ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <a href="editar_proveedor.php" class="btn btn-warning">Editar Proveedores</a>
                </div>
                <small class="text-muted">Agregar, editar o eliminar proveedores</small>
            </div>

            <button class="btn btn-primary w-100">Actualizar Medicamento</button>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conexion->close(); ?>
