<!-- Footer Moderno Corregido -->
<footer class="footer-modern text-center text-white py-4 mt-auto w-100">
  <div class="container">
    <p class="mb-1 fw-semibold">
      Sistema de Inventario / Asistencia Farmacia El Ahorro
    </p>
    <div class="footer-icons">
      <a href="#" class="mx-2"><i class="bi bi-github"></i></a>
      <a href="#" class="mx-2"><i class="bi bi-envelope"></i></a>
      <a href="#" class="mx-2"><i class="bi bi-globe"></i></a>
    </div>
  </div>
</footer>

<!-- Estilos del footer -->
<style>
  .footer-modern {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
    background: rgba(20, 20, 30, 0.85);
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #eee;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.3px;
    box-shadow: 0 -2px 15px rgba(0,0,0,0.3);
    text-align: center;
  }

  .footer-modern p {
    font-size: 0.95rem;
    margin-bottom: 5px;
  }

  .footer-icons a {
    color: #00b4d8;
    text-decoration: none;
    font-size: 1.2rem;
    transition: 0.3s;
  }

  .footer-icons a:hover {
    color: #90e0ef;
    transform: scale(1.1);
  }

  /* Asegura que el footer quede centrado en pantallas pequeñas */
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .container {
    flex: 1;
  }
</style>

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Datatables script -->
<script type="text/javascript" charset="utf8" src="vendor/DataTables/datatables.js"></script>
<script type="text/javascript" charset="utf8" src="vendor/DataTables/sumsum.js"></script>

<!-- Chosen files for select boxes -->
<script src="vendor/chosen/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="vendor/chosen/chosen.css" />

<!-- Datepicker JS -->
<script src="vendor/datepicker164/js/bootstrap-datepicker.min.js"></script>

<!-- Bootbox JS -->
<script src="vendor/bootbox/bootbox.min.js"></script>

<!-- Custom scripts -->
<script src="assets/js/scripts.js"></script>
<script src="assets/js/login.js"></script>