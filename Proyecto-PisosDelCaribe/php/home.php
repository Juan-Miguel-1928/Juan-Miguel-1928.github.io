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
  <title>Pisos del Caribe - Home</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!--CSS personalizado (solo lo que NO se puede con Bootstrap) -->
  <link rel="stylesheet" type="text/css" href="css/home.css">
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

  <!-- HERO BANNER -->
  <section class="hero-banner">
    <div class="container">
      <div class="row justify-content-start">
        <div class="col-lg-6 col-md-8 col-sm-9">
          <div class="cuadro p-4 rounded-4 shadow-lg border border-light">
            <h1 class="display-5 fw-bold text-dark mb-3">
              Todo lo que debe saber sobre pisos vinílicos
            </h1>
            <p class="lead text-muted mb-4">
              Descubra cómo un piso vinílico puede mejorar el confort y la estética de su hogar o negocio.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SECCIÓN CONCEPTO -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center shadow rounded-4 overflow-hidden">
        <div class="col-lg-6 p-0">
          <img src="img/instalacion5.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-start">
        </div>
        <div class="col-lg-6 p-5">
          <h3 class="fw-bold text-dark">¿Qué es un piso vinílico y por qué deberías considerarlo?</h3>
          <p class="text-secondary mb-4">
            El piso vinílico es una opción práctica y moderna para revestir tus pisos, ideal si buscas:
          </p>
          <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-3">
              <i class="bi bi-shield-check text-warning fs-2 me-3"></i>
              <span class="fw-semibold text-dark">Durabilidad</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <i class="bi bi-brush text-warning fs-2 me-3"></i>
              <span class="fw-semibold text-dark">Fácil mantenimiento</span>
            </li>
            <li class="d-flex align-items-center">
              <i class="bi bi-palette text-warning fs-2 me-3"></i>
              <span class="fw-semibold text-dark">Estilo moderno</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- COTIZA AHORA -->
  <section class="min-vh-100 d-flex align-items-center position-relative overflow-hidden">
    <!-- Imagen de fondo (cubre todo) -->
    <img src="img/resultado21.jpg" class="position-absolute w-100 h-100 object-fit-cover" alt="Sala con pisos vinílicos"
      style="z-index: 1; top: 0; left: 0;">

    <!-- Degradado blanco arriba (simula el efecto) -->
    <div class="position-absolute w-100 h-50" style="background: linear-gradient(to bottom, white, transparent); opacity: 0.6; z-index: 2; top: 0;">
    </div>

    <!-- Contenido -->
    <div class="container position-relative text-center text-white" style="z-index: 3;">
      <h3 class="fw-bold display-5 mb-3">Cotiza Ahora</h3>
      <p class="lead mb-4">Transforma tus espacios con estilo, durabilidad y fácil mantenimiento.</p>
      <p class="mb-5">Cotiza hoy tus pisos vinílicos y descubre lo fácil que es renovar tu hogar.</p>
      <a type="button" href="cotiza.php" class="btn btn-warning btn-lg rounded-pill px-5 py-3 fw-bold text-dark shadow-lg">Cotiza ahora</a>
    </div>
  </section>

  <!-- PRUEBA -->
  <section class=" py-5 bg-warning-subtle mt-5">
    <div class="container">
      <!-- Título -->
      <h2 class="text-center fw-bold text-dark mb-5">Beneficios de los pisos vinílicos</h2>
      <!-- Tarjetas de beneficios -->
      <div class="row g-4 justify-content-center">
        <!-- Tarjeta 1: Instalación rápida -->
        <div class="col-lg-4 col-md-6">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="position-relative">
              <img src="img/instalacion2.jpg" class="card-img-top" alt="Instalación rápida">
              <div class="icon-circle bg-warning text-dark">
                <i class="bi bi-tools fs-4"></i>
              </div>
            </div>
            <div class="card-body bg-dark text-white p-4">
              <h5 class="card-title fw-bold">Instalación rápida y sencilla</h5>
              <p class="card-text small"> La instalación de pisos vinílicos es rápida, limpia y sin obras: renueva tus espacios nunca fue tan sencillo y rápido.
              </p>
            </div>
          </div>
        </div>

        <!-- Tarjeta 2: Durabilidad -->
        <div class="col-lg-4 col-md-6">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="position-relative">
              <img src="img/instalacion1.jpg" class="card-img-top" alt="Durabilidad">
              <div class="icon-circle bg-warning text-dark">
                <i class="bi bi-person-walking fs-4"></i>
              </div>
            </div>
            <div class="card-body bg-dark text-white p-4">
              <h5 class="card-title fw-bold">Durabilidad garantizada</h5>
              <p class="card-text small">
                Los pisos vinílicos destacan por su resistencia, fácil mantenimiento y diseños que imitan madera, piedra u otros acabados.</p>
            </div>
          </div>
        </div>

        <!-- Tarjeta 3: Cualidades extraordinarias -->
        <div class="col-lg-4 col-md-6">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="position-relative">
              <img src="img/resultado23.png" class="card-img-top" alt="Resistencia al agua">
              <div class="icon-circle bg-warning text-dark">
                <i class="bi bi-droplet fs-4"></i>
              </div>
            </div>
            <div class="card-body bg-dark text-white p-4">
              <h5 class="card-title fw-bold">Cualidades extraordinarias</h5>
              <p class="card-text small">
                Los pisos vinílicos resisten la humedad, aíslan el ruido y ofrecen diseños versátiles que aportan calidez a cualquier espacio.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CATÁLOGO DE PISOS VINÍLICOS -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center flex-column-reverse flex-lg-row text-center text-lg-start">

        <!-- Texto (se centra en móvil) -->
        <div class="col-lg-6 mt-4 mt-lg-0">
          <h3 class="fw-bold">Catálogo de pisos vínilicos</h3>
          <p class="text-muted">
            Explore nuestro amplio catálogo con diseños únicos para su hogar
          </p>
          <a href="catalogo.php" type="button" class="btn btn-warning rounded-pill px-4">
            Catálogo
          </a>
        </div>

        <!-- Imagen -->
        <div class="col-lg-6">
          <img src="img/variedad.png" class="img-fluid rounded-5 shadow" alt="Variedad de pisos vinílicos">
        </div>
      </div>
    </div>
  </section>

  <!-- Areas de recomendacion-->
  <section class=" py-5 bg-warning-subtle mt-5">
    <div class="container">
      <!-- Título -->
      <h3 class="text-center fw-bold text-dark mb-5">Áreas recomendadas de uso</h3>
      <!-- Tarjetas de beneficios -->
      <div class="row g-4 justify-content-center">
        <!-- Tarjeta 1: Instalación rápida -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body bg-dark text-white p-4 rounded-top">
              <h5 class="card-title fw-bold mb-2">Hogar</h5>
              <p class="card-text small mb-0">
                Ideal para espacios residensiales como recámaras, salas, comedores y pasillos. Su textura cálida y su amplia variedad de diseños permiten adaptarse a distintos estilos de decoración.</p>
            </div>
            <div class="position-relative">
              <img src="img/resultado13.jpg" class="card-img-bottom img-fluid rounded-bottom" alt="Instalación rápida">
            </div>
          </div>
        </div>

        <!-- Tarjeta 2: Durabilidad -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body bg-dark text-white p-4 rounded-top">
              <h5 class="card-title fw-bold mb-2">Oficína</h5>
              <p class="card-text small mb-0">
                Ideal para ambientes laborales ofreciendo una gran resistencia, reducción del ruido, mejorando el confort, estetica y permitiendo una facil limpieza diaria</p>
            </div>
            <div class="position-relative">
              <img src="img/resultado20.jpg" class="card-img-bottom img-fluid rounded-bottom" alt="Instalación rápida">
            </div>
          </div>
        </div>

        <!-- Tarjeta 3: Cualidades extraordinarias -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card beneficio-card h-100 border-0 shadow-sm overflow-hidden">
            <div class="card-body bg-dark text-white p-4 rounded-top">
              <h5 class="card-title fw-bold mb-2">Comercio</h5>
              <p class="card-text small mb-0">
                Ideal para comercios ofrenciendo un atractivo, una alta resistencia al transito constante de las personas, facil limpieza y profesionalismo para mejorar la imagen de su negocio.</p>
            </div>
            <div class="position-relative">
              <img src="img/resultado9.jpg" class="card-img-bottom img-fluid rounded-bottom" alt="Instalación rápida">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Procesos de instalacion -->
  <section class="my-5">
    <div class="container bg-warning-subtle rounded-4 p-4">
      <h3 class="text-center mb-5"><strong>Procesos de instalación</strong></h3>

      <!-- PASO 1 -->
      <div class="row bg-light mb-5 rounded-4 align-items-center">
        <div class="col-md-6 p-3">
          <img src="img/instalacion2.jpg" class="img-fluid rounded-4 img-fixed shadow-sm" alt="Evaluación">
        </div>
        <div class="col-md-6 p-3">
          <h5 class="fw-bold">1. Evaluación del área</h5>
          <p class="mb-0">Antes de instalar, se revisa que el piso esté nivelado, limpio y seco. Se eliminan imperfecciones.</p>
        </div>
      </div>

      <!-- PASO 2 -->
      <div class="row bg-light mb-5 rounded-4 align-items-center">
        <div class="col-md-6 order-md-2 p-3">
          <img src="img/instalacion6.jpg" class="img-fluid rounded-4 img-fixed shadow-sm" alt="Limpieza">
        </div>
        <div class="col-md-6 order-md-1 p-3">
          <h5 class="fw-bold">2. Limpieza y preparación</h5>
          <p class="mb-0">Los pisos vinílicos destacan por su resistencia, fácil mantenimiento y diseños realistas.</p>
        </div>
      </div>

      <!-- PASO 3 -->
      <div class="row bg-light mb-4 rounded-4 align-items-center">
        <div class="col-md-6 p-3">
          <img src="img/instalacion7.jpeg" class="img-fluid rounded-4 img-fixed shadow-sm" alt="Medición">
        </div>
        <div class="col-md-6 p-3">
          <h5 class="fw-bold">3. Medición y preparación</h5>
          <p class="mb-0">Se mide el espacio y se planea la distribución para minimizar cortes.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ANTES Y DESPUÉS -->
  <section class="my-5">
    <div class="container">
      <h3 class="text-center mb-4"><strong>Antes y Después</strong></h3>
      <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="img/ad1.png" class="d-block w-100 img-fluid carousel-img" alt="sala">
          </div>
          <div class="carousel-item">
            <img src="img/ad2.png" class="d-block w-100 img-fluid carousel-img" alt="habiatacion">
          </div>
          <div class="carousel-item">
            <img src="img/ad3.png" class="d-block w-100 img-fluid carousel-img" alt="baño">
          </div>
          <div class="carousel-item">
            <img src="img/ad4.png" class="d-block w-100 img-fluid carousel-img" alt="cocina">
          </div>
          <div class="carousel-item">
            <img src="img/ad5.png" class="d-block w-100 img-fluid carousel-img" alt="cuarto">
          </div>
        </div>

        <!-- Botones de control -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
          <i class="bi bi-chevron-compact-left text-white"></i>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
          <i class="bi bi-chevron-compact-right text-light"></i>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>
    </div>
  </section>


  <!-- PREGUNTAS FRECUENTES-->
  <section class="container my-5">
    <h3 class="text-center fw-bold mb-4">Preguntas frecuentes</h3>
    <div class="accordion" id="faqAccordion">

      <!-- Pregunta 1 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
          <p><strong>¿Qué tan buenos son los pisos vinílicos?</strong></p>
        </button>
        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Los pisos vinílicos cuentan con múltiples beneficios a diferencia de otras superficies, pues tienen gran resistencia a la humedad, al agua, al desgaste y a los impactos, además de contar con alta durabilidad y amortiguar el ruido de las pisadas.</p>
          </div>
        </div>
      </div>

      <!-- Pregunta 2 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
          <p><strong>¿Cómo se limpian los pisos vinílicos?</strong></p>
        </button>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Se recomienda barrer o aspirar regularmente y limpiar con un trapo húmedo y jabón neutro.</p>
          </div>
        </div>
      </div>

      <!-- Pregunta 3 -->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
          <p><strong>¿Cómo se adhieren los pisos vinílicos?</strong></p>
        </button>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Dependiendo del tipo, pueden instalarse con adhesivo, clic o autoadhesivo.</p>
          </div>
        </div>
      </div>

      <!-- Pregunta 4-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
          <p><strong>¿De qué material están hechos los pisos vinílicos?</strong></p>
        </button>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Los pisos vinílicos están fabricados principalmente con policloruro de vinilo (PVC), un material plástico muy resistente y flexible. Además, cuentan con capas protectoras que les brindan mayor durabilidad, resistencia a la humedad, al desgaste y a los impactos.</p>
          </div>
        </div>
      </div>

      <!-- Pregunta 5-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
          <p><strong>¿Cómo elegir tus pisos vinílicos?</p></strong>
        </button>
        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Para elegir los pisos vinílicos adecuados, considera el tipo de ambiente donde los instalarás, el nivel de tráfico, el diseño que combine con tu espacio y el tipo de instalación (clic, adhesivo o autoadhesivo). También revisa el grosor y la capa de uso para asegurarte de su durabilidad.
            <p>
          </div>
        </div>
      </div>

      <!-- Pregunta 6-->
      <div class="accordion-item">
        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
          <p><strong>¿Qué tipos de pisos vinílicos hay?</strong></p>
        </button>
        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary">
            <p>Existen varios tipos de pisos vinílicos según su forma y sistema de instalación:</p>
            <ul>
              <li>
                En rollo: ideales para cubrir grandes áreas sin uniones visibles.
              </li>
              <li>
                En loseta o tabla: imitan materiales como la madera o la piedra.
              </li>
              <li>
                Autoadhesivos: fáciles de instalar, con adhesivo en la parte posterior.
              </li>
              <li>
                Sistema clic: encajan entre sí sin pegamento, ofreciendo una instalación limpia y rápida.
              </li>
            </ul>
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