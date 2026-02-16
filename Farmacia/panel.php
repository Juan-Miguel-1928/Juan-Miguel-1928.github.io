<?php
session_start();

//1. VERIFICAR SESIÓN ACTIVA
if (!isset($_SESSION['usuario_id'])) {
   header("Location: login.php");
    exit();
}

// 2. CONEXIÓN A LA BASE
require_once "conexion.php";

// 3. FILTRO POR CATEGORÍA
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";

// Consulta principal de medicamentos
$sql = "SELECT m.id, m.nombre, m.categoria, m.cantidad, m.precio,
               p.nombre AS proveedor
        FROM medicamentos m
        LEFT JOIN proveedores p ON m.proveedor_id = p.id";

if ($categoria !== "") {
    $sql .= " WHERE m.categoria = ?";
}

$sql .= " ORDER BY m.nombre ASC";

$stmt = $conexion->prepare($sql);

if ($categoria !== "") {
    $stmt->bind_param("s", $categoria);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Consulta para categorías únicas
$sqlCat = "SELECT DISTINCT categoria FROM medicamentos ORDER BY categoria";
$categoriasRes = $conexion->query($sqlCat);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Inventario</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      body{
        background-color:#ecf0f5;
      }
      .navbar {
        box-shadow: 0 1px 3px rgba(0,0,0,.2);
      }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">
      <i class="bi bi-hospital me-2"></i>Panel de Inventario
    </span>
    <div>
      <a href="registro.php" class="btn btn-sm btn-success me-2">
        <i class="bi bi-plus-lg"></i> Nuevo medicamento
      </a>
      <a href="cerrar_sesion.php" class="btn btn-sm btn-outline-light">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
      </a>
    </div>
  </div>
</nav>

<div class="container">

    <!-- FILTRO POR CATEGORÍA -->
    <h2 class="mb-3">Inventario de medicamentos</h2>
    <form class="row g-3 mb-4" method="get" action="panel.php">
        <div class="col-auto">
            <label for="categoria" class="col-form-label">Filtrar por categoría:</label>
        </div>
        <div class="col-auto">
            <select name="categoria" id="categoria" class="form-select">
                <option value="">Todas</option>
                <?php while ($rowCat = $categoriasRes->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($rowCat['categoria']); ?>"
                        <?php if ($categoria === $rowCat['categoria']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($rowCat['categoria']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-funnel"></i> Aplicar filtro
            </button>
        </div>
    </form>

    <!-- TABLA -->
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle">
          <thead class="table-primary">
              <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Categoría</th>
                  <th>Cantidad</th>
                  <th>Precio</th>
                  <th>Proveedor</th>
                  <th>Acciones</th>
              </tr>
          </thead>
          <tbody>
          <?php if ($resultado->num_rows > 0): ?>
              <?php while ($med = $resultado->fetch_assoc()): ?>
                  <tr>
                      <td><?php echo $med['id']; ?></td>
                      <td><?php echo htmlspecialchars($med['nombre']); ?></td>
                      <td><?php echo htmlspecialchars($med['categoria']); ?></td>
                      <td><?php echo $med['cantidad']; ?></td>
                      <td>$<?php echo number_format($med['precio'], 2); ?></td>
                      <td><?php echo htmlspecialchars($med['proveedor'] ?? 'Sin proveedor'); ?></td>

                      <td>
                          <a href="editar_medicamento.php?id=<?php echo $med['id']; ?>" class="btn btn-sm btn-warning mb-1">
                              <i class="bi bi-pencil-square"></i> Editar
                          </a>
                          <a href="eliminar_medicamento.php?id=<?php echo $med['id']; ?>"
                             class="btn btn-sm btn-danger mb-1"
                             onclick="return confirm('¿Seguro que deseas eliminar este medicamento?');">
                              <i class="bi bi-trash"></i> Eliminar
                          </a>
                      </td>
                  </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="7" class="text-center">No hay medicamentos registrados.</td>
              </tr>
          <?php endif; ?>
          </tbody>
      </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$stmt->close();
$conexion->close();
?>
