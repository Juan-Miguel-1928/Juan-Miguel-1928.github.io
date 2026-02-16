<?php
session_start();
require "php/enlaze_base_de_datos.php";

function mostrarBotonSesion()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/cierre_de_sesion.php" class="btn btn-outline-danger btn-sm fw-bold px-4">Cerrar sesión</a>';
  } else {
    echo '<a href="login.html" class="btn btn-outline-warning btn-sm fw-bold px-4">Iniciar sesión</a>';
  }
}
function mostrarPerfil()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/perfil.php" class="btn btn-outline-warning btn-sm fw-bold px-5 m-4">Ver Perfil</a>';
  }
}
function carrito()
{
  if (isset($_SESSION['usuario'])) {
    echo '<li class="nav-item mx-2">
            <a class="nav-link text-white" href="carrito.php">Carrito</a>
          </li>';
  }
}


function mostrarCardProducto($mysqli, $id_producto)
{
  // Consultar datos del producto
  $sql = "SELECT p.nombreProducto, p.precio, p.imagenProducto, i.cantidadActual
            FROM proyectoWeb.productos p
            LEFT JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
            WHERE p.id_producto = ?";
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("i", $id_producto);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if (!$row) {
    echo "<div class='alert alert-danger'>Producto no encontrado.</div>";
    return;
  }

  $nombreProducto = $row['nombreProducto'];
  $precio         = $row['precio'];
  $imagen         = $row['imagenProducto'];
  $stock          = $row['cantidadActual'];

  $enCarrito = false;
  if (isset($_SESSION['uid'])) {
    $id_usuario = $_SESSION['uid'];

    // Obtener id_cliente
    $sqlCliente = "SELECT id_cliente FROM proyectoWeb.clientes WHERE id_usuario = ?";
    $stmtCliente = $mysqli->prepare($sqlCliente);
    $stmtCliente->bind_param("i", $id_usuario);
    $stmtCliente->execute();
    $resCliente = $stmtCliente->get_result();
    $cliente = $resCliente->fetch_assoc();

    if ($cliente) {
      $id_cliente = $cliente['id_cliente'];

      // Obtener id_carrito
      $sqlCarrito = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
      $stmtCarrito = $mysqli->prepare($sqlCarrito);
      $stmtCarrito->bind_param("i", $id_cliente);
      $stmtCarrito->execute();
      $resCarrito = $stmtCarrito->get_result();
      $carrito = $resCarrito->fetch_assoc();

      if ($carrito) {
        $id_carrito = $carrito['id_carrito'];

        // Verificar si el producto ya está en carrito_detalle
        $sqlCheck = "SELECT 1 FROM proyectoWeb.carrito_detalle WHERE id_carrito = ? AND id_producto = ?";
        $stmtCheck = $mysqli->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id_carrito, $id_producto);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows > 0) {
          $enCarrito = true;
        }
      }
    }
  }
?>

  <div class="product-card">
    <div class="product-img">
      <img src="<?= $imagen ?>" style="width: 100%;">
    </div>
    <div class="product-info text-center">
      <p class="product-name"><?= $nombreProducto ?></p>
      <p class="product-price">$<?= $precio ?> m²</p>
      <?php if ($stock <= 0) { ?>
        <!-- Mostrar alerta si no hay stock -->
        <div class="alert alert-danger mt-2 mb-0 d-flex align-items-center justify-content-center"
          role="alert" style="height: 30px;">
          Sin stock
        </div>
      <?php } elseif ($enCarrito) { ?>
        <!-- Mostrar alerta si ya está en el carrito -->
        <div class="alert  mt-2 mb-0 d-flex align-items-center justify-content-center"
          role="alert" style="height: 30px; background: green;">
          Agregado
        </div>
      <?php } else { ?>
        <!-- Mostrar botón si hay stock y no está en carrito -->
        <form method="POST" action="">
          <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
          <button type="submit" name="agregar_carrito" class="btn btn-warning btn-sm">
            Agregar al Carrito
          </button>
        </form>
      <?php } ?>
    </div>
  </div>
<?php
}

if (isset($_POST['agregar_carrito'])) {
  // Validar sesión SOLO al pulsar el botón
  if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "cliente") {
    echo "<script>alert('Necesitas una sesión activa.'); window.location='login.html';</script>";
    exit();
  }

  $id_producto = $_POST['id_producto'];
  $cantidad    = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

  // Obtener id_cliente a partir de id_usuario guardado en sesión
  $id_usuario = $_SESSION['uid'];
  $sqlCliente = "SELECT id_cliente FROM proyectoWeb.clientes WHERE id_usuario = ?";
  $stmtCliente = $mysqli->prepare($sqlCliente);
  $stmtCliente->bind_param("i", $id_usuario);
  $stmtCliente->execute();
  $resCliente = $stmtCliente->get_result();
  $cliente = $resCliente->fetch_assoc();

  if (!$cliente) {
    echo "<script>alert('No se encontró cliente asociado.');</script>";
    return;
  }

  $id_cliente = $cliente['id_cliente'];

  $sqlProd = "SELECT p.precio, i.cantidadActual 
                FROM proyectoWeb.productos p
                JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
                WHERE p.id_producto = ?";
  $stmtProd = $mysqli->prepare($sqlProd);
  $stmtProd->bind_param("i", $id_producto);
  $stmtProd->execute();
  $resProd = $stmtProd->get_result();
  $producto = $resProd->fetch_assoc();

  if (!$producto) {
    echo "<script>alert('Producto no encontrado.');</script>";
    return;
  }

  $precioUnitario  = $producto['precio'];
  $stockDisponible = $producto['cantidadActual'];

  if ($stockDisponible < $cantidad) {
    echo "<script>alert('No hay suficiente stock disponible.'); window.location='catalogo.php';</script>";
    return;
  }

  // Obtener carrito del cliente
  $sqlCarrito = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
  $stmtCarrito = $mysqli->prepare($sqlCarrito);
  $stmtCarrito->bind_param("i", $id_cliente);
  $stmtCarrito->execute();
  $resCarrito = $stmtCarrito->get_result();
  $carrito = $resCarrito->fetch_assoc();

  if (!$carrito) {
    echo "<script>alert('No se encontró carrito para este cliente.');</script>";
    return;
  }

  $id_carrito = $carrito['id_carrito'];

  $sqlCheck = "SELECT id_carrito_detalle, cantidad FROM proyectoWeb.carrito_detalle 
                 WHERE id_carrito = ? AND id_producto = ?";
  $stmtCheck = $mysqli->prepare($sqlCheck);
  $stmtCheck->bind_param("ii", $id_carrito, $id_producto);
  $stmtCheck->execute();
  $resCheck = $stmtCheck->get_result();
  $existe = $resCheck->fetch_assoc();

  if ($existe) {
    $nuevaCantidad = $existe['cantidad'] + $cantidad;

    if ($nuevaCantidad > $stockDisponible) {
      echo "<script>alert('No puedes agregar más de lo disponible en stock.');</script>";
      return;
    }

    $nuevoSubtotal = $nuevaCantidad * $precioUnitario;

    $sqlUpdate = "UPDATE proyectoWeb.carrito_detalle 
                      SET cantidad = ?, subtotal = ? 
                      WHERE id_carrito_detalle = ?";
    $stmtUpdate = $mysqli->prepare($sqlUpdate);
    $stmtUpdate->bind_param("idi", $nuevaCantidad, $nuevoSubtotal, $existe['id_carrito_detalle']);
    $stmtUpdate->execute();
  } else {
    $subtotal = $precioUnitario * $cantidad;
    $sqlInsert = "INSERT INTO proyectoWeb.carrito_detalle (cantidad, precioUnitario, subtotal, id_carrito, id_producto)
                      VALUES (?, ?, ?, ?, ?)";
    $stmtInsert = $mysqli->prepare($sqlInsert);
    $stmtInsert->bind_param("idiii", $cantidad, $precioUnitario, $subtotal, $id_carrito, $id_producto);
    $stmtInsert->execute();
  }

  echo "<script>alert('Producto agregado al carrito');</script>";
}
$mensaje = "";
if (isset($_POST['nombre']) &&  isset($_POST['apellidos']) &&  isset($_POST['correo']) &&  isset($_POST['telefono']) &&  isset($_POST['mensaje'])) {
  $nombre = $_POST['nombre'];
  $apellidos = $_POST['apellidos'];
  $email = $_POST['correo'];
  $telefono = $_POST['telefono'];
  $mensajeUsuario = $_POST['mensaje'];
  $stmt = $mysqli->prepare("INSERT INTO contacto (nombre, apellidos, correo, telefono, mensaje) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $nombre, $apellidos, $email, $telefono, $mensajeUsuario);

  if ($stmt->execute()) {
    $mensaje = "Mensaje enviado correctamente ";
  } else {
    $mensaje = "Error al registrar mensaje " . $stmt->error;
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pisos del Caribe - Catalogo de pisos vinilicos</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!--CSS personalizado (solo lo que NO se puede con Bootstrap) -->
  <link rel="stylesheet" type="text/css" href="css/catalogo.css">

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
            <a class="nav-link text-white" href="home.php">Inicio</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="sobrenosotros.php">Acerca de nosotros</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="catalogo.php">Catálogo</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="cotiza.php">Cotiza</a>
          </li>
          <?php carrito(); ?>
        </ul>

        <!-- Botón Contacto (a la derecha del menú, dentro del collapse) -->
         <div class="text-lg-end mt-3 mt-lg-0">
          <?php mostrarPerfil(); ?> 
        </div>
        <div class="text-lg-end mt-3 mt-lg-0">
          <?php mostrarBotonSesion(); ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- BANNER HERO (con Bootstrap Tabs - SIN JS) -->
  <section class="catalog-hero">
    <div class="container-fluid">
      <h1 class="logo-title">PISOS VINÍLICOS</h1>

      <!-- NAVEGACIÓN DE COLECCIONES (Bootstrap Tabs) -->
      <ul class="nav nav-pills justify-content-center collections-nav mb-5" id="catalogTab" role="tablist">
        <li class="nav-item col-item" role="presentation">
          <a class="nav-link active text-light" id="concrete-tab" data-bs-toggle="pill" data-bs-target="#concrete" type="button" role="tab">CONCRETE</a>
        </li>
        <li class="nav-item col-item" role="presentation">
          <a class="nav-link text-light" id="forest-tab" data-bs-toggle="pill" data-bs-target="#forest" type="button" role="tab">FOREST</a>
        </li>
        <li class="nav-item col-item" role="presentation">
          <a class="nav-link text-light" id="herringbone-tab" data-bs-toggle="pill" data-bs-target="#herringbone" type="button" role="tab">HERRINGBONE</a>
        </li>
        <li class="nav-item col-item" role="presentation">
          <a class="nav-link text-light" id="futura-tab" data-bs-toggle="pill" data-bs-target="#futura" type="button" role="tab">FUTURA</a>
        </li>
        <li class="nav-item col-item" role="presentation">
          <a class="nav-link text-light" id="max-tab" data-bs-toggle="pill" data-bs-target="#max" type="button" role="tab">MAX</a>
        </li>
      </ul>

      <!-- CONTENIDO DINÁMICO (Tab Panes) -->
      <div class="tab-content">
        <!-- CONCRETE -->
        <div class="tab-pane fade" id="concrete" role="tabpanel">
          <h3 class="text-center mb-4">Coleccion CONCRETE</h3>
          <div class="product-grid">
            <?php mostrarCardProducto($mysqli, 1); ?>
            <?php mostrarCardProducto($mysqli, 2); ?>
            <?php mostrarCardProducto($mysqli, 3); ?>
            <?php mostrarCardProducto($mysqli, 4); ?>
          </div>
        </div>

        <!-- FOREST (activa por defecto) -->
        <div class="tab-pane fade show active" id="forest" role="tabpanel">
          <h3 class="text-center mb-4">Colección FOREST</h3>
          <div class="product-grid">
            <?php mostrarCardProducto($mysqli, 5); ?>
            <?php mostrarCardProducto($mysqli, 6); ?>
            <?php mostrarCardProducto($mysqli, 7); ?>
            <?php mostrarCardProducto($mysqli, 8); ?>
            <?php mostrarCardProducto($mysqli, 9); ?>

          </div>
        </div>

        <!-- HERRINGBONE -->
        <div class="tab-pane fade" id="herringbone" role="tabpanel">
          <h3 class="text-center mb-4">Colección HERRINGBONE</h3>
          <div class="product-grid">
            <?php mostrarCardProducto($mysqli, 10); ?>
            <?php mostrarCardProducto($mysqli, 11); ?>
            <?php mostrarCardProducto($mysqli, 12); ?>
            <?php mostrarCardProducto($mysqli, 13); ?>
          </div>
        </div>

        <!-- FUTURA -->
        <div class="tab-pane fade" id="futura" role="tabpanel">
          <h3 class="text-center mb-4">Colección FUTURA</h3>
          <div class="product-grid">
            <?php mostrarCardProducto($mysqli, 14); ?>
            <?php mostrarCardProducto($mysqli, 15); ?>
            <?php mostrarCardProducto($mysqli, 16); ?>
            <?php mostrarCardProducto($mysqli, 17); ?>
          </div>
        </div>

        <!-- MAX -->
        <div class="tab-pane fade" id="max" role="tabpanel">
          <h3 class="text-center mb-4">Colección MAX</h3>
          <div class="product-grid">
            <?php mostrarCardProducto($mysqli, 18); ?>
            <?php mostrarCardProducto($mysqli, 19); ?>
            <?php mostrarCardProducto($mysqli, 20); ?>
            <?php mostrarCardProducto($mysqli, 21); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--IR A REVIEWS -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center flex-column-reverse flex-lg-row text-center text-lg-start">

        <!-- Texto (se centra en móvil) -->
        <div class="col-lg-6 mt-4 mt-lg-0">
          <h3 class="fw-bold">Conozca nuestros trabajos</h3>
          <p class="text-muted">
            Explore la experiencias de otros clientes
          </p>
        </div>
        <div class="col-lg-6">
          <img src="img/variedad.png" class="img-fluid rounded-5 shadow" alt="Variedad de pisos vinílicos">
        </div>
      </div>
    </div>
  </section>

  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold">Colecciones Más Populares</h2>

      <div id="coleccionesSlider" class="carousel slide mobile-single">
        <div class="carousel-inner">
          <?php
          $sql = "SELECT p.id_producto, p.nombreProducto, p.precio, p.imagenProducto, i.cantidadActual
          FROM proyectoWeb.productos p
          LEFT JOIN proyectoWeb.inventario i ON p.id_producto = i.id_producto
          WHERE p.id_producto IN (1,8,3)";
          $result = $mysqli->query($sql);
          $active = "active";
          while ($row = $result->fetch_assoc()) {
            $id_producto = $row['id_producto'];
            $nombre      = $row['nombreProducto'];
            $precio      = $row['precio'];
            $imagen      = $row['imagenProducto'];
            $stock       = $row['cantidadActual'];
          ?>
            <div class="carousel-item <?= $active ?>">
              <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                  <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <img src="<?= $imagen ?>" class="card-img-top" alt="<?= $nombre ?>">
                    <div class="card-body text-center bg-white p-4">
                      <h5 class="card-title mb-3"><?= $nombre ?></h5>
                      <button
                        class="btn btn-warning ver-producto"
                        data-bs-toggle="modal"
                        data-bs-target="#modalProducto"
                        data-producto="<?= $nombre ?>"
                        data-imagen="<?= $imagen ?>"
                        data-precio="<?= $precio ?>"
                        data-stock="<?= $stock ?>">
                        Ver piso
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
            $active = "";
          }
          ?>
        </div>

        <!-- FLECHAS -->
        <button class="carousel-control-prev" type="button" data-bs-target="#coleccionesSlider" data-bs-slide="prev">
          <i class="bi bi-chevron-left fs-1 text-warning"></i>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#coleccionesSlider" data-bs-slide="next">
          <i class="bi bi-chevron-right fs-1 text-warning"></i>
        </button>
      </div>
    </div>
  </section>

  <!-- MODAL -->
  <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background: #2C1F17; color: white;">
          <h5 class="modal-title" id="modalProductoLabel">Detalle del Producto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center d-flex row justify-content-center">
          <h4 id="productoModal" class="fw-bold mb-3"></h4>
          <img id="imagenModal" src="" alt="Producto" class="img-fluid rounded mb-3">
          <p><strong>Precio:</strong> $<span id="precioModal"></span></p>
          <p><strong>Cantidad Disponible:</strong> <span id="stockModal"></span></p>
          <button type="button" class="btn btn-warning btn-sm" style="width: 150px;" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- PREGUNTAS FRECUENTES-->
  <section class="container my-5">
    <h3 class="text-center fw-bold mb-4">Preguntas frecuentes</h3>
    <div class="accordion" id="faqAccordion">

      <!-- Pregunta 1 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
          <p><strong>¿Qué clasificación real tienen los pisos (residencial o comercial)?</strong></p>
        </button>
        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>Clase 33 (uso pesado):</strong>
            <ul>
              <li>Colecciones <em>Concrete, Max</em>: Ideal para casas y oficinas</li>
              <li>Colecciones <em>Forest, Futura</em>: Hasta tráfico comercial</li>
            </ul>
            <em>Certificado AC4 – resiste +10,000 ciclos.</em></p>
          </div>
        </div>
      </div>

      <!-- Pregunta 2 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
          <p><strong>¿Cuál es el costo real por m² instalado?</strong></p>
        </button>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>$550 – $850 m² instalado:</strong>
            <ul>
              <li><strong>SPC (Concrete):</strong> $650 m²</li>
              <li><strong>LVT (Forest):</strong> $550 m²</li>
            </ul>
            <em>Incluye material + mano de obra + limpieza. Sin demolición.</em></p>
          </div>
        </div>
      </div>

      <!-- Pregunta 3 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
          <p><strong>¿Cuánto cuesta el envío real a mi ciudad?</strong></p>
        </button>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>Gratis desde 30 m²:</strong>
            <ul>
              <li><strong>CDMX / Edo. Mex:</strong> 1 día – $0</li>
              <li><strong>Guadalajara / Monterrey:</strong> 2-3 días – $0</li>
              <li><strong>Otras ciudades:</strong> $150 por caja (máx 5)</li>
            </ul>
            <em>Entrega puerta a puerta con FedEx.</em></p>
          </div>
        </div>
      </div>

      <!-- Pregunta 4-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
          <p><strong>¿Qué dicen clientes de estas colecciones?</strong></p>
        </button>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>+1,200 instalaciones en CDMX:</strong>
            <ul>
              <li><em>"Concrete en mi depa: 0 rayones en 2 años" – Ana, Polanco</em></li>
              <li><em>"Forest en oficina: fácil limpieza" – Luis, Reforma</em></li>
            </ul>
            <em>Ver reviews con fotos en cada producto.</em></p>
          </div>
        </div>
      </div>

      <!-- Pregunta 5-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
          <p><strong>¿Los precios del catálogo incluyen IVA?</p></strong>
        </button>
        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>Sí, todo incluye IVA.</strong>Precio final = lo que ves en el catálogo.
              <em>Factura electrónica al instante.</em>
            <p>
          </div>
        </div>
      </div>

      <!-- Pregunta 6-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
          <p><strong>¿Qué los diferencia de otros catálogos?</strong></p>
        </button>
        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p><strong>3 garantías únicas:</strong>
            <ul>
              <li><strong>15 años real</strong> (no solo papel)</li>
              <li><strong>Muestras gratis a domicilio</strong> (Cancun 24h)</li>
              <li><strong>Instalación en 48h o te devolvemos $500</strong></li>
            </ul>
            <em>Nadie más lo ofrece.</em></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer text-white py-5">
    <div class="container">
      <div class="row align-items-start">

        <!-- Columna 1: Logo y redes -->
        <div class="col-md-4 mb-4">
          <div class="d-flex align-items-center mb-3">
            <div>
              <img src="img/logo.png" alt="Logo" class="me-2 rounded" width="auto" height="80">
            </div>
          </div>
          <p class="fw-semibold mb-1 fs-5">Síguenos en nuestras redes sociales:</p>
          <p class="small">Cada espacio cuenta una historia. Le invitamos a unirse a nuestra comunidad y descubrir cómo lograr ambientes únicos, cómodos y con estilo.</p>

          <div class="d-flex gap-5 fs-2 justify-content-center">
            <a href="#instagram" class="text-white">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#facebook" class="text-white">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#whatsapp" class="text-white">
              <i class="bi bi-whatsapp"></i>
            </a>
            <a href="#twitter" class="text-white">
              <i class="bi bi-twitter"></i>
            </a>
          </div>
          <small class="form-text text-white text-center">2025 Pisos del caribe. Todos los derechos reservados.
            <br>
            <a  id="avisoPrivacidad" class="text-primary">Aviso de privacidad</a>
          </small>
        </div>

        <!-- Columna 2: Formulario -->
        <div class="col-md-4 mb-4">
          <div class="p-4 text-dark rounded form-box">
            <h5 class="fw-bold text-center mb-3 text-light">Contáctanos</h5>
            <form action="" method="POST">
              <?php if (!empty($mensaje)) { ?>
                <div class="alert alert-warning text-center " role="alert">
                  <?php echo htmlspecialchars($mensaje); ?>
                </div>
              <?php } ?>
              <div class="row g-2 mb-2">
                <div class="col">
                  <input type="text" class="form-control bg-light" placeholder="nombre" name="nombre" required>
                </div>
                <div class="col">
                  <input type="text" class="form-control" placeholder="apellidos" name="apellidos" required>
                </div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col">
                  <input type="correo" class="form-control" placeholder="ejemplo@gmail.com" name="correo" required>
                </div>
                <div class="col">
                  <input type="text" class="form-control" placeholder="telefono" inputmode="numeric" pattern="[0-9]*" title="Solo números" name="telefono" required>
                </div>
              </div>
              <div class="mb-3">
                <textarea type="text" class="form-control" rows="3" placeholder="mensaje" name="mensaje" required></textarea>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-warning fw-semibold px-4" onclick="return confirm('¿Desea enviar el mensaje?');">Enviar</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Columna 3: Mapa -->
        <div class="col-md-4 mb-4">
          <iframe
            class="w-100 rounded"
            height="280"
            style="border:0; color: black;"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.1414214843177!2d-99.16766232566762!3d19.404643186892417!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff36b93f09d1%3A0xcbb6d9f9b35c0cc!2sCDMX!5e0!3m2!1ses!2smx!4v1700000000000!5m2!1ses!2smx">
          </iframe>
        </div>
      </div>
    </div>
  </footer>
  <!-- Bootstrap JS (solo para funcionalidad básica) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.ver-producto').forEach(btn => {
      btn.addEventListener('click', function() {
        const producto = this.getAttribute('data-producto');
        const imagen = this.getAttribute('data-imagen');
        const precio = parseFloat(this.getAttribute('data-precio')).toLocaleString('es-MX', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        const stock = this.getAttribute('data-stock');

        document.getElementById('productoModal').textContent = producto;
        document.getElementById('imagenModal').src = imagen;
        document.getElementById('precioModal').textContent = precio;
        document.getElementById('stockModal').textContent = stock;
      });
    });
  </script>

  <script>
    const avisoPrivacidad = document.getElementById("avisoPrivacidad");
    avisoPrivacidad.addEventListener("click", function(event) {
      event.preventDefault();

      alert("Pisos del Caribe es responsable del uso y protección de sus datos personales\n" +
        "Los datos que recabamos, tales como nombre completo, número telefónico, correo electrónico y datos relacionados con compras o consultas, serán utilizados únicamente para brindar información sobre productos, procesar pedidos, gestionar entregas y ofrecer atención al cliente.\n" +
        "Le informamos que sus datos personales no serán compartidos con terceros sin\n" +
        "su consentimiento, salvo por requerimientos legales o para cumplir servicios necesarios\n" +
        "con proveedores, como empresas de mensajería o plataformas de pago.\n" +
        "Pisos del Caribe se reserva el derecho de realizar modificaciones o actualizaciones al presente aviso en\n" +
        "cualquier momento. Cualquier cambio será publicado en nuestro sitio web oficial");
    })
  </script>
</body>

</html>