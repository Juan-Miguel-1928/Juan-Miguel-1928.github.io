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
function carrito(){
  if (isset($_SESSION['usuario'])){
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pisos del Caribe - Cotiza tu Piso Vinílico</title>

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Alpine.js para el cotizador en tiempo real -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- CSS Personalizado -->
  <link rel="stylesheet" href="css/cotiza.css">
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

  <!-- SECCIÓN COTIZADOR -->
  <section class="py-5 min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #8B6F47 0%, #6F4E37 50%, #4A3728 100%);">
    <div class="container">
      <div x-data="{
        ancho: 4.2,
        largo: 6,
        precioM2: 420,
        instalacion: false,
        metros() { return (this.ancho * this.largo).toFixed(2) },
        total() {
          let base = this.metros() * this.precioM2;
          if (this.instalacion) base += this.metros() * 150;
          return base.toLocaleString('es-MX')
        }
      }" class="calc-card mx-auto p-5 text-white rounded-4 shadow-lg" style="max-width: 680px;">
        
        <h2 class="text-center mb-5 fw-bold display-5">
          <i class="bi bi-calculator-fill text-warning me-3"></i>
          Cotiza tu Piso Vinílico
        </h2>

        <div class="row g-4">
          <!-- Ancho -->
          <div class="col-md-6">
            <label class="form-label fw-bold mb-3">Ancho (metros)</label>
            <input x-model.number="ancho" type="number" step="0.1" min="0.5" class="form-control input-custom shadow" placeholder="4.2">
          </div>
          <!-- Largo -->
          <div class="col-md-6">
            <label class="form-label fw-bold mb-3">Largo (metros)</label>
            <input x-model.number="largo" type="number" step="0.1" min="0.5" class="form-control input-custom shadow" placeholder="6">
          </div>

          <!-- Colección -->
          <div class="col-12">
            <label class="form-label fw-bold mb-3">Colección</label>
            <select x-model.number="precioM2" class="form-select select-custom shadow">
              <optgroup label="CONCRETE">
                <option value="390">Concrete Gris Claro – $390 m²</option>
                <option value="410">Concrete Gris Oscuro – $410 m²</option>
                <option value="425">Concrete Pulido – $425 m²</option>
                <option value="405">Concrete Texturizado – $405 m²</option>
              </optgroup>
              <optgroup label="FOREST">
                <option value="420" selected>Roble Natural – $420 m²</option>
                <option value="435">Roble Ahumado – $435 m²</option>
                <option value="450">Nogal Suave – $450 m²</option>
                <option value="470">Nogal Oscuro – $470 m²</option>
                <option value="445">Encino Gris – $445 m²</option>
              </optgroup>
              <optgroup label="HERRINGBONE">
                <option value="480">Herringbone Roble – $480 m²</option>
                <option value="510">Herringbone Nogal – $510 m²</option>
                <option value="465">Herringbone Gris – $465 m²</option>
                <option value="495">Herringbone Blanco – $495 m²</option>
              </optgroup>
              <optgroup label="FUTURA">
                <option value="430">Futura Blanca – $430 m²</option>
                <option value="445">Futura Negra – $445 m²</option>
                <option value="460">Futura Geométrica – $460 m²</option>
                <option value="415">Futura Cemento Suave – $415 m²</option>
              </optgroup>
              <optgroup label="MAX">
                <option value="520">Max Ébano – $520 m²</option>
                <option value="550">Max Roble XL – $550 m²</option>
                <option value="490">Max Gris Suave – $490 m²</option>
                <option value="475">Max Arena – $475 m²</option>
              </optgroup>
            </select>
          </div>

          <!-- Instalación -->
          <div class="col-12">
            <div class="form-check form-switch switch-custom">
              <input class="form-check-input" type="checkbox" x-model="instalacion" id="instalacion">
              <label class="form-check-label fw-bold text-white" for="instalacion">
                Incluir instalación (+$150 m²)
              </label>
            </div>
          </div>

          <!-- Resultado -->
          <div class="col-12 text-center pt-4">
            <hr class="border-secondary opacity-50">
            <p class="mb-2 fs-5 opacity-90">Superficie total:</p>
            <h3 class="fw-bold mb-3" x-text="metros() + ' m²'"></h3>
            <div class="total-price display-3 fw-bold mb-2" x-text="'$' + total() + ' MXN'"></div>
            <small class="text-white-50">*Precio aproximado sin IVA</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SECCIÓN RESEÑAS -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold display-5 text-dark">Lo que dicen nuestros clientes</h2>

      <div id="reviewsCarousel" class="carousel slide">
        <div class="carousel-inner">

          <!-- Reseña 1 -->
          <div class="carousel-item active">
            <div class="text-center">
              <div class="review-card mx-auto">
                <img src="img/cliente1.jpg" class="rounded-circle review-img mb-4" alt="Ana Gamboa">
                <h5 class="fw-bold">Ana Gamboa</h5>
                <div class="stars fs-3 mb-3 text-warning">
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-fill"></i>
                  <i class="bi bi-star-half"></i>
                </div>
                <p class="lead text-secondary px-4">
                  "Quedé enamorada del Roble Natural en mi sala. La instalación fue en un solo día y se ve espectacular. ¡100% recomendado!"
                </p>
              </div>
            </div>
          </div>

          <!-- Reseña 2 -->
          <div class="carousel-item">
            <div class="text-center">
              <div class="review-card mx-auto">
                <img src="img/cliente2.jpg" class="rounded-circle review-img mb-4" alt="Samuel Castillo">
                <h5 class="fw-bold">Samuel Castillo</h5>
                <div class="stars fs-3 mb-3 text-warning">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="lead text-secondary px-4">
                  "Elegimos el Nogal Oscuro para toda la casa. La calidad es increíble y el equipo súper profesional. ¡Gracias!"
                </p>
              </div>
            </div>
          </div>

          <!-- Reseña 3 -->
          <div class="carousel-item">
            <div class="text-center">
              <div class="review-card mx-auto">
                <img src="img/cliente3.jpg" class="rounded-circle review-img mb-4" alt="Laura Martínez">
                <h5 class="fw-bold">Laura Martínez</h5>
                <div class="stars fs-3 mb-3 text-warning">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                </div>
                <p class="lead text-secondary px-4">
                  "El Concrete Pulido transformó mi cocina. Se ve moderno y elegante. ¡El mejor cambio que hemos hecho en casa!"
                </p>
              </div>
            </div>
          </div>

        </div>

        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="prev">
          <i class="bi bi-chevron-left fs-1 text-warning"></i>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#reviewsCarousel" data-bs-slide="next">
          <i class="bi bi-chevron-right fs-1 text-warning"></i>
        </button>

        <!-- Indicadores -->
        <div class="carousel-indicators position-relative mt-4">
          <button type="button" data-bs-target="#reviewsCarousel" data-bs-slide-to="0" class="active bg-warning"></button>
          <button type="button" data-bs-target="#reviewsCarousel" data-bs-slide-to="1" class="bg-warning"></button>
          <button type="button" data-bs-target="#reviewsCarousel" data-bs-slide-to="2" class="bg-warning"></button>
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
             <a href="#instagram" class="text-white" >
              <i class="bi bi-instagram"></i>
            </a>
            <a href="#facebook" class="text-white" >
              <i class="bi bi-facebook"></i>
            </a>
            <a href="#whatsapp" class="text-white" >
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