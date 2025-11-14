<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Revisar si el POST query fue recibido
	if(isset($_POST['customerDetailsCustomerID'])) {
		
		$customerDetailsCustomerID = htmlentities($_POST['customerDetailsCustomerID']);
		$customerDetailsCustomerFullName = htmlentities($_POST['customerDetailsCustomerFullName']);
		$customerDetailsCustomerMobile = htmlentities($_POST['customerDetailsCustomerMobile']);
		$customerDetailsCustomerPhone2 = htmlentities($_POST['customerDetailsCustomerPhone2']);
		$customerDetailsCustomerEmail = htmlentities($_POST['customerDetailsCustomerEmail']);
		$customerDetailsCustomerAddress = htmlentities($_POST['customerDetailsCustomerAddress']);
		$customerDetailsCustomerAddress2 = htmlentities($_POST['customerDetailsCustomerAddress2']);
		$customerDetailsCustomerCity = htmlentities($_POST['customerDetailsCustomerCity']);
		$customerDetailsCustomerDistrict = htmlentities($_POST['customerDetailsCustomerDistrict']);
		$customerDetailsStatus = htmlentities($_POST['customerDetailsStatus']);
		
		// Revisar si los campos obligatorios no están vacíos
		if(isset($customerDetailsCustomerFullName) && isset($customerDetailsCustomerMobile) && isset($customerDetailsCustomerAddress)) {
			
			// Validar teléfono
			if(filter_var($customerDetailsCustomerMobile, FILTER_VALIDATE_INT) === 0 || filter_var($customerDetailsCustomerMobile, FILTER_VALIDATE_INT)) {
				// Teléfono válido
			} else {
				// Teléfono no es válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número de teléfono válido</div>';
				exit();
			}
			
			// Verificar si el campo CustomerID está vacío. De ser así, mostrar un mensaje de error.
			// Debemos informarle específicamente al usuario porque el (*) no está presente en ese campo.
			if(empty($customerDetailsCustomerID)){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese el CustomerID para actualizar ese cliente.</div>';
				exit();
			}
			
			// Validar el segundo número de teléfono solo si lo proporciona el usuario
			if(!empty($customerDetailsCustomerPhone2)){
				if(filter_var($customerDetailsCustomerPhone2, FILTER_VALIDATE_INT) === 0 || filter_var($customerDetailsCustomerPhone2, FILTER_VALIDATE_INT)) {
					// segundo número de teléfono es válido
				} else {
					// segundo número de teléfono no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número de teléfono válido.</div>';
					exit();
				}
			}
			
			// Validar el correo electrónico solo si es proporcionado por el usuario
			if(!empty($customerDetailsCustomerEmail)) {
				if (filter_var($customerDetailsCustomerEmail, FILTER_VALIDATE_EMAIL) === false) {
					// correo electrónico no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un correo electrónico válido</div>';
					exit();
				}
			}

			// Compruebe si el CustomerID proporcionado está en la base de datos
			$customerIDSelectSql = 'SELECT customerID FROM customer WHERE customerID = :customerDetailsCustomerID';
			$customerIDSelectStatement = $conn->prepare($customerIDSelectSql);
			$customerIDSelectStatement->execute(['customerDetailsCustomerID' => $customerDetailsCustomerID]);
			
			if($customerIDSelectStatement->rowCount() > 0) {
				
				// ClienteID está disponible en la base de datos. Por lo tanto, podemos actualizar sus datos.
				// Construct the UPDATE query
				$updateCustomerDetailsSql = 'UPDATE customer SET fullName = :fullName, email = :email, mobile = :mobile, phone2 = :phone2, address = :address, address2 = :address2, city = :city, district = :district, status = :status WHERE customerID = :customerID';
				$updateCustomerDetailsStatement = $conn->prepare($updateCustomerDetailsSql);
				$updateCustomerDetailsStatement->execute(['fullName' => $customerDetailsCustomerFullName, 'email' => $customerDetailsCustomerEmail, 'mobile' => $customerDetailsCustomerMobile, 'phone2' => $customerDetailsCustomerPhone2, 'address' => $customerDetailsCustomerAddress, 'address2' => $customerDetailsCustomerAddress2, 'city' => $customerDetailsCustomerCity, 'district' => $customerDetailsCustomerDistrict, 'status' => $customerDetailsStatus, 'customerID' => $customerDetailsCustomerID]);
				
				// UPDATE nombre del cliente también en la tabla de ventas
				$updateCustomerInSaleTableSql = 'UPDATE sale SET customerName = :customerName WHERE customerID = :customerID';
				$updateCustomerInSaleTableStatement = $conn->prepare($updateCustomerInSaleTableSql);
				$updateCustomerInSaleTableStatement->execute(['customerName' => $customerDetailsCustomerFullName, 'customerID' => $customerDetailsCustomerID]);
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalle de cliente actualizado.</div>';
				exit();
			} else {
				// ClienteID no está en la base de datos. Por lo tanto, detenga la actualización y salga.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>ClienteID no existe en la base de datos. Por lo tanto, no es posible actualizar.</div>';
				exit();
			}
			
		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, muestre el mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>