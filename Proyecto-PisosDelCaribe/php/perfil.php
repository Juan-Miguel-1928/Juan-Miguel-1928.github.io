<?php
session_start();
require "enlaze_base_de_datos.php";
function mostrarPerfil()
{
  if (isset($_SESSION['usuario'])) {
    echo '<a href="php/perfil.php" class="btn btn-outline-warning btn-sm fw-bold px-5 m-4">Ver Perfil</a>';
  }
}

function mostrarBotonSesion() {
    if (isset($_SESSION['usuario'])) {
        echo '<a href="cierre_de_sesion.php" class="btn btn-outline-danger btn-sm fw-bold px-4">Cerrar sesión</a>';
    } else {
        echo '<a href="../login.html" class="btn btn-outline-warning btn-sm fw-bold px-4">Iniciar sesión</a>';
    }
}

function carrito() {
    if (isset($_SESSION['usuario'])) {
        echo '<li class="nav-item mx-2"><a class="nav-link text-white" href="../carrito.php">Carrito</a></li>';
    }
}

if(!isset($_SESSION['uid'])) {
    header("Location: ../login.html");
    exit();
}

$id = $_SESSION['uid'];

$stmt = $mysqli->prepare("SELECT nombre, correo, telefono, contraseña FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($user = $result->fetch_assoc()) {
    $nombre = $user['nombre'];
    $email = $user['correo'];
    $telefono = $user['telefono'];
    $contraseña = $user['contraseña'];
}

$stmt->close();
$mensaje = "";
if(isset($_POST['nombre']) &&  isset($_POST['apellidos']) &&  isset($_POST['correo']) &&  isset($_POST['telefono']) &&  isset($_POST['mensaje']))
    {
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $mensajeUsuario = $_POST['mensaje'];
        $stmt = $mysqli->prepare("INSERT INTO contacto (nombre, apellidos, correo, telefono, mensaje) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss",$nombre, $apellidos, $email, $telefono, $mensajeUsuario );

         if($stmt->execute()){
        $mensaje = "Mensaje enviado correctamente ";
       
        }else
        {
            $mensaje = "Error al registrar mensaje ". $stmt->error;
        }
         $stmt->close();
    }


?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perfil</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!--CSS personalizado (solo lo que NO se puede con Bootstrap) -->
  <link rel="stylesheet" type="text/css" href="../css/perfil.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg" style="background-color: #2C1F17;">
    <div class="container">
      <!-- Logo + Nombre -->
      <a class="navbar-brand d-flex align-items-center text-white" href="#">
        <img src="../img/logo.png" alt="Logo" class="me-2 rounded" width="auto" height="80">
      </a>

      <!-- Toggler (menú hamburguesa blanco) -->
      <button class="navbar-toggler border border-light bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menú -->
      <div class="collapse navbar-collapse justify-content-between" id="menuPrincipal">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="../home.php">Inicio</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="../sobrenosotros.php">Acerca de nosotros</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="../catalogo.php">Catálogo</a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link text-white" href="../cotiza.php">Cotiza</a>
          </li>
          <?php carrito(); ?>
        </ul>

        <!-- Botón Contacto (a la derecha del menú, dentro del collapse) -->
         
        <div class="text-lg-end mt-3 mt-lg-0">
          <?php mostrarBotonSesion(); ?>
        </div>
      </div>
    </div>
  </nav> 
  <!-- Formulario actualizar datos usuario -->
  <section class="my-5">
  
    

     <div class="container register-container">
    <div class="card shadow-lg border-0 rounded-lg bg-white">

    <div class="card-header card-header-custom rounded-top-lg">
            <h2>Datos de tu cuenta</h2>
    </div>

    <div class="card-body">
        <form  action="" method="POST" class=" text-center">
            <?php if(!empty($mensajeActualizarDatos)) { ?>
                <div class = "alert alert-success text-center " role="alert">
                <?php echo htmlspecialchars($mensajeActualizarDatos);?>
                </div>
                <?php } ?>
             <div class="form-group row">
                    <label class="col-sm-3 col-form-label"  for="nombre" > Nombre: </label> 
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nombre" placeholder="Escribe el nuevo nombre de usaurio" maxlength="100" name="nombre" value="<?php echo $nombre;?>" readonly  required> 
                    </div>
              </div>
             <div class="form-group row">
                    <label class="col-sm-3 col-form-label"  for="correo" > Correo: </label> 
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="correo" placeholder="Escribe el nuevo correo" maxlength="100" name="correo" value="<?php echo $email;?>" readonly  required> 
                    </div>
              </div>
              <div class="form-group row">
                    <label class="col-sm-3 col-form-label"  for="telefono" > Telefono: </label> 
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="telefono" placeholder="Escribe el numero de telefono"  inputmode="numeric" pattern="[0-9]*" name="telefono" value="<?php echo $telefono;?>" readonly  required> 
                    </div>
              </div>
              <div class="form-group row">
                    <label class="col-sm-3 col-form-label"  for="contraseña" > Contraseña: </label> 
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="contraseña" placeholder="Escribe la nueva contraseña"  maxlength="100" name="contraseña" value="<?php echo $contraseña;?>" readonly  required> 
                    </div>
              </div>
               <div class="text-center form-group row p-1">
                    <div class="col-sm-12 text-center">
                    <a href="editarPerfil.php" class="btn btn-editar fw-semibold px-4">Editar datos</a>
                    </div>
                </div>
            </form>
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
              <img src="../img/logo.png" alt="Logo" class="me-2 rounded" width="auto" height="80">
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
            <a  id="avisoPrivacidad" class="text-primary">Aviso de privacidad</a>
            </small> 
        </div>

        <!-- Columna 2: Formulario -->
     <div class="col-md-4 mb-4">
        <div class="p-4 text-dark rounded form-box">
          <h5 class="fw-bold text-center mb-3 text-light">Contáctanos</h5>
          <form action="" method="POST">
             <?php if(!empty($mensaje)) { ?>
                <div class = "alert alert-warning text-center " role="alert">
                <?php echo htmlspecialchars($mensaje);?>
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
 avisoPrivacidad.addEventListener("click", function(event)
 {
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