<?php
session_start();
require "php/enlaze_base_de_datos.php";

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
  header("Location: login.html");
  exit();
}
function mostrarBotonSesion()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/cierre_de_sesion.php" class="btn btn-outline-danger fw-bold px-4">Cerrar sesión</a>';
  } else {
    echo '<a href="login.html" class="btn btn-outline-warning fw-bold px-4">Iniciar sesión</a>';
  }
}
function cantidad($mysqli)
{
  $sql = "SELECT SUM(cantidadActual) AS totalProductos FROM proyectoWeb.inventario";
  $result = $mysqli->query($sql);

  if ($result) {
    $row = $result->fetch_assoc();
    if ($row && $row['totalProductos'] !== null) {
      echo $row['totalProductos'];
    } else {
      echo "No se encontró el producto.";
    }
  } else {
    echo "Error en la consulta: " . $mysqli->error;
  }
}
function SinStock($mysqli)
{
  $sql = "SELECT COUNT(*) AS cantidad FROM proyectoWeb.inventario WHERE cantidadActual = 0";
  $result = $mysqli->query($sql);

  if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
      echo $row['cantidad']; // imprime el número de productos sin stock
    }
  } else {
    echo "Error en la consulta: " . $mysqli->error;
  }
}


function buscarProducto($mysqli, $busqueda)
{
  if (is_numeric($busqueda)) {
    $sql = "SELECT p.id_producto, p.nombreProducto, p.precio, p.imagenProducto, p.tipo,
               i.cantidadActual, i.porcetanjeVenta
        FROM proyectoWeb.productos p
        INNER JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
        WHERE p.id_producto = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $busqueda);
  } else {
    $sql = "SELECT p.id_producto, p.nombreProducto, p.precio, p.imagenProducto, p.tipo, 
                       i.cantidadActual, i.porcetanjeVenta
                FROM proyectoWeb.productos p
                INNER JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
                WHERE p.nombreProducto LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $like = "%$busqueda%";
    $stmt->bind_param("s", $like);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $productos = $result->fetch_all(MYSQLI_ASSOC);

  $total = count($productos);
  echo '<p class="text-muted mb-4">Mostrando <span id="count">' . $total . '</span> resultados</p>';

  if ($productos) {
    foreach ($productos as $producto) {
?>
      <div class="col-md-6 col-xl-4 mb-4">
        <div class="product-card">
          <div class="product-bg" style="background-image:url('<?php echo htmlspecialchars($producto['imagenProducto']); ?>')"></div>
          <div class="product-overlay"></div>

          <?php if ($producto['cantidadActual'] == 0) {
            echo "<div class='out-of-stock'>SIN STOCK</div>";
          } ?>

          <div class="product-info">
            <h6 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($producto['nombreProducto']); ?></h6>
            <p class="small text-white-50 mb-2">Colección: <?php echo htmlspecialchars($producto['tipo']); ?></p>
            <p class="h4 fw-bold text-warning mb-3">
              Desde $<?php echo htmlspecialchars($producto['precio']); ?>
              <small class="text-white-50">m²</small>
            </p>
          </div>

          <div class="badges-bottom">
            <span class="badge bg-success">Ventas <?php echo htmlspecialchars($producto['porcetanjeVenta']); ?>%</span>
            <span class="badge bg-light text-dark">
              <?php
              if ($producto['cantidadActual'] == 0) {
                echo "No hay Stock";
              } else {
                echo htmlspecialchars($producto['cantidadActual']) . " Piezas";
              }
              ?>
            </span>
          </div>
        </div>
      </div>
    <?php
    }
  } else {
    echo "<p>No se encontraron resultados para <strong>" . htmlspecialchars($busqueda) . "</strong>.</p>";
  }
}


function mostrarProductosFiltrados($mysqli, $orden = 'relevance', $tipos = [], $precios = [])
{
  $sql = "SELECT p.id_producto, p.nombreProducto, p.precio, p.imagenProducto, p.tipo, 
                   i.cantidadActual, i.porcetanjeVenta
            FROM proyectoWeb.productos p
            INNER JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
            WHERE 1=1";

  $params = [];
  $types  = "";

  if (!empty($tipos)) {
    $placeholders = implode(',', array_fill(0, count($tipos), '?'));
    $sql .= " AND p.tipo IN ($placeholders)";
    $params = array_merge($params, $tipos);
    $types .= str_repeat("s", count($tipos));
  }

  if (!empty($precios)) {
    $placeholders = implode(',', array_fill(0, count($precios), '?'));
    $sql .= " AND p.precio IN ($placeholders)";
    $params = array_merge($params, $precios);
    $types .= str_repeat("i", count($precios));
  }

  switch ($orden) {
    case 'price-asc':
      $sql .= " ORDER BY p.precio ASC";
      break;
    case 'price-desc':
      $sql .= " ORDER BY p.precio DESC";
      break;
    case 'sales':
      $sql .= " ORDER BY i.porcetanjeVenta DESC";
      break;
    case 'Stock-asc':
      $sql .= " ORDER BY i.cantidadActual ASC";
      break;
    case 'Stock-des':
      $sql .= " ORDER BY i.cantidadActual DESC";
      break;
    default:
      $sql .= " ORDER BY p.id_producto ASC";
  }

  $stmt = $mysqli->prepare($sql);
  if (!$stmt) {
    die("Error en prepare: " . $mysqli->error);
  }

  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  $productos = $result->fetch_all(MYSQLI_ASSOC);

  $total = count($productos);
  echo '<p class="text-muted mb-4">Mostrando <span id="count">' . $total . '</span> resultados</p>';

  foreach ($productos as $producto) {
    ?>
    <div class="col-md-6 col-xl-4 mb-4">
      <div class="product-card">
        <div class="product-bg" style="background-image:url('<?php echo htmlspecialchars($producto['imagenProducto']); ?>')"></div>
        <div class="product-overlay"></div>

        <?php if ($producto['cantidadActual'] == 0) {
          echo "<div class='out-of-stock'>SIN STOCK</div>";
        } ?>

        <div class="product-info">
          <h6 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($producto['nombreProducto']); ?></h6>
          <p class="small text-white-50 mb-2">Colección: <?php echo htmlspecialchars($producto['tipo']); ?></p>
          <p class="h4 fw-bold text-warning mb-3">
            Desde $<?php echo htmlspecialchars($producto['precio']); ?>
            <small class="text-white-50">m²</small>
          </p>
        </div>

        <div class="badges-bottom">
          <span class="badge bg-success">Ventas <?php echo htmlspecialchars($producto['porcetanjeVenta']); ?>%</span>
          <span class="badge bg-light text-dark">
            <?php
            if ($producto['cantidadActual'] == 0) {
              echo "No hay Stock";
            } else {
              echo htmlspecialchars($producto['cantidadActual']) . " Piezas";
            }
            ?>
          </span>
        </div>
      </div>
    </div>
<?php
  }
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pisos del Caribe - Catálogo de pisos vinílicos</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- CSS personalizado -->
  <link rel="stylesheet" type="text/css" href="css/inventario.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg" style="background-color: #2C1F17;">
    <div class="container">
      <!-- Logo + Nombre -->
      <a class="navbar-brand d-flex align-items-center text-white" href="#">
        <img src="img/logo.png" alt="Logo" class="me-2 rounded" width="auto" height="80">
      </a>

      <!-- Toggler (menú hamburguesa blanco) -->
      <button class="navbar-toggler border border-light bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menú -->
      <div class="collapse navbar-collapse justify-content-between" id="menuPrincipal">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="Inventario.php">Inventario</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="ventas.php">Gestion de Ventas</a>
          </li>
        </ul>
        <!-- Botón Contacto (a la derecha del menú, dentro del collapse) -->
        <div class="text-lg-end mt-3 mt-lg-0">
          <?php mostrarBotonSesion(); ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- BUSCADOR -->
  <section class="py-4 bg-white">
    <div class="container-fluid">
      <div class="row justify-content-end">
        <div class="col-12 col-md-6 col-lg-4">
          <form action="" method="POST">
            <div class="input-group input-group-lg shadow-sm">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text" id="searchInput" name="busqueda" class="form-control border-start-0 ps-0" placeholder="Busca por nombre o código...">
              <button type="submit" class="btn btn-warning d-none d-sm-flex">Buscar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- ESTADÍSTICAS -->
  <section class="py-5 stats text-center">
    <div class="container py-5">
      <div class="row g-5">
        <div class="col-6 col-md-3">
          <h3 class="display-5 fw-bold mb-0"><?php cantidad($mysqli); ?></h3>
          <p class="lead mb-0 fs-5 fw-semibold">Pisos disponibles</p>
        </div>
        <div class="col-6 col-md-3">
          <h3 class="display-5 fw-bold mb-0 text-danger"><?php SinStock($mysqli); ?></h3>
          <p class="lead mb-0 fs-5 fw-semibold">Pisos sin stock</p>
        </div>
        <div class="col-6 col-md-3">
          <h3 class="display-5 fw-bold mb-0">23,572 m²</h3>
          <p class="lead mb-0 fs-5 fw-semibold">Vendidos</p>
        </div>
        <div class="col-6 col-md-3">
          <h3 class="display-5 fw-bold mb-0">32</h3>
          <p class="lead mb-0 fs-5 fw-semibold">Instalaciones pendientes</p>
        </div>
      </div>
    </div>
  </section>

  <!-- BOTÓN FILTROS MÓVIL -->
  <div class="container-fluid my-4 mobile-filter-btn d-lg-none">
    <button class="btn btn-warning btn-lg w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
      <i class="bi bi-funnel"></i> Filtros y ordenar
    </button>
  </div>

  <!-- CATÁLOGO -->
  <section class="py-5 bg-light">
    <div class="container-fluid">
      <div class="row">

        <!-- FILTROS DESKTOP -->
        <div class="col-lg-3 desktop-filters d-none d-lg-block">
          <div class="bg-white p-4 rounded shadow-sm sticky-top" style="top:100px;">
            <h5 class="fw-bold mb-4">Filtros</h5>

            <!-- Por tipo (colección) -->
            <div class="mb-4">
              <form method="POST" action="">
                <label class="form-label fw-semibold">Por tipo</label>
                <div class="form-check"><input class="form-check-input filter-type" type="checkbox" name="tipos[]" value="concrete"><label class="form-check-label">Concrete</label></div>
                <div class="form-check"><input class="form-check-input filter-type" type="checkbox" name="tipos[]" value="forest"><label class="form-check-label">Forest</label></div>
                <div class="form-check"><input class="form-check-input filter-type" type="checkbox" name="tipos[]" value="herringbone"><label class="form-check-label">Herringbone</label></div>
                <div class="form-check"><input class="form-check-input filter-type" type="checkbox" name="tipos[]" value="futura"><label class="form-check-label">Futura</label></div>
                <div class="form-check"><input class="form-check-input filter-type" type="checkbox" name="tipos[]" value="max"><label class="form-check-label">Max</label></div>
                <button type="submit" class="btn btn-warning">Filtrar por tipo</button>
              </form>
            </div>
            <!-- Por precio m² -->
            <div class="mb-4">
              <form method="POST" action="">
                <label class="form-label fw-semibold">Por precio m²</label>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="390"><label>$390</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="410"><label>$410</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="415"><label>$415</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="420"><label>$420</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="425"><label>$425</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="430"><label>$430</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="445"><label>$445</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="450"><label>$450</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="460"><label>$460</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="465"><label>$465</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="470"><label>$470</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="475"><label>$475</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="480"><label>$480</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="490"><label>$490</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="495"><label>$495</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="510"><label>$510</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="520"><label>$520</label></div>
                <div class="form-check"><input name="precios[]" class="form-check-input filter-price" type="checkbox" value="550"><label>$550</label></div>
                <button type="submit" class="btn btn-warning">Filtrar por precio</button>
              </form>
            </div>
          </div>
        </div>

        <!-- LISTADO PRODUCTOS -->
        <div class="col-lg-9">
          <form action="" method="POST">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
              <h2 class="fw-bold text-dark mb-0">Inventario de Pisos del Caribe</h2>
              <select id="sortSelect" name="orden" class="form-select w-auto shadow-sm" style="max-width:220px;" onchange="this.form.submit()">
                <option value="relevance">Relevancia</option>
                <option value="price-asc">Precio: menor a mayor</option>
                <option value="price-desc">Precio: mayor a menor</option>
                <option value="sales">Más vendidos</option>
                <option value="Stock-asc">Stock: menor a mayor</option>
                <option value="Stock-des">Stock: mayor a menor</option>
              </select>
            </div>
          </form>
          <!-- GRID DESKTOP -->
          <div class="row g-4 d-none d-lg-flex" id="desktopGrid">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

              if (!empty($_POST['busqueda'])) {
                buscarProducto($mysqli, $_POST['busqueda']);
              } else {
                $orden   = $_POST['orden'] ?? 'relevance';
                $tipos   = $_POST['tipos'] ?? [];
                $precios = $_POST['precios'] ?? [];
                // Mostrar productos filtrados
                mostrarProductosFiltrados($mysqli, $orden, $tipos, $precios);
              }
            } else {
              // Mostrar todos los productos por defecto
              mostrarProductosFiltrados($mysqli);
            }
            ?>
          </div>

          <!-- CAROUSEL MÓVIL -->
          <div class="d-lg-none">
            <div id="mobileCarousel" class="carousel slide">
              <div class="carousel-inner" id="mobileCarouselInner"></div>
              <button class="carousel-control-prev" type="button" data-bs-target="#mobileCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#mobileCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          </div>
        </div>

        </div>
      </div>
    </div>
  </section>

  <!-- OFFCANVAS FILTROS MÓVIL -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Filtros y ordenamiento</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="mb-4">
        <form action="" method="POST">
          <label class="form-label fw-semibold">Ordenar por</label>
          <select id="sortSelect" name="orden" class="form-select w-auto shadow-sm" style="max-width:220px;" onchange="this.form.submit()">
            <option value="relevance">Relevancia</option>
            <option value="price-asc">Precio: menor a mayor</option>
            <option value="price-desc">Precio: mayor a menor</option>
            <option value="sales">Más vendidos</option>
            <option value="Stock-asc">Stock: menor a mayor</option>
            <option value="Stock-des">Stock: mayor a menor</option>
          </select>
        </form>
      </div>
      <div class="mb-4">
        <form method="POST" action="">
          <label class="form-label fw-semibold">Por tipo</label>
          <div class="form-check"><input class="form-check-input filter-type" type="checkbox" value="concrete"><label>Concrete</label></div>
          <div class="form-check"><input class="form-check-input filter-type" type="checkbox" value="forest"><label>Forest</label></div>
          <div class="form-check"><input class="form-check-input filter-type" type="checkbox" value="herringbone"><label>Herringbone</label></div>
          <div class="form-check"><input class="form-check-input filter-type" type="checkbox" value="futura"><label>Futura</label></div>
          <div class="form-check"><input class="form-check-input filter-type" type="checkbox" value="max"><label>Max</label></div>
          <button type="submit" class="btn btn-warning">Filtrar por tipo</button>
        </form>
      </div>
      <div class="mb-4">
        <form method="POST" action="">
          <label class="form-label fw-semibold">Por precio m²</label>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="390"><label>$390</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="405"><label>$405</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="410"><label>$410</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="420"><label>$420</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="435"><label>$435</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="445"><label>$445</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="480"><label>$480</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="510"><label>$510</label></div>
          <div class="form-check"><input class="form-check-input filter-price" type="checkbox" value="550"><label>$550</label></div>
          <button type="submit" class="btn btn-warning">Filtrar por precio</button>
        </form>
      </div>
    </div>
  </div>
<footer>
  
</footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/inventario.js"></script>
  
</body>

</html>