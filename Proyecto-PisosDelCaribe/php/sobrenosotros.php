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
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pisos del Caribe - Sobre Nosotros</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/sobrenosotros.css">
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


  <!-- BANNER -->
  <section class="banner position-relative d-flex align-items-center text-center text-white">
    <img src="img/resultado19.jpg" class="bg-img" alt="Sala con pisos vinílicos">
    <div class="gradient-top"></div>
    <div class="container position-relative z-2">
      <h3 class="fw-bold display-5 mb-3">¿Quiénes somos?</h3>
      <p class="lead mb-3 fw-bold sub">Una Empresa Comprometida con la Excelencia</p>
      <p class="mb-4">Somos una empresa con más de 13 años de experiencia en la industria. Nuestro equipo de profesionales altamente capacitados trabaja día a día para garantizar que cada proyecto cumpla con los más altos estándares de calidad. Transparencia, innovación y satisfacción del cliente son nuestros pilares.</p>
    </div>
  </section>

  <!-- CONTADORES -->
  <section class="py-5 stats text-white">
    <div class="container py-5">
      <div class="row text-center g-5">
        <div class="col-6 col-md-3 stat">
          <h3 class="display-3 fw-bold mb-0"><span class="accent">+</span>1,300</h3>
          <p class="lead mb-0">Proyectos terminados</p>
        </div>
        <div class="col-6 col-md-3 stat">
          <h3 class="display-3 fw-bold mb-0"><span class="accent">+</span>13</h3>
          <p class="lead mb-0">Años de experiencia</p>
        </div>
        <div class="col-6 col-md-3 stat">
          <h3 class="display-3 fw-bold mb-0"><span class="accent">+</span>99<span class="accent">%</span></h3>
          <p class="lead mb-0">Clientes satisfechos</p>
        </div>
        <div class="col-6 col-md-3 stat">
          <h3 class="display-3 fw-bold mb-0">24</h3>
          <p class="lead mb-0">Estados con presencia</p>
        </div>
      </div>
    </div>
  </section>

  <!-- NUESTRA HISTORIA (Timeline con Accordion) -->
  <section class="py-5 bg-light">
    <div class="container faq-wrapper">
      <h2 class="text-center mb-5 fw-bold display-5">Nuestra Historia</h2>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="accordion card-accordion" id="timeline">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2010">
                  2010 – Fundación
                </button>
              </h2>
              <div id="y2010" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Nace <strong>Pisos del Caribe</strong> en la Ciudad de México con solo 3 personas y una gran visión: llevar pisos de alta calidad a todo el país.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2011">
                  2011 – Primeros pasos
                </button>
              </h2>
              <div id="y2011" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Abrimos nuestra primera bodega y comenzamos a distribuir pisos laminados y vinílicos en el centro del país. Cerramos el año con más de 50 proyectos residenciales.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2012">
                  2012 – Expansión nacional
                </button>
              </h2>
              <div id="y2012" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Llegamos a Guadalajara, Monterrey y Mérida. Incorporamos la línea de pisos decks y muros WPC, pioneros en materiales ecológicos.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2013">
                  2013 – Certificaciones
                </button>
              </h2>
              <div id="y2013" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Obtenemos las certificaciones <strong>FSC</strong> y <strong>Greenguard Gold</strong>. Lanzamos instalación profesional garantizada.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2014">
                  2014 – Crecimiento récord
                </button>
              </h2>
              <div id="y2014" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Duplicamos el equipo, abrimos 3 centros y superamos los 1,000 proyectos completados.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#y2015">
                  2015 – Consolidación
                </button>
              </h2>
              <div id="y2015" class="accordion-collapse collapse" data-bs-parent="#timeline">
                <div class="accordion-body">
                  Nos posicionamos como una de las principales empresas de pisos en México y comenzamos a exportar a Centroamérica.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NUESTROS VALORES -->
  <section class="py-5 section-values">
    <div class="container">
      <h1 class="text-center mb-5 fw-bold display-4 text-dark">Nuestros Valores</h1>

      <!-- Desktop -->
      <div class="row g-4 desktop-values d-none d-md-flex">
        <div class="col-md-4">
          <div class="valor-card premium">
            <i class="bi bi-bullseye icono"></i>
            <h3>Calidad</h3>
            <p>Productos y servicios que superan expectativas mediante controles estrictos.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="valor-card premium">
            <i class="bi bi-lightbulb icono"></i>
            <h3>Innovación</h3>
            <p>Materiales y procesos vanguardistas que protegen el ambiente y tu inversión.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="valor-card premium">
            <i class="bi bi-hand-thumbs-up-fill icono"></i>
            <h3>Compromiso</h3>
            <p>Responsabilidad y garantía en cada proyecto, siempre con foco en el cliente.</p>
          </div>
        </div>
      </div>

      <!-- Mobile Carousel -->
      <div id="valoresMobile" class="carousel slide d-md-none mobile-values" data-bs-ride="carousel" data-bs-interval="4200">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="valor-card premium mx-auto">
              <i class="bi bi-bullseye icono"></i>
              <h3>Calidad</h3>
              <p>Productos y servicios que superan expectativas.</p>
            </div>
          </div>
          <div class="carousel-item">
            <div class="valor-card premium mx-auto">
              <i class="bi bi-lightbulb icono"></i>
              <h3>Innovación</h3>
              <p>Materiales y procesos vanguardistas.</p>
            </div>
          </div>
          <div class="carousel-item">
            <div class="valor-card premium mx-auto">
              <i class="bi bi-hand-thumbs-up-fill icono"></i>
              <h3>Compromiso</h3>
              <p>Responsabilidad y garantía en cada proyecto.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NUESTRO EQUIPO -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold display-5">Nuestro Equipo</h2>

      <!-- Desktop -->
      <div class="row g-4 d-none d-lg-flex">
        <div class="col-lg-4">
          <article class="equipo-card-3d">
            <div class="card-inner">
              <img src="img/empleado1.jpg" class="foto-equipo" alt="Carlos Méndez">
              <div class="info-equipo">
                <h3 class="nombre">Carlos Méndez</h3>
                <p class="cargo">CEO & Fundador</p>
              </div>
            </div>
          </article>
        </div>
        <div class="col-lg-4">
          <article class="equipo-card-3d">
            <div class="card-inner">
              <img src="img/empleado2.jpg" class="foto-equipo" alt="Juan Manuel">
              <div class="info-equipo">
                <h3 class="nombre">Juan Manuel</h3>
                <p class="cargo">Director Operativo</p>
              </div>
            </div>
          </article>
        </div>
        <div class="col-lg-4">
          <article class="equipo-card-3d">
            <div class="card-inner">
              <img src="img/empleado3.jpg" class="foto-equipo" alt="Juan López">
              <div class="info-equipo">
                <h3 class="nombre">Juan López</h3>
                <p class="cargo">Jefe Técnico</p>
              </div>
            </div>
          </article>
        </div>
      </div>

      <!-- Mobile Carousel -->
      <div id="equipoMobile" class="carousel slide d-lg-none" data-bs-ride="carousel" data-bs-touch="true">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="equipo-card-3d mx-auto">
              <div class="card-inner">
                <img src="img/empleado1.jpg" class="foto-equipo" alt="Carlos Méndez">
                <div class="info-equipo">
                  <h3 class="nombre">Carlos Méndez</h3>
                  <p class="cargo">CEO & Fundador</p>
                  <button class="btn btn-ver-perfil">Ver perfil</button>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="equipo-card-3d mx-auto">
              <div class="card-inner">
                <img src="img/empleado2.jpg" class="foto-equipo" alt="Juan Manuel">
                <div class="info-equipo">
                  <h3 class="nombre">Juan Manuel</h3>
                  <p class="cargo">Director Operativo</p>
                  <button class="btn btn-ver-perfil">Ver perfil</button>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="equipo-card-3d mx-auto">
              <div class="card-inner">
                <img src="img/empleado3.jpg" class="foto-equipo" alt="Juan López">
                <div class="info-equipo">
                  <h3 class="nombre">Juan López</h3>
                  <p class="cargo">Jefe Técnico</p>
                  <button class="btn btn-ver-perfil">Ver perfil</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#equipoMobile" data-bs-slide="prev">
          <span class="control-circle bg-warning"><i class="bi bi-chevron-left fs-1 text-dark"></i></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#equipoMobile" data-bs-slide="next">
          <span class="control-circle bg-warning"><i class="bi bi-chevron-right fs-1 text-dark"></i></span>
        </button>
      </div>
    </div>
  </section>

  <!-- NUESTROS PROYECTOS -->
  <section class="py-5 section-projects">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold display-5">Nuestros Proyectos</h2>

      <!-- Desktop Grid -->
      <div class="row g-4 d-none d-md-flex">
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-1"><img src="img/proyecto1.jpg" alt="Cozumel">
            <div class="proyecto-body">
              <h3>Proyecto Cozumel</h3>
              <p class="short">Penthouse 500 m² — Instalación vinílico extra ancho.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-2"><img src="img/proyecto4.jpg" alt="Selvática">
            <div class="proyecto-body">
              <h3>Proyecto Selvática</h3>
              <p class="short">Áreas exteriores con decks WPC — Resistente clima tropical.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-3"><img src="img/proyecto2.jpg" alt="Isla Mujeres">
            <div class="proyecto-body">
              <h3>Proyecto Catamarán</h3>
              <p class="short">Catamarán - Suelos marinos con SPC 100% impermeable.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-4"><img src="img/proyecto3.jpg" alt="Residencia">
            <div class="proyecto-body">
              <h3>Proyecto Residencial</h3>
              <p class="short">Remodelación integral — mezcla madera y vinílico.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-5"><img src="img/proyecto5.jpg" alt="Oficina">
            <div class="proyecto-body">
              <h3>Proyecto Corporativo</h3>
              <p class="short">Oficinas — acústica y durabilidad en alto tránsito.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="proyecto-card" data-bs-toggle="modal" data-bs-target="#modal-proj-6"><img src="img/proyecto6.jpg" alt="Hotel">
            <div class="proyecto-body">
              <h3>Proyecto Hotelero</h3>
              <p class="short">Hoteles en la costa — resistencia UV y mantenimiento fácil.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Carousel -->
      <div id="proyectosMobile" class="carousel slide d-md-none" data-bs-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-1"><img src="img/proyecto1.jpg" alt="Cozumel">
              <div class="proyecto-body">
                <h3>Proyecto Cozumel</h3>
                <p class="short">Penthouse 500 m² — Instalación vinílico extra ancho.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-2"><img src="img/proyecto4.jpg" alt="Selvática">
              <div class="proyecto-body">
                <h3>Proyecto Selvática</h3>
                <p class="short">Áreas exteriores con decks WPC.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-3"><img src="img/proyecto2.jpg" alt="Isla Mujeres">
              <div class="proyecto-body">
                <h3>Proyecto Catamarán</h3>
                <p class="short">Suelos marinos con SPC 100% impermeable.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-4"><img src="img/proyecto3.jpg" alt="Residencia">
              <div class="proyecto-body">
                <h3>Proyecto Residencial</h3>
                <p class="short">Remodelación integral.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-5"><img src="img/proyecto5.jpg" alt="Oficina">
              <div class="proyecto-body">
                <h3>Proyecto Corporativo</h3>
                <p class="short">Oficinas — acústica y durabilidad.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="proyecto-card mx-auto" data-bs-toggle="modal" data-bs-target="#modal-proj-6"><img src="img/proyecto6.jpg" alt="Hotel">
              <div class="proyecto-body">
                <h3>Proyecto Hotelero</h3>
                <p class="short">Hoteles en la costa — resistencia UV.</p>
              </div>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#proyectosMobile" data-bs-slide="prev">
          <span class="control-circle bg-warning"><i class="bi bi-chevron-left fs-1 text-dark"></i></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#proyectosMobile" data-bs-slide="next">
          <span class="control-circle bg-warning"><i class="bi bi-chevron-right fs-1 text-dark"></i></span>
        </button>
      </div>
    </div>
  </section>

  <!-- TODOS LOS MODALES DE PROYECTOS -->
  <!-- Modal 1 -->
  <div class="modal fade" id="modal-proj-1" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Cozumel — El Cielo VIP</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto1.jpg" class="img-fluid rounded-4 mb-4" alt="Cozumel">
          <p class="lead">Penthouse de 500 m² con vistas 360° al mar. Se instalaron tablas vinílicas extra anchas (1.80 m x 23 cm), diseñadas para minimizar juntas y maximizar continuidad visual, logrando un look nórdico con resistencia caribeña.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>Tablas extra anchas y largas — menos juntas visuales.</li>
            <li>SPC 100% impermeable, resistente al sol y a la humedad.</li>
            <li>Instalación flotante sobre losa radiante.</li>
            <li>Garantía de instalación profesional por 5 años.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 2 -->
  <div class="modal fade" id="modal-proj-2" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Selvática — Tirolinas y Decks</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto4.jpg" class="img-fluid rounded-4 mb-4" alt="Selvática">
          <p class="lead">Diseño de áreas exteriores con decks WPC y muros decorativos. Buscamos máxima durabilidad con mínima mantención, ideales para clima húmedo y tropical.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>Decks WPC eco-friendly, sin astillas.</li>
            <li>Revestimientos con protección UV y antimicrobiana.</li>
            <li>Herrajes de acero inoxidable para climas costeros.</li>
            <li>Colores y texturas personalizables según proyecto.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 3 -->
  <div class="modal fade" id="modal-proj-3" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Catamarán — Isla Mujeres</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto2.jpg" class="img-fluid rounded-4 mb-4" alt="Isla Mujeres">
          <p class="lead">Suelos marinos adaptados a ambientes salinos y uso intensivo. Instalaciones en embarcaciones y terrazas con materiales resistentes y antideslizantes.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>SPC y vinílicos con base antideslizante.</li>
            <li>Protección adicional contra salitre y UV.</li>
            <li>Mantenimiento sencillo y rápido.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 4 -->
  <div class="modal fade" id="modal-proj-4" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Residencial — Remodelación Integral</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto3.jpg" class="img-fluid rounded-4 mb-4" alt="Residencia">
          <p class="lead">Remodelación con mezcla de madera natural y vinílico de alta resistencia para lograr calidez y durabilidad.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>Acabados personalizados y junta mínima.</li>
            <li>Compatibilidad con calefacción por losa.</li>
            <li>Garantía de color y resistencia al desgaste.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 5 -->
  <div class="modal fade" id="modal-proj-5" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Corporativo — Oficinas</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto5.jpg" class="img-fluid rounded-4 mb-4" alt="Oficina">
          <p class="lead">Suelos diseñados para alto tránsito con control acústico y fácil mantenimiento.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>Capa de uso reforzada para tráfico intenso.</li>
            <li>Propiedades acústicas para reducción de ruido.</li>
            <li>Fácil reemplazo por secciones.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 6 -->
  <div class="modal fade" id="modal-proj-6" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header border-0">
          <h3 class="modal-title fw-bold">Proyecto Hotelero — Costa y Resorts</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <img src="img/proyecto6.jpg" class="img-fluid rounded-4 mb-4" alt="Hotel">
          <p class="lead">Instalaciones en hoteles con materiales resistentes a UV, salitre y uso intenso. Enfoque en estética y facilidad de mantenimiento.</p>
          <h5 class="fw-bold">Detalles:</h5>
          <ul>
            <li>Materiales con estabilizadores UV.</li>
            <li>Texturas antideslizantes y seguras.</li>
            <li>Planes de mantenimiento y reposición.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- PREGUNTAS FRECUENTES -->
  <section class="py-5">
    <div class="container faq-wrapper">
      <h2 class="text-center mb-5 fw-bold display-5">Preguntas frecuentes — Sobre Nosotros</h2>
      <div class="accordion card-accordion" id="faqSobreNosotros">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button fw-bold " type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
              ¿Qué hace diferente a Pisos del Caribe?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqSobreNosotros">
            <div class="accordion-body">
              Nos enfocamos en materiales resistentes al clima tropical (SPC y vinílicos con estabilizadores UV), instalación profesional con garantía y atención personalizada para cada proyecto. Además trabajamos con certificaciones que aseguran calidad y sostenibilidad.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              ¿Ofrecen instalación y garantía?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqSobreNosotros">
            <div class="accordion-body">
              Sí — contamos con equipo de instalación certificado y ofrecemos garantía en mano de obra y materiales según la línea del producto. Pregunta por las condiciones al cotizar tu proyecto.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold " type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              ¿Trabajan proyectos residenciales y comerciales?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqSobreNosotros">
            <div class="accordion-body">
              Atendemos ambos sectores: desde remodelaciones residenciales hasta instalaciones de alto tránsito para oficinas, hoteles y embarcaciones. Adaptamos el producto a la necesidad (acústica, antideslizante, resistencia UV).
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold " type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
              ¿Tienen showroom o visitas técnicas?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqSobreNosotros">
            <div class="accordion-body">
              Contamos con showroom y también ofrecemos visitas técnicas para cotizar con precisión. Puedes agendar una visita vía nuestro formulario de contacto o por WhatsApp.
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
              ¿Qué tipos de pisos vinílicos recomiendan para zonas húmedas?
            </button>
          </h2>
          <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqSobreNosotros">
            <div class="accordion-body">
              Para zonas húmedas recomendamos SPC 100% impermeable o vinílicos con núcleo rígido y tratamiento antideslizante. También evaluamos juntas y pendientes para evitar acumulación de agua.
            </div>
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