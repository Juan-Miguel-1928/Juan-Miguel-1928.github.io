<?php
session_start();
require "php/enlaze_base_de_datos.php";
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.html");
    exit();
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

function mostrarBotonSesion()
{
    if (isset($_SESSION['usuario'])) {
        echo '<a href="php/cierre_de_sesion.php" class="btn btn-outline-danger fw-bold px-4">Cerrar sesión</a>';
    } else {
        echo '<a href="login.html" class="btn btn-outline-warning fw-bold px-4">Iniciar sesión</a>';
    }
}
$where = [];
$params = [];
$types  = "";

if (!empty($_GET['folio'])) {
    $where[] = "v.id_venta = ?";
    $params[] = $_GET['folio'];
    $types   .= "i";
}
if (!empty($_GET['cliente'])) {
    $where[] = "ucli.nombre LIKE ?";
    $params[] = "%" . $_GET['cliente'] . "%";
    $types   .= "s";
}
if (!empty($_GET['telefono'])) {
    $where[] = "ucli.telefono LIKE ?";
    $params[] = "%" . $_GET['telefono'] . "%";
    $types   .= "s";
}
if (!empty($_GET['fecha'])) {
    $where[] = "v.fechaVenta = ?";
    $params[] = $_GET['fecha'];
    $types   .= "s";
}
$contarUsuario = $mysqli->query("SELECT COUNT(*) as id_admin FROM venta");
$resultadoContar = $contarUsuario->fetch_assoc();
$cantidad = $resultadoContar['id_admin'];
if ($cantidad > 0) {
    $sqlBase = "SELECT v.id_venta, v.fechaVenta, v.totalVenta, 
                   ucli.nombre AS cliente_nombre, ucli.correo AS cliente_correo, ucli.telefono AS cliente_telefono,
                   uadm.nombre AS admin_nombre, uadm.correo AS admin_correo, uadm.telefono AS admin_telefono
            FROM venta v 
            INNER JOIN clientes c ON v.id_cliente = c.id_cliente 
            INNER JOIN usuarios ucli ON c.id_usuario = ucli.id_usuario
            INNER JOIN administradores a ON v.id_admin = a.id_admin
            INNER JOIN usuarios uadm ON a.id_usuario = uadm.id_usuario";
    if ($where) {
        $sqlBase .= " WHERE " . implode(" AND ", $where);
    }
    $sqlBase .= " ORDER BY v.id_venta";

    $stmt = $mysqli->prepare($sqlBase);
    if ($where) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $cantidad = $result->num_rows;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/ventas.css">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg" style="background-color: #2C1F17;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-white" href="#">
                <img src="img/logo.png" alt="Logo" class="me-2 rounded" width="auto" height="80">
            </a>
            <button class="navbar-toggler border border-light bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="menuPrincipal">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-2"><a class="nav-link text-white" href="Inventario.php">Inventario</a></li>
                    <li class="nav-item mx-2"><a class="nav-link text-white" href="ventas.php">Gestion de Ventas</a></li>
                </ul>
                <div class="text-lg-end mt-3 mt-lg-0">
                    <?php mostrarBotonSesion(); ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Formulario de búsqueda -->
        <div class="busqueda text-light p-4 mb-4">
            <h2 class="text-center mb-4">Ventas Registradas</h2>
            <form action="" method="GET" class="mb-0">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2"><label class="form-label text-white fw-500">Folio</label><input type="number" name="folio" class="form-control" placeholder="Folio" required></div>
                    <div class="col-md-3"><label class="form-label text-white fw-500">Nombre</label><input type="text" name="cliente" class="form-control" placeholder="Nombre del Cliente" required></div>
                    <div class="col-md-3"><label class="form-label text-white fw-500">Telefono</label><input type="tel" name="telefono" class="form-control" placeholder="999-999-9999" required></div>
                    <div class="col-md-2"><label class="form-label text-white fw-500">Fecha</label><input type="date" name="fecha" class="form-control" required></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-warning w-100">Buscar</button></div>
                </div>
            </form>
        </div>
        <div class="tabla">
            <h2 class="text-center mb-0">Ventas Registradas</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>CLIENTE</th>
                            <th>TOTAL</th>
                            <th>FECHA</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($cantidad > 0) {
                            while ($u = $result->fetch_assoc()) {
                                $productos = [];
                                $idVenta = $u['id_venta'];

                                $sqlProductos = $mysqli->query("
            SELECT p.nombreProducto, vd.cantidad, vd.precioUnitario, vd.subtotal
            FROM venta_detalle vd
            INNER JOIN productos p ON vd.id_producto = p.id_producto
            WHERE vd.id_venta = $idVenta
        ");

                                while ($prod = $sqlProductos->fetch_assoc()) {
                                    $productos[] = [
                                        "producto" => $prod["nombreProducto"],
                                        "cant" => $prod["cantidad"],
                                        "precio" => $prod["precioUnitario"],
                                        "subtotal" => $prod["subtotal"]
                                    ];
                                }
                                $productosVenta = htmlspecialchars(json_encode($productos), ENT_QUOTES, 'UTF-8'); ?>
                                <tr>
                                    <td><?php echo $u['id_venta']; ?></td>
                                    <td><?php echo $u['cliente_nombre']; ?></td>
                                    <td class="fw-bold"><?php echo $u['totalVenta']; ?></td>
                                    <td><?php echo $u['fechaVenta']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm ver-detalle"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetalle"
                                            data-folio="<?php echo $u['id_venta']; ?>"
                                            data-cliente="<?php echo $u['cliente_nombre']; ?>"
                                            data-correo="<?php echo $u['cliente_correo']; ?>"
                                            data-telefono="<?php echo $u['cliente_telefono']; ?>"
                                            data-fecha="<?php echo $u['fechaVenta']; ?>"
                                            data-total="<?php echo $u['totalVenta']; ?>"
                                            data-productos="<?php echo $productosVenta; ?>">
                                            Ver detalles
                                        </button>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            echo '<tr> <td class ="text-center"> La tabla de ventas esta vacio </td>
                <td class ="text-center"> No hay datos disponibles </td>
                <td class ="text-center"> No hay datos disponibles </td>
                <td class ="text-center"> No hay datos disponibles</td>
                <td class ="text-center"> No hay datos disponibles </td>
                 </tr>';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- MODAL-->
        <div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header" style="background: #2C1F17; color: white;">
                        <h5 class="modal-title" id="modalDetalleLabel">Detalle de Venta - Folio <span id="folioModal"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>Cliente:</strong> <span id="clienteModal"></span></p>
                                <p><strong>Correo:</strong> <span id="correoModal"></span></p>
                                <p><strong>Teléfono:</strong> <span id="telefonoModal"></span></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p><strong>Fecha:</strong> <span id="fechaModal"></span></p>
                                <h4 class="text-warning mt-3">Total: $<span id="totalModal"></span></h4>
                            </div>
                        </div>
                        <h5 class="border-bottom pb-2">Productos</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaProductos"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="" method="GET" class="mb-2 d-flex justify-content-center">
            <button type="submit" class="btn btn-warning">Lista Completa</button>
        </form>
<footer>
    
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- EL SCRIPT QUE HACE QUE FUNCIONE EL MODAL -->
    <script>
        document.querySelectorAll('.ver-detalle').forEach(btn => {
            btn.addEventListener('click', function() {
                const folio = this.getAttribute('data-folio');
                const cliente = this.getAttribute('data-cliente');
                const correo = this.getAttribute('data-correo');
                const telefono = this.getAttribute('data-telefono');
                const fecha = this.getAttribute('data-fecha');
                const total = parseFloat(this.getAttribute('data-total')).toLocaleString('es-MX', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                const productos = JSON.parse(this.getAttribute('data-productos'));

                document.getElementById('folioModal').textContent = folio;
                document.getElementById('clienteModal').textContent = cliente;
                document.getElementById('correoModal').textContent = correo;
                document.getElementById('telefonoModal').textContent = telefono;
                document.getElementById('fechaModal').textContent = fecha;
                document.getElementById('totalModal').textContent = total;

                const tbody = document.getElementById('tablaProductos');
                tbody.innerHTML = '';
                productos.forEach(p => {
                    tbody.innerHTML += `<tr>
                    <td>${p.producto}</td>
                    <td class="text-center">${p.cant}</td>
                    <td class="text-end">$${parseFloat(p.precio).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                    <td class="text-end">$${parseFloat(p.subtotal).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
                </tr>`;
                });
            });
        });
    </script>

</body>

</html>