<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$registerFullName = '';
	$registerUsername = '';
	$registerPassword1 = '';
	$registerPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['registerUsername'])){
		$registerFullName = htmlentities($_POST['registerFullName']);
		$registerUsername = htmlentities($_POST['registerUsername']);
		$registerPassword1 = htmlentities($_POST['registerPassword1']);
		$registerPassword2 = htmlentities($_POST['registerPassword2']);
		
		if(!empty($registerFullName) && !empty($registerUsername) && !empty($registerPassword1) && !empty($registerPassword2)){
			
			// Desinfectar nombre
			$registerFullName = filter_var($registerFullName, FILTER_SANITIZE_STRING);
			
			// Revisar si nombre está vacío
			if($registerFullName == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingresa tu nombre.</div>';
				exit();
			}
			
			// Revisar si nombre de usuario está vacío
			if($registerUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingresa tu nombre de usuario.</div>';
				exit();
			}
			
			// Revisar si ambos están vacío
			if($registerPassword1 == '' || $registerPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingresa ambas contraseñas.</div>';
				exit();
			}
			
			// Revisar si nombre de usuario esta disponible
			$usernameCheckingSql = 'SELECT * FROM user WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $registerUsername]);
			
			if($usernameCheckingStatement->rowCount() > 0){
				// El nombre de usuario ya existe. Por lo tanto, no se puede crear uno nuevo.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Nombre de usuario no disponible. Por favor seleccione otro nombre de usuario.</div>';
				exit();
			} else {
				// Revisar si las contraseñas son iguales
				if($registerPassword1 !== $registerPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Las contraseñas no coinciden.</div>';
					exit();
				} else {
					// Iniciar la inserción del usuario en la base de datos
					// Cifrar la contraseña
					$hashedPassword = md5($registerPassword1);
					$insertUserSql = 'INSERT INTO user(fullName, username, password) VALUES(:fullName, :username, :password)';
					$insertUserStatement = $conn->prepare($insertUserSql);
					$insertUserStatement->execute(['fullName' => $registerFullName, 'username' => $registerUsername, 'password' => $hashedPassword]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Registro completo.</div>';
					exit();
				}
			}
		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se mostrará un mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>