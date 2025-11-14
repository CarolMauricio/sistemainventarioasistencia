<?php
	session_start();
	if(isset($_SESSION['loggedIn'])){
		header('Location: index.php');
		exit();
	}

	require_once('inc/config/constants.php');
	require_once('inc/config/db.php');
	require_once('inc/header.html');
?>
  <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login Moderno</title>
	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
	<!-- Estilos personalizados -->
	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background: linear-gradient(135deg, #1f1c2c, #928dab);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
		}
		.card {
			backdrop-filter: blur(20px);
			background: rgba(255, 255, 255, 0.15);
			border: none;
			border-radius: 20px;
			box-shadow: 0 10px 30px rgba(0,0,0,0.2);
			color: #fff;
			transition: transform 0.3s ease;
		}
		.card:hover {
			transform: translateY(-5px);
		}
		.card-header {
			background: transparent;
			border-bottom: 1px solid rgba(255,255,255,0.3);
			font-weight: 600;
			font-size: 1.3rem;
			text-align: center;
			color: #fff;
		}
		.form-control {
			background: rgba(255,255,255,0.1);
			border: 1px solid rgba(255,255,255,0.3);
			color: #fff;
			border-radius: 12px;
			padding: 10px 15px;
		}
		.form-control:focus {
			background: rgba(255,255,255,0.2);
			box-shadow: none;
			border-color: #00b4d8;
		}
		label {
			font-weight: 500;
		}
		.btn {
			border-radius: 12px;
			font-weight: 500;
			padding: 10px 18px;
		}
		.btn-primary { background: #00b4d8; border: none; }
		.btn-success { background: #38b000; border: none; }
		.btn-warning { background: #fcbf49; border: none; color: #000; }
		.btn:hover {
			opacity: 0.9;
			transform: scale(1.03);
			transition: 0.2s;
		}
		.container {
			max-width: 420px;
			width: 100%;
		}
		.requiredIcon {
			color: #f94144;
		}
	</style>
  </head>
  <body>

<?php
$action = '';
	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action == 'register'){
?>
			<div class="container">
				<div class="card p-4">
					<div class="card-header">Registro</div>
					<div class="card-body">
						<form action="">
							<div id="registerMessage"></div>
							<div class="mb-3">
								<label for="registerFullName">Nombre <span class="requiredIcon">*</span></label>
								<input type="text" class="form-control" id="registerFullName" name="registerFullName">
							</div>
							<div class="mb-3">
								<label for="registerUsername">Nombre de Usuario <span class="requiredIcon">*</span></label>
								<input type="email" class="form-control" id="registerUsername" name="registerUsername" autocomplete="on">
							</div>
							<div class="mb-3">
								<label for="registerPassword1">Contraseña <span class="requiredIcon">*</span></label>
								<input type="password" class="form-control" id="registerPassword1" name="registerPassword1">
							</div>
							<div class="mb-3">
								<label for="registerPassword2">Confirma la contraseña <span class="requiredIcon">*</span></label>
								<input type="password" class="form-control" id="registerPassword2" name="registerPassword2">
							</div>
							<div class="d-flex justify-content-between mt-3">
								<a href="login.php" class="btn btn-primary">Iniciar</a>
								<button type="button" id="register" class="btn btn-success">Registrar</button>
							</div>
							<div class="d-flex justify-content-between mt-3">
								<a href="login.php?action=resetPassword" class="btn btn-warning">Restablecer</a>
								<button type="reset" class="btn btn-light">Limpiar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
<?php
			require 'inc/footer.php';
			echo '</body></html>';
			exit();
		} elseif($action == 'resetPassword'){
?>
			<div class="container">
				<div class="card p-4">
					<div class="card-header">Restablecer Contraseña</div>
					<div class="card-body">
						<form action="">
							<div id="resetPasswordMessage"></div>
							<div class="mb-3">
								<label for="resetPasswordUsername">Nombre de Usuario</label>
								<input type="text" class="form-control" id="resetPasswordUsername" name="resetPasswordUsername">
							</div>
							<div class="mb-3">
								<label for="resetPasswordPassword1">Nueva Contraseña</label>
								<input type="password" class="form-control" id="resetPasswordPassword1" name="resetPasswordPassword1">
							</div>
							<div class="mb-3">
								<label for="resetPasswordPassword2">Confirmar Nueva Contraseña</label>
								<input type="password" class="form-control" id="resetPasswordPassword2" name="resetPasswordPassword2">
							</div>
							<div class="d-flex justify-content-between mt-3">
								<a href="login.php" class="btn btn-primary">Iniciar</a>
								<a href="login.php?action=register" class="btn btn-success">Registrar</a>
							</div>
							<div class="d-flex justify-content-between mt-3">
								<button type="button" id="resetPasswordButton" class="btn btn-warning">Restablecer</button>
								<button type="reset" class="btn btn-light">Limpiar</button>
							</div>
						</form>
					</div>
				</div>
			</div>
<?php
			require 'inc/footer.php';
			echo '</body></html>';
			exit();
		}
	}
?>
	<!-- FORMULARIO DE LOGIN -->
    <div class="container">
		<div class="card p-4">
			<div class="card-header">Iniciar Sesión</div>
			<div class="card-body">
				<form action="">
					<div id="loginMessage"></div>
					<div class="mb-3">
						<label for="loginUsername">Nombre de Usuario</label>
						<input type="text" class="form-control" id="loginUsername" name="loginUsername">
					</div>
					<div class="mb-3">
						<label for="loginPassword">Contraseña</label>
						<input type="password" class="form-control" id="loginPassword" name="loginPassword">
					</div>
					<div class="d-flex justify-content-between mt-3">
						<button type="button" id="login" class="btn btn-primary">Iniciar</button>
						<a href="login.php?action=register" class="btn btn-success">Registrar</a>
					</div>
					<div class="d-flex justify-content-between mt-3">
						<a href="login.php?action=resetPassword" class="btn btn-warning">Restablecer</a>
						<button type="reset" class="btn btn-light">Limpiar</button>
					</div>
				</form>
			</div>
		</div>
    </div>

<?php require 'inc/footer.php'; ?>
  </body>
</html>