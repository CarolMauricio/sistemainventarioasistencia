<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$resetPasswordUsername = '';
	$resetPasswordPassword1 = '';
	$resetPasswordPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['resetPasswordUsername'])){
		$resetPasswordUsername = htmlentities($_POST['resetPasswordUsername']);
		$resetPasswordPassword1 = htmlentities($_POST['resetPasswordPassword1']);
		$resetPasswordPassword2 = htmlentities($_POST['resetPasswordPassword2']);
		
		if(!empty($resetPasswordUsername) && !empty($resetPasswordPassword1) && !empty($resetPasswordPassword2)){
			
			// Comprobar si el nombre de usuario está vacío
			if($resetPasswordUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca su nombre de usuario.</div>';
				exit();
			}
			
			// Comprobar si las contraseñas están vacías
			if($resetPasswordPassword1 == '' || $resetPasswordPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca ambas contraseñas.</div>';
				exit();
			}
			
			// Comprobar si el nombre de usuario está disponible
			$usernameCheckingSql = 'SELECT * FROM user WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $resetPasswordUsername]);
			
			if($usernameCheckingStatement->rowCount() < 1){
				// El nombre de usuario no existe. Por lo tanto, no se puede restablecer la contraseña.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Nombre de usuario no existe.</div>';
				exit();
			} else {
				// Revisar si las contraseñas son iguales
				if($resetPasswordPassword1 !== $resetPasswordPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Las contraseñas no coinciden.</div>';
					exit();
				} else {
					// Iniciar la actualización de la contraseña en la base de datos
					// Cifrar la contraseña
					$hashedPassword = md5($resetPasswordPassword1);
					$updatePasswordSql = 'UPDATE user SET password = :password WHERE username = :username';
					$updatePasswordStatement = $conn->prepare($updatePasswordSql);
					$updatePasswordStatement->execute(['password' => $hashedPassword, 'username' => $resetPasswordUsername]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Restablecimiento de contraseña completado. Inicie sesión con su nueva contraseña.</div>';
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