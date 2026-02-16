<?php
session_start();
require "php/enlaze_base_de_datos.php";

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "cliente") {
  header("Location: login.html");
  exit();
}
function mostrarPerfil()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/perfil.php" class="btn btn-outline-warning btn-sm fw-bold px-5 m-4">Ver Perfil</a>';
  }
}

$id_cliente = $_SESSION['uid'];
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

if (isset($_POST['eliminar_producto']) && isset($_SESSION['uid'])) {
  $id_producto = intval($_POST['eliminar_producto']);
  $id_usuario  = $_SESSION['uid'];

  // Obtener id_cliente desde id_usuario
  $sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
  $stmtCliente = $mysqli->prepare($sqlCliente);
  $stmtCliente->bind_param("i", $id_usuario);
  $stmtCliente->execute();
  $resCliente = $stmtCliente->get_result();
  $cliente = $resCliente->fetch_assoc();

  if ($cliente) {
    $id_cliente = $cliente['id_cliente'];

    // Obtener ID del carrito del cliente
    $sql = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $id_carrito = $result->fetch_assoc()['id_carrito'];

      // Eliminar el producto de carrito_detalle
      $sqlDel = "DELETE FROM proyectoWeb.carrito_detalle 
                       WHERE id_carrito = ? AND id_producto = ?";
      $stmtDel = $mysqli->prepare($sqlDel);
      $stmtDel->bind_param("ii", $id_carrito, $id_producto);
      $stmtDel->execute();
    }
  }

  // Recargar para que se vea el cambio
  header("Location: carrito.php");
  exit();
}

if (isset($_POST['actualizar_producto'])) {

  $id_producto     = intval($_POST['actualizar_producto']);
  $cantidad_actual = intval($_POST['nueva_cantidad']);

  // Obtener id_cliente REAL desde id_usuario
  $id_usuario = $_SESSION['uid'];

  $sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
  $stmtCliente = $mysqli->prepare($sqlCliente);
  $stmtCliente->bind_param("i", $id_usuario);
  $stmtCliente->execute();
  $resCliente = $stmtCliente->get_result();
  $cliente = $resCliente->fetch_assoc();

  if (!$cliente) {
    header("Location: carrito.php");
    exit();
  }

  $id_cliente = $cliente['id_cliente'];

  // Obtener carrito
  $sqlCarrito = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
  $stmtCarrito = $mysqli->prepare($sqlCarrito);
  $stmtCarrito->bind_param("i", $id_cliente);
  $stmtCarrito->execute();
  $resCarrito = $stmtCarrito->get_result();

  if ($resCarrito->num_rows > 0) {

    $id_carrito = $resCarrito->fetch_assoc()['id_carrito'];

    // Actualizar cantidad y subtotal
    $sqlUpd = "UPDATE proyectoWeb.carrito_detalle 
               SET cantidad = ?, subtotal = (SELECT precio FROM proyectoWeb.productos WHERE id_producto = ?) * ?
               WHERE id_carrito = ? AND id_producto = ?";

    $stmtUpd = $mysqli->prepare($sqlUpd);
    $stmtUpd->bind_param("iiiii", $cantidad_actual, $id_producto, $cantidad_actual, $id_carrito, $id_producto);
    $stmtUpd->execute();
  }

  header("Location: carrito.php");
  exit();
}

function mostrarBotonSesion()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/cierre_de_sesion.php" class="btn btn-outline-danger btn-sm fw-bold px-4">Cerrar sesión</a>';
  } else {
    echo '<a href="login.html" class="btn btn-outline-warning btn-sm fw-bold px-4">Iniciar sesión</a>';
  }
}

if (isset($_POST['proceder_pago'])) {
  $id_usuario = $_SESSION['uid'];
  $id_admin   = 1; // o el admin que esté logueado

  // Obtener id_cliente
  $sqlCliente = "SELECT id_cliente FROM proyectoWeb.clientes WHERE id_usuario = ?";
  $stmtCliente = $mysqli->prepare($sqlCliente);
  $stmtCliente->bind_param("i", $id_usuario);
  $stmtCliente->execute();
  $resCliente = $stmtCliente->get_result();
  $cliente = $resCliente->fetch_assoc();

  if ($cliente) {
    $id_cliente = $cliente['id_cliente'];

    // Obtener carrito
    $sqlCarrito = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
    $stmtCarrito = $mysqli->prepare($sqlCarrito);
    $stmtCarrito->bind_param("i", $id_cliente);
    $stmtCarrito->execute();
    $resCarrito = $stmtCarrito->get_result();

    if ($resCarrito->num_rows > 0) {
      $id_carrito = $resCarrito->fetch_assoc()['id_carrito'];

      // Obtener productos del carrito
      $sqlDetalle = "SELECT cd.id_producto, cd.cantidad, cd.precioUnitario, cd.subtotal
                           FROM proyectoWeb.carrito_detalle cd
                           WHERE cd.id_carrito = ?";
      $stmtDetalle = $mysqli->prepare($sqlDetalle);
      $stmtDetalle->bind_param("i", $id_carrito);
      $stmtDetalle->execute();
      $resDetalle = $stmtDetalle->get_result();

      // Crear venta
      $sqlVenta = "INSERT INTO proyectoWeb.venta (fechaVenta, totalVenta, id_cliente, id_admin)
                         VALUES (NOW(), 0, ?, ?)";
      $stmtVenta = $mysqli->prepare($sqlVenta);
      $stmtVenta->bind_param("ii", $id_cliente, $id_admin);
      $stmtVenta->execute();
      $id_venta = $stmtVenta->insert_id;

      // Crear compra
      $sqlCompra = "INSERT INTO proyectoWeb.compra (fechaCompra, totalCompra, id_admin)
                          VALUES (NOW(), 0, ?)";
      $stmtCompra = $mysqli->prepare($sqlCompra);
      $stmtCompra->bind_param("i", $id_admin);
      $stmtCompra->execute();
      $id_compra = $stmtCompra->insert_id;

      $totalVenta  = 0;
      $totalCompra = 0;

      while ($row = $resDetalle->fetch_assoc()) {
        $id_producto = $row['id_producto'];
        $cantidad    = $row['cantidad'];
        $precio      = $row['precioUnitario'];
        $subtotal    = $row['subtotal'];

        $totalVenta  += $subtotal;
        $totalCompra += $subtotal;

        // Insertar detalle de compra
        $sqlCompraDet = "INSERT INTO proyectoWeb.compra_detalle 
                                 (cantidad, costoUnitario, subtotal, id_compra, id_producto)
                                 VALUES (?, ?, ?, ?, ?)";
        $stmtCompraDet = $mysqli->prepare($sqlCompraDet);
        $stmtCompraDet->bind_param("idiii", $cantidad, $precio, $subtotal, $id_compra, $id_producto);
        $stmtCompraDet->execute();

        $sqlVentaDet = "INSERT INTO proyectoWeb.venta_detalle
                                (cantidad, precioUnitario, subtotal, id_venta, id_producto)
                                VALUES (?, ?, ?, ?, ?)";
        $stmtVentaDet = $mysqli->prepare($sqlVentaDet);
        $stmtVentaDet->bind_param("idiii", $cantidad, $precio, $subtotal, $id_venta, $id_producto);
        $stmtVentaDet->execute();


        // Actualizar inventario
        $sqlInv = "UPDATE proyectoWeb.inventario 
                           SET cantidadActual = cantidadActual - ?, ultimaActualizacion = NOW()
                           WHERE id_producto = ?";
        $stmtInv = $mysqli->prepare($sqlInv);
        $stmtInv->bind_param("ii", $cantidad, $id_producto);
        $stmtInv->execute();
      }

      // Actualizar totales
      $sqlUpdVenta = "UPDATE proyectoWeb.venta SET totalVenta = ? WHERE id_venta = ?";
      $stmtUpdVenta = $mysqli->prepare($sqlUpdVenta);
      $stmtUpdVenta->bind_param("di", $totalVenta, $id_venta);
      $stmtUpdVenta->execute();

      $sqlUpdCompra = "UPDATE proyectoWeb.compra SET totalCompra = ? WHERE id_compra = ?";
      $stmtUpdCompra = $mysqli->prepare($sqlUpdCompra);
      $stmtUpdCompra->bind_param("di", $totalCompra, $id_compra);
      $stmtUpdCompra->execute();

      // Vaciar carrito
      $sqlVaciar = "DELETE FROM proyectoWeb.carrito_detalle WHERE id_carrito = ?";
      $stmtVaciar = $mysqli->prepare($sqlVaciar);
      $stmtVaciar->bind_param("i", $id_carrito);
      $stmtVaciar->execute();

      echo "<script>alert('Venta y compra registradas con éxito. Total: $" . number_format($totalVenta, 2) . "'); window.location='catalogo.php';</script>";
    }
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

function verCarrito($mysqli)
{
  if (!isset($_SESSION['uid'])) {
    echo "<p>No hay cliente en sesión.</p>";
    return;
  }

  // PASO 1: obtener id_cliente desde id_usuario
  $id_usuario = $_SESSION['uid'];

  $q = $mysqli->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
  $q->bind_param("i", $id_usuario);
  $q->execute();
  $r = $q->get_result();
  $cliente = $r->fetch_assoc();

  if (!$cliente) {
    echo "<p>Error: el usuario no tiene cliente asociado.</p>";
    return;
  }

  $id_cliente = $cliente['id_cliente'];

  // PASO 2: consultar carrito
  $sql = "SELECT p.id_producto, p.nombreProducto, p.precio, cd.cantidad, cd.subtotal, p.imagenProducto, i.cantidadActual
          FROM carrito_detalle cd
          JOIN carrito c ON cd.id_carrito = c.id_carrito
          JOIN productos p ON cd.id_producto = p.id_producto
          JOIN inventario i ON p.id_producto = i.id_producto
          WHERE c.id_cliente = ?";

  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("i", $id_cliente);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    echo '<p class="text-center mt-5" style="font-size: 32px; font-weight: bold; ">Tu carrito está vacío.</p>';
    return;
  }

  $productos = $result->fetch_all(MYSQLI_ASSOC);

  foreach ($productos as $row) { ?>
    <div class="card mb-3 productos p-2">
      <div class="row g-0 align-items-start">
        <div class="col-12 col-md-4">
          <div class="imagenilustrativa bg-light">
            <img src="<?= htmlspecialchars($row['imagenProducto']) ?>" width="auto" height="150px">
          </div>
        </div>
        <div class="col-md-8">
          <div class="card-body">
            <h5 class="mb-2"><?= htmlspecialchars($row['nombreProducto']) ?></h5>
            <span class="sku">SKU: <?= $row['id_producto'] ?></span><br>
            <span class="sku">Entrega Gratis el 20 de Diciembre al terminar la compra</span><br>
            <span class="sku"><?= $row['cantidad'] ?> Piezas</span>
            <div class="d-flex precioscar justify-content-between py-2">
              <span>Precio: $<?= number_format($row['precio'], 2) ?></span>
              <span>Ahorro: $0</span>
              <span>Subtotal: $<?= number_format($row['subtotal'], 2) ?></span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex justify-content-start align-items-center">

                <!-- Formulario actualizar -->
                <form method="POST" action="" style="display:inline-flex; align-items:center;">
                  <input type="hidden" name="actualizar_producto" value="<?= $row['id_producto'] ?>">
                  <input type="hidden" id="cantidad_<?= $row['id_producto'] ?>" name="nueva_cantidad" value="<?= $row['cantidad'] ?>">

                  <div class="contador">
                    <button type="button" class="anadir restar" onclick="cambiarCantidad(<?= $row['id_producto'] ?>, -1)">-</button>
                    <span id="cantidad_texto_<?= $row['id_producto'] ?>"
                      class="cantidad text-center mb-0"
                      data-stock="<?= $row['cantidadActual'] ?>">
                      <?= $row['cantidad'] ?>
                    </span>
                    <button type="button" class="anadir sumar" onclick="cambiarCantidad(<?= $row['id_producto'] ?>, 1)">+</button>
                  </div>

                  <button type="submit" class="btn btn-sm text-primary mt-2"
                    onclick="return confirm('¿Desea actualizar el producto?');">
                    Actualizar
                  </button>
                </form>

                <script>
                  function cambiarCantidad(idProducto, delta) {
                    const input = document.getElementById('cantidad_' + idProducto);
                    const span = document.getElementById('cantidad_texto_' + idProducto);
                    let cantidad = parseInt(input.value);
                    const stock = parseInt(span.dataset.stock); // stock máximo

                    // Limitar entre 1 y stock disponible
                    cantidad = Math.max(1, Math.min(stock, cantidad + delta));

                    input.value = cantidad;
                    span.textContent = cantidad;
                  }
                </script>

                <!-- Formulario eliminar -->
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="eliminar_producto" value="<?= $row['id_producto'] ?>">
                  <button type="submit" class="btn btn-sm text-danger mt-2"
                    onclick="return confirm('¿Desea eliminar el producto?');">
                    Eliminar
                  </button>
                </form>

              </div>
              <div>
                <span class="px-2"><?= $row['cantidadActual'] ?> disponibles</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php };
}

function obtenerTotalCarrito($mysqli) {
$id_cliente = $_SESSION['uid'];
    $sqlCliente = "SELECT id_cliente FROM proyectoWeb.clientes WHERE id_usuario = ?";
    $stmtCliente = $mysqli->prepare($sqlCliente);
    $stmtCliente->bind_param("i", $id_cliente);
    $stmtCliente->execute();
    $resCliente = $stmtCliente->get_result();
    $cliente = $resCliente->fetch_assoc();

    $id_cliente = $cliente['id_cliente'];

    // Obtener carrito del cliente
    $sqlCarrito = "SELECT id_carrito FROM proyectoWeb.carrito WHERE id_cliente = ?";
    $stmtCarrito = $mysqli->prepare($sqlCarrito);
    $stmtCarrito->bind_param("i", $id_cliente);
    $stmtCarrito->execute();
    $resCarrito = $stmtCarrito->get_result();
    $carrito = $resCarrito->fetch_assoc();

    if (!$carrito) {
        return 0; 
    }

    $id_carrito = $carrito['id_carrito'];

    // Calcular total del carrito
    $sqlTotal = "SELECT SUM(subtotal) AS totalCarrito 
                 FROM proyectoWeb.carrito_detalle 
                 WHERE id_carrito = ?";
    $stmtTotal = $mysqli->prepare($sqlTotal);
    $stmtTotal->bind_param("i", $id_carrito);
    $stmtTotal->execute();
    $resTotal = $stmtTotal->get_result();
    $row = $resTotal->fetch_assoc();

    echo $row['totalCarrito'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrito</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Tu CSS personalizado (solo lo que NO se puede con Bootstrap) -->
  <link rel="stylesheet" type="text/css" href="css/carrito.css">
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

  <!-- carrito -->
  <h1 class="text-center">Bienvenido a tu carrito</h1>
  <div class="container-fluid">
    <div class="row flex-lg-row-reverse justify-content-around m-4">
      <div class="col-12 col-lg-3 resumen p-4 mt-3">
        <h2>Resumen de compra</h2>
        <p>Subtotal : $<?php obtenerTotalCarrito($mysqli); ?></p>
        <p style="color: green;">Ahorro: $0</p>
        <h2>Total: $<?php obtenerTotalCarrito($mysqli); ?></h2>
        <form method="POST" action="">
          <button type="submit" name="proceder_pago" class="btn w-100 btn-warning"
            onclick="return confirm('¿Desea realizar la compra?');">
            Proceder Al Pago
          </button>
        </form>
      </div>
      <div class="col-12 col-lg-8 p-3 productos mt-3">
        <h2 class="text-center">Detalle del pedido</h2>
        <p class="text-center">precio y disponibilidad de invetario validos para esta ubicacion</p>
        <?php verCarrito($mysqli); ?>
      </div>
    </div>
  </div>

  <br>
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
          <small  class="form-text text-white text-center">2025 Pisos del caribe. Todos los derechos reservados.
            <br> 
            <a id="avisoPrivacidad" class="text-primary">Aviso de privacidad</a>
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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

<!-- descargar sql server-->