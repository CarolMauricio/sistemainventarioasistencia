<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-dark shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase" href="<?php echo ROOT_URL; ?>">
      <i class="bi bi-box-seam me-2"></i> SISTEMA FARMACIA EL AHORRO
    </a>
    <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarResponsive" 
      aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarResponsive">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <span class="nav-link text-light fw-semibold">Bienvenido, <?php echo $_SESSION['fullName']; ?></span>
        </li>
        <li class="nav-item mx-2">
          <span class="text-light opacity-50">|</span>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-light btn-sm px-3 py-1 rounded-pill hover-glow" href="model/login/logout.php">
            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Gradiente moderno y efecto sutil */
  .bg-gradient-dark {
    background: linear-gradient(135deg, #0d1117, #1b2838);
  }

  /* Efecto de brillo en hover */
  .hover-glow:hover {
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
    transform: scale(1.03);
    transition: all 0.3s ease-in-out;
  }

  /* Transiciones suaves */
  .navbar, .navbar a, .navbar-brand {
    transition: all 0.3s ease-in-out;
  }

  /* Íconos Bootstrap */
  @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
</style>