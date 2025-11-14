<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['customerDetailsCustomerFullName'])){
		
		$fullName = htmlentities($_POST['customerDetailsCustomerFullName']);
		$email = htmlentities($_POST['customerDetailsCustomerEmail']);
		$mobile = htmlentities($_POST['customerDetailsCustomerMobile']);
		$phone2 = htmlentities($_POST['customerDetailsCustomerPhone2']);
		$address = htmlentities($_POST['customerDetailsCustomerAddress']);
		$address2 = htmlentities($_POST['customerDetailsCustomerAddress2']);
		$city = htmlentities($_POST['customerDetailsCustomerCity']);
		$district = htmlentities($_POST['customerDetailsCustomerDistrict']);
		$status = htmlentities($_POST['customerDetailsStatus']);
		
		if(isset($fullName) && isset($mobile) && isset($address)) {
			// Validar numero de teléfono
			if(filter_var($mobile, FILTER_VALIDATE_INT) === 0 || filter_var($mobile, FILTER_VALIDATE_INT)) {
				// Validar numero de teléfono
			} else {
				// teléfono móvil incorrecto
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un numero de teléfono válido</div>';
				exit();
			}
			
			// Validar el segundo número de teléfono solo si lo proporciona el usuario 
			if(!empty($phone2)){
				if(filter_var($phone2, FILTER_VALIDATE_INT) === false) {
					// numero de teléfono no valido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número de teléfono válido</div>';
					exit();
				}
			}
			
			// Validar el correo electrónico solo si es proporcionado por el usuario
			if(!empty($email)) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
					// correo electrónico no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un correo electrónico valido</div>';
					exit();
				}
			}
			
			// validar dirección
			if($address == ''){
				// Dirección esta vacía
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese la dirección</div>';
				exit();
			}
			
			// Revisar si nombre completo esta vacío o no
			if($fullName == ''){
				// Nombre completo esta vacío
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese nombre completo.</div>';
				exit();
			}
			
			// Incicia el proceso de inserción
			$sql = 'INSERT INTO customer(fullName, email, mobile, phone2, address, address2, city, district, status) VALUES(:fullName, :email, :mobile, :phone2, :address, :address2, :city, :district, :status)';
			$stmt = $conn->prepare($sql);
			$stmt->execute(['fullName' => $fullName, 'email' => $email, 'mobile' => $mobile, 'phone2' => $phone2, 'address' => $address, 'address2' => $address2, 'city' => $city, 'district' => $district, 'status' => $status]);
			echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Cliente añadido a la base de datos</div>';
		} else {
			// Uno o más campos están vacíos
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>