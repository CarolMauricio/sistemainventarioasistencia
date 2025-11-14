<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['vendorDetailsStatus'])){
		
		$fullName = htmlentities($_POST['vendorDetailsVendorFullName']);
		$email = htmlentities($_POST['vendorDetailsVendorEmail']);
		$mobile = htmlentities($_POST['vendorDetailsVendorMobile']);
		$phone2 = htmlentities($_POST['vendorDetailsVendorPhone2']);
		$address = htmlentities($_POST['vendorDetailsVendorAddress']);
		$address2 = htmlentities($_POST['vendorDetailsVendorAddress2']);
		$city = htmlentities($_POST['vendorDetailsVendorCity']);
		$district = htmlentities($_POST['vendorDetailsVendorDistrict']);
		$status = htmlentities($_POST['vendorDetailsStatus']);
	
		if(isset($fullName) && isset($mobile) && isset($address)) {
			// Validar número de móvil
			if(filter_var($mobile, FILTER_VALIDATE_INT) === 0 || filter_var($mobile, FILTER_VALIDATE_INT)) {
				// Número de móvil válido
			} else {
				// El móvil está equivocado
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número de teléfono válido.</div>';
				exit();
			}
			
			// Comprueba si el móvil está vacío
			if($mobile == ''){
				// El teléfono móvil 1 está vacío
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca el número de teléfono móvil.</div>';
				exit();
			}
			
			// Validar el segundo número de teléfono solo si lo proporciona el usuario
			if(!empty($phone2)){
				if(filter_var($phone2, FILTER_VALIDATE_INT) === false) {
					//El número de teléfono adicional no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número de móvil adicional válido.</div>';
					exit();
				}
			}
			
			// Validar el correo electrónico solo si es proporcionado por el usuario
			if(!empty($email)) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
					// El correo electrónico no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un correo electrónico válido.</div>';
					exit();
				}
			}
			
			// Validar dirección, dirección adicional y municipio
			// Validar dirección
			if($address == ''){
				// Dirección está vacío
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca la dirección.</div>';
				exit();
			}
			
			// Iniciar el proceso de inserción
			$sql = 'INSERT INTO vendor(fullName, email, mobile, phone2, address, address2, city, district, status) VALUES(:fullName, :email, :mobile, :phone2, :address, :address2, :city, :district, :status)';
			$stmt = $conn->prepare($sql);
			$stmt->execute(['fullName' => $fullName, 'email' => $email, 'mobile' => $mobile, 'phone2' => $phone2, 'address' => $address, 'address2' => $address2, 'city' => $city, 'district' => $district, 'status' => $status]);
			echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Proveedor añadido a la base de datos</div>';
		} else {
			// Uno o más campos están vacíos
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	
	}
?>