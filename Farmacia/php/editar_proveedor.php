<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";

$mensaje = "";
$tipo = "";

// === ELIMINAR PROVEEDOR ===
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];

    // Verificar si tiene medicamentos asociados
    $check = $conexion->prepare("SELECT COUNT(*) FROM medicamentos WHERE proveedor_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        $mensaje = "No se puede eliminar: el proveedor tiene medicamentos asociados.";
        $tipo = "warning";
    } else {
        $stmt = $conexion->prepare("DELETE FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $mensaje = "Proveedor eliminado correctamente.";
            $tipo = "success";
        }
        $stmt->close();
    }
}

// Actualizar proveedor
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if (empty($nombre)) {
        $mensaje = "El nombre del proveedor es obligatorio.";
        $tipo = "danger";
    } else {
        if ($id) {
            // Actualizar
            $sql = "UPDATE proveedores SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $id);
            $accion = "actualizado";
        } else {
            // Insertar nuevo
            $sql = "INSERT INTO proveedores (nombre, telefono, direccion, creado_en) VALUES (?, ?, ?, NOW())";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $nombre, $telefono, $direccion);
            $accion = "registrado";
        }

        if ($stmt->execute()) {
            $mensaje = "Proveedor $accion correctamente.";
            $tipo = "success";
            // Limpiar formulario después de éxito
            $_POST = [];
        } else {
            $mensaje = "Error al guardar: " . $stmt->error;
            $tipo = "danger";
        }
        $stmt->close();
    }
}

// Cargar datos para edición
$proveedor_edit = null;
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM proveedores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $proveedor_edit = $res->fetch_assoc();
    }
    $stmt->close();
}

// Lista de proveedores
$proveedores = $conexion->query("SELECT * FROM proveedores ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #ecf0f5; }
        .navbar { box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
            <i class="bi bi-truck"></i> Gestión de Proveedores
        </span>
        <a href="panel.php" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
    </div>
</nav>

<div class="container">
    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo ?> alert-dismissible fade show">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario -->
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-header <?= $proveedor_edit ? 'bg-warning' : 'bg-primary' ?> text-white">
                    <h5 class="mb-0">
                        <?= $proveedor_edit ? 'Editar Proveedor' : 'Nuevo Proveedor' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($proveedor_edit): ?>
                            <input type="hidden" name="id" value="<?= $proveedor_edit['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?= htmlspecialchars($proveedor_edit['nombre'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="<?= htmlspecialchars($proveedor_edit['telefono'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea name="direccion" class="form-control" rows="3"><?= htmlspecialchars($proveedor_edit['direccion'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <?= $proveedor_edit ? 'Actualizar' : 'Guardar' ?> Proveedor
                        </button>
                        <?php if ($proveedor_edit): ?>
                            <a href="editar_proveedor.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Lista de Proveedores</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $proveedores->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $p['id'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td><?= htmlspecialchars($p['telefono'] ?: '—') ?></td>
                                    <td>
                                        <a href="?editar=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
                                            Editar
                                        </a>
                                        <a href="?eliminar=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Eliminar este proveedor?\nSolo si no tiene medicamentos asociados.');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($proveedores->num_rows == 0): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No hay proveedores aún</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conexion->close(); ?>